<!DOCTYPE html>
<html lang="en-US">
	
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="http://gmpg.org/xfn/11">

		<title><?php print $page_title; ?></title>
		<meta name="robots" content="noindex,follow">
		
		<link rel="stylesheet" href="<?php print $base_path; ?>/static/style.static.css" type="text/css" media="all">

		<link rel='stylesheet'  href='https://www.uri.edu/styleguide/wp-content/plugins/uri-component-library/css/cl.built.css' type='text/css' media='all' />

		<link rel="stylesheet" type="text/css" href="<?php print $base_path; ?>/css/directory.css<?php print $cache_buster; ?>" />


		<!-- Favicons -->
    <link rel="mask-icon" href="<?php print $base_path; ?>/static/images/safari-pinned-tab.svg" color="#005eff">
    <link rel="icon" type="image/png" href="<?php print $base_path; ?>/static/images/favicon.png">
    <link rel="apple-touch-icon" href="<?php print $base_path; ?>/static/images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php print $base_path; ?>/static/images/apple-touch-icon-180x180.png">



		<script type="text/javascript" src="https://www.uri.edu/styleguide/wp-content/plugins/uri-component-library/js/cl.built.js"></script>

		
	</head>
	
	<body class="page-template-default page">
	
		<div id="page" class="site">
			<a class="skip-link screen-reader-text" href="#content">Skip to content</a>
			
			<div id="masthead">
				
				<header id="brandbar" class="site-header" role="banner">
		
					<div id="identity-print"><img src="<?php print $base_path; ?>/images/logo-print.png" width="120px" alt="University of Rhode Island"></div>

					<div id="globalsearch" role="search">
						<input type="checkbox" id="gsform-toggle" role="presentation" aria-label="Toggle visibility of the search box.">
						<label for="gsform-toggle" id="gsform"><span>Search</span></label>
						<form id="gs" method="get" action="https://www.uri.edu/search" name="global_general_search_form">
							<input type="hidden" name="cx" value="016863979916529535900:17qai8akniu" />
							<input type="hidden" name="cof" value="FORID:11" />
							<label id="gs-query-label" for="gs-query">Searchbox</label>
							<input role="searchbox" name="q" id="gs-query" type="text" placeholder="Search" />
							<input type="submit" id="gs-submit" class="searchsubmit" name="searchsubmit" value="Search" />
						</form>
					</div>

					<div id="globalbanner-wrapper">
						<div id="globalbanner">
							<a href="http://www.uri.edu/" title="University of Rhode Island"><div id="identity">University of Rhode Island</div></a>

							<div id="gateways">
								<input type="checkbox" id="gateways-toggle" role="presentation" aria-label="Open the audience gateways menu when browsing on mobile">
								<label for="gateways-toggle" id="gateways-label"><span>You</span></label>
								<ul id="gateways-menu" role="menu">
									<li><a href="https://www.uri.edu/gateway/future-students" role="menuitem">Future Students</a></li>
									<li><a href="https://www.uri.edu/gateway/students" role="menuitem">Students</a></li>
									<li><a href="https://www.uri.edu/gateway/faculty" role="menuitem">Faculty</a></li>
									<li><a href="https://www.uri.edu/gateway/staff" role="menuitem">Staff</a></li>
									<li><a href="https://www.uri.edu/gateway/families" role="menuitem">Parents and Families</a></li>
									<li><a href="https://www.uri.edu/gateway/alumni" role="menuitem">Alumni</a></li>
									<li><a href="https://www.uri.edu/gateway/community" role="menuitem">Community</a></li>
								</ul>
							</div>

						</div>
					</div>

				</header><!-- #brandbar -->
				
				<header id="siteheader">

					<div id="sitebanner" class="light"><!-- option: class="light" -->

						<div id="sb-backdrop">
							<div id="sb-background-image" style="background-image:url(<?php print $base_path; ?>/images/banner-millennium-falcon.jpg); background-position: 100% 50%"></div><!-- option: style="background-image:url()" -->
							<div id="sb-screen"></div>
						</div>

						<div id="sitebranding">
							<div id="siteidentity">
								<h1 class="site-title"><a href="<?php print $base_path; ?>" rel="home">Directory</a></h1>
								<h2 class="site-description"><?php print $display_description; ?></h2>
							</div>
							<div id="sitesocial"></div>
						</div><!-- #sitebranding -->

					</div><!-- #sitebanner -->

					<div id="navigation" class="content-width">
						<nav id="breadcrumbs" aria-label="Breadcrumb">
							<ol>
								<?php 
									if ( isset( $breadcrumbs ) && is_array( $breadcrumbs ) ) {
										foreach ( $breadcrumbs as $b ) {
											echo '<li><a href="' . $b['href'] . '">' . $b['text'] . '</a></li>';
										}
									}
								?>
								<li aria-current="page"><?php print $display_title; ?></li>
							</ol>
						</nav>
						<!-- <div id="localnav"></div> -->
					</div>
					
				</header>
				
			</div><!-- #masthead -->
	
			<div id="content" class="site-content">
				
				<main id="main" class="site-main" role="main">
					
<!-- ===================== DO NOT CHANGE ANYTHING ABOVE THIS LINE ===================== -->
