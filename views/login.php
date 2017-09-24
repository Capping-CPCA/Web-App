<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['role'] = Role::SuperAdmin;
    header('Location: ' . BASEURL . '/dashboard');
    die();
} else if (isset($_SESSION['username'])) {
    header('Location: ' . BASEURL . '/dashboard');
}

$hideMenu = true;
include('header.php');

?>

<div class="page-wrapper">
    <div class="jumbotron form-wrapper">
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
