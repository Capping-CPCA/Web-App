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
	 
	//public $route = null;
	
	public function setRoute($route){
		$this->route =$route;
	}
	public function addPage(){
		if($this::checkLength()){
			echo "array is populated";
		}else{
			echo "array is empty";
		}
		/*	$arr = [$route['title'] => $route['url']];
			$_SESSION['history'][] = $arr;
		}else{
		if($route['title'] != "Page not found!"  && $route['title'] != $this::getLastKey() ){
			//$_SESSION['history'][] = $route['title'];
			
			$arr = [$route['title'] => $route['url']];
			//key($_SESSION['history'][sizeof($_SESSION['history'])]);
			//print_r(key($_SESSION['history'][0]));
			$_SESSION['history'][] = $arr;
			
			echo sizeof($_SESSION['history']);
			
			}
			//$_SESSION['history'][] = $route['title'];
			//$_SESSION['history']= array();
			//var_dump($_SESSION['history']);
			//print_r($_SESSION['history']);
			foreach($_SESSION['history'] as $keys =>$values){
				if($keys == 0){
					foreach($values as $names => $urls){
							echo " > <a class='cpca-link' href='$urls'>".$names."</a> ";
					}
				//	echo " > ".$values." ";
				}else{
					foreach($values as $names => $urls){
							echo " | <a class='cpca-link' href='$urls'> ".$names." </a> ";
					}
					//echo " | ".$values." ";
				}
			}
		}*/
	}
	public function getLastKey(){
		return key($_SESSION['history'][ (sizeof($_SESSION['history']) -1)]);
	}
	
	public function displayBreadcrumbs(){
		print_r($_SESSION['history']);
		print_r($this->route);
	}
	
	public function checkDuplicates(){
		$check = false;
		return $check;
	}
	
	public function checkLength(){
		if(empty($_SESSION['history'])){
			return false;
		}else{
			return true;
		}
	}
	
	public function clearHistory(){
		$_SESSION['history'] = array();
	}
	

}


/*$checkKey = null;
		if(empty($_SESSION['history'])){
			
			$arr = [$route['title'] => $route['url']];
			$_SESSION['history'][] = $arr;
		}else{
			$checkKey= key($_SESSION['history'][ (sizeof($_SESSION['history']) -1)]);
		if($route['title'] != "Page not found!"  && $route['title'] != $checkKey){
			//$_SESSION['history'][] = $route['title'];
			
			$arr = [$route['title'] => $route['url']];
			//key($_SESSION['history'][sizeof($_SESSION['history'])]);
			//print_r(key($_SESSION['history'][0]));
			$_SESSION['history'][] = $arr;
			
			echo sizeof($_SESSION['history']);
			
			}
			//$_SESSION['history'][] = $route['title'];
			//$_SESSION['history']= array();
			//var_dump($_SESSION['history']);
			//print_r($_SESSION['history']);
			foreach($_SESSION['history'] as $keys =>$values){
				if($keys == 0){
					foreach($values as $names => $urls){
							echo " > <a class='cpca-link' href='$urls'>".$names."</a> ";
					}
				//	echo " > ".$values." ";
				}else{
					foreach($values as $names => $urls){
							echo " | <a class='cpca-link' href='$urls'> ".$names." </a> ";
					}
					//echo " | ".$values." ";
				}
			}
			
		}*/