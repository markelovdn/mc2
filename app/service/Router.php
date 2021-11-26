<?php
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {

    $r->addRoute('GET', '/', ['\app\ViewsController', 'index']);
    $r->addRoute('GET', '/404', ['\app\ViewsController', 'notFound']);
    $r->addRoute('GET', '/405', ['\app\ViewsController', 'notAlowed']);
    $r->addRoute('GET', '/registration', ['\app\ViewsController', 'registration']);
    $r->addRoute('GET', '/pagelogin', ['\app\ViewsController', 'login']);
    $r->addRoute('POST', '/login', ['\app\RegistrationController', 'login']);
    $r->addRoute('GET', '/logout', ['\app\RegistrationController', 'logoutUser']);
    $r->addRoute('POST', '/reguser', ['\app\RegistrationController', 'regUser']);
    $r->addRoute('GET', '/emailverify/{selector:.+}/{token:.+}', ['\app\RegistrationController', 'emailverify']);
    $r->addRoute('GET', '/users[/{num:\d+}]', ['\app\UsersController', 'getAll']);
    $r->addRoute('GET', '/create_user', ['\app\UsersController', 'formCreateUser']);
    $r->addRoute('POST', '/addGeneralInfo', ['\app\UsersController', 'addGeneralInfo']);
    $r->addRoute('GET', '/profile', ['\app\UsersController', 'openProfile']);
    $r->addRoute('GET', '/editform', ['\app\UsersController', 'formEditUserInfo']);
    $r->addRoute('POST', '/edituserinfo', ['\app\UsersController', 'editUserInfo']);
    $r->addRoute('GET', '/securityform', ['\app\UsersController', 'formEditSecurityUserInfo']);
    $r->addRoute('POST', '/editusersecurityinfo', ['\app\UsersController', 'editUserSecurityInfo']);
    $r->addRoute('GET', '/statusform', ['\app\UsersController', 'formEditStatusInfo']);
    $r->addRoute('POST', '/edituserstatusinfo', ['\app\UsersController', 'editUserStatusInfo']);
    $r->addRoute('GET', '/avatarform', ['\app\UsersController', 'formEditAvatarInfo']);
    $r->addRoute('POST', '/edituseravatarinfo', ['\app\UsersController', 'editUserAvatarInfo']);
    $r->addRoute('GET', '/dropuser', ['\app\UsersController', 'delUser']);


    $r->addRoute('GET', '/usersapi', ['\app\UsersController', 'getApi']);
    $r->addRoute('GET', '/fake', ['\app\FakeData', 'fakeData']);
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        header('Location: /404' );
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        header('Location: /405' );
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        $container->call($routeInfo[1], [$vars]);
        break;
}