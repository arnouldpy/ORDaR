<?php
namespace search\controller;

use \search\controller\ConnectController as ConnectDB;
use \search\controller\MailerController as Mailer;
use \search\model\LostPassword as LostPassword;
use \search\model\MailValidation as MailValidation;
use \search\model\Users as Users;

class UserController
{
    private $DBinstance;

    public function __construct()
    {
        $ConnectDB  = new ConnectDB();
        $DBinstance = $ConnectDB->EloConfigure($_SERVER['DOCUMENT_ROOT'] . '/../AuthDB.ini');
    }
    public function check_current_user($email)
    {
        $verif = Users::find($email);
        if ((count($verif) == 1) && ($verif->status != 0)) {
            return $verif;
        }
    }

    public function login($id, $password)
    {
        $verif = Users::select('mail', 'mdp', 'name', 'firstname', 'status', 'mail_validation', 'type')->where('mail', '=', $id)->get();
        $user  = Users::select('mail')->where('mail', '=', $id)->first();
        if (count($verif) == 0) {
            $error = "No account linked to this email!";
            return $error;
        } else {
            if (!empty($verif[0])) {
                $hash = $verif[0]->mdp;
                if (password_verify($password, $hash)) {
                    if ($verif[0]->status == 0 or $verif[0]->mail_validation == 0) {
                        $error = "Account not verified, please wait for an admin to activate your account";
                        return $error;
                    } else {
                        $_SESSION['name']      = $verif[0]->name;
                        $_SESSION['firstname'] = $verif[0]->firstname;
                        $_SESSION['mail']      = $verif[0]->mail;
                        $_SESSION['admin']     = $verif[0]->type;

                    }
                } else {
                    $error = "Bad password!";
                    return $error;
                }
            }

        }
    }
    public function signup($name, $firstname, $email, $password, $passwordconfirm)
    {
        $verif = Users::select('mail', 'mdp')->where('mail', '=', $email)->get();
        if (count($verif) == 0) {
            if (preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}/", $password)) {
                if ($password == $passwordconfirm && empty(!$password)) {
                    $passwordtest      = password_hash($password, PASSWORD_DEFAULT);
                    $signup            = new Users;
                    $signup->name      = $name;
                    $signup->firstname = $firstname;
                    $signup->mail      = $email;
                    $signup->mdp       = $passwordtest;
                    $signup->type      = 0;
                    $signup->status    = 0;
                    if ($signup->save()) {
                        $token                    = bin2hex(openssl_random_pseudo_bytes(32));
                        $MailValidation           = new MailValidation();
                        $MailValidation->mail     = $email;
                        $MailValidation->datetime = date("Y-m-d H:i:s");
                        $MailValidation->token    = $token;
                        $MailValidation->save();
                        $mail = new Mailer();
                        $mail->Send_Validation_Mail($email, $token);
                    }
                } else {
                    $error = "Please type identical password!";
                }
            } else {
                $error = "Password must have at least one number, one uppercase and one lowercase, and one special characters";
            }
        } else {
            $error = "Email already registred in database !";
        }
        return $error;
    }

    public function activate_account($token)
    {
        $verif = MailValidation::select('token', 'mail', 'datetime')->where('token', '=', $token)->get();
        if (count($verif) != 0) {
            $d2   = new \DateTime(date("Y-m-d H:i:s"));
            $diff = new \DateTime($verif[0]->datetime);
            $diff = $diff->diff($d2);
            if ($diff->format("%H") >= 01) {
                $verif = MailValidation::select('token', 'mail')->where('token', '=', $token)->delete();
                return false;
            } else {
                $write                  = Users::find($verif[0]->mail);
                $write->mail_validation = 1;
                if ($write->save()) {
                    $mail = new Mailer();
                    $mail->Send_mail_validation($verif[0]->mail);
                    $verif = MailValidation::select('token', 'mail')->where('token', '=', $token)->delete();
                    return true;
                }
            }
        } else {
            return false;
        }

    }

    public function recover_send_mail($email)
    {
        $verif = Users::select('mail')->where('mail', '=', $email)->get();
        if (count($verif) != 0) {
            $verif2 = LostPassword::select('token', 'mail', "datetime")->where('mail', '=', $email)->get();
            //if (count($verif2) != 0) {
            //    $token           = bin2hex(openssl_random_pseudo_bytes(32));
            //    $verif           = LostPassword::find($verif2[0]->mail);
            //    $verif->token    = $token;
            //    $verif->datetime = date("Y-m-d H:i:s");
            //    $verif->save();
            //    $mailer = new Mailer();
            //    $mailer->Send_Reset_Mail($email, $token);
            //} else {
            //    $token                  = bin2hex(openssl_random_pseudo_bytes(32));
            //    $lostpassword           = new LostPassword;
            //    $lostpassword->mail     = $verif[0]->mail;
            //    $lostpassword->token    = $token;
            //    $lostpassword->datetime = date("Y-m-d H:i:s");
            //    $lostpassword->save();
            //}
            if (count($verif2) == 0) {
                $token                  = bin2hex(openssl_random_pseudo_bytes(32));
                $lostpassword           = new LostPassword;
                $lostpassword->mail     = $verif[0]->mail;
                $lostpassword->token    = $token;
                $lostpassword->datetime = date("Y-m-d H:i:s");
                $lostpassword->save();
            } else {
                $token           = bin2hex(openssl_random_pseudo_bytes(32));
                $verif           = LostPassword::find($verif2[0]->mail);
                $verif->token    = $token;
                $verif->datetime = date("Y-m-d H:i:s");
                $verif->save();
            }
            $mailer = new Mailer();
            $mailer->Send_Reset_Mail($email, $token);

            return "Please check your email and click on the link to reset your password";
        } else {
            return "Mail not found in our database";
        }
    }

    public function check_token($token)
    {
        $verif = LostPassword::select('token', 'mail', "datetime")->where('token', '=', $token)->get();
        if (count($verif) != 0) {
            $d2   = new \DateTime(date("Y-m-d H:i:s"));
            $diff = new \DateTime($verif[0]->datetime);
            $diff = $diff->diff($d2);
            if ($diff->format("%H") >= 01) {
                $verif = LostPassword::select('token', 'mail', "datetime")->where('token', '=', $token)->delete();
                return false;
            } else {
                return $verif[0]->token;
            }
        } else {
            return false;
        }
    }

    public function change_password($token, $password, $passwordconfirm)
    {
        $verif = LostPassword::select('token', 'mail', "datetime")->where('token', '=', $token)->get();
        if (count($verif) != 0) {
            $d2   = new \DateTime(date("Y-m-d H:i:s"));
            $diff = new \DateTime($verif[0]->datetime);
            $diff = $diff->diff($d2);
            if ($diff->format("%H") >= 01) {
                $verif = LostPassword::select('token', 'mail', "datetime")->where('token', '=', $token)->delete();
                return "Invalid Token";
            } else {
                $verif2 = Users::find($verif[0]->mail);
                if (preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}/", $password)) {

                    if ($password == $passwordconfirm && empty(!$password)) {
                        $passwordtest = password_hash($password, PASSWORD_DEFAULT);
                        $verif2->mdp  = $passwordtest;
                        if ($verif2->save()) {
                            LostPassword::select('token', 'mail', "datetime")->where('token', '=', $token)->delete();
                            $mailer = new Mailer();
                            $mailer->Send_password_success($verif[0]->mail);

                            return "Password updated successfully";
                        }
                    } else {
                        return "Please type identical password!";
                    }
                } else {
                    return "Password invalid";
                }
            }
        } else {
            return "Invalid Token";
        }
    }

     public function getOrcid_id($email)
    {
        $verif                 = Users::select('ORCID_ID')->where('mail', '=', $email)->get();
        return $verif[0]->ORCID_ID;

    }

    public function getUserInfo($email)
    {
        $verif                 = Users::select('name', 'firstname','ORCID_ID')->where('mail', '=', $email)->get();
        $_SESSION['name']      = $verif[0]->name;
        $_SESSION['firstname'] = $verif[0]->firstname;
        return $verif;

    }

    public function setUserInfo($email, $name, $firstname,$ORCID_ID)
    {
        $verif            = Users::find($email);
        $verif->name      = $name;
        $verif->firstname = $firstname;
        $verif->ORCID_ID = $ORCID_ID;
        if ($verif->save()) {
            return true;
        } else {
            return false;
        }

    }

    public function getAllUsersApproved()
    {
        $verif = Users::where('status', '=', "1")->get();
        return $verif;

    }

    public function getAllUsersWaiting()
    {
        $verif = Users::where('status', '=', "0")->where('mail_validation', '=', "1")->get();
        if (count($verif) != 0) {
            return $verif;
        } else {
            return false;
        }
    }

    public function approveUser($email)
    {
        $verif         = Users::find($email);
        $verif->status = 1;
        if ($verif->save()) {
            $mail = new Mailer();
            $mail->Send_mail_account_activation($email);
            return true;
        } else {
            return false;
        }
    }

    public function disableUser($email)
    {
        $verif         = Users::find($email);
        $verif->status = 0;
        if ($verif->save()) {
            $mail = new Mailer();
            $mail->Send_mail_account_disable($email);
            return true;
        } else {
            return false;
        }
    }
    public function modifyUser($email, $name, $firstname, $type)
    {
        $verif            = Users::find($email);
        $verif->name      = $name;
        $verif->firstname = $firstname;
        $verif->type      = $type;
        if ($verif->save()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteUser($email)
    {
        $verif = Users::find($email)->delete();
        if ($verif == true) {
            $mail = new Mailer();
            $mail->Send_mail_account_removed($email);
            return true;
        } else {
            return false;
        }
    }

}
