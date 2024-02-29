<?php

include('config.php');
include('header.php');

if (!(isset($_SESSION['user']) && $_SESSION['user']['group'] === "ADMIN")) {
    echo "<script>window.location.href='index.php';</script>";
    return;
}

$returnuri = isset($_GET['returnuri']) ? urldecode($_GET['returnuri']) : 'admin_list_articles.php';

if (isset($_POST["submit"])) {

    $fileName = $_FILES["file"]["tmp_name"];

    if ($_FILES["file"]["size"] > 0) {

        $file = fopen($fileName, "r");

        $importCount = 0;
        $errorCount = 0;

        while (($columns = fgetcsv($file, 10000, ",")) !== FALSE) {

            $keywords = mysqli_real_escape_string($con, trim($columns[0]));
            $title = mysqli_real_escape_string($con, trim($columns[1]));

            if (!(strtolower($keywords) == 'keywords' && strtolower($title) == 'title')) { //Skip title row if exists
                $content = "";
                if (isset($columns[2])) {
                    $content = mysqli_real_escape_string($con, trim($columns[2]));
                }

                //Check for already existing / duplicate records. Assumption : keywords + title will not duplicate
                $res = mysqli_query($con, "SELECT id FROM `articles` WHERE keywords='$keywords' AND title='$title'");

                if (!($res && mysqli_num_rows($res) > 0)) {

                    $userid = $_SESSION['user']['id'];

                    $res = mysqli_query($con, "INSERT INTO `articles` (`keywords`,`title`,`content`,`lastupdatedby`)
                    VALUES('$keywords','$title','$content','$userid')");

                    if ($res) $importCount++;
                    else $errorCount++;
                } else {
                    $errorCount++;
                }
            }
        }

        if ($errorCount > 0) {
            echo "<script>alert('$errorCount of " . ($errorCount + $importCount) . " articles failed to upload. Please check for duplicate keyword + title');</script>";
        } else {
            echo "<script>alert('$importCount article(s) imported.'); window.location.href='$returnuri';</script>";
        }
    }
}

?>

<head>
    <title>Chatbot | Import Articles</title>

    <style>
        .outer-scontainer {
            border: #e0dfdf 1px solid;
            padding: 20px;
            border-radius: 2px;
        }

        .table th,
        tr {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: center;
        }

        .table {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 99%;
        }
    </style>
</head>

<body>
    <center>
        <section class="w3l-inputs-12">
            <div class="contact-top pt-5">
                <div class="container py-md-4 py-3">
                    <div class="title-heading-w3 text-center mx-auto">
                        <h3 class="title-main">Import Articles</h3>
                        <br><br>
                    </div>
                    <div class="mt-lg-2 outer-scontainer" style="width: 70%;">
                        <form class="form-horizontal" action="" method="POST" name="frmCSVImport" id="frmCSVImport" enctype="multipart/form-data">
                            <div class="input-row">
                                <br>
                                <div class="input-group" style="margin-left:18%">
                                    <div>
                                        <a class='btn btn-info' href='<?= $returnuri ?>'>Cancel</a>&nbsp;&nbsp;
                                        <input type="file" name="file" title="Choose CSV file with the below format" id="file" accept=".csv">
                                        <button type="submit" class="btn btn-success" name="submit">Import</button>
                                    </div>
                                </div>
                                <br>
                                <label><strong>Example</strong></label> <br>
                                <table class="table table-striped" border="2">
                                    <thead>
                                        <th>Keywords</th>
                                        <th>Title</th>
                                        <th>Content</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Keyword1 Keyword2</td>
                                            <td>Example Title 1</td>
                                            <td>Example Content 1</td>
                                        </tr>
                                        <tr>
                                            <td>Keyword3 Keyword4</td>
                                            <td>Example Title 2</td>
                                            <td>Example Content 2</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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