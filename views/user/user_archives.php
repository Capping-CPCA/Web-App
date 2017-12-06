<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Page that displays archived employees.
 *
 * This page displays archived employees
 * in a list where superusers can restore
 * them.
 *
 * @author Michelle Crawley
 * @copyright 2017 Marist College
 * @version 0.3.3
 * @since 0.1
 */

global $db, $route, $params, $view;

# Get all archived employee names and ids
$res = $db->query("SELECT firstname, lastname, employeeid FROM people, employees ".
    "WHERE employees.employeeid = people.peopleid AND employees.df = TRUE ORDER BY lastname, firstname", []);
include ('header.php');
?>
    <div style="width: 100%">
        <button class="cpca btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
        <form class="jumbotron form-wrapper mb-3">
            <?php
            # Displays the archived employees
            if (pg_num_rows($res) > 0) { ?>
                <h3 class="text-center" style="font-weight: 300;color: #333">Archived Users</h3>
                <?php
                while ($user = pg_fetch_assoc($res)) {
                    ?>
                    <div class="card mb-2 user-card">
                        <div class="p-3 card-body d-flex flex-row justify-content-start">
                            <p class="mb-0 align-self-center"
                               style="flex: 1"><?= $user['lastname'] . ', ' . $user['firstname'] ?></p>
                            <div class="text-right align-middle">
                                <a href="/manage-users/restore/<?= $user['employeeid'] ?>">
                                    <button type='button' name="restore" class="btn outline-cpca btn-sm ml-2">
                                        Restore
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                # Displays a pretty message if there are no archived employees
            } else { ?>
                <div class="w-100 d-flex flex-column justify-content-center text-center">
                    <h3 class="display-3 text-secondary" style="font-size: 40px;"><i
                                class="fa fa-exclamation-circle"></i></h3>
                    <h3 class="display-3 text-secondary" style="font-size: 40px;">No Users Archived.</h3>
                </div>
            <?php }
            pg_free_result($res);
            ?>
        </form>

    </div>
<?php
include ('footer.php');