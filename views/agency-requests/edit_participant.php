<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Allows user to edit participant information
 *
 * After a user selects a participant to view, they can 
 * edit either the participant's information.
 *
 * @author Vallie Joseph
 * @copyright 2017 Marist College
 * @version 0.7.1
 * @since 0.6.2
 */

include('header.php');
global $db, $params ,$formid;
?>

<div class="d-flex flex-column w-100" style="height: fit-content;">
<?php
// Get people id from params
$peopleid = rawurldecode(implode('/', $params));

//grab participant info
$db->prepare("get-participant-form", "SELECT * 
				FROM people 
				INNER JOIN participants ON people.peopleid = participants.participantid 
				LEFT JOIN forms ON participants.participantid = forms.participantid 
				WHERE participants.participantid = $1 ");
$result = $db->execute("get-participant-form", [$peopleid]);
$participant = pg_fetch_assoc($result);

extract($participant);

//grab address information
$db->prepare("get-participant-addresses", "SELECT * 
				FROM participants
				INNER JOIN people ON participants.participantid = people.peopleid
				LEFT JOIN forms as fo ON participants.participantid = fo.participantid
				LEFT JOIN addresses ON  fo.addressid = addresses.addressid
				LEFT JOIN zipcodes ON zipcodes.zipcode =addresses.zipcode
				WHERE participants.participantid = $1");
$result = $db->execute("get-participant-addresses",[$peopleid]);
$address = pg_fetch_assoc($result);

extract($address);

/**
 * Checks the value parameter 
 * If it's blank, return a placeholder that tells the user there is nothing on file
 * If not, show the value
**/
function checkValue($rowValue){
	if($rowValue ==""){
		echo " placeholder = 'No info on file' ";
	}else{
		echo "value = '$rowValue' ";
	}
}

/**
 * Looks for a phone number belonging to the participant
 * if there is a number in the db, return it.
 * if not, say so in the placeholder
 * @params $phoneTypes string of the type of phone (ie. Primary, Secondary, Day, Evening)
 * @returns array of the phone number and the placeholder value
**/
function getPhoneNumbers($phoneTypes){
	global $db, $params;
	$peopleid = rawurldecode(implode('/', $params));
	$verifyPhone = ucwords(strtolower($phoneTypes));
	$phoneInfo = $db->query("SELECT * 
							 FROM people 
							 INNER JOIN participants ON people.peopleid = participants.participantid
							 INNER JOIN forms ON participants.participantid = forms.participantid
							 INNER JOIN formphonenumbers ON forms.formid = formphonenumbers.formid 
							 WHERE peopleid = $1 AND phonetype = $2 ", [$peopleid, $verifyPhone]);
 	$phoneResults = pg_fetch_assoc($phoneInfo);

	$phoneCheck = "";
	$actualResults= null;
	$readOnlyFlag = false;
	$readOnly= "";
		if(empty($phoneResults) || $phoneResults["phonenumber"]==""){
			$phoneCheck = 0;
			$phoneResults = 0;
			$readOnlyFlag = true;
		}else{
			$phoneCheck = 1;
			$actualResults = $phoneResults["phonenumber"];
		}
		if($readOnlyFlag){
			$readOnly = " placeholder='No number on file' ";
		}else{
			$readOnly = " value='$actualResults' ";
		}
	return array("phoneNumnber" => $actualResults, "readOnly" => $readOnly,"whatwegot"=>$phoneResults);
}


 if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$firstname = $_POST['fname-update'];
	$lastname = $_POST['lname-update'];
	$middleinit = $_POST['mname-update'];
	
	//makes sure the middle name is only 1 character
	if(strlen($middleinit) <= 1 ){
		$res = $db->query("UPDATE people SET firstname = $1, lastname = $2, middleinit =$3 ".
                "WHERE peopleid = $4", [$firstname, $lastname, $middleinit, $peopleid]);
			if(!$res){
			?>
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<strong>Participant Update Success</strong> Participant name information has been updated.
				</div>

			<?php
			}
		
	}else{
		?>
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
				<strong>Error: </strong> Participant name information update has failed. Only 1 character allowed for middle name initial
			</div>
			  <?php
	}
	
	/**
	 * Checks to see if the updated phone number already exists or 
	 * not. If it exists, create an update statement; 
	 * if not, create an insert statement
	 * @params $db database object from globals
	 * @params $formid id of the form associated with the participant 
	**/
	function updateInsert( $db, $formid){
	global $db, $params;
	$peopleid = rawurldecode(implode('/', $params));
		$pphone = $_POST['pphone-update'];
		$sphone = $_POST['sphone-update'];
		$dphone = $_POST['dphone-update'];
		$ephone = $_POST['ephone-update'];
		$error = 0;
		$errorArray = array();
		$phoneNumbers = ["Primary"=>$pphone, "Secondary"=>$sphone, "Day"=>$dphone, "Evening"=>$ephone];
		$resArray = array();
		foreach($phoneNumbers as $key => $values){
			$res2 = $db->query("UPDATE formphonenumbers SET phonenumber = $1 WHERE phonetype = $2 AND formid = $3 ", [$values, "$key", $formid]);
			if($res2){
				$res3 = $db->query("INSERT INTO formphonenumbers (formid, phonenumber, phonetype) VALUES($1, $2, $3)", [$formid, $values, "$key"]);
				if(!$res3){
					$error++;
				}
			}
		}
		
		
		if($error == 0){
		?>
			
			<div class="alert alert-success alert-dismissible fade show" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
				<strong>Success: </strong> Participant information has updated 
			</div>
			<div class="">
				<span class="cpca btn"> 
				<i class="fa fa-arrow-left"></i><a style="text-decoration:none; color:white;"href="/ps-view-participant/<?=$peopleid?>">
				 Back to View Participant</a>
				</span>
			</div>
			
			  <?php
		}
	}
	if(!empty($_POST['form'])){
		updateInsert($db, $formid);
	}
	
	//Currently waiting on stored procedure
	/*$addressid = $_POST['addressid'];
	$streetnumber = $_POST['house-update'];
	$streetaddress = $_POST['street-update'];
	$aptinfo = $_POST['apt-update'];
	$city = $_POST['city-update'];
	$state = $_POST['street-update'];
	$zip = $_POST['zip-update'];
	
	$addressupdate = $db->query("UPDATE addresses SET addressnumber = $2, aptinfo = $3, street = $4, zipcode =$5 WHERE addressid = $1",
								[$addressid, $streetnumber, $aptinfo, $streetaddress, $zip]);*/
}

?>

<div class="d-flex justify-content-center">
<form class="jumbotron form-wrapper mb-3" method="POST" action= "" >
  <div class="form-group">
	<h4>Information</h4>
	
<div class="form-group row">
                    <div class="col-sm-4">
                        <label for="class-name" class=""><b>First Name</b></label>
                        <input type="text" class="form-control" value="<?= ucwords($participant['firstname'])?>" id="fname-update" name="fname-update" required="">
                        <div class="invalid-feedback">
                            First Name cannot be empty.
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <label for="class-name" class=""><b>Middle Initial</b></label>
                        <input type="text" class="form-control" placeholder="Middle initial" value="<?= $participant['middleinit']?>"  maxlength="1" id="mname-update" name="mname-update">
                    </div>
                    <div class="col-sm-4">
                        <label for="class-name" class=""><b>Last Name</b></label>
                        <input type="text" class="form-control" value="<?= $participant['lastname']?>" id="lname-update" name="lname-update" required="">
                        <div class="invalid-feedback">
                            Last Name cannot be empty.
                        </div>
                    </div>
                </div>
   </div>
  <div class="form-group">
	<h4>Phone Information</h4>
	<?php
	//only display phone information if participant has a form associated with them
	if($formid !==null){
	?>
	
  <div class="form-group row">
	<div class="col-sm-4">
		<label for="class-name" class=""><b>Primary Phone</b></label>
		<input type="phone" class="form-control mask-phone" id="pphone-update" name="pphone-update" 
		<?php
			$phone = getPhoneNumbers("primary");
			if($phone['whatwegot'] !=0){
				echo "required=''";
			}
			echo $phone['readOnly'];
		?> >
		<div class="invalid-feedback">
			Primary Phone cannot be empty.
		</div>
	</div>
	<div class="col-sm-4">
		<label for="class-name" class=""><b>Secondary Phone</b></label>
		<input type="phone" class="form-control mask-phone" id="sphone-update" name="sphone-update"
		<?php
			$phone = getPhoneNumbers("secondary");
			echo $phone['readOnly'];
		?> >
	</div>
	<div class="col-sm-4">
		<label for="class-name" class=""><b>Day Phone</b></label>
		<input type="phone" class="form-control mask-phone"   id="dphone-update" name="dphone-update" 
		<?php
			$phone = getPhoneNumbers("day");
			echo $phone['readOnly'];
		?> >
	</div>
	<div class="col-sm-4">
		<label for="class-name" class=""><b>Evening Phone</b></label>
		<input type="phone" class="form-control mask-phone"  id="ephone-update" name="ephone-update" 
		<?php
			$phone = getPhoneNumbers("evening");
			echo $phone['readOnly'];
		?> >
	</div>
</div>
	<?php
	//if there is no form associated with the participant, prompt user to create one
	}else{
		?>
		<span>There is no intake packet found for this participant. Would you like to <a href='/intake-packet'> create one </a> ?</span>
		<?php
	}
	?>
	
		<div class="form-group">
		<h4>Address Information</h4>
		<?php
	//only display address information if participant has a form associated with them
	if($addressid!==null){
		?>
			<div class="form-group row"> 
				<div class="col-sm-3">
					<label for="class-name" class=""><b>House Number</b></label>
					<input type="text" class="form-control" <?=checkValue($address['addressnumber'])?> id="house-update" name="house-update" >
				</div>
				<div class="col-sm-4">
					<label for="class-name" class=""><b>Street Address</b></label>
					<input type="text" class="form-control" <?=checkValue($address['street'])?> id="street-update" name="street-update" >
				</div>
				<div class="col-sm-2">
					<label for="class-name" class=""><b>Apt </b></label>
					<input type="text" class="form-control" <?=checkValue($address['aptinfo'])?> id="apt-update" name="apt-update" >
				</div>
			</div>
			<div class="form-group row"> 
				<div class="col-sm-3">
					<label for="class-name" class=""><b>City</b></label>
					<input type="text" class="form-control" readonly <?=checkValue($address['city'])?> id="city-update" name="city-update" >
				</div>
				<div class="col-sm-4">
					<label for="class-name" class=""><b>State</b></label>
					<input type="text" class="form-control" readonly <?=checkValue($address['state'])?> id="state-update" maxlength="2" name="state-update" >
				</div>
				<div class="col-sm-2">
					<label for="class-name" class=""><b>Zip Code</b></label>
					<input type="text" class="form-control" readonly <?=checkValue($address['zipcode'])?> id="zip-update" maxlength="6" name="zip-update" >
				</div>
			</div>
		<?php
	}else{
		?>
		<span>There is no intake packet found for this participant. Would you like to <a href='/intake-packet'> create one </a> ?</span>
		<?php
	}
	?>
	</div>
	</div>
  <input type='hidden' name='addressid' value='<?=$addressid?>'>
  <input type='hidden' name='form' value='<?=$formid?>'>
  <input type="submit" name="update" value="Update Information" class="btn cpca">
</form>	
</div>

</div>


<?php
include('footer.php');
?>