<?php
/**
 * Directory
 * common functions file
 * @author		John Pennypacker <jpennypacker@uri.edu>
 *
 * $Id$
 */


/**
 * handle exceptions so we can display smart messages to users
 *
 * @return bool
 */
function exception_error_handler($error_number, $error_string, $error_file, $error_line) {
	global $errors, $too_many_results;
	if(strpos($error_string, 'Size limit exceeded') !== FALSE) {
		if(!SHOW_PARTIAL_RESULTS) {
			$too_many_results = TRUE; 
		}
		$errors[] = 'There are too many results to display them all. Please refine your search and try again.';
	}
}

/**
 * determine if we should display the privileged view or the anonymous view
 * 
 * @return bool
 */
function use_privileged_view() {
	return TRUE;
	
	$privileged = FALSE;
	if(SHOW_PRIVILEGED_VIEW_FROM_CAMPUS_IP === TRUE) {
		if(preg_match('/128\.148.*/', $_SERVER['REMOTE_ADDR'])) {
			$privileged = TRUE;
		}
		if(preg_match('/10\..*/', $_SERVER['REMOTE_ADDR'])) {
			$privileged = TRUE;
		}
		if($_SERVER['REMOTE_ADDR'] == '::1') { // localhost
			$privileged = TRUE;
		}
	}
	if(isset($_SERVER['REMOTE_USER'])) {
		$privileged = TRUE;
	}	
	return $privileged;
}

/**
 * get credentials for binding to LDAP.  Privileged-view gets different credentials than
 * world view
 * 
 * @return arr
 */
function get_ldap_credentials($world=FALSE) {
	global $privileged_view_ldap_dn, $privileged_view_ldap_password, $privileged_view_ldap_host;
	if(use_privileged_view() && $world !== TRUE) {
		return array(
			 'bind_rdn' => $privileged_view_ldap_dn
			,'bind_password' => $privileged_view_ldap_password
			,'ldaphost' => $privileged_view_ldap_host
		);
	} else {
		return array(
			 'ldaphost' => $privileged_view_ldap_host
		);
	}
}

/**
 * pad the search string with wildcards based on input
 * @param str $str the search string
 * @param str $type can be either "contains", "begins", "exact", or "ends"; defaults to contains
 * @return str
 */
function pad_search($str, $type='contains') {
	switch ($type) {
		case 'begins':
			return $str . '*';
		break;
		case 'ends':
			return '*' . $str;
		break;
		case 'exact':
			return $str;
		break;
		default: 
			return '*' . $str . '*';
		break;
	}
}


/**
 * Parse out the address string from LDAP and return an array with fields set.
 * It's not perfect, but it's perfect enough.
 * @param str $address a string out of LDAP that includes the whole address
 * @return arr
 */
function parse_address($address) {

	$return = array();
	@list($box, $street, $city, $state, $zip, $country) = explode(', ', $address);
	
	$address_bits = array_reverse(explode(', ', trim($address)));
	
	if(strlen($address_bits[1]) == 2) { // if element 1 is a state
		$zip = $address_bits[0];
		$state = $address_bits[1];
		$city = (isset($address_bits[2])) ? $address_bits[2] : NULL;
		$street = (isset($address_bits[3])) ? $address_bits[3] : NULL;
		$box = (isset($address_bits[4])) ? $address_bits[4] : NULL;
	} else {
		list($state, $zip) = explode(' ', $address_bits[0]);
		$city = (isset($address_bits[1])) ? $address_bits[1] : NULL;
		$street = (isset($address_bits[2])) ? $address_bits[2] : NULL;
		$box = (isset($address_bits[3])) ? $address_bits[3] : NULL;
	}
	

	if(empty($country) && $state == 'RI') {
		$country = 'United States';
	}
	
	$return['box'] = $box;
	$return['street'] = $street;
	$return['city'] = $city;
	$return['state'] = $state;
	$return['zip'] = $zip;
	$return['country'] = $country;

	return $return;
	
}


	
?>