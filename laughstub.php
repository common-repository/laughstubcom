<?php
ob_start();
/*
Plugin Name: LaughStub.com
Plugin URI: http://www.laughstub.com/wordpress
Description: This plugin allows any comedian or venue to embed their schedule and sell comedy tickets on their WordPress website. Maintain your schedule centrally and watch all your website have an up-do-date calendar of upcoming shows without doing any work at all.

Version: 2.0
Author: Ankit Chaudhary

Copyright (C) 2010 LaughStub

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/
?>

<?php
	
	//error_reporting(E_ALL);
	require_once(dirname(__FILE__).'/core/class-public.php') ;
	add_action('admin_head', array('LaughStubPlugin', 'addHeaderCode'), 1);
	add_action('wp_head', array('LaughStubPlugin', 'addHeaderCode'), 1);
	add_action("widgets_init", array('LaughStubPlugin', 'register'));
	register_activation_hook( __FILE__, array('LaughStubPlugin', 'activate'));
	register_deactivation_hook( __FILE__, array('LaughStubPlugin', 'deactivate'));

?>