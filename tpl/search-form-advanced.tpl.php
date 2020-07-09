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

				<form method="get" action="<?php print $base_path; ?>" id="advanced-search">
					<fieldset>
						<legend>Advanced Search</legend>

						<input type="hidden" name="rm" value="advanced" />

						<?php
							foreach($advanced_search_fields as $k => $v):
						?>
						<div class="form-row">
							<label for="<?php print $k; ?>"><?php print $v; ?></label>
							<?php $selected = (isset($_GET[$k.'_type'])) ? $_GET[$k.'_type'] : 'contains'; ?>
							<select name="<?php print $k; ?>_type">
								<?php foreach($advanced_search_types as $key => $value): ?>
								<option value="<?php print $key;?>"<?php if($selected == $key) { print ' selected'; };?>><?php print htmlspecialchars(trim($value));?></option>
								<?php endforeach; ?>
							</select> 
						
							<input type="text" name="<?php print $k; ?>" id="<?php print $k; ?>" class="textinput" value="<?php print (isset($_GET[$k])) ? htmlspecialchars(trim($_GET[$k])) : ''; ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" />
						</div>
						<?php endforeach; ?>
			
						<input type="submit" id="submit-advanced" class="button" value="Search" /> 

					</fieldset>
				</form>
