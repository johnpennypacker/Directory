<?php
/**
 * Directory
 *
 * an api script to pull data in alternative formats
 * 
 * @author		John Pennypacker <jpennypacker@uri.edu>
 *
 */
 
	require_once 'config.php';
	

	function get_uid_from_url() {
		$url_bits = explode('/', $_SERVER['REQUEST_URI']);
		$k = array_search('uid', explode('/', $_SERVER['REQUEST_URI']));
		if(isset($url_bits[$k+1])) {
			return $url_bits[$k+1];
		} else {
			return FALSE;
		}
	}
	// pull the variables from the URL.
	$eppn = get_uid_from_url();
	
	if(!empty($eppn)) {
		$LDAPPeople = new LDAPPeople(get_ldap_credentials());
		if($LDAPResult = $LDAPPeople->SearchByUID($eppn)) {
			$results_array = $LDAPResult->ResultsToArray();
			$results = $LDAPResult->ResultsToHCard();
			$LDAPPeople->Close();
		}

		if($LDAPResult->GetNumberOfRows() == 0) {
			$errors[] = 'No match was found.';
		} else if($LDAPResult->GetNumberOfRows() != 1) {
			$errors[] = 'More than one result found.  Please refine your search'; // this should never happen
		}

	} else {
		$errors[] ='No ID specified';
	}

	if(!use_privileged_view()) {
		$parsed_path = parse_url($base_path);
		$url = str_replace($parsed_path['path'], '', $_SERVER['REQUEST_URI']);
		$authentication_prompt = 'Members of the university community can <a href="' . $base_path . '/authenticate?destination='. urlencode($url). '">log in to view more information</a>.';
	}


	$name = $results_array[0]['displayname'];
	if(empty($name)) {
		$name = $results_array[0]['givenname'] . ' ' . $results_array[0]['sn'];
	}
	$page_title = 'Directory Page for ' . $name;
	$page_headline = $name;
	$show_forms = FALSE;

	$display_title = $name;

	$breadcrumbs[] = array('href' => $base_path, 'text' => 'Directory');


	include 'header.tpl.php';
	include 'main.tpl.php';
	include 'footer.tpl.php';
?>
