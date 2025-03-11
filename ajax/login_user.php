<?php
session_start();
include("../settings/connect_datebase.php");

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
?>