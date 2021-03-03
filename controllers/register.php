<?php
defined('C5_EXECUTE') or die('Access Denied.');

class RegisterController extends Concrete5_Controller_Register
{

    public function view($arg = '')
    {
        $th        = Loader::helper('text');
        $fb         = Facebook::getFacebookService();
        $helper     = $fb->getRedirectLoginHelper();
        $FBloginUrl = $helper->getLoginUrl(BASE_URL . DIR_REL . FB_REDIRECT_URL);
        $referralToken = $th->sanitize($arg);

        $this->set('FBloginUrl', $FBloginUrl);
        $this->set('referralToken', $referralToken);
    }

    public function do_register()
    {
        /** @var TextHelper $th */

        $registerData['success'] = 0;

        $th         = Loader::helper('text');
        $userHelper = Loader::helper('concrete/user');
        $e          = Loader::helper('validation/error');
        $ip         = Loader::helper('validation/ip');
        $vals       = Loader::helper('validation/strings');
        $valc       = Loader::helper('concrete/validation');
        /** @var ValidationIdentifierHelper $vih */
        $vih        = Loader::helper('validation/identifier');

        $format = Str::lower($this->request('format'));

        if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
            $username = $vih->generate('Users', 'uName');
        } else {
            $username = $_POST['uName'];
        }
        $email    = $_POST['uEmail'];
        $password = $_POST['uPassword'];
        $referralToken = $_POST['token'];

        $fullName = $th->sanitize($this->post('fullName'));

        if (!$fullName) {
            $e->add(t('Please add your Full Name.'));
        }

        // clean the username
        $username = trim($username);
        $username = preg_replace('/ +/', ' ', $username);


        if (!$ip->check()) {
            $e->add($ip->getErrorMessage());
        }

        if (ENABLE_REGISTRATION_CAPTCHA) {
            $captcha = Loader::helper('validation/captcha');
            if (!$captcha->check()) {
                $e->add(t('Invalid Captcha.'));
            }
        }

        if (!$vals->email($email)) {
            $e->add('Invalid email address provided.');
        } elseif (!$valc->isUniqueEmail($email)) {
             //$e->add(t('The email address %s is already in use. Please choose another.', $email));
             $loginLInk = "<a href='".View::url('/login')."'>".t('There is already an account associated with this email. Please click here to login or change your password.')."</a>";
             $e->add($loginLInk);
        }

        //if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == false) {

        if (strlen($username) < USER_USERNAME_MINIMUM) {
            $e->add(t('A username must be at least %s characters long.', USER_USERNAME_MINIMUM));
        }

        if (strlen($username) > USER_USERNAME_MAXIMUM) {
            $e->add(t('A username cannot be more than %s characters long.', USER_USERNAME_MAXIMUM));
        }


        if (strlen($username) >= USER_USERNAME_MINIMUM && !$valc->username($username)) {
            if (USER_USERNAME_ALLOW_SPACES) {
                $e->add(t('A username may only contain letters, numbers and spaces.'));
            } else {
                $e->add(t('A username may only contain letters or numbers.'));
            }
        }
        if (!$valc->isUniqueUsername($username)) {
            $e->add(t('The username %s already exists. Please choose another', $username));
        }
        //}

        if ($username == USER_SUPER) {
            $e->add(t('Invalid Username'));
        }

        /*

        if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
            $e->add(t('A password must be between %s and %s characters', USER_PASSWORD_MINIMUM, USER_PASSWORD_MAXIMUM));
        }
        if (strlen($password) >= USER_PASSWORD_MINIMUM && !$valc->password($password)) {
            $e->add(t('A password may not contain ", \', >, <, or any spaces.'));
        }

        */

        $userHelper->validNewPassword($password, $e);


        if (!$e->has()) {

            // do the registration
            $data               = $_POST;
            $data['uName']      = $username;
            $data['email']      = $email;
            $data['uPassword']  = $password;
            $data['fullName']   = $fullName;
            $data['uHasAvatar'] = 1;

            /** @var UserInfo $process */

            $process = UserInfo::register($data);

            if($referralToken){
                $referrer = UserInfo::getByUniqueToken($referralToken);
                $referredBy = $referrer->getUserEmail();
                $referralList = new ReferralList();
                $referralList->filterByReferrerEmail($referredBy);
                $referralList->filterByReferredEmail($email);
                $referrals = $referralList->get();

                if($referrals){
                    $process->updateReferredDetails($referredBy);
                }
            }

            $avatar = $th->sanitize($this->post('avatar'));
            if ($avatar) {
                $this->saveRemoteImage($avatar, $process->getUserID());
            }
            if (is_object($process)) {
                $process->setAvatar();
                if (REGISTER_NOTIFICATION) { //do we notify someone if a new user is added?
                    $mh = Loader::helper('mail');
                    if (EMAIL_ADDRESS_REGISTER_NOTIFICATION) {
                        $mh->to(EMAIL_ADDRESS_REGISTER_NOTIFICATION);
                    } else {
                        $adminUser = UserInfo::getByID(USER_SUPER_ID);
                        if (is_object($adminUser)) {
                            $mh->to($adminUser->getUserEmail());
                        }
                    }

                    $mh->addParameter('uID', $process->getUserID());
                    $mh->addParameter('user', $process);
                    $mh->addParameter('uName', $process->getFirstName());
                    $mh->addParameter('uEmail', $process->getUserEmail());
                    $mh->addParameter('fullName', $fullName);

                    if (defined('EMAIL_ADDRESS_REGISTER_NOTIFICATION_FROM')) {
                        $mh->from(EMAIL_ADDRESS_REGISTER_NOTIFICATION_FROM, t('Website Registration Notification'));
                    } else {
                        $adminUser = UserInfo::getByID(USER_SUPER_ID);
                        if (is_object($adminUser)) {
                            $mh->from($adminUser->getUserEmail(), t('Website Registration Notification'));
                        }
                    }
                    if (REGISTRATION_TYPE == 'manual_approve') {
                        $mh->load('user_register_approval_required');
                    } elseif(REGISTRATION_TYPE == 'validate_email') {
                        $mh->load('validate_user_email');
                    }else{
                        $mh->load('user_register');
                    }
                    $mh->sendMail();
                }

                // now we log the user in
                if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
                    $u = new User($email, $password);
                } else {
                    $u = new User($username, $password);
                }
                // if this is successful, uID is loaded into session for this user

                $rcID = $this->post('rcID');
                $nh   = Loader::helper('validation/numbers');
                if (!$nh->integer($rcID)) {
                    $rcID = 0;
                }

                // now we check whether we need to validate this user's email address
                if (defined('USER_VALIDATE_EMAIL') && USER_VALIDATE_EMAIL) {
                    if (USER_VALIDATE_EMAIL > 0) {
                        $uHash = $process->setupValidation();
                        $mh    = Loader::helper('mail');
                        $mh->from(EMAIL_DEFAULT_FROM_ADDRESS, EMAIL_DEFAULT_FROM_NAME);
                        $mh->addParameter('uEmail', $email);
                        $mh->addParameter('fullname', $fullName);
                        $mh->addParameter('uName', $process->getFirstName());
                        $mh->addParameter('uHash', $uHash);
                        $mh->to($email);
                        $mh->load('validate_user_email');
                        Log::addEntry(var_export($mh->getBodyHtml(), true), 'Validate User Email');
                        $mh->sendMail();


                        //$this->redirect('/register', 'register_success_validate', $rcID);
                        $redirectMethod      = 'register_success_validate';
                        $registerData['msg'] = implode('<br><br>', $this->getRegisterSuccessValidateMsgs());

                        $u->logout();
                    }
                } elseif (defined('USER_REGISTRATION_APPROVAL_REQUIRED') && USER_REGISTRATION_APPROVAL_REQUIRED) {
                    $ui = UserInfo::getByID($u->getUserID());
                    $ui->deactivate();
                    //$this->redirect('/register', 'register_pending', $rcID);
                    $redirectMethod      = 'register_pending';
                    $registerData['msg'] = $this->getRegisterPendingMsg();
                    $u->logout();
                }

                if (!$u->isError()) {
                    //$this->redirect('/register', 'register_success', $rcID);
                    if (!$redirectMethod) {
                        $redirectMethod      = 'register_success';
                        $registerData['msg'] = $this->getRegisterSuccessMsg();
                    }
                    $registerData['uID'] = intval($u->uID);
                }

                $registerData['success'] = 1;

                if ($format !== 'json') {
                    $this->redirect('/register', $redirectMethod, $rcID);
                }
            }
        } else {
            $ip->logSignupRequest();
            if ($ip->signupRequestThreshholdReached()) {
                $ip->createIPBan();
            }
            $this->set('error', $e);
            $registerData['errors'] = $e->getList();
        }

        if ($format === 'json') {
            $jsonHelper = Loader::helper('json');
            echo $jsonHelper->encode($registerData);
            die;
        }
    }

    public function getRegisterSuccessMsg()
    {
        return t('Your account has been created, and you are now logged in.');
    }

    public function getRegisterSuccessValidateMsgs()
    {
        $msgs   = array();
        $msgs[] = t('You are registered but you need to validate your email address. Some or all functionality on this site will be limited until you do so.');
        $msgs[] = t('An email has been sent to your email address. Click on the URL contained in the email to validate your email address.');

        return $msgs;
    }

    protected function saveRemoteImage($img_url, $uID)
    {
        $img_url = str_replace('_normal', '', $img_url);
        $img_url = str_replace('_s.', '_n.', $img_url);
        $fp      = FilePermissions::getGlobal();
        Loader::library('3rdparty/Zend/Http/Client');
        Loader::library('3rdparty/Zend/Uri/Http');
        $client   = new Zend_Http_Client($img_url);
        $response = $client->request();
        if ($response->isSuccessful()) {
            $uri   = Zend_Uri_Http::fromString($img_url);
            $fname = $uID . '.png';
            $fpath = DIR_FILES_UPLOADED_STANDARD . '/avatars';
            if (strlen($fname)) {
                $handle = fopen($fpath . '/' . $fname, "w");
                fwrite($handle, $response->getBody());
                fclose($handle);
                $db = Loader::db();
                $db->Execute("UPDATE Users SET uHasAvatar = 1 WHERE uID = ?", array($uID));
            }
        }
    }
}
