<?php

include('config.php');
include('header.php');

if (!(isset($_SESSION['user']) && $_SESSION['user']['group'] === "ADMIN")) {
    echo "<script>window.location.href='index.php';</script>";
    return;
}

$returnuri = isset($_GET['returnuri']) ? urldecode($_GET['returnuri']) : 'admin_list_articles.php';
$articleid = $_GET['id'];

if (isset($_POST['submit'])) {
    $keywords = mysqli_real_escape_string($con, $_POST['keywords']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $content = mysqli_real_escape_string($con, $_POST['content']);
    $userid = $_SESSION['user']['id'];

    $qry = "UPDATE `articles` SET `keywords`='$keywords', `title`='$title', `content`='$content', `lastupdatedby`='$userid' WHERE `id`='$articleid'";
    $res = mysqli_query($con, $qry);

    if (!$res) {
        echo "<script>alert('An error occurred while updating the record. Please check for duplicate keyword + title');</script>";
    } else {
        echo "<script>window.location.href='admin_list_articles.php';</script>";
    }
}

$res =  mysqli_query($con, "SELECT * from `articles` where `id`='$articleid'");
$article = mysqli_fetch_array($res);

?>

<head>
    <title>Chatbot | Update Article</title>
    <script src=".\assets\js\ckeditor.js"></script>
    <style>
        .ck-editor__editable.ck-content {
            min-height: 500px;
        }

        .ck.ck-editor__main>.ck-editor__editable {
            background-color: var(--bg-gray);
        }
    </style>
</head>

<body>
    <center>
        <section class="w3l-inputs-12">
            <div class="contact-top pt-5">
                <div class="container py-md-4 py-3">
                    <div class="title-heading-w3 text-center mx-auto">
                        <h3 class="title-main">Update Article</h3>
                        <br><br>
                    </div>
                    <div class="mt-lg-2" style="width: 70%;">
                        <form class="main-input" method="POST">
                            <label for="keywords"><b>Keywords</b></label>
                            <input type="text" name="keywords" required value="<?= $article['keywords'] ?>">
                            <br>
                            <br>

                            <label for="title"><b>Title</b></label>
                            <input type="text" name="title" required value="<?= $article['title'] ?>">

                            <br>
                            <br>

                            <label for="content"><b>Details</b></label>
                            <textarea id="content" name="content"> <?= $article['content'] ?> </textarea>
                            
                            <a class='btn btn-info btn-style mt-4' href='<?= $returnuri ?>'>Cancel</a>
                            <button type="submit" class="btn btn-success btn-style mt-4" name="submit">Update</button>
                        </form>
                    </div>
                    <br><br>
                    <iframe src="filemanager/dialog.php?type=2" style="height:500px;width:95%;" title="File Manager"></iframe>
                </div>
                <script>
                    ClassicEditor
                        .create(document.querySelector('#content'), {
                            toolbar: {
                                items: ['heading', '|', 'bold', 'italic', 'strikethrough', 'link', 'bulletedList', 'numberedList', '|', 'indent', 'outdent', '|', 'imageInsert', 'blockQuote', 'insertTable', 'mediaEmbed', 'undo', 'redo', '|', 'horizontalLine', 'fontBackgroundColor', 'fontColor', 'fontFamily', 'fontSize', 'specialCharacters']
                            },
                            language: 'en',
                            image: {
                                toolbar: ['imageTextAlternative', 'imageStyle:full', 'imageStyle:side']
                            },
                            table: {
                                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                            },
                        })
                        .then(editor => {
                            window.editor = editor;
                        })
                        .catch(error => {
                            console.error('Oops, something went wrong!');
                            console.error('Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:');
                            console.warn('Build id: b3x41bciwt5c-4fj8rnra4jlv');
                            console.error(error);
                        });
                </script>
            </div>
        </section>
    </center>
</body>

<?php

include('footer.php');

?>