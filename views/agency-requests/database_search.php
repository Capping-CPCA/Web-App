<?php
authorizedPage();
include('header.php');
  global $db, $params;
  #Search for user inputed name, check both first/last combination and last/first name combinations
        $searchquery= $_GET['searchquery'];
        $result = $db->query("SELECT  participants.participantid, participants.dateofbirth, participants.race, people.firstname, people.lastname, people.middleinit
								FROM participants
								INNER JOIN people ON participants.participantid = people.peopleid
								WHERE  CONCAT(people.firstname, ' ' , people.lastname) LIKE $1
								OR CONCAT(people.lastname, ' ' , people.firstname) LIKE $1",  ['%'.$searchquery.'%']);
	?>
    <div class="col-sm-12">
	<ul class="list-group">
		<?php 
			#if query returns nothing, throw an error to the user
			if(pg_num_rows($result) == 0){
		?>
				<div class="alert alert-warning">
					<strong>Sorry, did not return any results for </strong> <u><?php echo $searchquery ?></u>
				</div>
        <?php
			}//end if
		#check the result of query and loop through each row to display search results
        while ($row = pg_fetch_assoc($result)) {
            ?>
            <li class="list-group-item">
                <button class="btn btn-warning advanced-info"> ? </button>
                <?php echo $row['lastname'].", ".$row['middleinit']." ".$row['firstname'];?>
				
                <form class="result-launch " action="view-participant" method="GET">
                    <button type="submit" name="view-id"class="p-view btn btn-primary" value="<?php echo $row['participantid'] ?>">
                        View Record
                    </button>
                </form>
				
                <ul class="list-group sublist">
                    <li class="list-group-item ">
                        <b>DOB: </b><?php echo $row['dateofbirth']. " | <b>Race: </b> ".  $row['race']?>
                    </li>
                </ul>
            </li>

		<?php
		}
		?>
        </ul>

    </div>

<?php

include('footer.php');
?>