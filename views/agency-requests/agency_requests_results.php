<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays the results from a participant search
 *
 * After a user enters a search or goes to the related URL,
 * the route params are parsed and converted into a search.
 * The results from the database are then displayed as
 * separate cards.
 *
 * @author Vallie Joseph
 * @copyright 2017 Marist College
 * @version 1.0
 * @since 0.1
 */

global $db, $params;
#Search for user inputed name, check both first/last combination and last/first name combinations

$searchquery = strtolower(rawurldecode(implode('/',$params)));
$result = $db->query("SELECT participants.participantid, participants.dateofbirth, participants.race, people.firstname, 
							people.lastname, people.middleinit , participants.sex
							FROM participants 
							INNER JOIN people ON participants.participantid = people.peopleid
							WHERE LOWER(CONCAT(people.firstname, ' ' , people.middleinit , ' ' , people.lastname)) LIKE $1
							OR LOWER(CONCAT(people.lastname, ' ' , people.firstname, ' ',people.middleinit )) LIKE $1
							OR LOWER(CONCAT(people.firstname, ' ' , people.lastname)) LIKE $1
							OR LOWER(CONCAT(people.lastname, ' ' , people.firstname)) LIKE  $1 ",  ["%$searchquery%"]);
/**
 * Checks to see if the participants' details exist in the query
 * @params $rowValue the string returned from the db query
 * return either the result of the query or a 'no records found' string
 */
function checkEmpty($rowValue){
	if($rowValue == ""){
		return  "<i> No Records Found</i>";
	}else{
		return $rowValue;
	}

}
include('header.php');
?>
<div class="w-100 d-flex flex-column">
    <div class="mb-2">
        <button class="cpca btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
    </div>
    <ul class="list-group" style="max-width: 700px; width: 100%; margin: 0 auto">
        <?php
        #if query returns nothing, throw an error to the user
        if(pg_num_rows($result) == 0){
        ?>
			<div class="w-100 d-flex flex-column justify-content-center text-center">
				<h3 class="display-3 text-secondary" style="font-size: 40px;">
					<i class="fa fa-exclamation-circle"></i>
				</h3>
				<h3 class="display-3 text-secondary" style="font-size: 40px;">No Participants Found.</h3>
			</div>
        <?php
        }//end if
		
        #check the result of query and loop through each row to display search results
        while ($row = pg_fetch_assoc($result)) {
           
		?>
			<li class="list-group-item">
				<button class="btn btn-outline-secondary advanced-info"><i class="fa fa-caret-right" aria-hidden="true"></i></button>

				<span><?= ucwords($row['lastname'].", ".$row['firstname']. " ". $row['middleinit']);?></span>
				
				<a class="float-right" href="/ps-view-participant/<?= $row['participantid'] ?>">
					<button class="p-view btn cpca">View Record</button>
				</a>

				<ul class="list-group sublist">
						<?php
                        $formInfo = $db->query("SELECT  
                                                formid, selfreferralid, intakeinformationid, agencyreferralid,
                                                tentativestartdate,
                                                participants.participantid, participants.sex, participants.dateofbirth
                                                FROM forms
                                                INNER JOIN participants ON forms.participantid = participants.participantid
                                                LEFT JOIN selfreferral ON forms.formid = selfreferral.selfreferralid
                                                LEFT JOIN intakeinformation ON forms.formid = intakeinformation.intakeinformationid
                                                LEFT JOIN agencyreferral ON forms.formid = agencyreferral.agencyreferralid
                                                WHERE participants.participantid= $1",[$row['participantid']]);
                        while($formResults  = pg_fetch_assoc($formInfo)){
                            ?>   
                            <div>
                                <b>DOB: </b><?=checkEmpty($formResults['dateofbirth']);?> |  
                                <b>Program Start Date: </b> <?=checkEmpty($formResults['tentativestartdate']);?> | 
                                <b>Sex: </b> <?=checkEmpty($formResults['sex']); ?>
                            </div>
                            <?php
                        }
                        ?>
					</li>
				</ul>
			</li>
		<?php
        }
        ?>
    </ul>
</div>
<script>
    $(function() {
        showTutorial('participantResult');
    });
</script>
<?php
include('footer.php');
?>