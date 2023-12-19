<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nöbet Sistemi</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>

<?php include 'modules/sidebar.php'; ?>

<div class="content">
    <?php
    if(isset($_GET['content'])) {
        $content = $_GET['content'];
        $contentPath = "modules/{$content}.php";

        if(file_exists($contentPath)) {
            include $contentPath;
        } else {
            echo 'İstediğiniz içerik bulunamadı.';
        }
    } else {
        include 'modules/define_shift.php';
    }
    ?>
</div>

</body>
</html>
<script>
    function showMessage(message) {
        $('#message').text(message);

        $('#message').slideDown();

        setTimeout(function () {
            $('#message').slideUp();
        }, 2000);
    }

    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');

    if (message) {
        showMessage(message)
    }
</script>