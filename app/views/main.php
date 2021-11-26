<?php $this->layout('template/default_header', ['title' => 'Главная страница']);?>

    <div class="page-wrapper auth">
        <div class="page-inner bg-brand-gradient">
            <div class="page-content-wrapper bg-transparent m-0">
                <div class="height-10 w-100 shadow-lg px-4 bg-brand-gradient">
                    <div class="d-flex align-items-center container p-0">
                        <div class="page-logo width-mobile-auto m-0 align-items-center justify-content-center p-0 bg-transparent bg-img-none shadow-0 height-9 border-0">
                            <a href="javascript:void(0)" class="page-logo-link press-scale-down d-flex align-items-center">
                                <img src="/img/logo.png" alt="SmartAdmin WebApp" aria-roledescription="logo">
                                <span class="page-logo-text mr-1">Учебный проект</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="flex-1" style="background: url(/img/svg/pattern-1.svg) no-repeat center bottom fixed; background-size: cover;">

                    <div class="container py-4 py-lg-5 my-lg-5 px-4 px-sm-0">
                        <?php if(isset($_SESSION['flash_messages'])) {echo flash()->display();} ?>
                        <div class="row">
                            <div class="col-xl-6 ml-auto mr-auto">
                                <div class="row no-gutters">
                                    <div class="col-md ml-auto text-right">
                                        <a href="/registration" class="btn btn-block btn-danger btn-lg mt-3">Зарегистрироваться</a>
                                    </div>
                                </div>

                                <div class="row no-gutters">
                                    <div class="col-md ml-auto text-right">
                                        <a href="/pagelogin" class="btn btn-block btn-success btn-lg mt-3">Войти</a>
                                    </div>
                                </div>

                                <div class="row no-gutters">
                                    <div class="col-md ml-auto text-right">
                                        <a href="/users" class="btn btn-block btn-success btn-lg mt-3">Все контакты</a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


