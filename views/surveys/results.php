<?php
global $params, $db;

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
    <a href="/surveys"><button class="btn btn-success"><i value="Back" class="fa fa-arrow-left"></i> Back</button></a>
    <!--<div class="jumbotron form-wrapper mb-3">-->
	<center>
	<div class="container">
		<div class="row justify-content-md-center">
			<div class="col-sm">
				
				<h3 style="color: #5C639A;" id="date">Results for <?php 
				$months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
				echo($_POST["classes"])?><br /></h3><h6 style="color: #5C639A;";><b> <?php echo($months[$_POST["Month"] - 1] . " " . $_POST["Day"] . ", " . $_POST["Year"]) ?></b></h6>
			</div>
		</div>
	</div>
	</center>
	<p>
	
	<?php
		
		$query = $db->query("SELECT participantname FROM surveys WHERE cast(starttime as text) LIKE '" . $_POST["Year"] . "-" . $_POST["Month"] . "-" . $_POST["Day"] . "%' and topicname = '" . $_POST["classes"] . "' ORDER BY participantname;", []);
		$counter = 1;
		$nameNum = 1;
		
		while ($line = pg_fetch_array($query, null, PGSQL_ASSOC)) {
			foreach ($line as $col_value) {
				echo ('
				<div class="container">
					<div class="row justify-content-md-center">
						<div class="col-sm" style="max-width:85%">
							<div id="accordion" role="tablist">
								<div class="card" style="border: 0.5px solid #5C639A;">
									<div class="card-header" role="tab" id="heading' . $nameNum . '">
										<h4 class="mb-0"><small><b>
											<a data-toggle="collapse" href="#collapse' . $nameNum . '" aria-expanded="false" aria-controls="collapse" style="color: #343A40;-">') . 
												stripslashes($col_value) . 
												('</a>
												</b>
												</small>
												</h4>
												
												</div>
												'); 
												
				$query2 = $db->query("SELECT * FROM surveys WHERE participantname='" . $col_value . "' and cast(starttime as text) LIKE '" . $_POST["Year"] . "-" . $_POST["Month"] . "-" . $_POST["Day"] . "%' and topicname = '" . $_POST["classes"] . "';", []);
				//$result2 = pg_query($db_connection,$query2);
				echo('
					<div id="collapse' . $nameNum . '" class="collapse" role="tabpanel" aria-labelledby="heading' . $nameNum . '" data-parent="" >
					<div class="card-body" id="testCopy' . $nameNum . '"> 
					');
					
				while ($line2 = pg_fetch_array($query2, null, PGSQL_ASSOC)) {
			
					foreach ($line2 as $col_value) {
								echo("<p><b>Question " . $counter . ": </b>");
								echo stripslashes($col_value);
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
		if($nameNum == 1) {
			
			echo("<center><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><h5 style='color: #CACCCE;'>No survey results found.</h5></center>");
			
			
		} else {
		
			?>
			<center>
			<p>
			<input value="Copy Class" type="Submit" class="btn btn-success" onclick="expand()">
			<input value="Print Class" type="Submit" class="btn btn-success" onclick="downloadPDFClass()">
			</p>
			</center>
			<?php
		}
		pg_free_result($query);
	
	
	?>
	
				
       
    <!--</div>-->
</div>



<?php
include ('footer.php');