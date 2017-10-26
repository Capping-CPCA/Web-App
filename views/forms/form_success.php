<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Success page for form submissions
 *
 * The success page is displayed when a user has properly submitted a form.
 * They will be redirected here once the information is successfully submitted into the database.
 * In its current state, it is used for showing functionality, and is not actually called on success of database submission,
 * just success in data validation
 *
 * @author Christian Menk and Stephen Bohner
 * @copyright 2017 Marist College
 * @version 0.3.3
 * @since 0.3.2
 */
include('header.php');

global $params, $route, $view;

?>

    <div style="width: 100%">
        <?php
        if(isset($_SESSION['form-type'])) {
            $form_type = $_SESSION['form-type'];
            unset($_SESSION['form-type']);
            echo '<div id="referral_submit_success" class="alert alert-success" role="alert">';
            echo  '<h4 class="alert-heading">Success!</h4>';
            if ($form_type == "agency referral") {
                echo '<p>You have successfully submitted the agency referral form.</p>';
                echo '<p>Would you like to <a id="submit_again_link" href="/referral-form" class="alert-link">submit another agency referral form?</a></p>';
            } else if ($form_type == "self referral") {
                echo '<p>You have successfully submitted the self referral form.</p>';
                echo '<p>Would you like to <a id="submit_again_link" href="/self-referral-form" class="alert-link">submit another self-referral form?</a></p>';
            } else if ($form_type == "intake packet") {
                echo '<p>You have successfully submitted the intake packet.</p>';
                echo '<p>Would you like to <a id="submit_again_link" href="/intake-packet" class="alert-link">submit another intake packet?</a></p>';
            }
        } else {
            echo '<div id="referral_submit_success" class="alert alert-danger" role="alert">';
            echo '<h4 class="alert-heading">Error!</h4>';
            echo '<p>A form was not properly submitted!</p>';
        }
        ?>

        <hr>

    </div>
</div>

<?php include('footer.php'); ?>