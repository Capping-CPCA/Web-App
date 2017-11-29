<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * search for classes
 *
 * form that prompts user to enter in a date and search for all the classes on that day
 *
 * @author Scott Hansen
 * @copyright 2017 Marist College
 * @version [version number]
 * @since [initial version number]
 */

include('header.php');

?>
    <div class="container">
        <div class="card" style="max-width: 700px; width: 100%; margin: 0 auto">
            <div class="card-body">
                <h4 class="card-title">Attendance Search</h4>
                <hr style="margin-top: 0px!important;">
                <form action="historical-class-search-results" method="post">
                    <div class="form-group" style="margin-left: 29.5%;">
                        <label for="date-input">Class Date</label>
                        <input class="form-control" style="width: 264.7px;" type="date" value="<?php echo date('Y-m-d'); ?>" id="date-input" name="date-input">
                    </div>
                    <div class="form-footer submit">
                        <button type="submit" class="btn cpca">Find Attendance Records</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
include('footer.php');
?>