<?php
	/**
	 * PEP Capping 2017 Algozzine's Class
	 *
	 * Displays the results of a custom report.
	 *
	 * This page display the information requested in the form
	 * on custom_reports.php page, formatted into a single table.
	 *
	 * @author Daniel Ahl
	 *		   Rafael Mormol
	 * @copyright 2017 Marist College
	 * @version 0.3
	 * @since 0.1.4
	 */
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') header('Location: custom-reports');
	
	global $db;

	//Get POST data to query database
	$startDate = $_POST['startDate'];
	$endDate = $_POST['endDate'];
	$sDateFormatted = date_format(date_create($startDate),"m/d/Y");
	$eDateFormatted = date_format(date_create($endDate),"m/d/Y");
	
	$currs = isset($_POST['curricula']) ? $_POST['curricula'] : [];
	$races = isset($_POST['race']) ? $_POST['race'] : [];
	$minAge = $_POST['minAge'];
	$maxAge = $_POST['maxAge'];
	
	//Build queries based on POST data
	$dateQuery = "(participantclassattendance.date >= '$startDate' AND participantclassattendance.date <= '$endDate')";
	$currQuery = "";
	
	if (count($currs) > 0) {
		$currQuery = "(participantclassattendance.curriculumid = '" . pg_escape_string($currs[0]) . "' ";
		for ($i = 1; $i < count($currs); $i++) {
			$currQuery .= "OR participantclassattendance.curriculumid = '" . pg_escape_string($currs[$i]) . "' ";
		}
		$currQuery .= ")";
	}
	
	$raceQuery = "";
	if (count($races) > 0) {
		$raceQuery = "(participants.race = '" . pg_escape_string($races[0]) . "' ";
		for ($i = 1; $i < count($races); $i++) {
			$raceQuery .= "OR participants.race = '" . pg_escape_string($races[$i]) . "' ";
		}
		$raceQuery .= ")";
	}
	$ageQuery = "";
	if ($minAge !== 'any') {
		$ageQuery = "((date_part('year', AGE(participants.dateOfBirth)) >= $minAge) ";
	}
	if ($maxAge !== 'any') {
		if ($minAge === 'any') {
			$ageQuery = "(";
		} else {
			$ageQuery .= "AND ";
		}
		$ageQuery .= "(date_part('year', AGE(participants.dateOfBirth)) <= $maxAge))";
	} else {
		if ($ageQuery !== "") $ageQuery .= ")";
	}
	
	$totalWhere = "$dateQuery ";
	if ($currQuery !== "") $totalWhere .= "AND $currQuery ";
	if ($raceQuery !== "") $totalWhere .= "AND $raceQuery ";
	if ($ageQuery !== "") $totalWhere .= "AND $ageQuery ";
	$newWhere = $totalWhere . "AND participantclassattendance.isnew = TRUE;";
	$totalWhere .= ";";
	
	$baseQuery = "SELECT COUNT(DISTINCT(participants.participantid)) as Participants
				FROM participants INNER JOIN participantclassattendance
				ON participants.participantid = participantclassattendance.participantid
				WHERE ";
	
	//Actually query database and store results to be displayed below
	$totalRes = pg_fetch_result($db->query($baseQuery . $totalWhere, []), 0, 0);
	$newRes = pg_fetch_result($db->query($baseQuery . $newWhere, []), 0, 0);
	$duplRes = $totalRes - $newRes;
	
	//Get all the names of the curriculum selected, using their ids
	$currIdSet = "( ";
	if (count($currs) > 0) {
		$currIdSet .= $currs[0];
	}
	for ($i=1; $i<count($currs); $i++) {
		$currIdSet .= ", $currs[$i]";
	}
	$currIdSet .= ")";
	
	$query = "SELECT curriculumname FROM curricula WHERE curriculumid IN $currIdSet;";
	$currNames = pg_fetch_all($db->query($query, []));
	
	include('header.php');
?>
<div class="container">
	<div class="container" align="right">
		<button type="button" class="btn cpca" onclick="window.print()"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
	</div>
	<div class="container py-2">
		<div align="center">
			<h2><?php
				//Display the date range, unless it's a
				//single day, then just display that date.
				if ($sDateFormatted === $eDateFormatted) {
					echo $sDateFormatted;
				} else {
					echo $sDateFormatted . " - " . $eDateFormatted;
				}?></h2>
		</div>
		<div align="center">
			<?php
			#Display the chosen curriculum
			if ($currNames[0]["curriculumname"] !== NULL) {
				echo "<div><b>Curricula:</b> " . $currNames[0]["curriculumname"];
				for ($i = 1; $i < count($currNames); $i++) {
					echo ", " . $currNames[$i]["curriculumname"];
				}
				echo "</div>";
			}
			
			#Display the chosen races
			if (count($races) > 0) {
				echo "<div><b>Races:</b> " . $races[0];
				for ($i = 1; $i < count($races); $i++) {
					echo ", " . $races[$i];
				}
				echo "</div>";
			}
			
			#Display the chosen ages
			if ($minAge !== 'any' || $maxAge !=='any') {
				if ($minAge === $maxAge) {
					echo "<div><b>Age Range:</b> " . $maxAge . "</div>";
				} elseif ($minAge === 'any') {
					echo "<div><b>Age Range:</b> " . $maxAge . " and below</div>";
				} elseif ($maxAge === 'any') {
					echo "<div><b>Age Range:</b> " . $minAge . " and above</div>";
				} else {
					echo "<div><b>Age Range:</b> " . $minAge . " - " . $maxAge . "</div>";
				}
			}
			?>
		</div>
	</div>
	<div class="container py-2">
		<table class="table table-hover table-striped table-bordered">
			<thead>
				<tr>
					<th>
						Newly Served
					</th>
					<th>
						Duplicate Served
					</th>
					<th>
						Total Served
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<?=$newRes?>
					</td>
					<td>
						<?=$duplRes?>
					</td>
					<td>
						<?=$totalRes?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="container" align="center">
		<button class="btn cpca" onclick="window.history.back();">Run Another Report</button>
	</div>
</div>
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