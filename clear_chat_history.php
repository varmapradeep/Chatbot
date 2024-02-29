<?php

include('config.php');

if (!(isset($_SESSION['user']))){
	header('HTTP/1.1 401 Authorization Required', true, 401);
	return;
}

$userid = $_SESSION['user']['id'];

$res = mysqli_query($con, "DELETE FROM `history` WHERE userid = $userid;");

if (!$res) {
	header('HTTP/1.1 500 Internal Server Error', true, 500);
	return;
}