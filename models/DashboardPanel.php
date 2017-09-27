<?php

class DashboardPanel {
    var $link, $title, $subtitle, $img;

    function __construct($link, $title, $subtitle, $img) {
        $this->link = $link;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->img = $img;
    }

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