<?php
class book {
	public function menu(){
		$items['book'] = array(
			'title' => 'Books',
			'callback' => 'book_management',
			'file' => null,
		);
		
		$items['book/list'] = array(
			'title' => 'List books',
			'callback' => 'book_list',
			'arguments' => array(2), 
			'file' => null,
		);
		return $items;
	}
	
	public function book_management(){
		return 'Content of book management page.';
	}
	
	public function book_list($arg){
		if($arg)
			return 'List of books by : '. $arg;
		return 'List of books !';
	}
}