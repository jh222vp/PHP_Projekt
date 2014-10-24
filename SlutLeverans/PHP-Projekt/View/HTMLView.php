<?php

class HTMLView{
	
	public function echoHTML($body){
		echo "<!DOCTYPE html>
				<meta charset = \'UTF-8'\>
				<link rel='stylesheet' type='text/css' media='all' href='CSS/style.css' />
				<html>
				<body>
				$body
				</body>
				</html>";
	}
}