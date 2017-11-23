<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Breadcrumbs for easier navigation.
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
	 
	public $route = null;
	
   
	public function setRoute($route){
		$this->route =$route;
	}
	//adds a page to the history array 
	public function addPage(){
		$arr = null;
		//checks to see if the history array has something in it
		if($this::checkLength()){
				//add route to the history array
				$this::addRouteToBreadcrumb();
		}else{
			if($this::checkEntries()){
				$arr = [$this->route['title'] => $this->route['url']];
				$_SESSION['history'][] = $arr;
			}
		}
	}
	//grabs the last key of history array
	public function getLastKey(){
		if($this::checkLength()){
			return key($_SESSION['history'][ (sizeof($_SESSION['history']) -1)]);
		}else{
			
		}
	}
	//loops through history array to generate links/labels for each breadcrumb
	public function displayBreadcrumbs(){
		$this::removeLink();
			foreach($_SESSION['history'] as $keys =>$values){
			if($keys == 0){
				foreach($values as $names => $urls){
					echo "<form action='$urls' method=\"POST\" ><input name='remove' type='hidden' value='$keys'><button class='cpca-link'  type='submit' > > ".$names." </button></form>";
				}
			}else{
				foreach($values as $names => $urls){
					echo "<form action='$urls' method=\"POST\" ><input name='remove'  type='hidden' value='$keys'><button class='cpca-link'  type='submit' > | ".$names." </button></form>";
				
}
			}
		}
	}
	//if the link is valid, add it to the history array
	public function addRouteToBreadcrumb(){
		if($this::checkEntries()){
				$arr = [$this->route['title'] => $this->route['url']];
				$_SESSION['history'][] = $arr;
		}
	}
	//checks the current length of the history array
	public function checkLength(){
		if(empty($_SESSION['history'])){
			return false;
		}else{
			return true;
		}
	}
	//validates the links before they are added to the history array
	public function checkEntries(){
		if($this->route['title'] != "Page not found!"  && $this->route['title'] != $this::getLastKey() ){
			return true;
		}else{
			return false;
		}	
	}
	//clears history array
	public function clearHistory(){
		$_SESSION['history'] = array();
	}
	//when a user clicks a specific  link, all entries after the link will be removed
	public function removeLink(){
		if(isset($_POST['remove'])){
			if($this->route['title'] == "Agency Requests"){
				unset($_POST);
			}
			$arrPos= $_POST['remove'];
			$newHistory =  array_slice($_SESSION['history'], 0, $arrPos+1);  
			$_SESSION['history'] = $newHistory;
		}else{
			if($this->route['title'] == 'Login'){
				$this::clearHistory();
			}
			if($this->route['title'] == 'Home'){
			$newHistory =  array_slice($_SESSION['history'], 0, 1);  
			$_SESSION['history'] = $newHistory;
			}
			
		}
	}
	
	

}
