<?php
session_start();
include("./settings/connect_datebase.php");
?>
<html>
<head>
    <meta charset="utf-8">
    <title>Подтверждение кода</title>
    <script src="https://code.jquery.com/jquery-1.8.3.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="top-menu">
    <a href="#"><img src="img/logo1.png"/></a>
    <div class="name">
        <a href="index.php">
            <div class="subname">БЕЗОПАСНОСТЬ ВЕБ-ПРИЛОЖЕНИЙ</div>
            Пермский авиационный техникум им. А. Д. Швецова
        </a>
    </div>
</div>
<div class="space"></div>
<div class="main">
    <div class="content">
        <div class="login">
            <div class="name">Подтверждение кода</div>

            <form id="verifyForm" onsubmit="return false;">
                <div class="sub-name">Введите код из письма:</div>
                <input name="_code" type="text" placeholder="Код из письма" required/>

                <input type="button" class="button" value="Подтвердить" onclick="VerifyCode()" style="margin-top: 10px; margin-bottom: 10px; width: 100%;"/>
                <img src="img/loading.gif" class="loading" style="margin-top: 0px; display: none;"/>
            </form>
        </div>

        <div class="footer">
            © КГАПОУ "Авиатехникум", 2020
            <a href="#">Конфиденциальность</a>
            <a href="#">Условия</a>
        </div>
    </div>
</div>

<script>
    var loading = document.getElementsByClassName("loading")[0];
    var button = document.getElementsByClassName("button")[0];

    function VerifyCode() {
        var _code = document.getElementsByName("_code")[0].value.trim();

        if (_code === "") {
            alert("Введите код.");
            return;
        }

        loading.style.display = "block";
        button.className = "button_diactive";

        var data = new FormData();
        data.append("code", _code);

        $.ajax({
            url: 'ajax/verify_code.php',
            type: 'POST',
            data: data,
            cache: false,
            dataType: 'html',
            processData: false,
            contentType: false,
            success: function (response) {
                try {
                    var data = JSON.parse(response);
                    if (data.status === "error") {
                        alert(data.message);
                    } else {
                        alert(data.message);
                        window.location.href = "user.php";
                    }
                } catch (e) {
                    console.error("Ошибка при обработке ответа сервера:", e);
                    alert("Произошла ошибка. Пожалуйста, попробуйте позже.");
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
</script>
</body>
</html>