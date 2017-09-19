<?php

authorizedPage();

include('header.php');
?>

<div class="page-wrapper">
    <div class="jumbotron" style="max-width: 700px; width: 100%; margin: 0 auto">
        <h4 class="secondary-title">Search for Participant</h4>
        <br />
        <form class="search-agency">
            <div class="form-group">
                <label for="lName">Last Name: </label>
                <input type="text" class="form-control" id="lName">
            </div>
            <div class="form-group">
                <label for="fFname">First Name: </label>
                <input type="text" class="form-control" id="fFname">
            </div>
            <button type="submit" class="btn cpca">Submit</button>
        </form>
    </div>
</div>

<div class="modal fade" id="studentModal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Result for Student Name</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img class="icon-img" src="https://x1.xingassets.com/assets/frontend_minified/img/users/nobody_m.original.jpg">
                <span><b>Name: </b> John Smith</span><br>
                <span><b>Status: </b> active</span><br>
                <span><b>Notes: </b> "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."</span><br>
                <span><b>Other: </b> something here i guess</span><br><br />
                <button type="button" class="btn cpca">Download as PDF</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(".search-agency").submit(function(e){
        e.preventDefault();
        $("#studentModal").modal();
    });
</script>

<?php
include('footer.php');
?>