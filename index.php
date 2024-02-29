<?php

include("config.php");
include("header.php");

$error = '';
if (isset($_GET['logout'])) {

	$_SESSION = array();
	session_destroy();

	echo "<script>window.location.href='index.php';</script>";
} else if (isset($_POST['submit'])) {

	$username = mysqli_real_escape_string($con, $_POST['username']);
	$password = mysqli_real_escape_string($con, $_POST['password']);

	$res = mysqli_query($con, "SELECT `id`, `name`, `group`, `isactive` FROM `users` WHERE `username`='$username' AND `password`='$password'");

	if ($res && mysqli_num_rows($res) === 1) {

		$user = mysqli_fetch_array($res);

		if ($user['isactive']) {
			$_SESSION['user'] = $user;
			$_SESSION['timezoneoffset'] = $_POST['timezoneoffset'];

			echo "<script>window.location.href='index.php';</script>";
		} else {
			$error = "Account is inactive. Please contact administrator.";
		}
	} else {
		$error = "Login Failed. Invalid username or password.";
	}
} else if (isset($_SESSION['user'])) {
	if ($_SESSION['user']["group"] === "ADMIN") {
		echo "<script>window.location.href='admin_list_articles.php';</script>";
	} else {
		echo "<script>window.location.href='home.php';</script>";
	}
}

?>

<head>
	<title>Chatbot</title>

	<script>
		$(document).ready(function() {
			var userTime = new Date();
			var userTimeZoneOffset = -userTime.getTimezoneOffset() * 60;
			jQuery('#timezoneoffset').val(userTimeZoneOffset);
		});
	</script>
</head>

<body>
	<center>
		<section class="w3l-inputs-12">
			<div class="contact-top pt-5">
				<div class="container py-md-4 py-3">
					<div class="title-heading-w3 text-center mx-auto">
						<h3 class="title-main">Login</h3>
						<br><br>
						<p class="mt-4 sub-title"></p>
					</div>
					<div class="mt-lg-2" style="width: 70%;">
						<form method="POST" class="main-input">
							<div class="top-inputs d-grid">
								<input type="text" placeholder="User Name" name="username" required>
								<input type="password" placeholder="Password" name="password" required>
								<input type="hidden" id="timezoneoffset" name="timezoneoffset">
							</div>
							<div style="height:10px;">
								<span id='errors' style="color:red; font-size:medium"><?= $error ?></span>
							</div>
							<button type="submit" class="btn btn-success btn-style mt-4" name="submit">Login</button>
						</form>
					</div>
				</div>
			</div>
		</section>
	</center>
	<br><br><br>
</body>

</html>

<?php

include('footer.php');

?>