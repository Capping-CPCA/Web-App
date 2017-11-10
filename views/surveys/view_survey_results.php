<?php
global $params, $route, $view;
include ('../models/Notification.php');
$pages = ['view','edit','create','delete','restore'];
# Update page title to reflect route
if (!empty($params) && in_array($params[0], $pages)) {
	$newTitle = $params[0];
	$route['title'] .= ' - ' . strtoupper($newTitle[0]) . strtolower(substr($newTitle, 1));
}
# Select page to display
if (!empty($params) && $params[0] == 'view') {
	$view->display('/view-survey-results.php');
} else if (!empty($params) && $params[0] == 'results') {
    $view->display('surveys/results.php');
} else {
	include('header.php');
	global $db;
	$filter = "";
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$filter = isset($_POST['filter']) ? "%" . $_POST['filter'] . "%" : "%%";
		$result = $db->query("SELECT * FROM curricula WHERE LOWER(curriculumname::text) LIKE LOWER($1) " .
			"OR LOWER(curriculumtype::text) LIKE LOWER($1) ORDER BY curriculumname", [$filter]);
	} else {
		//$result = $db->query("SELECT * FROM curricula ORDER BY curriculumname", []);
	}
	?>
	<div style="width: 100%; height: 100%">
		
		
		<!------------------------ This is the update button ------------------------->
		
		<?php
			function updateDB(){	
				//$command = escapeshellcmd('py C:\Program Files (x86)\EasyPHP-Devserver-17\eds-www\views\surveys\pullAndParseSurveys.py');
				$output = shell_exec('py "C:\Program Files (x86)\EasyPHP-Devserver-17\eds-www\views\surveys\pullAndParseSurveys.py"');
				echo("<script>alert('" . $output . "')</script>");
				?>
					<script>
						var now = new Date();
						
						localStorage.setItem("lastSave", now);
					</script>
				<?php
			}
			
			if (isset($_POST['updated'])) {
				
				updateDB();
			}
		?>
		
		<script>
			function validateForm() {
				var w = document.forms["userInput"]["classes"].value;
				var x = document.forms["userInput"]["Month"].value;
				var y = document.forms["userInput"]["Day"].value;
				var z = document.forms["userInput"]["Year"].value;
				if (w == "") {
					alert("Please select a class");
					return false;
				} else if (x == "") {
					alert("Please select a Month");
					return false;
				} else if (y == "") {
					alert("Please select a Day");
					return false;
				} else if (z == "") {
					alert("Please select a Year");
					return false;
				} else {
					return true;
				}
			}
		</script>
		<p>
		
		<!--<form method="POST">
		<input name="updated" value="Update Surveys" type="Submit" class="btn btn-success" style="float: right;"/>
		</form>-->
		
		<center>
		<br />
		<br />
		<div class="container">
		<div class="row justify-content-md-center">
		<div class="col-sm" style="max-width:75%">
		
		<h4>Select class and filter by date for results</h4>
		</div>
		</div>
		</div>
		<!------------------------ This is the class selector ------------------------->
		<div class="container" >
			<div class="row justify-content-md-center">
			<div class="col-sm col-lg-6">
		<form method="POST" action="/surveys/results" onsubmit = "return validateForm()"name = "userInput">
		<div class="form-group">
		<select class="form-control" id="classes" name="classes">
			<option value="" disabled selected hidden>Class</option>
			<option value="Cornerstone">Cornerstone</option>
			<option value="Fishkill/New Vision Church">Fishkill/New Vision Church</option>
			<option value="Florence Manor">Florence Manor</option>
			<option value="Fox Run">Fox Run</option>
			<option value="In-House Men">In-House Men</option>
			<option value="In-House Women">In-House Women</option>
			<option value="ITAP">ITAP</option>
			<option value="jenfjg">jenfjg</option>
			<option value="Meadow Run">Meadow Run</option>
			<option value="Men's DC Jail">Men's DC Jail</option>
			<option value="Women's DC Jail">Women's DC Jail</option>
			
		</select>
		</div>

		</div>
		</div>
		</div>
		<!------------------------ This is the date selector ------------------------->
		<div class="container" >
				<div class="row justify-content-md-center">
			<div class="col-sm col-lg-2">
		<div class="form-group">
		<select class="form-control" name="Month" id="month">
		<option selected disabled="disabled" value="">Month</option>
			<option value="01">January</option>
			  <option value="02">February</option>
			  <option value="03">March</option>
			  <option value="04">April</option>
			  <option value="05">May</option>
			  <option value="06">June</option>
			  <option value="07">July</option>
			  <option value="08">August</option>
			  <option value="09">September</option>
			  <option value="10">October</option>
			  <option value="11">November</option>
			  <option value="12">December</option>
		</select>
		</div>
		</div>
		<div class="col-sm col-lg-2">
		<div class="form-group">
		<select class="form-control" name="Day" id="day">
			<option selected disabled="disabled" value="">Day</option>
			<option value="01">1</option>
			  <option value="02">2</option>
			  <option value="03">3</option>
			  <option value="04">4</option>
			  <option value="05">5</option>
			  <option value="06">6</option>
			  <option value="07">7</option>
			  <option value="08">8</option>
			  <option value="09">9</option>
			  <option value="10">10</option>
			  <option value="11">11</option>
			  <option value="12">12</option>
			  <option value="13">13</option>
			  <option value="14">14</option>
			  <option value="15">15</option>
			  <option value="16">16</option>
			  <option value="17">17</option>
			  <option value="18">18</option>
			  <option value="19">19</option>
			  <option value="20">20</option>
			  <option value="21">21</option>
			  <option value="22">22</option>
			  <option value="23">23</option>
			  <option value="24">24</option>
			  <option value="25">25</option>
			  <option value="26">26</option>
			  <option value="27">27</option>
			  <option value="28">28</option>
			  <option value="29">29</option>
			  <option value="30">30</option>
			  <option value="31">31</option>
		</select>
		</div>
		</div>
		<div class="col-sm col-lg-2">
		<div class="form-group">
		<select class="form-control" name="Year" id="year"';
		<?php $starting_year  =date('2000');
		 $ending_year = date('Y', strtotime('+0 year'));
		 $current_year = date('Y');
		 for($starting_year; $starting_year <= $ending_year; $starting_year++) {
			 echo '<option value="'.$starting_year.'"';
			 if( $starting_year ==  $current_year ) {
					echo ' selected="selected"';
			 }
			 echo ' >'.$starting_year.'</option>';
		 }               
		 echo '<select>';
		 ?>
		<!--
		<select class="form-control" name="Year" id="year">
		<option selected disabled="disabled" value="">Year</option>
			<option value="2017">2017</option>
			<option value="2016">2016</option>
			<option value="2015">2015</option>
			<option value="2014">2014</option>
			<option value="2013">2013</option>
			<option value="2012">2012</option>
			<option value="2011">2011</option>
			<option value="2010">2010</option>
			<option value="2009">2009</option>
			<option value="2008">2008</option>
			<option value="2007">2007</option>
			<option value="2006">2006</option>
			<option value="2005">2005</option>
			<option value="2004">2004</option>
			<option value="2003">2003</option>
			<option value="2002">2002</option>
			<option value="2001">2001</option>
			<option value="2000">2000</option>
		</select>
		-->
		</div>
		</div>
		</div>
		</div>
<<<<<<< HEAD
			<input type="Submit" method = "POST" class="btn btn-primary" value="Search Surveys">
=======
			<input type="Submit" method = "POST" class="btn btn-secondary" value="Search Surveys">
>>>>>>> 75673d2592387acab13013ce084cfab93a1fd4a1
			</form>
		
		<p>
		<br />
<<<<<<< HEAD
		<a href="http://Mari.st/pep" target="_blank">Link to Survey</a>
=======
		<a href="http://Mari.st/pep" target="_blank" class="text-secondary">Link to Survey</a>
>>>>>>> 75673d2592387acab13013ce084cfab93a1fd4a1
		<div class="container">
			<div class="row justify-content-md-center">
				<div class="col-sm" style="max-width:75%; position: absolute; bottom: 5%;">
				
					<h5><small>Survey Database was last updated on:</small><h5> 
					<h6 style="font-size: 10px; color: darkGray;" id="result"></p>
					<script>
					if (localStorage.lastSave != null){
						
						document.getElementById("result").innerHTML = localStorage.lastSave;
						
					} else { 
					
						document.getElementById("result").innerHTML = "never";
						
					}
					</script>
					<p>
					<form method="POST">
<<<<<<< HEAD
						<input name="updated" value="Update Surveys" type="Submit" class="btn btn-success" />
=======
						<input name="updated" value="Update Surveys" type="Submit" class="btn cpca" />
>>>>>>> 75673d2592387acab13013ce084cfab93a1fd4a1
					</form>
				</div>
			</div>
		</div>
		
		</center>
	
	</div>

	<?php
	include('footer.php');
}
?>