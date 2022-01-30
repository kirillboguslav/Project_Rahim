<?php
session_start();
require_once 'function.php';
// $conn = connect();
$gps = $_GET['gps']; // NULL!
$gps = explodeURL($gps);

switch ($gps) {
    case ($gps[0] == ''):
        require_once "page_login.php";
        break;

    case ($gps[0] == 'page_register'):
        require_once "page_register.php";
        break;


    case ($gps[0] == 'zaloginit'):
        if($_POST['email'] == get_user_by_email($_POST['email'],$pdo) and password_verify($_POST['password'],get_user_by_password($_POST['email'],$pdo))){
            session_alert(5);
            header("location: /");
            break;
        }else{
            session_alert(1);
            header("location: /");
            break;
        }
        header("location: /");
        break;

///////////регистрация пользывателя 
    case ($gps[0] == 'registaration'):
        if($_POST['email'] == get_user_by_email($_POST['email'],$pdo)){
            session_alert(2);
            header("location: /page_register");
            break;
        }else{
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            if($email == true){
                add_user($_POST['email'], $_POST['password'],$pdo);
                session_alert(3);
                header("location: /");
                break;
            }    
            session_alert(4);            
            header("location: /page_register");
            break;
        }
        header("location: /");
        break;

    default:
    require_once "404.php";
}

?>