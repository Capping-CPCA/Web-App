<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays a participant's attendance record.
 *
 * This page displays details about the classes a
 * participant has taken and allows the user add a new
 * record or edit and delete an existing one.
 *
 * @author Michelle Crawley
 * @copyright 2017 Marist College
 * @version 0.7.1
 * @since 0.1
 */
include ('../models/Notification.php');
include($_SERVER['DOCUMENT_ROOT'].'/../views/attendance/attendance_utilities.php');
global  $db, $view, $route, $params;

// Select page to display
if (!empty($params) && $params[0] == 'edit') {
    $view->display('agency-requests/report_card_edit_entry.php');
} else if (!empty($params) && $params[0] == 'delete') {
    $view->display('agency-requests/report_card_delete_entry.php');
} else {
    include('header.php');

    // Get people id from params
    $peopleid = rawurldecode(implode('/', $params));
    $result = $db->query("SELECT * FROM people WHERE peopleid = $1", [$peopleid]);
    $person = pg_fetch_assoc($result);
    extract($person);

    // Get participant's attendance record
    $result = $db->query("SELECT * FROM participantclassattendance, participants WHERE participants.participantid = $1 AND participants.participantid = participantclassattendance.participantid ORDER BY date ASC;", [$peopleid]);
    ?>
    <div class="w-100" style="height: fit-content;">
        <?php
        // Displays a notification if modification was successful
        if (isset($_SESSION['notification'])) {
            $note = $_SESSION['notification'];
            $notification = new Notification($note['title'], $note['msg'], $note['type']);
            $notification->display();
            unset($_SESSION['notification']);
        }
        ?>
        <button class="cpca btn" onclick="window.location.href='/ps-view-participant/<?=$peopleid?>'"><i class="fa fa-arrow-left"></i> Back</button>
        <div class="card" style="max-width: 700px; width: 100%; margin: 0 auto;">
            <div class="card-header">
                <h4 id="report-card-heading"><?= ucwords($firstname . " " . ($middleinit ?: "") . " " . $lastname) . "'s Report Card" ?>
                    <div id="add-report-card-entry" onclick="document.location.href='/new-class/<?= $peopleid ?>'">
                        <i class="fa fa-plus"></i>
                    </div>
                </h4>
            </div>
            <?php if (pg_num_rows($result) > 0) {
                // Keep track if number of attendance entries
                $recordNumber = 1;
                while ($attendance = pg_fetch_assoc($result)) {
                    extract($attendance);

                    // Get class offering details
                    $res = $db->query("SELECT * FROM classoffering WHERE date = $1 AND siteName = $2 ", [$date, $sitename]);
                    extract(pg_fetch_assoc($res));

                    // Get class name
                    $res = $db->query("SELECT topicname FROM classes WHERE classid = $1", [$classid]);
                    $className = pg_fetch_assoc($res);

                    // Get curriculum name
                    $res = $db->query("SELECT curriculumname FROM curricula WHERE curriculumid = $1", [$curriculumid]);
                    $curriculumName = pg_fetch_assoc($res);

                    // Store important details in a json array to be passed to the edit/delete pages
                    $entryInfo = array($attendance, $className['topicname'], $curriculumName['curriculumname']);
                    $entryInfo = json_encode($entryInfo);
                    ?>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item report-card-item" data-toggle="modal"
                            data-target="#modal-<?= $recordNumber ?>">
                            <span style="float: left; margin: 15px 15px 5px 0px;"
                                  class="badge badge-cpca"><?= $recordNumber ?></span>
                            <b class="class-details"><?= $curriculumName['curriculumname'] . ": " . $className['topicname'] ?></b>
                            <br>
                            <?= preg_replace('/(?<!\ )[A-Z]/', ' $0', $sitename) ?><span
                                    style="float: right;"><i><?= formatSQLDateShort($date) ?></i></span>
                        </li>
                    </ul>
                    <div class="modal fade" id="modal-<?= $recordNumber ?>">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="padding: 10px 25px 5px 25px;">
                                    <h5 class="modal-title">More Details</h5>
                                    <div class="float-right" style="padding-top: 15px; display: inline-flex;">
                                        <form method="post" action="/report-card/edit/<?=$peopleid?>">
                                            <button type="submit" class="btn btn-outline-secondary btn-sm edit" style="margin: 0 5px 0 0;">Edit</button>
                                            <input type="hidden" name="entry-info" value='<?=$entryInfo?>'/>
                                        </form>
                                        <form method="post" action="/report-card/delete/<?=$peopleid?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" >Delete</button>
                                            <input type="hidden" name="entry-info" value='<?=$entryInfo?>'/>
                                        </form>
                                    </div>
                                </div>
                                <div class="modal-body">
                                    <ul class="list-group list-group-flush more-details-list">
                                        <li class="list-group-item more-details"><b>Site
                                                Name: </b><?= preg_replace('/(?<!\ )[A-Z]/', ' $0', $sitename); ?></li>
                                        <li class="list-group-item more-details">
                                            <b>Curriculum: </b><?= preg_replace('/(?<!\ )[A-Z]/', ' $0', $curriculumName['curriculumname']); ?>
                                        </li>
                                        <li class="list-group-item more-details">
                                            <b>Class: </b><?= preg_replace('/(?<!\ )[A-Z]/', ' $0', $className['topicname']); ?>
                                        </li>
                                        <li class="list-group-item more-details">
                                            <b>Date: </b><?= formatSQLDate($date) ?></li>
                                        <li class="list-group-item more-details"><b>Number of
                                                Children: </b><?= $numchildren ?></li>
                                        <li class="list-group-item more-details"><b>New
                                                Participant: </b><?= $isnew == "f" ? "No" : "Yes" ?></li>
                                        <li class="list-group-item more-details"><b>Zip Code: </b><?= $zipcode ?></li>
                                        <li class="list-group-item more-details"><b>Comments: </b><?= $comments ?></li>
                                    </ul>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $recordNumber++;
                }
            } else { ?>
                <div class="w-100 d-flex flex-column justify-content-center text-center no-report-card">
                    <h3 class="display-3 text-secondary" style="font-size: 20px;"><i
                                class="fa fa-exclamation-circle"></i></h3>
                    <h3 class="display-3 text-secondary" style="font-size: 20px;">User has not attended any
                        classes.</h3>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php
    include('footer.php');
} ?>