<div class="wrap">
    <h2><?php printf( __( '%s Settings', 'share' ), 'Share' ); ?></h2>

    <form method="post" action="options.php">
        <?php settings_fields( 'share' ); ?>

        <table class="form-table">
            <tbody>

            <tr valign="top">
                <th scope="row"><?php _e( 'Show counts', 'share' ); ?>:</th>
                <td>
                    <input type="checkbox" class="regular-checkbox" name="share_counts" value="1"<?php echo ( get_option( 'share_counts', 0 ) ? ' checked="checked"' : '' ) ?> />
                    <label for=""><?php _e( 'Show number of shares right next to button.', 'share' ); ?></label>
                </td>
            </tr>

            </tbody>
        </table>

        <h3><?php _e( 'Enable/Disable share buttons for post types', 'share' ) ?></h3>

        <table class="form-table">
            <tbody>

            <?php
            // get all post types
            $post_types = array_filter( get_post_types( '', 'objects' ), function( $post_type ) {
                return !in_array( $post_type->name, array( 'revision', 'nav_menu_item' ) );
            } );

            // ...
            if ( !$enabled = get_option( 'share_post_types', array() ) ) $enabled = array();

            // maybe add new post types
            foreach ( $post_types as $post_type ) {
                if ( !array_key_exists( $post_type->name, $enabled ) ) $enabled[$post_type->name] = 0;
            }

            foreach ( $enabled as $post_type => $status ) :
                if ( !array_key_exists( $post_type, $post_types ) ) continue;

                $post_type = $post_types[$post_type];
                $id = 'share_post_type_' . $post_type->name; ?>

                <tr valign="top">
                    <th scope="row"><?php echo $post_type->label; ?>:</th>
                    <td>
                        <input id="<?php echo $id; ?>" type="checkbox" class="regular-checkbox" name="share_post_types[<?php echo $post_type->name; ?>]" value="1"<?php echo ( $status ? ' checked="checked"' : '' ) ?> />
                        <label for="<?php echo $id; ?>"><?php _e( 'enable', 'share' ); ?></label>
                    </td>
                </tr>

            <?php endforeach; ?>

            </tbody>
        </table>

        <h3><?php _e( 'Enable/Disable and order share buttons', 'share' ) ?></h3>

        <table id="share__socials" class="form-table">
            <tbody>

            <?php foreach ( share_networks() as $network => $enabled ):
                $name = strtolower( $network );
                $id = "share__$name"; ?>

                <tr valign="top">
                    <th scope="row"><?php echo $network; ?>:</th>
                    <td>
                        <input id="<?php echo $id; ?>" type="checkbox" class="regular-checkbox" name="share_enabled[<?php echo $network; ?>]" value="1"<?php echo ( $enabled ? ' checked="checked"' : '' ) ?> />
                        <label for="<?php echo $id; ?>"><?php _e( 'enable', 'share' ); ?></label>
                    </td>
                </tr>

            <?php endforeach; ?>

            </tbody>
        </table>

        <?php //wp_nonce_field( 'update-options' ); ?>
        <input type="hidden" name="action" value="update" />

        <?php submit_button(); ?>

    </form>
</div>
