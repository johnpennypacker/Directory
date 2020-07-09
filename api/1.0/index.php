<?php
/**
 * Directory
 *
 * an api script to pull data in alternative formats
 * 
 * @author		John Pennypacker <jpennypacker@uri.edu>
 *
 */
 
	require_once '../../config.php';
	
	// pull the variables from the URL.
	// output format
	$output = 'json';
	if($_GET['output'] == 'vcard') {
		$output = 'vcard';
	}
	if($_GET['output'] == 'hcard') {
		$output = 'hcard';
	}
	
	//world-view override
	$world = (isset($_GET['world']));
	
	$LDAPPeople = new LDAPPeople(get_ldap_credentials($world));

	// assume single user
	if(isset($_GET['eppn'])) {
		if($LDAPResult = $LDAPPeople->SearchByEPPN($_GET['eppn'])) {
			$results = $LDAPResult->ResultsToArray();
		}
	}	elseif(isset($_GET['uuid'])) {
		if($LDAPResult = $LDAPPeople->SearchByUUID($_GET['uuid'])) {
			$results = $LDAPResult->ResultsToArray();
		}
	}	elseif(isset($_GET['uid'])) {
		if($LDAPResult = $LDAPPeople->SearchByUID($_GET['uid'])) {
			$results = $LDAPResult->ResultsToArray();
		}
	} else {
		$errors[] ='No ID specified';
	}
	
	if($LDAPResult->GetNumberOfRows() != 1) {
		$errors[] = 'More than one result found.  Please refine your search'; // this should never happen
	}
	$LDAPPeople->Close();




//	echo '<pre>', print_r($results, TRUE), '</pre>';

	// if hcard is desired, pull the html data, then pass the output down to JSON
	if($output == 'hcard') {
		$results = $LDAPResult->ResultsToHCard();
		$output = 'json';
	}

	if($output == 'vcard') {
		// $content variable contains the vcard, it's set in the template
		include 'vcard.tpl.php';
		$file = $results[0]['cn'] . '.vcf';
 		header('Content-Type: text/x-vcard');
 		header('Content-Disposition: inline; filename= "'.$file.'"');
 		header('Content-Length: '.strlen($content));  
		print $content;
	}

	if($output == 'json') {
		$json = json_encode($results[0]);
		// @todo: when the server supports json_last_error(), we should implement error checks
		header('Content-Type:application/json');
		print $json;
	}
?>
