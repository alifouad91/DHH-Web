<?php
defined('C5_EXECUTE') or die('Access Denied.');

class LoginController extends Concrete5_Controller_Login
{
    public function facebook()
    {
        /** @var ConcreteAvatarHelper $avatarHelper */
        /** @var ValidationIdentifierHelper $idHelper */
        /** @var \Facebook\GraphNodes\GraphUser $userNode */
        $idHelper = Loader::helper('validation/identifier');

        $fb = Facebook::getFacebookService();

        $helper = $fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
        }

        try {

            $fb->setDefaultAccessToken($accessToken);

            $response = $fb->get('/me?fields=email,first_name,last_name');
            $userNode = $response->getGraphUser();

            $ul = new UserList();
            $ul->filterByFacebookID($userNode->getId());

            $ui = reset($ul->get(1));

            if ($ui) {
                User::loginByUserID($ui->getUserID());
            } else {
                $ui = UserInfo::getByEmail($userNode->getEmail());

                if ($ui) {

                    $ui->setFacebookID($userNode->getId());

                    User::loginByUserID($ui->getUserID());

                } else {
                    $userAry              = array();
                    $userAry['uName']     = $idHelper->generate('Users', 'uName', 8);
                    $userAry['uEmail']    = $userNode->getEmail() ? $userNode->getEmail() : $this->generate_email();
                    $userAry['uIsActive'] = 1;
                    $ui                   = $this->createUser($userAry);
                    $ui->markValidated();

                    if (is_object($ui)) {
                        $ui->setFacebookID($userNode->getId());
                        $ui->update(['fullName' => $userNode->getFirstName() . ' ' . $userNode->getLastName()]);
                        $this->saveRemoteImage('http://graph.facebook.com/' . $userNode->getId() . '/picture?width=500&heigth=500', $ui->uID);
                        $_SESSION['social_login'] = 'facebook';
                    }

                }

            }

        } catch (Exception $e) {
            $this->redirect('/login');
        }
        if ($_COOKIE['redirectPath'])
        {
            $redirectPath = $_COOKIE['redirectPath'];
            unset($_COOKIE['redirectPath']);
            $this->redirect($redirectPath);
        }
        $this->redirect('/profile');
    }


    public function google()
    {
        /** @var ValidationIdentifierHelper $idHelper */
        $idHelper = Loader::helper('validation/identifier');

        try {
            $code = $this->get('code');

            $googleClient = Google::getGoogleClient();
            $googleClient->authenticate($code);

            $ticket = $googleClient->verifyIdToken();

            if (!$ticket || !$ticket['sub']) {
                throw new Exception('Failed to verify ID Token');
            }

            $ul = new UserList();
            $ul->filterByGoogleID($ticket['sub']);
            $ui = reset($ul->get(1));

            if ($ui) {
                User::loginByUserID($ui->getUserID());

            } else {

                $ui = UserInfo::getByEmail($ticket['email']);

                if ($ui) {

                    $ui->setGoogleID($ticket['sub']);
                    User::loginByUserID($ui->getUserID());;

                } else {
                    $userAry              = array();
                    $userAry['uName']     = $idHelper->generate('Users', 'uName', 8);
                    $userAry['uEmail']    = $ticket['email'] ? $ticket['email'] : $this->generate_email();
                    $userAry['uIsActive'] = 1;
                    $ui                   = $this->createUser($userAry);
                    $ui->markValidated();

                    if (is_object($ui)) {
                        $ui->setGoogleID($ticket['sub']);
                        $ui->update(['fullName' => $ticket['given_name'] . ' ' . $ticket['family_name']]);
                        $this->saveRemoteImage($ticket['picture'], $ui->uID);
                        $_SESSION['social_login'] = 'google';
                    }

                }

            }

        } catch (Exception $e) {
            Log::addEntry($e->getMessage(), 'google_login');
            $this->redirect('/');
        }
        if ($_COOKIE['redirectPath'])
        {
            $redirectPath = $_COOKIE['redirectPath'];
            unset($_COOKIE['redirectPath']);
            $this->redirect($redirectPath);
        }
        $this->redirect('/profile');
    }

    protected function createUser($userAry)
    {
        $ui = UserInfo::add($userAry);
        if (is_object($ui) && $ui->uID) {
            User::getByUserID($ui->uID, true);
            return $ui;
        }
        return false;
    }

    private function generate_email()
    {
        /** @var ConcreteValidationHelper $cvh */
        $this->auto_generated_email = true;
        $cvh                        = Loader::helper('concrete/validation');
        $username_length            = 10;
        $characters                 = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $extension                  = "ae";
        do {
            $randomName = '';
            for ($j = 0; $j < $username_length; $j++) {
                $randomName .= $characters[rand(0, strlen($characters) - 1)];
            }
            $email = $randomName . "@" . "dhh." . $extension;
        } while (!$cvh->isUniqueEmail($email));

        return $email;
    }

    protected function saveRemoteImage($img_url, $uID)
    {
        /** @var FileHelper $fh */
        /** @var ConcreteAvatarHelper $avatarHelper */
        $fh           = Loader::helper('file');
        $avatarHelper = Loader::helper('concrete/avatar');
        Loader::library('3rdparty/Zend/Http/Client');
        Loader::library('3rdparty/Zend/Uri/Http');


        if (filter_var($img_url, FILTER_VALIDATE_URL) !== false) {

            $tmpFile = $fh->getTemporaryDirectory() . '/' . md5(uniqid('avatars', true) . microtime());

            try {

                $imageData = (new GuzzleHttp\Client())->get($img_url)->getBody();

                if (@file_put_contents($tmpFile, $imageData) === false) {
                    throw new Exception('Failed to save profile picture');
                }

                $avatarHelper->updateUserAvatar($tmpFile, $uID);

                @unlink($tmpFile);

            } catch (Exception $e) {
                Log::addEntry('Failed to save profile picture at location ' . $tmpFile . ' for User ID ' . $uID . PHP_EOL . $e->getMessage(), 'social_login');
            }
        }
    }

    public function change_password($uHash = '')
    {
        $db = Loader::db();
        $h  = Loader::helper('validation/identifier');
        $e  = Loader::helper('validation/error');
        $ui = UserInfo::getByValidationHash($uHash);
        if (is_object($ui)) {
            $hashCreated = $db->GetOne('SELECT uDateGenerated FROM UserValidationHashes WHERE uHash=?', array($uHash));
            if ($hashCreated < (time() - (USER_CHANGE_PASSWORD_URL_LIFETIME))) {
                $h->deleteKey('UserValidationHashes', 'uHash', $uHash);
                throw new Exception(t('Key Expired. Please visit the forgot password page again to have a new key generated.'));
            } else {
                if (strlen($_POST['uPassword'])) {
                    $userHelper = Loader::helper('concrete/user');
                    $userHelper->validNewPassword($_POST['uPassword'], $e);

                    if (strlen($_POST['uPassword']) && $_POST['uPasswordConfirm'] != $_POST['uPassword']) {
                        $e->add(t('The two passwords provided do not match.'));
                    }

                    if (!$e->has()) {
                        $ui->changePassword($_POST['uPassword']);
                        $h->deleteKey('UserValidationHashes', 'uHash', $uHash);
                        $this->set('passwordChanged', true);

                        $u = $ui->getUserObject();
                        if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
                            $_POST['uName'] = $ui->getUserEmail();
                        } else {
                            $_POST['uName'] = $u->getUserName();
                        }
                        $this->do_login();

                        return;
                    } else { // This else is always used (due to return above), no need for else statement.
                        $this->set('uHash', $uHash);
                        $this->set('changePasswordForm', true);
                        $this->set('errorMsg', implode('<br>', $e->getList()));
                    }
                } else {
                    $this->set('uHash', $uHash);
                    $this->set('changePasswordForm', true);
                }
            }
        } else {
            throw new Exception(t('Invalid Key. Please visit the forgot password page again to have a new key generated.'));
        }
    }

    public function forgot_password()
    {

        $loginData['success'] = 0;

        $vs = Loader::helper('validation/strings');
        $em = $this->post('uEmail');
        $this->set('forgot_password',true);
        if ($this->isPost()) {
            try {
                if (!$vs->email($em)) {
                    throw new Exception(t('Invalid email address.'));
                }
                if (ENABLE_REGISTRATION_CAPTCHA) {
                    $captcha = Loader::helper('validation/captcha');
                    if (!$captcha->check()) {
                        throw new Exception(t('Invalid Captcha.'));
                    }
                }

                $oUser = UserInfo::getByEmail($em);
                if (!$oUser) {
                    throw new Exception(t('We have no record of that email address.'));
                }

                $mh = Loader::helper('mail');
                //$mh->addParameter('uPassword', $oUser->resetUserPassword());
                if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
                    $mh->addParameter('uName', $oUser->getUserEmail());
                } else {
                    $mh->addParameter('uName', $oUser->getUserName());
                }
                $mh->to($oUser->getUserEmail());

                //generate hash that'll be used to authenticate user, allowing them to change their password
                $uHash = UserValidationHash::add($oUser->getUserID(), UVTYPE_CHANGE_PASSWORD, true);

                $changePassURL = BASE_URL . View::url('/login', 'change_password', $uHash);
                $mh->addParameter('changePassURL', $changePassURL);
                $mh->from(EMAIL_DEFAULT_FROM_ADDRESS, EMAIL_DEFAULT_FROM_NAME);
                $mh->load('forgot_password');
                Log::addEntry(var_export($mh->getBodyHTML(), true), 'reset_password_template');
                @$mh->sendMail();

                $loginData['success'] = 1;
                $loginData['msg']     = $this->getPasswordSentMsg();
            } catch (Exception $e) {
                $this->error->add($e);
                $loginData['error'] = $e->getMessage();
            }

            if ($_REQUEST['format'] == 'JSON') {
                $jsonHelper = Loader::helper('json');
                echo $jsonHelper->encode($loginData);
                die;
            }

            if ($loginData['success'] == 1) {
                $this->redirect('/login', 'password_sent');
            }
        }
    }


    protected function finishLogin($loginData = array())
    {
        $u = new User();
        if ($this->post('uMaintainLogin')) {
            $u->setUserForeverCookie();
        }

        if (count($this->locales) > 0) {
            if (Config::get('LANGUAGE_CHOOSE_ON_LOGIN') && $this->post('USER_LOCALE') != '') {
                $u->setUserDefaultLanguage($this->post('USER_LOCALE'));
            }
        }

        // Verify that the user has filled out all
        // required items that are required on register
        // That means users logging in after new user attributes
        // have been created and required will be prompted here to
        // finish their profile

        $this->set('invalidRegistrationFields', false);
        Loader::model('attribute/categories/user');
        $ui  = UserInfo::getByID($u->getUserID());
        $aks = UserAttributeKey::getRegistrationList();

        $unfilledAttributes = array();
        foreach ($aks as $uak) {
            if ($uak->isAttributeKeyRequiredOnRegister()) {
                $av = $ui->getAttributeValueObject($uak);
                if (!is_object($av)) {
                    $unfilledAttributes[] = $uak;
                }
            }
        }

        if ($this->post('completePartialProfile')) {
            foreach ($unfilledAttributes as $uak) {
                $e1 = $uak->validateAttributeForm();
                if ($e1 == false) {
                    $this->error->add(t('The field "%s" is required', $uak->getAttributeKeyDisplayName()));
                } elseif ($e1 instanceof ValidationErrorHelper) {
                    $this->error->add($e1);
                }
            }

            if (!$this->error->has()) {
                // the user has needed to complete a partial profile, and they have done so,
                // and they have no errors. So we save our profile data against the account.
                foreach ($unfilledAttributes as $uak) {
                    $uak->saveAttributeForm($ui);
                    $unfilledAttributes = array();
                }
            }
        }

        if (count($unfilledAttributes) > 0) {
            $u->logout();
            $this->set('invalidRegistrationFields', true);
            $this->set('unfilledAttributes', $unfilledAttributes);
        }
        $txt  = Loader::helper('text');
        $rcID = $this->post('rcID');
        $nh   = Loader::helper('validation/numbers');

        // set redirect url
        if ($nh->integer($rcID)) {
            $nh                       = Loader::helper('navigation');
            $rc                       = Page::getByID($rcID);
            $url                      = $nh->getLinkToCollection($rc, true);
            $loginData['redirectURL'] = $url;
        } elseif (strlen($rcID)) {
            $rcID = trim($rcID, '/');

            $nc2 = Page::getByPath('/' . $rcID);
            if (is_object($nc2) && !$nc2->isError()) {
                $loginData['redirectURL'] = BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '/' . $rcID;
            }
        }

        /*
        //full page login redirect (non-ajax login)
        if( strlen($loginData['redirectURL']) && $_REQUEST['format']!='JSON' ) {
            header('Location: ' . $loginData['redirectURL']);
            exit;
        }
        */

        $dash = Page::getByPath('/dashboard', 'RECENT');
        $dbp  = new Permissions($dash);

        Events::fire('on_user_login', $this);

        //End JSON Login
        if ($_REQUEST['format'] == 'JSON') {
            return $loginData;
        }

        //should administrator be redirected to dashboard?  defaults to yes if not set.
        $adminToDash = intval(Config::get('LOGIN_ADMIN_TO_DASHBOARD'));

        //Full page login, standard redirection
        $u = new User(); // added for the required registration attribute change above. We recalc the user and make sure they're still logged in
        if ($u->isRegistered()) {
            if ($u->config('NEWSFLOW_LAST_VIEWED') == 'FIRSTRUN') {
                $u->saveConfig('NEWSFLOW_LAST_VIEWED', 0);
            }

            if ($this->post('redirectPath'))
            {
                $this->redirect($this->post('redirectPath'));
                die();
            }
            if ($loginData['redirectURL']) {
                //make double secretly sure there's no caching going on
                header('Cache-Control: no-store, no-cache, must-revalidate');
                header('Pragma: no-cache');
                header('Expires: Fri, 30 Oct 1998 14:19:41 GMT'); //in the past
                $this->externalRedirect($loginData['redirectURL']);
            } elseif ($dbp->canRead() && $adminToDash) {
                $this->redirect('/dashboard');
            } elseif ($u->isLandLord()){
                $this->redirect('/profile/my-properties');
            }else {
                //options set in dashboard/users/registration
                $login_redirect_cid  = intval(Config::get('LOGIN_REDIRECT_CID'));
                $login_redirect_mode = Config::get('LOGIN_REDIRECT');

                //redirect to user profile

                $this->redirect('/profile');
            }
        }
    }

    // responsible for validating a user's email address
    public function v($hash = '')
    {
        $ui = UserInfo::getByValidationHash($hash);
        if (is_object($ui)) {
            $ui->markValidated();
            $this->set('uEmail', $ui->getUserEmail());
            $this->set('validated', true);
            Events::fire('on_email_verification_success',$ui);
        }
    }
}
