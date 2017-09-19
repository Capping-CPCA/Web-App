<?php

$router->add('/', 'dashboard.php', 'Home');
$router->add('/dashboard', 'dashboard.php', 'Home');
$router->add('/agency-requests', 'agency_requests.php', 'Agency Requests');
$router->add('/test-page', 'test_page.php', 'Test Page');
$router->add('/login', 'login.php', 'Login');
$router->add('/logout', 'logout.php', '');