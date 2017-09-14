<?php
/**
 * This is the side menu that pops out when the hamburger button is pressed
 * in the header. Add / remove to this list to alter the list of links.
 *
 * TODO: Update active tab based on current page
 * TODO: Manage permissions for showing pages based on role
 */
?>
<div class="side-menu hidden">
    <button type="button" class="close" aria-label="Close" onclick="hideMenu()">
        <span aria-hidden="true">&times;</span>
    </button>
    <nav class="nav flex-column">
        <h1 id="menu-title" class="display-4">Menu</h1>
        <a class="nav-link text-secondary active" href="#">Home</a>
        <a class="nav-link text-secondary" href="#">Forms</a>
        <a class="nav-link text-secondary" href="#">Participants</a>
        <a class="nav-link text-secondary" href="#">Classes</a>
        <a class="nav-link text-secondary" href="#">Reports</a>
        <a class="nav-link text-secondary" href="#">Surveys</a>
        <div class="fill-spacer"></div>
        <a class="nav-link text-secondary align-self-end" href="#">Admin Tools</a>
    </nav>
</div>