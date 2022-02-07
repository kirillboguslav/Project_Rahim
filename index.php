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




    case ($gps[0] == 'logout'):
        session_unset();
        redirect_to('/');
        break;


    default:
    require_once "404.php";
}

?>