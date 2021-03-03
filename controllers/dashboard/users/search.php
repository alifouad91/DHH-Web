<?php
defined('C5_EXECUTE') or die('Access Denied.');

class DashboardUsersSearchController extends Concrete5_Controller_Dashboard_Users_Search
{

    public function validate_user()
    {
        $pke = PermissionKey::getByHandle('edit_user_properties');
        if (!$pke->validate()) {
            return false;
        }

        $assignment = $pke->getMyAssignment();


        $vals = Loader::helper('validation/strings');
        $valt = Loader::helper('validation/token');
        $valc = Loader::helper('concrete/validation');
        $th   = Loader::helper('text');

        $uo = UserInfo::getByID(intval($_GET['uID']));

        $username = trim($_POST['uName']);
        $username = preg_replace("/\s+/", ' ', $username);
        $fName    = $th->sanitize($_POST['fName']);
        $lName    = $th->sanitize($_POST['lName']);

        if ($assignment->allowEditPassword()) {
            $password        = $_POST['uPassword'];
            $passwordConfirm = $_POST['uPasswordConfirm'];

            if ($password) {
                if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
                    $this->error->add(t('A password must be between %s and %s characters', USER_PASSWORD_MINIMUM, USER_PASSWORD_MAXIMUM));
                }
            }
        }

        if ($assignment->allowEditEmail()) {
            if (!$vals->email($_POST['uEmail'])) {
                $this->error->add(t('Invalid email address provided.'));
            } elseif (!$valc->isUniqueEmail($_POST['uEmail']) && $uo->getUserEmail() != $_POST['uEmail']) {
                $this->error->add(t("The email address '%s' is already in use. Please choose another.", $_POST['uEmail']));
            }
        }

        if ($assignment->allowEditUserName()) {
            $_POST['uName'] = $username;
            if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == false) {
                if (strlen($username) < USER_USERNAME_MINIMUM) {
                    $this->error->add(t('A username must be at least %s characters long.', USER_USERNAME_MINIMUM));
                }

                if (strlen($username) > USER_USERNAME_MAXIMUM) {
                    $this->error->add(t('A username cannot be more than %s characters long.', USER_USERNAME_MAXIMUM));
                }

                /*
                if (strlen($username) >= USER_USERNAME_MINIMUM && !$vals->alphanum($username,USER_USERNAME_ALLOW_SPACES)) {
                    if(USER_USERNAME_ALLOW_SPACES) {
                        $e->add(t('A username may only contain letters, numbers and spaces.'));
                    } else {
                        $e->add(t('A username may only contain letters or numbers.'));
                    }

                }
                */

                if (strlen($username) >= USER_USERNAME_MINIMUM && !$valc->username($username)) {
                    if (USER_USERNAME_ALLOW_SPACES) {
                        $this->error->add(t('A username may only contain letters, numbers, spaces, dots (not at the beginning/end), underscores (not at the beginning/end).'));
                    } else {
                        $this->error->add(t('A username may only contain letters numbers, dots (not at the beginning/end), underscores (not at the beginning/end).'));
                    }
                }
                if (strcasecmp($uo->getUserName(), $username) && !$valc->isUniqueUsername($username)) {
                    $this->error->add(t("The username '%s' already exists. Please choose another", $username));
                }
            }
        }

        if ($assignment->allowEditPassword()) {
            if (strlen($password) >= USER_PASSWORD_MINIMUM && !$valc->password($password)) {
                $this->error->add(t('A password may not contain ", \', >, <, or any spaces.'));
            }

            if ($password) {
                if ($password != $passwordConfirm) {
                    $this->error->add(t('The two passwords provided do not match.'));
                }
            }
        }
        if (!$fName) {
            $this->error->add(t("First name is required", $fName));
        }
        if (!$lName) {
            $this->error->add(t("Last name is required", $fName));
        }

        if (!$valt->validate('update_account_' . intval($_GET['uID']))) {
            $this->error->add($valt->getErrorMessage());
        }

        if (!$this->error->has()) {
            // do the registration
            $data = array();
            if ($assignment->allowEditUserName()) {
                $data['uName'] = $_POST['uName'];
            }
            if ($assignment->allowEditEmail()) {
                $data['uEmail'] = $_POST['uEmail'];
            }
            if ($assignment->allowEditPassword()) {
                $data['uPassword']        = $_POST['uPassword'];
                $data['uPasswordConfirm'] = $_POST['uPasswordConfirm'];
            }
            if ($assignment->allowEditTimezone()) {
                $data['uTimezone'] = $_POST['uTimezone'];
            }
            if ($assignment->allowEditDefaultLanguage()) {
                $data['uDefaultLanguage'] = $_POST['uDefaultLanguage'];
            }
            $data['fullName'] = sprintf('%s %s', $fName, $lName);
            $process          = $uo->update($data);

            //$db = Loader::db();
            if ($process) {
                if ($assignment->allowEditAvatar()) {
                    $av = Loader::helper('concrete/avatar');
                    if (is_uploaded_file($_FILES['uAvatar']['tmp_name'])) {
                        $uHasAvatar = $av->updateUserAvatar($_FILES['uAvatar']['tmp_name'], $uo->getUserID());
                    }
                }

                $gak  = PermissionKey::getByHandle('assign_user_groups');
                $gIDs = array();
                if (is_array($_POST['gID'])) {
                    foreach ($_POST['gID'] as $gID) {
                        if ($gak->validate($gID)) {
                            $gIDs[] = intval($gID);
                        }
                    }
                }

                $gIDs = array_unique($gIDs);

                $uo->updateGroups($gIDs);

                $message = t('User updated successfully. ');
                if ($password) {
                    $message .= t('Password changed.');
                }
                $editComplete = true;
                // reload user object
                $uo = UserInfo::getByID(intval($_GET['uID']));
                $this->set('message', $message);
            } else {
                $db = Loader::db();
                $this->error->add($db->ErrorMsg());
                $this->set('error', $this->error);
            }
        } else {
            $this->set('error', $this->error);
        }
    }
}
