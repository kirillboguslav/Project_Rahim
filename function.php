<?php
$pdo = new PDO("mysql:host=localhost; dbname=projectrahim", "root", "");     //Подкл к базе

function explodeURL($url)
{
    return explode("/", $url);
    // функция для расделение слешом
}
/////////////////////////////////////////////////////////////

function get_user_by_email($email,$pdo){
    $sql = "SELECT * FROM `users` WHERE login = :email";
    $result = $pdo->prepare($sql);
    $result->execute(['email' => $email]);
    $email = $result->fetch(PDO::FETCH_ASSOC);
    return $email['login'];
}

function get_user_by_password($email,$pdo){
    $sql = "SELECT * FROM `users` WHERE login = :email";
    $result = $pdo->prepare($sql);
    $result->execute(['email' => $email]);
    $pas = $result->fetch(PDO::FETCH_ASSOC);
    return $pas['password'];
}

function add_user($email,$password,$pdo){
    $password = password_hash($password, PASSWORD_DEFAULT);    
    $sql = "INSERT INTO `users` (login, password) VALUES (:login,:password)";
    $result = $pdo->prepare($sql);
    $result->execute(
        [
            'login' => $email,
            'password' => $password
        ]
    );
}

function session_alert($mess){
    if($mess == 1){
        $_SESSION['alert'] = '<div class="alert alert-danger">Такой логин и/или пароль не существует!</div>';
    }
    if($mess == 2){
        $_SESSION['alert'] = '<div class="alert alert-danger">Такой логин уже занят!</div>';
    }
    if($mess == 3){
        $_SESSION['alert'] = '<div class="alert alert-success">Вы успешно зарегистрировались</div>';
    }
    if($mess == 4){
        $_SESSION['alert'] = '<div class="alert alert-danger">Не корректный логин!</div>';
    }
    if($mess == 5){
        $_SESSION['alert'] = '<div class="alert alert-primary">Вы успешно залогинились</div>';
    }

    return $_SESSION['alert'];

}
?>