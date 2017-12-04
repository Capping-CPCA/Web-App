<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Configures all the available routes in the system.
 *
 * Each route has a connected url, file, and title. The url is
 * what the user will type into the address bar. The file is
 * the relative location of the PHP file (based in the views
 * directory) to be displayed. The title is the title to be
 * displayed on the top of the page when the page is loaded.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.3.3
 * @since 0.1
 */

global $router;

$router->add('/', 'dashboard.php', 'Home');
$router->add('/dashboard', 'dashboard.php', 'Home');

$router->add('/login', 'login.php', 'Login');
$router->add('/logout', 'logout.php', '');
$router->add('/create-account', 'create_account.php', 'Create Account');

# Agency Requests
$router->add('/agency-requests', 'agency-requests/agency_requests.php', 'Agency Requests');
$router->add('/participant-search', 'agency-requests/agency_requests_results.php', 'Search Results');
$router->add('/ps-view-participant', 'agency-requests/view_participant.php', 'View Participant');
$router->add('/ps-edit-participant', 'agency-requests/edit_participant.php', 'Edit Participant');

# Manage Curricula & Classes
$router->add('/curricula', 'curricula/curricula.php', 'Curricula');
$router->add('/classes', 'classes/classes.php', 'Classes');
$router->add('/locations', 'locations/locations.php', 'Locations');

# Account Settings
$router->add('/account-settings', 'account/account_settings.php', 'Account Settings');

# User Management
$router->add('/manage-users', 'user/user_management.php', 'User Management');

#Reports
$router->add('/quarterly-reports', 'reports/quarterly.php', 'Quarterly Reports');
$router->add('/year-end-reports', 'reports/half_year.php', 'Year-End Reports');
$router->add('/monthly-reports', 'reports/monthly_report.php', 'Monthly Reports');
$router->add('/custom-reports', 'reports/custom_reports.php', 'Custom Report');
$router->add('/custom-reports-table', 'reports/custom_reports_table.php', 'Custom Report');

#Forms
$router->add('/referral-form', 'forms/referral_form.php', 'Referral Form');
$router->add('/self-referral-form', 'forms/self_referral_form.php', 'Initial Contact Form- Self-Referral');
$router->add('/intake-packet', 'forms/intake_packet.php', 'Intake Packet');
$router->add('/form-success', 'forms/form_success.php', 'Form Submitted');

# Attendance Application
$router->add('/attendance', 'attendance/attendance_dashboard.php', 'Attendance Dashboard');
$router->add('/new-class', 'attendance/new_class.php', 'New Attendance Sheet');
$router->add('/attendance-form', 'attendance/attendance_form.php', 'Attendance Form');
$router->add('/attendance-form-confirmation', 'attendance/attendance_form_confirmation.php', 'Confirm Attendance');
$router->add('/historical-class-view', 'attendance/historical_class_view.php', 'Recent Class View');
$router->add('/historical-class-search', 'attendance/historical_class_search.php', 'Historical Class Search');
$router->add('/historical-class-search-results', 'attendance/historical_class_search_results.php', 'Historical Class Search Results');
$router->add('/historical-class-search-view', 'attendance/historical_class_search_view.php', 'Historical Class View');
$router->add('/attendance-form-confirmed', 'attendance/attendance_form_confirmed.php', 'Attendance Confirmed');
$router->add('/attendance-edit-participant', 'attendance/edit_participant.php', 'Edit Participant');
$router->add('/edit-class-info', 'attendance/attendance_edit_class_info.php', 'Change Class Information');

# Surveys
$router->add('/surveys', 'surveys/view_survey_results.php', 'Surveys');
