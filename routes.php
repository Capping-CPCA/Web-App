<?php

$router->add('/', 'dashboard.php', 'Home');
$router->add('/dashboard', 'dashboard.php', 'Home');
$router->add('/agency-requests', 'agency-requests/agency_requests.php', 'Agency Requests');
$router->add('/login', 'login.php', 'Login');
$router->add('/logout', 'logout.php', '');
$router->add('/db-search', 'agency-requests/database_search.php', 'Search Results');
$router->add('/view-participant', 'agency-requests/view_participant.php', 'View Participant');