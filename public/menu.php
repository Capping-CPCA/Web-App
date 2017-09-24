<div class="side-menu">
    <nav class="nav flex-column flex-nowrap">
        <a class="nav-link text-secondary <?= active($route, 'dashboard.php') ?>" href="<?= BASEURL.'/dashboard' ?>"><i class="fa fa-home fa-fw" aria-hidden="true"></i>Home</a>
        <a class="nav-link text-secondary <?= active($route, 'agency_requests.php') ?>" href="<?= BASEURL.'/agency-requests' ?>"><i class="fa fa-search fa-fw" aria-hidden="true"></i>Agency Requests</a>
        <a class="nav-link text-secondary collapsed" data-toggle="collapse" data-target="#ParticipantFormsSubMenu" href="#ParticipantForms"><i class="fa fa-files-o fa-fw" aria-hidden="true"></i>Participant Referrals & Intake</a>
        <div class="collapse" id="ParticipantFormsSubMenu">
            <ul class="flex-column pl-2 nav">
                <a class="nav-link text-secondary py-0 <?= active($route, 'referral_form.php') ?>" data-parent="#ParticipantForms" href="<?= BASEURL.'/referal-form' ?>">Referral Form</a>
                <a class="nav-link text-secondary py-0 <?= active($route, 'intake_packet.php') ?>" data-parent="#ParticipantForms"  href="<?= BASEURL.'/itnake-packet' ?>">Intake Packet</a>
            </ul>
        </div>
        <a class="nav-link text-secondary collapsed" data-toggle="collapse" data-target="#ClassActivitySubMenu" href="#ClassActivity"><i class="fa fa-book fa-fw" aria-hidden="true"></i>Class Activity</a>
        <div class="collapse" id="ClassActivitySubMenu">
            <ul class="flex-column pl-2 nav">
                <a class="nav-link text-secondary py-0 <?= active($route, 'record_attendance.php') ?>" data-parent="#ClassActivity" href="<?= BASEURL.'/record-attendance' ?>">Record Attendance</a>
                <a class="nav-link text-secondary py-0 <?= active($route, 'record_survey_results.php') ?>" data-parent="#ClassActivity" href="<?= BASEURL.'/record-survey-results' ?>">Record Survey Results</a>
                <a class="nav-link text-secondary py-0 <?= active($route, 'view_survey.php') ?>" data-parent="#ClassActivity" href="<?= BASEURL.'/view-survey-results' ?>">View Survey Results</a>
            </ul>
        </div>
        <a class="nav-link text-secondary collapsed" data-toggle="collapse" data-target="#ManageCoursesSubMenu" href="#ManageCourses"><i class="fa fa-university fa-fw" aria-hidden="true"></i>Manage Curricula & Classes</a>
        <div class="collapse" id="ManageCoursesSubMenu">
            <ul class="flex-column pl-2 nav">
                <a class="nav-link text-secondary py-0 collapsed <?= active($route, 'curricula/curricula.php') ?>" data-parent="#ManageCourses" href="<?= BASEURL.'/curricula' ?>">Curricula</a>
                <a class="nav-link text-secondary py-0 collapsed <?= active($route, 'classes/classes.php') ?>" data-parent="#ManageCourses" href="<?= BASEURL.'/classes' ?>">Classes</a>
                <a class="nav-link text-secondary py-0 collapsed <?= active($route, 'locations/locations.php') ?>" data-parent="#ManageCourses" href="<?= BASEURL.'/locations' ?>">Locations</a>
            </ul>
        </div>
        <a class="nav-link text-secondary collapsed" data-toggle="collapse" data-target="#ReportsSubMenu" href="#Reports"><i class="fa fa-bar-chart fa-fw" aria-hidden="true"></i>Reports</a>
        <div class="collapse" id="ReportsSubMenu">
            <ul class="flex-column pl-2 nav">
                <a class="nav-link text-secondary py-0 <?= active($route, 'monthly_report.php') ?>" data-parent="#Reports" href="<?= BASEURL.'/monthly-reports' ?>">Monthly Report</a>
                <a class="nav-link text-secondary py-0 <?= active($route, 'quarterly_report.php') ?>" data-parent="#Reports" href="<?= BASEURL.'/quarterly-reports' ?>">Quarterly Report</a>
                <a class="nav-link text-secondary py-0 <?= active($route, 'year_end_report.php') ?>" data-parent="#Reports" href="<?= BASEURL.'/year-end-reports' ?>">Year End Report</a>
                <a class="nav-link text-secondary py-0 <?= active($route, 'custom_report.php') ?>" data-parent="#Reports" href="<?= BASEURL.'/custom-reports' ?>">Custom Report</a>
            </ul>
        </div>
        <a class="nav-link text-secondary <?= active($route, 'manage_users.php') ?>" href="<?= BASEURL.'/manage-users' ?>"><i class="fa fa-users fa-fw" aria-hidden="true"></i>User Management</a>
    </nav>
    <a class="side-menu-welcome">Welcome, <?php echo $_SESSION["username"] ?>!</a>
</div>