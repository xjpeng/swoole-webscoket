<?php
class Route{
   public static function run(){
	   $m = filter_input(INPUT_GET,'m') ?: 'Action';
	   $a = filter_input(INPUT_GET,'a') ?: 'index';
	   $class = new $m;
	   $class->$a();
   }
}
?>