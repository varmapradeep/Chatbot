<?php

include("config.php");
include("header.php");

if (!(isset($_SESSION['user']))) {
	echo "<script>window.location.href='index.php';</script>";
	return;
}

$returnuri = isset($_GET['returnuri']) ? urldecode($_GET['returnuri']) : 'index.php';
$error = '';

if (isset($_POST['submit'])) {

	$password = mysqli_real_escape_string($con, $_POST['password']);
	$newpassword = mysqli_real_escape_string($con, $_POST['newpassword']);
	$confirmpassword = mysqli_real_escape_string($con, $_POST['confirmpassword']);

	if ($password !== $newpassword) {

		if ($newpassword === $confirmpassword) {

			$userid = isset($_SESSION['user']) ? $_SESSION['user']['id'] : 'NULL';

			$sql = "UPDATE `users` SET `password`='$newpassword', `lastupdatedby`='$userid' WHERE `id`='$userid' AND `password`='$password'";

			$res = mysqli_query($con, $sql);

			if ($res) {
				if (mysqli_affected_rows($con)) {
					echo "<script>alert('Password change successful.');</script>";
					echo "<script>window.location.href='$returnuri';</script>";
				} else {
					$error = "Current password is incorrect.";
				}
			} else {
				$error = "Password change failed. Please try again or contact administrator.";
			}
		} else {
			$error = "Passwords does not match.";
		}
	} else {
		$error = "Current passwords is the same as new password.";
	}
}

?>

<head>
	<title>Chatbot | Change Password</title>
</head>

<body>
	<center>
		<section class="w3l-inputs-12">
			<div class="contact-top pt-5">
				<div class="container py-md-4 py-3">
					<div class="title-heading-w3 text-center mx-auto">
						<h3 class="title-main">Change Password</h3>
						<br><br>
					</div>
					<div style="width: 50%;">
						<form method="post" class="main-input">
							<div class="d-grid">
								<input type="password" value="<?= $password ?? '' ?>" class="form-control" placeholder="Current Password" required name="password"> <br>
								<input type="password" value="<?= $newpassword ?? '' ?>" class="form-control" placeholder="New Password" required name="newpassword"> <br>
								<input type="password" value="<?= $confirmpassword ?? '' ?>" class="form-control" placeholder="Confirm Password" required name="confirmpassword"> <br>
							</div>
							<div style="height:25px; padding: 10px;">
								<span id='errors' style="color:red; font-size:medium"><?= $error ?></span>
							</div>
							<a class="btn btn-info btn-style mt-4" href='<?= $returnuri ?>'>Cancel</a>
							<button type="submit" class="btn btn-success btn-style mt-4" name="submit">Update</button>
						</form>
					</div>
				</div>
			</div>
		</section>
	</center>
</body>

<?php

include('footer.php');

?>