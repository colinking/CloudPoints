<?php
session_start();
if ($_SESSION['access'] !== 'coder') {
    syslog(LOG_INFO, $_SESSION['name'] . ' tried to access "test" page');
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
        <p>Tester</p>



        <!-- logout -->
        <!--<button class='btn btn-default' id='logoutbutton'>Logout</button>-->
        <script>
            $(document).ready(function() {
//                $('#logoutbutton').click(function() {
//                    $.ajax({
//                        url: 'drive.php',
//                        dataType: 'text',
//                        type: 'GET',
//                        data: {
//                            type: 'logout'
//                        },
//                        success: function(resp) {
//                            console.log(resp);
//                        }
//                    });
//                });
////                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
////                    $('#selectEventType').selectpicker('mobile');
////                } else {
//                $('#selectName').selectpicker();
//                $('#selectEventType').selectpicker();
//                $('#defSelection').append('<option data-divider="true"></option>');
//                $('#defaultSelection').append('<option data-divider="true"></option>');
//                $('.datepicker').pickadate();
//                }
                $('#inputDate').pickadate({
                    format: 'dddd, mmm d, yyyy',
                    container: 'body',
                    onStart: function() {
                        var date = new Date();
                        this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()]);
                    }
                });
            });
        </script>
        
        
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        
        <button onclick="$('.modal').modal('show');">Open Modal</button>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <p>Test</p>
        <div class="modal fade" id="modalEditEvent" role="dialog" aria-labelledby="modalEditEventLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Edit Event</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="modalBodyCont">
                                        <p>Change the event type</p>
                                        <select id="selectNewEventType" data-style="btn-info" data-width='100%' class="selectpicker show-tick"></select>
                                        <p>Change the date</p>
                                        <input class="form-control datepicker" id='inputDate'>
                    <!--                    <p>Change the location</p>
                                        <input type='text' class="form-control" id='newlocation'>-->
                                        <p>Add or remove attendees</p>
                                        <table id="attendeeTable" class='table table-condensed table-striped table-bordered' style='margin-bottom: 0;'>
                                            <thead>
                                                <tr>
                                                    <td>Attendees</td>
                                                </tr>
                                            </thead>
                                            <tbody id="editAttendeesBody"></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div id='removeEventVerify' class="alert alert-danger" style="display: none; margin: 0px auto 10px auto;">
                                        <p class="lead text-danger">Are you sure?</p>
                                        <button class="btn btn-danger" id='buttonRemoveEvent' style="display: inline-block; margin-top: 5px; min-width: 83px; height: 34px;" data-loading-text="Removing..." onclick="removeEvent()">Remove</button>
                                        <button class="btn btn-default" onclick="$('#removeEventVerify').css('display', 'none');" style="margin-top: 5px; min-width: 83px; height: 35px;">Cancel</button>
                                    </div>
                                    <div id="divFooter">
                                        <button class="btn btn-danger" onclick="$('#removeEventVerify').css('display', 'block');
                                                $('#modalEditEvent').animate({scrollTop: $(document).height() - $(window).height()}, 1000);"><i class='fa fa-trash-o fa-inverse'></i> Remove Event</button>
                                        <button type="button" class="btn btn-default" id='buttonEditEvent' data-loading-text="<i class='fa fa-save' style='color: #555;'></i> Saving..."><i class='fa fa-save' style='color: #555;'></i> Save</button>
                                    </div>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
        
        
<!--        <button class='btn btn-warning' onclick='init()'>Initialize</button>

        <button class="btn btn-success" onclick="getEventTypes('selectEventType');">Get Event Types</button>
        <button class="btn btn-success" onclick="getMembers();">Get Members</button>
        <select id="selectEventType" data-style="btn-info"class="selectpicker show-tick">
            <option id='defSelection' selected>Select Event Type</option>
        </select>
        <select id="selectName" data-title="Select your name" data-style="btn-info btn-lg" data-live-search='true' class="selectpicker show-tick">
            <option id="defaultSelection" selected>Select your name</option>
        </select>

        <br>
        <p>Date Picker</p>
        <input class="datepicker form-control" style="max-width: 200px;">-->











<!--        <input id='datapicker'>
<script>
//            $('#datapicker').pickadate();
</script>
<br>
<table id="testerTable">
    <tfoot>
        <tr>
            <td>
                An item
            </td>
        </tr>
    </tfoot>
</table>
<script>
    $(document).ready(function() {
        $("#testerTable tr").click(function() {
            console.log('Clicked Tester!');
        });
        $("#memberTable tr").click(function() {
            console.log('Clicked Member!');
        });
    });
</script>

<br>

<table id="memberTable" class='table table-bordered table-condensed table-hover table-striped'>
    <thead>
        <tr>
            <td>
                Name
            </td>
            <td>
                Points
            </td>
            <td>
                Edit
            </td>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
        <tr id='addMemberRow'>
            <td id='tdAddMember' colspan="3">
                <p>Add <i class="fa fa-plus"></i></p>

            </td>
        </tr>
    </tfoot>
</table>-->

        <br>
    </body>
</html>