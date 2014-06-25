<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   WP_Side_Comments
 * @author    Pierre SYLVESTRE <pierre@strategio.fr>
 * @license   GPL-2.0+
 * @link      http://www.strategio.fr
 * @copyright 2014 Strategio
 */
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form method="post" action="options-general.php?page=wp-side-comments">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="contentSelector"><?php _e('Content Selector', $domain); ?></label></th>
					<td>
						<input name="contentSelector" type="text" id="contentSelector" value="<?php echo isset($settings['contentSelector']) ? $settings['contentSelector'] : ''; ?>" class="regular-text">
						<p class="description"><?php _e('class or id to target content area', $domain); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="commentSelector"><?php _e('Comment Selector', $domain); ?></label></th>
					<td>
						<input name="commentSelector" type="text" id="commentSelector" value="<?php echo isset($settings['commentSelector']) ? $settings['commentSelector'] : ''; ?>" class="regular-text">
						<p class="description"><?php _e('class or id to target comment area', $domain); ?></p>
					</td>
				</tr>
			</tbody>
		</table>

		<?php wp_nonce_field( 'wp-side-comments-settings' ); ?>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save settings', $domain); ?>">
		</p>
	</form>

</div>
