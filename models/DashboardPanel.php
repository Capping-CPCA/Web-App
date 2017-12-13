<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Template for the dashboard panels.
 *
 * This class allows simple construction and display
 * of the dashboard panels present on the home page. Calling
 * the <code>createPanel()</code> function displays the panel
 * to the screen.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.1
 * @since 0.1
 */
class DashboardPanel {
    var $link, $title, $subtitle, $img;

    function __construct($link, $title, $subtitle, $img) {
        $this->link = $link;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->img = $img;
    }

    /**
     * Displays the panel
     */
    function createPanel() { 
        echo
            "<a class='dashboard-panel justify-content-center d-flex flex-column align-self-stretch'" .
               "href='" . $this->link . "'>" .
                "<div class='img-div'>" .
                    "<i class='fa fa-" . $this->img . "' aria-hidden='true'></i>" .
                "</div>" .
                "<h2 class='button-main-title'>" . $this->title . "</h2>" .
                "<p class='button-sub-title'>" . $this->subtitle . "</p>" .
            "</a>";
    }
}