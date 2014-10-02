<?php

session_start();

syslog(LOG_DEBUG, '(D2) Recieved Req: ' . $_GET['type']);

$loaded = false;

if (!array_key_exists('data', $_SESSION) && !array_key_exists('noautodownload', $_GET)) {
    syslog(LOG_ALERT, 'Automatic Download');
    download();
}

echo call_user_func($_GET['type']);

function addEvent() {
    $dates = explode(',', $_GET['dates']);
    foreach ($dates as $date) {
        $date = trim($date);
        if ($date != '') {
            $datearr = explode('/', $date);
            $_SESSION['data']['events'][$datearr[2]][$datearr[0]][$datearr[1]][$_GET['eventtype']] = array(
                'attendees' => array(),
                'location' => $_GET['location']
            );
            syslog(LOG_INFO, 'Added event of type ' . $_GET['eventtype'] . ' on ' . $date);
        }
    }
    $_SESSION['shouldUpload'] = true;
}

function addEventType() {
    $_SESSION['data']['eventtypes'][trim($_GET['name'])] = array(
        'hasscores' => $_GET['scores'] === 'true',
        'points' => intval($_GET['points'])
    );
    $_SESSION['shouldUpload'] = true;
    syslog(LOG_INFO, 'Added event type: ' . $_GET['name']);
}

function addMember() {
    $names = explode(',', $_GET['names']);
    foreach ($names as $name) {
        $name = trim($name);
        if ($name != '') {
            $_SESSION['data']['names'][$name] = array(
                'password' => rand(0, 9999),
                'access' => 'student',
                'points' => 0,
                'class' => $_GET['class']
            );
            syslog(LOG_INFO, 'Added new member: ' . $name);
        }
    }
    $_SESSION['shouldUpload'] = true;
}

function addTutors() {
    $year = $_GET['year'];
    $month = $_GET['month'];
    $day = $_GET['day'];
    $tutors = explode(',', $_GET['tutors']);
    foreach ($tutors as $tutor) {
        $tutor = trim($tutor);
        if ($tutor != '') {
            array_push($_SESSION['data']['tutoringhours'][$year][$month][$day]['tutors'], $tutor);
        }
    }
    $_SESSION['data']['tutoringhours'][$year][$month][$day]['tutors'] = $tutors;
}

function arrayRecursiveDiff($aArray1, $aArray2) {
    $aReturn = array();

    foreach ($aArray1 as $mKey => $mValue) {
        if (array_key_exists($mKey, $aArray2)) {
            if (is_array($mValue)) {
                $aRecursiveDiff = arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                if (count($aRecursiveDiff)) {
                    $aReturn[$mKey] = $aRecursiveDiff;
                }
            } else {
                if ($mValue != $aArray2[$mKey]) {
                    $aReturn[$mKey] = $mValue;
                }
            }
        } else {
            $aReturn[$mKey] = $mValue;
        }
    }
    return $aReturn;
}

function calcPoints() {
    foreach (array_keys($_SESSION['data']['names']) as $name) {
        //Teachers do not have points
        if ($_SESSION['data']['names'][$name]['access'] === 'teacher')
            continue;
        $_SESSION['data']['names'][$name]['points'] = 0;
        //go through events
        foreach (array_keys($_SESSION['data']['events']) as $year) {
            foreach (array_keys($_SESSION['data']['events'][$year]) as $month) {
                foreach ($_SESSION['data']['events'][$year][$month] as $day => $todaysevents) {
                    foreach ($todaysevents as $eventtype => $eventdata) {
                        if (array_key_exists($name, $eventdata['attendees'])) {
                            $_SESSION['data']['names'][$name]['points'] += $_SESSION['data']['eventtypes'][$eventtype]['points'];
                        }
                    }
                }
            }
        }
        //go through tutoring hours
        foreach (array_keys($_SESSION['data']['tutoringhours']) as $year) {
            foreach (array_keys($_SESSION['data']['tutoringhours'][$year]) as $month) {
                foreach (array_keys($_SESSION['data']['tutoringhours'][$year][$month]) as $day) {
                    if (in_array($name, $_SESSION['data']['tutoringhours'][$year][$month][$day]['tutors'])) {
                        $_SESSION['data']['names'][$name]['points'] += $_SESSION['data']['eventtypes']['Tutoring']['points'];
                    }
                }
            }
        }
    }
}

function createDataFile($content) {
    global $service;

    $file = new Google_DriveFile();

    $file->setTitle('Last Edit by: ' . $_SESSION['name']);
    $file->setMimeType('text/plain');

    return $service->files->insert($file, array(
                'data' => ($content ? $content : 'No Data'),
                'mimeType' => 'text/plain',
    ));
}

function debugSession() {
    if(in_array($_SESSION['access'], ['teacher', 'coder']))
            return 'Invalid Access Level';
    return json_encode($_SESSION['data']['tutoringhours']);
}

function clearSession() {
    if(in_array($_SESSION['access'], ['teacher', 'coder']))
            return;
    unset($_SESSION['data']['tutoringhours']);
}

function deleteDataFile($fileid) {
    global $service;
    if ($fileid)
        $service->files->trash($fileid);
}

function download() {
    loadDrive();
    $downloadeddata = json_decode(getContent(getDataFile()), true);
    $return = array(
        'downloaded' => $downloadeddata,
        'changed' => null,
        'diff, old->new' => null,
        'diff, new->old' => null
    );
//    if (array_key_exists('merge', $_GET)) {
//        //recursively go through array and when changed values are found, make change to $downloadeddata array
//        $changes = arrayRecursiveDiff($_SESSION['data'], $_SESSION['olddata']);
//        $downloadeddata = array_replace_recursive($downloadeddata, $changes);
//
//        if ($changes)
//            upload($downloadeddata);
//    }
//    $return['diff, old->new'] = $changes;
//    $return['diff, new->old'] = arrayRecursiveDiff($_SESSION['olddata'], $_SESSION['data']);
    if (array_key_exists('refresh', $_GET)) {
        upload();
    } else
        $_SESSION['data'] = $downloadeddata;
//    $_SESSION['olddata'] = $downloadeddata;
//    $return['changed'] = $downloadeddata;
//    if (!array_key_exists($_SESSION['name'], $_SESSION['data']['names'])) {
//        //the name you signed in as has changed due to the download, force logout
//        logout();
//    }
    syslog(LOG_INFO, 'Downloaded Data');
    return json_encode($return);
}

function editEvent() {
    $oldtype = $_GET['oldtype'];
    $newtype = $_GET['newtype'];
    $olddate = explode('/', $_GET['olddate']);
    $newdate = explode('/', $_GET['newdate']);
    $location = $_GET['location'];
    $attendees = $_GET['attendees'];
//    $oldattendees = $_SESSION['data']['events'][$olddate[2]][$olddate[0]][$olddate[1]][$oldtype]['attendees'];
    $attendeelist = array();
    //convert attendees
    foreach ($attendees as $attendee) {
        $attendeelist[$attendee] = null;
    }

    unset($_SESSION['data']['events'][$olddate[2]][$olddate[0]][$olddate[1]][$oldtype]);
    $_SESSION['data']['events'][$newdate[2]][$newdate[0]][$newdate[1]][$newtype] = array(
        'attendees' => $attendeelist,
        'location' => $location
    );
//    $oldpointvalue = $_SESSION['data']['eventtypes'][$oldtype]['points'];
//    $newpointvalue = $_SESSION['data']['eventtypes'][$newtype]['points'];
    //adjusts point totals for edited members
//    foreach (array_keys($_SESSION['data']['names']) as $member) {
//        if (in_array($oldattendees, $member)) {
//            if (!in_array($attendees, $member)) {
//                $_SESSION['data']['names'][$member]['points'] -= $oldpointvalue;
//            }
//        } else {
//            if (in_array($attendees, $member))
//                $_SESSION['data']['names'][$member]['points'] += $newpointvalue;
//        }
//    }
    //TODO: Update name in list

    $_SESSION['shouldUpload'] = true;
    //something was accidentally deleted from this line I think...damn
    syslog(LOG_INFO, 'Edited event: ' . $oldtype);
}

function editEventType() {
    $oldname = $_GET['oldname'];
    $newname = $_GET['newname'];
    $hasscores = $_GET['hasscores'];
    $newpointvalue = $_GET['points'];
    $oldpointvalue = $_GET['oldpoints'];

    $update = array();

    unset($_SESSION['data']['eventtypes'][$oldname]);
    $_SESSION['data']['eventtypes'][$newname]['points'] = $newpointvalue;
    $_SESSION['data']['eventtypes'][$newname]['hasscores'] = $hasscores;

    if ($newname !== $oldname) {
        //search for events with this type
        foreach ($_SESSION['data']['events'] as $year => $montharr) {
            foreach ($montharr as $month => $dayarr) {
                foreach ($dayarr as $day => $todaysevents) {
                    foreach ($todaysevents as $eventtype => $eventdata) {
                        if ($oldname === $eventtype) {
                            $_SESSION['data']['events'][$year][$month][$day][$newname] = $_SESSION['data']['events'][$year][$month][$day][$oldname];
                            unset($_SESSION['data']['events'][$year][$month][$day][$oldname]);
                        }
                    }
                }
            }
        }
        syslog(LOG_INFO, 'Changed event type ' . $oldname . ' to ' . $newname);
        $update['events'] = true;
    }
    if ($newpointvalue !== $oldpointvalue) {
//        $pointdiff = $newpointvalue - $oldpointvalue;
//        if ($newname === 'Tutoring') {
//            //go through tutoring hours, recalculate
//            foreach (array_keys($_SESSION['data']['tutoringhours']) as $year) {
//                foreach (array_keys($_SESSION['data']['tutoringhours'][$year]) as $month) {
//                    foreach (array_keys($_SESSION['data']['tutoringhours'][$year][$month]) as $day) {
//                        //for each name, adjust
//                        foreach ($_SESSION['data']['tutoringhours'][$year][$month][$day]['tutors'] as $name) {
//                            $_SESSION['data']['names'][$name]['points'] += $pointdiff;
//                        }
//                    }
//                }
//            }
//        } else {
//            //go through events and calculate
//            foreach (array_keys($_SESSION['data']['events']) as $year) {
//                foreach (array_keys($_SESSION['data']['events'][$year]) as $month) {
//                    foreach ($_SESSION['data']['events'][$year][$month] as $day => $todaysevents) {
//                        foreach ($todaysevents as $eventtype => $eventdata) {
//                            if ($eventtype === $newname) {
//                                //for each name, adjust
//                                foreach (array_keys($_SESSION['data']['events'][$year][$month][$day][$eventtype]['attendees']) as $name) {
//                                    $_SESSION['data']['names'][$name]['points'] += $pointdiff;
//                                }
//                            }
//                        }
//                    }
//                }
//            }
//        }
//        //go through each event, 
        $update['member'] = true;
    }
    $_SESSION['shouldUpload'] = true;
    syslog(LOG_INFO, 'Changed event type: ' . $oldname);
    return json_encode($update);
}

function editMember() {
    $newName = $_GET['newName'];
    $oldName = $_GET['oldName'];
    $access = $_GET['access'];
    $class = $_GET['class'];

    if ($newName == '') {
        $_SESSION['data']['names'][$oldName]['access'] = $access;
        return;
    }
    $_SESSION['data']['names'][$newName] = $_SESSION['data']['names'][$oldName];
    if (in_array($_SESSION['access'], ['teacher', 'coder']))
        $_SESSION['data']['names'][$newName]['access'] = $access;
    $_SESSION['data']['names'][$newName]['class'] = $class;
    if ($newName !== $oldName) {
        unset($_SESSION['data']['names'][$oldName]);
        //search for attendences of oldName
        foreach ($_SESSION['data']['events'] as $year => $montharr) {
            foreach ($montharr as $month => $dayarr) {
                foreach ($dayarr as $day => $todaysevents) {
                    foreach ($todaysevents as $eventtype => $eventdata) {
                        if (array_key_exists($oldName, $eventdata['attendees'])) {
                            $_SESSION['data']['events'][$year][$month][$day][$eventtype]['attendees'][$newName] = $_SESSION['data']['events'][$year][$month][$day][$eventtype]['attendees'][$oldName];
                            unset($_SESSION['data']['events'][$year][$month][$day][$eventtype]['attendees'][$oldName]);
                        }
                    }
                }
            }
        }
        if ($_SESSION['name'] === $oldName) {
            $_SESSION['name'] = $newName;
            $_SESSION['shouldUpload'] = true;
            syslog(LOG_INFO, 'Changed ' . $oldName . ' to ' . $newName . (in_array($_SESSION['access'], ['teacher', 'coder']) ? ' with access level: ' . $access : ''));
            return $newName;
        }
    }
    $_SESSION['shouldUpload'] = true;
    syslog(LOG_INFO, 'Changed ' . $oldName . ' to ' . $newName . ' with access level: ' . access);
}

function editTutor() {
    $tutor = $_GET['tutor'];
    $date = explode('/', $_GET['date']);
    if ($_GET['didTutor'] === 'true') {
        array_push($_SESSION['data']['tutoringhours'][$date[0]][$date[1]][$date[2]]['tutors'], $tutor);
//        $_SESSION['data']['names'][$tutor]['points'] += $_SESSION['data']['eventtypes']['Tutoring']['points'];
    } else {
        $key = array_search($tutor, $_SESSION['data']['tutoringhours'][$date[0]][$date[1]][$date[2]]['tutors']);
        unset($_SESSION['data']['tutoringhours'][$date[0]][$date[1]][$date[2]]['tutors'][$key]);
//        $_SESSION['data']['names'][$tutor]['points'] -= $_SESSION['data']['eventtypes']['Tutoring']['points'];
    }
    $_SESSION['shouldUpload'] = true;
}

function getAttendees() {
    $members = array();
    $date = explode('/', $_GET['date']);
    $type = $_GET['name'];
    uksort($_SESSION['data']['events'][$date[2]][$date[0]][$date[1]][$type]['attendees'], 'strcasecmp');
    foreach (array_keys($_SESSION['data']['names']) as $name) {
        $members[$name] = false;
    }
    foreach (array_keys($_SESSION['data']['events'][$date[2]][$date[0]][$date[1]][$type]['attendees']) as $attendee) {
        $members[$attendee] = true;
    }
    return json_encode($members);
}

function getContent($file) {
    $downloadUrl = $file->getDownloadUrl();
    if ($downloadUrl) {
        $request = new Google_HttpRequest($downloadUrl, 'GET', null, null);
        $httpRequest = Google_Client::$io->authenticatedRequest($request);
        if ($httpRequest->getResponseHttpCode() == 200) {
            return $httpRequest->getResponseBody();
        } else {
            syslog(LOG_ERR, "ERROR (getContent): HTTP error");
        }
    } else {
        syslog(LOG_ERR, "ERROR (getContent): No content on file");
    }
}

function getDataFile() {
    global $service;
    $files = $service->files->listFiles(array(
        'maxResults' => 1,
        'q' => 'trashed=false'
    ));
    return $files->getItems()[0];
}

function getEventTypes() {
    uksort($_SESSION['data']['eventtypes'], 'strcasecmp');
    return json_encode(array_keys($_SESSION['data']['eventtypes']));
}

function getMembers() {
    download();
    uksort($_SESSION['data']['names'], 'strcasecmp');
    return json_encode(array_keys($_SESSION['data']['names']));
}

function getPasswords() {
    $passwords = array();
    uksort($_SESSION['data']['names'], 'strcasecmp');
    foreach (array_keys($_SESSION['data']['names']) as $name) {
        $passwords[$name] = $_SESSION['data']['names'][$name]['password'];
    }
    return json_encode($passwords);
}

//function getPoints() {
//    
//}

function getUserData() {
    return json_encode(array(
        'name' => $_SESSION['name'],
        'access' => $_SESSION['access']
    ));
}

function init() {
    if (!in_array($_SESSION['access'], ['teacher', 'coder']))
        return;
//    $_SESSION['data'] = array(
//        'names' => array(
//            'Glenn Hyman' => array(
//                'password' => 1234,
//                'access' => 'teacher'
//            ),
//            'Colin King' => array(
//                'password' => 5678,
//                'access' => 'coder',
//                'points' => 0,
//                'class' => 'senior'
//            )
//        ),
//        'events' => array(
//            '13' => array(
//                '12' => array(
//                    '12' => array(
//                        'Meeting' => array(
//                            'attendees' => array(
//                                'Colin King' => null,
//                                'Glenn Hyman' => null
//                            ),
//                            'location' => 'THS'
//                        )
//                    ),
//                    '14' => array(
//                        'RCML' => array(
//                            'attendees' => array(
//                                'Colin King' => 5
//                            ),
//                            'location' => 'THS'
//                        )
//                    )
//                )
//            ),
//            '14' => array(
//                '1' => array(
//                    '2' => array(
//                        'BCML' => array(
//                            'attendees' => array(
//                                'Glenn Hyman' => 999
//                            ),
//                            'location' => 'DHS'
//                        )
//                    )
//                )
//            )
//        ),
//        'eventtypes' => array(
//            'Meeting' => array(
//                'hasscores' => false,
//                'points' => 3
//            ),
//            'RCML' => array(
//                'hasscores' => true,
//                'points' => 6
//            ),
//            'BCML' => array(
//                'hasscores' => true,
//                'points' => 4
//            ),
//            'Tutoring' => array(
//                 'hasscores' => false,
//                 'points' => 3
//            )
//        ),
//        'reqpoints' => 60
//    );
    //same as the above, except with event types and events from 2014 sheet
    $_SESSION['data'] = json_decode('{"names":{"Colin King":{"password":' . strval(rand(0, 9999)) . ',"access":"officer","points":0,"graduationyear":2014},"Glenn Hyman":{"password":' . strval(rand(0, 9999)) . ',"access":"teacher","points":"-"}},"events":{"14":{"4":{"10":{"Meeting":{"attendees":[],"location":"THS"}},"1":{"Math Month Questions":{"attendees":[],"location":"THS"}}},"3":{"14":{"Pi Day Help":{"attendees":[],"location":"THS"}},"13":{"AIME":{"attendees":[],"location":"THS"}},"11":{"MDML":{"attendees":[],"location":"THS"}},"10":{"RCML":{"attendees":[],"location":"THS"}},"6":{"Meeting":{"attendees":[],"location":"THS"}}},"2":{"20":{"Meeting":{"attendees":[],"location":"THS"}},"17":[],"11":{"MDML":{"attendees":[],"location":"THS"}},"10":{"RCML":{"attendees":[],"location":"THS"}},"5":[],"4":{"AMC":{"attendees":[],"location":"THS"}}},"1":{"27":{"Meeting":{"attendees":[],"location":"THS"}},"16":{"Meeting":{"attendees":[],"location":"THS"}},"14":{"MDML":{"attendees":[],"location":"THS"}},"13":{"RCML":{"attendees":[],"location":"THS"}},"8":{"Meeting":{"attendees":[],"location":"THS"}},"2":[]}},"13":{"12":{"17":{"Meeting":{"attendees":[],"location":"THS"}},"14":[],"12":[],"3":{"MDML":{"attendees":[],"location":"THS"}}},"11":{"20":{"UMCP":{"attendees":[],"location":"THS"}},"12":{"MDML":{"attendees":[],"location":"THS"}},"8":{"Meeting":{"attendees":[],"location":"THS"}}},"10":{"24":{"Meeting":{"attendees":[],"location":"THS"}},"23":{"UMCP":{"attendees":[],"location":"THS"}},"21":{"RCML":{"attendees":[],"location":"THS"}},"15":{"MDML":{"attendees":[],"location":"THS"}},"4":{"Meeting":{"attendees":[],"location":"THS"}}}}},"eventtypes":{"AIME":{"points":"5","hasscores":"false"},"AMC":{"hasscores":false,"points":5},"BCML":{"points":"5","hasscores":"false"},"Math Month Questions":{"hasscores":false,"points":3},"MDML":{"hasscores":false,"points":3},"Meeting":{"hasscores":false,"points":3},"Pi Day Help":{"hasscores":false,"points":3},"RCML":{"points":"4","hasscores":"false"},"UMCP":{"hasscores":false,"points":3}},"reqpoints":"50"}', true);
    upload();
    if (!array_key_exists($_SESSION['name'], $_SESSION['data']['names'])) {
        //the name you signed in as has changed due to the download, force logout
        logout();
    }
}

function listMembers() {
    calcPoints();
    $name_points = array();

    uksort($_SESSION['data']['names'], 'strcasecmp');
    if (in_array($_SESSION['access'], ['teacher', 'coder'])) {
        foreach ($_SESSION['data']['names'] as $name => $nameData) {
            $name_points[$name] = array(
                'points' => $nameData['points'],
                'access' => $nameData['access'],
                'class' => $nameData['class']
            );
        }
    } else {
        foreach ($_SESSION['data']['names'] as $name => $nameData) {
            $name_points[$name] = array(
                'points' => 'Hidden',
                'access' => $nameData['access'],
                'class' => $nameData['class']
            );
        }
    }
    return json_encode($name_points);
}

function listEvents() {
    $index = 0;
    $returneventdata = array();
    krsort($_SESSION['data']['events'], SORT_NUMERIC);
    foreach (array_keys($_SESSION['data']['events']) as $year) {
        krsort($_SESSION['data']['events'][$year], SORT_NUMERIC);
        foreach (array_keys($_SESSION['data']['events'][$year]) as $month) {
            krsort($_SESSION['data']['events'][$year][$month], SORT_NUMERIC);
            foreach ($_SESSION['data']['events'][$year][$month] as $day => $todaysevents) {
                foreach ($todaysevents as $eventtype => $eventdata) {
                    $returneventdata[$index]['datearr'] = [$year, $month - 1, $day];
                    $returneventdata[$index]['date'] = $month . '/' . $day . '/' . $year;
                    $returneventdata[$index]['eventtype'] = $eventtype;
                    uksort($_SESSION['data']['events'][$year][$month][$day][$eventtype]['attendees'], 'strcasecmp');
                    $returneventdata[$index]['attendees'] = $_SESSION['data']['events'][$year][$month][$day][$eventtype]['attendees'];
                    $returneventdata[$index]['location'] = $eventdata['location'];
                    $index++;
                }
            }
        }
    }
    return json_encode($returneventdata);
}

function listEventTypes() {
    uksort($_SESSION['data']['eventtypes'], 'strcasecmp');
    return json_encode($_SESSION['data']['eventtypes']);
}

//from the php website, works like array merge recursive except keeps numerical keys
function array_merge_recursive_new() {

    $arrays = func_get_args();
    $base = array_shift($arrays);

    foreach ($arrays as $array) {
        reset($base); //important
        while (list($key, $value) = @each($array)) {
            if (is_array($value) && @is_array($base[$key])) {
                $base[$key] = array_merge_recursive_new($base[$key], $value);
            } else {
                $base[$key] = $value;
            }
        }
    }

    return $base;
}

function listPoints() {
    $name = $_SESSION['name'];
    $resp = array(
        'events' => array(),
        'reqpoints' => $_SESSION['data']['reqpoints']
    );
    $events = array_merge_recursive_new($_SESSION['data']['events'], $_SESSION['data']['tutoringhours']);
//    syslog(LOG_DEBUG, json_encode($events));
//    syslog(LOG_DEBUG, json_encode($_SESSION['data']['events'] + $_SESSION['data']['tutoringhours']));
    syslog(LOG_DEBUG, json_encode(array_merge_recursive_new($_SESSION['data']['events'], $_SESSION['data']['tutoringhours'])));
//    syslog(LOG_DEBUG, json_encode($_SESSION['data']['events']));
//    syslog(LOG_DEBUG, json_encode($_SESSION['data']['tutoringhours']));
    $index = 0;
    krsort($events, SORT_NUMERIC);
    foreach (array_keys($events) as $year) {
        krsort($events[$year], SORT_NUMERIC);
        foreach (array_keys($events[$year]) as $month) {
            krsort($events[$year][$month], SORT_NUMERIC);
            foreach ($events[$year][$month] as $day => $types) {
                foreach ($types as $type => $typedata) {
                    if ($type === 'tutors') {
                        if (in_array($name, $typedata)) {
                            $resp['events'][$index] = array(
                                'date' => $month . '/' . $day . '/' . $year,
                                'eventtype' => 'Tutoring',
                                'points' => $_SESSION['data']['eventtypes']['Tutoring']['points'],
                                'attended' => 'success'
                            );
                        } else {
                            //nothing will be added, so we don't want the index to change
                            $index-=1;
                        }
                    } else {
                        //put the event in the array
                        $resp['events'][$index] = array(
                            'date' => $month . '/' . $day . '/' . $year,
                            'eventtype' => $type,
                            'points' => $_SESSION['data']['eventtypes'][$type]['points'],
                            'attended' => (array_key_exists($name, $typedata['attendees']) ? 'success' : 'danger')
                        );
                    }
                    $index += 1;
                }
            }
        }
    }
    return json_encode($resp);
}

function listTutors() {
    $year = $_GET['year'];
    $month = $_GET['month'];
    $day = $_GET['day'];
    uksort($_SESSION['data']['names'], 'strcasecmp');
    $members = array();
    foreach (array_keys($_SESSION['data']['names']) as $name) {
        $members[$name] = false;
    }
    if (!isset($_SESSION['data']['tutoringhours'][$year][$month][$day]['tutors'])) {
        syslog(LOG_INFO, 'Clearing data>tutoringhours>' . $year . '>' . $month . '>' . $day . '>' . 'tutors');
        $_SESSION['data']['tutoringhours'][$year][$month][$day]['tutors'] = array();
    } else {
        syslog(LOG_INFO, json_encode($_SESSION['data']['tutoringhours'][$year][$month][$day]));
        foreach ($_SESSION['data']['tutoringhours'][$year][$month][$day]['tutors'] as $tutor) {
            syslog(LOG_INFO, $tutor . ' tutored!');
            $members[$tutor] = true;
        }
    }
    return json_encode($members);
}

function loadDrive() {
    global $service, $loaded;
    if ($loaded)
        return;
    require_once 'google-api-php-client/src/Google_Client.php';
    require_once 'google-api-php-client/src/contrib/Google_DriveService.php';

//reads in client ID from file
    $cidfile = 'data/clientID.txt';
    $handlecid = fopen($cidfile, 'r');
    $clientId = fread($handlecid, filesize($cidfile));
//reads in client secret from file
    $csfile = 'data/clientSecret.txt';
    $handlecs = fopen($csfile, 'r');
    $clientSecret = fread($handlecs, filesize($csfile));
//reads in access token from file
    $tokenfile = 'data/token.txt';
    $handlet = fopen($tokenfile, 'r');
    $accessToken = fread($handlet, filesize($tokenfile));

//sets up client
    $client = new Google_Client();
    $client->setClientId($clientId);
    $client->setClientSecret($clientSecret);
    $client->setRedirectUri('http://towsonhsmathpoints.appspot.com/');
    $client->setScopes(array('https://www.googleapis.com/auth/drive'));
    $client->setAccessType('offline');

//sets access token
    $client->setAccessToken($accessToken);

//initializes service
    $service = new Google_DriveService($client);
    syslog(LOG_INFO, 'Loaded GD');
    $loaded = true;
}

function login() {
    $resp = array();
    $enteredPassword = $_GET['enteredPassword'];
    $enteredName = $_GET['enteredName'];
    if ($enteredName === 'Select your name') {
        $resp['err'] = 'Please select your name.';
        return json_encode($resp);
    }
    if ($enteredPassword == null) {
        $resp['err'] = 'Please enter your password.';
        return json_encode($resp);
    }
    if (intval($enteredPassword) === $_SESSION['data']['names'][$enteredName]['password']) {
        $_SESSION['name'] = $enteredName;
        $_SESSION['access'] = $_SESSION['data']['names'][$enteredName]['access'];
        $resp['name'] = $enteredName;
        $resp['access'] = $_SESSION['access'];
    } else {
        $resp['err'] = 'Incorrect password.';
    }
    return json_encode($resp);
}

function logout() {
    $_SESSION['name'] = NULL;
    $_SESSION['access'] = NULL;
}

function makeChanges($change) {
    //downloads, merges changes, returns new content to upload
    return array_replace_recursive(json_decode(getContent(getDataFile()), true), $change);
}

function removeEvent() {
    $date = explode('/', $_GET['date']);
    $type = $_GET['name'];
    unset($_SESSION['data']['events'][$date[2]][$date[0]][$date[1]][$type]);
    $_SESSION['shouldUpload'] = true;

    syslog(LOG_INFO, 'Removed ' . $type . ' on ' . $_GET['date']);
}

function removeEventType() {
    $etypetoremove = $_GET['eventtype'];
    if ($etypetoremove !== 'Tutoring') {
        unset($_SESSION['data']['eventtypes'][$etypetoremove]);
        //remove events of this event type
        foreach (array_keys($_SESSION['data']['events']) as $year) {
            foreach (array_keys($_SESSION['data']['events'][$year]) as $month) {
                foreach ($_SESSION['data']['events'][$year][$month] as $day => $todaysevents) {
                    foreach (array_keys($todaysevents) as $eventtype) {
                        if ($eventtype === $etypetoremove) {
                            unset($_SESSION['data']['events'][$year][$month][$day][$eventtype]);
                            syslog(LOG_INFO, 'Deleted ' . $eventtype . ' on ' . $month . '/' . $day . '/' . $year);
                        }
                    }
                }
            }
        }
    }
    $_SESSION['shouldUpload'] = true;
    syslog(LOG_INFO, 'Removed event type: ' . $eventtype);
}

function removeMember() {
    $oldName = $_GET['oldName'];
    if ($_SESSION['data']['names'][$oldName]['access'] === 'teacher' && !in_array($_SESSION['access'], ['teacher', 'coder']))
        return;
    unset($_SESSION['data']['names'][$oldName]);
    //search for attendences of oldName
    foreach ($_SESSION['data']['events'] as $year => $montharr) {
        foreach ($montharr as $month => $dayarr) {
            foreach ($dayarr as $day => $todaysevents) {
                foreach ($todaysevents as $eventtype => $eventdata) {
                    if (array_key_exists($oldName, $eventdata['attendees'])) {
                        unset($_SESSION['data']['events'][$year][$month][$day][$eventtype]['attendees'][$oldName]);
                    }
                }
            }
        }
    }
    syslog(LOG_INFO, 'Removed: ' . $oldName);
    $_SESSION['shouldUpload'] = true;
    ;
    syslog(LOG_INFO, 'Removed member: ' . $oldname);
    if ($_SESSION['name'] === $oldName) {
        upload();
        return 'logout';
    }
}

function setReqPoints() {
    if (!in_array($_SESSION['access'], ['teacher', 'coder']))
        return 'denied';
    $_SESSION['data']['reqpoints'] = $_GET['reqpoints'];
    $_SESSION['shouldUpload'] = true;
    syslog(LOG_INFO, 'Set required points to: ' . $_GET['reqpoints']);
    return $_GET['reqpoints'];
}

/*
 * $change should be the full data (call makeChanges($change) if you need to download)
 */

function upload($change = null) {
    //safeguard for uploading when developing
//    if (explode('.', $_SERVER['CURRENT_VERSION_ID'])[0] === 'dev') {
//        syslog(LOG_INFO, 'Upload avoided');
//        return;
//    }
    loadDrive();
    try {
        $file = getDataFile();
        deleteDataFile($file->getId());
        if ($change !== null)
            createDataFile(json_encode($change));
        else
            createDataFile(json_encode($_SESSION['data']));
        syslog(LOG_INFO, 'Uploaded Data');
    } catch (Exception $e) {
        syslog(LOG_ERR, "ERROR (upload): " . $e->getMessage());
    }
    $_SESSION['shouldUpload'] = false;
}

/*
 * 
function download() {
    loadDrive();
    $downloadeddata = json_decode(getContent(getDataFile()), true);
    $return = array(
        1 => $downloadeddata,
        2 => null
    );
    if (array_key_exists('merge', $_GET)) {
//        $changearr = array();
        //check for changes between currently viewable data and data from the last download
        $diff = arrayRecursiveDiff($_SESSION['data'], $_SESSION['olddata']);
        syslog(LOG_DEBUG, json_encode($diff));
        
        //if changes have been made, make them to downloadeddata
//        $index = 0;
//        foreach ($diff as $change) {
//            syslog(LOG_DEBUG, json_encode($change));
            syslog(LOG_DEBUG, json_encode(arrayRecursiveDiff($downloadeddata, array_merge_recursive($downloadeddata, $diff))));
            $downloadeddata = array_merge_recursive($downloadeddata, $diff);
//            syslog(LOG_DEBUG, $key);
//            $call = '$downloadeddata[' . $key . ']';
//            syslog(LOG_DEBUG, $call);
//            while (is_object($value)) {
//                syslog(LOG_DEBUG, 'is object:');
//                syslog(LOG_DEBUG, $value);
//                $nextkey = get_object_vars($value)[0];
//                syslog(LOG_DEBUG, $nextkey);
//                $call += '[' . $nextkey . ']';
//                $value = $value[$nextkey];
//            }
//            $call += ' = ' . $value + ';';
//            syslog(LOG_DEBUG, $call);
//            $changearr[$index] = $value;
//            $changearr[$index + 1] = $call;
//            $index += 2;
//            syslog(LOG_DEBUG, $call);
//            eval($call);
//            $downloadeddata[$key] = $value;
//            $changearr[$index] = $key;
//            $downloadeddata[$key]
//        }
//        $return = $changearr;
    }
    $_SESSION['data'] = $downloadeddata;
    $_SESSION['olddata'] = $downloadeddata;

//    if (!array_key_exists($_SESSION['name'], $_SESSION['data']['names'])) {
//        //the name you signed in as has changed due to the download, force logout
//        logout();
//    }
    syslog(LOG_INFO, 'Downloaded Data');
    $return[2] = $downloadeddata;
    return json_encode($return);
}

function recArrDiff($arr1, $arr2) {
    $return = array();
    
    
}

function arr2val($arrOfKeys, $arr) {
    $current = $arr;
    foreach($arrOfKeys as $key) {
        $current = $current[$key];
    }
}

function arrayRecursiveDiff($aArray1, $aArray2) {
    $aReturn = array();

    foreach ($aArray1 as $mKey => $mValue) {
        if (array_key_exists($mKey, $aArray2)) {
            if (is_array($mValue)) {
                $aRecursiveDiff = arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                if (count($aRecursiveDiff)) {
                    $aReturn[$mKey] = $aRecursiveDiff;
                }
            } else {
                if ($mValue != $aArray2[$mKey]) {
                    syslog(LOG_DEBUG, $mValue);
                    $aReturn[$mKey] = $mValue;
                }
            }
        } else {
            $aReturn[$mKey] = $mValue;
        }
    }
    return $aReturn;
}
 */