<?php

include('config.php');
include('header.php');

if (!(isset($_SESSION['user']) && $_SESSION['user']['group'] === "ADMIN")) {
    echo "<script>window.location.href='index.php';</script>";
    return;
}

?>

<head>
    <title>Chatbot | List Users</title>

    <style>
        .table {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 99%;
        }

        .table th,
        td {
            padding-left: 12px;
            padding-right: 12px;
            text-align: center;
            border: 1px solid;
        }
    </style>
</head>

<body>
    <center>
        <br><br><br>
        <h3 class="title-main">Users</h3>
        <br><br>

        <?php

        if (isset($_REQUEST['active'])) {
            $filter = 'WHERE u.IsActive=True';
        } else if (isset($_REQUEST['inactive'])) {
            $filter = 'WHERE u.IsActive=False';
        } else {
            $filter = '';
        }
        
        ?>

        <div>
            <a class='btn btn-success' href='register.php?returnuri=<?= $pageuri ?>'>Add User</a>
            <a class='btn btn-info <?= $filter == '' ? 'disabled' : '' ?>' href='admin_list_users.php'>All Users</a>
            <a class='btn btn-info <?= isset($_REQUEST['active']) ? 'disabled' : '' ?>' href='admin_list_users.php?active'>Active Users</a>
            <a class='btn btn-info <?= isset($_REQUEST['inactive']) ? 'disabled' : '' ?>' href='admin_list_users.php?inactive'>Inactive Users</a>
        </div>
        <br>
        <table border="2px solid" class="table table-striped">
            <thead>
                <th>Id</th>
                <th>UserName</th>
                <th>Group</th>
                <th>Active</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Created On</th>
                <th>Last Updated On</th>
                <th>Last Updated By</th>
                <th>Edit</th>
            </thead>
            <?php

            $query = "SELECT u.*, up.name AS 'lastupdatedname' FROM `users` u LEFT JOIN `users` up ON u.lastupdatedby = up.id $filter ORDER BY u.id;";
            $res = mysqli_query($con, $query);
            while ($user = mysqli_fetch_object($res)) {
                $isactive = $user->isactive ? "Yes" : "<span style='color:red'><strong>No</strong></span>";
                $user->lastupdated = $user->lastupdated > 0 ? $user->lastupdated : "";

                echo "<tr>
                    <td>$user->id</td>
                    <td>$user->username</td>
                    <td>$user->group</td>
                    <td>$isactive</td>
                    <td>$user->name</td>
                    <td>$user->email</td>
                    <td>$user->phone</td>
                    <td>$user->created</td>
                    <td>$user->lastupdated</td>
                    <td>$user->lastupdatedname</td>
                    <td><a class='btn btn-info' href='admin_update_user.php?id=$user->id&returnuri=$pageuri'>Edit</a></td>";
            }
            ?>
        </table>
    </center>
</body>

<?php

include('footer.php');

?>