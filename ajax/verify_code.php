<?php
session_start();
include("../settings/connect_datebase.php");

if (!isset($_SESSION['temp_user'])) {
    echo json_encode(["status" => "error", "message" => "Сессия истекла. Повторите регистрацию."]);
    exit;
}

$input_code = trim($_POST['code']);
$temp_user = $_SESSION['temp_user'];

if ($input_code === $temp_user['code']) {
    $token = bin2hex(random_bytes(32));
    $update_query = $mysqli->query("UPDATE `users` SET `verification_code` = NULL, `session_token` = '" . $mysqli->real_escape_string($token) . "' WHERE `id` = " . $temp_user['id']);
    if ($update_query) {
        $_SESSION['user'] = $temp_user['id'];
        $_SESSION['token'] = $token;
        unset($_SESSION['temp_user']);
        echo json_encode(["status" => "success", "message" => "Регистрация успешно завершена."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Ошибка при подтверждении регистрации."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Неверный код."]);
}
?>