<?php
/**
 * Directory advanced search form html
 *
 * a web application to look up people
 * 
 * @author		John Pennypacker <jpennypacker@uri.edu>
 *
 */
?>

				<form method="get" action="<?php print $base_path; ?>" id="name-search">
					<fieldset>
						<legend>Search by Name</legend>
						<label for="search-string">
							<span class="label-text">Name Search:</span>
							<?php 
								$value = (isset($_GET['search_string'])) ? $_GET['search_string'] : NULL;
							?>
							<input type="text" name="search_string" id="search-string" class="textinput" value="<?php print htmlspecialchars(trim($value)); ?>" autofocus="autofocus" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" />
							<span class="label-description">Search by first or last name.</span>
						</label>
						<input type="submit" id="submit-basic" name="basic" class="button" value="Search" /> 
					</fieldset>
				</form>
