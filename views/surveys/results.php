<?php

global $db;

// Prevent navigating to page through URL
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    header("Location: /surveys");
    die();
}

$class = html_entity_decode($_POST['classes'], ENT_COMPAT);

# Display Page
include ('header.php');

?>

<script>
    function copyToClipboard(num) {
        var tempInput = document.createElement("textarea");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = document.querySelector("#testCopy" + num).innerText;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        
        alert("Individual has been copied successfully!");
    }    
 
    function copyClassToClipboard() {
     
        var tempInput = document.createElement("textarea");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = document.querySelector("#entireClass").innerText.replace("Back", "");
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
    }    
      
    function expand() {
        var originalContents = document.body.innerHTML;
        $('.collapse').collapse('toggle');    
        setTimeout(copyClassToClipboard, 200);
        setTimeout(function(){document.body.innerHTML = originalContents;}, 300);
        setTimeout(function(){
            alert("Class has been copied successfully!")
        }, 1000);
    }
        
    function collapseCards() {
        $('.collapse').collapse('toggle');
    }
    
    function downloadPDFClass(){
        var originalContents = document.body.innerHTML;
        $('.collapse').collapse('toggle');    
        setTimeout(function(){
            var printContents = document.getElementById("entireClass").innerHTML.replace('Back', '');
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }, 200);
    }
    
    function downloadPDFIndividual(num){
        var printContents = document.getElementById("testCopy" + num).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>

<div class="page-wrapper" id="entireClass">
    <a href="/surveys"><button class="btn cpca"><i class="fa fa-arrow-left"></i> Back</button></a>
    <!--<div class="jumbotron form-wrapper mb-3">-->
    <div style="text-align: center;">
        <div class="container">
            <div class="row justify-content-md-center">
                <div class="col-sm">
                    <h3 style="color: #343A40;" id="date">
                        Results for
                        <?php
                            $months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                            echo($_POST["classes"])
                        ?>
                    </h3>
                    <h6 style="color: #5C639A;">
                        <b><?= $months[$_POST["Month"] - 1] . " " . $_POST["Day"] . ", " . $_POST["Year"] ?></b>
                    </h6>
                </div>
            </div>
        </div>
    </div>

    <?php

        $date = $_POST["Year"] . "-" . $_POST["Month"] . "-" . $_POST["Day"] . "%";
        $query = $db->query("SELECT participantname FROM surveys WHERE cast(starttime as text) LIKE $1 AND topicname = $2 ORDER BY participantname", [$date, $class]);
        $counter = 1;
        $nameNum = 1;
        while ($line = pg_fetch_assoc($query)) {
            ?>
            <div class="container">
                <div class="row justify-content-md-center">
                    <div class="col-sm" style="max-width:85%">
                        <div id="accordion" role="tablist">
                            <div class="card" style="border: 0.5px solid #5C639A;">
                                <div class="card-header" role="tab" id="<?= 'heading' . $nameNum ?>">
                                    <h4 class="mb-0">
                                        <small>
                                            <b>
                                                <a data-toggle="collapse" href="<?= '#collapse' . $nameNum ?>" aria-expanded="false"
                                                   aria-controls="collapse" style="color: #343A40;">
                                                    <?= stripslashes($line['participantname']) ?>
                                                </a>
                                            </b>
                                        </small>
                                    </h4>
                                </div>
                                <div id="<?= 'collapse' . $nameNum ?>" class="collapse" role="tabpanel" aria-labelledby="<?= 'heading' . $nameNum ?>" data-parent="" >
                                    <div class="card-body" id="<?= 'testCopy' . $nameNum ?>">
                                    <?php
                                        foreach ($line as $key => $col_value) {
                                            // Skip these columns
                                            if ($key == 'surveyid')
                                                continue;
                                            ?>
                                            <p>
                                                <b>Question <?= $counter ?>: </b><?= stripslashes($col_value) ?>
                                            </p>
                                            <?php
                                            $counter += 1;
                                        }
                                        $counter = 1;
                                    ?>
                                    </div>
                                    <div style="text-align: center;">
                                        <input value="Copy Individual" type="Submit" class="btn btn-success" onclick="copyToClipboard(<?= $nameNum ?>)" />
                                        <input value="Print Individual" type="Submit" class="btn btn-success" onclick="downloadPDFIndividual(<?= $nameNum ?>)" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $nameNum += 1;
        }

        if($nameNum == 1) {
            ?>
            <br /><br />
            <div class="w-100 d-flex flex-column justify-content-center text-center">
                <h3 class="display-3 text-secondary" style="font-size: 40px;"><i
                            class="fa fa-exclamation-circle"></i></h3>
                <h3 class="display-3 text-secondary" style="font-size: 40px;">No Survey Results Found.</h3>
            </div>
            <?php
        } else {
            ?>
            <div style="text-align: center;">
                <input value="Copy Class" type="Submit" class="btn btn-success" onclick="expand()">
                <input value="Print Class" type="Submit" class="btn btn-success" onclick="downloadPDFClass()">
            </div>
            <?php
        }
        pg_free_result($query);
    ?>
</div>

<?php
include ('footer.php');