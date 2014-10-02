<?php
$page_name = 'settings';
session_start();
if ($_SESSION['access'] === 'student') {
    header('Location: /pointsheet');
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

            <div class="jumbotron nopadding">
                <div id="innerjumbo">
                    <h1 style='color: darkslategrey; position: relative;'>Settings <i id='reloaderS' class='clickable fa fa-save' onclick='if ($(this).hasClass("fa-save"))
                                refreshSettings();'></i></h1>
                    <div class="alert alert-info" style="margin: 10px auto 10px auto">
                        <p style='display: inline; vertical-align: middle'>If the page does not load properly, just reload it. Thanks!</p>
                    </div>
                    <div id='saveWarning' class="alert alert-warning" style="margin: 10px auto 10px auto;">
                        <p style='display: inline; vertical-align: middle'>Make sure to save changes <em>above</em> before leaving.</p>
                        <!--                            <button style='display: inline-block; margin-left: 10px;' class="btn btn-success" data-loading-text="Saving..."  onclick="$(this).button('loading'); upload(this);">Save</button>-->
                    </div>
                    <!-- 
                    =============
                    Event Section 
                    =============
                    -->

                    <h3>Events</h3>
                    <!--<p class='text-muted'>Add and remove events.</p>-->

                    <div class='loading events' style='position: relative; width: 0px; height: 0; margin: auto auto;'>
                        <p class='lead' style='font-size: 10px !important; color: grey; position: absolute; top: -17px; left: -26px;'>LOADING...</p>
                    </div>

                    <!--<div class="table-responsive">-->
                    <table id="eventTable" class='table table-bordered table-condensed table-striped snaptable events'>
                        <thead>
                            <tr>
                                <td>
                                    Date
                                </td>
                                <td>
                                    Event
                                </td>
                                <td>
                                    # Present
                                </td>                   
                                <td>
                                    Edit
                                </td>
                            </tr>
                        </thead>
                        <tbody id="eventBody" style='display: none'>
                        </tbody>
                        <tfoot>
                            <tr id="eventShowRow" class="clickable addHover" onclick="toggleTable(true, 'event');">
                                <td colspan="4">
                                    <p class="rowButtonText"><i class='fa fa-angle-down'></i> Show <i class='fa fa-angle-down'></i></p>
                                </td>
                            </tr>
                            <tr id="eventHideRow" class="clickable addHover" onclick="toggleTable(false, 'event');" style="display: none;">
                                <td colspan="4">
                                    <p class="rowButtonText"><i class='fa fa-angle-up'></i> Hide <i class='fa fa-angle-up'></i></p>
                                </td>
                            </tr>
                            <tr id='addEventRow' class='clickable addHover addButton' onclick="getEventTypes('selectEventType');
                                    $('#modalAddEvent').modal('show');">
                                <td id='tdAddEvent' colspan="4">
                                    <p class="rowButtonText"><i class='fa fa-plus'></i></p>

                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <!--</div>-->

                    <!-- 
                    ===============
                    Add Event modal
                    ===============
                    -->

                    <div class="modal fade" id="modalAddEvent" tabindex="-1" role="dialog" aria-labelledby="modalAddEventLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel">Add Event</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-danger" id="addEventErrDiv" style="display: none; min-width: 250px; margin: 0 auto 10px auto;">
                                        <p id='addEventErrLine' class="lead text-danger" style='font-size: 16px;'><b>Error</b><br>Please select an event type.</p>
                                    </div>
                                    <p>Choose the event type</p>
                                    <div class='modalBodyCont'>
                                        <select id="selectEventType" data-style="btn-info" data-width='100%' class="selectpicker show-tick" title='Select the Event Type'>
                                            <option id='defSelection' data-hidden="true"></option>
                                        </select>
                                    </div>
                                    <p>Enter the date</p>
                                    <div class='modalBodyCont'>
                                        <input class="form-control datepicker" id='inputDate'>
                                    </div>
                    <!--                <p>Set the location (defaults to THS)</p>
                    
                                    <div class='modalBodyCont'>
                                        <input type='text' class="form-control" id="inputLocation" placeholder="THS">
                                    </div>-->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" id='buttonAddEvent' data-loading-text="<i class='fa fa-check' style='color: #555;'></i> Adding..."><i class='fa fa-check' style='color: #555;'></i> Add Event</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                    <!-- End Add Event modal -->

                    <!-- 
                    ================
                    Edit Event modal
                    ================
                    -->

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
                                        <input class="form-control datepicker" id='newdate'>
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

                    <!-- End Edit Event modal -->

                    <!-- 
                    ==================
                    Event Type Section 
                    ==================
                    -->

                    <h3>Event Types</h3>
                    <!--<p class='text-muted'>Create different meeting types</p>-->

                    <div class='loading eventType' style='position: relative; width: 0px; height: 0; margin: auto auto;'>
                        <p class='lead' style='font-size: 10px !important; color: grey; position: absolute; top: -17px; left: -26px;'>LOADING...</p>
                    </div>

                    <!--<div class="table-responsive">-->
                    <table id="eventTypeTable" class='table table-bordered table-condensed table-striped snaptable eventType'>
                        <thead>
                            <tr>
                                <td>
                                    Type
                                </td>
                                <td>
                                    Point Value
                                </td>
                                <td>
                                    Edit
                                </td>
                            </tr>
                        </thead>
                        <tbody id="eventTypeBody" style='display: none'>
                        </tbody>
                        <tfoot>
                            <tr id="eventTypeShowRow" class="clickable addHover" onclick="toggleTable(true, 'eventType')">
                                <td colspan="4">
                                    <p class="rowButtonText"><i class='fa fa-angle-down'></i> Show <i class='fa fa-angle-down'></i></p>
                                </td>
                            </tr>
                            <tr id="eventTypeHideRow" class="clickable addHover" onclick="toggleTable(false, 'eventType');" style="display: none;">
                                <td colspan="4">
                                    <p class="rowButtonText"><i class='fa fa-angle-up'></i> Hide <i class='fa fa-angle-up'></i></p>
                                </td>
                            </tr>
                            <tr id='addEventTypeRow' class='clickable addHover addButton' onclick="$('#modalAddEventType').modal('show');">
                                <td id='tdAddEventType' colspan="3">
                                    <p class="rowButtonText"><i class='fa fa-plus'></i></p>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <!--</div>-->

                    <!-- 
                    ====================
                    Add Event Type modal
                    ====================
                    -->

                    <div class="modal fade" id="modalAddEventType" tabindex="-1" role="dialog" aria-labelledby="modalAddEventTypeLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Add Event Type</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-danger" id="addEventTypeErrDiv" style="display: none; min-width: 250px; margin: 0 auto 10px auto;">
                                        <p id='addEventTypeErrLine' class="lead text-danger" style='font-size: 16px;'></p>
                                    </div>
                                    <div class='modalBodyCont'>
                                        <!--<p>Enter name of event type.</p>-->
                                        <input type='text' class="form-control" id='eventtypename' placeholder='Name of Event Type'>
                                        <!--                    <div class="checkbox">
                                                                <label>
                                                                    <input id='checkboxscores' type="checkbox" value="">
                                                                    Has scores?
                                                                </label>
                                                            </div>-->
                                                            <!--<p>Enter the number of points awarded.</p>-->
                                        <input type='text' pattern="[0-9]*" class="form-control" id='eventtypepointvalue' placeholder='Points Awarded'>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div id="divFooter">
                                        <button type="button" class="btn btn-default" id='buttonAddEventType' data-loading-text="<i class='fa fa-check' style='color: #555;'></i> Adding..."><i class='fa fa-check' style='color: #555;'></i> Add Event Type</button>
                                    </div>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                    <!-- End Add Event Type modal -->

                    <!-- 
                    =====================
                    Edit Event Type modal
                    =====================
                    -->

                    <div class="modal fade" id="modalEditEventType" tabindex="-1" role="dialog" aria-labelledby="modalEditEventTypeLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Edit Event Type</h4>
                                </div>
                                <div class="modal-body">
                                    <div class='modalBodyCont'>
                                        <p>Change the event type name</p>
                                        <input type='text' class="form-control" id='editeventtypename'>
                                        <!--                    <div class="checkbox">
                                                                <label>
                                                                    <input id='checkboxeditscores' type="checkbox" value="">
                                                                    Has scores?
                                                                </label>
                                                            </div>-->
                                        <p>Change the point award</p>
                                        <input type='text' pattern="[0-9]*" class="form-control" id='editeventtypepointvalue'>
                                    </div>
                                </div>
                                <div class="modal-footer" style='padding: 20px 10px;'>
                                    <div id='removeEventTypeVerify' class="alert alert-danger" style="display: none; margin: 0px auto 10px auto;">
                                        <p class="lead text-danger">Are you sure?</p>
                                        <button class="btn btn-danger" id='buttonRemoveEventType' style="display: inline-block; margin-top: 5px; min-width: 83px; height: 34px;" data-loading-text="Removing..." onclick="removeEventType()">Remove</button>
                                        <button class="btn btn-default" onclick="$('#removeEventTypeVerify').css('display', 'none');" style="margin-top: 5px; min-width: 83px; height: 35px;">Cancel</button>
                                    </div>
                                    <div id="divFooter">
                                        <button class="btn btn-danger edit eventType remove" onclick="$('#removeEventTypeVerify').css('display', 'block');
                                                $('#modalEditEventType').animate({scrollTop: $(document).height() - $(window).height()}, 1000);"><i class='fa fa-trash-o fa-inverse'></i> Remove Event Type</button>
                                        <button type="button" class="btn btn-default" id='buttonEditEventType' data-loading-text="<i class='fa fa-save' style='color: #555;'></i> Saving..."><i class='fa fa-save' style='color: #555;'></i> Save</button>
                                    </div>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                    <!-- End Edit Event Type modal -->


                    <!-- 
                    ===============
                    Members Section
                    ===============
                    -->

                    <h3>Members</h3>
                    <!--<p class='text-muted'>Add and remove members.</p>-->

                    <div class='loading member' style='position: relative; width: 0px; height: 0; margin: auto auto;'>
                        <p class='lead' style='font-size: 10px !important; color: grey; position: absolute; top: -17px; left: -26px;'>LOADING...</p>
                    </div>

                    <!--<div class="table-responsive">-->
                    <table id="memberTable" class='table table-bordered table-condensed table-striped snaptable member main'>
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
                        <tbody id="memberBody" style='display: none'>

                        </tbody>
                        <tfoot>
                            <tr id="memberShowRow" class="clickable addHover" onclick="toggleTable(true, 'member');">
                                <td colspan="4">
                                    <p class="rowButtonText"><i class='fa fa-angle-down'></i> Show <i class='fa fa-angle-down'></i></p>
                                </td>
                            </tr>
                            <tr id="memberHideRow" class="clickable addHover" onclick="toggleTable(false, 'member');" style="display: none;">
                                <td colspan="4">
                                    <p class="rowButtonText"><i class='fa fa-angle-up'></i> Hide <i class='fa fa-angle-up'></i></p>
                                </td>
                            </tr>
                            <tr id='addMemberRow' class='clickable addHover addButton' onclick="$('#modalAddMember').modal('show');">
                                <td id='tdAddMember' colspan="3">
                                    <p class="rowButtonText"><i class='fa fa-plus'></i></p>

                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <!--</div>-->

                    <!-- 
                    ================
                    Add Member modal 
                    ================
                    -->

                    <div class="modal fade" id="modalAddMember" tabindex="-1" role="dialog" aria-labelledby="modalAddMemberLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Add Members</h4>
                                </div>
                                <div class="modal-body">
                                    <p class=''>Enter one or more names separated by commas.</p>
                                    <!--<div class='modalBodyCont'>-->
                                    <input type='text' class="form-control" id='namestoadd' placeholder='Names'>
                                    <div class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-primary yearlabel">
                                            <input type="radio" name="options" id="freshmen"> Freshmen
                                        </label>
                                        <label class="btn btn-primary yearlabel">
                                            <input type="radio" name="options" id="sophomore"> Sophomore
                                        </label>
                                        <label class="btn btn-primary yearlabel">
                                            <input type="radio" name="options" id="junior"> Junior
                                        </label>
                                        <label class="btn btn-primary yearlabel">
                                            <input type="radio" name="options" id="senior"> Senior
                                        </label>
                                    </div>
                                    <div id="tableSizeLimiter">
                                        <table id="memberTableAddMember" class='table table-condensed table-striped table-bordered member add'>
                                            <thead>
                                            <th>
                                                Current Members
                                            </th>
                                            </thead>
                                            <tbody id="addMemberBody" style="display: none;">

                                            </tbody>
                                            <tfoot>
                                                <tr id="addMemberShowRow" class="clickable addHover" onclick="toggleTable(true, 'addMember');">
                                                    <td colspan="1">
                                                        <p class="rowButtonText"><i class='fa fa-angle-down'></i> Show <i class='fa fa-angle-down'></i></p>
                                                    </td>
                                                </tr>
                                                <tr id="addMemberHideRow" class="clickable addHover" onclick="toggleTable(false, 'addMember');" style="display: none;">
                                                    <td colspan="1">
                                                        <p class="rowButtonText"><i class='fa fa-angle-up'></i> Hide <i class='fa fa-angle-up'></i></p>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!--</div>-->
                                </div>
                                <div class="modal-footer">
                                    <div id="divFooter">
                                        <button type='button' class="btn btn-default" id='buttonAddMember' data-loading-text="<i class='fa fa-check' style='color: #555;'></i> Adding..."><i class='fa fa-check' style='color: #555;'></i> Add Members</button>
                                    </div>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                    <!-- End Add Member modal -->

                    <!--
                    =================
                    Edit Member modal
                    =================
                    -->

                    <div class="modal fade" id="modalEditMember" tabindex="-1" role="dialog" aria-labelledby="modalEditMemberLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Edit Member</h4>
                                </div>
                                <div class="modal-body">
                                    <p>Change the spelling of the name</p>
                                    <div class='modalBodyCont'>
                                        <input type='text' class="form-control" id='inputChangedName'>
                                        <div class="btn-group" data-toggle="buttons">
                                            <label class="btn btn-primary yearlabel freshmen">
                                                <input type="radio" name="options" id="freshmen"> Freshmen
                                            </label>
                                            <label class="btn btn-primary yearlabel sophomore">
                                                <input type="radio" name="options" id="sophomore"> Sophomore
                                            </label>
                                            <label class="btn btn-primary yearlabel junior">
                                                <input type="radio" name="options" id="junior"> Junior
                                            </label>
                                            <label class="btn btn-primary yearlabel senior">
                                                <input type="radio" name="options" id="senior"> Senior
                                            </label>
                                        </div>
                                    </div>
                                    <div class="modalBodyCont">
                                        <div class="btn-group" data-toggle="buttons">
                                            <label id='labelstudent' class="btn btn-primary accesslabel student">
                                                <input type="radio" name="options" id="student"> Student
                                            </label>
                                            <label id='labelofficer' class="btn btn-primary accesslabel officer coder">
                                                <input type="radio" name="options" id="officer"> Officer
                                            </label>
                                            <label id='labelteacher' class="btn btn-primary accesslabel disabled teacher">
                                                <input type="radio" name="options" id="teacher"> Teacher
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div id='removeMemberVerify' class="alert alert-danger" style="display: none; margin: 0px auto 10px auto;">
                                        <p class="lead text-danger">Are you sure?</p>
                                        <button class="btn btn-danger" id='buttonRemoveMember' style="display: inline-block; margin-top: 5px; min-width: 83px; height: 34px;" data-loading-text="Removing..." onclick="removeMember()">Remove</button>
                                        <button class="btn btn-default" onclick="$('#removeMemberVerify').css('display', 'none');" style="margin-top: 5px; min-width: 83px; height: 35px;">Cancel</button>
                                    </div>
                                    <div id="divFooter">
                                        <button class="btn btn-danger" onclick="$('#removeMemberVerify').css('display', 'block');
                                                $('#modalEditMember').animate({scrollTop: $(document).height() - $(window).height()}, 1000);"><i class='fa fa-trash-o fa-inverse'></i> Remove Member</button>
                                        <button type="button" class="btn btn-default" id='buttonEditMember' data-loading-text="<i class='fa fa-save' style='color: #555;'></i> Saving..."><i class='fa fa-save' style='color: #555;'></i> Save</button>
                                    </div>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                    <!-- End Edit Member modal -->

                    <!-- Tutoring Section -->

                    <h2 class='tutoring'>Tutoring Hours</h2>
                    <div class="input-group input-group-lg tutoring" style='margin: auto auto 20px auto;'>
                        <span class="input-group-btn">
                            <button class="btn btn-default btn-grey left" onclick='changeDay(-1);'><i class='fa fa-angle-left fa-default'></i></button>
                        </span>
                        <input class="form-control datepicker tutoring">
                        <span class="input-group-btn">
                            <button class="btn btn-default btn-grey right" onclick='changeDay(1);'><i class='fa fa-angle-right fa-default'></i></button>
                        </span>
                    </div>

                    <div class='loading tutoring' style='position: relative; width: 0px; height: 0; margin: auto auto;'>
                        <p class='lead' style='font-size: 10px !important; color: grey; position: absolute; top: -15px; left: -23px;'>LOADING...</p>
                    </div>

                    <table id='tutoringTable' class='table table-bordered table-condensed table-striped snaptable tutoring'>
                        <thead>
                            <tr>
                                <td>
                                    Name
                                </td>
                            </tr>
                        </thead>
                        <tbody id="tutoringBody" class='body tutoring' style='display: none;'>

                        </tbody>
                        <tfoot>
                            <tr id="tutoringShowRow" class="clickable addHover tutoring showRow" onclick="toggleTable(true, 'tutoring');">
                                <td colspan="1">
                                    <p class="rowButtonText"><i class='fa fa-angle-down'></i> Show <i class='fa fa-angle-down'></i></p>
                                </td>
                            </tr>
                            <tr id="tutoringHideRow" class="clickable addHover tutoring hideRow" onclick="toggleTable(false, 'tutoring');" style="display: none;">
                                <td colspan="1">
                                    <p class="rowButtonText"><i class='fa fa-angle-up'></i> Hide <i class='fa fa-angle-up'></i></p>
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <!--<button class='btn btn-warning' style='margin-top: 10px;' onclick='debugSession();'>Print Session</button>-->
                    <!--<button class='btn btn-warning' style='margin-top: 10px;' onclick='clearSession();'>Clear Session</button>-->


                    <div <?php if (!in_array($_SESSION['access'], ['teacher', 'coder'])) echo 'style="display: none;"' ?> id='divTeacherOnly'>
                        <h2>Teacher only</h2>
                        <div id='resetVerify' class="alert alert-danger" style="display: none; margin: 10px auto 10px auto;">
                            <p class="lead text-danger">Are you sure?</p>
                            <button class="btn btn-danger" id='buttonReset' style="display: inline-block; margin-top: 5px; min-width: 83px; height: 34px;" data-loading-text="Resetting..." onclick="$(this).button('loading');">Reset (Disabled)</button>
                            <button class="btn btn-default" onclick="$('#resetVerify').css('display', 'none');" style="margin-top: 5px; min-width: 83px; height: 35px;">Cancel</button>
                        </div>
                        <a href='passwordstable' class='btn btn-primary btnmargin'>View Passwords</a>
                        <button class='btn btn-danger btnmargin' onclick="
                                $('.alert').css('display', 'none');
                                $('#resetVerify').css('display', 'block');
                                $('html, body').animate({scrollTop: $(document).height() - $(window).height()}, 0);">Reset</button>
                        <a href="#" onclick="" class='btn btn-info' disabled>Download Data (Todo)</a>
                        <p class='text-muted' style='margin: 5px auto'>Required points</p>
                        <div id='reqPointsErrDiv' class='alert alert-danger' style="display: none; margin: 10px auto 10px auto;">
                            <p class="lead text-danger"><b>Error</b><br>Please enter a point value.</p>
                        </div>
                        <div class="input-group has-info" style='max-width: 208px; margin: 3px auto;'>
                            <input id='inputReqPoints' type='text' pattern="[0-9]*" class="form-control" placeholder='Currently <?php echo $_SESSION['data']['reqpoints']; ?> points'>
                            <span class="input-group-btn">
                                <button id='setReqPointsButton' class="btn btn-info" type="submit" onclick='$(this).button("loading");
                                        setReqPoints();' data-loading-text="Setting...">Set</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Script -->
        <script>
            var mobile = isMobile();
            var clicked = false;
            loadSettings();

            window.onload = function() {
                //forces to the top of the page on reload
                setTimeout(function() {
                    scrollTo(0, -1);
                }, 0);
            }
            $(document).ready(function() {
//                $(this).scrollTop(0);
                //creates placeholders for IE < 9
                $('input').placeholder();
                if (mobile) {
                    $('#selectEventType').selectpicker('mobile');
                } else {
                    $('#selectEventType').selectpicker();
                }
                $('#inputDate').pickadate({
                    format: 'dddd, mmm d, yyyy',
                    container: 'body',
                    onStart: function() {
                        var date = new Date();
                        this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()]);
                    }
                });
                $('.datepicker.tutoring').pickadate({
                    format: ($(window).width()<365 ? 'ddd, mmm d, yyyy':'dddd, mmm d, yyyy'),
//                    container: 'body',
                    onStart: function() {
                        var date = new Date();
                        //muted prevents this from triggering the onset callback
                        this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()], {muted: true});
                        listTutors(date.getFullYear(), date.getMonth() + 1, date.getDate());
                    },
                    onSet: function(setTo) {
                        //switching months triggers 'highlight' sets, we don't want these
                        if ('select' in setTo) {
                            var date = new Date(setTo['select']);
                            listTutors(date.getFullYear(), date.getMonth() + 1, date.getDate());
                        }
                    }
                });
            });
            $('#modalAddEvent').on('show.bs.modal', function() {
                $('#addEventErrDiv').css('display', 'none');
            });
            $('#modalAddEvent').on('shown.bs.modal', function() {
                $(document)
                        .keyup(function(event) {
                            if (event.keyCode === 13) {
                                $("#buttonAddEvent").click();
                            }
                        });
                $('#buttonAddEvent').click(function() {
                    if (!clicked) {
                        clicked = addEvent();
                    }
                });
            });
            $('#modalEditEvent').on('show.bs.modal', function() {
                $('#removeEventVerify').css('display', 'none');
                if (mobile) {
                    $('#selectNewEventType').selectpicker('mobile');
                } else {
                    $('#selectNewEventType').selectpicker();
                }
                getEventTypes('selectNewEventType');
            });
            $('#modalEditEvent').on('shown.bs.modal', function() {
                $(document)
                        .keyup(function(event) {
                            if (event.keyCode === 13) {
                                $("#buttonEditEvent").click();
                            }
                        });
                $('#buttonEditEvent').click(function() {
                    if (!clicked) {
                        editEvent();
                        clicked = true;
                    }
                });
            });
            $('#modalAddEventType').on('show.bs.modal', function() {
                $('#addEventTypeErrDiv').css('display', 'none');
            });
            $('#modalAddEventType').on('shown.bs.modal', function() {
                $('#eventtypename').focus();
                $(document)
                        .keyup(function(event) {
                            if (event.keyCode === 13) {
                                $("#buttonAddEventType").click();
                            }
                        });
                $('#buttonAddEventType').click(function() {
                    if (!clicked) {
                        clicked = addEventType();
                    }
                });
            });
            $('#modalEditEventType').on('show.bs.modal', function() {
                $('#removeEventTypeVerify').css('display', 'none');
                //check if this event type is removable
                if (modalData['eventname'] === 'Tutoring') {
                    $('button.remove.edit.eventType').html('<i class="fa fa-trash-o fa-inverse"></i> Non-removable').attr('disabled', true);
                }
            });
            $('#modalEditEventType').on('shown.bs.modal', function() {
                $(document)
                        .keyup(function(event) {
                            if (event.keyCode === 13) {
                                $("#buttonEditEventType").click();
                            }
                        });
                $('#buttonEditEventType').click(function() {
                    if (!clicked) {
                        editEventType();
                        clicked = true;
                    }
                });
            });
            $('#modalAddMember').on('shown.bs.modal', function() {
                if (!mobile)
                    $('#namestoadd').focus();
                $(document)
                        .keyup(function(event) {
                            if (event.keyCode === 13) {
                                $("#buttonAddMember").click();
                            }
                        });
                $('#buttonAddMember').click(function() {
                    if (!clicked) {
                        addMember();
                        clicked = true;
                    }
                });
            });
            $('#modalEditMember').on('show.bs.modal', function() {
                $('#removeMemberVerify').css('display', 'none');
            });
            $('#modalEditMember').on('shown.bs.modal', function() {
                $(document)
                        .keyup(function(event) {
                            if (event.keyCode === 13) {
                                $("#buttonEditMember").click();
                            }
                        });
                $('#buttonEditMember').click(function() {
                    if (!clicked) {
                        editMember();
                        clicked = true;
                    }
                });
            });

            $('.modal').on('hide.bs.modal', function() {
                clicked = false;
                $(document).unbind('keyup');
            });
            $('#inputReqPoints').keyup(function(event) {
                if (event.keyCode === 13 && !$('#setReqPointsButton').is(':disabled')) {
                    $('#setReqPointsButton').button("loading");
                    setReqPoints();
                }
            });
            // Code from: http://www.abeautifulsite.net/blog/2013/11/bootstrap-3-modals-and-the-ios-virtual-keyboard/
            if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {

                $('.modal').on('show.bs.modal', function() {

                    // Position modal absolute and bump it down to the scrollPosition
                    $(this)
                            .css({
                                position: 'absolute',
                                marginTop: $(window).scrollTop() + 'px',
                                bottom: 'auto'
                            });

                    // Position backdrop absolute and make it span the entire page
                    //
                    // Also dirty, but we need to tap into the backdrop after Boostrap 
                    // positions it but before transitions finish.
                    //
                    setTimeout(function() {
                        $('.modal-backdrop').css({
                            position: 'absolute',
                            top: 0,
                            left: 0,
                            width: '100%',
                            height: Math.max(
                                    document.body.scrollHeight, document.documentElement.scrollHeight,
                                    document.body.offsetHeight, document.documentElement.offsetHeight,
                                    document.body.clientHeight, document.documentElement.clientHeight
                                    ) + 'px'
                        });
                    }, 0);

                });
            }
//            $(function() {
            $(document).on('touchend', function(event) {
//
//            if($(e.target).parents('.modal').length()) {
//                        alert('Modal!');
//                    }
//                    else 
                if (!$(event.target).closest('.modal').length && !$(event.target).closest('.picker__wrap').length) {
                    $('.modal').modal('hide');
                }
            });
//            $('.navbar-toggle').on('touchstart', function() {
//                $(this).addClass('toggleHover');
//            });
//            $('.navbar-toggle').on('touchend', function() {
//                setTimeout(function() {$('.navbar-toggle').removeClass('toggleHover');}, 100);
//            });
//                $("body").click(function(e) {
//                    if (e.target.id === "myDiv" || $(e.target).parents("#myDiv").size()) {
//                        alert("Inside div");
//                    } else {
//                        alert("Outside div");
//                    }
//                });
//            })
        </script>
        <!-- End of scripts -->
    </body>
</html>