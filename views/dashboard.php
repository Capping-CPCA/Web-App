<?php
include_once('../models/DashboardPanel.php');

authorizedPage();

if (isset($_GET['role'])) {
    $_SESSION['role'] = $_GET['role'];
}

$roleViews = [
    "default" => [
        new DashboardPanel(BASEURL."/agency-requests", "Agency Requests", "Participants", "img/004-magnifier.png"),
        new DashboardPanel(BASEURL."/test-page", "Option 2", "Group", "img/003-multiple-users-silhouette.png"),
        new DashboardPanel("#", "Option 3", "Group", "img/002-notepad.png"),
        new DashboardPanel("#", "Option 4", "Group", "img/001-line-chart.png"),
    ],
    "facilitator" => [
        new DashboardPanel("#", "Participant Referrals", "Participants", "img/002-notepad.png"),
        new DashboardPanel("#", "Class Activity", "Classes", "img/003-multiple-users-silhouette.png"),
        new DashboardPanel(BASEURL."/agency-requests", "Agency Requests", "Participants", "img/004-magnifier.png"),
        new DashboardPanel("#", "Participant Intake", "Participants", "img/002-notepad.png"),
    ],
    "admin" => [
        new DashboardPanel("#", "Manage Curricula and Classes", "Classes", "img/002-notepad.png"),
        new DashboardPanel("#", "Manage Locations", "Classes", "img/002-notepad.png"),
        new DashboardPanel("#", "Reports", "Reporting", "img/001-line-chart.png"),
        new DashboardPanel("#", "User Management", "Participants", "img/003-multiple-users-silhouette.png"),
    ],
    "super admin" => [
        new DashboardPanel("#", "Manage Curricula and Classes", "Classes", "img/002-notepad.png"),
        new DashboardPanel("#", "Manage Locations", "Classes", "img/002-notepad.png"),
        new DashboardPanel("#", "Manage Courses", "Participants", "img/002-notepad.png"),
        new DashboardPanel("#", "Manage Participants", "Users", "img/003-multiple-users-silhouette.png"),
        new DashboardPanel("#", "User Management", "Users", "img/003-multiple-users-silhouette.png"),
    ]
];

include('header.php');
?>

    <div id="dashboard-wrapper" class="d-flex flex-row justify-content-center flex-wrap">
        <?php
        /* @var $panel DashboardPanel */
        foreach ($roleViews[$_SESSION['role']] as $panel) {
            $panel->createPanel();
        }
        ?>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){

            showTutorial('menu');

            $("#login-btn").click(function(){
                $("#myModal").modal()
                $("#myModal").on('hidden.bs.modal', function () {
                    var username = $(".username").val();
                    $(".navbar-right").empty();
                    $(".navbar-right").html("<span class='userLoggedIn'>Welcome, "+username+"!</span>");
                })
            })
        });
    </script>

<?php
include('footer.php');
?>