<?php
session_start();
include("./settings/connect_datebase.php");

if (isset($_SESSION['user'])) {
	if ($_SESSION['user'] != -1) {
		$user_query = $mysqli->query("SELECT * FROM `users` WHERE `id` = " . $_SESSION['user']); // проверяем
		while ($user_read = $user_query->fetch_row()) {
			if ($user_read[3] == 0) header("Location: index.php");
		}
	} else header("Location: login.php");
} else {
	header("Location: login.php");
	echo "Пользователя не существует";
}

include("./settings/session.php");
?>
<!DOCTYPE HTML>
<html>

<head>
	<script src="https://code.jquery.com/jquery-1.8.3.js"></script>
	<meta charset="utf-8">
	<title> Admin панель </title>

	<link rel="stylesheet" href="style.css">
</head>

<body>
	<div class="top-menu">

		<a href=#><img src="img/logo1.png" /></a>
		<div class="name">
			<a href="index.php">
				<div class="subname">БЗОПАСНОСТЬ ВЕБ-ПРИЛОЖЕНИЙ</div>
				Пермский авиационный техникум им. А. Д. Швецова
			</a>
		</div>
	</div>
	<div class="space"> </div>
	<div class="main">
		<div class="content">
			<input type="button" class="button" value="Выйти" onclick="logout()" />

			<div class="name">Административная панель</div>

			Административная панель служит для создания, редактирования и удаления записей на сайте.
			<p><a href="logs.php">Журнал событий</a></p>


			<?php
			$Sql = "SELECT * FROM `session` WHERE `IdUser` = {$_SESSION["user"]} ORDER BY `DateStart` DESC";
			$Query = $mysqli->query($Sql);
			if ($Query->num_rows > 1) {
				$Read = $Query->fetch_assoc();
				$Read = $Query->fetch_assoc();

				$TimeEnd = strtotime($Read["DateNow"]);
				$TimeNow = time();

				$TimeDelta = round(($TimeNow - $TimeEnd) / 60);
				echo "<br>Последняя активная сессия была: {$TimeDelta} минут назад";
			}
			?>

			<div class="footer">
				© КГАПОУ "Авиатехникум", 2020
				<a href=#>Конфиденциальность</a>
				<a href=#>Условия</a>
			</div>
		</div>
	</div>

	<script>
		function logout() {
			$.ajax({
				url: 'ajax/logout.php',
				type: 'POST',
				data: null,
				cache: false,
				dataType: 'html',
				processData: false,
				contentType: false,
				success: function(_data) {
					location.reload();
				},
				error: function() {
					console.log('Системная ошибка!');
				}
			});
		}
	</script>
</body>

</html>