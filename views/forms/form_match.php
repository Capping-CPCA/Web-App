<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Matching lookup that searchs for possible duplicate participants in the db
 *
 * After the referral/sel-referral/ intake form is completed, the page will
 * grab all previously entered information and attempt to find matches on this page 
 * through an ajax request. This page will compare user input against the db and 
 * return a list of matches that are then displayed to the user
 *
 * @author Vallie Joseph and Michelle Crawley
 * @copyright 2017 Marist College
 * @version 1.1
 * @since 1.1
 */

authorizedPage();
global $db, $params;
include ("header.php");

// Grab info passed from hidden for mfields
$firstName = ucwords(trim($_POST['firstname']));
$lastName = ucwords(trim($_POST['lastname']));

// Query the db to find any duplicate entries
$db->prepare("get-duplicate-data", "SELECT DISTINCT ON (peopleid) *
                                    FROM ParticipantModal
                                    WHERE firstname = LOWER($1) AND lastname = LOWER($2) LIMIT 10");
$result = $db->execute("get-duplicate-data", [$firstName, $lastName]);

function checkEmpty($string){
    if($string == ""){
        echo "<i> N/A</i>";
    }else{
         echo $string;
    }
}
?>
 <ul class='list-group'>

    <?php
    // Display all matching values as a list item
    while($row = pg_fetch_assoc($result)){
    ?>
         <li class="list-group-item duplicate-entries mt-1 mb-1" id="<?=$row['peopleid']?>">
                <?= ucwords($row['lastname']).", ".ucwords($row['firstname']). " ". ucwords($row['middleinit']);?>
            <div class='details'>
                <?= " <div><b>DOB: </b>".$row['dateofbirth']." </div> ".
                "<div> <b>Address: </b> ".$row['addressnumber'].$row['aptinfo']." ".$row['street']. " ".$row['city']. " ".$row['state']. " ".$row['zipcode']." </div> ".
                " <div><b>Phone: </b>".($row['phonenumber'] == "" ? "" : prettyPrintPhone($row['phonenumber']))."</div> ".
                " <div><b>Sex: </b> ".$row['sex']."</div>"; 
                ?>
            </div>
        </li>
    <?php
    }
    ?>
<ul>
<?php include('footer.php'); ?>