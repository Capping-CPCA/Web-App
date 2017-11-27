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

	$query = "SELECT DISTINCT(curriculumname), curriculumid FROM curricula WHERE df IS FALSE;";
	$currs = pg_fetch_all($db->query($query, []));
	
	$query = "SELECT DISTINCT(sitename) FROM sites;";
	$sites = pg_fetch_all($db->query($query, []));

	$query = "SELECT unnest(enum_range(NULL::race))  ";
	$races = pg_fetch_all($db->query($query, []));
	
	$query = "SELECT unnest(enum_range(NULL::sex))  ";
	$sexes = pg_fetch_all($db->query($query, []));

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
				<label class="col-md-2 col-form-label" for="sex[]"><b>Sex</b></label>
				<div class="col-md-4">
					<div>
						<input type="button" class="btn btn-sm" id="sexSelectAll" value="Deselect All" onclick="onSelectAll('sexSelectAll', 'sexCheckBox')">
						<hr>
					</div>
					<?php
					for ($i=0; $i<count($sexes); $i++) {
						$sex = $sexes[$i]["unnest"];
						echo "<div class='checkbox'>
							<label for='sex-$i'>
							<input onclick=\"onCheckBoxChange('sexSelectAll','sexCheckBox')\" class='sexCheckBox' type='checkbox' name='sex[]' id='sex-$i' value=\"$sex\">
							$sex
							</label>
						</div>";
					} ?>
				</div>
			</div>
			<br>
			<!-- Multiple Checkboxes -->
			<div id="custom-reports-checkboxes" class="form-group row">
				<label class="col-md-2 col-form-label" for="sites[]"><b>Locations</b></label>
				<div class="col-md-4">
					<div>
						<input type="button" class="btn btn-sm" id="sitesSelectAll" value="Deselect All" onclick="onSelectAll('sitesSelectAll', 'sitesCheckBox')">
						<hr>
					</div>
					<?php
						if ($sites[0]["sitename"] !== NULL) {
							for ($i=0; $i<count($sites); $i++) {
								$siteName = $sites[$i]["sitename"];
								echo "<div class='checkbox'>
									<label for='sites-$i'>
									<input onclick=\"onCheckBoxChange('sitesSelectAll','sitesCheckBox')\" class='sitesCheckBox' type='checkbox' name='sites[]' id='sites-$i' value=\"$siteName\">
									$siteName
									</label>
								</div>";
							}
						}
					?>
				</div>
			</div>
			<br>
			<!-- Multiple Checkboxes -->
			<div id="custom-reports-checkboxes" class="form-group row">
				<label class="col-md-2 col-form-label" for="curricula[]"><b>Curricula</b></label>
				<div class="col-md-4">
					<div>
						<input type="button" class="btn btn-sm" id="currSelectAll" value="Deselect All" onclick="onSelectAll('currSelectAll', 'currCheckBox')">
						<hr>
					</div>
					<?php
						if ($currs[0]["curriculumid"] !== NULL) {
							for ($i=0; $i<count($currs); $i++) {
								$currName = $currs[$i]["curriculumname"];
								$currId = $currs[$i]["curriculumid"];
								echo "<div class='checkbox'>
									<label for='curricula-$i'>
									<input onclick=\"onCheckBoxChange('currSelectAll','currCheckBox')\" class='currCheckBox' type='checkbox' name='curricula[]' id='curricula-$i' value=\"$currId\">
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
						<input type="button" class="btn btn-sm" id="raceSelectAll" onclick="onSelectAll('raceSelectAll', 'raceCheckBox')" value="Deselect All">
						<hr>
					</div>
					<?php
					for ($i=0; $i<count($races); $i++) {
						$race = $races[$i]["unnest"];
						echo "<div class='checkbox'>
							<label for='race-$i'>
							<input onclick=\"onCheckBoxChange('raceSelectAll','raceCheckBox')\" class='raceCheckBox' type='checkbox' name='race[]' id='race-$i' value=\"$race\">
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
	const MIN_YEAR = 2016;
	
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
			startDate.min = MIN_YEAR + "-01-01";
			endDate.min = MIN_YEAR + "-01-01";
		} else {
			startElem.max = endElem.value;
			endElem.min = startElem.value;
			endElem.max = year + "-" + month + "-" + day;
			startElem.min = MIN_YEAR + "-01-01";
			minAgeChange()
			maxAgeChange()
		}
		//Initialize 'Select All' btns
		var checkboxes = document.querySelectorAll("input[type='checkbox']");
		for (var i = 0; i < checkboxes.length; i++) {
			if (!checkboxes[i].checked) {
				className = checkboxes[i].className;
				switch (className) {
					case "sexCheckBox": 
						document.getElementById("sexSelectAll").value = "Select All";
						break;
					case "sitesCheckBox": 
						document.getElementById("sitesSelectAll").value = "Select All";
						break;
					case "currCheckBox": 
						document.getElementById("currSelectAll").value = "Select All";
						break;
					case "raceCheckBox": 
						document.getElementById("raceSelectAll").value = "Select All";
						break;
				}
			}
		}
	}
	
	/*
	* Enable Select All when checkbox is selected
	*/
	function onCheckBoxChange(btnId, className) {
		btn = document.getElementById(btnId);
		boxes = document.getElementsByClassName(className);
		checked = true;
		for (var i = 0; i < boxes.length; i++) {
			if (!boxes[i].checked) checked = false;
		}
		btn.value = (checked) ? "Deselect All" : "Select All";
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
	
	function onSelectAll(btnId, className) {
		btn = document.getElementById(btnId);
		boxes = document.getElementsByClassName(className);
		for (i = 0; i < boxes.length; i++){
			boxes[i].checked = btn.value == "Select All";
		}
		btn.value = (btn.value == "Select All") ? "Deselect All" : "Select All";
	}

	//Display tutorials for page
	$(function() {
        showTutorial('customReportsFields');
    });
</script>
<?php include('footer.php'); ?>
