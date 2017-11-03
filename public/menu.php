<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Side menu displayed on every page for navigation.
 *
 * This file contains all the HTML and PHP logic to
 * allow navigation through the system via the menu.
 * This page is used in conjunction with the header
 * and footer files. It should only be included once
 * on a page.
 *
 * @author Jack Grzechowiak
 * @author Michelle Crawley
 * @copyright 2017 Marist College
 * @version 0.3.2
 * @since 0.1
 */

function isActive($urls) {
    global $route;
    return in_array($route['url'], $urls);
}

$active = [
    "referral-intake" => isActive(['/referral-form','/intake-packet','/self-referral-form']),
    "class-activity" => isActive(['/record-attendance','/record-surver-results','/view-survey-results']),
    "curr-and-class" => isActive(['/curricula','/locations','/classes']),
    "reports" => isActive(['/monthly-reports','/quarterly-reports','/year-end-reports','/custom-reports'])
];

?>
<div class="side-menu">
    <nav class="nav flex-column flex-nowrap">

        <!-- Dashboard -->
        <a class="nav-link text-secondary <?= active('/dashboard') ?> <?= active('/') ?>" href="<?= BASEURL.'/dashboard' ?>">
            <i class="fa fa-home fa-fw" aria-hidden="true"></i>Home
        </a>

        <?php if(hasRole(Role::User)) { ?>
            <!-- Agency Requests -->
            <a class="nav-link text-secondary <?= active('/agency-requests') ?>" href="<?= BASEURL.'/agency-requests' ?>">
                <i class="fa fa-search fa-fw" aria-hidden="true"></i>Agency Requests
            </a>

            <!-- Referrals & Intake -->
            <a class="nav-link text-secondary <?=!$active['referral-intake']?'collapsed':''?>"
               data-toggle="collapse" data-target="#ParticipantFormsSubMenu" href="#ParticipantForms">
                <i class="fa fa-files-o fa-fw" aria-hidden="true"></i>Referrals & Intake
            </a>
            <div class="collapse <?=!$active['referral-intake']?'':'show'?>" id="ParticipantFormsSubMenu">
                <ul class="flex-column pl-2 nav">
                    <a class="nav-link text-secondary py-0 <?= active('/referral-form') ?>" data-parent="#ParticipantForms" href="<?= BASEURL.'/referral-form' ?>">
                        Referral Form
                    </a>
                    <a class="nav-link text-secondary py-0 <?= active('/self-referral-form') ?>" data-parent="#ParticipantForms"  href="<?= BASEURL.'/self-referral-form' ?>">
                        Initial Contact Form
                    </a>
                    <a class="nav-link text-secondary py-0 <?= active('/intake-packet') ?>" data-parent="#ParticipantForms"  href="<?= BASEURL.'/intake-packet' ?>">
                        Intake Packet
                    </a>
                </ul>
            </div>

            <!-- Class Activity -->
            <a class="nav-link text-secondary <?=!$active['class-activity']?'collapsed':''?>"
               data-toggle="collapse" data-target="#ClassActivitySubMenu" href="#ClassActivity">
                <i class="fa fa-book fa-fw" aria-hidden="true"></i>Class Activity
            </a>
            <div class="collapse <?=!$active['class-activity']?'':'show'?>" id="ClassActivitySubMenu">
                <ul class="flex-column pl-2 nav">
                    <a class="nav-link text-secondary py-0 <?= active('/record-attendance') ?>" data-parent="#ClassActivity" href="<?= BASEURL.'/record-attendance' ?>">
                        Record Attendance
                    </a>
                    <a class="nav-link text-secondary py-0 <?= active('/record-survey-results') ?>" data-parent="#ClassActivity" href="<?= BASEURL.'/record-survey-results' ?>">
                        Record Survey Results
                    </a>
                    <a class="nav-link text-secondary py-0 <?= active('/view-survey-results') ?>" data-parent="#ClassActivity" href="<?= BASEURL.'/view-survey-results' ?>">
                        View Survey Results
                    </a>
                </ul>
            </div>

            <!-- Curricula & Classes -->
            <a class="nav-link text-secondary <?=!$active['curr-and-class']?'collapsed':''?>"
               data-toggle="collapse" data-target="#ManageCoursesSubMenu" href="#ManageCourses">
                <i class="fa fa-university fa-fw" aria-hidden="true"></i>Curricula & Classes
            </a>
            <div class="collapse <?=!$active['curr-and-class']?'':'show'?>" id="ManageCoursesSubMenu">
                <ul class="flex-column pl-2 nav">
                    <a class="nav-link text-secondary py-0 collapsed <?= active('/curricula') ?>" data-parent="#ManageCourses" href="<?= BASEURL.'/curricula' ?>">
                        Curricula
                    </a>
                    <a class="nav-link text-secondary py-0 collapsed <?= active('/classes') ?>" data-parent="#ManageCourses" href="<?= BASEURL.'/classes' ?>">
                        Classes
                    </a>
                    <a class="nav-link text-secondary py-0 collapsed <?= active('/locations') ?>" data-parent="#ManageCourses" href="<?= BASEURL.'/locations' ?>">
                        Locations
                    </a>
                </ul>
            </div>

            <!-- Reports -->
            <?php if (hasRole(Role::Coordinator)) { ?>
                <a class="nav-link text-secondary <?=!$active['reports']?'collapsed':''?>"
                   data-toggle="collapse" data-target="#ReportsSubMenu" href="#Reports">
                    <i class="fa fa-bar-chart fa-fw" aria-hidden="true"></i>Reports
                </a>
                <div class="collapse <?=!$active['reports']?'':'show'?>" id="ReportsSubMenu">
                    <ul class="flex-column pl-2 nav">
                        <a class="nav-link text-secondary py-0 <?= active('/monthly-reports') ?>" data-parent="#Reports" href="<?= BASEURL.'/monthly-reports' ?>">
                            Monthly Report
                        </a>
                        <a class="nav-link text-secondary py-0 <?= active('/quarterly-reports') ?>" data-parent="#Reports" href="<?= BASEURL.'/quarterly-reports' ?>">
                            Quarterly Report
                        </a>
                        <a class="nav-link text-secondary py-0 <?= active('/year-end-reports') ?>" data-parent="#Reports" href="<?= BASEURL.'/year-end-reports' ?>">
                            Year End Report
                        </a>
                        <a class="nav-link text-secondary py-0 <?= active('/custom-reports') ?>" data-parent="#Reports" href="<?= BASEURL.'/custom-reports' ?>">
                            Custom Report
                        </a>
                    </ul>
                </div>
            <?php } ?>

            <!-- User Management -->
            <?php if (hasRole(Role::Admin)) { ?>
                <a class="nav-link text-secondary <?= active('/manage-users') ?>" href="<?= BASEURL.'/manage-users' ?>">
                    <i class="fa fa-users fa-fw" aria-hidden="true"></i>User Management
                </a>
            <?php } ?>
        <?php } ?>
    </nav>
<!--    <a class="side-menu-welcome">Welcome, --><?//= $_SESSION["username"] ?><!--!</a>-->
</div>