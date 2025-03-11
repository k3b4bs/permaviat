<?php
    require_once("../../settings/connect_datebase.php");

    $Sql = "SELECT * FROM logs ORDER BY `Date`";
    $Query = $mysqli->query($Sql);


    // открытие для записи (перезаписи)
    $logFile = fopen('log.txt', 'w'); 
    if (!$logFile) {
        error_log("Не удалось открыть файл log.txt для записи!");
        echo json_encode(array("success" => false, "message" => "Не удалось открыть файл log.txt"));
        exit;
    }

    // заголовки столбцов
    $logHeaders = "Дата,IP,Время в сети,Статус,Событие\n";
    if (fwrite($logFile, $logHeaders) === FALSE) {
        error_log("Не удалось записать заголовки в log.txt!");
        echo json_encode(array("success" => false, "message" => "Не удалось записать заголовки в log.txt"));
        exit;
    }


    while ($Read = $Query->fetch_assoc()) {
        $Status = "";
        $SqlSession = "SELECT * FROM session WHERE IdUser = {$Read["IdUser"]} ORDER BY DateStart DESC";
        $QuerySession = $mysqli->query($SqlSession);
        if ($QuerySession->num_rows > 0) {
            $ReadSession = $QuerySession->fetch_assoc();
            $TimeEnd = strtotime($ReadSession["DateNow"]) + 5 * 60;
            $TimeNow = time();
            if ($TimeEnd > $TimeNow) {
                $Status = "Онлайн";
            } else {
                $TimeEnd = strtotime($ReadSession["DateNow"]);
                $TimeDelta = round(($TimeNow - $TimeEnd) / 60);
                $Status = "Был в сети: {$TimeDelta} минут назад";
            }
        }

        // строка для записи
        $logEntry = sprintf(
            "%s,%s,%s,%s,%s\n",
            $Read["Date"],
            $Read["Ip"],
            $Read["TimeOnline"],
            $Status,
            $Read["Event"]
        );

        // запись
        if (fwrite($logFile, $logEntry) === FALSE) {
            error_log("Не удалось записать строку в log.txt!");
            echo json_encode(array("success" => false, "message" => "Не удалось записать строку в log.txt"));
            exit;
        }
    }

    // закрытие 
    fclose($logFile);

    // отпрвка успешного ответ
    echo json_encode(array("success" => true, "message" => "Данные успешно экспортированы в log.txt"));