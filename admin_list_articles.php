<?php

include('config.php');
include('header.php');

if (!(isset($_SESSION['user']) && $_SESSION['user']['group'] === "ADMIN")) {
    echo "<script>window.location.href='index.php';</script>";
    return;
}

?>

<head>
    <title>Chatbot | List Articles</title>

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

        .table td:nth-child(4) {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>
    <center>
        <br><br><br>
        <h3 class="title-main">Articles</h3>
        <br><br>

        <?php

        if (isset($_REQUEST['active'])) {
            $filter = 'WHERE a.IsDeleted=False';
        } else if (isset($_REQUEST['inactive'])) {
            $filter = 'WHERE a.IsDeleted=True';
        } else {
            $filter = '';
        }

        ?>

        <div>
            <a class='btn btn-success' href='admin_create_article.php?returnuri=<?= $pageuri ?>'>Create New Article</a>
            <a class='btn btn-info <?= $filter == '' ? 'disabled' : '' ?>' href='admin_list_articles.php'>All Articles</a>
            <a class='btn btn-info <?= isset($_REQUEST['active']) ? 'disabled' : '' ?>' href='admin_list_articles.php?active'>Active Articles</a>
            <a class='btn btn-info <?= isset($_REQUEST['inactive']) ? 'disabled' : '' ?>' href='admin_list_articles.php?inactive'>Inactive Articles</a>
            <a class='btn btn-success' href='admin_import_articles.php?returnuri=<?= $pageuri ?>'>Import Articles</a>
        </div>
        <br>
        <table border="2px solid" class="table table-striped">
            <colgroup>
                <col span="1" style="width:50px;">
                <col span="2" style="width:200px;">
                <col span="1">
                <col span="1" style="width:200px;">
                <col span="2" style="width:150px;">
            </colgroup>

            <thead>
                <th>Id</th>
                <th>Keywords</th>
                <th>Title</th>
                <th>Content</th>
                <th>Last Updated</th>
                <th>Edit</th>
                <th>Delete</th>
            </thead>

            <?php

            $res = mysqli_query($con, "SELECT a.*, u.name as editor FROM `articles` a LEFT JOIN `users` u ON a.lastupdatedby = u.id $filter ORDER BY id");
            while ($article = mysqli_fetch_array($res)) {
                $id = $article['id'];
                $isdeleted = $article['isdeleted'] == true;

                echo "<tr>
                    <td>$id</td>
                    <td>" . htmlspecialchars($article['keywords'], ENT_QUOTES) . "</td>
                    <td><a href='view_article.php?id=$id&origin=admin&returnuri=$pageuri'>" . htmlspecialchars($article['title'], ENT_QUOTES) . "</a></td>
                    <td>" . htmlspecialchars($article['content'], ENT_QUOTES) . "</td>
                    <td>$article[lastupdated] <br> by $article[editor]</td>
                    <td><a class='btn btn-info' href='admin_update_article.php?id=$id&returnuri=$pageuri'>Edit</a></td>
                    <td><a class='btn btn-warning' href='admin_delete_article.php?id=$id&returnuri=$pageuri" . ($isdeleted ? "&restore" : "") . "'> " . ($isdeleted ? "Restore" : "Delete") . "</a></td></tr>";
            }
            ?>
        </table>
    </center>
</body>

<?php

include('footer.php');

?>