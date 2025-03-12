<?php
session_start();
include("../settings/connect_datebase.php");
include("../recaptcha/autoload.php");
$secret = '6LcEDfIqAAAAAFzTdU4MHimn3-vBrOmzsTYcTaID';

if (isset($_POST['g-recaptcha-response'])) {
    $recaptcha = new \ReCaptcha\ReCaptcha($secret);
    $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

    if($resp->isSuccess()){
        //login

        $login = trim($_POST['login']);
        $password = trim($_POST['password']);
        
        $query_user = $mysqli->query("SELECT * FROM `users` WHERE `login` = '" . $mysqli->real_escape_string($login) . "'");
        if ($user = $query_user->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                // Генерация нового токена сессии
                $token = bin2hex(random_bytes(32));
        
                // Обновление токена в базе данных
                $update_query = $mysqli->query("UPDATE `users` SET `session_token` = '" . $mysqli->real_escape_string($token) . "' WHERE `id` = " . $user['id']);
                if (!$update_query) {
                    echo json_encode(["status" => "error", "message" => "Ошибка при обновлении токена."]);
                    exit;
                }
        
                // Установка данных сессии
                $_SESSION['user'] = $user['id'];
                $_SESSION['token'] = $token;
        
                // Успешная авторизация
                echo json_encode(["status" => "success", "message" => "Авторизация успешна."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Неверный логин или пароль."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Пользователь не найден."]);
        }

    } else {
        echo json_encode(["status" => "error", "message" => "Пользователь не распазнован"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Нет ответа от ReCaptcha"]);
}

?>