<?
session_start();
include("../settings/connect_datebase.php");

$IdUser = $_SESSION['user'];
$Message = $_POST["Message"];
$IdPost = $_POST["IdPost"];
$IdSession = $_SESSION["IdSession"];

$mysqli->query("INSERT INTO `comments`(`IdUser`, `IdPost`, `Messages`) VALUES ({$IdUser}, {$IdPost}, '{$Message}');");


$Sql = "SELECT `session`.*, `users`.`login` " .
	"FROM `session` `session` " .
	"JOIN `users` `users` ON `users`.`id` = `session`.`IdUser` " .
	"WHERE `session`.`Id` = {$IdSession}";

$Query = $mysqli->query($Sql);
$Read = $Query->fetch_array();

$TimeStart = strtotime($Read["DateStart"]);
$TimeNow = time();
$Ip = $Read["Ip"];
$TimeDelta = gmdate("H:i:s", ($TimeNow - $TimeStart));
$Date = date("Y-m-d H:i:s");
$login = $Read["login"];

$Sql = "INSERT INTO logs (Ip, IdUser, Date, TimeOnline, Event) VALUES ('{$Ip}',{$IdUser},'{$Date}','{$TimeDelta}','Пользователь {$login} оставил комментарий к записи [Id: {$IdPost}]: {$Message}')";
$mysqli->query($Sql);
