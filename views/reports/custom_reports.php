<?php
	/**
	 * PEP Capping 2017 Algozzine's Class
	 *
	 * Allows users to create a custom report.
	 *
	 * This allows users to specify what information they would 
	 * like to see in a custom report.
	 * The 'Generate Report' button will kick off a custom query
	 * to the database and display the results in a new page
	 * (defined in 'custom_reports_table.php')
	 *
	 * @author Daniel Ahl
	 *		   Rafael Mormol
	 * @copyright 2017 Marist College
	 * @version 0.3
	 * @since 0.1.4
	 */
	
	global $db;

	$query = "SELECT DISTINCT(curriculumname), curriculumid FROM curricula;";
	$currs = pg_fetch_all($db->query($query, []));

	$query = "SELECT unnest(enum_range(NULL::race))  ";
	$races = pg_fetch_all($db->query($query, []));

	include('header.php');
?>
<div class="container py-5">
	<form action="custom-reports-table" method="POST" autocomplete="on">
		<fieldset id="custom-reports-fields">
			<!-- Select Basic -->
			<div class="form-group row">
				<label class="col-md-2 col-form-label" for="startDate"><b>Start Date</b></label>
				<div class="col-md-4">
					<input class="form-control" type="date" id="startDate" name="startDate" onchange="onStartDateChange()"/>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-md-2 col-form-label" for="endDate"><b>End Date</b></label>
				<div class="col-md-4">
					<input class="form-control" type="date" id="endDate" name="endDate" onchange="onEndDateChange()"/>
				</div>
			</div>
			<!-- Multiple Checkboxes -->
			<div id="custom-reports-checkboxes" class="form-group row">
				<label class="col-md-2 col-form-label" for="curricula[]"><b>Curricula</b></label>
				<div class="col-md-4">
					<div>
						<input type="button" class="btn" id="currSelectAll" value="Select All" onclick="onCurrSelectAll()">
						<hr>
					</div>
					<?php
						if ($currs[0]["curriculumid"] !== NULL) {
							for ($i=0; $i<count($currs); $i++) {
								$currName = $currs[$i]["curriculumname"];
								$currId = $currs[$i]["curriculumid"];
								echo "<div class='checkbox'>
									<label for='curricula-$i'>
									<input class='currCheckBox' type='checkbox' name='curricula[]' id='curricula-$i' value=\"$currId\">
									$currName
									</label>
								</div>";
							}
						}
					?>
				</div>
			</div>
			<br>
			<!-- Multiple Checkboxes -->
			<div class="form-group row">
				<label class="col-md-2 col-form-label" for="race[]"><b>Race</b></label>
				<div class="col-md-4">
					<div>
						<input type="button" class="btn" id="raceSelectAll" onclick="onRaceSelectAll()" value="Select All">
						<hr>
					</div>
					<?php
					for ($i=0; $i<count($races); $i++) {
						$race = $races[$i]["unnest"];
						echo "<div class='checkbox'>
							<label for='race-$i'>
							<input class='raceCheckBox' type='checkbox' name='race[]' id='race-$i' value=\"$race\">
							$race
							</label>
						</div>";
					} ?>
				</div>
			</div>
			<!-- Multiple Checkboxes -->
			<div class="form-group row">
				<label class="col-md-2 col-form-label"><b>Age</b></label>
				<div class="col col-lg-4 col-lg-5">
					<div class="row">
						<div class="col-md-5">
							<select id="minAge" name="minAge" class="form-control" onchange="minAgeChange()">
								<option value="any">Any</option>
								<?php
									for ($i = 65; $i >= 18; $i--) {
										echo "<option value='$i'>$i</option>";
									}
									?>
							</select>
						</div>
						<div class="col-md-1" align="center">To</div>
						<div class="col-md-5">
							<select id="maxAge" name="maxAge" class="form-control" onchange="maxAgeChange()">
								<option value="any">Any</option>
								<?php
									for ($i = 65; $i >= 18; $i--) {
										echo "<option value='$i'>$i</option>";
									}
									?>
							</select>
						</div>
					</div>
				</div>
			</div>
			<!-- Submit -->
			<div class="form-group pt-2">
				<div class="col-md-7" align="center">
					<button id="custom-reports-generate" type="submit" class="btn cpca">Generate Report</button>
				</div>
			</div>
		</fieldset>
	</form>
</div>
<script>
	const NUM_YEARS_BACK = 4;
	
	window.onload = initPage;
	
	/*
	* Initialize the page by setting the date input to the
	* current day, and their min & max. The max is the current day,
	* and the the min is 4 years back from the current day.
	* If the date value is already set (meaning the user hit "back"
	* on the next page), do not reset the date values.
	*/
	function initPage() {
		var d = new Date();
		var startElem = document.getElementById("startDate");
		var endElem = document.getElementById("endDate");
		var year = d.getFullYear();
		var day = (d.getDate() < 10) ? "0" + d.getDate() : d.getDate();
		var month = (d.getMonth() < 9) ? "0" + (d.getMonth()+1) : d.getMonth() + 1;
		if (startElem.value === "") {
			startElem.value = year + "-" + month + "-" + day;
			endElem.value = startElem.value;
			startElem.max = startElem.value;
			endElem.max = endElem.value;
			year -= NUM_YEARS_BACK;
			startDate.min = year + "-" + month + "-" + day;
			endDate.min = year + "-" + month + "-" + day;
		} else {
			startElem.max = endElem.value;
			endElem.min = startElem.value;
			endElem.max = year + "-" + month + "-" + day;
			year -= NUM_YEARS_BACK;
			startElem.min = year + "-" + month + "-" + day;
			minAgeChange()
			maxAgeChange()
		}
	}
	
	/*
	* Disable all ages on maxAge that are less than the new minAge.
	*/
	function minAgeChange() {
		var minIndex = document.getElementById("minAge").selectedIndex;
		var maxList = document.getElementById("maxAge").options;
		if (minIndex === 0) {
			for (i = 1; i < maxList.length; i++) {
				maxList[i].disabled = false;
			}
		} else {
			for (i = 1; i < maxList.length; i++) {
				maxList[i].disabled = i > minIndex;
			}
		}
	}
	
	/*
	* Disable all ages on minAge that are greater than the new maxAge.
	*/
	function maxAgeChange() {
		var maxIndex = document.getElementById("maxAge").selectedIndex;
		var minList = document.getElementById("minAge").options;
		for (i = 1; i < minList.length; i++) {
			minList[i].disabled = i < maxIndex;
        }
	}

	/*
	* Set the endDate's min to the new startDate value.
	*/
	function onStartDateChange() {
		var startElem = document.getElementById("startDate");
		var endElem = document.getElementById("endDate");
		endElem.min = startElem.value;
		if (endElem.value < endElem.min) endElem.value = endElem.min;
	}

	/*
	* Set the startDate's max to the new endDate value.
	*/
	function onEndDateChange() {
		var startElem = document.getElementById("startDate");
		var endElem = document.getElementById("endDate");
		startElem.max = endElem.value;
		if (startElem.value > startElem.max) startElem.value = startElem.max;
	}
	
	function onRaceSelectAll() {
		raceButton = document.getElementById("raceSelectAll");
		raceBoxes = document.getElementsByClassName("raceCheckBox");
		for (i = 0; i < raceBoxes.length; i++){
			raceBoxes[i].checked = true;
		}
	}
	
	function onCurrSelectAll() {
		currButton = document.getElementById("currSelectAll");
		currBoxes = document.getElementsByClassName("currCheckBox");
		for (i = 0; i < currBoxes.length; i++){
			currBoxes[i].checked = true;
		}
	}

	//Display tutorials for page
	$(function() {
        showTutorial('customReportsFields');
    });
</script>
<?php include('footer.php'); ?>
