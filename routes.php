<?php

$router->add('/', 'dashboard.php', 'Home');
$router->add('/back', 'back.php', '');
$router->add('/dashboard', 'dashboard.php', 'Home');

$router->add('/login', 'login.php', 'Login');
$router->add('/logout', 'logout.php', '');

# Agency Requests
$router->add('/agency-requests', 'agency-requests/agency_requests.php', 'Agency Requests');
$router->add('/participant-search', 'agency-requests/agency_requests_results.php', 'Search Results');
$router->add('/view-participant', 'agency-requests/view_participant.php', 'View Participant');

# Manage Curricula & Classes
$router->add('/curricula', 'curricula/curricula.php', 'Curricula');
$router->add('/classes', 'classes/classes.php', 'Classes');
$router->add('/locations', 'locations/locations.php', 'Locations');

#Reports
$router->add('/quarterly-reports', 'reports/quarterly.php', 'Quarterly Reports');
$router->add('/year-end-reports', 'reports/half_year.php', 'Year-End Reports');
$router->add('/monthly-reports', 'reports/monthly_report.php', 'Monthly Reports');
$router->add('/custom-reports', 'reports/custom_reports.php', 'Custom Report');
$router->add('/custom-reports-table', 'reports/custom_reports_table.php', 'Custom Report');