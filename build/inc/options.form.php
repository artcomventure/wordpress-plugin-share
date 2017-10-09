<div class="wrap">
	<h2><?php printf( __( '%s Settings', 'share' ), __( 'Share', 'share' ) . '/' . __( 'Follow', 'share' ) ); ?></h2>

	<form id="share-settings-form" method="post" action="options.php">
		<?php settings_fields( 'share' ); ?>

		<table class="form-table">
			<tbody>

			<tr valign="top">
				<th scope="row"><?php _e( 'Load default CSS', 'share' ); ?>:</th>
				<td>
					<input type="checkbox" class="regular-checkbox" name="share[css]"
					       value="1"<?php echo( share_get_option( 'css' ) ? ' checked="checked"' : '' ) ?> />
					<label for=""><?php _e( 'Display links as buttons', 'share' ); ?></label>
				</td>
			</tr>

			</tbody>
		</table>

		<div class="nav-tab-wrapper hide-if-no-js">
			<a href="#share-settings" class="nav-tab"><?php _e( 'Share', 'share' ) ?></a>
			<a href="#follow-settings" class="nav-tab"><?php _e( 'Follow', 'share' ) ?></a>
		</div>

		<div id="share-settings">

			<table class="form-table">
				<tbody>

				<tr valign="top">
					<th scope="row"><?php _e( 'Show counts', 'share' ); ?>:</th>
					<td>
						<input type="checkbox" class="regular-checkbox" name="share[counts]"
						       value="1"<?php echo( share_get_option( 'counts' ) ? ' checked="checked"' : '' ) ?> />
						<label for=""><?php _e( 'Get and show number of shares', 'share' ); ?></label>

						<p class="description">
							<?php _e( "Unfortunately this can't be provided for all share buttons. For detailed information see network section below.", 'share' ); ?>
						</p>
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

			<p><?php _e( "To change the order of the share links just drag'n'drop the <i>tabs</i> on the left in the desired order.", 'share' ); ?></p>

			<div id="share">

				<ul class="tabs">

					<?php foreach ( $share_options['share'] as $network => $options ) : ?>

						<li<?php echo( $options['enabled'] ? ' class="enabled"' : '' ); ?>>
							<a href="#tab_<?php echo sanitize_title( $network ); ?>">
								<?php echo $network; ?>
							</a>
						</li>

					<?php endforeach; ?>
				</ul>

				<div class="panels">
					<?php foreach ( $share_options['share'] as $network => $options ) :
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
									       name="share[share][<?php echo $network; ?>][enabled]"
									       value="1"<?php checked( 1, $options['enabled'] ); ?> />
								</td>
							</tr>

							<tr valign="top">
								<th><?php _e( 'Icon', 'share' ); ?></th>
								<td>
									<div class="icon-wrapper">
										<input type="text" class="regular-text"
										       value="<?php echo !empty( $options['icon'] ) ? $options['icon'] : ''; ?>"
										       name="share[share][<?php echo $network; ?>][icon]" />
										<span class="dashicons dashicons-format-gallery" title="<?php _e( 'Choose icon', 'share' ); ?>"></span>
									</div>
									<p class="description"><?php _e( 'Absolute path to image file or string of HTML classes (for icon fonts).', 'share' ); ?></p>
								</td>
							</tr>

							<?php // Facebook AppID
							if ( $network == 'Facebook' ) : ?>
								<tr valign="top">
									<th><?php _e( 'Facebook AppID', 'share' ) ?></th>
									<td>
										<input type="text" class="regular-text"
										       value="<?php echo( !empty( $options['app_id'] ) ? $options['app_id'] : '' ); ?>"
										       name="share[share][<?php echo $network; ?>][app_id]"/>
										<p class="description"><?php _e( 'optional' ); ?></p>
									</td>
								</tr>
							<?php endif; ?>

							<?php // share subject
							if ( in_array( $network, array( 'Email' ) ) ) : ?>
								<tr valign="top">
									<th><?php _e( 'Subject', 'share' ) ?></th>
									<td>
										<input type="text" class="regular-text"
										       value="<?php echo( !empty( $options['subject'] ) ? $options['subject'] : '' ); ?>"
										       name="share[share][<?php echo $network; ?>][subject]"
										       placeholder="<?php echo share_default_subject(); ?>"/>
									</td>
								</tr>
							<?php endif; ?>

							<?php // non social networks share text
							if ( in_array( $network, array( 'SMS', 'Whatsapp', 'Email', 'Linkedin' ) ) ) : ?>
								<tr valign="top">
									<th><?php _e( 'Share text', 'share' ) ?></th>
									<td>
									<textarea name="share[share][<?php echo $network; ?>][text]"
									          style="width:calc(25em + 1px);"
									          placeholder="<?php echo ( ! in_array( $network, array( 'Email' ) ) ? share_default_subject() . ' ' : '' ) . share_default_text(); ?>"><?php echo( !empty( $options['text'] ) ? $options['text'] : '' ); ?></textarea>
									</td>
								</tr>

								<tr valign="top" class="patterns">
									<th><?php _e( 'Patterns', 'share' ) ?></th>
									<td>
										<?php foreach ( share_patterns( TRUE ) as $pattern => $replacement ) {
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

		</div>

		<div id="follow-settings">

			<script id="follow-item" type="text/html">

			</script>

			<table id="follow-list" class="form-table">
				<tbody>

				<?php // first empty array is used as template
				foreach ( array_merge( array( array() ), $share_options['follow'], array( array() ) ) as $nb => $network ) : ?>
					<tr<?php echo !$nb ? '  style="display: none"' : ''; ?>>
						<?php // $nb == 0 is template
						if ( $nb ) $nb--; // by increasing the $nb the first 'real' entry we override the template one on save ?>
						<td>
							<span class="dashicons dashicons-move"></span>
							<input type="text" name="share[follow][<?php echo $nb ?>][network]" value="<?php echo !empty( $network['network'] ) ? $network['network'] : ''; ?>" placeholder="<?php _e( 'Network', 'share' ) ?>" />
							<input type="text" name="share[follow][<?php echo $nb ?>][url]" value="<?php echo !empty( $network['url'] ) ? $network['url'] : ''; ?>" placeholder="<?php _e( 'Url', 'share' ) ?>" />
							<div class="icon-wrapper">
								<input type="text" name="share[follow][<?php echo $nb ?>][icon]" value="<?php echo !empty( $network['icon'] ) ? $network['icon'] : ''; ?>" placeholder="<?php _e( 'Icon', 'share' ) ?>" />
								<span class="dashicons dashicons-format-gallery" title="<?php _e( 'Choose icon', 'share' ); ?>"></span>
							</div>
							<span class="dashicons dashicons-editor-help" title="<?php _e( 'Absolute path to image file or string of HTML classes (for icon fonts).', 'share' ); ?>"></span>
							<span class="dashicons dashicons-no-alt"></span>
						</td>
					</tr>
				<?php endforeach; ?>

				</tbody>
			</table>

			<a id="add-follow-network" class="button"><?php _e( 'Add Network', 'share' ); ?></a>
		</div>

		<?php submit_button(); ?>

	</form>
</div>
