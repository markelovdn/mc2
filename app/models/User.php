<?php


namespace app\models;

use Delight\Auth\Auth;
use Tamtamchik\SimpleFlash\Flash;


class User
{
    private $auth;
    private $flash;

    public function __construct(Auth $auth, Flash $flash)
    {
        $this->auth = $auth;
        $this->flash = $flash;
    }

    public function getEmail() {
        return $this->auth->getEmail();
    }

    public function getRole() {

        $userRole = $this->auth->getRoles();
        return $userRole[1];
    }

    public function getUserId() {
        $userId = $this->auth->getUserId();
        return $userId;
    }

    public function uploadImg($img, $id) {
        $file_name = '/img/userfoto/'.$id."_photo".".jpg";
        $dir=$_SERVER['DOCUMENT_ROOT'];
        move_uploaded_file($img, $dir.$file_name);
        return $file_name;
    }

    public function addNewUserInfo()
    {
        try {
            $userId = $this->auth->register($_POST['email'], $_POST['password'], $_POST['username'], function ($selector, $token) {
                $this->auth->confirmEmailAndSignIn($selector, $token);
            });
            return $userId;
        } catch (\Delight\Auth\InvalidEmailException $e) {
            $flash = $this->flash;
            $flash->error('Email адрес указан не верно');
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            $flash = $this->flash;
            $flash->error('Пароль должен содержать не менее 6 символов');
        } catch (\Delight\Auth\UserAlreadyExistsException $e) {
            $flash = $this->flash;
            $flash->error('Пользователь с таким Email уже зарегистрирован');
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
    }

    public function checkLogin() {
            if (!$this->auth->isLoggedIn()) {
                $flash = $this->flash;
                $flash->error('Войдите в систему для доступа к сайту');
                header('Location: /');
                exit;
            }
        }

    public function checkAdminPermission()
    {
        $userId = $this->auth->getUserId();
        if ($this->auth->hasRole(\Delight\Auth\Role::ADMIN) || $userId == $_GET['id']) {
            return true;
        }
        $this->flash->warning('Доступ запрещен');
        header('Location: /users');
        exit;
    }

    public function changePass () {
        $userId = $_POST['id'];
        if ($_POST['newPassword']!=$_POST['rePassword']) {
            $this->flash->error('Пароли не совпадают');
            header("Location: /securityform?id=$userId");
            exit;
        }

        try {
            $this->auth->changePassword($_POST['oldPassword'], $_POST['newPassword']);
            return true;
        }
        catch (\Delight\Auth\NotLoggedInException $e) {
            die('Not logged in');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            $flash = $this->flash;
            $flash->error('Не верный старый пароль');
            header("Location: /securityform?id=$userId");
            exit();
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
    }

    public function changeEmail() {
        $userId = $_POST['id'];

            try {
                    $this->auth->changeEmail($_POST['email'], function ($selector, $token) {
                        $this->auth->confirmEmailAndSignIn($selector, $token);});
            }
            catch (\Delight\Auth\InvalidEmailException $e) {
                $this->flash->error('Invalid email address');
                header("Location: /securityform?id=$userId");
                exit;
            }
            catch (\Delight\Auth\UserAlreadyExistsException $e) {
                $this->flash->error('Email address already exists');
                header("Location: /securityform?id=$userId");
                exit;
            }
            catch (\Delight\Auth\EmailNotVerifiedException $e) {
                die('Account not verified');
            }
            catch (\Delight\Auth\NotLoggedInException $e) {
                die('Not logged in');
            }
            catch (\Delight\Auth\TooManyRequestsException $e) {
                die('Too many requests');
            }
        }



}