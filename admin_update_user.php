<?php

include('config.php');
include('header.php');

if (!(isset($_SESSION['user']) && $_SESSION['user']['group'] === "ADMIN")) {
    echo "<script>window.location.href='index.php';</script>";
    return;
}

$returnuri = isset($_GET['returnuri']) ? urldecode($_GET['returnuri']) : 'admin_list_users.php';
$userid = $_GET['id'];

if (isset($_POST['submit'])) {

    if ($_SESSION['user']['id'] === $userid) {
        $group = "ADMIN";
        $isactive = "True";
    } else {
        $group = isset($_POST['group']) ? mysqli_real_escape_string($con, $_POST['group']) : "ADMIN";
        $isactive = isset($_POST['isactive']) ? "True" : "False";
    }

    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);

    $password = mysqli_real_escape_string($con, $_POST['password']);
    $password_query = $password === '' ? '' :  "`password`='$password', ";
    $sessionuserid = $_SESSION['user']['id'];

    $qry = "UPDATE `users` SET $password_query`group`='$group', `name`='$name', `isactive`=$isactive, `lastupdatedby`='$sessionuserid' WHERE `id`='$userid'";
    $res = mysqli_query($con, $qry);

    if (!$res) {
        echo "<script>alert('An error occurred while updating the record. Please try again.');</script>";
    } else {
        echo "<script>window.location.href='$returnuri';</script>";
    }
}

$res =  mysqli_query($con, "SELECT * from `users` where `id`='$userid'");
$user = mysqli_fetch_object($res);

?>

<head>
    <title>Chatbot | Update User</title>
</head>

<body>
    <center>
        <section class="w3l-inputs-12">
            <div class="contact-top pt-5">
                <div class="container py-md-4 py-3">
                    <div class="title-heading-w3 text-center mx-auto">
                        <h3 class="title-main">Update User</h3>
                        <br><br>
                    </div>
                    <div style="width: 50%;">
                        <form method="post" class="main-input">
                            <div class="d-grid">
                                <label for="username" class="col-sm-2 col-form-label">Username</label>
                                <input type="text" name="username" class="form-control" disabled readonly title="Username" value="<?= $user->username; ?>"> <br>
                                <label for="password" class="col-sm-2 col-form-label">Password</label>
                                <input type="text" class="form-control" placeholder="Leave this field empty if not updating password" title="Password" name="password"> <br>
                                <label for="group" class="col-sm-2 col-form-label">Group</label>
                                <select <?= $_SESSION['user']['id'] === $userid ? 'disabled title="Users are not allowed to change own group"' : 'title="Group"'; ?> style="background: var(--bg-grey)" class="form-control" id="group" name="group">
                                    <option <?= ($group ?? $user->group) == 'ADMIN' ? 'selected' : ''; ?>>Admin</option>
                                    <option <?= ($group ?? $user->group) == 'USER' ? 'selected' : ''; ?>>User</option>
                                </select> <br>
                                <div class="form-check" style="width:1px;">
                                    <input <?= $_SESSION['user']['id'] === $userid ? 'disabled title="Users are not allowed to deactivate self"' : 'title="Active"'; ?> class="form-check-input" style="-webkit-appearance:checkbox" type="checkbox" <?= $isactive ?? $user->isactive ? 'checked' : ''; ?> name="isactive" id="isactive">
                                    <label <?= $_SESSION['user']['id'] === $userid ? 'disabled title="Users are not allowed to deactivate self"' : 'title="Active"'; ?> class="form-check-label" for="isactive">&nbsp;&nbsp;Active</label>
                                </div>

                                <label for="name" class="col-sm-2 col-form-label">Name</label>
                                <input placeholder="Name" class="form-control" type="text" title="Name" required name="name" pattern="^\S+.*" value="<?= $name ?? $user->name; ?>"> <br>
                                <label for="email" class="col-sm-2 col-form-label">Email</label>
                                <input placeholder="Email" class="form-control" type="email" title="Email" name="email" value="<?= $email ?? $user->email; ?>"> <br>
                                <label for="phone" class="col-sm-2 col-form-label">Phone</label>
                                <input placeholder="Phone" class="form-control" type="tel" name="phone" maxlength="14" pattern="^[0-9]{10,14}$" title="Phone number between 10 and 14 numbers" value="<?= $phone ?? $user->phone; ?>">
                            </div>

                            <a class='btn btn-info btn-style mt-4' href='<?= $returnuri ?>'>Cancel</a>
                            <button type="reset" class="btn btn-warning btn-style mt-4">Reset</button>
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