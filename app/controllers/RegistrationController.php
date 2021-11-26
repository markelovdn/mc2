<?php

namespace app;

use Delight\Auth\Auth;
use SimpleMail;
use Tamtamchik\SimpleFlash\Flash;


class RegistrationController
{
    private $auth;
    private $mail;
    private $flash;


    public function __construct(Auth $auth, SimpleMail $mail, Flash $flash)
    {
        $this->auth = $auth;
        $this->mail = $mail;
        $this->flash = $flash;
    }

    public function regUser() {

        try {
            $userId = $this->auth->register($_POST['email'], $_POST['password'], $_POST['username'], function ($selector, $token) {
                $this->sendEmail($selector, $token);
                $this->flash->success('Проверьте почту и подтвердите свой Email');
                header('Location: /login');
                exit;
            });
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            $this->flash->error('Email адрес указан не верно');
            header('Location: /login');
            exit;
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            $this->flash->error('Пароль должен содержать не менее 6 символов');
            header('Location: /login');
            exit;
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
        $this->flash->error('Пользователь с таким Email уже зарегистрирован');
            header('Location: /login');
            exit;
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
            exit;
        }
    }

    public function sendEmail($selector=null, $token=null) {
        $mail = $this->mail;
        $mail::make()
            ->setTo($_POST['email'], $_POST['username'])
            ->setFrom('markelovdn@gmail.com', 'Markelov')
            ->setSubject('New thems')
            ->setHtml(true)
            ->setMessage('Для подтверждения Email перейдите по ссылке <a href="http://mc2/emailverify/selector:'.$selector.'/token:'.$token.'">Подтвердить Email</a>')
            ->send();
    }

    public function login() {

        try {
            $this->auth->login($_POST['email'], $_POST['password']);
            header('Location: /users');
            exit;
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            $this->flash->error('Email или пароль указаны не верно');
            header('Location: /login');
            exit;
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            $this->flash->error('Email адрес или пароль указаны не верно');
            header('Location: /login');
            exit;
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            $this->flash->error('Email не верифицирован');
            header('Location: /login');
            exit;
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            $this->flash->error('Too many requests');
            header('Location: /login');
            exit;
        }

    }

    public function logoutUser() {
        $this->auth->logOut();
        header('Location: /');
        exit;
    }

    public function emailverify() {
        //Пришлось сделать так, потому-что не смог передать в метод параметры селектор и токен
        $explode = explode(':', $_SERVER['REQUEST_URI']);
        $selector = stristr($explode[1], '/', true);
        $token = $explode[2];

        try {
            $this->auth->confirmEmail($selector, $token);
            $this->flash->success('Email успешно верифицирован');
            header('Location: /login');
            exit;
        }
        catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            $this->flash->error('Invalid token');
            header('Location: /login');
            exit;
        }
        catch (\Delight\Auth\TokenExpiredException $e) {
            $this->flash->error('Token expired');
            header('Location: /login');
            exit;
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            $this->flash->error('Email address already exists');
            header('Location: /login');
            exit;
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            $this->flash->error('Too many requests');
            header('Location: /login');
            exit;
        }

    }

    public function asLogin ($id)
    {
        try {
            $this->auth->admin()->logInAsUserById($id);
        }
        catch (\Delight\Auth\UnknownIdException $e) {
            $this->flash->error('Unknown ID');
            header("Location: /users");
            exit;
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            $this->flash->error('Email address not verified');
            header("Location: /users");
            exit;
        }
    }

}

