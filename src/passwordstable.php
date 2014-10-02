<?php
$page_name = 'passwordstable';
session_start();
if (!in_array($_SESSION['access'], ['teacher', 'coder'])) {
    header('Location: /menu');
    die();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include("header.php"); ?>
        <link rel='stylesheet' href='stylesheets/passwordstable-print.css' media="print">
    </head>
    <body>
        <div class="container">
            <script>
                getPasswords();
            </script>

            <?php include("navbar.php"); ?>

            <div class="jumbotron">
                <div id="innerjumbo">
                    <div class="well">
                        <a href='settings' class='btn btn-link btn-info' style='text-decoration: none; float: left; margin-bottom: 10px;'><i class='fa fa-angle-left' style='color: #428BCA'></i> Back</a>
                        <a href="javascript:window.print()" style='float: right; margin-right: 10px; margin-top: 5px;'><i class="fa fa-print fa-2x clickable"></i></a>
                        <table class='table table-condensed table-bordered table-striped'>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Password</th>
                                </tr>
                            </thead>
                            <tbody id='passwordsTableBody'>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>