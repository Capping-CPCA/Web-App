<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Views details about a given participant.
 *
 * This display shows participant information in subsections
 * to allow for easy reading. Administrators can edit this
 * information if necessary.
 *
 * @author Vallie Joseph
 * @copyright 2017 Marist College
 * @version 0.7.1
 * @since 0.1
 */
include('header.php');
global $db, $params;
$peopleid = $params[0];

// All info related to class history
$resultNotes = $db->query("SELECT participants.participantid, participants.dateofbirth, 
                            participants.race, people.firstname, people.lastname, people.middleinit, 
                            classes.topicname, classoffering.date, curricula.curriculumname
                            FROM participants 
                            INNER JOIN people ON participants.participantid = people.peopleid  
                            LEFT JOIN participantclassattendance ON people.peopleid  = participantclassattendance.participantid 
                            LEFT JOIN classoffering ON participantclassattendance.date = classoffering.date
                            LEFT JOIN classes ON classoffering.classid = classes.classid
                            LEFT JOIN curricula ON classoffering.curriculumid =  curricula.curriculumid
                            WHERE participants.participantid = $1 ORDER BY date DESC ", [$peopleid]);
$notes = pg_fetch_all($resultNotes);

// All info related to family
$familyInfo = $db->query("SELECT firstname AS childFirst , lastname AS childLast , middleinit AS childM,
                            relationship, dateofbirth,
                            sex
                            FROM people
                            INNER JOIN familymembers ON people.peopleid = familymembers.familymemberid
                            INNER JOIN family ON familymembers.familymemberid = family.familymembersid
                            INNER JOIN forms ON forms.formid = family.formid
                            WHERE participantid = $1", [$peopleid]);


// Grab agency information
$db->prepare("get-participant-agency-info","SELECT participants.*, agency, phone, email, 
                            ismaincontact,
                            hasagencyconsentform, hasmentalhealth,hasspecialneeds, hasmentalhealth,
                            people.*
                            FROM participants
                            INNER JOIN people on participants.participantid = people.peopleid
                            INNER JOIN forms ON participants.participantid = forms.participantid
                            LEFT JOIN agencyreferral ON forms.formid = agencyreferral.agencyreferralid
                            LEFT JOIN contactagencyassociatedwithreferred ON agencyreferral.agencyreferralid = contactagencyassociatedwithreferred.agencyreferralid
                            LEFT JOIN contactagencymembers ON contactagencyassociatedwithreferred.contactagencyid = contactagencymembers.contactagencyid
							 WHERE participants.participantid = $1");
$result = $db->execute("get-participant-agency-info",[$peopleid]);
$agencyResult = pg_fetch_assoc($result);
extract($agencyResult);

// Grab address information
$db->prepare("get-participant-addresses", "	SELECT * 
											FROM participants
											INNER JOIN people ON participants.participantid = people.peopleid
											LEFT JOIN forms as fo ON participants.participantid = fo.participantid
											LEFT JOIN addresses ON  fo.addressid = addresses.addressid
											LEFT JOIN zipcodes ON zipcodes.zipcode =addresses.zipcode
											WHERE participants.participantid =  $1 ORDER BY employeesigneddate DESC LIMIT 1");
$result = $db->execute("get-participant-addresses",[$peopleid]);
$address = pg_fetch_assoc($result);
extract($address);

// Empty var for holding the edit/delete options; Based on role 
$buttonOptions = null;

/**
 * Checks the phone numbers a participant is associated with
 * sets active status based on last class attended
 * shows last date/time of attendence  
 * @param $phoneType string of the phonetype
 * @return string of phone number
**/
function checkPhone($phoneType, $db, $peopleid){
	$phoneInfo = $db->query("SELECT * from people INNER JOIN participants ON people.peopleid = participants.participantid
	INNER JOIN forms ON participants.participantid = forms.participantid
	INNER JOIN formphonenumbers ON forms.formid = formphonenumbers.formid WHERE peopleid = $1 AND phonetype = $2 ", [$peopleid, $phoneType]);
	
	$result = pg_fetch_assoc($phoneInfo);
	
	if($result['phonenumber']== ""){
		echo "<i>No number</i>";
	}else{
        // Check to see if phone string is already pretty printed, if it is, display normally
        if (strpos($result['phonenumber'], '(') !== false) {
            echo $result['phonenumber'];
        }else{
            echo prettyPrintPhone($result['phonenumber']);
        }
		
	}
}
// Only displays the edit button if the user is an admin
if(hasRole(Role::Admin)){
	$buttonOptions = "<a href='/ps-edit-participant/".$agencyResult['participantid']."'><button class='btn btn-outline-secondary ml-2 float-right'>Edit</button></a>";
}

?>

<div class="d-flex flex-column w-100" style="height: fit-content;">
<?php
/**
 * Checks the status of a participant
 * sets active status based on last class attended
 * shows last date/time of attendence  
 * @param $timestamp last time participant was seen
 * @param $activePeriod range for which participant can be absent before 
 * being considered inactive
 * @return array the set of status properties that
 *   will be assigned to the status span
**/
function status($timestamp, $activePeriod){
	$currentDate =new DateTime("now", new DateTimeZone("America/New_York"));
	$passedTime =new DateTime($timestamp, new DateTimeZone("America/New_York"));
	$timeDifference =(int) $passedTime->diff($currentDate)->format('%a');
	
	if($timeDifference <= $activePeriod){
		$readableDate= $passedTime->format('m/d/Y g:i A');
		$statuses = array(
						"status" => "active </span> <i>last seen  : ".toString($readableDate)[0]."</i>",
						"class" => "badge badge-success",
						);
	}else if($timeDifference >= $activePeriod){
		$readableDate= $passedTime->format(' l jS F Y \a\t g:ia ');
		$statuses = array(
						"status" => "inactive </span> <i>last seen  : ".toString($readableDate)[0]."</i>",
						"class" => "badge badge-secondary",
						);
	}
	if($timestamp == ""){
				$statuses = array(
				"status" => "inactive</span>  <i> No classes taken yet</i>",
				"class" => "badge badge-primary",
				);
	}
	return $statuses;
}

$statuses = status($notes[0]['date'],40);

/**
  * Simple function that checks to see if the value pulled is empty 
  * if it is, return a 'No Record Found' message
  * @params $value assoc aray index of db query
  * returns either text alerting user to lack of info in db or value of db query
**/
function checkSet($value){
	if($value == ""){
		return "No Record Found";
	}else{
		return $value;
	}
}
?>
    <div class="card" style="max-width: 700px; width: 100%; margin: 0 auto;">
        <div class="card-header">
            <h4 class="modal-title"><?= ucwords($agencyResult['firstname']." ".$agencyResult['middleinit']." ".$agencyResult['lastname'])." ".$buttonOptions?></h4>
        </div>
        <div class="card-body">
            <div class="w-100 text-center">
                <img class="icon-img" src="/img/default_av.jpg">
            </div>
            <h4 class="thin-title">Information</h4>
            <hr>
            <div class="pl-3">
				<div class="participant_detailed row pb-2">
				<div class="col-sm-4">
                <p class="participant_name"><b>Name: </b> <?= ucwords($agencyResult['firstname']." ".$agencyResult['middleinit']." ".$agencyResult['lastname']);?></p>
				</div>
				<div class="col-sm-8">
                <p class="participant_status"><b>Status: </b> <span class="<?=$statuses['class']?>"><?=$statuses['status']?></p>
				</div>
				</div>
				<div class="participant_detailed row pb-2">
					<div class="col-sm-4">
						<b>Sex: </b><div><?=$sex=($agencyResult['sex']=="" ? "<i>No records found</i>":$agencyResult['sex'])?></div>
					</div>
					<div class="col-sm-4">
						<b>Race: </b><div><?=$race=($agencyResult['race']=="" ? "<i>No records found</i>":$agencyResult['race'])?></div>
					</div>
					<div class="col-sm-4">
						<b>DOB: </b><div><?=$dob =($agencyResult['dateofbirth']=="" ? "<i>No records found</i>":$agencyResult['dateofbirth'])?></div>
					</div>
				</div>
				
				<div class="participant_detailed2 row pt-2">
				<?php
                
               
				?>
					<div class="col-sm-4">
					<b>Agency Name: </b>
						<div> 
							<?=$agency =($agencyResult['agency']=="" ? "<i>No records found</i>" : $agencyResult['agency'])?>
						</div>
					</div>
					
					<div class="col-sm-4">
					<b>Agency Contact: </b> 
					<div> 
						<div>Phone: 
						<?=$phone = ($agencyResult['phone']=="" ? "<i>No records found</i>" :prettyPrintPhone($agencyResult['phone']))?></div>
						<div>Email: 
						<?=$email = ($agencyResult['email']=="" ? "<i>No records found</i>" :$agencyResult['email'])?></div>
					</div>
					</div>
					
					<div class="col-sm-4">
					<b>Consent to Agency: </b> 
						<div>
							<?=$consent = ($agencyResult['hasagencyconsentform'] =="f" || $agencyResult['hasagencyconsentform'] =="" ? "No" :" Yes")?>
						</div>
					</div>
				</div>
				<br>
                <p class="participant_contact"><b>Contact: </b></p>
                <div class="d-flex justify-content-center">
                    <div class="display-stack">
                        <div class="display-top">
								<?php
								checkPhone('Primary', $db, $peopleid);
								?>	
						</div>
                        <div class="display-split"></div>
                        <div class="display-bottom">Primary Phone</div>
                    </div>
                    <div class="display-stack">
                        <div class="display-top">	
								<?php
								checkPhone('Secondary', $db, $peopleid);
								?>
					</div>
                        <div class="display-split"></div>
                        <div class="display-bottom">Secondary Phone</div>
                    </div>
                </div>
            </div>
			<div class="participant_address row ">
				<div class="col-sm-12">
            
            <br>
			<h4 class="thin-title">Address Information</h4>
            <hr>
			<div class="addressholder pl-5">
            <div class="row">
            <div class="addressline1">
				<?= $aptinfo ." ". checkSet($addressnumber)." ".checkSet($street)?>
				</div>
            </div>
            <div class="row">
            <div class="addressline2">
					<?= $address['city']." ".$address['state']." ".$address['zipcode'];?>
				</div>
            </div>
			</div>
					
				</div>
			</div>
            <br>
			<h4 class="thin-title">Class Information</h4>
            <hr>
            <table class="table table-striped">
                <tr>
					<th>Class</th>
					<th>Date of Attended</th>
					<th>Curriculum</th>
				</tr>
				
				<?php 
				// If query returns nothing, throw message to the user
				if(pg_num_rows($resultNotes) == 0){
					echo "<i> No Classes Found </i>";
				}else{
					foreach($notes as $resultNotes){
						echo 	"<tr>
									<td>".$resultNotes['topicname']."</td>
									<td>".$resultNotes['date']."</td>
									<td>".$resultNotes['curriculumname']."</td>
								</tr>";								
					}
				}
				?>
				
            </table>
            <h4 class="thin-title">Family Info</h4>
            <hr>
				<table class="table table-striped">
					<tr>
						<th>Name</th>
						<th>Relationship</th>
						<th>DOB</th>
						<th>Sex</th>
					</tr>			
					<?php
                    // Display all family information
                    $familyRow = pg_fetch_all($familyInfo);
                    if(pg_num_rows($familyInfo) > 0){
                    foreach($familyRow as $row){

                        echo    "<tr>".
                                    "<td>".ucwords(checkSet($row['childlast']).", ".checkSet($row['childfirst'])." ".$row['childm'])."</td>".
                                    "<td>".checkSet($row['relationship'])."</td>".
                                    "<td>".checkSet($row['dateofbirth'])."</td>".
                                    "<td>".checkSet($row['sex'])."</td>".
                                "</tr>";
                    }

                    }else{
                    echo "<i> No Family Information Found </i>";
                    }
					?>
            </table>
        </div>
        <div class="card-footer text-center">
            <a href="/forms-view/<?= $params[0] ?>">
                <button class="btn btn-outline-secondary">View Forms</button>
            </a>
            <a href="/report-card/<?= $agencyResult['participantid'] ?>">
                <button class="btn btn-outline-secondary">View Attendance</button>
            </a>
        </div>
    </div>
</div>

<?php
include('footer.php'); ?>