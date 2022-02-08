<?php
session_start();
require_once 'function.php';

$gps = $_GET['gps']; // NULL!
$gps = explodeURL($gps);

switch ($gps) {
    case ($gps[0] == ''):
        require_once "page_register.php";
        break;

    case ($gps[0] == 'register'):
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) and !get_user_by_email($_POST['email'])){
            add_user($_POST['email'], $_POST['password']);
            display_flash_message('info', 'Вы успешно зарегились');
            redirect_to('/page_login');
            break;
        }
        display_flash_message('danger', 'Такой пользыватель уже есть');
        redirect_to('/');
        break;

    case ($gps[0] == 'page_login'):
        require_once "page_login.php";
        break;

    case ($gps[0] == 'authorization'):
        login($_POST['email'], $_POST['password']);
        break;

    case ($gps[0] == 'users'):
        require_once "users.php";
        break;

    case ($gps[0] == 'create_user'):
        require_once "create_user.php";
        break;

    case ($gps[0] == 'add_new_user'):
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) and !get_user_by_email($_POST['email'])){
            $user_id = add_user($_POST['email'], $_POST['password']);
            edit_info($user_id, $_POST['name'], $_POST['work'], $_POST['phone'], $_POST['address']);
            set_status($user_id, $_POST['status']);
            upload_avatar($user_id, $_FILES);
            display_flash_message('info', 'Пользыватель успешнео добавлен!');
            redirect_to('/users');
            break;
        }
        display_flash_message('danger', 'Такой пользыватель уже есть');
        redirect_to('/create_user');
        break;

    case ($gps[0] == 'edit' and !empty($gps[1])):
        $user = get_user_by_id($gps[1]);
        require_once "edit.php";
        break;

    case ($gps[0] == 'edituser'):
        edit_info($_POST['id'], $_POST['name'], $_POST['work'], $_POST['phone'], $_POST['address']);
        display_flash_message('info', 'Пользыватель успешно отредактирован - '.$_POST['name']);
        redirect_to('/users');
        break;

    case ($gps[0] == 'page_profile' and !empty($gps[1])):
        $user = get_user_by_id($gps[1]);
        require_once "page_profile.php";
        break;

    case ($gps[0] == 'security' and !empty($gps[1])):
        $user = get_user_by_id($gps[1]);
        require_once "security.php";
        break;

    case ($gps[0] == 'update_security' ):
        edit_credentials($_POST['id'], $_POST['email'], $_POST['password']);
        redirect_to('/users');
        break;

    case ($gps[0] == 'status' and !empty($gps[1])):
        $user = get_user_by_id($gps[1]);
        require_once "status.php";
        break;

    case ($gps[0] == 'setstatus' ):
        set_status($_POST['id'], $_POST['status']);
        display_flash_message('info', 'Статус успешно изменен');
        redirect_to('/users');
        break;

    case ($gps[0] == 'media' and !empty($gps[1])):
        $user = get_user_by_id($gps[1]);
        require_once "media.php";
        break;

    case ($gps[0] == 'update_avatar'):
        upload_avatar($_POST['id'], $_FILES);
        display_flash_message('info', 'Картинка успешно обновлена');
        redirect_to('/users');
        break;

    case ($gps[0] == 'delete_user' and !empty($gps[1])):
        delete($gps[1]);
        break;

    case ($gps[0] == 'logout'):
        session_unset();
        redirect_to('/');
        break;

    default:
    require_once "404.php";
}

?>