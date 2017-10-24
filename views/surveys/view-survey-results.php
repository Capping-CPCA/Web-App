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
	<div style="width: 100%">
		
		<center>
		
		<select id="classes" name="classes">
			<option value="Class One">Class One</option>
			<option value="Class Two">Class Two</option>
			<option value="Class Three">Class Three</option>
		</select>

		<br><br>

			<select name="Month" id="month">
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
			<select name="Day" id="day">
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
			<select name="Year" id="year">
			  <option value="2017">2017</option>
			</select>
		<br><br>
			<button type="submit" onclick="searchSurveys();">Search Surveys</button>
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
			alert(selectedClass+"  "+selectedMonth+"  "+selectedDay+"  "+selectedYear);
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