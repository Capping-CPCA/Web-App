<?php
global $params, $route, $view;

# Select page to display
if (!empty($params) && $params[0] == 'results') {
    $view->display('surveys/results.php');
} else {
	include('header.php');
	global $db;
	$filter = "";
	
	?>
	<div style="width: 100%;">

		<!------------------------ This is the update button ------------------------->
		<?php
			function updateDB(){
				$surveyConfig = CONFIG['survey'];
				$output = shell_exec('py "../views/surveys/pullAndParseSurveys.py" '
					. $surveyConfig['database'] . ' ' . $surveyConfig['username'] . ' '
					. $surveyConfig['password'] . ' ' . $surveyConfig['address'] . ' '
					. $surveyConfig['googleEmail'] . ' ' . $surveyConfig['googlePassword']);
				echo("<script>alert('" . $output . "')</script>");
				?>
					<script>
						var now = new Date();
						localStorage.setItem("lastSave", now);
					</script>
				<?php
			}
			
			if (isset($_POST['updated'])) {
				updateDB();
			}
		?>
		
		<script>
			function validateForm() {
				var w = document.forms["userInput"]["classes"].value;
				var x = document.forms["userInput"]["Month"].value;
				var y = document.forms["userInput"]["Day"].value;
				var z = document.forms["userInput"]["Year"].value;
				if (w == "") {
					alert("Please select a class");
					return false;
				} else if (x == "") {
					alert("Please select a Month");
					return false;
				} else if (y == "") {
					alert("Please select a Day");
					return false;
				} else if (z == "") {
					alert("Please select a Year");
					return false;
				} else {
					return true;
				}
			}
		</script>

		<!--<form method="POST">
		<input name="updated" value="Update Surveys" type="Submit" class="btn btn-success" style="float: right;"/>
		</form>-->
		
		<div style="text-align: center;">
            <br />
            <br />
            <div class="container">
                <div class="row justify-content-md-center">
                    <div class="col-sm" style="max-width:75%">
                        <h4 style="color: #343A40;">Select class and filter by date for results</h4>
                    </div>
                </div>
            </div>
            <!------------------------ This is the class selector ------------------------->
            <div class="container" >
                <form method="POST" action="/surveys/results" onsubmit = "return validateForm()" name = "userInput">
                    <div class="row justify-content-md-center">
                        <div class="col-sm col-lg-6">
                            <div class="form-group">
                                <select class="form-control" id="classes" name="classes" style="color: #5C639A;">
                                    <option value="" disabled selected hidden>Class</option>
                                    <?php

                                    $query = $db->query("SELECT topicname, classid FROM classes ORDER BY topicname", []);
                                    while ($line = pg_fetch_assoc($query)) {
                                        $col_value = $line['topicname'];
                                        echo('<option value="' . htmlentities($col_value, ENT_COMPAT) . '">' . $col_value . '</option>');
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!------------------------ This is the date selector ------------------------->
                    <div class="row justify-content-md-center">
                        <div class="col-sm col-lg-2">
                            <div class="form-group">
                                <select class="form-control" name="Month" id="month" style="color: #5C639A;">
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
                                <select class="form-control" name="Day" id="day" style="color: #5C639A;">
                                    <option selected disabled="disabled" value="">Day</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
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
                                <select class="form-control" name="Year" id="year" style="color: #5C639A;">
                                <?php
                                    $starting_year = date('2000');
                                    $ending_year = date('Y', strtotime('+1 year'));
                                    $current_year = date('Y');
                                    for( ; $starting_year <= $ending_year; $starting_year++) {
                                        echo '<option value="'.$starting_year.'"';
                                        if( $starting_year ==  $current_year ) {
                                            echo ' selected="selected"';
                                        }
                                        echo ' >'.$starting_year.'</option>';
                                    }
                                 ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <input type="Submit" method = "POST" class="btn cpca" value="Search Surveys">
                </form>
            </div>

            <br />
            <a href="http://Mari.st/pep" target="_blank" style="color: #5C639A;">Link to Survey</a>
            <div class="container">
                <div class="row justify-content-md-center">
                    <div class="col-sm" style="max-width:75%; position: absolute; bottom: 5%;">
                        <h5><small style="color: #343A40;">Survey Database was last updated on:</small></h5>
                        <h6 style="font-size: 10px; color: darkGray;" id="result"></h6>
                        <script>
                            if (localStorage.lastSave != null){
                                document.getElementById("result").innerHTML = localStorage.lastSave;
                            } else {
                                document.getElementById("result").innerHTML = "never";
                            }
                        </script>
                        <form method="POST">
                            <input name="updated" value="Update Surveys" type="Submit" class="btn cpca"/>
                        </form>
                    </div>
                </div>
            </div>
		</div>
	</div>

	<?php
	include('footer.php');
}
?>
