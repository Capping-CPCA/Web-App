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
	$sexes = isset($_POST['sex']) ? $_POST['sex'] : [];
	$sites = isset($_POST['sites']) ? $_POST['sites'] : [];
	$minAge = $_POST['minAge'];
	$maxAge = $_POST['maxAge'];
	
	//Build queries based on POST data
	$dateQuery = "(classattendancedetails.date::date >= '$startDate' AND classattendancedetails.date::date <= '$endDate')";
	$currQuery = "";
	
	if (count($currs) > 0) {
		$currQuery = "(classattendancedetails.curriculumid = '" . pg_escape_string($currs[0]) . "' ";
		for ($i = 1; $i < count($currs); $i++) {
			$currQuery .= "OR classattendancedetails.curriculumid = '" . pg_escape_string($currs[$i]) . "' ";
		}
		$currQuery .= ")";
	}
	
	$raceQuery = "";
	if (count($races) > 0) {
		$raceQuery = "(classattendancedetails.race = '" . pg_escape_string($races[0]) . "' ";
		for ($i = 1; $i < count($races); $i++) {
			$raceQuery .= "OR classattendancedetails.race = '" . pg_escape_string($races[$i]) . "' ";
		}
		$raceQuery .= ")";
	}
	
	$sexQuery = "";
	if (count($sexes) > 0) {
		$sexQuery = "(classattendancedetails.sex = '" . pg_escape_string($sexes[0]) . "' ";
		for ($i = 1; $i < count($sexes); $i++) {
			$sexQuery .= "OR classattendancedetails.sex = '" . pg_escape_string($sexes[$i]) . "' ";
		}
		$sexQuery .= ")";
	}
	
	if (count($sites) > 0) {
		$siteQuery = "(classattendancedetails.sitename = '" . pg_escape_string($sites[0]) . "' ";
		for ($i = 1; $i < count($sites); $i++) {
			$siteQuery .= "OR classattendancedetails.sitename = '" . pg_escape_string($sites[$i]) . "' ";
		}
		$siteQuery .= ")";
	}
	
	if (count($races) > 0 && count($currs) > 0 && count($sexes) > 0 && count($sites) > 0) {
		$ageQuery = "";
		if ($minAge !== 'any') {
			$ageQuery = "((date_part('year', AGE(classattendancedetails.dateOfBirth)) >= $minAge) ";
		}
		if ($maxAge !== 'any') {
			if ($minAge === 'any') {
				$ageQuery = "(";
			} else {
				$ageQuery .= "AND ";
			}
			$ageQuery .= "(date_part('year', AGE(classattendancedetails.dateOfBirth)) <= $maxAge))";
		} else {
			if ($ageQuery !== "") $ageQuery .= ")";
		}
		
		$totalWhere = "$dateQuery ";
		if ($currQuery !== "") $totalWhere .= "AND $currQuery ";
		if ($raceQuery !== "") $totalWhere .= "AND $raceQuery ";
		if ($ageQuery !== "") $totalWhere .= "AND $ageQuery ";
		if ($sexQuery !== "") $totalWhere .= "AND $sexQuery ";
		if ($siteQuery !== "") $totalWhere .= "AND $siteQuery ";
		$newWhere = $totalWhere . "AND classattendancedetails.isnew = TRUE;";
		$totalWhere .= ";";
		
		$baseQuery = "SELECT COUNT(DISTINCT(classattendancedetails.participantid)) as participants
					FROM classattendancedetails
					WHERE ";
		
		
		//Actually query database and store results to be displayed below
		$totalRes = pg_fetch_result($db->query($baseQuery . $totalWhere, []), 0, 0);
		$newRes = pg_fetch_result($db->query($baseQuery . $newWhere, []), 0, 0);
		
	} else {
		$totalRes = 0;
		$newRes = 0;
	}
	
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
		<hr>
		<div align="left">
			<?php
			#Display the chosen locations
			echo "<p><b>Locations:</b> ";
			if (count($sites) > 0) {
				echo $sites[0];
				for ($i = 1; $i < count($sites); $i++) {
					echo ", " . $sites[$i];
				}
			} else {
				echo "None";
			}
			echo "</p>";
			
			#Display the chosen curriculum
			echo "<p><b>Curricula:</b> ";
			if ($currNames[0]["curriculumname"] !== NULL) {
				echo $currNames[0]["curriculumname"];
				for ($i = 1; $i < count($currNames); $i++) {
					echo ", " . $currNames[$i]["curriculumname"];
				}
			} else {
				echo "None";
			}
			echo "</p>";
			
			#Display the chosen races
			echo "<p><b>Races:</b> ";
			if (count($races) > 0) {
				echo $races[0];
				for ($i = 1; $i < count($races); $i++) {
					echo ", " . $races[$i];
				}
				echo "</p>";
			} else {
				echo "None</p>";
			}
			
			#Display the chosen sexes
			echo "<p><b>Sexes:</b> ";
			if (count($sexes) > 0) {
				echo $sexes[0];
				for ($i = 1; $i < count($sexes); $i++) {
					echo ", " . $sexes[$i];
				}
				echo "</p>";
			} else {
				echo "None</p>";
			}
			
			#Display the chosen ages
			echo "<div><b>Age Range:</b> ";
			if ($minAge !== 'any' || $maxAge !=='any') {
				if ($minAge === $maxAge) {
					echo  $maxAge;
				} elseif ($minAge === 'any') {
					echo $maxAge . " and below";
				} elseif ($maxAge === 'any') {
					echo $minAge . " and above";
				} else {
					echo $minAge . " - " . $maxAge;
				}
				echo "</div>";
			} else {
				echo "Any Age</div>";
			}
			?>
		</div>
	</div>
	<hr>
	<br>
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