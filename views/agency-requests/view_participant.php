<?php
global $db, $params;
$peopleid = $params[0];

$result = $db->query("SELECT participants.participantid, participants.dateofbirth, participants.race, people.firstname, people.lastname, people.middleinit " .
					"FROM participants " .
					"INNER JOIN people ON participants.participantid = people.peopleid WHERE people.peopleid=$1", [$peopleid]);

$participant = pg_fetch_assoc($result);

include('header.php');
?>
<div class="d-flex flex-column w-100" style="height: fit-content;">
    <a href="/back"><button class="cpca btn"><i class="fa fa-arrow-left"></i> Back</button></a>
    <div class="card" style="max-width: 700px; width: 100%; margin: 0 auto;">
        <div class="card-header">
            <h4 class="modal-title"><?= $participant['firstname']." ".$participant['middleinit']." ".$participant['lastname'] ?></h4>
        </div>
        <div class="card-body">
            <div class="w-100 text-center">
                <img class="icon-img" src="/img/default_av.jpg">
            </div>
            <h4 class="thin-title">Information</h4>
            <hr>
            <div class="pl-3">
                <p class="participant_name"><b>Name: </b> <?= $participant['firstname']." ".$participant['middleinit']." ".$participant['lastname'] ?></p>
                <p class="participant_status"><b>Status: </b> <span class="badge badge-success">active</span> </p>
                <p class="participant_notes"><b>Notes: </b> <p class="pl-3">"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."</p></p>
                <p class="participant_other"><b>Other: </b> Other items to note</p>
                <p class="participant_contact"><b>Contact: </b></p>
                <div class="d-flex justify-content-center">
                    <div class="display-stack">
                        <div class="display-top">(123) 456-7890</div>
                        <div class="display-split"></div>
                        <div class="display-bottom">Home Phone</div>
                    </div>
                    <div class="display-stack">
                        <div class="display-top">(800) 867-5309</div>
                        <div class="display-split"></div>
                        <div class="display-bottom">Cell Phone</div>
                    </div>
                </div>
            </div>
            <br>
            <h4 class="thin-title">Family Info</h4>
            <hr>

            <!-- <button type="button" class="btn cpca">Download as PDF</button>-->
            <table class="table table-striped">
                <tr><th>Col 1</th><th>Col 2</th><th>Col 3</th></tr>
                <tr><td>Data</td><td>Data</td><td>Data</td></tr>
                <tr><td>Data</td><td>Data</td><td>Data</td></tr>
                <tr><td>Data</td><td>Data</td><td>Data</td></tr>
                <tr><td>Data</td><td>Data</td><td>Data</td></tr>
                <tr><td>Data</td><td>Data</td><td>Data</td></tr>
                <tr><td>Data</td><td>Data</td><td>Data</td></tr>
            </table>
        </div>
        <div class="card-footer text-center">
            <a href="/intake-packet/<?= $participant['participantid'] ?>">
                <button class="btn btn-outline-secondary">View Intake Packet </button>
            </a>
            <a href="#">
                <button class="btn btn-outline-secondary">View Attendence Record</button>
            </a>
            <a href="#">
                <button class="btn btn-outline-secondary">View Current Assigned Curriculum</button>
            </a>
        </div>
    </div>
</div>

<?php
include('footer.php'); ?>