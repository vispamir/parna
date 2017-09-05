<?php
class insertAction {
	public function content(){
		if($args = func_get_args())
			return 'Insert new book by : '. json_encode($args);
		return 'Insert new book !';
	}
}