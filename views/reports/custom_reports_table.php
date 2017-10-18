<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays the results of a custom report.
 *
 * This page will display the information requested in the form
 * on custom_reports.php page, formatted into a single table.
 *
 * @author Rafael Mormol & Daniel Ahl
 * @copyright 2017 Marist College
 * @version 0.1.5
 * @since 0.1.4
 */
    global $params, $route, $view;
    include('header.php');
    ?>
<div class="container">
    <h3 align="center">Custom Report</h3>
    <br />
    <h6 align="center">Month: October</h6>
    <h6 align="center">Year: 2016</h6>
    <br />
    <h6 align="center">Location: Cornerstone | Race: African American | Age: 41-64 </h6>
    <table class="table table-hover table-striped table-bordered">
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
                    7
                </td>
                <td>
                    2
                </td>
                <td>
                    0
                </td>
                <td>
                    24
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php include('footer.php'); ?>