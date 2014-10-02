
var modalData = null;
// =======================================================

function addEvent() {
    $('#buttonAddEvent').button('loading');
    var eventtype = document.getElementById('selectEventType').options[document.getElementById('selectEventType').selectedIndex].text;
    var dates = $('#inputDate').pickadate().pickadate('picker').get('select', 'm/d/yyyy');
    if (eventtype === 'Select Event Type') {
        $('#addEventErrDiv').css('display', 'table');
        //the button is keeping :focus, so this removes it
        $(document).focus();
        $('#buttonAddEvent').button('reset');
        return false;
    }
//    var location = $('#inputLocation').val();
//    if (!location)
//        location = 'THS';
    var location = 'THS';
    $.ajax({
        url: 'drive2.php',
        data: {
            type: 'addEvent',
            eventtype: eventtype,
            location: location,
            dates: dates
        },
        success: function(resp) {
            updateEvents();
            $('#buttonAddEvent').button('reset');
//            $('#inputLocation').val('');
            modalData = null;
            $('#modalAddEvent').modal('hide');
        }
    });
    return true;
}
function addEventType() {
    $('#buttonAddEventType').button('loading');
//    console.log($('#checkboxscores').is(':checked'));
    var name = $('#eventtypename').val();
    var points = $('#eventtypepointvalue').val();
    if (name === '') {
        $('#addEventTypeErrDiv').css('display', 'table');
        document.getElementById('addEventTypeErrLine').innerHTML = '<b>Error</b><br>Please enter an event name.';
        $('#eventtypename').focus();
        $('#buttonAddEventType').button('reset');
        return false;
    }
    if (points === '') {
        $('#addEventTypeErrDiv').css('display', 'table');
        document.getElementById('addEventTypeErrLine').innerHTML = '<b>Error</b><br>Please enter a point value.';
        $('#eventtypepointvalue').focus();
        $('#buttonAddEventType').button('reset');
        return false;
    }
    $.ajax({
        url: 'drive2.php',
        data: {
            type: 'addEventType',
            name: $('#eventtypename').val(),
//            scores: $('#checkboxscores').is(':checked'),
            scores: false,
            points: $('#eventtypepointvalue').val()
        },
        success: function(resp) {
            updateEventTypes();
            $('#buttonAddEventType').button('reset');
            $('#eventtypename').val('');
//            $('#checkboxscores').val('');
            $('#eventtypepointvalue').val('');
            modalData = null;
            $('#modalAddEventType').modal('hide');
        }
    });
    return true;
}

function addMember() {
    $('#buttonAddMember').button('loading');
    $.ajax({
        url: 'drive2.php',
        data: {
            type: 'addMember',
            names: $('#namestoadd').val(),
            'class': $('.yearlabel.active input').attr('id')
        },
        success: function(resp) {
            updateMembers();
            $('#buttonAddMember').button('reset');
            $('#namestoadd').val('');
            modalData = null;
            $('#modalAddMember').modal('hide');
        }
    });
}

//function calcPoints() {
//    $.ajax({
//        url: 'drive2.php',
//        data: {
//            type: 'calcPoints'
//        },
//        success: function(resp) {
////            refreshSettings();
//            console.log('Finished');
//        }
//    });
//}

//function download() {
//    console.log('Downloading...');
//    $.ajax({
//        url: 'drive2.php',
//        data: {
//            type: 'download',
//            merge: false
//        },
//        success: function(resp) {
//            console.log('Downloaded');
//        }
//    });
//}

function changeDay(change) {
    var currdate = $('.datepicker.tutoring').pickadate('picker').get('select', 'yyyy/m/d').split('/');
    currdate = new Date(currdate[0], currdate[1] - 1, currdate[2]);
    var newdate = new Date(currdate.getTime() + change * 86400000);
    $('.datepicker.tutoring').pickadate('picker').set('select', newdate);
}

function clearSession() {
    $.ajax({
        url: 'drive2.php',
        dataType: 'text',
        data: {
            type: 'clearSession'
        }
    });
}

function debugSession() {
    $.ajax({
        url: 'drive2.php',
        dataType: 'json',
        data: {
            type: 'debugSession'
        },
        success: function(resp) {
            console.log(resp);
        }
    });
}

function editMember() {
    $('#buttonEditMember').button('loading');
    $.ajax({
        url: 'drive2.php',
        dataType: 'text',
        data: {
            type: 'editMember',
            newName: $('#inputChangedName').val(),
            access: $('.accesslabel.active input').attr('id'),
            oldName: modalData['name'],
            'class': $('.yearlabel.active input').attr('id')
        },
        success: function(resp) {
            if (resp) {
                $('#yourname').text(resp);
                $.cookie('name', resp, {expires: 200, path: '/'});
            }
            updateMembers();
            $('#buttonEditMember').button('reset');
            modalData = null;
            $('#modalEditMember').modal('hide');
        }
    });
}
function editEvent() {
    $('#buttonEditEvent').button('loading');

    var attendees = [];
    var attendeeJS = $('#editAttendeesBody > tr.success > td');
    for (var index = 0; index < attendeeJS.length; index++) {
        attendees.push(attendeeJS[index].innerHTML);
    }
    $.ajax({
        url: 'drive2.php',
        data: {
            type: 'editEvent',
            oldtype: modalData['eventtype'],
            newtype: document.getElementById('selectNewEventType').options[document.getElementById('selectNewEventType').selectedIndex].text,
            olddate: modalData['date'],
            newdate: $('#newdate').pickadate('picker').get('select', 'm/d/yyyy'),
//            location: $('#newlocation').val(),
            location: 'THS',
            attendees: attendees
        },
        success: function(resp) {
            updateEvents();
            updateMembers();
            modalData = null;
            $('#buttonEditEvent').button('reset');
            $('#modalEditEvent').modal('hide');
        }
    });
}
function editEventType() {
    $('#buttonEditEventType').button('loading');
    var newname = $('#editeventtypename').val();
    if (newname === '')
        newname = modalData['eventname'];
    var points = $('#editeventtypepointvalue').val();
    if (points === '')
        points = modalData['data']['points'];
    console.log(modalData['data']['points'] + ' --> ' + points);
    $.ajax({
        url: 'drive2.php',
        dataType: 'json',
        data: {
            type: 'editEventType',
            newname: newname,
            oldname: modalData['eventname'],
//            hasscores: $('#checkboxeditscores').is(':checked'),
            hasscores: false,
            points: points,
            oldpoints: modalData['data']['points']
        },
        success: function(resp) {
            console.log(resp);
            if (resp['events'])
                updateEvents();
            if (resp['member'])
                updateMembers();
            updateEventTypes();
            $('#buttonEditEventType').button('reset');
            modalData = null;
            $('#modalEditEventType').modal('hide');
        }
    });
}

function getAttendees() {
    $.ajax({
        url: 'drive2.php',
        dataType: 'json',
        data: {
            type: 'getAttendees',
            date: modalData['date'],
            name: modalData['eventtype']
        },
        success: function(resp) {
            var rowNum = 0;
            for (var member in resp) {
                var rowAtt = document.getElementById('editAttendeesBody').insertRow(rowNum);
                rowAtt.insertCell(0).innerHTML = member;
                rowAtt.id = 'attendee:' + member;
                $('[id="attendee:' + member + '"]').addClass('clickable');
                if (resp[member])
                    $('[id="attendee:' + member + '"]').addClass('success');
                else
                    $('[id="attendee:' + member + '"]').addClass('danger');
                $('[id="attendee:' + member + '"]').click(function() {
                    $('[id="' + this.id + '"]').toggleClass('success');
                    $('[id="' + this.id + '"]').toggleClass('danger');
                });
                rowNum++;
            }
            $('#attendeeLoading').remove();
        }
    });
}

function getEventTypes(selectid) {
    $('#' + selectid).prop('disabled', true);
    if (selectid === 'selectEventType')
        var prevSelected = document.getElementById(selectid).options[document.getElementById(selectid).selectedIndex].text;
    $.ajax({
        url: 'drive2.php',
        dataType: 'json',
        data: {
            type: 'getEventTypes'
        },
        success: function(resp) {
            $('#' + selectid).empty();
            if (selectid !== 'selectNewEventType') {
                $('#' + selectid).append('<option id="defSelection" data-hidden="true"></option>');
            }
            for (var index = 0; index < resp.length; index++) {
                var options = document.getElementById(selectid).options;
                options[options.length] = new Option(resp[index]);
            }
            if (selectid === 'selectNewEventType') {
                $('#' + selectid).selectpicker('val', modalData['eventtype']);
            }
            else if (prevSelected) {
                $('#' + selectid).selectpicker('val', prevSelected);
            }
            $('#' + selectid).prop('disabled', false);
            $('#' + selectid).selectpicker('refresh');
        }
    });
}

function getMembers(selectid) {
    $.ajax({
        url: 'drive2.php',
        dataType: 'json',
        data: {
            'type': 'getMembers',
            'noautodownload': true
        },
        success: function(resp) {
            for (var index = 0; index < resp.length; index++) {
                var options = document.getElementById(selectid).options;
                options[options.length] = new Option(resp[index]);
            }
            if (selectid === 'selectName' && $.cookie('name')) {
                $('#' + selectid).selectpicker('val', $.cookie('name'));
            }
            $('#buttonPassword').prop('disabled', false);
            $('#' + selectid).prop('disabled', false);
            $('#' + selectid).selectpicker('refresh');
//            alert(document.getElementById(selectid).innerHTML);
        }
    });
}

function getPasswords() {
    $(document).ready(function() {
        $('#passwordsTableBody').append($('<tr>').append($('<td>').text('Loading...').attr('colspan', 2)).attr('id', 'passwordsTableLoading'));
    });
    $.ajax({
        url: 'drive2.php',
        dataType: 'json',
        data: {
            type: 'getPasswords'
        },
        success: function(resp) {
            var rowNum = 0;
            for (var index in resp) {
                var row = document.getElementById('passwordsTableBody').insertRow(rowNum);
                row.insertCell(0).innerHTML = index;
                row.insertCell(1).innerHTML = resp[index];
                rowNum++;
            }
            $('#passwordsTableLoading').remove();
        }
    });
}

function init() {
    $.ajax({
        url: 'drive2.php',
        data: {
            type: 'init'
        },
        success: function(resp) {
            window.location = 'settings';
        }
    });
}

function isMobile() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent);
}

function listEvents() {
    $.ajax({
        url: 'drive2.php',
        dataType: 'json',
        data: {
            type: 'listEvents'
        },
        success: function(resp) {
            var alerts = 0;
            for (var index in resp) {
                if (!resp[index]['eventtype']) {
                    //Internet explorer is not smart enough to skip over map/filter/indexOf, show as undefined
                    continue;
                }
                var row = document.getElementById('eventBody').insertRow(0);
                row.insertCell(0).innerHTML = resp[index]['date'];
                row.insertCell(1).innerHTML = resp[index]['eventtype'];
                var length = numkeys(resp[index]['attendees']);
                row.insertCell(2).innerHTML = length;
                var editCell = row.insertCell(3);
                editCell.innerHTML = '<i class="fa fa-cogs"></i>';
                var id = JSON.stringify(resp[index]);
                editCell.id = id;
                $('[id="' + id + '"]').addClass('clickable customHover');
                $('[id="' + id + '"]').click(function() {
                    modalData = JSON.parse(this.id);
                    getEventTypes('selectNewEventType');
                    $('#newdate').pickadate({
                        format: 'dddd, mmm d, yyyy',
                        container: 'body',
                        onStart: function() {
//                            this.set('select', modalData['datearr']);
//                            console.log('Changed to: ' + modalData['datearr']);
                        }
                    });
                    $('#newdate').pickadate().pickadate('picker').set('select', modalData['datearr']);
                    $('#newlocation').val(modalData['location']);
                    $('#editAttendeesBody').empty();
                    $('#editAttendeesBody').append($('<tr>').append($('<td>').text('Loading...').attr('colspan', 1)).attr('id', 'attendeeLoading'));
                    getAttendees();
                    $("#modalEditEvent").modal("show");
                });
            }
            $('.loading.events').css('display', 'none');
        }
    });
}
function listEventTypes() {
    $.ajax({
        url: 'drive2.php',
        dataType: 'json',
        data: {
            type: 'listEventTypes'
        },
        success: function(resp) {
            for (var eventname in resp) {
                var rowNum = document.getElementById('eventTypeBody').getElementsByTagName('tr').length;
                var row = document.getElementById('eventTypeBody').insertRow(rowNum);
                row.insertCell(0).innerHTML = eventname;
                row.insertCell(1).innerHTML = resp[eventname]['points'];
                var editCell = row.insertCell(2);
                editCell.innerHTML = '<i class="fa fa-cogs"></i>';
                var id = JSON.stringify({'eventname': eventname, 'data': resp[eventname]});
                editCell.id = id;
                $('[id="' + id + '"]').addClass('clickable customHover');
                $('[id="' + id + '"]').click(function() {
                    modalData = $.parseJSON(this.id);
                    $('#editeventtypepointvalue').val(modalData['data']['points']);
                    $('#editeventtypepointvalue').attr('placeholder', modalData['data']['points']);
//                    $('#checkboxeditscores').prop('checked', modalData['data']['hasscores']);
                    $('#editeventtypename').val(modalData['eventname']);
                    $('#editeventtypename').attr('placeholder', modalData['eventname']);
                    $("#modalEditEventType").modal("show");
                });
            }
            $('.loading.eventType').css('display', 'none');
        }
    });
}

function listMembers() {
    $.ajax({
        url: 'drive2.php',
        dataType: 'json',
        data: {
            type: 'listMembers'
        },
        success: function(resp) {
            var names = resp;
            for (var name in names) {
                var rowNum = document.getElementById('memberBody').getElementsByTagName('tr').length;
                var row = document.getElementById('memberBody').insertRow(rowNum);
                row.insertCell(0).innerHTML = name;
                row.insertCell(1).innerHTML = names[name]['points'];
                var editCell = row.insertCell(2);
                editCell.innerHTML = '<i class="fa fa-cogs"></i>';
                var id = JSON.stringify({'name': name, 'access': names[name]['access'], 'class': names[name]['class']});
                editCell.id = id;
                $('[id="' + id + '"]').addClass('clickable customHover');
                $('[id="' + id + '"]').click(function() {
                    modalData = JSON.parse(this.id);
                    $('#inputChangedName').val(modalData['name']);
                    $('#inputChangedName').attr('placeholder', modalData['name']);
                    $('#labelstudent').removeClass('disabled active activeDisabled');
                    $('#labelofficer').removeClass('disabled active activeDisabled');
                    $('#labelteacher').removeClass('active activeDisabled');
                    $('.yearlabel').removeClass('active');
                    $.ajax({
                        url: 'drive2.php',
                        dataType: 'json',
                        data: {
                            type: 'getUserData'
                        },
                        success: function(resp) {
                            if ($.inArray(resp['access'], ['teacher', 'coder']) && resp['name'] !== modalData['name']) {
                                $('label.' + modalData['access']).addClass('active');
                            } else {
                                $('label.student').addClass('disabled');
                                $('label.officer').addClass('disabled');
                                $('label.' + modalData['access']).addClass('activeDisabled');
                            }
                            $('.yearlabel.' + modalData['class']).addClass('active');
                            $("#modalEditMember").modal("show");
                        }
                    });
                });
                //inserts name into addMember modal list
                rowNum = document.getElementById('addMemberBody').getElementsByTagName('tr').length;
                document.getElementById('addMemberBody').insertRow(rowNum).insertCell(0).innerHTML = name;
            }
            $('.loading.member').css('display', 'none');
        }
    });
}

function listPoints() {
    $(document).ready(function() {
        $('#pointsTable > tbody tr').remove();
//        $('#pointsBody').append($('<tr>').append($('<td>').text('Loading...').attr('colspan', 3)).attr('id', 'pointsLoading'));
    });
    $.ajax({
        url: 'drive2.php',
        dataType: 'json',
        data: {
            type: 'listPoints'
        },
        success: function(resp) {
            var total = 0;
            for (var index = 0; index < resp['events'].length; index++) {
                var row = document.getElementById('pointsBody').insertRow(0);
                row.insertCell(0).innerHTML = resp['events'][index]['date'];
                row.insertCell(1).innerHTML = resp['events'][index]['eventtype'];
                row.insertCell(2).innerHTML = resp['events'][index]['points'];
                if (resp['events'][index]['attended'] === 'success')
                    total += parseInt(resp['events'][index]['points']);
                row.className = resp['events'][index]['attended'];
            }
            $('.points.total').text(total + '/' + resp['reqpoints']);
        }
    });
}

function listTutors(year, month, day) {
    $('.loading.tutoring').css('display', 'block');
    $.ajax({
        url: 'drive2.php',
        dataType: 'json',
        data: {
            type: 'listTutors',
            year: year,
            month: month,
            day: day
        },
        success: function(resp) {
            var currNumRows = $('table.tutoring tbody tr').length;
            while (currNumRows < numkeys(resp)) {
                //add rows if needed
                $('table.tutoring tbody').get(0).insertRow(currNumRows).insertCell(0).className += 'clickable';
                currNumRows++;
            }
            while (currNumRows > numkeys(resp)) {
                //remove rows if needed
                $('table.tutoring tbody').get(0).deleteRow(currNumRows - 1);
                currNumRows--;
            }
            var names = new Array();
            for (var name in resp) {
                names.push(name);
            }
            var index = 0;
            $('table.tutoring tbody tr td').each(function() {
                $(this).text(names[index]);
                if (resp[names[index]]) {
                    $(this).addClass('success');
                    $(this).removeClass('danger');
                } else {
                    $(this).removeClass('success');
                    $(this).addClass('danger');
                }
                $(this).unbind('click');
                $(this).click(function() {
                    $('.loading.tutoring').css('display', 'block');
                    $.ajax({
                        url: 'drive2.php',
                        dataType: 'text',
                        data: {
                            type: 'editTutor',
                            tutor: $(this).text(),
                            didTutor: $(this).hasClass('danger'),
                            date: $('.datepicker.tutoring').pickadate('picker').get('select', 'yyyy/m/d')
                        },
                        success: function(resp) {
                            updateMembers();
                            $('.loading.tutoring').css('display', 'none');
                        }
                    });
                    $(this).toggleClass('success');
                    $(this).toggleClass('danger');
                });
                index++;
            });
            $('.loading.tutoring').css('display', 'none');
        }
    });
}

/* smooth edit row code
 * var currNumRows = $('table.tutoring tbody tr').length;
 while(currNumRows < resp.length) {
 //add rows if needed
 $('table.tutoring tbody').get(0).insertRow(currNumRows).insertCell(0);
 currNumRows++;
 }
 while(currNumRows > resp.length) {
 //remove rows if needed
 $('table.tutoring tbody').get(0).deleteRow(currNumRows - 1);
 currNumRows--;
 }
 var index = 0;
 $('table.tutoring tbody tr td').each(function() {
 $(this).text(resp[index++]);
 });
 */
//function loadPoints() {
//    console.log('Downloading...');
//    $(document).ready(function() {
//        $('#pointsBody').append($('<tr>').append($('<td>').text('Loading...').attr('colspan', 3)).attr('id', 'pointsLoading'));
//    });
//    $.ajax({
//        url: 'drive2.php',
//        data: {
//            type: 'download'
//        },
//        success: function(resp) {
//            console.log('Downloaded');
//            listPoints();
//        }
//    });
//}

function loadSettings() {
//    console.log('Downloading...');
//    $(document).ready(function() {
//        $('#eventBody').append($('<tr>').append($('<td>').text('Loading...').attr('colspan', 4)).attr('id', 'eventLoading'));
//        $('#eventTypeBody').append($('<tr>').append($('<td>').text('Loading...').attr('colspan', 3)).attr('id', 'eventTypeLoading'));
//        $('#memberBody').append($('<tr>').append($('<td>').text('Loading...').attr('colspan', 3)).attr('id', 'memberLoading'));
//    });
//    $.ajax({
//        url: 'drive2.php',
//        data: {
//            type: 'download'
//        },
//        success: function(resp) {
//            console.log('Downloaded');
    listEvents();
    listMembers();
    listEventTypes();
//        }
//    });
}

function login() {
    $('#buttonPassword').button('loading');
    var name = document.getElementById('selectName').options[document.getElementById('selectName').selectedIndex].text;
    var password = $('#inputPassword').val();
    $.ajax({
        url: 'drive2.php',
        dataType: 'json',
        data: {
            type: 'login',
            enteredName: name,
            enteredPassword: password
        },
        success: function(resp) {
            $('#buttonPassword').button('reset');
            if (!resp['err']) {
                $.cookie('name', resp['name'], {expires: 200, path: '/'});
//                $('#innerjumbo').animate({opacity: 0}, 300, function() {
                window.location = 'menu';
//                });
            } else {
                $('#loginErrDiv').css('display', 'block');
                document.getElementById('loginErrLine').innerHTML = '<b>Error</b><br>' + resp['err'];
//                $('inputPassword').val('');
            }
        }
    });
}

function logout() {
    $.ajax({
        url: 'drive2.php',
        data: {
            type: 'logout'
        },
        success: function() {
            window.location = 'index';
        }
    });
}

function numkeys(arr) {
    //workaround for IE8, doesn't support Object.keys()
    var length = 0;
    for (var prop in arr) {
        if (arr.hasOwnProperty(prop))
            length++;
    }
    return length;
}

function refreshPointSheet() {
    $('#reloaderPS').toggleClass("fa-spin clickable");
    $.ajax({
        url: 'drive2.php',
        data: {
            type: 'download'
        },
        success: function(resp) {
            updatePoints();

            $('#reloaderPS').fadeOut(300, function() {
                $('#reloaderPS').toggleClass('fa-spin fa-refresh fa-check');
                $('#reloaderPS').fadeIn(200);
            });
            setTimeout(function() {
                $('#reloaderPS').fadeOut(300, function() {
                    $('#reloaderPS').toggleClass('fa-refresh fa-check clickable');
                    $('#reloaderPS').fadeIn(200);
                });
            }, 1000);

        }
    });
}

function refreshSettings() {
    $('#reloaderS').fadeOut(300, function() {
        $('#reloaderS').toggleClass('fa-refresh fa-save fa-spin clickable');
        $('#reloaderS').fadeIn(300);
    });
    $.ajax({
        url: 'drive2.php',
        dataType: 'json',
        data: {
            type: 'download',
            merge: true,
            refresh: true
        },
        success: function(resp) {
            console.log(resp);
            $('.loading.events, .loading.eventType, .loading.member').css('display', 'block');
            updateEvents();
            updateEventTypes();
            updateMembers();

            $('#reloaderS').fadeOut(300, function() {
                $('#reloaderS').toggleClass('fa-spin fa-refresh fa-check');
                $('#reloaderS').fadeIn(200);
            });
            setTimeout(function() {
                $('#reloaderS').fadeOut(300, function() {
                    $('#reloaderS').toggleClass('fa-save fa-check clickable');
                    $('#reloaderS').fadeIn(200);
                });
            }, 1000);

        }
    });
}

function removeMember() {
    $('#buttonRemoveMember').button('loading');
    $.ajax({
        url: 'drive2.php',
        dataType: 'text',
        data: {
            type: 'removeMember',
            oldName: modalData['name']
        },
        success: function(resp) {
            if (resp === 'logout')
                logout();
            updateMembers();
            updateEvents();
            $('#buttonRemoveMember').button('reset');
            modalData = null;
            $('#modalEditMember').modal('hide');
        }
    });
}
function removeEvent() {
    $('#buttonRemoveEvent').button('loading');
    $.ajax({
        url: 'drive2.php',
        data: {
            type: 'removeEvent',
            date: modalData['date'],
            name: modalData['eventtype']
        },
        success: function(resp) {
            updateEvents();
            updateMembers();
            modalData = null;
            $('#buttonRemoveEvent').button('reset');
            $('#modalEditEvent').modal('hide');
        }
    });
}
function removeEventType() {
    $('#buttonRemoveEventType').button('loading');
    $.ajax({
        url: 'drive2.php',
        data: {
            type: 'removeEventType',
            eventtype: modalData['eventname']
        },
        success: function(resp) {
            updateEventTypes();
            updateEvents();
            updateMembers();
            modalData = null;
            $('#buttonRemoveEventType').button('reset');
            $('#modalEditEventType').modal('hide');
        }
    });
}

function setReqPoints() {
    var reqp = $('#inputReqPoints').val();
    if (reqp === '') {
        $('.alert').css('display', 'none');
        $('#reqPointsErrDiv').css('display', 'block');
        $('html, body').animate({scrollTop: $(document).height() - $(window).height()}, 0);
        $('#setReqPointsButton').button('reset');
        return;
    }
    $.ajax({
        url: 'drive2.php',
        dataType: 'text',
        data: {
            type: 'setReqPoints',
            reqpoints: reqp
        },
        success: function(resp) {
            if (resp !== 'denied')
                $('#inputReqPoints').attr('placeholder', 'Currently ' + resp + ' points');
            $('#inputReqPoints').val('');
            $('#reqPointsErrDiv').css('display', 'none');
            $('#setReqPointsButton').button('reset');
        }
    });
}

function toggleTable(makeVisible, tableType) {
    if (makeVisible) {
        $('#' + tableType + 'Body').css('display', 'table-row-group');
        $('#' + tableType + 'ShowRow').css('display', 'none');
        $('#' + tableType + 'HideRow').css('display', 'table-row');
    } else {
        $('#' + tableType + 'Body').css('display', 'none');
        $('#' + tableType + 'HideRow').css('display', 'none');
        $('#' + tableType + 'ShowRow').css('display', 'table-row');
    }
}

function updateEvents() {
    $.ajax({
        url: 'drive2.php',
        dataType: 'json',
        data: {
            type: 'listEvents'
        },
        success: function(resp) {
            var currNumRows = $('table.events tbody tr').length;
            while (currNumRows < resp.length) {
                var row = $('table.events tbody').get(0).insertRow(currNumRows);
                row.insertCell(0);
                row.insertCell(1);
                row.insertCell(2);
                row.insertCell(3);
                currNumRows++;
            }
            while (currNumRows > resp.length) {
                $('table.events tbody').get(0).deleteRow(currNumRows - 1);
                currNumRows--;
            }
            var index = resp.length - 1;
            $('table.events tbody tr').each(function() {
                $(this).children(':nth-child(1)').text(resp[index]['date']);
                $(this).children(':nth-child(2)').text(resp[index]['eventtype']);
                $(this).children(':nth-child(3)').text(numkeys(resp[index]['attendees']));
                $(this).children(':nth-child(4)').html('<i class="fa fa-cogs"></i>');
                $(this).children(':nth-child(4)').attr('id', JSON.stringify(resp[index]));
                index--;
            });
            $('.loading.events').css('display', 'none');
        }
    });
}

function updateEventTypes() {
    $.ajax({
        url: 'drive2.php',
        dataType: 'json',
        data: {
            type: 'listEventTypes'
        },
        success: function(resp) {
            var currNumRows = $('table.eventType tbody tr').length;
            while (currNumRows < numkeys(resp)) {
                var row = $('table.eventType tbody').get(0).insertRow(currNumRows);
                row.insertCell(0);
                row.insertCell(1);
                row.insertCell(2);
                currNumRows++;
            }
            while (currNumRows > numkeys(resp)) {
                $('table.eventType tbody').get(0).deleteRow(currNumRows - 1);
                currNumRows--;
            }
            var types = new Array();
            for (var type in resp) {
                types.push(type);
            }
            var index = 0;
            $('table.eventType tbody tr').each(function() {
                $(this).children(':nth-child(1)').text(types[index]);
                $(this).children(':nth-child(2)').text(resp[types[index]]['points']);
                $(this).children(':nth-child(3)').html('<i class="fa fa-cogs"></i>');
                $(this).children(':nth-child(3)').attr('id', JSON.stringify({'eventname': types[index], 'data': resp[types[index]]}));
                index++;
            });
            $('.loading.eventType').css('display', 'none');
        }
    });
}

function updateMembers() {
    $.ajax({
        url: 'drive2.php',
        dataType: 'json',
        data: {
            type: 'listMembers'
        },
        success: function(resp) {
            console.log(resp);
            var currNumRows = $('table.member.main tbody tr').length;
            console.log(currNumRows);
            while (currNumRows < numkeys(resp)) {
                console.log('Added Row');
                var row = $('table.member.main tbody').get(0).insertRow(currNumRows);
                row.insertCell(0);
                row.insertCell(1);
                row.insertCell(2);
                currNumRows++;
            }
            while (currNumRows > numkeys(resp)) {
                console.log('Removed Row');
                $('table.member.main tbody').get(0).deleteRow(currNumRows - 1);
                currNumRows--;
            }
            var names = new Array();
            $('table.member.add tbody tr').remove();
            var index = 0;
            for (var name in resp) {
                names.push(name);
                $('table.member.add tbody').get(0).insertRow(index++).insertCell(0).innerHTML = name;
            }
            index = 0;
            $('table.member.main tbody tr').each(function() {
                $(this).children(':nth-child(1)').text(names[index]);
                $(this).children(':nth-child(2)').text(resp[names[index]]['points']);
                $(this).children(':nth-child(3)').html('<i class="fa fa-cogs"></i>');
                $(this).children(':nth-child(3)').attr('id', JSON.stringify({'name': names[index], 'access': resp[names[index]]['access']}));
                index++;
            });
            $('.loading.member').css('display', 'none');
        }
    });
}

function updatePoints() {
    $.ajax({
        url: 'drive2.php',
        dataType: 'json',
        data: {
            type: 'listPoints'
        },
        success: function(resp) {
            console.log(resp);
            var currNumRows = $('table.points tbody tr').length;
            console.log(currNumRows);
            while (currNumRows < resp['events'].length) {
                console.log('Added Row');
                var row = $('table.points tbody').get(0).insertRow(currNumRows);
                row.insertCell(0);
                row.insertCell(1);
                row.insertCell(2);
                currNumRows++;
            }
            while (currNumRows > resp['events'].length) {
                console.log('Removed Row');
                $('table.points tbody').get(0).deleteRow(currNumRows - 1);
                currNumRows--;
            }
            var index = resp['events'].length - 1;
            var total = 0;
            $('table.points tbody tr').each(function() {
                $(this).children(':nth-child(1)').text(resp['events'][index]['date']);
                $(this).children(':nth-child(2)').text(resp['events'][index]['eventtype']);
                $(this).children(':nth-child(3)').text(resp['events'][index]['points']);
                if (resp['events'][index]['attended'] === 'success')
                    total += parseInt(resp['events'][index]['points']);
                $(this).removeClass('danger success');
                $(this).addClass(resp['events'][index]['attended']);
                index--;
            });
            $('.points.total').text(total + '/' + resp['reqpoints']);
//            $('.loading.points').css('display', 'none');
        }
    });
}

function upload() {
    upload(null);
}

function upload(clickedFrom) {
    $.ajax({
        url: 'drive2.php',
        data: {
            type: 'upload'
        },
        success: function(resp) {
            if (clickedFrom) {
                $(clickedFrom).button('reset');
                $('#saveWarning').css('display', 'none');
            }

        }
    });
}