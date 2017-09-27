<?php
authorizedPage();

global $db, $params;

#Search for user inputed name, check both first/last combination and last/first name combinations
$searchquery = strtolower(rawurldecode(implode('/',$params)));
$result = $db->query("SELECT  participants.participantid, participants.dateofbirth, participants.race, people.firstname, people.lastname, people.middleinit ".
                        "FROM participants ".
                        "INNER JOIN people ON participants.participantid = people.peopleid ".
                        "WHERE LOWER(CONCAT(people.firstname, ' ' , people.lastname)) LIKE $1 ".
                        "OR LOWER(CONCAT(people.lastname, ' ' , people.firstname)) LIKE $1",  ['%'.$searchquery.'%']);

include('header.php');
?>
<div class="w-100 d-flex flex-column">
    <a href="/back"><button class="cpca btn"><i class="fa fa-arrow-left"></i> Back</button></a>
    <ul class="list-group" style="max-width: 700px; width: 100%; margin: 0 auto">
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
                <button class="btn btn-outline-info advanced-info"><i class="fa fa-question" aria-hidden="true"></i></button>
                <span>&nbsp;<?php echo $row['lastname'].", ".$row['middleinit']." ".$row['firstname'];?></span>
                <a class="float-right" href="/view-participant/<?= $row['participantid'] ?>">
                    <button class="p-view btn cpca">
                        View Record
                    </button>
                </a>

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