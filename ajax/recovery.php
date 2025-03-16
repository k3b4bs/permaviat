<?php
session_start();
include("../settings/connect_datebase.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

$login = trim($_POST['login']);

$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login` = '" . $mysqli->real_escape_string($login) . "'");
if ($user = $query_user->fetch_assoc()) {
    $new_password = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()"), 0, 12);
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $update_query = $mysqli->query("UPDATE `users` SET `password` = '" . $mysqli->real_escape_string($hashed_password) . "', `password_updated_at` = NOW() WHERE `id` = " . $user['id']);
    if (!$update_query) {
        echo json_encode(["status" => "error", "message" => "Ошибка при обновлении пароля."]);
        exit;
    }
    $mail = new PHPMailer(true);
    try {
		$mail->isSMTP();
		$mail->Host = 'smtp.yandex.ru';
		$mail->SMTPAuth = true;
		$mail->Username = 'leushkanovk3b4bs@yandex.ru';
		$mail->Password = 'oaaujhxazjeffarq';
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		$mail->Port = 465;
		$mail->CharSet = 'UTF-8';

         $mail->setFrom('leushkanovk3b4bs@yandex.ru', 'Леушканов Иван');
        $mail->addAddress($user['email']); 

        $mail->isHTML(true);
        $mail->Subject = 'Восстановление пароля';
        $mail->Body = "
            <h1>Восстановление пароля</h1>
            <p>Ваш новый пароль:</p>
            <p><b>$new_password</b></p> ";
        $mail->send();

        echo json_encode(["status" => "success", "message" => "Новый пароль отправлен на вашу почту."]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Не удалось отправить письмо с новым паролем."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Пользователь с таким логином не найден."]);
}
?>