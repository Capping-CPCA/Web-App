<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Matching lookup that searchs for possible duplicate participants in the db
 *
 * After the user submits a form, they are redirected to this page to find possible
 * duplicates. If the db returns any duplicates, the user able to select the duplicate*
 * and create a new form without creating another participant
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
                                    WHERE LOWER(firstname) = LOWER($1) AND LOWER(lastname) = LOWER($2) LIMIT 10;");
                                    $result = $db->execute("get-duplicate-data", [$firstName, $lastName]);

/**
 * Parsing form information sent by previous page
 * to send back to original page once duplicate is found
 * @param valArray - a string of serialized data sent form previous page
 * return associative array of the serialized values
*/
function getPrevFormValues($valArray){
    // Remove all & that seperates the passed form vars
    $array = explode("&",$valArray);
    $newArray = array();
    // Loop through all of the passed vars to create an assoc array
    foreach($array as $key=>$value){
        if($value !="selectedID="){
            // Split the string on = to create 2 values, one will be the key of the key value pair
            $value = (explode("=",$value));
            // Decode the html entities in the passed string
            if(strpos($value[0], "race") === true || strpos($value[0], "state") === true || strpos($value[0], "race") === true){
               $value[1]= strtolower($value);
            }else{
                
                $newArray[$value[0]]= urldecode($value[1]);
            }
        } 
    }
    return $newArray;
}

// Assigning new associative array to variable for reference in hidden form
$prevData = getPrevFormValues($_POST['prevFormData']);

// Display all matching values as a list item
if (pg_num_rows($result) > 0){  
?>

<div style="width: 100%">
    <div class="row">
        <div class="col">
            <h4 class="display-4" style="font-size:2rem;">Possible Duplicates Found: </h4>
            <h4 class="display-4 text-muted" style="font-size:1.75rem;">Are You Sure this is a new Participant?</h4>
        </div>
    </div>
             <form action="<?=$_POST['pageFrom']?>" method="POST">
                <?php
                // Option for the user to create a new participant if they do not believe there is a duplicate
                foreach ($prevData as $key=>$value){
                echo "<input type='hidden' name='$key' value='$value'>";
                }
                ?>
                <input type="hidden" name="selectedID" value="">
                <button class="btn cpca btn-sm" type="submit"> Yes, Continue Creating Participant</button>
            </form>
            
        <h4 class="display-4 text-muted text-center" style="font-size:1.75rem;">Possible Matches:</h4>
    <div class="row d-flex flex-row justify-content-center flex-wrap">
            <?php
            // Displays all possible duplicates with additional information
            while ($row = pg_fetch_assoc($result)){
            ?>
            <div class="card result-card" >
                    <div class="card-body">
                    <div class="row">
                        <div class="col text-center">
                            <img class="icon-img" src="/img/default_av.jpg">
                            <h4 class="card-title">
                            <?= ucwords($row['lastname']).", ".ucwords($row['firstname']). " ". ucwords($row['middleinit']);?>
                            </h4>
                        </div>
                        <div class="col">
                            <div class="card-subtitle text-muted">
                                <div><b>DOB: </b><?= $row['dateofbirth'] ?></div>
                                <div><b>Address: </b><?= $row['addressnumber'].$row['aptinfo']." ".$row['street']. " ".$row['city']. " ".$row['state']. " ".$row['zipcode'] ?></div>
                                <div><b>Phone: </b><?= ($row['phonenumber'] == "" ? "" : prettyPrintPhone($row['phonenumber'])) ?></div>
                                <div><b>Sex: </b><?= $row['sex'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer d-flex flex-row justify-content-center">
                    <form action="<?=$_POST['pageFrom']?>" method="POST" class="mb-0">
                        <?php
                        foreach ($prevData as $key=>$value){
                        echo "<input type='hidden' name='$key' value='$value'>";
                        }
                        ?>
                        <input type="hidden" name="selectedID" value="<?=$row['peopleid']?>">
                        <button class="btn btn-outline-secondary btn-sm" type="submit">Select</button>
                    </form>
                </div>

            </div>
            <?php } ?>
        </div>
           
            
    </div>
    <?php
    }else{
        // Triggers if there are no duplicates found
        ?>
        
        <h4 class="display-4 text-muted text-center" style="font-size:1.75rem;">Creating Participant...</h4>
        
        <form action="<?=$_POST['pageFrom']?>" method="POST" id="noDuplicates">
            <?php
            foreach ($prevData as $key=>$value){
                echo "<input type='hidden' name='$key' value='$value'>";
            }
            ?>
            <input type="hidden" name="selectedID" value="">
        </form>
        
        <script>
        $("#noDuplicates").submit();
        </script>
            
        <?php
    }
  ?>
  
            
            </div>
            <?php
include('footer.php'); ?>