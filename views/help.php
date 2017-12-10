<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays help about the system to the user.
 *
 * The help is context specific based on the page you were
 * just at. If you go to the help directly then the general
 * help will be displayed. Help is also role-specific too.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.3.3
 * @since 0.3.3
 */

if (isset($_SERVER['HTTP_REFERER'])) {
    $referrer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);

    // Maps URL route to href
    $routeToHref = array(
        '/agency-requests' => '#agency',
        '/attendance' => '#attendance',
        '/new-class' => '#attendance',
        '/attendance-form' => '#attendance',
        '/attendance-edit-participant' => '#attendance',
        '/attendance-form-confirmation' => '#attendance',
        '/curricula' => '#curricula',
        '/curricula/create' => '#curricula-create',
        '/curricula/archive' => '#curricula-archives',
        '/classes' => '#classes',
        '/classes/create' => '#classes-create',
        '/classes/archive' => '#classes-archives',
        '/quarterly-reports' => '#reports-quarterly',
        '/year-end-reports' => '#reports-half-year',
        '/monthly-reports' => '#reports-monthly',
        '/custom-reports' => '#reports-custom',
        '/custom-reports-table' => '#reports-custom',
        '/referral-form' => '#referrals',
        '/self-referral-form' => '#referrals',
        '/intake-packet' => '#referrals',
        '/form-success' => '#referrals',
        '/manage-users' => '#user-manage',
        '/manage-users/archives' => '#user-manage-restore',
        '/surveys' => '#surveys',
        '/surveys/results' => '#surveys',
    );

    // Check for general routes
    if (isset($routeToHref[$referrer])) {
        $href = $routeToHref[$referrer];
    }
    // If not found, be more specific
    else {
        if (startsWith($referrer, "/participant-search") ||
            startsWith($referrer, "/view-participant") ||
            startsWith($referrer, "/ps-view-participant")) {
            $href = '#agency';
        }
        // Curricula
        else if (startsWith($referrer, '/curricula/view')) {
            $href = '#curricula';
        } else if (startsWith($referrer, '/curricula/delete')) {
            $href = '#curricula-delete';
        } else if (startsWith($referrer, '/curricula/classes')) {
            $href = '#curricula-add-class';
        } else if (startsWith($referrer, '/curricula/edit')) {
            $href = '#curricula-edit';
        }
        // Classes
        else if (startsWith($referrer, '/classes/view') ||
            startsWith($referrer, '/classes/edit')) {
            $href = '#classes';
        } else if (startsWith($referrer, '/classes/delete')) {
            $href = '#classes-delete';
        }
        // User Management
        else if (startsWith($referrer, '/account-settings/modify')) {
            $href = '#user-manage-edit';
        } else if (startsWith($referrer, '/account-settings')) {
            $href = '#user-manage';
        }
    }
}

$figureCounter = 1;

include('header.php');
?>

<div data-spy="scroll" data-target="#toc-list" data-offset="75" id="help-section" style="flex: 1; padding: 0 1rem">
    <div style="width: 100%; max-width: 800px; margin: 0 auto">
        <h4 id="general">General</h4>
        <p>
            This page provides detailed descriptions of various user functions in the PEP Manager. If after looking
            through the help sections you still have not solved your problem, please contact IT for further information.
        </p>
        <h5 id="display">Display</h5>
        <p>
            The PEP Manager display is set up in three parts: the top bar, the side menu, and the main content section.
        </p>
        <ul>
            <li>
                <b>Top Bar</b> - This bar contains the name of the system, the current page you are on, and user
                specific links (Logout, Account Settings, Help).
            </li>
            <li>
                <b>Side Menu</b> - This menu contains the navigation links to get to every page within the PEP Manager.
            </li>
            <li>
                <b>Main Content</b> - This is the largest portion of the page which will display all of the content
                for each page.
            </li>
        </ul>

        <?php if (hasRole(Role::User)) { ?>
            <h4 id="agency">Agency Requests</h4>
            <div>
                <p>
                    The Agency Requests page provides a search engine for the program’s participants, where you can
                    filter through past and current students and view their information.
                </p>
                <p>
                    <i><span class="text-muted">Searching</span></i><br />
                    As you type in a participants name into the field, suggested matches will be displayed beneath the
                    search bar. You can either select one of these names or click the "Submit" button. You will be
                    redirected to a page with a list of possible matches. Click the "View Record" button to see more
                    detailed information. If you want to simply view information quickly, select the small
                    "<span class="fa fa-caret-right"></span>" button to the left of the name.
                </p>
                <p>
                    <i><span class="text-muted">Viewing Details</span></i><br />
                    After selecting "View Record" next to a participants name, the participant’s information will then
                    be displayed at the center of the screen. You are able to view their name, current status in the
                    program, relevant notes, modes of contact, and family information. As you scroll down the same
                    screen, you’re able to view any additional information, modes of contact, and the participant’s
                    family information.
                </p>
                <p>
                    There is also a set of buttons at the bottom of the participant's profile related to their attendance,
                    intake & referral forms, and also their currently assigned curricula. Clicking on the "View Forms"
                    button will display a grid of the forms filled out for the participant. They can be edited or viewed
                    from here.
                </p>
            </div>

            <h4 id="referrals">Referrals & Intake</h4>
            <div>
                <p>
                    The Referrals and Intake pages provide a way for adding new participants into the system. The drop
                    down includes a <i>Referral Form, Self-Referral Form</i> and the <i>Intake Packet</i>.
                </p>
                <p>
                    To navigate through sections, the user may use their mouse to click through each field, or press the
                    TAB key on their keyboard. Pressing the TAB key will bring the user to the next field in the form.
                    Pressing the TAB key on the last field in the form’s section will bring you to the next section.
                </p>
                <p>
                    Another way to move between form sections is by clicking on the form section header
                    (ie: <i>Participant Household Information</i>). Doing so will bring you to that corresponding
                    section.
                </p>
                <p>
                    <i><span class="text-muted">Duplicate Participants</span></i><br />
                    If the system finds an existing participant in the system with the same first and last name as the
                    participant entered in the form, a page with a grid of possible matches are displayed. If the
                    participant entered is one of the ones displayed, "Select" that participant to finish the form.
                    Otherwise, click "Yes, Continue Creating Participant".
                </p>
            </div>

            <h4 id="curricula">Curricula</h4>
            <h5 id="curricula-search">Search Curricula / Grid</h5>
            <div>
                <p>
                    To filter the results displayed on the curricula screen, enter a search filter like "Cornerstone"
                    into the filter field and click "Search".
                </p>
                <div class="input-group" style="max-width: 700px; width: 100%; margin: 0 auto;">
                    <input type="text" class="form-control" placeholder="Filter for..." value="">
                    <span class="input-group-btn">
                        <button class="btn btn-secondary" type="button">Search</button>
                    </span>
                </div>
                <div class="text-center mt-2">
                    <small class="text-muted font-italic">
                        Figure <?= $figureCounter++ ?>. <b>Filter search field</b>
                    </small>
                </div>
                <br />
                <p>
                    The results are displayed in a grid-like format. Each curriculum entry is displayed with its name
                    and corresponding location. To view, edit, or delete a curriculum use the buttons below the
                    curriculum name. Also the "Classes" button will allow a user to manage a curriculum's classes.
                </p>
            </div>
            <?php if (hasRole(Role::Coordinator)) { ?>
                <h5 id="curricula-create">Create New Curriculum</h5>
                <div>
                    <p>
                        To create a new curriculum, select the "Create Curriculum" button located in the top left corner
                        of the <a href="/curricula">curriculum page</a>. You will be redirected to a new page where you
                        can enter the curriculum's:
                    </p>
                    <ul>
                        <li>
                            <b>Name</b> - A title to identify the curriculum
                        </li>
                        <li>
                            <b>Maximum Number of Missed Classes</b> - How many classes a participant is allowed to miss
                        </li>
                    </ul>
                    <p>
                        To finish, click the "Add Curriculum" button. If at any point you want to cancel making changes,
                        click the "Back" button in the upper left part of the page.
                    </p>
                </div>
                <h5 id="curricula-edit">Editing a Curriculum</h5>
                <div>
                    <p>
                        On the <a href="/curricula">curriculum page</a>, click the corresponding "Edit" button beneath
                        the desired curriculum's name. From here you will be redirected to a new page displaying a form
                        to edit the current values of the curriculum. Once changes are made, click the "Submit New
                        Changes" button.
                    </p>
                    <p>
                        There is also a "Click to Manage Classes" button which will redirect to the current curriculum's
                        class management page.
                        <i>Note: This will not save the changes made to the curriculum edit form.</i>
                    </p>
                </div>
                <h5 id="curricula-add-class">Add / Remove Classes from a Curriculum</h5>
                <div>
                    <p>
                        On the <a href="/curricula">curriculum page</a>, click the corresponding "Classes" button
                        beneath the desired curriculum's name. From here you will be redirected to a new page displaying
                        the curriculum's <i>current classes</i> and a drop down to select and add a new class.
                    </p>
                    <p>
                        <i><span class="text-muted">Add</span></i><br />
                        To add a new class, select a value from the drop down underneath "Add New Class", and then click
                        the "Add Class" button.
                    </p>
                    <p>
                        <i><span class="text-muted">Remove</span></i><br />
                        To remove a class from a curriculum, click the red "Remove" button next to the class you wish to
                        remove.
                    </p>
                </div>
                <h5 id="curricula-delete">Deleting a Curriculum</h5>
                <div>
                    <p>
                        To delete a curriculum, while on the <a href="/curricula">curriculum page</a>, click the red
                        "Delete" button underneath the desired curriculum. A confirmation page will then be displayed
                        to make sure you really want to delete the curriculum. If this is the case, click "Delete".
                        Otherwise, click "Cancel" if you did not mean to delete the curriculum.
                    </p>
                </div>
            <?php } ?>
            <?php if (hasRole(Role::Admin)) { ?>
                <h5 id="curricula-archives">Restore / Archived Curricula</h5>
                <div>
                    <p>
                        When a curriculum is "deleted" it is actually sent to the archive where it can be either
                        restored or fully deleted from the system. To access the <a href="/curricula/archive">archive</a>,
                        go to the main <a href="/curricula">curriculum page</a>, and then click the "See Archive" button
                        in the top left corner of the page.
                    </p>
                    <p>
                        <i><span class="text-muted">Restore</span></i><br />
                        To restore a curriculum, click the "Restore" button beneath the desired curriculum.
                        <i>Note: All classes previously associated with the curriculum will still be linked.</i>
                    </p>
                    <p>
                        <i><span class="text-muted">Full Delete</span></i><br />
                        To fully delete a curriculum, click the "Delete" button beneath the desired curriculum.
                        <b>If there is attendance based on this curriculum, this information will be removed.</b>
                    </p>
                </div>
            <?php } ?>

            <h4 id="classes">Classes</h4>
            <h5 id="classes-search">Search Classes / Grid</h5>
            <div>
                <p><i>See <a href="#curricula-search">searching for curricula</a>.</i></p>
            </div>
            <?php if (hasRole(Role::Coordinator)) { ?>
                <h5 id="classes-create">Create New Class</h5>
                <div>
                    <p>
                        To create a new class, select the "Create Class" button located in the top left corner
                        of the <a href="/classes">classes page</a>. You will be redirected to a new page where you
                        can enter the new class':
                    </p>
                    <ul>
                        <li>
                            <b>Name</b> - A title to identify the class
                        </li>
                        <li>
                            <b>Description</b> - Short description about the class
                        </li>
                    </ul>
                    <p>
                        To finish, click the "Add Class" button. If at any point you want to cancel making changes,
                        click the "Back" button in the upper left part of the page.
                    </p>
                </div>
                <h5 id="classes-edit">Editing a Class</h5>
                <div>
                    <p>
                        On the <a href="/classes">classes page</a>, click the corresponding "Edit" button beneath
                        the desired class' name. From here you will be redirected to a new page displaying a form
                        to edit the current values of the class. Once changes are made, click the "Submit New
                        Changes" button.
                    </p>
                </div>
                <h5 id="classes-delete">Deleting a Class</h5>
                <div>
                    <p>
                        To delete a class, while on the <a href="/classes">classes page</a>, click the red
                        "Delete" button underneath the desired class. A confirmation page will then be displayed
                        to make sure you really want to delete the class. If this is the case, click "Delete".
                        Otherwise, click "Cancel" if you did not mean to delete the class.
                    </p>
                </div>
                <?php if (hasRole(Role::Admin)) { ?>
                    <h5 id="classes-archives">Restore / Archived Classes</h5>
                    <div>
                        <p>
                            When a class is "deleted" it is actually sent to the archive where it can be either
                            restored or fully deleted from the system. To access the <a href="/classes/archive">archive</a>,
                            go to the main <a href="/classes">classes page</a>, and then click the "See Archive" button
                            in the top left corner of the page.
                        </p>
                        <p>
                            <i><span class="text-muted">Restore</span></i><br />
                            To restore a class, click the "Restore" button beneath the desired class.
                            <i>Note: All curricula previously associated with the class will still be linked.</i>
                        </p>
                        <p>
                            <i><span class="text-muted">Full Delete</span></i><br />
                            To fully delete a class, click the "Delete" button beneath the desired class.
                            <b>If there is attendance based on this class, this information will be removed.</b>
                        </p>
                    </div>
                <?php } ?>
            <?php } ?>

            <?php if (hasRole(Role::Coordinator)) { ?>
                <h4 id="reports">Reports</h4>
                <h5 id="reports-monthly">Monthly</h5>
                <div>
                    <p>
                        The Monthly Reports page provides the demographics of participants served.  It also displays
                        the amounts of participants served based on their home zip code.
                    </p>
                    <p>
                        Click the left drop down, to select the month that you would like to generate a report for and
                        the right drop down to select the year. Once the appropriate selections have been made, click
                        the "Generate Report" button.  The Monthly Report results will be displayed on the same page.
                        It displays:
                    </p>
                    <ul>
                        <li>Month and year being reported on</li>
                        <li>Amount of participants served</li>
                        <li>Participant ethnicity</li>
                        <li>Amount of participants served organized by their home zip code</li>
                    </ul>
                </div>
                <h5 id="reports-quarterly">Quarterly</h5>
                <div>
                    <p>
                        The Quarterly Reports page provides you with participant totals and survey results for the
                        Quarter chosen. Participant totals show the amount of unduplicated Adults and Children served
                        in the Quarter chosen. Survey Results displays the information collected from the surveys
                        administered at the end of each class.
                    </p>
                    <p>
                        Click the left drop down, to select the quarter that you would like to generate a report for and
                        the right drop down to select the year. Once the appropriate selections have been made, click
                        the "Generate Report" button.  The Quarterly Report results will be displayed on the same page.
                        It displays:
                    </p>
                    <ul>
                        <li>Quarter number and year being reported on</li>
                        <li>Number of unduplicated adults and children served</li>
                        <li>Survey results</li>
                    </ul>
                </div>
                <h5 id="reports-half-year">Half-Year / Year-End</h5>
                <div>
                    <p>
                        The Year End Reports page provides survey results for either the half year or full year.
                    </p>
                    <p>
                        Click the left drop down, to select the length of time for the report, and
                        the right drop down to select the year. Once the appropriate selections have been made, click
                        the "Generate Report" button.  The Year End Report results will be displayed on the same page.
                        It displays:
                    </p>
                    <ul>
                        <li>Timeframe being reported on (Semi-Annual or Year End) and year</li>
                        <li>Survey questions and participant responses</li>
                    </ul>
                </div>
                <h5 id="reports-custom">Custom</h5>
                <div>
                    <p>
                        The Custom Reports page provides you with the ability to create your own reports. You can
                        select or deselect as many of these characteristics as they want.
                    </p>
                    <p>
                        Once all desired selections have been made, click the "Generate Report" button. The Report will
                        display the Month, Year, and additional characteristics that are being reported on.
                        The data is then displayed below the options that were selected.
                    </p>
                </div>
            <?php } ?>

            <?php if (hasRole(Role::Admin)) { ?>
                <h4 id="user-manage">User Management</h4>
                <div>
                    <p>
                        The User Management feature allows administrators and super administrators the ability to assign
                        roles to other users. This would be the place where new employee users would be assigned to
                        their facilitator, coordinator or administrator roles.
                    </p>
                    <h5 id="user-manage-view">Viewing User Details</h5>
                    <p>
                        When navigating to the user management page, a list of users will be displayed. To view their
                        details click the "View" button to the right of their names. On this page, various details
                        such as the user's email, role, and phone number is displayed. There are "Edit" and "Delete"
                        buttons to the right of the user's name for user editing and deletion.
                    </p>
                    <h5 id="user-manage-edit">Editing a User</h5>
                    <p>
                        On the view user page, if the "Edit" button is clicked, the page will be directed to an edit
                        page to update the user's details. If a user is a facilitator there will be options to update
                        the user's primary and secondary languages. To update the primary language, select a language
                        from the drop down and click the "Update Language" button. To add a secondary language, choose
                        a language from the drop down and click "Add Language". <b>These languages will not be added
                        to the user until the "Submit New Changes" button is clicked.</b>
                    </p>
                    <h5 id="user-manage-delete">Deleting a User</h5>
                    <p>
                        To delete a user, click the "Delete" button on either the main user management page or when
                        viewing a user's details. A message will be displayed to confirm the deletion of the user.
                        Click "Delete" to actually delete the user, or "Cancel" to return back without deleting.
                    </p>
                    <?php if (hasRole(Role::Superuser)) { ?>
                        <h5 id="user-manage-restore">Restoring a User</h5>
                        <p>
                            To restore a deleted user, click the "See Archive" button on the
                            <a href="/manage-users">user management</a> page. This page will look similar to the main
                            user management page, but with a single option to "Restore" the listed user.
                        </p>
                    <?php } ?>
                </div>
            <?php } ?>

            <h4 id="class-activity">Class Activity</h4>
            <h5 id="attendance">Attendance</h5>
            <div>
                <p>
                    The Attendance section of the web application allows the user to view attendance from previous
                    classes or submit attendance that is initially recorded on paper.
                </p>
                <p>
                    <i><span class="text-muted">Recording Attendance</span></i><br />
                    To record attendance for a class, navigate to the <a href="/attendance">attendance page</a> and
                    click on the "Record Attendance For New Class" button. You can then select the location, curriculum,
                    class, language, facilitator, and date of the attendance. Once all the appropriate selections have
                    been made, click the "Create Attendance Sheet" button.
                </p>
                <p>
                    Using the Current Participants list, click on the checkbox next to the participant’s name if they
                    were present for the class. (Optional: Use the text box in the comments column to record additional
                    information about the participant.) To add a new participant to the class, use the search bar to
                    search by their name. If the participant’s name is not displayed, they did not fill out an Intake
                    Packet. Click the "No Intake Form" tab and fill out the appropriate participant information, then
                    click "Add Person".
                </p>
                <p>
                    To submit the attendance sheet, click the "Submit Attendance" button at the bottom of the page. You
                    will be redirected to a confirmation page to review the attendance. Once satisfied, click the
                    "Submit Attendance" buttton. If not click the "Edit Attendance" button to return.
                </p>
                <p>
                    <i><span class="text-muted">Viewing Attendance History</span></i><br />
                    The attendance dashboard will display recent classes that the currently logged in user has taught.
                    To search for other attendance, click the "Search For Historical Attendance" button. From here you
                    can search attendance by class date, curriculum, and the specific class. Once satisfied, click the
                    "Find Attendance Records" button. Next to each attendance displayed is a "More details..." button
                    which will display in-depth details about the attendance sheet.
                </p>
            </div>
            <h5 id="surveys">Surveys</h5>
            <div>
                <p>
                    The Survey Results page allows you to view the results of the in-class surveys. When on the surveys
                    page, click the class drop down box and select the name of the class you would like to view the
                    survey results for. After making your class selection, using the appropriate drop down boxes,
                    select the month, day and year to filter survey results. Then click the "Search Surveys" button
                </p>
                <p>
                    To update survey data click the "Update Surveys" button located on the bottom of the page. The time
                    that the surveys were last updated is located above this button. Only surveys before this time will
                    be displayed in the results.
                </p>
            </div>
        <?php } ?>

<!--        <h4 id="faq">FAQ</h4>-->
<!--        <p class="text-muted">Deserunt quis elit Lorem eiusmod amet enim enim amet minim Lorem proident nostrud. Ea id dolore anim exercitation aute fugiat labore voluptate cillum do laboris labore. Ex velit exercitation nisi enim labore reprehenderit labore nostrud ut ut. Esse officia sunt duis aliquip ullamco tempor eiusmod deserunt irure nostrud irure. Ullamco proident veniam laboris ea consectetur magna sunt ex exercitation aliquip minim enim culpa occaecat exercitation. Est tempor excepteur aliquip laborum consequat do deserunt laborum esse eiusmod irure proident ipsum esse qui.</p>-->
    </div>
</div>

<!-- Table of Contents -->
<div id="toc">
    <nav class="nav flex-column" id="toc-list">
        <a class="nav-link toc-entry" href="#general">General</a>
        <nav class="nav flex-column">
            <a class="nav-link toc-entry" href="#display">Display</a>
        </nav>
        <?php if (hasRole(Role::User)) { ?>
            <a class="nav-link toc-entry" href="#agency">Agency Requests</a>
            <a class="nav-link toc-entry" href="#referrals">Referrals & Intake</a>
            <a class="nav-link toc-entry" href="#curricula">Curricula</a>
            <nav class="nav flex-column">
                <a class="nav-link toc-entry" href="#curricula-search">Search / Grid</a>
                <?php if (hasRole(Role::Coordinator)) { ?>
                    <a class="nav-link toc-entry" href="#curricula-create">Create New</a>
                    <a class="nav-link toc-entry" href="#curricula-edit">Edit</a>
                    <a class="nav-link toc-entry" href="#curricula-add-class">Add / Remove Classes</a>
                    <a class="nav-link toc-entry" href="#curricula-delete">Delete</a>
                <?php } ?>
                <?php if (hasRole(Role::Admin)) { ?>
                    <a class="nav-link toc-entry" href="#curricula-archives">Restore / Archived</a>
                <?php } ?>
            </nav>
            <a class="nav-link toc-entry" href="#classes">Classes</a>
            <nav class="nav flex-column">
                <a class="nav-link toc-entry" href="#classes-search">Search / Grid</a>
                <?php if (hasRole(Role::Coordinator)) { ?>
                    <a class="nav-link toc-entry" href="#classes-create">Create New</a>
                    <a class="nav-link toc-entry" href="#classes-edit">Edit</a>
                    <a class="nav-link toc-entry" href="#classes-delete">Delete</a>
                <?php } ?>
                <?php if (hasRole(Role::Admin)) { ?>
                    <a class="nav-link toc-entry" href="#classes-archives">Restore / Archived</a>
                <?php } ?>
            </nav>
            <?php if (hasRole(Role::Coordinator)) { ?>
                <a class="nav-link toc-entry" href="#reports">Reports</a>
                <nav class="nav flex-column">
                    <a class="nav-link toc-entry" href="#reports-monthly">Monthly</a>
                    <a class="nav-link toc-entry" href="#reports-quarterly">Quarterly</a>
                    <a class="nav-link toc-entry" href="#reports-half-year">Half-Year / Year-End</a>
                    <a class="nav-link toc-entry" href="#reports-custom">Custom</a>
                </nav>
            <?php } ?>
            <?php if (hasRole(Role::Admin)) { ?>
                <a class="nav-link toc-entry" href="#user-manage">User Management</a>
                <nav class="nav flex-column">
                    <a class="nav-link toc-entry" href="#user-manage-view">View Details</a>
                    <a class="nav-link toc-entry" href="#user-manage-edit">Edit</a>
                    <a class="nav-link toc-entry" href="#user-manage-delete">Delete</a>
                    <?php if (hasRole(Role::Superuser)) { ?>
                        <a class="nav-link toc-entry" href="#user-manage-restore">Restore</a>
                    <?php } ?>
                </nav>
            <?php } ?>
            <a class="nav-link toc-entry" href="#class-activity">Class Activity</a>
            <nav class="nav flex-column">
                <a class="nav-link toc-entry" href="#attendance">Attendance</a>
                <a class="nav-link toc-entry" href="#surveys">Surveys</a>
            </nav>
        <?php } ?>
<!--        <a class="nav-link toc-entry" href="#faq">FAQ</a>-->
    </nav>
</div>

<?php if(isset($href)) { ?>
    <script>
        window.location.href = "<?= $href ?>";
    </script>
<?php } ?>

<?php
include('footer.php');
?>
