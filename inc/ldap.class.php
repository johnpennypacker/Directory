<?php
/**
 * 
 * Directory Classes
 *
 *
 * One does the searching, the other handles the results from the search
 *
 * @author		John Pennypacker <jpennypacker@uri.edu>
 *
 */



/**
 * 
 * LDAPPeople Class
 *
 * LDAPPeople class is a utility that binds to LDAP
 * and returns people data as an LDAPResult object.
 *
 * @author		John Pennypacker <jpennypacker@uri.edu>
 *
 */

class LDAPPeople {

	public $ldaphost = '';
	public $ldapport = 636;
	public $dn;

	public $ldapconn;
	
	public $result;
	public $filter = array();
	public $operator;

	private $bind_password; // password (leaving blank binds anonymously)
		
/**
 * Class Constructor
 * Establish a connection to LDAP
 * 
 * @param	$args			An array of parameters.  Intended to be bind_rdn, bind_password, etc.
 *
 */
	public function __construct($args='') {

		$this->bind_rdn = '';
		$this->bind_password = '';
			
		$this->dn = 'dc=uri,dc=edu';
		$this->operator = 'and';
		
		if(!empty($args) && is_array($args)) {
			foreach($args as $k => $v) {
				$this->$k = $v;
			}
		}
		
		if(isset($this->LdapDn) && empty($this->bind_rdn)) { $this->bind_rdn = $this->LdapDn; }
		if(isset($this->LdapDnPwd) && empty($this->bind_password)) { $this->bind_password = $this->LdapDnPwd; }

		// Connecting to LDAP
		if($this->ldapconn = ldap_connect($this->ldaphost, $this->ldapport)) {
			// we're connected
			
			if(!ldap_bind($this->ldapconn, $this->bind_rdn, $this->bind_password)) {
				$this->Error();
			}
		} else {
			echo 'not connected';
		}
	}


/**
 * Add a parameter to the search filter
 * 
 * @param	$args		The argument is an array of key value pairs to search on.
 *						e.g. 'sn' => 'Penny' or 'title' =>'*student'
 * 
 */
	public function AddFilter($args) {
		
		if(is_string($args)) {
			// the argument is a string.  search on name only
			$args = array('sn' => $args);
		}
	
		foreach($args as $key => $value) {
			if(isset($this->filter[$key]) && !empty($value)) {
				$this->filter[$key][] = $value;
			} else {
				$this->filter[$key] = array($value);
			}
		}
	}


/**
 * Reset the search filter
 * Removes all of the search criteria previously set with AddFilter()
 * 
 */
	public function ResetFilter() {
		$this->filter = array();
	}


/**
 * Set the filter to use AND or OR
 * 
 * @param	$op		op can be either "|", "&", "and", or "or"
 * 
 * @return	boolean
 */
	public function SetOperator($op) {
		switch(strtolower($op)) {
			case '|':
			case 'or':
				$this->operator = "or";
				return TRUE;
			break;

			case '&':
			case 'and':
				$this->operator = 'and';
				return TRUE;
			break;
			
			default:
				return FALSE;
		}
	}



/**
 * Take the filter arguments and create a formatted filter
 * 
 * @return	string	Returns a string of all the added filters
 * 
 */
	public function GetFilter() {
		$op = ($this->operator == 'or') ? '|' : '&';
		$filter = "($op";

		foreach($this->filter as $key => $value) {
			if(count($value) == 1) {
				// we've only got one search string for this column
				$filter .= "($key={$value[0]})";	
			} else {
				// we've got many search strings for a single column
				foreach($value as $v) {
				$filter .= "($key=$v)";	
				}
			}
		}
		
		$filter .= ")";

		//$filter = '(|(&(sn=cog*)(sn=*ut))(displayname=*cog*cen*))';

		return $filter;
	}


/**
 * Wraps existing constructed queries with an OR filter on displayname
 * This is very much a hack
 * it was discovered that sn and displayname contain entirely separate values
 * and filters do not return expected results
 *
 * @param str $query the search string from the user
 * @return str
 * 
 */
	public function GetFilterWithDisplayNameOption($query) {
		if(empty($query)) {
			return $this->GetFilter();
		}
		$filter = '(|';
		$filter .= $this->GetFilter();
		$filter .= '(cn=' . str_replace(' ', '*', $query) . '*)';
		$filter .= ')';
		return $filter;
	}


/**
 * Search for records based on a custom filter
 * Specify a search query to run against the LDAP server.
 * e.g.  "sn=penny*" will search for records where the surname begins with Penny
 *
 * @param str $add_display the search string from the user
 * @return mixed Result object on success, boolean FALSE if no result
 * 
 */
	public function Search($add_display=FALSE) {
		
		if($add_display != FALSE) {
			$filter = $this->GetFilterWithDisplayNameOption($add_display);
		} else {
			$filter = $this->GetFilter();
		}
				
		$result = ldap_search($this->ldapconn, $this->dn, $filter);
				
		if($result !== FALSE && $this->result = ldap_get_entries($this->ldapconn, $result)) {
			ldap_free_result($result);
			$LDAPResult = new LDAPResult($this->result);
			return $LDAPResult;
		} else {
			// and error has occurred
			return FALSE;
		}
			
	}



/**
 * Search for a user by name
 * <p>
 * This is a convenience method so that people can find a record by name easily.
 * <p>
 * You may specify an asterisk (*) as a wildcard character when searching.
 * So "Penny" returns users named "Penny" but "Penny*" returns "Penny" and "Pennypacker"
 * This is the broadest search function, so it's most likely to return a result
 *
 * @param	$str	The string to search
 * @see Search()
 * 
 */
	public function SearchByName($str) {
		if(empty($str)) return FALSE;
		$this->ResetFilter();
		$this->AddFilter(array('sn' => $str));
		return $this->Search();
	}


/**
 * Search for a user by their last name only
 * <p>
 * This is a convenience method to search just last names.
 * <p>
 * You may specify an asterisk (*) as a wildcard character when searching.
 * So "Penny" returns users named "Penny" but "Penny*" returns "Penny" and "Pennypacker"
 *
 * @param	$str	The string to search
 * @see Search()
 */
	public function SearchByLastName($str) {
		if(empty($str)) return FALSE;
		$this->ResetFilter();
		$this->AddFilter(array('sn' => $str));
		return $this->Search();
	}


/**
 * Search for a user by their UUID
 * <p>
 * This is a convenience method to search by ID (and return a single result).
 * <p>
 *
 * @param	$str	The UUID to search  eg 77c55f23-958d-9d6b-aa4e-e2619c8cc33d
 * @see Search()
 */
	public function SearchByUUID($uuid) {
		if(empty($uuid)) return FALSE;
		$this->ResetFilter();
		$this->AddFilter(array('uuid' => $uuid));
		return $this->Search();
	}

	public function SearchByUID($uuid) {
		if(empty($uuid)) return FALSE;
		$this->ResetFilter();
		$this->AddFilter(array('uid' => $uuid));
		return $this->Search();
	}

	public function SearchByEPPN($eppn) {
		if(empty($eppn)) return FALSE;
		$this->ResetFilter();
		$this->AddFilter(array('edupersonprincipalname' => $eppn));
		return $this->Search();
	}


/**
 * Close the LDAP connection
 * 
 */
	public function Close() {
		ldap_close($this->ldapconn);
	}

/**
 * Get the last error that occurred
 * 
 * @return	string	The LDAP error message
 */
	private function Error() {
		$n = ldap_errno($this->ldapconn);
		return "$n: " . ldap_error($n);
	}




}








/**
 * 
 * LDAPResult Class
 *
 * LDAPResult class is an object that stores and handles results from LDAPPeople.
 *
 * @author		John Pennypacker <jpennypacker@uri.edu>
 *
 */


class LDAPResult {

	public $result;
	public $number_of_rows;

/**
 * Class Constructor
 * Establish a connection to LDAP
 * 
 * @param	$result	A results array from the LDAP object
 *
 */
	public function __construct($result) {
		if(!empty($result)) {
			$this->result = $result;
			$this->number_of_rows = $this->NumResults();
		}
	}


/**
 * Count the number of records in the result set
 * 
 * @return int Number of records in the result set;
 */
	public function GetNumberOfRows() {
			return $this->number_of_rows;
	}

/**
 * Convert the raw LDAP result set into a friendlier array of values
 * 
 * @return	array	An array of records from the result set
 */
	public function ResultsToArray() {

		$a = array();
	
		foreach ($this->result as $key => $inf) {
			$res = array();
			if (is_array($inf) && is_numeric($key)) {
				foreach ($inf as $key => $in) {
					if ((count($inf[$key]) - 1) > 0) {
						if (is_array($in)) {
							unset($inf[$key]['count']);
						}
						if(count($inf[$key]) == 1) {
							$res[$key] = $inf[$key][0];
						} else {
							$res[$key] = $inf[$key];
						}
					}
				}
			$a[] = $res;
			}
		}
		
		return $a;
		
	}




/**
 * Convert the raw LDAP result set into an array of hCard formatted html
 * @return str
 */

	public function ResultsToHCard() {
		$a = array();
		$r = $this->ResultsToArray();
		
		foreach($r as $record) {
			$a[] = $this->HCard($record);
		}
		return $a;
	}


/**
 * Convert the raw LDAP result set into an array of hCard formatted html
 *
 * @param	$r	An array of data pulled from the directory
 * @return str	A string of HTML in the hCard microformat.
 */

	public function HCard($r) {
		global $base_path;
		include 'hcard.tpl.php';

		return $hcard;
	}




/**
 * Format phone numbers in a nice, friendly way.
 * 
 * @param	str	$tel		A string that represents a phone number
 * @return str 
 */
	private function FormatPhoneNumber($tel) {
		$tel = preg_replace("/\D/", "", $tel); // remove non-numerals
		
		if(!empty($tel) && $tel{0} == 1) { //if the number starts with a 1, remove it
			$tel = substr($tel, 1);
		}
		if(empty($tel)) { return ""; }
		
		if(strlen($tel) == 10) {
			$areacode = substr($tel,0,3);
			$exchange = substr($tel,3,3);
			$digits = substr($tel,6,4);
		}
		if(strlen($tel) == 7) {
			$areacode = "401";
			$exchange = substr($tel,0,3);
			$digits = substr($tel,3,4);
		}
		return $areacode . "." . $exchange . "." . $digits;
	}


/**
 * Count the number of records in the result set
 * 
 * @return	int	Number of records in the result set
 */
	private function NumResults() {
		if(isset($this->result["count"])) {
			return $this->result["count"];
		} else {
			return 0;
		}
	}
}

?>