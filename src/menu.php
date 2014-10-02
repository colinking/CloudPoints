<?php
$page_name = 'menu';
session_start();
if ($_SESSION['access'] === 'student') {
    header('Location: /pointsheet');
    die();
} else if ($_SESSION['access'] === 'teacher') {
    header('Location: /settings');
    die();
} else if ($_SESSION['name'] === NULL) {
    header('Location: /');
    die();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include("header.php"); ?>
    </head>
    <body>
        <div class="container">

            <?php include("navbar.php"); ?>

            <div class="jumbotron">
                <div id="innerjumbo">
                    <div class="well">
                        <a class='btn btn-lg btn-success menuBtn redirect' href='pointsheet'><i class='fa fa-ticket fa-inverse'></i> Point Sheet</a>
                        <a class='btn btn-lg btn-warning menuBtn redirect' href='settings'><i class='fa fa-cog fa-inverse'></i>  Settings</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>