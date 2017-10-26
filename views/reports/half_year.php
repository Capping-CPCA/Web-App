<?php 
	/**
	 * PEP Capping 2017 Algozzine's Class
	 *
	 * Allows users to view Annual and Semi-Annual report figures.
	 *
	 * Displays all information required in the Annual and Semi-Annual
	 * reports, formatted into a series of tables.
	 * Users can select a year and either an Annual or Semi-Annual report
	 * from the dropdowns menus at the top, then hit the "Generate Report"
	 * to populate the table with values from the database.
	 *
	 * @author Daniel Ahl
	 *		   Rafael Mormol
	 * @copyright 2017 Marist College
	 * @version 0.3
	 * @since 0.1.4
	 */
	 
    global $db; 
	
	$MIN_FAVOR_SCORE = 7;
	
	$pResult = "";
	$year = "";
	$half = "";
	$sTotalResults = [];
	$sFavorResults = [];
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$year = $_POST["year"];
		$isHalf = $_POST["half"];
		$half = ($isHalf === "true") ? "Semi-Annual" : "Annual";
		$pWhere = "(date_part('year', participantclassattendance.date) = $year ";
		if ($isHalf === "true") {
			$pWhere .= "AND date_part('month', participantclassattendance.date) < 6 "; 
		}
		$pWhere .= "AND participantclassattendance.isnew = TRUE)";
		$pQuery = "SELECT COUNT(DISTINCT(participantclassattendance.participantid)) FROM participantclassattendance WHERE $pWhere";
		$pResult = pg_fetch_result($db->query($pQuery, []), 0, 0);
		
		$pIDQuery = "SELECT DISTINCT(participantclassattendance.participantid) FROM participantclassattendance WHERE $pWhere";
		$formIDQuery = "SELECT DISTINCT(formid) FROM participantsformdetails WHERE participantid IN ($pIDQuery)";
		
		$sBaseQuery = "SELECT COUNT(prestopicdiscussedscore) as topic, COUNT(preschildperspectivescore) as perspective,
					COUNT(presotherparentsscore) as otherparents, COUNT(practiceinfoscore) as practice
					FROM surveys ";
		$sInFavor = " prestopicdiscussedscore > $MIN_FAVOR_SCORE AND preschildperspectivescore > $MIN_FAVOR_SCORE AND presotherparentsscore > $MIN_FAVOR_SCORE AND practiceinfoscore > $MIN_FAVOR_SCORE ";
		$sTotalQuery = $sBaseQuery . "WHERE surveyid IN ($formIDQuery) ";
		$sFavorQuery = $sTotalQuery . "AND ($sInFavor)";
					
		$sTotalResults = pg_fetch_all($db->query($sTotalQuery, []))[0];
		$sFavorResults = pg_fetch_all($db->query($sFavorQuery, []))[0];
	}
	
	include('header.php');
?>
<div class="container">
	<div class="container pt-5">
		<form action="" method="POST" autocomplete="on">
			<div class="row" style="margin-bottom: 1%">
				<div class="col">
					<div class="form-group">
						<select class="form-control" name="half" id="half">
                     <option value="true">Semi-Annual</option>
                     <option value="false">Annual</option>
                  </select>
					</div>
				</div>
				<div class="col">
					<div class="form-group">
						<select class="form-control" name="year" id="year">
				  <!-- Javascript below adds the options based on current year -->
                  </select>
					</div>
				</div>
			</div>
			<div class="row pb-3">
				<div class="col"></div>
				<div class="col-centered">
					<button type="submit" class="btn cpca">Generate Report</button>
				</div>
				<div class="col"></div>
			</div>
		</form>
	</div>
	<div class="container" <?php if ($_SERVER['REQUEST_METHOD'] !== 'POST') echo "style='display: none;'"?>>
		<div class="container py-3">
			<h1 class="text-center" id="date_display"><?=$half . " " . $year?></h1>
		</div>
		<div class="container py-3">
			<table class="table table-active">
				<thead>
				</thead>
				<tbody>
					<tr>
						<th scope="row">Total # of clients served (unduplicated):</td>
							<td><b><?=$pResult?></b></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="container pb-2">
			<h3 class="text-center">Survey Results</h3>
		</div>
		<div class="container">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Questions</th>
						<th>% in Favor</th>
						<th># in Favor</th>
						<th>Total Respondents</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td scope="row">Have an increased knowledge of the topics</td>
						<td>
							<?php if (count($sFavorResults) > 0) {
							if ($sTotalResults["topic"] > 0) 
								echo ($sFavorResults["topic"]/$sTotalResults["topic"])*100 . "%";
						}?>
						</td>
						<td>
							<?php if (count($sFavorResults) > 0) echo $sFavorResults["topic"];?>
						</td>
						<td>
							<?php if (count($sTotalResults) > 0) echo $sTotalResults["topic"];?>
						</td>
					</tr>
					<tr>
						<td scope="row">Plan on using specific techniques discussed in class</td>
						<td>
							<?php if (count($sFavorResults) > 0) {
							if ($sTotalResults["practice"] > 0) 
								echo ($sFavorResults["practice"]/$sTotalResults["practice"])*100 . "%";
						}?>
						</td>
						<td>
							<?php if (count($sFavorResults) > 0) echo $sFavorResults["practice"];?>
						</td>
						<td>
							<?php if (count($sTotalResults) > 0) echo $sTotalResults["practice"];?>
						</td>
					</tr>
					<tr>
						<td scope="row">Realized other parents share the same concerns</td>
						<td>
							<?php if (count($sFavorResults) > 0) {
							if ($sTotalResults["otherparents"] > 0) 
								echo ($sFavorResults["otherparents"]/$sTotalResults["otherparents"])*100 . "%";
						}?>
						</td>
						<td>
							<?php if (count($sFavorResults) > 0) echo $sFavorResults["otherparents"];?>
						</td>
						<td>
							<?php if (count($sTotalResults) > 0) echo $sTotalResults["otherparents"];?>
						</td>
					</tr>
					<tr>
						<td scope="row">Understand children have different perspectives than they do</td>
						<td>
							<?php if (count($sFavorResults) > 0) {
							if ($sTotalResults["perspective"] > 0) 
								echo ($sFavorResults["perspective"]/$sTotalResults["perspective"])*100 . "%";
						}?>
						</td>
						<td>
							<?php if (count($sFavorResults) > 0) echo $sFavorResults["perspective"];?>
						</td>
						<td>
							<?php if (count($sTotalResults) > 0) echo $sTotalResults["perspective"];?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	const NUM_YEARS_BACK = 4;

	window.onload = initPage;

	function initPage() {
		var d = new Date();
		var halfElem = document.getElementById("half");
		var yearElem = document.getElementById("year");
		var year = d.getFullYear();
		for (i = 0; i <= NUM_YEARS_BACK; i++) {
			var opt = document.createElement('option');
			opt.value = year - i;
			opt.innerHTML = year - i;
			yearElem.appendChild(opt);
		}
		display = document.getElementById("date_display").innerHTML;
		if (display === "") {
			var month = d.getMonth();
			halfElem.selectedIndex = (month < 6) ? 0 : 1;
		} else {
			halfElem.selectedIndex = (display.toLowerCase().includes("semi")) ? 0 : 1;
			var y = parseInt(display.substr(display.length - 4));
			yearElem.selectedIndex = year - y;
		}
	}
</script>
</script>
<style>
@media print{
	.btn {
		display: none;
	}
	.form-group {
		display: none;
	}
}
</style>
<?php include('footer.php'); ?>