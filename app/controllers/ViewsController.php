<?php

namespace app;

use League\Plates\Engine;

class ViewsController
{
    private $template;

    public function __construct(Engine $engine)
    {
        $this->template = $engine;
    }

    public function index()
    {
        echo $this->template->render('main');
    }

    public function notFound()
    {
        echo $this->template->render('404');
    }

    public function notAlowed()
    {
        echo $this->template->render('405');
    }

    public function registration()
    {
        echo $this->template->render('registration');
    }

    public function login()
    {
        echo $this->template->render('login');
    }

    public function profile()
    {
        echo $this->template->render('profile');
    }

}