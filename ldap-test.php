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
	
// test_staff1
// 11Beyonce**
// test_staff1@uri.edu
// 
// test_student1
// 11Beyonce**
// test_sudent1@my.uri.edu 
	$user = 'test_staff1';
	$pass = '11Beyonce**';

	$user = 'jpennypacker';
	$pass = 'agtmc2ae!U';

	$user = 'wpressuri';
  $pass = 'd2pTb29zMkxweDF3';


	$ldaphost = $privileged_view_ldap_host;
	$ldapconn = ldap_connect( $ldaphost, 636 ) or die( 'Could not connect to ' . $ldaphost );
	
	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);


	
// 	$ldaprdn = $user . '@' . $ldaphost;
// 	$ldaprdn = $user . '@uri.edu';
//	$ldaprdn = 'ldaps.uri.edu' . "\\" . $user;
 	$ldaprdn = 'uid=' . $user . ',ou=authenticators,dc=uri,dc=edu';
//	$ldaprdn = $user;
// 	$ldaprdn = $user . '@uri.edu';

	if ($ldapconn) {

		// binding to ldap server
		$ldapbind = ldap_bind( $ldapconn, $ldaprdn, $pass );
		
		echo '<pre>';
		echo 'user: ' . $ldaprdn . "\n";
		echo 'pass: ' . $pass . "\n";
		var_dump( ldap_error( $ldapconn ) );
		echo '</pre>';

		// verify binding
		if ( $ldapbind ) {
			echo "LDAP bind successful...";
		} else {
			echo "LDAP bind failed...";
		}
	}
	
// 	$Result = ldap_search($ldapconn, "OU=IT,DC="Domain",DC=corp", "(samaccountname=$ldaprdn)", array("dn"));
// $data = ldap_get_entries($ldapconn, $Result);
// print_r($data);


	ldap_close($ldapconn);

exit;


$ldaprdn = 'mydomain' . "\\" . $user;

$bind = @ldap_bind($ldapconn, $ldaprdn, $pass);

if ($bind) {
	$filter="(sAMAccountName=$user)";
	$result = ldap_search($ldapconn, "dc=MYDOMAIN,dc=COM", $filter);
	ldap_sort($ldapconn, $result, "sn");
	$info = ldap_get_entries($ldapconn, $result);
	for ($i=0; $i<$info["count"]; $i++)
	{
			if($info['count'] > 1)
					break;
			echo "<p>You are accessing <strong> ". $info[$i]["sn"][0] .", " . $info[$i]["givenname"][0] ."</strong><br /> (" . $info[$i]["samaccountname"][0] .")</p>\n";
			echo '<pre>';
			var_dump($info);
			echo '</pre>';
			$userDn = $info[$i]["distinguishedname"][0]; 
	}
	@ldap_close($ldapconn);
} else {
	$msg = "Invalid email address / password";
	echo $msg;
}


    
?>
