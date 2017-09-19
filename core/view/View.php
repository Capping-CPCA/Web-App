 <?php

 class View {
     /* @var $viewLoader ViewLoader  */
     public function __construct($viewLoader) {
         $this->viewLoader = $viewLoader;
     }

     public function display($viewName) {
         $this->viewLoader->load($viewName);
     }
 }