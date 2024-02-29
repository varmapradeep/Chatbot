<?php

include('config.php');
include('header.php');

if (!(isset($_SESSION['user']) && $_SESSION['user']['group'] === "ADMIN")) {
    echo "<script>window.location.href='index.php';</script>";
    return;
}

$returnuri = isset($_GET['returnuri']) ? urldecode($_GET['returnuri']) : 'admin_list_articles.php';

if (isset($_POST['submit'])) {

    $keywords = mysqli_real_escape_string($con, $_POST['keywords']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $content = mysqli_real_escape_string($con, $_POST['content']);
    $userid = $_SESSION['user']['id'];

    $res = mysqli_query($con, "INSERT INTO `articles` (`keywords`,`title`,`content`,`lastupdatedby`) VALUES('$keywords','$title','$content','$userid')");

    if (!$res) {
        echo "<script>alert('An error occurred while adding the record. Please check for duplicate keyword + title');</script>";
    } else {
        echo "<script>window.location.href='$returnuri';</script>";
    }
}

?>

<head>
    <title>Chatbot | Create Article</title>
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
                        <h3 class="title-main">Create Article</h3>
                        <br><br>
                    </div>
                    <div class="mt-lg-2" style="width: 70%;">
                        <form method="POST" class="main-input">

                            <input type="text" value="<?= $keywords ?? '' ?>" required placeholder="Enter Article Keywords ..." name="keywords">
                            <br>
                            <br>
                            <input type="text" value="<?= $title ?? '' ?>" required placeholder="Title Goes Here ..." name="title">
                            <br>
                            <br>

                            <textarea placeholder="Content Goes Here ..." id="content" name="content"> <?= $content ?? '' ?> </textarea>
                            
                            <a class='btn btn-info btn-style mt-4' href='<?= $returnuri ?>'>Cancel</a>
                            <button type="submit" class="btn btn-success btn-style mt-4" name="submit">Create</button>
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