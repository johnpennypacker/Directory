<?php
/**
 * Directory
 *
 * a web application to look up people in LDAP
 * 
 * @author		John Pennypacker <jpennypacker@uri.edu>
 *
 */
 
	require_once 'config.php';
	
	// an accessible place to store whether or not there are too many results to show in one page
	$too_many_results = FALSE; 
	
	$results = array(); // initialize results so we never have null
	
	// execute a plain old basic search
	if(isset($_GET['search_string']) && !isset($_GET['rm'])) {
		// @todo: can we make this smarter?
		// what if the user searches for initials like JP?  
		// can we search on other fields at the same time? 
		// can we add weight to the results?
		
		$too_many_results = FALSE; 
		$LDAPPeople = new LDAPPeople(get_ldap_credentials());

		// break the search string into bits (by spaces) and a new filter for each segment
		// just dropping wildcards into one segment results in a less performant search
		$query_bits = explode(' ', trim($_GET['search_string']));
		foreach($query_bits as $k => $v) {
			if(!empty($query_bits[$k])) {
				$LDAPPeople->AddFilter(pad_search($v, 'begins'));
			}
		}
		
		// display the filter in the message area
		$messages[] = 'Filter: ' . $LDAPPeople->GetFilter();
		//$messages[] = 'Filter: ' . $LDAPPeople->GetFilterWithDisplayNameOption(trim($_GET['search_string']));

		if( $LDAPResult = $LDAPPeople->Search( trim( $_GET['search_string'] ) ) ) {
			$number_of_results = $LDAPResult->GetNumberOfRows();
			$results = $LDAPResult->ResultsToHCard();
			$results_array = $LDAPResult->ResultsToArray();
			$LDAPPeople->Close();
			if(!$too_many_results) {
				$messages[] = 'Displaying ' . ($number_of_results . (($number_of_results == 1) ? ' result.' : ' results.'));
			}
		} else {
			$errors[] = 'No results were found';
		}


	}
	
	// execute an advanced search
	if(isset($_GET['rm']) && $_GET['rm'] == 'advanced') {
		$too_many_results = FALSE; 
		$LDAPPeople = new LDAPPeople(get_ldap_credentials());
		
		foreach($advanced_search_fields as $k => $v) {
			if(!empty($_GET[$k])) {
				$LDAPPeople->AddFilter(array($k=> pad_search($_GET[$k], $_GET[$k.'_type'])));
			}
		}

		if($LDAPResult = $LDAPPeople->Search()) {
			$number_of_results = $LDAPResult->GetNumberOfRows();
			$results = $LDAPResult->ResultsToHCard();
			$results_array = $LDAPResult->ResultsToArray();
			$LDAPPeople->Close();
			if(!$too_many_results) {
				$messages[] = 'Displaying ' . ($number_of_results . (($number_of_results == 1) ? ' result.' : ' results.'));
			}
		} else {
			$errors[] = 'No results were found';
		}

	}

	if(!use_privileged_view()) {
		$parsed_path = parse_url($base_path);
		$url = str_replace($parsed_path['path'], '', $_SERVER['REQUEST_URI']);
		$authentication_prompt = 'Members of the university community can <a href="' . $base_path . '/authenticate?destination='. urlencode($url). '">log in to view more information</a>.';
	}

	if(!SHOW_PARTIAL_RESULTS && $too_many_results) {
		// if there are too many results, do not display them.
		$results = array();
	}
	$results['errors'] = $errors;
	$results['messages'] = $messages;

	if(isset($_GET['output']) && $_GET['output'] == 'json') {
		$json = json_encode($results);
		// @todo: when the server supports json_last_error(), we should implement error checks
		header('Content-Type:application/json');
		print $json;
	} else {
		include 'header.tpl.php';
		include 'main.tpl.php';
		include 'footer.tpl.php';
	}
?>
