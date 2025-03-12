<?php
	session_start();
	include("./settings/connect_datebase.php");
	
	if (isset($_SESSION['user']) && isset($_SESSION['token'])) {
		$user_id = $_SESSION['user'];
		$token = $_SESSION['token'];
	
		$query_user = $mysqli->query("SELECT * FROM `users` WHERE `id` = " . $user_id);
		if ($user = $query_user->fetch_assoc()) {
			if ($user['session_token'] === $token) {
				if ($user['roll'] == 0) {
					header("Location: user.php");
					exit;
				} elseif ($user['roll'] == 1) {
					header("Location: admin.php");
					exit;
				}
			}
		}
	}
?>
<html>
	<head> 
		<meta charset="utf-8">
		<title> Авторизация </title>
		
		<script src="https://code.jquery.com/jquery-1.8.3.js"></script>
		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
		<div class="top-menu">
			<a href=#><img src = "img/logo1.png"/></a>
			<div class="name">
				<a href="index.php">
					<div class="subname">БЗОПАСНОСТЬ  ВЕБ-ПРИЛОЖЕНИЙ</div>
					Пермский авиационный техникум им. А. Д. Швецова
				</a>
			</div>
		</div>
		<div class="space"> </div>
		<div class="main">
			<div class="content">
				<div class = "login">
					<div class="name">Авторизация</div>
				
					<div class = "sub-name">Логин:</div>
					<input name="_login" type="text" placeholder="" onkeypress="return PressToEnter(event)"/>
					<div class = "sub-name">Пароль:</div>
					<input name="_password" type="password" placeholder="" onkeypress="return PressToEnter(event)" value="asdqweASD_123"/>
					
					<a href="regin.php">Регистрация</a>
					<br><a href="recovery.php">Забыли пароль?</a>
					<input type="button" class="button" value="Войти" onclick="LogIn()"/>
					<center><div class="g-recaptcha" data-sitekey="6LcEDfIqAAAAAIYJ5nMFgmhOVDbZ40hFpvn4wTSJ"></div></center>
					<img src = "img/loading.gif" class="loading"/>
				</div>
				
				<div class="footer">
					© КГАПОУ "Авиатехникум", 2020
					<a href=#>Конфиденциальность</a>
					<a href=#>Условия</a>
				</div>
			</div>
		</div>
		
		<script>
			
			function LogIn() {
				var captcha = grecaptcha.getResponse(); // Получаем ответ reCAPTCHA
				if (!captcha) {
					alert("Пройдите проверку reCAPTCHA.");
					return;
				}

				var loading = document.getElementsByClassName("loading")[0];
				var button = document.getElementsByClassName("button")[0];

				var _login = document.getElementsByName("_login")[0].value.trim();
				var _password = document.getElementsByName("_password")[0].value.trim();

				if (_login === "" || _password === "") {
					alert("Введите логин и пароль.");
					return;
				}

				loading.style.display = "block";
				button.className = "button_diactive";

				var data = new FormData();
				data.append("login", _login);
				data.append("password", _password);
				data.append("g-recaptcha-response", captcha); // Добавляем ответ reCAPTCHA

				$.ajax({
					url: 'ajax/login_user.php',
					type: 'POST',
					data: data,
					cache: false,
					dataType: 'json',
					processData: false,
					contentType: false,
					success: function (response) {
						if (response.status === "success") {
							alert(response.message);
							location.reload();
						} else if (response.status === "expired") {
							alert(response.message);
							window.location.href = response.redirect;
						} else {
							alert(response.message);
						}
					},
					error: function () {
						console.log('Системная ошибка!');
						alert("Не удалось выполнить запрос к серверу.");
					},
					complete: function () {
						loading.style.display = "none";
						button.className = "button";
					}
				});
			}

			
			function PressToEnter(e) {
				if (e.keyCode == 13) {
					var _login = document.getElementsByName("_login")[0].value;
					var _password = document.getElementsByName("_password")[0].value;
					
					if(_password != "") {
						if(_login != "") {
							LogIn();
						}
					}
				}
			}
			
		</script>
	</body>
</html>