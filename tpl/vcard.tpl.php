<?php
/**
 * Directory
 *
 * VCard template  
 * I use the term "template" loosely.  I am avoiding the overhead of a tpl engine.
 * 
 * @author		John Pennypacker <jpennypacker@uri.edu>
 *
 */

$content = "BEGIN:VCARD\r\n";
$content .= "VERSION:4.0\r\n";
$content .= "CLASS:PUBLIC\r\n";
$content .= "FN:{$results[0]['displayname']}\r\n";
$content .= "N:{$results[0]['sn']};{$results[0]['givenname']};;;\r\n";
$content .= "TITLE:{$results[0]['title']}\r\n";
$content .= "ORG:{$results[0]['ou']}\r\n";

if(!empty($results[0]['localaddress'])) {
	$address = parse_address($results[0]['localaddress']);
	$content .= 'ADR;TYPE=work:;;' . implode(';', $address) . ";\r\n";
}

if(!empty($results[0]['mail'])) {
	$content .= "EMAIL;TYPE=internet,pref:{$results[0]['mail']}\r\n";
}
if(!empty($results[0]['telephonenumber'])) {
	$content .= "TEL;TYPE=work,voice:{$results[0]['telephonenumber']}\r\n";
}
if(!empty($results[0]['localtelephone'])) {
	$content .= "TEL;TYPE=alternate,voice:{$results[0]['localtelephone']}\r\n";
}
if(!empty($results[0]['mobile'])) {
	$content .= "TEL;TYPE=mobile,voice:{$results[0]['mobile']}\r\n";
}

//$content .= "TEL;TYPE=HOME,voice:8352355189\r\n";
if(isset($results[0]['labeleduri'])) {
	$content .= "URL:{$results[0]['labeleduri']}\r\n";
}
$content .= "END:VCARD\r\n";

// note: do not print anything from this file, just set $content
?>
