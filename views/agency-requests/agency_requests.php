<?php
authorizedPage();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    # send search GET request with params to /participant-search
    $search = isset($_POST['searchquery']) ? $_POST['searchquery'] : '';
    header("Location: /participant-search/$search");
    die();
}

include('header.php');
?>
    <div class="page-wrapper">
        <div class="jumbotron" style="max-width: 700px; width: 100%; margin: 0 auto" >
            <h4 class="secondary-title">Search for Participants</h4>
            <br />
            <form class="search-agency" method="POST" action="/agency-requests">
                <div class="form-group">
                    <input type="text" class="form-control" name="searchquery" placeholder="Begin typing participant's name...">
                </div>
                <button type="submit" class="btn cpca form-control">Submit</button>
            </form>
        </div>
    </div>


<?php
include('footer.php');
?>