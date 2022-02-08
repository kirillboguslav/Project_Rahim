<?php

function explodeURL($url)
{
    return explode("/", $url);
    // функция для расделение слешом
}
function get_user_by_email($email){
    $pdo = new PDO("mysql:host=localhost; dbname=projectrahim", "root", "");
    $sql = "SELECT * FROM `users` WHERE email = :email";
    $statement = $pdo->prepare($sql);
    $statement->execute(['email' => $email]);
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result;
}
function get_user_by_id($id){
    $pdo = new PDO("mysql:host=localhost; dbname=projectrahim", "root", "");
    $sql = "SELECT * FROM `users` WHERE id = :id";
    $statement = $pdo->prepare($sql);
    $statement->execute(['id' => $id]);
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result;
}

//////////все проверки на существующий email в index.php
function add_user($email, $password){
    $pdo = new PDO("mysql:host=localhost; dbname=projectrahim", "root", "");
    $sql = "INSERT INTO `users` (email, password, role) VALUES (:email, :password, :user)";
    $statement = $pdo->prepare($sql);
    $statement->execute([
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'user' => 'user'
        ]);
    return $pdo->lastInsertId();
}

//function set_flash_message($key, $massage){}
// Привет я тчательно изучил все твои видео и сделал как ты показал, скажи пожалуйста можно обойтись без этой функции?
// на странице page_register делаю вот так:
//if ($_SESSION['alert']) {
//    echo '<div class="alert alert-' . $_SESSION['alert'] . '">' . $_SESSION['message'] . '</div>';
//}
//session_unset();

function display_flash_message($key,$message){
    $_SESSION['alert'] = $key;
    $_SESSION['message'] = $message;
}

function redirect_to($path){
    header("location: ".$path."");
    exit;
}

function login($email, $password){
    $user = get_user_by_email($email);
    if($email == $user['email'] and password_verify($password,$user['password'])){
        $_SESSION['user'] = $user;
        display_flash_message('info', 'Авторизация успешна - '.$_SESSION['user']['email']);
        redirect_to('/users');
    }
    display_flash_message('danger', 'Не правильный логин или пароль!');
    redirect_to('page_login');
}

function is_not_logged_in (){
    if(!$_SESSION['user']){
        redirect_to('/');
    }
}
function is_admin(){
    if($_SESSION['user']['role'] == 'admin'){
        return true;
    }
}

function is_author($logged_user_id, $edit_user_id){
    if($logged_user_id == $edit_user_id){
        return true;
    }
    display_flash_message('danger', 'Можно править только свой профиль!');
    redirect_to('/users');
}

function get_users(){
    $pdo = new PDO("mysql:host=localhost; dbname=projectrahim", "root", "");
    $sql = "SELECT * FROM `users`";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $array = $statement->fetchALL(PDO::FETCH_ASSOC);
    return $array;
}
////////////// можно ли так делать ??? что бы тоже самое не дублировать на странице юзерс
function status_id($type){
    if($type == 'Онлайн'){
        echo '<span class="status status-success mr-3">';
    }
    if($type == 'Отошел'){
        echo '<span class="status status-warning mr-3">';
    }
    if($type == 'Не беспокоить'){
        echo '<span class="status status-danger mr-3">';
    }
}

function edit_info($user_id, $name, $work, $phone, $address){
    $pdo = new PDO("mysql:host=localhost; dbname=projectrahim", "root", "");
    $sql = "UPDATE `users` SET `name`=:name, `work`=:work, `phone`=:phone, `address`=:address WHERE `users`.`id`= :user_id";
    $statement = $pdo->prepare($sql);
    $statement->execute([
        'name' => $name,
        'work' => $work,
        'phone' => $phone,
        'address' => $address,
        'user_id' => $user_id
    ]);
}

function set_status($user_id, $status){
    $pdo = new PDO("mysql:host=localhost; dbname=projectrahim", "root", "");
    $sql = "UPDATE `users` SET `status`=:status WHERE `users`.`id`= :user_id";
    $statement = $pdo->prepare($sql);
    $statement->execute([
        'status' => $status,
        'user_id' => $user_id
    ]);
}

function upload_avatar($user_id, $image){
    $pdo = new PDO("mysql:host=localhost; dbname=projectrahim", "root", "");
    $result = pathinfo($image['avatar']['name']);
    $result = uniqid() .".".$result['extension'];
        if(!empty($result)){
            $sql = "UPDATE `users` SET `avatar`=:avatar WHERE `users`.`id`=:user_id";
            $statement = $pdo->prepare($sql);
            $statement->execute([
                    'avatar' => $result,
                    'user_id' => $user_id
            ]);
            move_uploaded_file($image['avatar']['tmp_name'], 'img/demo/avatars/'.$result);
        }
}

//////////////вот тут не очень уверен что сделал правильно

function edit_credentials($user_id, $email, $password){
    $user = get_user_by_email($email);

    if($user and !is_author($_SESSION['user'],$user)){
            display_flash_message('danger', 'Такой емейл уже существует!' );
            redirect_to('/security/'.$user_id);
    }

    $pdo = new PDO("mysql:host=localhost; dbname=projectrahim", "root", "");
    $sql = "UPDATE `users` SET `email`=:email,`password`=:password WHERE `users`.`id`= :user_id";
    $statement = $pdo->prepare($sql);
    $statement->execute([
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'user_id' => $user_id
    ]);
    display_flash_message('info', 'Логин/Пароль успешно обновлен');
}

function delete($user_id){
    //////честно говоря с этими проверками запутался, по другому не могу придумать, подскажи пожалуйста
    if(!is_admin()){
        if(is_not_logged_in() or !is_author($_SESSION['user'],$user)){
            display_flash_message('danger', 'Удалять может только админ!');
            redirect_to('/page_login');
        }
    }
    //////////поиск и удаление картинки
    $pdo = new PDO("mysql:host=localhost; dbname=projectrahim", "root", "");
    $sql = "SELECT * FROM `users` WHERE `users`.`id`= :user_id";
    $statement = $pdo->prepare($sql);
    $statement->execute(['user_id' => $user_id]);
    $array = $statement->fetch(PDO::FETCH_ASSOC);

    unlink('img/demo/avatars/'.$array['avatar']);
    /////////удаления пользывателя по айдишнику
    $sql = "DELETE FROM `users` WHERE `users`.`id`= :user_id";
    $statement = $pdo->prepare($sql);
    $statement->execute(['user_id' => $user_id]);
    session_unset();
    redirect_to('/page_login');

}









