<?php
include_once('../models/DashboardPanel.php');

$roleViews = [
    Role::User => [
        new DashboardPanel("/attendance", "Record Attendance", "Participants", "clock-o"),
        new DashboardPanel("/referral-form", "Participant Referrals", "Participants", "files-o"),
        new DashboardPanel("/intake-packet", "Participant Intake", "Classes", "file-text-o"),
        new DashboardPanel("/agency-requests", "Agency Requests", "Participants", "search"),
    ],
    Role::Coordinator => [
        new DashboardPanel("/classes", "Class Management", "Classes", "book"),
        new DashboardPanel("/curricula", "Curricula Management", "Classes", "university"),
        new DashboardPanel("/locations", "Location Management", "Classes", "map-marker"),
        new DashboardPanel("/agency-requests", "Agency Requests", "Participants", "search"),
    ],
    Role::Admin => [
        new DashboardPanel("/manage-users", "User Management", "Employees", "users"),
        new DashboardPanel("/referral-form", "Participant Referrals", "Participants", "files-o"),
        new DashboardPanel("/intake-packet", "Participant Intake", "Classes", "file-text-o"),
        new DashboardPanel("/agency-requests", "Agency Requests", "Participants", "search"),
    ],
    Role::Superuser => [
        new DashboardPanel("/curricula", "Manage Curricula", "Classes", "university"),
        new DashboardPanel("/locations", "Manage Locations", "Classes", "map-marker"),
        new DashboardPanel("/classes", "Manage Classes", "Classes", "book"),
        new DashboardPanel("/manage-users", "User Management", "Employees", "users"),
        new DashboardPanel("/agency-requests", "Agency Requests", "Participants", "search"),
    ]
];

include('header.php');

?>

    <div id="dashboard-wrapper" class="d-flex flex-row justify-content-center flex-wrap">
        <?php
        if ($_SESSION['role'] != Role::NewUser) {
            /* @var $panel DashboardPanel */
            foreach ($roleViews[$_SESSION['role']] as $panel) {
                $panel->createPanel();
            }
        } else {
            ?>
            <div class="jumbotron align-self-center text-center" style="max-width: 700px; margin: 0 auto; width: 100%">
                <h1 class="display-3" style="color: #5C629C"><i class="fa fa-child"></i></h1>
                <h1 class="display-3">Welcome!</h1>
                <p class="lead">You currently have no role assigned, please see your supervisor.</p>
            </div>
            <?php
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