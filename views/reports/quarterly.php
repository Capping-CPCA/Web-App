<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays Quarterly Reports.
 *
 * This page will display the information required for Quarterly
 * Reports, formatted into tables. The desired month and year can be
 * selected by the drop down menus at the top and the 'Generate Report'
 * button will query the database and update the tables below.
 *
 * @author Daniel Ahl & Rafael Mormol
 * @copyright 2017 Marist College
 * @version 0.1.4
 * @since 0.1.4
 */
    authorizedPage();
    global $params, $route, $view;
    include('header.php'); 
    ?>
<div class="container">
    <div class="container pt-5 pb-4">
        <div class="row" style="margin-bottom: 1%">
            <div class="col">
                <form>
                    <div class="form-group">
                        <select class="form-control" id="quarter">
                            <option>Q1</option>
                            <option>Q2</option>
                            <option>Q3</option>
                            <option>Q4</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="col">
                <form>
                    <div class="form-group">
                        <select class="form-control" id="year">
                            <option>2017</option>
                            <option>2016</option>
                            <option>2015</option>
                            <option>2014</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div class="row pb-5">
            <div class="col"></div>
            <div class="col-centered">
                <p>
                    <a class="btn cpca" href="#">Generate Report</a>
                </p>
            </div>
            <div class="col"></div>
        </div>
    </div>
    <div class="container py-3">
        <h1 class="text-center">
        Q1 2017</h1>
    </div>
    <div class="container py-2">
        <h3 class="text-center">Participant Totals</h3>
    </div>
    <div class="container pb-2">
        <table class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th></th>
                    <th>Quarter</th>
                    <th>YTD</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Adults Served (unduplicated)</th>
                    <td>509</td>
                    <td>509</td>
                </tr>
                <tr>
                    <th scope="row">Children Represented (unduplicated)</th>
                    <td>533</td>
                    <td>728</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="container py-2">
        <h3 class="text-center">Survey Results</h3>
    </div>
    <div class="container">
        <table class="table table-hover table-striped table-bordered">
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
                    <td>92%</td>
                    <td>468</td>
                    <td>509</td>
                </tr>
                <tr>
                    <td scope="row">Plan on using specific techniques discussed in class</td>
                    <td>93%</td>
                    <td>473</td>
                    <td>509</td>
                </tr>
                <tr>
                    <td scope="row">Realized other parents share the same concerns</td>
                    <td>93%</td>
                    <td>473</td>
                    <td>509</td>
                </tr>
                <tr>
                    <td scope="row">Understand children have different perspectives than they do</td>
                    <td>94%</td>
                    <td>478</td>
                    <td>509</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php include('footer.php'); ?>