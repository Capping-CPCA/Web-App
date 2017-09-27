<?php

class Notification {

    function __construct($title, $subtitle, $type = 'info') {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->type = $type;
    }

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