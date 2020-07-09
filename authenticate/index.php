<?php
/**
 * Directory authentication script
 *
 * If using a service like Shib or webauth, 
 * this page will authenticate a user, open a session,
 * then send them back to where they came from
 * 
 * @author		John Pennypacker <jpennypacker@uri.edu>
 *
 */

require_once '../config.php';

$destination = $base_path;

if(!empty($_GET['destination'])) {
	$destination .= urldecode($_GET['destination']);
}

header('Location: ' . $destination);
?>
