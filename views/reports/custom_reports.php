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
	
	include('header.php');
?>
<div class="container py-5">
	<div class="col-auto" >
		<form action="custom-reports-table" method="POST" autocomplete="on">
			<fieldset>
				<!-- Select Basic -->
				<div class="form-group row">
					<label class="col-md-2 col-form-label" for="month"><b>Month</b></label>
					<div class="col-md-4">
						<select id="month" name="month" class="form-control">
							<option value="1">January</option>
							<option value="2">February</option>
							<option value="3">March</option>
							<option value="4">April</option>
							<option value="5">May</option>
							<option value="6">June</option>
							<option value="7">July</option>
							<option value="8">August</option>
							<option value="9">September</option>
							<option value="10">October</option>
							<option value="11">November</option>
							<option value="12">December</option>
						</select>
					</div>
				</div>
				<!-- Select Basic -->
				<div class="form-group row">
					<label class="col-md-2 col-form-label" for="year"><b>Year</b></label>
					<div class="col-md-4">
						<select id="year" name="year" class="form-control">
							<!-- YEAR OPTIONS INIT IN JAVASCRIPT BELOW.
								BASED ON CURRENT YEAR.-->
						</select>
					</div>
				</div>
				<!-- Multiple Checkboxes -->
				<div class="form-group row">
					<label class="col-md-2 col-form-label" for="location[]"><b>Location</b></label>
					<div class="col-md-4">
						<div class="checkbox">
							<label for="location-0">
							<input type="checkbox" name="location[]" id="location-0" value="Cornerstone">
							Cornerstone
							</label>
						</div>
						<div class="checkbox">
							<label for="location-1">
							<input type="checkbox" name="location[]" id="location-1" value="Dutchess County Jail">
							Dutchess County Jail
							</label>
						</div>
						<div class="checkbox">
							<label for="location-2">
							<input type="checkbox" name="location[]" id="location-2" value="Florence Manor">
							Florence Manor
							</label>
						</div>
						<div class="checkbox">
							<label for="location-3">
							<input type="checkbox" name="location[]" id="location-3" value="Fox Run">
							Fox Run
							</label>
						</div>
						<div class="checkbox">
							<label for="location-4">
							<input type="checkbox" name="location[]" id="location-4" value="ITAP Meadow Run">
							ITAP Meadow Run
							</label>
						</div>
					</div>
				</div>
				<!-- Multiple Checkboxes -->
				<div class="form-group row">
					<label class="col-md-2 col-form-label" for="race[]"><b>Race</b></label>
					<div class="col-md-4">
						<div class="checkbox">
							<label for="race-0">
							<input type="checkbox" name="race[]" id="race-0" value="Caucasian">
							Caucasian
							</label>
						</div>
						<div class="checkbox">
							<label for="race-1">
							<input type="checkbox" name="race[]" id="race-1" value="African American">
							African American
							</label>
						</div>
						<div class="checkbox">
							<label for="race-2">
							<input type="checkbox" name="race[]" id="race-2" value="Multi Racial">
							Multi Racial
							</label>
						</div>
						<div class="checkbox">
							<label for="race-3">
							<input type="checkbox" name="race[]" id="race-3" value="Latino">
							Latino
							</label>
						</div>
						<div class="checkbox">
							<label for="race-4">
							<input type="checkbox" name="race[]" id="race-4" value="Pacific Islander">
							Pacific Islander
							</label>
						</div>
						<div class="checkbox">
							<label for="race-5">
							<input type="checkbox" name="race[]" id="race-5" value="Native American">
							Native American
							</label>
						</div>
						<div class="checkbox">
							<label for="race-6">
							<input type="checkbox" name="race[]" id="race-6" value="Other">
							Other
							</label>
						</div>
					</div>
				</div>
				<!-- Multiple Checkboxes -->
				<div class="form-group row">
					<label class="col-md-2 col-form-label"><b>Age</b></label>
					<div class="col-md-5">
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
						<button type="submit" class="btn cpca">Generate Report</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
</div>
<script>
	const NUM_YEARS_BACK = 4;
	
	window.onload = initPage;
	
	function initPage() {
		var d = new Date();
		var monthElem = document.getElementById("month");
		monthElem.selectedIndex = d.getMonth();
		var yearElem = document.getElementById("year");
		var year = d.getFullYear();
		for (i = 0; i <= NUM_YEARS_BACK; i++) {
			var opt = document.createElement('option');
			opt.value = year - i;
			opt.innerHTML = year - i;
			yearElem.appendChild(opt);
		}
	}
	
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
	
	function maxAgeChange() {
		var maxIndex = document.getElementById("maxAge").selectedIndex;
		var minList = document.getElementById("minAge").options;
		for (i = 1; i < minList.length; i++) {
			minList[i].disabled = i < maxIndex;
		}
	}
</script>
<?php include('footer.php'); ?>