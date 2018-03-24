<?php
class dwSession{
	static protected $session = array();
	
	public function get($key=NULL){
		if( empty(self::$session) ){
			@session_start();
			self::$session = $_SESSION[$GLOBALS['app_id']];
			session_write_close();
		}
		
		if( empty($key) ) return self::$session;
		$arr = explode('.', $key);
		switch( count($arr) ){
			case 1 : 
				if( isset(self::$session[ $arr[0] ])) {
					return self::$session[ $arr[0] ];
				}
				break;
			case 2 : 
				if( isset(self::$session[ $arr[0] ][ $arr[1] ])) {
					return self::$session[ $arr[0] ][ $arr[1] ];
				}
				break;
			case 3 : 
				if( isset(self::$session[ $arr[0] ][ $arr[1] ][ $arr[2] ])) {
					return self::$session[ $arr[0] ][ $arr[1] ][ $arr[2] ];
				}
				break;						
			default: break;
		}
		return NULL;
	}
	
	public function set($key, $value){
		$arr = explode('.', $key);
		switch( count($arr) ){
			case 1 : 
				self::$session[ $arr[0] ] = $value;
				break;
			case 2 : 
				self::$session[ $arr[0] ][ $arr[1] ] = $value;
				break;
			case 3 : 
				self::$session[ $arr[0] ][ $arr[1] ][ $arr[2] ] = $value;
				break;					
			default: return false;
		}
		
		@session_start();
		$_SESSION[$GLOBALS['app_id']] = self::$session;
		session_write_close();
		
		return true;
	}
}