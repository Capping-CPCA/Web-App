<?php 
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays Annual and Semi-Annual Reports.
 *
 * This page will display the information required for the Annual
 * and Semi-Annual Reports, formatted into tables. The desired month
 * and year can be selected by the drop down menus at the top and the
 * 'Generate Report' button will query the database and update the tables.
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
                            <option>Semi-Annual</option>
                            <option>Annual</option>
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
        Semi-Annual 2017</h1>
    </div>
    <div class="container py-3">
        <table class="table table-active table-bordered">
            <thead>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Total # of clients served during the year (unduplicated):</td>
                    <td>2100</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="container pb-2">
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
                    <td>95%</td>
                    <td>945</td>
                    <td>996</td>
                </tr>
                <tr>
                    <td scope="row">Plan on using specific techniques discussed in class</td>
                    <td>98%</td>
                    <td>965</td>
                    <td>996</td>
                </tr>
                <tr>
                    <td scope="row">Realized other parents share the same concerns</td>
                    <td>97%</td>
                    <td>974</td>
                    <td>996</td>
                </tr>
                <tr>
                    <td scope="row">Understand children have different perspectives than they do</td>
                    <td>96%</td>
                    <td>955</td>
                    <td>996</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php include('footer.php'); ?>