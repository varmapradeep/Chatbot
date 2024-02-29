<?php

include('config.php');
include('header.php');

if (!(isset($_SESSION['user']) && $_SESSION['user']['group'] === "ADMIN")) {
    echo "<script>window.location.href='index.php';</script>";
    return;
}

?>

<head>
    <title>Chatbot | Manage Resources</title>
</head>

<body>
    <br><br><br>
    <h3 class="title-main">Manage Resources</h3>
    <br><br>
    <center>
        <iframe src="filemanager/dialog.php?type=2" style="height:500px;width:95%;" title="File Manager 1"></iframe>
    </center>
</body>

<?php

include('footer.php');

?>