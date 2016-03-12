<div class="wrap">
	<h2><?php printf( __( '%s Settings', 'share' ), 'Share' ); ?></h2>

	<form id="share-settings-form" method="post" action="options.php">
		<?php settings_fields( 'share' ); ?>

		<table class="form-table">
			<tbody>

			<tr valign="top">
				<th scope="row"><?php _e( 'Show counts', 'share' ); ?>:</th>
				<td>
					<input type="checkbox" class="regular-checkbox" name="share[counts]"
					       value="1"<?php echo( share_get_option( 'counts' ) ? ' checked="checked"' : '' ) ?> />
					<label for=""><?php _e( 'Get and show number of shares.', 'share' ); ?></label>

					<p class="description">
						<?php _e( "Unfortunately this can't be provided for all share buttons. For detailed information see network section below.", 'share' ); ?>
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e( 'Load default CSS', 'share' ); ?>:</th>
				<td>
					<input type="checkbox" class="regular-checkbox" name="share[css]"
					       value="1"<?php echo( share_get_option( 'css' ) ? ' checked="checked"' : '' ) ?> />
				</td>
			</tr>

			</tbody>
		</table>

		<h3><?php _e( 'Enable/Disable share buttons for post types', 'share' ) ?></h3>

		<table class="form-table">
			<tbody>

			<?php // get share options
			$share_options = share_get_options();

			foreach ( $share_options['post_types'] as $post_type => $status ) :
				$post_type = get_post_type_object( $post_type );
				$id = 'share_post_type_' . $post_type->name; ?>

				<tr valign="top">
					<th scope="row"><?php echo $post_type->label; ?>:</th>
					<td>
						<input id="<?php echo $id; ?>" type="checkbox" class="regular-checkbox"
						       name="share[post_types][<?php echo $post_type->name; ?>]"
						       value="1"<?php echo( $status ? ' checked="checked"' : '' ) ?> />
						<label for="<?php echo $id; ?>"><?php _e( 'Enable', 'share' ); ?></label>
					</td>
				</tr>

			<?php endforeach; ?>

			</tbody>
		</table>

		<h3><?php _e( 'Enable/Disable and order share buttons', 'share' ); ?></h3>

		<p><?php _e( "To change the order of the share links just drag'n'drop the <i>tabs</i> on the left in the desired order.",'share' ); ?></p>

		<div id="share-settings">

			<ul class="tabs">

				<?php foreach ( $share_options['networks'] as $network => $enabled ) : ?>

					<li<?php echo( $enabled ? ' class="enabled"' : '' ); ?>>
						<a href="#tab_<?php echo sanitize_title( $network ); ?>">
							<?php echo $network; ?>
						</a>
					</li>

				<?php endforeach; ?>
			</ul>

			<div class="panels">
				<?php foreach ( $share_options['networks'] as $network => $enabled ) :
					$network_id = sanitize_title( $network ); ?>

					<table id="tab_<?php echo $network_id; ?>" class="form-table">
						<thead>
						<tr>
							<td colspan="2">
								<p>
									<?php printf( __( 'Let your content be shared via <strong>%1$s</strong>.', 'share' ), $network ); ?>
								</p>

								<?php $notes = array();

								// mobile devices notice
								if ( in_array( $network, array( 'SMS', 'Whatsapp' ) ) ) {
									$notes[] = __( 'Only available on mobile devices.', 'share' );
								}

								// no share count
								if ( ! in_array( $network, array( 'Facebook', 'Google+', 'Linkedin', 'Pinterest' ) ) ) {
									$notes[] = __( 'No share count available.', 'share' );
								}

								if ( $notes ) {
									echo '<ul class="notes"><li>' . implode( '</li><li>', $notes ) . '</li></ul>';
								} ?>
							</td>
						</tr>
						</thead>

						<tbody>

						<tr valign="top">
							<th><?php _e( 'Enable', 'share' ); ?></th>
							<td>
								<input type="checkbox" class="regular-checkbox"
								       name="share[networks][<?php echo $network; ?>]"
								       value="1"<?php echo( $enabled ? ' checked="checked"' : '' ) ?> />
							</td>
						</tr>

						<?php // Facebook AppID
						if ( $network == 'Facebook' ) : ?>
							<tr valign="top">
								<th><?php _e( 'Facebook AppID', 'share' ) ?></th>
								<td>
									<input type="text" class="regular-text"
									       value="<?php echo( $share_options[ $network ]['app_id'] ? $share_options[ $network ]['app_id'] : '' ); ?>"
									       name="share[<?php echo $network; ?>][app_id]" />
								</td>
							</tr>
						<?php endif; ?>

						<?php // share subject
						if ( in_array( $network, array( 'Email' ) ) ) : ?>
							<tr valign="top">
								<th><?php _e( 'Subject', 'share' ) ?></th>
								<td>
									<input type="text" class="regular-text"
									       value="<?php echo( $share_options[ $network ]['subject'] ? $share_options[ $network ]['subject'] : '' ); ?>"
									       name="share[<?php echo $network; ?>][subject]"
									       placeholder="<?php echo share_default_subject(); ?>"/>
								</td>
							</tr>
						<?php endif; ?>

						<?php // non social networks share text
						if ( in_array( $network, array( 'SMS', 'Whatsapp', 'Email', 'Linkedin' ) ) ) : ?>
							<tr valign="top">
								<th><?php _e( 'Share text', 'share' ) ?></th>
								<td>
									<textarea name="share[<?php echo $network; ?>][text]"
									          style="width:calc(25em + 1px);"
									          placeholder="<?php echo ( !in_array( $network, array( 'Email' ) ) ? share_default_subject() . ' ' : '' ) . share_default_text(); ?>"><?php echo( $share_options[ $network ]['text'] ? $share_options[ $network ]['text'] : '' ); ?></textarea>
								</td>
							</tr>

							<tr valign="top" class="patterns">
								<th><?php _e( 'Patterns', 'share' ) ?></th>
								<td>
									<?php foreach (share_patterns() as $pattern => $replacement) {
										echo '<b>[' . $pattern . ']</b> &ndash; <i>' . $replacement . '</i><br />';
									} ?>
								</td>
							</tr>
						<?php endif; ?>

						<?php // todo: action for extending
						?>

						</tbody>
					</table>

				<?php endforeach; ?>
			</div>

		</div>

		<?php submit_button(); ?>

	</form>
</div>
