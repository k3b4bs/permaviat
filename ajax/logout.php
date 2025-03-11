<?php
	session_start();
	require_once("../settings/connect_datebase.php");

	if (isset($_SESSION['user'])) {
		$user_id = $_SESSION['user'];
	
		$mysqli->query("UPDATE `users` SET `session_token` = NULL WHERE `id` = " . $user_id);
	
		session_destroy();
	
		echo json_encode(["status" => "success", "message" => "Вы успешно вышли из системы."]);
	} else {
		echo json_encode(["status" => "error", "message" => "Вы уже вышли из системы."]);
	}
