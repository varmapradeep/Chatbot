<?php

include('config.php');
include('header.php');

if (!(isset($_SESSION['user']))){
	echo "<script>window.location.href='index.php';</script>";
	return;
}

$returnuri = isset($_GET['returnuri']) ? urldecode($_GET['returnuri']) : 'home.php';
$showbackbutton = isset($_GET['origin']) && $_GET['origin'] == 'admin';

$id = $_GET['id'];

if ($id === 0) {
	$article = [];

	$article['title'] = "Sorry, I could not understand you";
	$article['content'] = "I am not able to find an answer to your query. I will note this down and update mself.";
} else {
	$res = mysqli_query($con, "SELECT a.*, u.name as editor FROM `articles` a LEFT JOIN `users` u ON a.lastupdatedby = u.id WHERE a.id ='$id'". ($_SESSION['user']['group'] === "ADMIN" ? "" : " AND a.isdeleted = false"));

	if ($res && mysqli_num_rows($res) > 0) {
		$article = mysqli_fetch_array($res);
	} else {
		$article = [];

		$article['title'] = "Sorry, the article no longer exists in this system";
		$article['content'] = "";
	}
}

?>

<head>
	<title>Chatbot | View Article</title>
</head>

<body>
	<br>
	&nbsp;&nbsp;<a class='btn btn-info' <?= $showbackbutton == '' ? 'style="display:none;"' : '' ?> href='<?= $returnuri ?>'>Back</a>
	<p style="font-size: 30px; margin-left: 40px; margin-top: 60px;"><?= $article['title'] ?></p>
	<p style="font-size: 12px; margin-left: 40px;"><?php if(isset($article['lastupdated'])) echo "Last updated by ". $article['editor'] ." on ". date('Y M d h:i:s A', strtotime($article['lastupdated'])) ?></p>
	<div style="size: 50px; margin-left: 30px; margin-top: 20px;"><?= $article['content'] ?></div>
	<br>
	&nbsp;&nbsp;<a class='btn btn-info' <?= $showbackbutton == '' ? 'style="display:none;"' : '' ?> href='<?= $returnuri ?>'>Back</a>
</body>

<?php

include('footer.php');

?>