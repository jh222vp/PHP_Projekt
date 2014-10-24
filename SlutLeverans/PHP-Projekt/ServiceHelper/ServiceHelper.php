<?php
class ServiceHelper{
	//retunerar användaragenten från webläsaren
	public function userAgent(){
		return $_SERVER['HTTP_USER_AGENT'];
	}
}