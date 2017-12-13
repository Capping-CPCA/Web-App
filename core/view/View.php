 <?php

 /**
  * PEP Capping 2017 Algozzine's Class
  *
  * View object to hold basic functions for view switching.
  *
  * This class holds a basic display function to show specific
  * views (pages) of the application.
  *
  * @author Jack Grzechowiak
  * @copyright 2017 Marist College
  * @version 0.1
  * @since 0.1
  */
 class View {
     /* @var $viewLoader ViewLoader  */
     public function __construct($viewLoader) {
         $this->viewLoader = $viewLoader;
     }

     /**
      * Displays the data at the specified file
      * @param $viewName string filename based from /views folder
      * @throws Exception
      */
     public function display($viewName) {
         $this->viewLoader->load($viewName);
     }
 }