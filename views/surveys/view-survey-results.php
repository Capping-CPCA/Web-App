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
		<button type="button" class="btn btn-success">Update Surveys</button>
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
		<form>
		<div class="form-group">
		<select class="form-control" id="classes" name="classes">
			<option value="Class One">Class One</option>
			<option value="Class Two">Class Two</option>
			<option value="Class Three">Class Three</option>
			<option value="Class Three">Class Four</option>
			<option value="Class Three">Class Five</option>
			<option value="Class Three">Class Six</option>
		</select>
		</div>
		</form>
		</div>
		</div>
		</div>
		<!------------------------ This is the date selector ------------------------->
		<div class="container" style="height: 70px">
			<div class="row justify-content-md-center">
			<div class="col-sm col-lg-2">
		<form>
		<div class="form-group">
		<select class="form-control" name="Month" id="month">
			<option value="January">January</option>
			  <option value="February">February</option>
			  <option value="March">March</option>
			  <option value="April">April</option>
			  <option value="May">May</option>
			  <option value="June">June</option>
			  <option value="July">July</option>
			  <option value="August">August</option>
			  <option value="September">September</option>
			  <option value="October">October</option>
			  <option value="Nobember">November</option>
			  <option value="December">December</option>
		</select>
		</div>
		</form>
		</div>
		<div class="col-sm col-lg-2">
		<form>
		<div class="form-group">
		<select class="form-control" name="Day" id="day">
			<option value="1">1</option>
			  <option value="2">2</option>
			  <option value="3">3</option>
			  <option value="4">4</option>
			  <option value="5">5</option>
			  <option value="6">6</option>
			  <option value="7">7</option>
			  <option value="8">8</option>
			  <option value="9">9</option>
		</select>
		</div>
		</form>
		</div>
		<div class="col-sm col-lg-2">
		<form>
		<div class="form-group">
		<select class="form-control" name="Year" id="year">
			<option value="1">2017</option>
			<option value="2">2016</option>
			<option value="3">2015</option>
			<option value="4">2014</option>
			<option value="5">2013</option>
			<option value="6">2012</option>
			<option value="7">2011</option>
			<option value="8">2010</option>
			<option value="9">2009</option>
			<option value="6">2008</option>
			<option value="7">2007</option>
			<option value="8">2006</option>
			<option value="9">2005</option>
			<option value="6">2004</option>
			<option value="7">2003</option>
			<option value="8">2002</option>
			<option value="9">2001</option>
			<option value="9">2000</option>
		</select>
		</div>
		</form>
		</div>
		</div>
		</div>
		<a id="new-curriculum-btn" href="/surveys/results">
                <!--<button class="cpca btn"><i class="fa fa-plus"></i> Create Curriculum</button>-->
		<button type="button" class="btn btn-primary" onclick="searchSurveys();" href="results">Search Surveys</button>
		</a>	
		</center >
		
		
	
		
		
        
		
		<script>
		function searchSurveys() {
			
			var currentClass = document.getElementById('classes');
			var currentMonth = document.getElementById('month');
			var currentDay = document.getElementById('day');
			var currentYear = document.getElementById('year');
			var selectedClass = currentClass.options[currentClass.selectedIndex].value;
			var selectedMonth = currentMonth.options[currentMonth.selectedIndex].value;
			var selectedDay = currentDay.options[currentDay.selectedIndex].value;
			var selectedYear = currentYear.options[currentYear.selectedIndex].value;
			//alert(selectedClass+"  "+selectedMonth+"  "+selectedDay+"  "+selectedYear);
			fullSearch = selectedMonth + '/' + selectedDay + '/' + selectedYear;
			var query = "SELECT fullname FROM answers WHERE currentdate = '" + fullSearch + "' and class = '" + selectedClass + "'"
			alert(query);
		}
		
		
		
		
		</script>

	</div>

		<script>
		//$(function() {
			//showTutorial('curriculum');
		//});
	</script>

	<?php
	include('footer.php');
}
?>