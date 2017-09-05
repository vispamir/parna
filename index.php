<?php
	/**
	* New project with custom framework
	* Start by boot
	* Root directory of installation.
	*/
	define('PROJECT_ROOT', getcwd());
	require_once(PROJECT_ROOT .'/includes/boot.php');
	$boot = new boot;
?>
