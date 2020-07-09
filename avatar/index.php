<?php
/**
 * Directory Avatars
 * lookup avatars from other sources
 * currently checks google+ via picasa
 * maybe gravatar?
 * @todo: what about having user-managed profile images and be done with it?
 * 
 * @author		John Pennypacker <jpennypacker@uri.edu>
 *
 */
 
	require_once '../config.php';
//	require_once 'ldap_config.inc.php';
	
	define('AVATAR_PATH', dirname(__FILE__));
	define('AVATAR_CACHE_DIR', '/pictures');
	
	/**
	 * resizes an existing image and returns the image resource
	 *
	 * @param str $file is a path to load the file from
	 * @param int $w is the desired width
	 * @param int $h is the desired height
	 * @param bool $crop if true, this will crop the image to the specified size (from the center)
	 * @return resource
	 *
	 */
	function resize_image($file, $w, $h, $crop=FALSE) {
		$src = load_image($file);
		$width = imagesx($src);
		$height = imagesy($src);
		$r = $width / $height;
		if ($crop) {
			if ($width > $height) {
				$width = ceil($width-($width*abs($r-$w/$h)));
				$x1 = 0;
				$x2 = $w;
				$y1 = 0;
				$y2 = 0;
			
			} else {
				$height = ceil($height-($height*abs($r-$w/$h)));
				$x1 = 0;
				$x2 = 0;
				$y1 = 0;
				$y2 = $h;
			}
			$newwidth = $w;
			$newheight = $h;
		} else {
			if ($w/$h > $r) {
				$newwidth = $h*$r;
				$newheight = $h;
			} else {
				$newheight = $w/$r;
				$newwidth = $w;
			}
			$x1 = 0;
			$x2 = 0;
			$y1 = 0;
			$y2 = 0;
		}
		$src = load_image($file);
		$dst = imagecreatetruecolor($newwidth, $newheight);
		$white = imagecolorallocate($dst, 255, 255, 255);
		imagefilledrectangle($dst, 0, 0, $newwidth, $newheight, $white);
		imagecopyresampled($dst, $src, $x1, $y1, $x2, $y2, $newwidth, $newheight, $width, $height);
		return $dst;
	}

	/**
	 * resizes an existing image and returns the image resource
	 *
	 * @param str $destination is a path to save the file
	 * @param res $image is the image resource
	 * @return bool
	 */
	function save_image($destination, $image) {
		$ok = FALSE;
		switch(get_image_type($destination)) {
			case 'jpg':
				if(imagejpeg($image, $destination)) $ok = TRUE;
			break;
			case 'png':
				if(imagepng($image, $destination)) $ok = TRUE;
			break;
			case 'gif':
				if(imagegif($image, $destination)) $ok = TRUE;
			break;
			default:
				echo 'uh oh.  could not save a file of unknown type.';
			break;
		}
		return $ok;
	}


	/**
	 * load an image from the filesystem into memory
	 *
	 * @param str $path is a path to the file
	 * @return resource or FALSE if the image doesn't exist
	 */
	function load_image($path) {
		if(file_exists($path)) {
			switch(get_image_type($path)) {
				case 'jpg':
					$image = imagecreatefromjpeg($path);
				break;
				case 'png':
					$image = imagecreatefrompng($path);
				break;
				case 'gif':
					$image = imagecreatefromgif($path);
				break;
			}
		} else {
			return FALSE;
		}
		return $image;
	}


	/**
	 * get the image type (just looks at the extension)
	 *
	 * @param str $name is the file name
	 * @return str
	 */
	function get_image_type($name) {
		$type = FALSE;
		$bits = explode('.', $name);
		switch(array_pop($bits)) {
			case 'jpg':
				$type = 'jpg';
			break;
			case 'png':
				$type = 'png';
			break;
			case 'gif':
				$type = 'gif';
			break;
		}
		return $type;
	}


	/**
	 * validate size and convert it into a size that we deem acceptable
	 *
	 * @param str $desired_size should be something like xsmall, small, medium, or large
	 * @return int
	 */
	function validate_size($desired_size) {
		$size = 100;
		if($desired_size == 'xsmall') {
			$size = 60;
		}
		if($desired_size == 'medium') {
			$size = 300;
		}
		if($desired_size == 'large') {
			$size = 600;
		}
		return $size;
	}

	/**
	 * get
	 *
	 * @param str $name
	 * @param str $path
	 * @return int
	 */
	function get_image_path_by_name($name, $path) {
		$name = strtolower($name);
		$types = array('jpg', 'png', 'gif');
		$file = FALSE;
		foreach($types as $t) {
			if(file_exists($path . '/' . $name . '.' . $t)) {
				$file = $name . '.' . $t;
				break;
			}
		}
		return $file;
	}

	/** 
	 * get a user's image from picasa, save a local copy
	 * return the path to the local image
	 * path may be to a default generic image
	 * @param str $email is the user's email
	 * @return str
	 */
	function get_google_plus_avatar_url($email) {

		global $base_path;
		$data = @file_get_contents('http://picasaweb.google.com/data/entry/api/user/' . urlencode($email) . '?alt=json');
		$data = json_decode($data);

		if($data->{'entry'}) {
			$avatar_url = str_replace('s64', 's100', $data->{'entry'}->{'gphoto$thumbnail'}->{'$t'});
			$path = $base_path . '/avatar' . AVATAR_CACHE_DIR . '/' . $email . '.jpg';
			file_put_contents(strtolower(AVATAR_PATH . AVATAR_CACHE_DIR . '/' . $email . '.jpg'), file_get_contents($avatar_url));
		} else {
			$path = $base_path . '/avatar/none.jpg';
		}
		
				
		die($path);


		return $path;
	}

	/** 
	 * take the server's path and convert it a URL
	 * then send appropriate headers and serve the image
	 * @param str $path the file path to the image
	 */
	function serve_image($path) {
		global $base_path;
		
		if(empty($path)) {
			$path = AVATAR_PATH . '/none.jpg';
		} else {
			$path = str_replace($base_path.'/avatar', '', $path);
			$path = AVATAR_PATH . $path;
		}

		$seconds_to_cache = 3600;
		$ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . ' GMT';
		header('Expires: ' . $ts);
		header('Pragma: cache');
		header('Cache-Control: max-age=' . $seconds_to_cache);
		$bits = explode('.', $path);
		$extension = array_pop($bits);
		switch($extension) {
			case 'jpg':
				header('Content-type: image/jpeg');
			break;
			case 'png':
				header('Content-type: image/png');
			break;
		}
		print file_get_contents($path);
	}

	
	/** 
	 * validate that there's a valid email in the GET request
	 * @todo: validate the email
	 * @return str
	 */
	function validate_email($email) {
		$email = strtolower(trim(str_replace('gtest.', '', $email)));
		
		if(function_exists('filter_var')) {
			$email = filter_var($email, FILTER_VALIDATE_EMAIL);
		} else {
			// validate email with older versions of php
		}
		return $email;
	}
	
	/** 
	 * get a gravatar image URL
	 * @param str user's email address
	 * @return str
	 */
	function get_gravatar($email) {
		$url = 'http://www.gravatar.com/avatar/' . md5($email) . '?s=100';
		$url .= '&d=' . urlencode(get_google_plus_avatar_url($email));
		
		return $url;
	}
	

	
	/** 
	 * get a user's short id from ldap by providing an email address
	 *
	 * @param str user's email address
	 * @return str
	 */
	function get_shortid_from_email($email) {
		global $ldap_query_bind_rdn, $ldap_query_bind_password, $ldap_host_url;
		$result = FALSE;
		$credentials = array(			
			 'bind_rdn' => $ldap_query_bind_rdn
			,'bind_password' => $ldap_query_bind_password
			,'ldaphost' => $ldap_host_url
		);

		$LDAPPeople = new LDAPPeople($credentials);

		$LDAPPeople->AddFilter(array('mail' => $email));
		if($LDAPResult = $LDAPPeople->Search()) {
			$result = $LDAPResult->ResultsToArray();
			$LDAPPeople->Close();
		} else {
			echo 'no result';
		}
		$brown_short_id = $result[0]['brownshortid'];
		return $brown_short_id;
	}
	

	$email = validate_email($_GET['email']);


	$file = (isset($email)) ? $email : 'generic';
	$desired_size = (isset($_GET['size'])) ? $_GET['size'] : 'small';
	$size = validate_size($desired_size);

	if(isset($_COOKIE['HTTP_IS_RETINA']) && $_COOKIE['HTTP_IS_RETINA'] == TRUE) {
		$size = $size * 2;
	}

	$base_directory = dirname(__FILE__);
	$source_directory = $base_directory . '/pictures';
	$cache_directory = $base_directory . '/cache/' . $size;


	if(!is_dir($cache_directory)) {
		mkdir($cache_directory);
	}

	$destination_path = strtolower($cache_directory . '/' . $file . '.jpg');
	$source_file = get_image_path_by_name($file, $source_directory);

	// remove files older than a day
	if(file_exists($destination_path)) {
		$file_date = filemtime($destination_path);
		$now = time();
		if($now - $file_date > 86400) { // if the file is older than a day
			unlink($destination_path);
			if($source_file) {
				unlink($source_directory . '/' . $source_file);
			}
		}

	}

	if(!file_exists($destination_path)) {
		$path = get_gravatar($email);
		$source_file = get_image_path_by_name($file, $source_directory);
		if($source_file == FALSE) {
			$source_file = 'generic.jpg';
		}
		$image = resize_image($source_directory . '/' . $source_file, $size, $size, FALSE);
		save_image($destination_path, $image);
	}




	$seconds_to_cache = 3600;
	$ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . ' GMT';
	header('Expires: ' . $ts);
	header('Pragma: cache');
	header('Cache-Control: max-age=' . $seconds_to_cache);
	header('Content-Type: image/jpeg');
	echo file_get_contents($destination_path);



	
// 	//$url = get_gravatar($email);
// 	//$url = get_google_plus_avatar_url($email);
// 	//echo $url;
// 	serve_image($url);


?>