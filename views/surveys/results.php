<?php
global $params, $db;
$isEdit = $params[0] == 'edit';
$id = isset($params[1]) ? $params[1] : '';
# Prepare SQL statements for later use
$db->prepare("get_curriculum", "SELECT * FROM curricula WHERE curriculumid = $1");
$db->prepare("get_curr_classes", "SELECT * FROM curriculumclasses WHERE curriculumid = $1 ORDER BY topicname");
$db->prepare("get_other_classes",
    "SELECT * FROM classes WHERE topicname NOT IN (" .
    "SELECT topicname FROM curriculumclasses WHERE curriculumid = $1" .
    ") ORDER BY topicname");
// If editing, populate data into variables
if ($isEdit) {
    $result = $db->execute("get_curriculum", [$id]);
    # If no results, curricula doesn't exist, redirect
    if (pg_num_rows($result) == 0) {
        header('Location: /curricula');
        die();
    }
    $curricula = pg_fetch_assoc($result);
    pg_free_result($result);
    # Classes associated with curriculum
    $topics = $db->execute("get_curr_classes", [$id]);
    # All other available classes
    $allTopics = $db->execute("get_other_classes", [$id]);
} else {
    $allTopics = $db->query("SELECT * FROM classes", []);
}
# Store table columns in variable
$name = isset($curricula) ? $curricula['curriculumname'] : '';
$type = isset($curricula) ? $curricula['curriculumtype'] : '';
$miss = isset($curricula) ? $curricula['missnumber'] : '';
# Used to track POST errors
$errors = [
    "name" => false,
    "type" => false,
    "miss" => false
];
# Used to display messages based on POST
$success = null;
# Validate form information, display errors if needed
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['name']) ? $_POST['name'] : $name;
    $type = isset($_POST['type']) ? $_POST['type'] : $type;
    $miss = isset($_POST['miss']) ? $_POST['miss'] : $miss;
    $newClasses = isset($_POST['classes']) ? $_POST['classes'] : [];
    $removedClasses = isset($_POST['removed']) ? $_POST['removed'] : [];
    $valid = true;
	
    if (!isValidText($name)) {
        $errors['name'] = true;
        $valid = false;
    }
    if (empty($type)) {
        $errors['type'] = true;
    }
    if (!isValidNumber($miss, 0)) {
        $errors['miss'] = true;
        $valid = false;
    }
    if ($valid) {
        if ($isEdit) {
            $res = $db->query("UPDATE curricula SET curriculumname = $1, curriculumtype = $2, " .
                "missnumber = $3 WHERE curriculumid = $4", [$name, $type, $miss, $id]);
        } else {
            $res = $db->query("INSERT INTO curricula (curriculumname, curriculumtype, missnumber) VALUES ($1, $2, $3) RETURNING curriculumid",
                [$name, $type, $miss]);
            $id = pg_fetch_assoc($res)['curriculumid'];
        }
        if ($res) {
            foreach ($newClasses as $c) {
                $db->query("INSERT INTO curriculumclasses VALUES ($1, $2)", [$c, $id]);
            }
            foreach ($removedClasses as $r) {
                $db->query("DELETE FROM curriculumclasses WHERE topicname = $1 AND curriculumid = $2",
                    [$r, $id]);
            }
            $success = true;
        } else {
            $success = false;
        }
        # update variables for d<isplaying new values
        $curricula = pg_fetch_assoc($db->execute("get_curriculum", [$id]));
        if (isset($topics)) pg_free_result($topics);
        $topics = $db->execute("get_curr_classes", [$id]);
        if (isset($allTopics)) pg_free_result($allTopics);
        $allTopics = $db->execute("get_other_classes", [$id]);
    }
}
# Display Page
include ('header.php');

?>

<script>
function copyToClipboard(num) {
     var tempInput = document.createElement("textarea");
    tempInput.style = "position: absolute; left: -1000px; top: -1000px";
    tempInput.value = document.querySelector("#testCopy" + num).innerText;
	console.log(tempInput.value);
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
	
	alert("Individual has been copied successfully!");
	//var dummyContent = document.querySelector("#testCopy" + num).innerText;
	
	
	//var dummy = $('<input>').val(dummyContent).appendTo('body').select();
	//document.execCommand('Copy', false, null);
	//console.log(dummyContent);
  }	
 
 function copyClassToClipboard() {
	 
     var tempInput = document.createElement("textarea");
    tempInput.style = "position: absolute; left: -1000px; top: -1000px";
    tempInput.value = document.querySelector("#entireClass").innerText.replace("Back", "");
	console.log(tempInput.value);
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
	
	
	//var dummyContent = document.querySelector("#testCopy" + num).innerText;
	
	
	//var dummy = $('<input>').val(dummyContent).appendTo('body').select();
	//document.execCommand('Copy', false, null);
	//console.log(dummyContent);
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
    <a href="/back"><button class="btn btn-success"><i value="Back" class="fa fa-arrow-left"></i> Back</button></a>
    <!--<div class="jumbotron form-wrapper mb-3">-->
	<center>
	<div class="container">
		<div class="row justify-content-md-center">
			<div class="col-sm">
				
				<h4 id="date">Results for <?php echo($_POST["classes"])?> held on <?php echo($_POST["Month"] . "/" . $_POST["Day"] . "/" . $_POST["Year"]) ?></h4>
			</div>
		</div>
	</div>
	</center>
	<p>
	
	<?php
	
		$db_connection = pg_connect("host=10.11.12.24 dbname=Survey user=postgres password=password");
		
		$query = "SELECT fullname FROM answers WHERE currentdate LIKE '" . $_POST["Month"] . "/" . $_POST["Day"] . "/" . $_POST["Year"] . "%' and workshoptopic = '" . $_POST["classes"] . "';";
		$result = pg_query($db_connection,$query);
		$counter = 1;
		$nameNum = 1;
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			
			foreach ($line as $col_value) {
				echo ('
				<div class="container">
					<div class="row justify-content-md-center">
						<div class="col-sm" style="max-width:85%">
							<div id="accordion" role="tablist">
								<div class="card">
									<div class="card-header" role="tab" id="heading' . $nameNum . '">
										<h5 class="mb-0">
											<a data-toggle="collapse" href="#collapse' . $nameNum . '" aria-expanded="false" aria-controls="collapse">') . 
												$col_value . 
												('</a>
												</h5>
												</div>
												'); 
				$query2 = "SELECT * FROM answers WHERE fullname='$col_value' and currentdate LIKE '" . $_POST["Month"] . "/" . $_POST["Day"] . "/" . $_POST["Year"] . "%' and workshoptopic = '" . $_POST["classes"] . "';";
				$result2 = pg_query($db_connection,$query2);
				echo('
					<div id="collapse' . $nameNum . '" class="collapse" role="tabpanel" aria-labelledby="heading' . $nameNum . '" data-parent="" >
					<div class="card-body" id="testCopy' . $nameNum . '"> 
					');
					
				while ($line2 = pg_fetch_array($result2, null, PGSQL_ASSOC)) {
			
					foreach ($line2 as $col_value) {
								echo("<p><b>Question " . $counter . ": </b>");
								echo($col_value);
								echo("</p>");
								$counter += 1;
				
					}
					
				}
				$counter = 1;
											
				
			}
			echo('
			</div><p><center>'); ?>
			<input value="Copy Individual" type="Submit" class="btn btn-success" onclick="copyToClipboard(<?php echo($nameNum); ?>)">
			<input value="Print Individual" type="Submit" class="btn btn-success" onclick="downloadPDFIndividual(<?php echo($nameNum);?>)">
			<?php
			echo('</center></p>
			</div>
			</div>
			</div>
            </div>
		</div>
	</div>
	</p>
	');	
			$nameNum += 1;
		}
			?>
			<center>
			<input value="Copy Class" type="Submit" class="btn btn-success" onclick="expand()">
			<input value="Print Class" type="Submit" class="btn btn-success" onclick="downloadPDFClass()">
			</center>
			<?php
		pg_free_result($result);
		pg_close($db_connection);
	
	
	?>
	
				
       
    <!--</div>-->
</div>



<?php
include ('footer.php');