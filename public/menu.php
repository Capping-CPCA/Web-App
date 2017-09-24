<div class="side-menu">
    <nav class="nav flex-column flex-nowrap">
        <a class="nav-link text-secondary <?= active($route, '/dashboard') ?> <?= active($route, '/') ?>" href="<?= BASEURL.'/dashboard' ?>"><i class="fa fa-home fa-fw" aria-hidden="true"></i>Home</a>
        <a class="nav-link text-secondary <?= active($route, '/agency-requests') ?>" href="<?= BASEURL.'/agency-requests' ?>"><i class="fa fa-search fa-fw" aria-hidden="true"></i>Agency Requests</a>
        <a class="nav-link text-secondary collapsed" data-toggle="collapse" data-target="#ParticipantFormsSubMenu" href="#ParticipantForms"><i class="fa fa-files-o fa-fw" aria-hidden="true"></i>Referrals & Intake</a>
        <div class="collapse" id="ParticipantFormsSubMenu">
            <ul class="flex-column pl-2 nav">
                <a class="nav-link text-secondary py-0 <?= active($route, '/referral-form') ?>" data-parent="#ParticipantForms" href="<?= BASEURL.'/referral-form' ?>">Referral Form</a>
                <a class="nav-link text-secondary py-0 <?= active($route, '/intake-packet') ?>" data-parent="#ParticipantForms"  href="<?= BASEURL.'/intake-packet' ?>">Intake Packet</a>
            </ul>
        </div>
        <a class="nav-link text-secondary collapsed" data-toggle="collapse" data-target="#ClassActivitySubMenu" href="#ClassActivity"><i class="fa fa-book fa-fw" aria-hidden="true"></i>Class Activity</a>
        <div class="collapse" id="ClassActivitySubMenu">
            <ul class="flex-column pl-2 nav">
                <a class="nav-link text-secondary py-0 <?= active($route, '/record-attendance') ?>" data-parent="#ClassActivity" href="<?= BASEURL.'/record-attendance' ?>">Record Attendance</a>
                <a class="nav-link text-secondary py-0 <?= active($route, '/record-survey-results') ?>" data-parent="#ClassActivity" href="<?= BASEURL.'/record-survey-results' ?>">Record Survey Results</a>
                <a class="nav-link text-secondary py-0 <?= active($route, '/view-survey-results') ?>" data-parent="#ClassActivity" href="<?= BASEURL.'/view-survey-results' ?>">View Survey Results</a>
            </ul>
        </div>
        <a class="nav-link text-secondary collapsed" data-toggle="collapse" data-target="#ManageCoursesSubMenu" href="#ManageCourses"><i class="fa fa-university fa-fw" aria-hidden="true"></i>Curricula & Classes</a>
        <div class="collapse" id="ManageCoursesSubMenu">
            <ul class="flex-column pl-2 nav">
                <a class="nav-link text-secondary py-0 collapsed <?= active($route, '/curricula') ?>" data-parent="#ManageCourses" href="<?= BASEURL.'/curricula' ?>">Curricula</a>
                <a class="nav-link text-secondary py-0 collapsed <?= active($route, '/classes') ?>" data-parent="#ManageCourses" href="<?= BASEURL.'/classes' ?>">Classes</a>
                <a class="nav-link text-secondary py-0 collapsed <?= active($route, '/locations') ?>" data-parent="#ManageCourses" href="<?= BASEURL.'/locations' ?>">Locations</a>
            </ul>
        </div>
        <a class="nav-link text-secondary collapsed" data-toggle="collapse" data-target="#ReportsSubMenu" href="#Reports"><i class="fa fa-bar-chart fa-fw" aria-hidden="true"></i>Reports</a>
        <div class="collapse" id="ReportsSubMenu">
            <ul class="flex-column pl-2 nav">
                <a class="nav-link text-secondary py-0 <?= active($route, '/monthly-reports') ?>" data-parent="#Reports" href="<?= BASEURL.'/monthly-reports' ?>">Monthly Report</a>
                <a class="nav-link text-secondary py-0 <?= active($route, '/quarterly-reports') ?>" data-parent="#Reports" href="<?= BASEURL.'/quarterly-reports' ?>">Quarterly Report</a>
                <a class="nav-link text-secondary py-0 <?= active($route, '/year-end-reports') ?>" data-parent="#Reports" href="<?= BASEURL.'/year-end-reports' ?>">Year End Report</a>
                <a class="nav-link text-secondary py-0 <?= active($route, '/custom-reports') ?>" data-parent="#Reports" href="<?= BASEURL.'/custom-reports' ?>">Custom Report</a>
            </ul>
        </div>
        <a class="nav-link text-secondary <?= active($route, 'manage_users.php') ?>" href="<?= BASEURL.'/manage-users' ?>"><i class="fa fa-users fa-fw" aria-hidden="true"></i>User Management</a>
    </nav>
    <a class="side-menu-welcome">Welcome, <?php echo $_SESSION["username"] ?>!</a>
</div>