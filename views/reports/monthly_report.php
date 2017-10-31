<?php 
	/**
	 * PEP Capping 2017 Algozzine's Class
	 *
	 * Allows users to view Monthly report figures.
	 *
	 * Displays all information required in the monthly
	 * reports, formatted into a series of tables.
	 * Users can select the month and year from the dropdowns
	 * at the top, then hit the "Generate Report" to populate
	 * the table with values from the database.
	 *
	 * @author Daniel Ahl
	 *		   Rafael Mormol
	 * @copyright 2017 Marist College
	 * @version 0.3
	 * @since 0.1.4
	 */
	 
    global $db;

	$month = "";
	$year = "";	
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$year = $_POST["year"];
		$month = $_POST["month"];
		$monthWhere = "(date_part('month', date) = $month)";
		$yearWhere = "(date_part('year', date) = $year)";
		$pNewWhere = "(isnew = TRUE)";
		
		#Total Participant Queries
		$pBaseQuery = "SELECT COUNT(DISTINCT(participantid)) FROM classattendancedetails ";
		
		$pMonthQuery = $pBaseQuery . "WHERE " . $monthWhere . " AND " . $yearWhere;
		$pMonthRes = pg_fetch_result($db->query($pMonthQuery ,[]), 0, 0);
		$pNewQuery = $pBaseQuery . "WHERE " . $monthWhere . " AND " . $yearWhere . " AND " . $pNewWhere;
		$pNewRes = pg_fetch_result($db->query($pNewQuery ,[]), 0, 0);
		$pDupRes = $pMonthRes - $pNewRes;
		$pYearQuery = $pBaseQuery . "WHERE " . $yearWhere;
		$pYearRes = pg_fetch_result($db->query($pYearQuery ,[]), 0, 0);
		
		$queryBuild = function($where, &$monthRes, &$newRes, &$dupRes, &$yearRes)
				use ($db, $pMonthQuery, $pNewQuery, $pYearQuery) {
			$query = $pMonthQuery . " AND $where ";
			$monthRes = pg_fetch_result($db->query($query ,[]), 0, 0);
			$query = $pNewQuery . " AND $where ";
			$newRes = pg_fetch_result($db->query($query ,[]), 0, 0);
			$dupRes = $monthRes - $newRes;
			$query = $pYearQuery . " AND $where ";
			$yearRes = pg_fetch_result($db->query($query ,[]), 0, 0);
		};
		
		#Gender Where clauses
		$femaleWhere = "(sex = 'Female')";
		$maleWhere = "(sex = 'Male')";
		
		#Gender Queries
		$queryBuild($maleWhere, $mMonthRes, $mNewRes, $mDupRes, $mYearRes);
		$queryBuild($femaleWhere, $fMonthRes, $fNewRes, $fDupRes, $fYearRes);
		
		#Age Where clauses
		$_20Where = "(date_part('year', AGE(dateofbirth)) >= 20)
				AND (date_part('year', AGE(dateofbirth)) <= 40)";
		$_41Where = "(date_part('year', AGE(dateofbirth)) >= 41)
				AND (date_part('year', AGE(dateofbirth)) <= 64)";
		$_65Where = "(date_part('year', AGE(dateofbirth)) >= 65)";
		
		#Age Queries
		$queryBuild($_20Where, $_20MonthRes, $_20NewRes, $_20DupRes, $_20YearRes);
		$queryBuild($_41Where, $_41MonthRes, $_41NewRes, $_41DupRes, $_41YearRes);
		$queryBuild($_65Where, $_65MonthRes, $_65NewRes, $_65DupRes, $_65YearRes);
		
		#Ethnicity Where clauses
		$afAmWhere = " race = 'African American' ";
		$natAmWhere = " race = 'Native American' ";
		$pacIslWhere = " race = 'Pacific Islander' ";
		$caucWhere = " race = 'Caucasian' ";
		$multRacWhere = " race = 'Multi Racial' ";
		$latWhere = " race = 'Latino' ";
		$otherRacWhere = " race = 'Other' ";
		
		#Ethnicity Queries
		$queryBuild($afAmWhere, $afAmMonthRes, $afAmNewRes, $afAmDupRes, $afAmYearRes);
		$queryBuild($natAmWhere, $natAmMonthRes,$natAmNewRes, $natAmDupRes, $natAmYearRes);
		$queryBuild($pacIslWhere, $pacIslMonthRes,$pacIslNewRes, $pacIslDupRes, $pacIslYearRes);
		$queryBuild($caucWhere, $caucMonthRes,$caucNewRes, $caucDupRes, $caucYearRes);
		$queryBuild($multRacWhere, $multRacMonthRes,$multRacNewRes, $multRacDupRes, $multRacYearRes);
		$queryBuild($latWhere, $latMonthRes,$latNewRes, $latDupRes, $latYearRes);
		$queryBuild($otherRacWhere, $otherRacMonthRes,$otherRacNewRes, $otherRacDupRes, $otherRacYearRes);
		
		#Zip Codes
		$queryBuild(" zipcode = 12501 ", $_12501MonthRes, $_12501NewRes, $_12501DupRes, $_12501YearRes);
		$queryBuild(" zipcode = 12504 ", $_12504MonthRes, $_12504NewRes, $_12504DupRes, $_12504YearRes);
		$queryBuild(" zipcode = 12506 ", $_12506MonthRes, $_12506NewRes, $_12506DupRes, $_12506YearRes);
		$queryBuild(" zipcode = 12507 ", $_12507MonthRes, $_12507NewRes, $_12507DupRes, $_12507YearRes);
		$queryBuild(" zipcode = 12508 ", $_12508MonthRes, $_12508NewRes, $_12508DupRes, $_12508YearRes);
		$queryBuild(" zipcode = 12514 ", $_12514MonthRes, $_12514NewRes, $_12514DupRes, $_12514YearRes);
		$queryBuild(" zipcode = 12522 ", $_12522MonthRes, $_12522NewRes, $_12522DupRes, $_12522YearRes);
		$queryBuild(" zipcode = 12524 ", $_12524MonthRes, $_12524NewRes, $_12524DupRes, $_12524YearRes);
		$queryBuild(" zipcode = 12531 ", $_12531MonthRes, $_12531NewRes, $_12531DupRes, $_12531YearRes);
		$queryBuild(" zipcode = 12533 ", $_12533MonthRes, $_12533NewRes, $_12533DupRes, $_12533YearRes);
		$queryBuild(" zipcode = 12537 ", $_12537MonthRes, $_12537NewRes, $_12537DupRes, $_12537YearRes);
		$queryBuild(" zipcode = 12538 ", $_12538MonthRes, $_12538NewRes, $_12538DupRes, $_12538YearRes);
		$queryBuild(" zipcode = 12540 ", $_12540MonthRes, $_12540NewRes, $_12540DupRes, $_12540YearRes);
		$queryBuild(" zipcode = 12545 ", $_12545MonthRes, $_12545NewRes, $_12545DupRes, $_12545YearRes);
		$queryBuild(" zipcode = 12546 ", $_12546MonthRes, $_12546NewRes, $_12546DupRes, $_12546YearRes);
		$queryBuild(" zipcode = 12564 ", $_12564MonthRes, $_12564NewRes, $_12564DupRes, $_12564YearRes);
		$queryBuild(" zipcode = 12567 ", $_12567MonthRes, $_12567NewRes, $_12567DupRes, $_12567YearRes);
		$queryBuild(" zipcode = 12569 ", $_12569MonthRes, $_12569NewRes, $_12569DupRes, $_12569YearRes);
		$queryBuild(" zipcode = 12570 ", $_12570MonthRes, $_12570NewRes, $_12570DupRes, $_12570YearRes);
		$queryBuild(" zipcode = 12571 ", $_12571MonthRes, $_12571NewRes, $_12571DupRes, $_12571YearRes);
		$queryBuild(" zipcode = 12572 ", $_12572MonthRes, $_12572NewRes, $_12572DupRes, $_12572YearRes);
		$queryBuild(" zipcode = 12574 ", $_12574MonthRes, $_12574NewRes, $_12574DupRes, $_12574YearRes);
		$queryBuild(" zipcode = 12578 ", $_12578MonthRes, $_12578NewRes, $_12578DupRes, $_12578YearRes);
		$queryBuild(" zipcode = 12580 ", $_12580MonthRes, $_12580NewRes, $_12580DupRes, $_12580YearRes);
		$queryBuild(" zipcode = 12581 ", $_12581MonthRes, $_12581NewRes, $_12581DupRes, $_12581YearRes);
		$queryBuild(" zipcode = 12582 ", $_12582MonthRes, $_12582NewRes, $_12582DupRes, $_12582YearRes);
		$queryBuild(" zipcode = 12583 ", $_12583MonthRes, $_12583NewRes, $_12583DupRes, $_12583YearRes);
		$queryBuild(" zipcode = 12585 ", $_12585MonthRes, $_12585NewRes, $_12585DupRes, $_12585YearRes);
		$queryBuild(" zipcode = 12590 ", $_12590MonthRes, $_12590NewRes, $_12590DupRes, $_12590YearRes);
		$queryBuild(" zipcode = 12592 ", $_12592MonthRes, $_12592NewRes, $_12592DupRes, $_12592YearRes);
		$queryBuild(" zipcode = 12594 ", $_12594MonthRes, $_12594NewRes, $_12594DupRes, $_12594YearRes);
		$queryBuild(" zipcode = 12601 ", $_12601MonthRes, $_12601NewRes, $_12601DupRes, $_12601YearRes);
		$queryBuild(" zipcode = 12602 ", $_12602MonthRes, $_12602NewRes, $_12602DupRes, $_12602YearRes);
		$queryBuild(" zipcode = 12603 ", $_12603MonthRes, $_12603NewRes, $_12603DupRes, $_12603YearRes);
		$queryBuild(" zipcode = 12604 ", $_12604MonthRes, $_12604NewRes, $_12604DupRes, $_12604YearRes);
		
		$otherZipWhere = "(zipcode NOT IN (12501,12504,12506,12507,12508,12514,12522,12524,12531,12533,12537,
							12538,12540,12545,12546,12564,12567,12569,12570,12571,12572,12574,12578,
							12580,12581,12582,12583,12585,12590,12592,12594,12601,12602,12603,12604))";
							
		$queryBuild($otherZipWhere, $otherZipMonthRes, $otherZipNewRes, $otherZipDupRes, $otherZipYearRes);
		
		#Children Served Indirectly
		$baseQuery = "SELECT SUM(numchildren) FROM classattendancedetails
						WHERE participantid IN (
							SELECT DISTINCT(participantid) FROM classattendancedetails
							WHERE $yearWhere ";
		$query = $baseQuery . " AND $monthWhere); ";
		$numChildMonthRes = pg_fetch_result($db->query($query ,[]), 0, 0);
		$query = $baseQuery . ");";
		$numChildYearRes = pg_fetch_result($db->query($query ,[]), 0, 0);
		$query = $baseQuery . " AND $monthWhere AND $pNewWhere); ";
		$numChildNewRes = pg_fetch_result($db->query($query ,[]), 0, 0);
		$numChildDupRes = $numChildMonthRes - $numChildNewRes;
		
		#Num of Classes
		$baseQuery = "SELECT COUNT(DISTINCT(date ::DATE)) FROM classattendancedetails ";
		$query = $baseQuery . "WHERE $monthWhere AND $yearWhere;";
		$numClassMonthRes = pg_fetch_result($db->query($query ,[]), 0, 0);
		$query = $baseQuery . "WHERE $yearWhere;";
		$numClassYearRes = pg_fetch_result($db->query($query ,[]), 0, 0);
	}
	
	include('header.php'); 
?>
<div class="container">
	<div class="container pt-5">
		<form action="" method="POST" autocomplete="on">
			<div class="row" style="margin-bottom: 1%">
				<div class="col">
					<div class="form-group">
						<select class="form-control" name="month" id="month">
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
		<div class="page-header">
			<div align="center">
				<h2 id="date_display"><?php
					if ($month !== "") {
						echo cal_info(0)['months'][$month] . " " . $year;
					}?></h2>
			</div>
		</div>
		<br />
		<h5 class="text-center">
			Number Served
		</h5>
		<br />
		<table class="table table-hover">
			<thead>
				<tr>
					<th>
						Current Month
					</th>
					<th>
						Newly Served
					</th>
					<th>
						Duplicate Served
					</th>
					<th>
						YTD
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<?php if (isset($pMonthRes)) echo $pMonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($pNewRes)) echo $pNewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($pDupRes)) echo $pDupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($pYearRes)) echo $pYearRes; else echo "";?>
					</td>
				</tr>
			</tbody>
		</table>
		<br />
		<h5 class="text-center">
			Demographics of Adults
		</h5>
		<br />
		<h6 class="text-center">
			Gender
		</h6>
		<table class="table table-hover">
			<thead>
				<tr>
					<th>
					</th>
					<th>
						Current Month
					</th>
					<th>
						Newly Served
					</th>
					<th>
						Duplicate Served
					</th>
					<th>
						YTD
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<b>
				   Male
				   </b>
					</td>
					<td>
						<?php if (isset($mMonthRes)) echo $mMonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($mNewRes)) echo $mNewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($mDupRes)) echo $mDupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($mYearRes)) echo $mYearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   Female
				   </b>
					</td>
					<td>
						<?php if (isset($fMonthRes)) echo $fMonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($fNewRes)) echo $fNewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($fDupRes)) echo $fDupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($fYearRes)) echo $fYearRes; else echo "";?>
					</td>
				</tr>
			</tbody>
		</table>
		<br />
		<h6 class="text-center">
			Age
		</h6>
		<table class="table table-hover">
			<thead>
				<tr>
					<th>
					</th>
					<th>
						Current Month
					</th>
					<th>
						Newly Served
					</th>
					<th>
						Duplicate Served
					</th>
					<th>
						YTD
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<b>
				   20-40
				   </b>
					</td>
					<td>
						<?php if (isset($_20MonthRes)) echo $_20MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_20NewRes)) echo $_20NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_20DupRes)) echo $_20DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_20YearRes)) echo $_20YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   41-64
				   </b>
					</td>
					<td>
						<?php if (isset($_41MonthRes)) echo $_41MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_41NewRes)) echo $_41NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_41DupRes)) echo $_41DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_41YearRes)) echo $_41YearRes; else echo "";?>
					</td>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   65+
				   </b>
					</td>
					<td>
						<?php if (isset($_65MonthRes)) echo $_65MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_65NewRes)) echo $_65NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_65DupRes)) echo $_65DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_65YearRes)) echo $_65YearRes; else echo "";?>
					</td>
				</tr>
			</tbody>
		</table>
		<br />
		<h6 class="text-center">
			Ethnicity
		</h6>
		<table class="table table-hover">
			<thead>
				<tr>
					<th>
					</th>
					<th>
						Current Month
					</th>
					<th>
						Newly Served
					</th>
					<th>
						Duplicate Served
					</th>
					<th>
						YTD
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<b>
				   Caucasian
				   </b>
					</td>
					<td>
						<?php if (isset($caucMonthRes)) echo $caucMonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($caucNewRes)) echo $caucNewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($caucDupRes)) echo $caucDupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($caucYearRes)) echo $caucYearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   African American
				   </b>
					</td>
					<td>
						<?php if (isset($afAmMonthRes)) echo $afAmMonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($afAmNewRes)) echo $afAmNewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($afAmDupRes)) echo $afAmDupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($afAmYearRes)) echo $afAmYearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   Multi Racial
				   </b>
					</td>
					<td>
						<?php if (isset($multRacMonthRes)) echo $multRacMonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($multRacNewRes)) echo $multRacNewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($multRacDupRes)) echo $multRacDupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($multRacYearRes)) echo $multRacYearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   Latino
				   </b>
					</td>
					<td>
						<?php if (isset($latMonthRes)) echo $latMonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($latNewRes)) echo $latNewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($latDupRes)) echo $latDupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($latYearRes)) echo $latYearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   Pacific Islander
				   </b>
					</td>
					<td>
						<?php if (isset($pacIslMonthRes)) echo $pacIslMonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($pacIslNewRes)) echo $pacIslNewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($pacIslDupRes)) echo $pacIslDupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($pacIslYearRes)) echo $pacIslYearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   Native American
				   </b>
					</td>
					<td>
						<?php if (isset($natAmMonthRes)) echo $natAmMonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($natAmNewRes)) echo $natAmNewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($natAmDupRes)) echo $natAmDupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($natAmYearRes)) echo $natAmYearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   Other
				   </b>
					</td>
					<td>
						<?php if (isset($otherRacMonthRes)) echo $otherRacMonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($otherRacNewRes)) echo $otherRacNewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($otherRacDupRes)) echo $otherRacDupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($otherRacYearRes)) echo $otherRacYearRes; else echo "";?>
					</td>
				</tr>
			</tbody>
		</table>
		<br />
		<h5 class="text-center">
			Zip Code Data
		</h5>
		<h6 class="text-center">
			For use by PSP, ISP, TLC, & CAC only
		</h6>
		<table class="table table-hover">
			<thead>
				<tr>
					<th>
						Zip
					</th>
					<th>
						Town
					</th>
					<th>
						Current Month
					</th>
					<th>
						Newly Served
					</th>
					<th>
						Duplicate Served
					</th>
					<th>
						YTD
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<b>
				   12501 
				   </b>
					</td>
					<td>
						<b>
				   Amenia
				   </b>
					</td>
					<td>
						<?php if (isset($_12501MonthRes)) echo $_12501MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12501NewRes)) echo $_12501NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12501DupRes)) echo $_12501DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12501YearRes)) echo $_12501YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12504
				   </b>
					</td>
					<td>
						<b>
				   Annandale
				   </b>
					</td>
					<td>
						<?php if (isset($_12504MonthRes)) echo $_12504MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12504NewRes)) echo $_12504NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12504DupRes)) echo $_12504DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12504YearRes)) echo $_12504YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12506
				   </b>
					</td>
					<td>
						<b>
				   Bangall
				   </b>
					</td>
					<td>
						<?php if (isset($_12506MonthRes)) echo $_12506MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12506NewRes)) echo $_12506NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12506DupRes)) echo $_12506DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12506YearRes)) echo $_12506YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12507
				   </b>
					</td>
					<td>
						<b>
				   Barrytown
				   </b>
					</td>
					<td>
						<?php if (isset($_12507MonthRes)) echo $_12507MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12507NewRes)) echo $_12507NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12507DupRes)) echo $_12507DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12507YearRes)) echo $_12507YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12508
				   </b>
					</td>
					<td>
						<b>
				   Beacon
				   </b>
					</td>
					<td>
						<?php if (isset($_12508MonthRes)) echo $_12508MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12508NewRes)) echo $_12508NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12508DupRes)) echo $_12508DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12508YearRes)) echo $_12508YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12514
				   </b>
					</td>
					<td>
						<b>
				   Clinton Corners
				   </b>
					</td>
					<td>
						<?php if (isset($_12514MonthRes)) echo $_12514MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12514NewRes)) echo $_12514NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12514DupRes)) echo $_12514DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12514YearRes)) echo $_12514YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12522
				   </b>
					</td>
					<td>
						<b>
				   Dover Plains
				   </b>
					</td>
					<td>
						<?php if (isset($_12522MonthRes)) echo $_12522MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12522NewRes)) echo $_12522NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12522DupRes)) echo $_12522DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12522YearRes)) echo $_12522YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12524
				   </b>
					</td>
					<td>
						<b>
				   Fishkill
				   </b>
					</td>
					<td>
						<?php if (isset($_12524MonthRes)) echo $_12524MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12524NewRes)) echo $_12524NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12524DupRes)) echo $_12524DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12524YearRes)) echo $_12524YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12531
				   </b>
					</td>
					<td>
						<b>
				   Holmes
				   </b>
					</td>
					<td>
						<?php if (isset($_12531MonthRes)) echo $_12531MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12531NewRes)) echo $_12531NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12531DupRes)) echo $_12531DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12531YearRes)) echo $_12531YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12533
				   </b>
					</td>
					<td>
						<b>
				   Hopewell Junction
				   </b>
					</td>
					<td>
						<?php if (isset($_12533MonthRes)) echo $_12533MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12533NewRes)) echo $_12533NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12533DupRes)) echo $_12533DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12533YearRes)) echo $_12533YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12537
				   </b>
					</td>
					<td>
						<b>
				   Hughsonville
				   </b>
					</td>
					<td>
						<?php if (isset($_12537MonthRes)) echo $_12537MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12537NewRes)) echo $_12537NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12537DupRes)) echo $_12537DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12537YearRes)) echo $_12537YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12538
				   </b>
					</td>
					<td>
						<b>
				   Hyde Park
				   </b>
					</td>
					<td>
						<?php if (isset($_12538MonthRes)) echo $_12538MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12538NewRes)) echo $_12538NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12538DupRes)) echo $_12538DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12538YearRes)) echo $_12538YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12540
				   </b>
					</td>
					<td>
						<b>
				   LaGrangeville
				   </b>
					</td>
					<td>
						<?php if (isset($_12540MonthRes)) echo $_12540MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12540NewRes)) echo $_12540NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12540DupRes)) echo $_12540DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12540YearRes)) echo $_12540YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12545
				   </b>
					</td>
					<td>
						<b>
				   Millbrook
				   </b>
					</td>
					<td>
						<?php if (isset($_12545MonthRes)) echo $_12545MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12545NewRes)) echo $_12545NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12545DupRes)) echo $_12545DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12545YearRes)) echo $_12545YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12546
				   </b>
					</td>
					<td>
						<b>
				   Millerton
				   </b>
					</td>
					<td>
						<?php if (isset($_12546MonthRes)) echo $_12546MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12546NewRes)) echo $_12546NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12546DupRes)) echo $_12546DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12546YearRes)) echo $_12546YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12564
				   </b>
					</td>
					<td>
						<b>
				   Pawling
				   </b>
					</td>
					<td>
						<?php if (isset($_12564MonthRes)) echo $_12564MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12564NewRes)) echo $_12564NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12564DupRes)) echo $_12564DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12564YearRes)) echo $_12564YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12567
				   </b>
					</td>
					<td>
						<b>
				   Pine Plains
				   </b>
					</td>
					<td>
						<?php if (isset($_12567MonthRes)) echo $_12567MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12567NewRes)) echo $_12567NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12567DupRes)) echo $_12567DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12567YearRes)) echo $_12567YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12569
				   </b>
					</td>
					<td>
						<b>
				   Pleasant Valley
				   </b>
					</td>
					<td>
						<?php if (isset($_12569MonthRes)) echo $_12569MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12569NewRes)) echo $_12569NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12569DupRes)) echo $_12569DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12569YearRes)) echo $_12569YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12570
				   </b>
					</td>
					<td>
						<b>
				   Poughquag
				   </b>
					</td>
					<td>
						<?php if (isset($_12570MonthRes)) echo $_12570MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12570NewRes)) echo $_12570NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12570DupRes)) echo $_12570DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12570YearRes)) echo $_12570YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12571
				   </b>
					</td>
					<td>
						<b>
				   Red Hook
				   </b>
					</td>
					<td>
						<?php if (isset($_12571MonthRes)) echo $_12571MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12571NewRes)) echo $_12571NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12571DupRes)) echo $_12571DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12571YearRes)) echo $_12571YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12572
				   </b>
					</td>
					<td>
						<b>
				   Rhinebeck
				   </b>
					</td>
					<td>
						<?php if (isset($_12572MonthRes)) echo $_12572MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12572NewRes)) echo $_12572NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12572DupRes)) echo $_12572DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12572YearRes)) echo $_12572YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12574
				   </b>
					</td>
					<td>
						<b>
				   Rhinecliff
				   </b>
					</td>
					<td>
						<?php if (isset($_12574MonthRes)) echo $_12574MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12574NewRes)) echo $_12574NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12574DupRes)) echo $_12574DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12574YearRes)) echo $_12574YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12578
				   </b>
					</td>
					<td>
						<b>
				   Salt Point
				   </b>
					</td>
					<td>
						<?php if (isset($_12578MonthRes)) echo $_12578MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12578NewRes)) echo $_12578NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12578DupRes)) echo $_12578DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12578YearRes)) echo $_12578YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12580
				   </b>
					</td>
					<td>
						<b>
				   Staatsburg
				   </b>
					</td>
					<td>
						<?php if (isset($_12580MonthRes)) echo $_12580MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12580NewRes)) echo $_12580NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12580DupRes)) echo $_12580DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12580YearRes)) echo $_12580YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12581
				   </b>
					</td>
					<td>
						<b>
				   Standfordville
				   </b>
					</td>
					<td>
						<?php if (isset($_12581MonthRes)) echo $_12581MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12581NewRes)) echo $_12581NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12581DupRes)) echo $_12581DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12581YearRes)) echo $_12581YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12582
				   </b>
					</td>
					<td>
						<b>
				   Stormville
				   </b>
					</td>
					<td>
						<?php if (isset($_12582MonthRes)) echo $_12582MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12582NewRes)) echo $_12582NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12582DupRes)) echo $_12582DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12582YearRes)) echo $_12582YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12583
				   </b>
					</td>
					<td>
						<b>
				   Tivoli
				   </b>
					</td>
					<td>
						<?php if (isset($_12583MonthRes)) echo $_12583MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12583NewRes)) echo $_12583NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12583DupRes)) echo $_12583DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12583YearRes)) echo $_12583YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12585
				   </b>
					</td>
					<td>
						<b>
				   Verbank
				   </b>
					</td>
					<td>
						<?php if (isset($_12585MonthRes)) echo $_12585MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12585NewRes)) echo $_12585NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12585DupRes)) echo $_12585DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12585YearRes)) echo $_12585YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12590
				   </b>
					</td>
					<td>
						<b>
				   Wappingers Falls
				   </b>
					</td>
					<td>
						<?php if (isset($_12590MonthRes)) echo $_12590MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12590NewRes)) echo $_12590NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12590DupRes)) echo $_12590DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12590YearRes)) echo $_12590YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12592
				   </b>
					</td>
					<td>
						<b>
				   Wassaic
				   </b>
					</td>
					<td>
						<?php if (isset($_12592MonthRes)) echo $_12592MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12592NewRes)) echo $_12592NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12592DupRes)) echo $_12592DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12592YearRes)) echo $_12592YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12594
				   </b>
					</td>
					<td>
						<b>
				   Wingdale
				   </b>
					</td>
					<td>
						<?php if (isset($_12594MonthRes)) echo $_12594MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12594NewRes)) echo $_12594NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12594DupRes)) echo $_12594DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12594YearRes)) echo $_12594YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12601
				   </b>
					</td>
					<td>
						<b>
				   City of Pok.
				   </b>
					</td>
					<td>
						<?php if (isset($_12601MonthRes)) echo $_12601MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12601NewRes)) echo $_12601NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12601DupRes)) echo $_12601DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12601YearRes)) echo $_12601YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12602
				   </b>
					</td>
					<td>
						<b>
				   Pok. P.O. Boxes
				   </b>
					</td>
					<td>
						<?php if (isset($_12602MonthRes)) echo $_12602MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12602NewRes)) echo $_12602NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12602DupRes)) echo $_12602DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12602YearRes)) echo $_12602YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12603
				   </b>
					</td>
					<td>
						<b>
				   Town of Pok.
				   </b>
					</td>
					<td>
						<?php if (isset($_12603MonthRes)) echo $_12603MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12603NewRes)) echo $_12603NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12603DupRes)) echo $_12603DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12603YearRes)) echo $_12603YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   12604
				   </b>
					</td>
					<td>
						<b>
				   Town of Pok.
				   </b>
					</td>
					<td>
						<?php if (isset($_12604MonthRes)) echo $_12604MonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12604NewRes)) echo $_12604NewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12604DupRes)) echo $_12604DupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($_12604YearRes)) echo $_12604YearRes; else echo "";?>
					</td>
				</tr>
				<tr>
					<td>
						<b>
				   </b>
					</td>
					<td>
						<b>
				   Other
				   </b>
					</td>
					<td>
						<?php if (isset($otherZipMonthRes)) echo $otherZipMonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($otherZipNewRes)) echo $otherZipNewRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($otherZipDupRes)) echo $otherZipDupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($otherZipYearRes)) echo $otherZipYearRes; else echo "";?>
					</td>
				</tr>
			</tbody>
		</table>
		<h5 class="text-center pt-4 pb-2">
			Parenting Services
		</h5>
		<table class="table table-hover">
			<thead>
				<tr>
					<th>
					</th>
					<th>
						Current Month
					</th>
					<th>
						Newly Served
					</th>
					<th>
						Duplicate Served
					</th>
					<th>
						YTD
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<b>Children Served Indirectly</b>
					</td>
					<td>
						<?php if (isset($numChildMonthRes)) echo $numChildMonthRes; else echo "0";?>
					</td>
					<td>
						<?php if (isset($numChildNewRes)) echo $numChildNewRes; else echo "0";?>
					</td>
					<td>
						<?php if (isset($numChildDupRes)) echo $numChildDupRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($numChildYearRes)) echo $numChildYearRes; else echo "0";?>
					</td>
				</tr>
			</tbody>
		</table>
		<h6 class="text-center pt-3 pb-2">
			Services Provided
		</h6>
		<table class="table table-hover">
			<thead>
				<tr>
					<th>
					</th>
					<th>
						Current Month
					</th>
					<th>
						YTD
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<b># of Classes</b>
					</td>
					<td>
						<?php if (isset($numClassMonthRes)) echo $numClassMonthRes; else echo "";?>
					</td>
					<td>
						<?php if (isset($numClassYearRes)) echo $numClassYearRes; else echo "";?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<script>
	const NUM_YEARS_BACK = 4;

	window.onload = initPage;

	function initPage() {
		var d = new Date();
		var monthElem = document.getElementById("month");
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
			monthElem.selectedIndex = d.getMonth();
		} else {
			monthIndex = 0;
			if (display.toLowerCase().includes("jan")) {
				monthIndex = 0;
			} else if (display.toLowerCase().includes("feb")) {
				monthIndex = 1;
			} else if (display.toLowerCase().includes("mar")) {
				monthIndex = 2;
			} else if (display.toLowerCase().includes("apr")) {
				monthIndex = 3;
			} else if (display.toLowerCase().includes("may")) {
				monthIndex = 4;
			} else if (display.toLowerCase().includes("jun")) {
				monthIndex = 5;
			} else if (display.toLowerCase().includes("jul")) {
				monthIndex = 6;
			} else if (display.toLowerCase().includes("aug")) {
				monthIndex = 7;
			} else if (display.toLowerCase().includes("sept")) {
				monthIndex = 8;
			} else if (display.toLowerCase().includes("oct")) {
				monthIndex = 9;
			} else if (display.toLowerCase().includes("nov")) {
				monthIndex = 10;
			} else if (display.toLowerCase().includes("dec")) {
				monthIndex = 11;
			}
			monthElem.selectedIndex = monthIndex;
			
			var y = parseInt(display.substr(display.length - 4));
			yearElem.selectedIndex = year - y;
		}
	}
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