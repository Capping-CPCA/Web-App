<div class="side-menu">
    <nav class="nav flex-column flex-nowrap">
        <a class="nav-link text-secondary <?= active($route, 'dashboard.php') ?>" href="<?= BASEURL.'/dashboard' ?>">Home</a>
        <a class="nav-link text-secondary <?= active($route, 'agency_requests.php') ?>" href="<?= BASEURL.'/agency-requests' ?>">Agency Requests</a>
        <a class="nav-link text-secondary collapsed" data-toggle="collapse" data-target="#ParticipantFormsSubMenu" href="#ParticipantForms">Participant Referrals & Intake</a>
        <div class="collapse" id="ParticipantFormsSubMenu">
            <ul class="flex-column pl-2 nav">
                <a class="nav-link text-secondary py-0" data-parent="#ParticipantForms" href="#">Referral Form</a>
                <a class="nav-link text-secondary py-0" data-parent="#ParticipantForms" href="#">Intake Packet</a>
            </ul>
        </div>
        <a class="nav-link text-secondary collapsed" data-toggle="collapse" data-target="#ClassActivitySubMenu" href="#ClassActivity">Class Activity</a>
        <div class="collapse" id="ClassActivitySubMenu">
            <ul class="flex-column pl-2 nav">
                <a class="nav-link text-secondary py-0" data-parent="#ClassActivity" href="#">Record Attendance</a>
                <a class="nav-link text-secondary py-0" data-parent="#ClassActivity" href="#">Record Survey Results</a>
                <a class="nav-link text-secondary py-0" data-parent="#ClassActivity" href="#">View Survey Results</a>
            </ul>
        </div>
        <a class="nav-link text-secondary collapsed" data-toggle="collapse" data-target="#ManageCoursesSubMenu" href="#ManageCourses">Manage Curricula & Classes</a>
        <div class="collapse" id="ManageCoursesSubMenu">
            <ul class="flex-column pl-2 nav">
                <a class="nav-link text-secondary py-0 collapsed" data-parent="#ManageCourses" data-toggle="collapse" data-target="#ManageCurriculaSubMenu" href="#ManageCurricula">Curricula</a>
                <div class="collapse" id="ManageCurriculaSubMenu">
                    <ul class="flex-column pl-2 nav">
                        <a class="nav-link text-secondary py-0" data-parent="#ManageCurricula" href="#">View</a>
                        <a class="nav-link text-secondary py-0" data-parent="#ManageCurricula" href="#">Modify</a>
                        <a class="nav-link text-secondary py-0" data-parent="#ManageCurricula" href="#">Create</a>
                        <a class="nav-link text-secondary py-0" data-parent="#ManageCurricula" href="#">Archive</a>
                        <a class="nav-link text-secondary py-0" data-parent="#ManageCurricula" href="#">View/Restore Archive</a>
                    </ul>
                </div>
                <a class="nav-link text-secondary py-0 collapsed" data-parent="#ManageCourses" data-toggle="collapse" data-target="#ManageClassesSubMenu" href="#ManageClasses">Classes</a>
                <div class="collapse" id="ManageClassesSubMenu">
                    <ul class="flex-column pl-2 nav">
                        <a class="nav-link text-secondary py-0" data-parent="#ManageClasses" href="#">View</a>
                        <a class="nav-link text-secondary py-0" data-parent="#ManageClasses" href="#">Modify</a>
                        <a class="nav-link text-secondary py-0" data-parent="#ManageClasses" href="#">Create</a>
                        <a class="nav-link text-secondary py-0" data-parent="#ManageClasses" href="#">Archive</a>
                        <a class="nav-link text-secondary py-0" data-parent="#ManageClasses" href="#">View/Restore Archive</a>
                    </ul>
                </div>
                <a class="nav-link text-secondary py-0 collapsed" data-parent="#ManageCourses" data-toggle="collapse" data-target="#ManageLocationsSubMenu" href="#ManageLocations">Locations</a>
                <div class="collapse" id="ManageLocationsSubMenu">
                    <ul class="flex-column pl-2 nav">
                        <a class="nav-link text-secondary py-0" data-parent="#ManageLocations" href="#">View</a>
                        <a class="nav-link text-secondary py-0" data-parent="#ManageLocations" href="#">Modify</a>
                        <a class="nav-link text-secondary py-0" data-parent="#ManageLocations" href="#">Create</a>
                        <a class="nav-link text-secondary py-0" data-parent="#ManageLocations" href="#">Archive</a>
                        <a class="nav-link text-secondary py-0" data-parent="#ManageLocations" href="#">View/Restore Archive</a>
                    </ul>
                </div>
            </ul>
        </div>
        <a class="nav-link text-secondary collapsed" data-toggle="collapse" data-target="#ReportsSubMenu" href="#Reports">Reports</a>
        <div class="collapse" id="ReportsSubMenu">
            <ul class="flex-column pl-2 nav">
                <a class="nav-link text-secondary py-0" data-parent="#Reports" href="#">Monthly Report</a>
                <a class="nav-link text-secondary py-0" data-parent="#Reports" href="#">Quarterly Report</a>
                <a class="nav-link text-secondary py-0" data-parent="#Reports" href="#">Year End Report</a>
                <a class="nav-link text-secondary py-0" data-parent="#Reports" href="#">Custom Report</a>
            </ul>
        </div>
        <a class="nav-link text-secondary collapsed" data-toggle="collapse" data-target="#UserManagementSubMenu" href="#UserManagement">User Management</a>
        <div class="collapse" id="UserManagementSubMenu">
            <ul class="flex-column pl-2 nav">
                <a class="nav-link text-secondary py-0" data-parent="#UserManagement" href="#">Add New User</a>
                <a class="nav-link text-secondary py-0" data-parent="#UserManagement" href="#">Revoke User</a>
                <a class="nav-link text-secondary py-0" data-parent="#UserManagement" href="#">Change/Reset Password</a>
            </ul>
        </div>
    </nav>
</div>