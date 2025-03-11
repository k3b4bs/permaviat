<?php
	session_start();
	include("./settings/connect_datebase.php");

	if (isset($_SESSION['user'])) {
		if ($_SESSION['user'] != -1) {
			$user_query = $mysqli->query("SELECT * FROM `users` WHERE `id` = " . $_SESSION['user']);
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
		<style>
			.but{
				background-color: #1A73E8;
				color: #FFFFFF;
				text-decoration: none;
				padding: 5px 10px;
				border-radius: 5px;
				box-shadow: 0 2px 2px 0 rgba(0, 0, 0, .14), 0 3px 1px -2px rgba(0, 0, 0, .12), 0 1px 5px 0 rgba(0, 0, 0, .2);
				margin-top: 20px;
			}
		</style>
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
				<button class="button" onclick="exportToLog()">Экспортировать в log.txt</button>

				<div style="text-align: center;" class="name">Журнал</div>


				<div class="main">
					<div class="content">
						<table>
							<thead>
								<tr>
									<th>Дата и время</th>
									<th>IP пользователя</th>
									<th>Время в сети</th>
									<th>Статус</th>
									<th>Произошедшее событие</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>

				<div class="footer">
					© КГАПОУ "Авиатехникум", 2020
					<a href=#>Конфиденциальность</a>
					<a href=#>Условия</a>
				</div>
			</div>
		</div>

		<script>
			GetEvents();

			function GetEvents() {
				$.ajax({
					url: 'ajax/events/get.php',
					type: 'POST',
					data: null,
					cache: false,
					dataType: 'json',
					success: GetEventsAjax,
					error: function() {
						console.log('Системная ошибка!');
					}
				});
			}

			function logout() {
				$.ajax({
					url: 'ajax/logout.php',
					type: 'POST',
					data: null,
					cache: false,
					dataType: 'html',
					success: function(_data) {
						location.reload();
					},
					error: function() {
						console.log('Системная ошибка!');
					}
				});
			}

			function GetEventsAjax(data) { 
				console.log(data); 
				let $Table = $("table > tbody");
				$Table.empty(); // чистка таблицы
				data.forEach(Event => { 
					$Table.append(`
						<tr>
							<td>${Event["Date"]}</td>
							<td class="iptable">${Event["Ip"]}</td>
							<td>${Event["TimeOnline"]}</td>
							<td>${Event["Status"]}</td>
							<td class="left">${Event["Event"]}</td>
						</tr>
						`);
				});
			}

			function filters() {
				let filterDate = $('#filter_date').val();
				let filterIp = $('#filter_ip').val();
				let filterStatus = $('#filter_status').val();
				let filterEvent = $('#filter_event').val();

				$.ajax({
					url: 'ajax/events/get.php',
					type: 'POST',
					data: {
						date: filterDate,
						ip: filterIp,
						status: filterStatus,
						event: filterEvent
					},
					dataType: 'json', // на json для корректного парсинга ответа
					success: GetEventsAjax,
					error: function(jqXHR, textStatus, errorThrown) {
						console.error('Ошибка при фильтрации событий!', textStatus, errorThrown); //  подробная инфа об ошибке
					}
				});
			}

			// экспорт в log.txt
			function exportToLog() {
            let filterDate = $('#filter_date').val();
            let filterIp = $('#filter_ip').val();
            let filterStatus = $('#filter_status').val();
            let filterEvent = $('#filter_event').val();

            $.ajax({
                url: 'ajax/events/export.php', 
                type: 'POST',
                data: {
                    date: filterDate,
                    ip: filterIp,
                    status: filterStatus,
                    event: filterEvent
                },
                dataType: 'json', 
                success: function(response) {
                    if (response.success) {
                        alert('Данные успешно экспортированы в log.txt');
                    } else {
                        alert('Ошибка при экспорте данных: ' + response.message);
                        console.error('Ошибка при экспорте данных: ', response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Ошибка AJAX при экспорте!', textStatus, errorThrown);
                    alert('Произошла ошибка при экспорте данных. Пожалуйста, попробуйте позже.');
                }
            });
        }
		</script>
	</body>

	</html>