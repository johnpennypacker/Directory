<?php
/**
 * Directory header html
 *
 * a web application to look up people
 * 
 * @author		John Pennypacker <jpennypacker@uri.edu>
 *
 */
?>

		<section id="main">
			
			<h2 id="page-headline"><?php if(isset($page_headline)): ?><?php print $page_headline; ?><?php endif; ?></h2>
			
			<?php if(isset($errors) && count($errors) > 0): ?>
			<div class="error">
				<?php foreach($errors as $m): ?>
				<p><strong><?php print $m; ?></strong></p>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<?php if(isset($messages) && count($messages) > 0): ?>
			<div class="message">
				<?php foreach($messages as $m): ?>
				<p><strong><?php print $m; ?></strong></p>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
			
			<?php if(isset($authentication_prompt)): ?>
			<div class="auth-message">
				<p><strong><?php print $authentication_prompt; ?></strong></p>
			</div>
			<?php endif; ?>

			<?php if($show_forms): ?>
			<div id="forms">
				<?php
					include('search-form-name.tpl.php');
					include('search-form-advanced.tpl.php');
				?>
			</div>
			<?php else: ?>
			<div id="goto-search"><a href="<?php print $base_path; ?>">&laquo; Search the directory</a></div>
			<?php endif; ?>

			<div id="results">
			<?php if(!empty($results)): ?>
				<?php foreach($results as $key => $row): ?>
					<?php if(is_numeric($key)) { echo $row; } ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</div>
	
	
		</section>
