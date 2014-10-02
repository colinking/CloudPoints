<!DOCTYPE html>
<?php
$page_name = 'pointsheet';
session_start();
if ($_SESSION['name'] === NULL) {
    header('Location: /');
//    die();
} else if ($_SESSION['access'] === 'teacher') {
    header('Location: /settings');
    die();
}
?>
<html>
    <head>
        <?php include("header.php"); ?>
    </head>
    <body>
        <div class="container">

            <?php include("navbar.php"); ?>

            <div class="jumbotron nopadding">
                <div id="innerjumbo">
                    <h1 style='color: darkslategrey; position: relative;'>Pointsheet <i id='reloaderPS' class='fa fa-refresh clickable' onclick='refreshPointSheet();'></i></h1>
                    <!--<div class="table-responsive">-->
                    <div class="alert alert-info" style="margin: 10px auto 10px auto">
                        <p style='display: inline; vertical-align: middle'>If the page does not load properly, just reload it. Thanks!</p>
                    </div>
                    <table id='pointsTable' class="table table-bordered table-striped table-condensed snaptable points">
                        <thead>
                        <td>
                            Date
                        </td>
                        <td>
                            Event
                        </td>
                        <td>
                            Points
                        </td>
                        </thead>
                        <tbody id="pointsBody" class="points">

                        </tbody>
                        <tfoot>
                        <td colspan="2">
                            Total:
                        </td>
                        <td class="points total">

                        </td>
                        </tfoot>
                    </table>
                    <!--</div>-->
                </div>
            </div>
        </div>
        <script>
            $(window).on('load', function() {
                listPoints();
            });
        </script>
    </body>
</html>