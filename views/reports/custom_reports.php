<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Allows users to create a custom report.
 *
 * This page has a form that allows users to specify what
 * information they would like to see in a custom report.
 * The 'Generate Report' button will kick off a custom query
 * to the database and display the results in a new page
 * (defined in 'custom_reports_table.php')
 *
 * @author Rafael Mormol & Daniel Ahl
 * @copyright 2017 Marist College
 * @version 0.1.4
 * @since 0.1.4
 */
    authorizedPage();
    global $params, $route, $view;
    include('header.php');
    ?>
<div class="container" >
    <h3 align="center" style="margin-bottom: 5%">Custom Report</h3>
    <form style="margin-left: 30%">
        <fieldset>
            <!-- Select Basic -->
            <div class="form-group row">
                <label class="col-md-2 col-form-label" for="month"><b>Month</b></label>
                <div class="col-md-4">
                    <select id="month" name="month" class="form-control">
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
                        <option value="November">November</option>
                        <option value="December">December</option>
                    </select>
                </div>
            </div>
            <!-- Select Basic -->
            <div class="form-group row">
                <label class="col-md-2 col-form-label" for="year"><b>Year</b></label>
                <div class="col-md-4">
                    <select id="year" name="year" class="form-control">
                        <option value="January">2017</option>
                        <option value="February">2016</option>
                        <option value="March">2015</option>
                        <option value="April">2014</option>
                        <option value="May">2013</option>
                        <option value="June">2012</option>
                        <option value="July">2011</option>
                    </select>
                </div>
            </div>
            <!-- Multiple Checkboxes -->
            <div class="form-group row">
                <label class="col-md-2 col-form-label" for="location"><b>Location</b></label>
                <div class="col-md-4">
                    <div class="checkbox">
                        <label for="location-0">
                        <input type="checkbox" name="location" id="location-0" value="Cornerstone">
                        Cornerstone
                        </label>
                    </div>
                    <div class="checkbox">
                        <label for="location-1">
                        <input type="checkbox" name="location" id="location-1" value="Dutches County Jail">
                        Dutches County Jail
                        </label>
                    </div>
                    <div class="checkbox">
                        <label for="location-2">
                        <input type="checkbox" name="location" id="location-2" value="Florence Manor">
                        Florence Manor
                        </label>
                    </div>
                    <div class="checkbox">
                        <label for="location-3">
                        <input type="checkbox" name="location" id="location-3" value="Fox Run">
                        Fox Run
                        </label>
                    </div>
                    <div class="checkbox">
                        <label for="location-4">
                        <input type="checkbox" name="location" id="location-4" value="ITAP Meadow Run">
                        ITAP Meadow Run
                        </label>
                    </div>
                </div>
            </div>
            <!-- Multiple Checkboxes -->
            <div class="form-group row">
                <label class="col-md-2 col-form-label" for="race"><b>Race</b></label>
                <div class="col-md-4">
                    <div class="checkbox">
                        <label for="race-0">
                        <input type="checkbox" name="race" id="race-0" value="Caucasian">
                        Caucasian
                        </label>
                    </div>
                    <div class="checkbox">
                        <label for="race-1">
                        <input type="checkbox" name="race" id="race-1" value="African American">
                        African American
                        </label>
                    </div>
                    <div class="checkbox">
                        <label for="race-2">
                        <input type="checkbox" name="race" id="race-2" value="Multi Racial">
                        Multi Racial
                        </label>
                    </div>
                    <div class="checkbox">
                        <label for="race-3">
                        <input type="checkbox" name="race" id="race-3" value="Latino">
                        Latino
                        </label>
                    </div>
                    <div class="checkbox">
                        <label for="race-4">
                        <input type="checkbox" name="race" id="race-4" value="Pacific Islander">
                        Pacific Islander
                        </label>
                    </div>
                    <div class="checkbox">
                        <label for="race-5">
                        <input type="checkbox" name="race" id="race-5" value="Native American">
                        Native American
                        </label>
                    </div>
                    <div class="checkbox">
                        <label for="race-6">
                        <input type="checkbox" name="race" id="race-6" value="Other">
                        Other
                        </label>
                    </div>
                </div>
            </div>
            <!-- Multiple Checkboxes -->
            <div class="form-group row">
                <label class="col-md-2 col-form-label" for="age"><b>Age</b></label>
                <div class="col-md-4">
                    <div class="checkbox">
                        <label for="age-0">
                        <input type="checkbox" name="age" id="age-0" value="January">
                        20-40
                        </label>
                    </div>
                    <div class="checkbox">
                        <label for="age-1">
                        <input type="checkbox" name="age" id="age-1" value="February">
                        41-64
                        </label>
                    </div>
                    <div class="checkbox">
                        <label for="age-2">
                        <input type="checkbox" name="age" id="age-2" value="March">
                        65+
                        </label>
                    </div>
                </div>
            </div>
            <!-- Button -->
            <div class="form-group">
                <div class="col-md-4">
                    <a href="custom-reports-table" class="btn cpca">Generate Report</a>
                </div>
            </div>
        </fieldset>
    </form>
</div>
<?php include('footer.php'); ?>