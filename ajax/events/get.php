<?
    require_once("../../settings/connect_datebase.php");

    $Sql = "SELECT * FROM `logs` WHERE 1=1"; 

    // данные фильтров из запроса
    $filterDate = isset($_POST['date']) ? $_POST['date'] : null;
    $filterIp = isset($_POST['ip']) ? $_POST['ip'] : null;
    $filterStatus = isset($_POST['status']) ? $_POST['status'] : null;
    $filterEvent = isset($_POST['event']) ? $_POST['event'] : null;

    // условия фильтрации в SQL-запрос
    if ($filterDate) {
        $Sql .= " AND Date LIKE '%" . $mysqli->real_escape_string($filterDate) . "%'";
    }

    if ($filterIp) {
        $Sql .= " AND Ip LIKE '%" . $mysqli->real_escape_string($filterIp) . "%'";
    }

    if ($filterStatus) {
        if ($filterStatus === 'Онлайн') {
            $Sql .= " AND EXISTS (SELECT 1 FROM `session` WHERE `IdUser` = `logs`.`IdUser` AND `DateNow` >= NOW() - INTERVAL 5 MINUTE)";
        } else if ($filterStatus === 'Был в сети') {
            $Sql .= " AND NOT EXISTS (SELECT 1 FROM `session` WHERE `IdUser` = `logs`.`IdUser` AND `DateNow` >= NOW() - INTERVAL 5 MINUTE)";
        }
    }

    if ($filterEvent) {
        $Sql .= " AND Event LIKE '%" . $mysqli->real_escape_string($filterEvent) . "%'";
    }

    $Sql .= " ORDER BY Date";

    $Query = $mysqli->query($Sql);
    $Events = array();


    while ($Read = $Query->fetch_assoc()) {
        $Status = "";

        $SqlSession = "SELECT * FROM `session` WHERE `IdUser` = {$Read["IdUser"]} ORDER BY `DateStart` DESC";
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
        $Event = array(
            "Id" => $Read["Id"],
            "Ip" => $Read["Ip"],
            "Date" => $Read["Date"],
            "TimeOnline" => $Read["TimeOnline"],
            "Status" => $Status,
            "Event" => $Read["Event"]
        );
        array_push($Events, $Event);
    }
    echo json_encode($Events, JSON_UNESCAPED_UNICODE);
