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
				$output = system('py "C:\Program Files (x86)\EasyPHP-Devserver-17\eds-www\views\surveys\pullAndParseSurveys.py"');
				echo $output;
			}
		?>
		<button class="btn btn-success" onclick="document.write('<? updateDB() ?>')">Update Surveys</button>
		
	
		<p>
		<center>
		<div class="container">
		<div class="row justify-content-md-center">
		<div class="col-sm" style="height: 70px; max-width:75%">
		<h4>Select class and filter by date for results</h4>
		</div>
		</div>
		</div>
		<!------------------------ This is the class selector ------------------------->
		<div class="container">
			<div class="row justify-content-md-center">
			<div class="col-sm col-lg-6">
		<form method="POST" action="/surveys/results">
		<div class="form-group">
		<select class="form-control" id="classes" name="classes">
		<option selected disabled="disabled" value="">Class</option>
			<option value="Class 1">Class 1</option>
			<option value="Class 2">Class 2</option>
			<option value="Class 3">Class 3</option>
			<option value="Class 4">Class 4</option>
			<option value="Class 5">Class 5</option>
			<option value="Class 6">Class 6</option>
		</select>
		</div>

		</div>
		</div>
		</div>
		<!------------------------ This is the date selector ------------------------->
		<div class="container" style="height: 70px">
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
		</div>
		</div>
		</div>
		</div>
			<!--<a id="new-curriculum-btn" href="/surveys/results">
					<button class="cpca btn"><i class="fa fa-plus"></i> Create Curriculum</button>
				
			
			</a>	
		</form> -->
		
			<input type="Submit" method = "POST" class="btn btn-primary" value="Search Surveys">
			</form>
		</center >
		
		<?php
		/*	
			
			if($_SERVER['REQUEST_METHOD'] == 'POST') { 
					
					$fullSearch = document.getElementById('month').options[currentMonth.selectedIndex].value + "/" + $_GET["Day"] + "/" + $_GET["Year"];
                    $db_connection = pg_connect("host=10.11.12.24 dbname=Survey user=postgres password=password");
                    $result = pg_query($db_connection, "SELECT fullname FROM answers WHERE currentdate = '" + $fullSearch + "' and class = '" + $_GET["classes"] + "'");
                    echo "<table>\n";
					while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
						echo "\t<tr>\n";
						foreach ($line as $col_value) {
							echo "\t\t<td>$col_value</td>\n";
						}
						echo "\t</tr>\n";
					}
					echo "</table>\n";
                    pg_free_result($result);
                    pg_close($db_connection);
				
			
			} else {
				
				echo("shit");
				
			}
			
			*/
        ?>

	</div>

	<?php
	include('footer.php');
}
?>