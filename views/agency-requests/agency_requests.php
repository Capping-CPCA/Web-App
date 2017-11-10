<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Allows a user to search for participants in the database.
 *
 * The main purpose of this page is to allow users a quick way to
 * access participant information (specifically agency requests). The
 * page uses a live search that
 *
 * @author Vallie Joseph
 * @copyright 2017 Marist College
 * @since 0.1.2
 * @version 0.1.5
 */
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
                    <input type="text" class="form-control user-search" name="searchquery" placeholder="Begin typing participant's name...">
                    <ul class='list-group'></ul>
                </div>
                <button type="submit" class="btn cpca form-control submit-search">Submit</button>
            </form>
        </div>
    </div>

    <script>
        //Disables the default browser autocomplete
        $( ".search-agency" ).attr( "autocomplete", "off" );

        //construct live search, needs the user input field, where search results will go,
        //desired class name of search result elements, url for where ajax is sending the request, and
        //type of data ajax is returning
        setInputListener($(".user-search"),$(".list-group"),"<li class='list-group-item suggestion' tabindex='0'></li>", "/participant-search", "html");

        //Set the ajax submit method to either put or get, will default to GET if left blank
        setMethod("PUT");

        //set what class you're lookin for in the search results
        setResultFilter('.list-group-item span');

        //search dynamically with every user input
        $(".user-search").keyup(function(){
            $('.search-agency').liveSearch();
        });
    </script>

<?php
include('footer.php');
?>