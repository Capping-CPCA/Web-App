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
 * @author Elijah Johnson, Vallie Joseph
 * @copyright 2017 Marist College
 * @version 0.7.1
 * @since 0.1
 */
include('header.php');
global $db, $params;
$peopleid = $params[0];

if($_SERVER['REQUEST_METHOD'] == 'POST'){


	$topic = real_escape_string($_POST['notetopic']);
	$desc = real_escape_string($_POST['content']);
	$empid = $_SESSION['empolyeeid'];
	$partid = $notesResult['participantid'];
	
	$insert = $db->query("INSERT INTO notes VALUES ($1, $2, $3, $4)", [$topic, $desc, $partid, $empid]);
 
	if(!$insert){
		die("Oh No!");
	} else {echo"Success"; }
	$db->close();
	
}

$noteQuery = $db->query("SELECT participants.participantid, participants.dateofbirth, participants.race, people.firstname, 
							people.lastname, people.middleinit , participants.sex 
							FROM participants 
							INNER JOIN people ON participants.participantid = people.peopleid
							WHERE people.peopleid = $1",  [$peopleid]);

$notesResult = pg_fetch_assoc($noteQuery); 

$agencyInfo = $db->query("	SELECT participants.participantid, people.middleinit, people.firstname, people.lastname, 
							 participants.dateofbirth, participants.sex, participants.race, 
							 contactagencymembers.agency, contactagencymembers.phone, contactagencymembers.email, 
							 agencyreferral.hasagencyconsentform 
							 FROM participants 
							 INNER JOIN people ON participants.participantid = people.peopleid 
							 LEFT JOIN forms ON participants.participantid = forms.formid 
							 LEFT JOIN agencyreferral ON forms.formid = agencyreferral.agencyreferralid 
							 LEFT JOIN contactagencyassociatedwithreferred ON agencyreferral.agencyreferralid = contactagencyassociatedwithreferred.agencyreferralid 
							 LEFT JOIN contactagencymembers ON contactagencyassociatedwithreferred.contactagencyid = contactagencymembers.contactagencyid  
							 WHERE participants.participantid = $1", [$peopleid]);
$agencyResult = pg_fetch_assoc($agencyInfo);

//All info related to family
$familyInfo = $db->query("	SELECT formid, participantid, people.firstname, people.lastname, people.middleinit, familymembers.familymemberid, familymembers.relationship, familymembers.dateofbirth, familymembers.race, familymembers.sex
							FROM participants, familyinfo
							INNER JOIN people ON participants.participantid = people.peopleid
							LEFT JOIN forms ON participants.participantid = forms.participantid
							LEFT JOIN family ON forms.formid = family.formid
							LEFT JOIN familymembers on familyinfo.familymemberid = familymembers.familymemberid
							left JOIN people on familymembers.familymemberid = people.peopleid WHERE participantid = $1", [$peopleid]);

$familyResult = pg_fetch_assoc($familyInfo);


?>

<div class="d-flex flex-column w-100" style="height: fit-content;">

<?php

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
            <h4 class="modal-title"><?= $notesResult['firstname']." ".$notesResult['middleinit']." ".$notesResult['lastname']?></h4>
        </div>
        <div class="card-body">
            <div class="w-100 text-center">
                <img class="icon-img" src="/img/default_av.jpg">
            </div>
            <h4 class="thin-title">Save Note</h4>
            <hr>
            <div class="pl-3">
				<div class="participant_detailed row pb-2">
					<form method="POST" action="notes.php">
		            	<p>
		            		<label>Topic:</label>
		            		<input type="text" name="notetopic">
		            	</p>
		            	<p>
		            			<label>Description:</label>
			    				<input type="text" name="content"></input>
			    		</p>
		            	<input type="submit" value="Submit Form">
		            </form>
				</div>
				
				<br>
                
            </div>
			
            <br>
            <h4 class="thin-title">Saved Notes</h4>
            <hr>
				<table class="table table-striped">
					<tr>
						<th>Topic</th>
						<th>Note</th>
						<th>Date</th>
						<th>Author</th>
					</tr>			
					<?php
					#if query returns nothing, throw an error to the user
					if(pg_num_rows($familyInfo) == 0){
						echo "no family found";
					}else{
						while ($rowF = pg_fetch_assoc($familyInfo)) {
							echo 	"<tr>".
									"<td>".checkSet($rowF['lastname'])." ".$rowF['middleinit']." ".checkSet($rowF['lastname'])."</td>".
									"<td>".checkSet($rowF['relationship'])."</td>".
									"<td>".checkSet($rowF['dateofbirth'])."</td>".
									"<td>".checkSet($rowF['sex'])."</td>".
									"</tr>";

						}
					}

					?>
            </table>
            
        </div>
               <div class="card-footer text-center">
		            <a href="/ps-view-participant/<?= $notesResult['participantid'] ?>">
						<button class="p-view btn cpca">View Record</button>
					</a>
		      
        		</div>
            </div>
            <br>
            				
        </div>
        
    </div>
</div>

<?php
include('footer.php');
?>
