<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['role'] = 'admin';
    header('Location: ' . BASEURL . '/dashboard');
    die();
} else if (isset($_SESSION['username'])) {
    header('Location: ' . BASEURL . '/dashboard');
}

$hideMenu = true;
include('header.php');

?>

<div class="page-wrapper">
    <div class="jumbotron" style="max-width: 700px; width: 100%; margin: 0 auto; margin-top: 10px!important;">
        <form class="form" method="post" action="/login">
            <label for="username">Username</label>
            <input class="form-control username" type="text" name="username">
            <label for="password">Password</label>
            <input class="form-control password" type="password" name="password">
            <div class="form-footer submit">
                <button type="submit" class="btn cpca">Submit</button>
            </div>
        </form>
    </div>
</div>

<?php

include('footer.php');
