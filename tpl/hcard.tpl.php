<?php
/**
 * Directory
 *
 * HCard template  
 * I use the term "template" loosely.  I am avoiding the overhead of a tpl engine.
 * @see http://microformats.org/wiki/hCard
 * 
 * @author		John Pennypacker <jpennypacker@uri.edu>
 *
 */

	$title = (!empty($r['longtitle'])) ? $r['longtitle'] : $r['title'];
	// some folks have multiple titles, show them all.
	if(is_array($title)) {
		$title = implode('<br />', $title);
	}
	
	if(is_array($r['employeetype'])) {
		$record_type = implode( ', ', $r['employeetype'] );
	} else {
		$record_type = (isset($r['employeetype'])) ? $r['employeetype'] : FALSE;
	}

	
	$badged_class = ($record_type != FALSE) ? 'badged' : '';

	// there are, from time to time, people who do not have a display name set.
	// why is this?
	$name = $r['displayname'];
	if(empty($name)) {
		$name = $r['givenname'] . ' ' . $r['sn'];
	}

	$hcard = '
<div id="hcard-'. str_replace(" ", "-", strtolower($name)).'" class="vcard ' . str_replace(array(', ', ' ', ','), '-', strtolower($record_type)) . ' ' . $badged_class . '">';

	if(!empty($r['mail']) && SHOW_AVATARS && $record_type == 'Faculty') {
		$hcard .= '<div class="avatar"><img src="' . $base_path . '/avatar/index.php?email=' . $r['mail'] . '" alt="" /></div>';
	}

	$hcard .= '<h2 class="name">';

		$hcard .= '
<span class="fn">'. $name .'</span>
		';

	if(!empty($r['mail'])) {
		$hcard .= '
			&lt;<a class="email" href="mailto:'. $r['mail'] .'">'. $r['mail'] .'</a>&gt;
		';
	}

	$hcard .= '</h2>';

	if(!empty($r['labeleduri'])) {
		$hcard .= '
			<a class="url" rel="nofollow" href="'. $r['labeleduri'] .'">'. $r['labeleduri'] .'</a>			
		';
	}
	
		$hcard .= '
<div class="title">'. $title .'</div>
		';

	if(!empty($record_type)) {
		$hcard .= '
<div class="record-type">'. $record_type .'</div>
		';
	}

	if(!empty($r['departmentname'])) {
		$hcard .= '
			<div class="org">'. $r['departmentname'] .'</div>';
	}



if( !empty($r['telephonenumber']) ) {
	$hcard .= '
<div class="tel">';

$tel = (isset($r['telephonenumber'])) ? trim($r['telephonenumber']) : '';
if(!empty($tel)) {
	$hcard .= '
		<div class="number"><span class="type">Telephone</span> ' . $this->FormatPhoneNumber($tel) . '</div>';
}

if(!empty($r['mobile'])) {
	$hcard .= '
		<div class="number"><span class="type">Mobile</span> '. $this->FormatPhoneNumber($r['mobile']) . '</div>';
}

	$hcard .= '
</div>'; // end telephone numbers
}

	if(!empty($r['uid'])) {
	$hcard .= '
		<div class="username">Username: '. $r['uid'] . '</div>';
	}
	
	$hcard .= '<div class="nerdy-output">';
	if(!empty($r['uid']) ) { 
		$hcard .= '<a class="vcard icon-download-4" title="Download Card to add to Address Book" href="' . $base_path . '/api/1.0/?uid=' . $r['uid'] . '&output=vcard" title="download this contact"><span>VCard</span></a>';
		$hcard .='<a class="json" title="JSON" href="' . $base_path . '/api/1.0/?uid=' . $r['uid'] . '&output=json" title="view as JSON"><span>JSON</span></a>';
	} 
	
	if(!empty($r['uid']) ) { 
		$hcard .= '<a class="permalink icon-bookmark-1" title="Permanent Link" href="'. $base_path . '/uid/' . $r['uid'] . '"><span>Permalink</span></a>';
	}
	$hcard .= '</div>'; // end .nerdy-output

	$hcard .= '</div>';
	

	if( substr ( $_SERVER['SCRIPT_NAME'], -7 ) === 'uid.php' ) {
		$hcard .= '<pre>';
		$hcard .= print_r($r, TRUE);
		$hcard .= '</pre>';
	}



?>