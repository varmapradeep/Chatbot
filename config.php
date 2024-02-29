<?php

session_start();

$timezonename = isset($_SESSION['timezoneoffset']) ? timezone_name_from_abbr("", intval($_SESSION['timezoneoffset']), 0) : 'Asia/Kolkata';

date_default_timezone_set($timezonename);

$con = mysqli_connect('localhost', 'root', '', 'chatbot');

$res = mysqli_query($con, "SET time_zone='" . date('P') . "';");

$conf = [
    "Host" => "http://localhost/",
    "BotResultLimit" => 3,
    "Public_RegisterUserAsActive" => false,
    "Public_EnableCaptchaForUserRegistration" => false,
    "DefaultTheme" => 'dark',
];

if (!isset($_SESSION['conf'])) {
    $_SESSION['conf'] = $conf;
}
