<?php

include('config.php');
include('header.php');

if (!(isset($_SESSION['user']) && $_SESSION['user']['group'] === "ADMIN")) {
    echo "<script>window.location.href='index.php';</script>";
    return;
}

?>

<head>
    <title>Chatbot | Admin Home</title>
</head>

<?php

include('footer.php');

?>