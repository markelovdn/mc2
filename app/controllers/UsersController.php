<?php

namespace app;
use app\models\QueryBuilder;
use League\Plates\Engine;
use app\models\User;
use Tamtamchik\SimpleFlash\Flash;
use app\RegistrationController;
use JasonGrimes\Paginator;

class UsersController
{
    private $qb;
    private $views;
    private $usermodel;
    private $flash;
    private $reg;

    public function __construct(QueryBuilder $qb, Engine $views, User $usermodel, Flash $flash, RegistrationController $reg)
    {
        $this->qb = $qb;
        $this->views = $views;
        $this->usermodel = $usermodel;
        $this->flash = $flash;
        $this->reg = $reg;
    }

    public function getAll($vars)
    {
        $this->usermodel->checkLogin();
        $itemsPerPage = 3;
        $currentPage = $vars['num'];
        $urlPattern = '/users/(:num)';

        $total = $this->qb->getAll('users');

        $users = $this->qb->getPagination('users', $itemsPerPage, $vars['num'] ?? 1);

        $paginator = new Paginator(count($total), $itemsPerPage, $currentPage, $urlPattern);

        echo $this->views->render('users',
            [
                'users' => $users,
                'paginator' => $paginator
            ]);

        d($this->usermodel->getEmail(), $this->usermodel->getRole(), $this->usermodel->getUserId(), $paginator);
    }

    public function openProfile()
    {
        $user = $this->qb->getOne('users', $_GET['id']);
        echo $this->views->render('profile', ['user' => $user]);
    }

    public function formCreateUser()
    {
        $this->usermodel->checkAdminPermission();
        echo $this->views->render('create_user');
    }

    public function addGeneralInfo()
    {
        $userId = $this->usermodel->addNewUserInfo();
        $photo = $this->usermodel->uploadImg($_FILES['photo']['tmp_name'], $userId);
        $data = [
            'username' => $_POST['username'],
            'workplace' => $_POST['workplace'],
            'mobile' => $_POST['mobile'],
            'adres' => $_POST['adres'],
            'vk' => $_POST['vk'],
            'telegram' => $_POST['telegram'],
            'instagram' => $_POST['instagram'],
            'status' => $_POST['status'],
            'photo' => $photo
        ];

        $this->qb->update('users', $data, $userId);
        header('Location: /users');
    }

    public function formEditUserInfo()
    {
        $this->usermodel->checkAdminPermission();

        $user = $this->qb->getOne('users', $_GET['id']);
        echo $this->views->render('edit', ['user' => $user]);

    }

    public function editUserInfo()
    {
        $data = [
            'username' => $_POST['username'],
            'workplace' => $_POST['workplace'],
            'mobile' => $_POST['mobile'],
            'adres' => $_POST['adres'],
        ];
        $this->qb->update('users', $data, $_POST['id']);
        $this->flash->success('Данные пользователя успешно обновлены');
        header('Location: /users');
    }

    public function formEditSecurityUserInfo()
    {
        if ($this->usermodel->checkAdminPermission()) {
            if ($this->usermodel->getRole() == 'ADMIN' and $this->usermodel->getUserId() != $_GET['id']) {
                $this->reg->asLogin($_GET['id']);
                $this->flash->warning('В настоящее время вы находитесь в системе как ' . $this->usermodel->getEmail() . ' Для возврата к своей учетной записи вам необходимо повторить вход');
            }
            $user = $this->qb->getOne('users', $_GET['id']);
            echo $this->views->render('security', ['user' => $user]);
            exit;
        }

    }

    public function editUserSecurityInfo()
    {
        $this->usermodel->checkAdminPermission();
        if ($_POST['newPassword'] == '' and $this->usermodel->getEmail() == $_POST['email']) {
            header('Location: /users');
            exit;
        } elseif ($_POST['newPassword'] != '' and $this->usermodel->getEmail() == $_POST['email']) {
            $this->usermodel->changePass();
            $this->flash->success('Данные пользователя успешно обновлены');
            header('Location: /users');
            exit;
        } elseif($_POST['newPassword'] == '' and $this->usermodel->getEmail() != $_POST['email']) {
            $this->usermodel->changeEmail();
            $this->flash->success('Данные пользователя успешно обновлены');
            header('Location: /users');
            exit;
        } elseif($_POST['newPassword'] != '' and $this->usermodel->getEmail() != $_POST['email']) {
            $this->usermodel->changePass();
            $this->usermodel->changeEmail();
            $this->flash->success('Данные пользователя успешно обновлены');
            header('Location: /users');
            exit;
        }
    }

    public function formEditStatusInfo()
    {
        $this->usermodel->checkAdminPermission();

        $user = $this->qb->getOne('users', $_GET['id']);
        echo $this->views->render('status', ['user' => $user]);

    }

    public function editUserStatusInfo()
    {
        $data = [
            'status' => $_POST['status'],
        ];
        $this->qb->update('users', $data, $_POST['id']);
        $this->flash->success('Данные пользователя успешно обновлены');
        header('Location: /users');
    }

    public function formEditAvatarInfo()
    {
        $this->usermodel->checkAdminPermission();

        $user = $this->qb->getOne('users', $_GET['id']);
        echo $this->views->render('media', ['user' => $user]);

    }

    public function editUserAvatarInfo()
    {
        $photo = $this->usermodel->uploadImg($_FILES['photo']['tmp_name'], $_POST['id']);
        $data = [
            'photo' => $photo
        ];
        $this->qb->update('users', $data, $_POST['id']);
        $this->flash->success('Данные пользователя успешно обновлены');
        header('Location: /users');
    }

    public function delUser()
    {
        $this->usermodel->checkAdminPermission();
        $this->qb->delete('users', $_GET['id']);
        $this->flash->warning('Пользователь удален');
        $this->reg->logoutUser();
    }

    public function getApi() {
        $this->usermodel->checkLogin();
        $total = $this->qb->getAll('users');
        echo json_encode($total);
    }

}
