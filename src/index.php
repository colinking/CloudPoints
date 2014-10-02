<?php
session_start();
$page_name = 'index';
if (isset($_SESSION['access'])) {
    header('Location: /menu');
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

            <?php include('navbar.php'); ?>

            <div class="jumbotron">
                <div id='innerjumbo'>
                    <h1>Math Point Sheets</h1>
                    <div id ='divPassword'>

                        <div id="indexContainer" class="invisible">
                            <div class="alert alert-danger" id="loginErrDiv" style="display: none; max-width: 250px; margin: 10px auto;">
                                <p id='loginErrLine' class="lead text-danger" style='font-size: 16px;'></p>
                            </div>
                            <select id="selectName" data-style="btn-info btn-lg" data-width='100%' class="selectpicker show-tick" title='Select your name' disabled>
                                <option id='defaultSelection' data-hidden="true">Select your name</option>
                            </select>
                            <div class="input-group input-group-lg has-info" id="inputPasswordDiv">
                                <input type='password' pattern="[0-9]*" class="form-control" id="inputPassword" placeholder='Password'>
                                <span class="input-group-btn">
                                    <button id='buttonPassword' class="btn btn-info" type="submit" onclick='login()' data-loading-text="Loading..." disabled>Enter</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(window).on('load', function() {
                //initializes the selectpicker
                var mobile = isMobile();
                if (mobile) {
                    $('.selectpicker').selectpicker('mobile');
                    
                } else {
                    $('#selectName').selectpicker();
                }
                getMembers('selectName');
                $("#inputPassword").keyup(function(event) {
                    if (event.keyCode === 13) {
                        $("#buttonPassword").click();
                    }
                });
                if ($.cookie('name') && !mobile)
                    $('input').focus();
                $('#selectName').change(function() {
                    //On selecting a name:
                    if (!mobile)
                        $('input').focus();
                });
                $('#indexContainer').removeClass('invisible');
            });
        </script>
    </body>
</html>