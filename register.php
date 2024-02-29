<?php

include("config.php");
include("header.php");

$isadmin = (isset($_SESSION['user']) && $_SESSION['user']['group'] === "ADMIN");
$showcaptcha = $conf['Public_EnableCaptchaForUserRegistration'] && !$isadmin;
$returnuri = isset($_GET['returnuri']) ? urldecode($_GET['returnuri']) : 'admin_list_users.php';
$error = '';

if (isset($_POST['submit'])) {

	$username = mysqli_real_escape_string($con, $_POST['username']);
	$password = mysqli_real_escape_string($con, $_POST['password']);
	$confirmpassword = mysqli_real_escape_string($con, $_POST['confirmpassword']);
	$name = mysqli_real_escape_string($con, $_POST['name']);
	$email = mysqli_real_escape_string($con, $_POST['email']);
	$phone = mysqli_real_escape_string($con, $_POST['phone']);

	if (!$showcaptcha || strcasecmp($_SESSION['captcha_code'], $_POST['captchacode']) === 0) {

		if ($password === $confirmpassword) {

			$res = mysqli_query($con, "SELECT `id` FROM `users` WHERE `username`='$username'");

			if ($res && mysqli_num_rows($res) === 0) {
				$activate = ($isadmin || $conf['Public_RegisterUserAsActive'] === true) ? "true" : "false";
				$lastupdatedby = isset($_SESSION['user']) ? $_SESSION['user']['id'] : 'NULL';

				$sql = "INSERT INTO `users` (`username`, `password`, `name`, `email`, `phone`, `isactive`, `lastupdatedby`)
			VALUES ('$username','$password', '$name', '$email', '$phone', $activate, $lastupdatedby)";

				$res = mysqli_query($con, $sql);

				if ($res) {
					echo "<script>alert('Registrations Completed" . ($activate === 'true' ? ' !!' : '. Please wait for Admin approval.') . "');</script>";
					echo "<script>window.location.href='$returnuri';</script>";
				} else {
					$error = "Registration Failed. Please contact Administrator.";
				}
			} else {
				$error = "A user with same username exists. Please choose a different one.";
			}
		} else {
			$error = "Passwords does not match.";
		}
	} else {
		$error = "Captcha code mismatch.";
	}
}

?>

<head>
	<title>Chatbot | User Registration</title>
</head>

<body>
	<center>
		<section class="w3l-inputs-12">
			<div class="contact-top pt-5">
				<div class="container py-md-4 py-3">
					<div class="title-heading-w3 text-center mx-auto">
						<h3 class="title-main">Register</h3>
						<br><br>
					</div>
					<div style="width: 50%;">
						<form method="post" class="main-input">
							<div class="d-grid">
								<input type="text" value="<?= $username ?? '' ?>" class="form-control" maxlength="15" placeholder="UserName" name="username" title="Letters, numbers and _ only" pattern="^[A-Za-z0-9_]{1,15}$" required> <br>
								<input type="password" value="<?= $password ?? '' ?>" class="form-control" placeholder="Password" required name="password"> <br>
								<input type="password" value="<?= $confirmpassword ?? '' ?>" class="form-control" placeholder="Confirm Password" required name="confirmpassword"> <br>

								<input placeholder="Name" value="<?= $name ?? '' ?>" class="form-control" type="text" required name="name" pattern="^\S+.*"> <br>
								<input placeholder="Email" value="<?= $email ?? '' ?>" class="form-control" type="email" name="email"> <br>
								<input placeholder="Phone" value="<?= $phone ?? '' ?>" class="form-control" type="tel" name="phone" maxlength="14" pattern="^[0-9]{10,14}$" title="Between 10 and 14 numbers only">

								<div <?= $showcaptcha ? "" : "style='display: none;'" ?>> <br>
									<img title="Captcha Image" width="205px" src="captcha.php?rand=<?php echo rand(); ?>" id='captchaimg'>
									<a href='javascript: refreshCaptcha();'><img title="Change Captcha Image" src="assets/images/refresh.png" width="40px;" alt="Change Captcha" /></a> <br><br>
									<input placeholder="Captcha" class="form-control" type="text" name="captchacode" maxlength="6" pattern="^\w{6}$" title="6 Letter Captcha Code">
								</div>
							</div>
							<div style="height:25px; padding: 10px;">
								<span id='errors' style="color:red; font-size:medium"><?= $error ?></span>
							</div>
							<a class="btn btn-info btn-style mt-4" href='<?= $returnuri ?>'>Cancel</a>
							<button type="submit" class="btn btn-success btn-style mt-4" name="submit">Register</button>
						</form>
					</div>
				</div>
			</div>
		</section>
	</center>

	<script>
		function refreshCaptcha() {
			var img = document.images['captchaimg'];
			img.src = img.src.substring(0, img.src.lastIndexOf("?")) + "?rand=" + Math.random() * 1000;
		}
	</script>

</body>

</html>

<?php

include('footer.php');

?>