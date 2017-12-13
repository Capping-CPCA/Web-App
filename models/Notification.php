<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Template for the notification messages.
 *
 * This class allows simple construction of a notification
 * popup that can be dismissed. These pop ups can be either a
 * success, info, warning, or error notification depending on the
 * type specified.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.1
 * @since 0.1
 */
class Notification {

    function __construct($title, $subtitle, $type = 'info') {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->type = $type;
    }

    /**
     * Displays the notification
     */
    function display() {
        echo
            '<div class="alert alert-'.$this->type.' alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>'.$this->title.'</strong> '.$this->subtitle.'
            </div>';
    }
}