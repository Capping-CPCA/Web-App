<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * A collection of PHP helper functions.
 *
 * These functions are generic functions that can be used
 * anywhere in the application to make development easier.
 * Add a function to this file if it is being used in
 * more than one file.
 *
 * @author Vallie Joseph
 * @copyright 2017 Marist College
 * @version 0.7.1
 * @since 0.7.1
 */
 
 class BreadCrumbs{
	 
	
	public function addPage($page){
	//	 $_SESSION["history"]->append($page);
		//return $this->historyArray;
	}
	public function getArray(){
		//print_r($this->$_SESSION["history"]);
	}

}


/*
if($route['title'] != "Page not found!"){
			$_SESSION['history'][] = $route['title'];
			}
			//$_SESSION['history'][] = $route['title'];
			$_SESSION['history']= array();
			var_dump($_SESSION['history']);
			*/