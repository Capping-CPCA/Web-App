<?php
function isActive($urls) {
    global $route;
    return in_array($route['url'], $urls);
}

$active = [
    "referral-intake" => isActive(['/referral-form','/intake-packet','/self-referral-form']),
    "class-activity" => isActive(['/record-attendance','/surveys']),
    "curr-and-class" => isActive(['/curricula','/locations','/classes']),
    "reports" => isActive(['/monthly-reports','/quarterly-reports','/year-end-reports','/custom-reports'])
];

?>
<div class="side-menu">
    <nav class="nav flex-column flex-nowrap">
        <a class="nav-link text-secondary <?= active('/dashboard') ?> <?= active('/') ?>" href="<?= BASEURL.'/dashboard' ?>"><i class="fa fa-home fa-fw" aria-hidden="true"></i>Home</a>
        <a class="nav-link text-secondary <?= active('/agency-requests') ?>" href="<?= BASEURL.'/agency-requests' ?>"><i class="fa fa-search fa-fw" aria-hidden="true"></i>Agency Requests</a>
        <a class="nav-link text-secondary <?=!$active['referral-intake']?'collapsed':''?>"
           data-toggle="collapse" data-target="#ParticipantFormsSubMenu" href="#ParticipantForms">
            <i class="fa fa-files-o fa-fw" aria-hidden="true"></i>Referrals & Intake
        </a>
        <div class="collapse <?=!$active['referral-intake']?'':'show'?>" id="ParticipantFormsSubMenu">
            <ul class="flex-column pl-2 nav">
                <a class="nav-link text-secondary py-0 <?= active('/referral-form') ?>" data-parent="#ParticipantForms" href="<?= BASEURL.'/referral-form' ?>">Referral Form</a>
                <a class="nav-link text-secondary py-0 <?= active('/self-referral-form') ?>" data-parent="#ParticipantForms"  href="<?= BASEURL.'/self-referral-form' ?>">Self-Referral Form</a>
                <a class="nav-link text-secondary py-0 <?= active('/intake-packet') ?>" data-parent="#ParticipantForms"  href="<?= BASEURL.'/intake-packet' ?>">Intake Packet</a>
            </ul>
        </div>
        <a class="nav-link text-secondary <?=!$active['class-activity']?'collapsed':''?>"
           data-toggle="collapse" data-target="#ClassActivitySubMenu" href="#ClassActivity">
            <i class="fa fa-book fa-fw" aria-hidden="true"></i>Class Activity
        </a>
        <div class="collapse <?=!$active['class-activity']?'':'show'?>" id="ClassActivitySubMenu">
            <ul class="flex-column pl-2 nav">
                <a class="nav-link text-secondary py-0 <?= active('/record-attendance') ?>" data-parent="#ClassActivity" href="<?= BASEURL.'/record-attendance' ?>">Record Attendance</a>
                <a class="nav-link text-secondary py-0 <?= active('/view-survey-results') ?>" data-parent="#ClassActivity" href="<?= BASEURL.'/surveys' ?>">View Survey Results</a>
            </ul>
        </div>
        <a class="nav-link text-secondary <?=!$active['curr-and-class']?'collapsed':''?>"
           data-toggle="collapse" data-target="#ManageCoursesSubMenu" href="#ManageCourses">
            <i class="fa fa-university fa-fw" aria-hidden="true"></i>Curricula & Classes
        </a>
        <div class="collapse <?=!$active['curr-and-class']?'':'show'?>" id="ManageCoursesSubMenu">
            <ul class="flex-column pl-2 nav">
                <a class="nav-link text-secondary py-0 collapsed <?= active('/curricula') ?>" data-parent="#ManageCourses" href="<?= BASEURL.'/curricula' ?>">Curricula</a>
                <a class="nav-link text-secondary py-0 collapsed <?= active('/classes') ?>" data-parent="#ManageCourses" href="<?= BASEURL.'/classes' ?>">Classes</a>
                <a class="nav-link text-secondary py-0 collapsed <?= active('/locations') ?>" data-parent="#ManageCourses" href="<?= BASEURL.'/locations' ?>">Locations</a>
            </ul>
        </div>
        <a class="nav-link text-secondary <?=!$active['reports']?'collapsed':''?>"
           data-toggle="collapse" data-target="#ReportsSubMenu" href="#Reports">
            <i class="fa fa-bar-chart fa-fw" aria-hidden="true"></i>Reports
        </a>
        <div class="collapse <?=!$active['reports']?'':'show'?>" id="ReportsSubMenu">
            <ul class="flex-column pl-2 nav">
                <a class="nav-link text-secondary py-0 <?= active('/monthly-reports') ?>" data-parent="#Reports" href="<?= BASEURL.'/monthly-reports' ?>">Monthly Report</a>
                <a class="nav-link text-secondary py-0 <?= active('/quarterly-reports') ?>" data-parent="#Reports" href="<?= BASEURL.'/quarterly-reports' ?>">Quarterly Report</a>
                <a class="nav-link text-secondary py-0 <?= active('/year-end-reports') ?>" data-parent="#Reports" href="<?= BASEURL.'/year-end-reports' ?>">Year End Report</a>
                <a class="nav-link text-secondary py-0 <?= active('/custom-reports') ?>" data-parent="#Reports" href="<?= BASEURL.'/custom-reports' ?>">Custom Report</a>
            </ul>
        </div>
        <a class="nav-link text-secondary <?= active('/manage-users') ?>" href="<?= BASEURL.'/manage-users' ?>"><i class="fa fa-users fa-fw" aria-hidden="true"></i>User Management</a>
    </nav>
    <a class="side-menu-welcome">Welcome, <?php echo $_SESSION["username"] ?>!</a>
</div>