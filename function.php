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

    $sql = "INSERT INTO `users_info` (name, avatar, work, phone, address, status) VALUES (:name,:avatar, :work, :phone, :address, :status)";
    $result = $pdo->prepare($sql);
    $result->execute([
        'name' => 'Имя'
        ,'avatar' => 'avatar-b.png'
        ,'work' => 'род деятельности'
        ,'phone' => '999-999-99-99'
        ,'address' => 'Страна'
        ,'status' => '1'
    ]);
}


//////////////////////////////////////  Взять всех юзеров из базы!!!
function get_user_by_allusers($tabl,$pdo){
    $sql = "SELECT * FROM `".$tabl."`";
    $result = $pdo->prepare($sql);
    $result->execute();
    $user = $result->fetchALL(PDO::FETCH_ASSOC);
    return $user;
}


function loginning($email,$password,$pdo){
    $sql = "SELECT * FROM `users` WHERE login = :email";
    $result = $pdo->prepare($sql);
    $result->execute(['email' => $email]);
    $pas = $result->fetch(PDO::FETCH_ASSOC);    
    $_SESSION['user'] = $pas['role'];
    $_SESSION['userlogin'] = $pas['login'];
}

function isLogIn(){
    return $_SESSION['userlogin'];
}
//////////////////////////////////////  Обновление данных юзера
function update_user($file = null,$massiv,$pdo){
    if(!empty($massiv['email'])){
        if(password_verify($massiv['password_old'], get_user_by_password($_POST['email'],$pdo))){

            $password = password_hash($massiv['password_new'], PASSWORD_DEFAULT);   
            $sql = "UPDATE `users` SET `login`=:login, `password`=:password WHERE `users`.`id`=:id";    
            $result = $pdo->prepare($sql);
            $result->execute(
                [
                    'login' => $massiv['email'],
                    'password' => $password,
                    'id' => $massiv['id']
                ]
            );
            session_alert(6);
            session_unset();
            header("location: /");
        }
        header("location: /");
        exit;
    }

    if( !empty($_FILES and $massiv) ){

        $sql = "SELECT * FROM `users_info` WHERE `users_info`.`id`=:id";
        $res1 = $pdo->prepare($sql);
        $res1->execute(['id' => $massiv['id']]);
        $f = $res1->fetch(PDO::FETCH_ASSOC);
        $f = 'img/demo/avatars/'.$f['avatar'];
        unlink($f);

        $result = pathinfo($_FILES['avatar']['name']);
        $result = uniqid() .".".$result['extension'];
        if(!empty($result)){        
        $sql = "UPDATE `users_info` SET `avatar`=:avatar WHERE `users_info`.`id`=:id";
        $res = $pdo->prepare($sql);
        $res->execute(
            [
                'avatar' => $result,
                'id' => $massiv['id']
            ]
        );
        move_uploaded_file($_FILES['avatar']['tmp_name'], 'img/demo/avatars/'.$result);
        }
        header("location: /");
        exit;
    }

    $sql = "UPDATE `users_info` SET `name`=:name,`work`=:work,`phone`=:phone,`address`=:address WHERE `users_info`.`id`=:id";
    $result = $pdo->prepare($sql);
    $result->execute([
        'name' => $massiv['name']
        ,'work' => $massiv['work']
        ,'phone' => $massiv['phone']
        ,'address' => $massiv['address']
        ,'id' => $massiv['id']
    ]);

}

//////////////////////////////////////  Обновление статуса юзера
function update_user_status($massiv,$pdo){
    $sql = "UPDATE `users_info` SET `status`=:status WHERE `users_info`.`id`=:id";
    $result = $pdo->prepare($sql);
    $result->execute([
        'status' => $massiv['setstatus']
        ,'id' => $massiv['id']
    ]);
}

//////////////////////////////////////  Обработка статуса
function status_id($id){
    if($id == 1){
        echo '<span class="status status-success mr-3">';
    }
    if($id == 2){
        echo '<span class="status status-warning mr-3">';
    }
    if($id == 3){
        echo '<span class="status status-danger mr-3">';
    }
}

//////////////////////////////////////  Удаления юзера
function delete_user($id,$pdo){
    $sql = "DELETE FROM `users_info` WHERE `users_info`.`id`=:id";
    $result = $pdo->prepare($sql);
    $result->execute(['id' => $id]);

    $sql = "DELETE FROM `users` WHERE `users`.`id`=:id";
    $result = $pdo->prepare($sql);
    $result->execute(['id' => $id]);


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
        $_SESSION['alert'] = '<div class="alert alert-primary">Вы успешно залогинились как - '.$_SESSION['userlogin'].'</div>';
    }
    if($mess == 6){
        $_SESSION['alert'] = '<div class="alert alert-danger">Вы успешно изменили логин и пароль залогинтесь снова!</div>';
    }
    return $_SESSION['alert'];
}
?>