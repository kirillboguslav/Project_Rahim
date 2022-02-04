<?php
session_start();
require_once 'function.php';

$gps = $_GET['gps']; // NULL!
$gps = explodeURL($gps);

switch ($gps) {
    case ($gps[0] == ''):
        require_once "users.php";
        break;        

    case ($gps[0] == 'page_login'):
        if(isLogIn()){
            header("location: /");
            break;
        }
        require_once "page_login.php";
        break;

    case ($gps[0] == 'page_register'):
        if(isLogIn()){
                header("location: /");
                break;
            }
        require_once "page_register.php";
        break;

    case ($gps[0] == 'zaloginit'):
        if($_POST['email'] == get_user_by_email($_POST['email'],$pdo) and password_verify($_POST['password'],get_user_by_password($_POST['email'],$pdo))){
            loginning($_POST['email'],$_POST['password'],$pdo);
            session_alert(5);
            header("location: /");
            break;
        }else{
            session_alert(1);
            header("location: /page_login");
            break;
        }
        header("location: /page_login");
        break;

    case ($gps[0] == 'logout'):
        session_unset();
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
                header("location: /page_login");
                break;
            }    
            session_alert(4);            
            header("location: /page_register");
            break;
        }
        header("location: /page_login");
        break;

    case ($gps[0] == 'edit' and !empty($gps[1])):
        require_once "edit.php";
        break;

    case ($gps[0] == 'edituser'):
        if($_FILES and $_POST){
            $massiv = $_POST;
            update_user($_FILES,$massiv,$pdo);
        }
        $massiv = $_POST;
        update_user($_FILES,$massiv,$pdo);
        header("location: /");
        break;     

    case ($gps[0] == 'status' and !empty($gps[1])):
        require_once "status.php";
        break;

    case ($gps[0] == 'statusedit'):
        $massiv = $_POST;
        update_user_status($massiv,$pdo);
        header("location: /");
        break;

    case ($gps[0] == 'deleteuser' and !empty($gps[1])):
        $user = get_user_by_allusers('users',$pdo);
        $id = (int) $gps[1] -1;        
        if($_SESSION['user'] != 'admin' and $_SESSION['userlogin'] != $user[$id]['login']){
        header("location: /page_login");
        }
        delete_user($user[$id]['id'],$pdo);
        session_unset();
        header("location: /");
        break;

    case ($gps[0] == 'security' and !empty($gps[1])):
        require_once "security.php";
        break;

    case ($gps[0] == 'updatesecurity'):
        $massiv = $_POST;
        update_user($_FILES,$massiv,$pdo);
        break;

        case ($gps[0] == 'media' and !empty($gps[1])):
        require_once "media.php";
        break;

        case ($gps[0] == 'create_user'):
        require_once "create_user.php";
        break;

    default:
    require_once "404.php";
}

?>