<?php
/**
 * Directory
 * config file
 * @author		John Pennypacker <jpennypacker@uri.edu>
 *
 */
 
 	// set the base path of the application, omit the trailing slash
 	// for example: 'https://sample.local/directory';
	$base_path = rtrim($_SERVER['SCRIPT_URI'], '/');

	set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/inc');
	set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/tpl');

	// @see use_privileged_view()
	define('SHOW_PRIVILEGED_VIEW_FROM_CAMPUS_IP', TRUE); // set to TRUE or FALSE
	define('SHOW_PARTIAL_RESULTS', FALSE); // set to TRUE or FALSE

	// NB: avatars doesn't work... code is from 2014 and didn't work well then
	define('SHOW_AVATARS', FALSE); // set to TRUE or FALSE

	// VALID LDAP INFO
	$privileged_view_ldap_host = '';
	$privileged_view_ldap_dn = ''; 
  $privileged_view_ldap_password = '';

	$page_title = 'Directory'; // default page title in case it isn't overridden
	$page_headline = 'Directory Search'; // default page headline
	$display_description = 'People Lookup Service';
	
	$show_forms = TRUE; // shows the search form on the page template

	// Breacrumb links on the page
	$breadcrumbs = array(
		array('href' => 'https://www.example.edu', 'text' => 'Example'),
	);
	
	$cache_buster = '?cache=' . strtotime( 'now' ); // a cache-busting query string to encourage css and js refresh

	// advanced search fields
	$advanced_search_fields = array();
	$advanced_search_fields['givenname'] = 'First Name';
	$advanced_search_fields['sn'] = 'Last Name';
	//$advanced_search_fields['title'] = 'Title';
	$advanced_search_fields['ou'] = 'Department';
	$advanced_search_fields['telephonenumber'] = 'Phone';
	$advanced_search_fields['mail'] = 'Email';

	// advanced search types
	$advanced_search_types = array(
		 'contains' => 'contains'
		,'begins' => 'begins with'
		,'ends' => 'ends with'
		,'exact' => 'is'
	);

	// include common functions
	include_once 'functions.php';
	set_error_handler('exception_error_handler'); // use a customized function to handle errors
	
	// include the ldap class
	include_once 'ldap.class.php';

	// initialize errors and messages
	$errors = array();
	$messages = array();
	
?>