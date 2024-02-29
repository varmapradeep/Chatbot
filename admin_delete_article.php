<?php

include('config.php');

if (!(isset($_SESSION['user']) && $_SESSION['user']['group'] === "ADMIN")) {
    echo "<script>window.location.href='index.php';</script>";
    return;
}

$returnuri = isset($_GET['returnuri']) ? urldecode($_GET['returnuri']) : 'admin_list_articles.php';
$id = $_GET['id'];
$delete = isset($_GET['restore']) ? 'false' : 'true';

mysqli_query($con, "UPDATE `articles` SET isdeleted = $delete WHERE id='$id'");

echo "<script>window.location.href='$returnuri';</script>";
