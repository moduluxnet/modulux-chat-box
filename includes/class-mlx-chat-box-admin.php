<?php
if ( ! defined( 'ABSPATH' ) ) exit;

final class MLX_Chat_Box_Admin {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
	}

	public static function add_menu() {
		add_options_page(
			__( 'Modulux Chat Box', 'modulux-chat-box' ),
			__( 'Modulux Chat Box', 'modulux-chat-box' ),
			'manage_options',
			'mlx-chat-box',
			array( __CLASS__, 'render_page' )
		);
	}

	public static function enqueue( $hook ) {
		if ( 'settings_page_mlx-chat-box' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'mlx-chat-box-admin', MLX_CHAT_BOX_URL . 'assets/css/admin.css', array(), MLX_CHAT_BOX_VERSION );
		wp_enqueue_media();
		wp_enqueue_script( 'mlx-chat-box-admin', MLX_CHAT_BOX_URL . 'assets/js/admin.js', array( 'jquery' ), MLX_CHAT_BOX_VERSION, true );

        // Color picker
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );  
	}

	public static function register_settings() {
		register_setting(
			'mlx_chat_box_group',
			MLX_Chat_Box::OPTION_KEY,
			array( __CLASS__, 'sanitize_options' )
		);

		add_settings_section(
			'mlx_chat_box_section_main',
			__( 'General', 'modulux-chat-box' ),
			'__return_false',
			'mlx-chat-box'
		);

		add_settings_field(
			'enabled',
			__( 'Enable chat box', 'modulux-chat-box' ),
			array( __CLASS__, 'field_enabled' ),
			'mlx-chat-box',
			'mlx_chat_box_section_main'
		);

		add_settings_section(
			'mlx_chat_box_section_launcher',
			__( 'Launcher & Position', 'modulux-chat-box' ),
			'__return_false',
			'mlx-chat-box'
		);

        add_settings_field(
            'launcher_style',
            __( 'Launcher styling', 'modulux-chat-box' ),
            array( __CLASS__, 'field_launcher_style' ),
            'mlx-chat-box',
            'mlx_chat_box_section_launcher'
        );

		add_settings_field(
			'launcher',
			__( 'Launcher icon', 'modulux-chat-box' ),
			array( __CLASS__, 'field_launcher' ),
			'mlx-chat-box',
			'mlx_chat_box_section_launcher'
		);

		add_settings_field(
			'position',
			__( 'Position', 'modulux-chat-box' ),
			array( __CLASS__, 'field_position' ),
			'mlx-chat-box',
			'mlx_chat_box_section_launcher'
		);

		add_settings_section(
			'mlx_chat_box_section_colors',
			__( 'Colors', 'modulux-chat-box' ),
			'__return_false',
			'mlx-chat-box'
		);

		add_settings_field(
			'colors',
			__( 'Color settings', 'modulux-chat-box' ),
			array( __CLASS__, 'field_colors' ),
			'mlx-chat-box',
			'mlx_chat_box_section_colors'
		);

		add_settings_section(
			'mlx_chat_box_section_contact',
			__( 'Contact / Redirect', 'modulux-chat-box' ),
			'__return_false',
			'mlx-chat-box'
		);

		add_settings_field(
			'contact',
			__( 'Contact settings', 'modulux-chat-box' ),
			array( __CLASS__, 'field_contact' ),
			'mlx-chat-box',
			'mlx_chat_box_section_contact'
		);

        add_settings_field(
            'confirm_gate',
            __( 'Contact confirmation', 'modulux-chat-box' ),
            array( __CLASS__, 'field_confirm_gate' ),
            'mlx-chat-box',
            'mlx_chat_box_section_messages'
        );

		add_settings_section(
			'mlx_chat_box_section_messages',
			__( 'Texts & Templates', 'modulux-chat-box' ),
			'__return_false',
			'mlx-chat-box'
		);

		add_settings_field(
			'messages',
			__( 'UI texts', 'modulux-chat-box' ),
			array( __CLASS__, 'field_messages' ),
			'mlx-chat-box',
			'mlx_chat_box_section_messages'
		);

        add_settings_field(
            'trigger_selector',
            __( 'Open triggers (CSS selector)', 'modulux-chat-box' ),
            array( __CLASS__, 'field_trigger_selector' ),
            'mlx-chat-box',
            'mlx_chat_box_section_launcher'
        );

		add_settings_section(
			'mlx_chat_box_section_hours',
			__( 'Working Hours', 'modulux-chat-box' ),
			'__return_false',
			'mlx-chat-box'
		);

		add_settings_field(
			'hours',
			__( 'Schedule', 'modulux-chat-box' ),
			array( __CLASS__, 'field_hours' ),
			'mlx-chat-box',
			'mlx_chat_box_section_hours'
		);
	}

	public static function sanitize_options( $input ) {
		$defaults = MLX_Chat_Box::default_options();
		$input    = is_array( $input ) ? $input : array();

		$out = array();

		$out['enabled']            = ! empty( $input['enabled'] ) ? 1 : 0;

		$out['launcher_icon_type'] = in_array( $input['launcher_icon_type'] ?? '', array( 'dashicon', 'image' ), true ) ? $input['launcher_icon_type'] : $defaults['launcher_icon_type'];
		$out['launcher_dashicon']  = sanitize_text_field( $input['launcher_dashicon'] ?? $defaults['launcher_dashicon'] );
		$out['launcher_image_id']  = absint( $input['launcher_image_id'] ?? 0 );

		$out['position_mode']      = in_array( $input['position_mode'] ?? '', array( 'right', 'left', 'custom' ), true ) ? $input['position_mode'] : $defaults['position_mode'];
		$out['custom_css_pos']     = sanitize_text_field( $input['custom_css_pos'] ?? $defaults['custom_css_pos'] );
		$out['custom_css_pos_mobile'] = sanitize_text_field( $input['custom_css_pos_mobile'] ?? $defaults['custom_css_pos_mobile'] );

        $out['launcher_size_width']     = sanitize_text_field( $input['launcher_size_width'] ?? $defaults['launcher_size_width'] );
        $out['launcher_size_height']    = sanitize_text_field( $input['launcher_size_height'] ?? $defaults['launcher_size_height'] );
        $out['launcher_size_width_mobile']     = sanitize_text_field( $input['launcher_size_width_mobile'] ?? $defaults['launcher_size_width_mobile'] );
        $out['launcher_size_height_mobile']    = sanitize_text_field( $input['launcher_size_height_mobile'] ?? $defaults['launcher_size_height_mobile'] );        
        $out['launcher_icon_color']    = sanitize_hex_color( $input['launcher_icon_color'] ?? $defaults['launcher_icon_color'] );
        $out['launcher_bg_color']      = sanitize_hex_color( $input['launcher_bg_color'] ?? $defaults['launcher_bg_color'] );
        $out['launcher_border_width']  = isset( $input['launcher_border_width'] ) ? absint( $input['launcher_border_width'] ) : (int) $defaults['launcher_border_width'];
        $out['launcher_border_color']  = sanitize_hex_color( $input['launcher_border_color'] ?? $defaults['launcher_border_color'] );
        $out['launcher_border_radius'] = isset( $input['launcher_border_radius'] ) ? absint( $input['launcher_border_radius'] ) : (int) $defaults['launcher_border_radius'];

		$out['primary_color']      = sanitize_hex_color( $input['primary_color'] ?? $defaults['primary_color'] );
		$out['text_color']         = sanitize_hex_color( $input['text_color'] ?? $defaults['text_color'] );
		$out['panel_bg']           = sanitize_hex_color( $input['panel_bg'] ?? $defaults['panel_bg'] );

		$out['contact_mode']       = in_array( $input['contact_mode'] ?? '', array( 'whatsapp', 'custom' ), true ) ? $input['contact_mode'] : $defaults['contact_mode'];
		$out['whatsapp_number']    = preg_replace( '/[^0-9]/', '', (string) ( $input['whatsapp_number'] ?? '' ) );
		$out['custom_url']         = esc_url_raw( $input['custom_url'] ?? '' );

		$out['header_title']       = sanitize_text_field( $input['header_title'] ?? $defaults['header_title'] );
		$out['search_placeholder'] = sanitize_text_field( $input['search_placeholder'] ?? $defaults['search_placeholder'] );
		$out['offline_message']    = sanitize_text_field( $input['offline_message'] ?? $defaults['offline_message'] );
		$out['contact_label']      = sanitize_text_field( $input['contact_label'] ?? $defaults['contact_label'] );

        $out['require_confirm'] = ! empty( $input['require_confirm'] ) ? 1 : 0;
        $out['confirm_text']    = sanitize_text_field( $input['confirm_text'] ?? $defaults['confirm_text'] );

		$out['product_template']   = wp_kses_post( $input['product_template'] ?? $defaults['product_template'] );

        $out['trigger_selector'] = sanitize_text_field( $input['trigger_selector'] ?? $defaults['trigger_selector'] );

		$out['use_hours']          = ! empty( $input['use_hours'] ) ? 1 : 0;

		$days = array( 'monday','tuesday','wednesday','thursday','friday','saturday','sunday' );
		$out['hours'] = array();
		foreach ( $days as $day ) {
			$enabled = ! empty( $input['hours'][ $day ]['enabled'] ) ? 1 : 0;
			$start   = sanitize_text_field( $input['hours'][ $day ]['start'] ?? $defaults['hours'][ $day ]['start'] );
			$end     = sanitize_text_field( $input['hours'][ $day ]['end'] ?? $defaults['hours'][ $day ]['end'] );

			// Basic HH:MM validation fallback
			if ( ! preg_match( '/^\d{2}:\d{2}$/', $start ) ) $start = $defaults['hours'][ $day ]['start'];
			if ( ! preg_match( '/^\d{2}:\d{2}$/', $end ) )   $end   = $defaults['hours'][ $day ]['end'];

			$out['hours'][ $day ] = array(
				'enabled' => $enabled,
				'start'   => $start,
				'end'     => $end,
			);
		}

		return wp_parse_args( $out, $defaults );
	}

	private static function opts() {
		return MLX_Chat_Box::get_options();
	}

	public static function field_enabled() {
		$opts = self::opts();
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( MLX_Chat_Box::OPTION_KEY ); ?>[enabled]" value="1" <?php checked( 1, (int) $opts['enabled'] ); ?> />
			<?php esc_html_e( 'Show the launcher button and chat panel on the frontend.', 'modulux-chat-box' ); ?>
		</label>
		<?php
	}

	public static function field_launcher() {
		$opts = self::opts();
		$key  = MLX_Chat_Box::OPTION_KEY;
		$image_url = $opts['launcher_image_id'] ? wp_get_attachment_image_url( $opts['launcher_image_id'], 'thumbnail' ) : '';
		?>
		<!--<div class="mlx-field">
			<p>
				<label>
					<input type="radio" name="<?php echo esc_attr( $key ); ?>[launcher_icon_type]" value="dashicon" <?php checked( 'dashicon', $opts['launcher_icon_type'] ); ?> />
					<?php esc_html_e( 'Dashicon class', 'modulux-chat-box' ); ?>
				</label>
				&nbsp;&nbsp;
				<label>
					<input type="radio" name="<?php echo esc_attr( $key ); ?>[launcher_icon_type]" value="image" <?php checked( 'image', $opts['launcher_icon_type'] ); ?> />
					<?php esc_html_e( 'Custom image', 'modulux-chat-box' ); ?>
				</label>
			</p>

			<p>
				<input type="text" class="regular-text" name="<?php echo esc_attr( $key ); ?>[launcher_dashicon]" value="<?php echo esc_attr( $opts['launcher_dashicon'] ); ?>" />
				<span class="description"><?php esc_html_e( 'Example: dashicons-whatsapp, dashicons-format-chat', 'modulux-chat-box' ); ?></span>
			</p>

			<div class="mlx-media">
				<input type="hidden" class="mlx-media-id" name="<?php echo esc_attr( $key ); ?>[launcher_image_id]" value="<?php echo esc_attr( (string) $opts['launcher_image_id'] ); ?>" />
				<button type="button" class="button mlx-media-pick"><?php esc_html_e( 'Select image', 'modulux-chat-box' ); ?></button>
				<button type="button" class="button mlx-media-clear"><?php esc_html_e( 'Remove', 'modulux-chat-box' ); ?></button>
				<div class="mlx-media-preview">
					<?php if ( $image_url ) : ?>
						<img src="<?php echo esc_url( $image_url ); ?>" alt="" />
					<?php endif; ?>
				</div>
			</div>
		</div>-->
        <p>
            <label>
                <input type="radio" name="<?php echo esc_attr( $key ); ?>[launcher_icon_type]" value="dashicon" <?php checked( 'dashicon', $opts['launcher_icon_type'] ); ?> />
                <?php esc_html_e( 'Dashicon class', 'modulux-chat-box' ); ?>
            </label>
            &nbsp;&nbsp;
            <label>
                <input type="radio" name="<?php echo esc_attr( $key ); ?>[launcher_icon_type]" value="image" <?php checked( 'image', $opts['launcher_icon_type'] ); ?> />
                <?php esc_html_e( 'Custom image', 'modulux-chat-box' ); ?>
            </label>
        </p>

        <div class="mlx-launcher-dashicon">
            <p>
                <input type="text" class="regular-text"
                    name="<?php echo esc_attr( $key ); ?>[launcher_dashicon]"
                    value="<?php echo esc_attr( $opts['launcher_dashicon'] ); ?>" />
                <span class="description"><?php esc_html_e( 'Example: dashicons-whatsapp, dashicons-format-chat', 'modulux-chat-box' ); ?></span>
            </p>
        </div>

        <div class="mlx-launcher-image">
            <div class="mlx-media">
                <input type="hidden" class="mlx-media-id"
                    name="<?php echo esc_attr( $key ); ?>[launcher_image_id]"
                    value="<?php echo esc_attr( (string) $opts['launcher_image_id'] ); ?>" />
                <button type="button" class="button mlx-media-pick"><?php esc_html_e( 'Select image', 'modulux-chat-box' ); ?></button>
                <button type="button" class="button mlx-media-clear"><?php esc_html_e( 'Remove', 'modulux-chat-box' ); ?></button>
                <div class="mlx-media-preview">
                    <?php if ( $image_url ) : ?>
                        <img src="<?php echo esc_url( $image_url ); ?>" alt="" />
                    <?php endif; ?>
                </div>
            </div>
        </div>
		<?php
	}

	/*public static function field_position() {
		$opts = self::opts();
		$key  = MLX_Chat_Box::OPTION_KEY;
		?>
		<p>
			<select name="<?php echo esc_attr( $key ); ?>[position_mode]">
				<option value="right" <?php selected( 'right', $opts['position_mode'] ); ?>><?php esc_html_e( 'Right bottom', 'modulux-chat-box' ); ?></option>
				<option value="left" <?php selected( 'left', $opts['position_mode'] ); ?>><?php esc_html_e( 'Left bottom', 'modulux-chat-box' ); ?></option>
				<option value="custom" <?php selected( 'custom', $opts['position_mode'] ); ?>><?php esc_html_e( 'Custom CSS', 'modulux-chat-box' ); ?></option>
			</select>
		</p>
		<p>
			<input type="text" class="regular-text" name="<?php echo esc_attr( $key ); ?>[custom_css_pos]" value="<?php echo esc_attr( $opts['custom_css_pos'] ); ?>" />
			<span class="description"><?php esc_html_e( 'Only used when Position = Custom. Example: left:10px; bottom:10px;', 'modulux-chat-box' ); ?></span>
		</p>
		<?php
	}*/
    public static function field_position() {
        $opts = self::opts();
        $key  = MLX_Chat_Box::OPTION_KEY;
        ?>
        <p>
            <select name="<?php echo esc_attr( $key ); ?>[position_mode]" id="mlx_position_mode">
                <option value="right" <?php selected( 'right', $opts['position_mode'] ); ?>><?php esc_html_e( 'Right bottom', 'modulux-chat-box' ); ?></option>
                <option value="left" <?php selected( 'left', $opts['position_mode'] ); ?>><?php esc_html_e( 'Left bottom', 'modulux-chat-box' ); ?></option>
                <option value="custom" <?php selected( 'custom', $opts['position_mode'] ); ?>><?php esc_html_e( 'Custom CSS', 'modulux-chat-box' ); ?></option>
            </select>
        </p>

        <div class="mlx-position-custom">
            <p>
                <label><?php esc_html_e( 'Desktop position CSS:', 'modulux-chat-box' ); ?></label><br/>
                <input type="text" class="regular-text"
                    name="<?php echo esc_attr( $key ); ?>[custom_css_pos]"
                    value="<?php echo esc_attr( $opts['custom_css_pos'] ); ?>" />
                <span class="description"><?php esc_html_e( 'Only used when Position = Custom. Example: left:10px; bottom:10px;', 'modulux-chat-box' ); ?></span>
            </p>
        </div>

        <div class="mlx-position-custom-mobile">
            <p>
                <label><?php esc_html_e( 'Mobile position CSS:', 'modulux-chat-box' ); ?></label><br/>
                <input type="text" class="regular-text"
                    name="<?php echo esc_attr( $key ); ?>[custom_css_pos_mobile]"
                    value="<?php echo esc_attr( $opts['custom_css_pos_mobile'] ); ?>" />
                <span class="description"><?php esc_html_e( 'Only used when Position = Custom. Example: left:10px; bottom:10px;', 'modulux-chat-box' ); ?></span>
            </p>
        </div>        
        <?php
    }

    public static function field_launcher_style() {
        $opts = self::opts();
        $key  = MLX_Chat_Box::OPTION_KEY;
        ?>
        <table class="form-table mlx-launcher-style">
            <tr>
                <th><?php esc_html_e( 'Size (px)', 'modulux-chat-box' ); ?></th>
                <td>
                    <input type="number" style="width: 60px;"
                        name="<?php echo esc_attr( $key ); ?>[launcher_size_width]"
                        value="<?php echo esc_attr( $opts['launcher_size_width'] ); ?>" />
                        x
                    <input type="number" style="width: 60px;"
                        name="<?php echo esc_attr( $key ); ?>[launcher_size_height]"
                        value="<?php echo esc_attr( $opts['launcher_size_height'] ); ?>" />
                    <span class="description"><?php esc_html_e( 'Width x Height (e.g., 60x60)', 'modulux-chat-box' ); ?></span>
                </td>
            </tr>

            <tr>
                <th><?php esc_html_e( 'Size for mobile devices (px)', 'modulux-chat-box' ); ?></th>
                <td>
                    <input type="number" style="width: 60px;"
                        name="<?php echo esc_attr( $key ); ?>[launcher_size_width_mobile]"
                        value="<?php echo esc_attr( $opts['launcher_size_width_mobile'] ); ?>" />
                        x
                    <input type="number" style="width: 60px;"
                        name="<?php echo esc_attr( $key ); ?>[launcher_size_height_mobile]"
                        value="<?php echo esc_attr( $opts['launcher_size_height_mobile'] ); ?>" />
                    <span class="description"><?php esc_html_e( 'Width x Height (e.g., 60x60)', 'modulux-chat-box' ); ?></span>
                </td>
            </tr>            

            <tr>
                <th><?php esc_html_e( 'Icon color', 'modulux-chat-box' ); ?></th>
                <td>
                    <input type="text" class="mlx-color-field"
                        name="<?php echo esc_attr( $key ); ?>[launcher_icon_color]"
                        value="<?php echo esc_attr( $opts['launcher_icon_color'] ); ?>" />
                </td>
            </tr>

            <tr>
                <th><?php esc_html_e( 'Background color', 'modulux-chat-box' ); ?></th>
                <td>
                    <input type="text" class="mlx-color-field"
                        name="<?php echo esc_attr( $key ); ?>[launcher_bg_color]"
                        value="<?php echo esc_attr( $opts['launcher_bg_color'] ); ?>" />
                </td>
            </tr>

            <tr>
                <th><?php esc_html_e( 'Border width (px)', 'modulux-chat-box' ); ?></th>
                <td>
                    <input type="number" min="0" step="1" style="width:120px"
                        name="<?php echo esc_attr( $key ); ?>[launcher_border_width]"
                        value="<?php echo esc_attr( (string) (int) $opts['launcher_border_width'] ); ?>" />
                </td>
            </tr>

            <tr>
                <th><?php esc_html_e( 'Border color', 'modulux-chat-box' ); ?></th>
                <td>
                    <input type="text" class="mlx-color-field"
                        name="<?php echo esc_attr( $key ); ?>[launcher_border_color]"
                        value="<?php echo esc_attr( $opts['launcher_border_color'] ); ?>" />
                </td>
            </tr>

            <tr>
                <th><?php esc_html_e( 'Border radius (px)', 'modulux-chat-box' ); ?></th>
                <td>
                    <input type="number" min="0" step="1" style="width:120px"
                        name="<?php echo esc_attr( $key ); ?>[launcher_border_radius]"
                        value="<?php echo esc_attr( (string) (int) $opts['launcher_border_radius'] ); ?>" />
                    <span class="description"><?php esc_html_e( 'Tip: 999 makes it fully rounded.', 'modulux-chat-box' ); ?></span>
                </td>
            </tr>
        </table>
        <?php
    }

	public static function field_colors() {
		$opts = self::opts();
		$key  = MLX_Chat_Box::OPTION_KEY;
		?>
		<table class="form-table mlx-colors">
			<tr>
				<th><?php esc_html_e( 'Primary', 'modulux-chat-box' ); ?></th>
				<td><input type="text" class="mlx-color mlx-color-field" name="<?php echo esc_attr( $key ); ?>[primary_color]" value="<?php echo esc_attr( $opts['primary_color'] ); ?>" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Text', 'modulux-chat-box' ); ?></th>
				<td><input type="text" class="mlx-color mlx-color-field" name="<?php echo esc_attr( $key ); ?>[text_color]" value="<?php echo esc_attr( $opts['text_color'] ); ?>" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Panel background', 'modulux-chat-box' ); ?></th>
				<td><input type="text" class="mlx-color mlx-color-field" name="<?php echo esc_attr( $key ); ?>[panel_bg]" value="<?php echo esc_attr( $opts['panel_bg'] ); ?>" /></td>
			</tr>
		</table>
		<p class="description"><?php esc_html_e( 'Tip: You can use any valid hex color (#RRGGBB).', 'modulux-chat-box' ); ?></p>
		<?php
	}

	public static function field_contact() {
		$opts = self::opts();
		$key  = MLX_Chat_Box::OPTION_KEY;
		?>
		<!--<p>
			<select name="<?php echo esc_attr( $key ); ?>[contact_mode]">
				<option value="whatsapp" <?php selected( 'whatsapp', $opts['contact_mode'] ); ?>><?php esc_html_e( 'WhatsApp', 'modulux-chat-box' ); ?></option>
				<option value="custom" <?php selected( 'custom', $opts['contact_mode'] ); ?>><?php esc_html_e( 'Custom URL', 'modulux-chat-box' ); ?></option>
			</select>
		</p>

		<p>
			<label><?php esc_html_e( 'WhatsApp number (country code + number):', 'modulux-chat-box' ); ?></label><br/>
			<input type="text" class="regular-text" name="<?php echo esc_attr( $key ); ?>[whatsapp_number]" value="<?php echo esc_attr( $opts['whatsapp_number'] ); ?>" />
			<span class="description"><?php esc_html_e( 'Digits only recommended. Example: 905551112233', 'modulux-chat-box' ); ?></span>
		</p>

		<p>
			<label><?php esc_html_e( 'Custom URL:', 'modulux-chat-box' ); ?></label><br/>
			<input type="url" class="regular-text" name="<?php echo esc_attr( $key ); ?>[custom_url]" value="<?php echo esc_url( $opts['custom_url'] ); ?>" />
			<span class="description"><?php esc_html_e( 'Used when Contact Mode = Custom URL.', 'modulux-chat-box' ); ?></span>
		</p>-->
            <p>
                <select name="<?php echo esc_attr( $key ); ?>[contact_mode]" id="mlx_contact_mode">
                    <option value="whatsapp" <?php selected( 'whatsapp', $opts['contact_mode'] ); ?>><?php esc_html_e( 'WhatsApp', 'modulux-chat-box' ); ?></option>
                    <option value="custom" <?php selected( 'custom', $opts['contact_mode'] ); ?>><?php esc_html_e( 'Custom URL', 'modulux-chat-box' ); ?></option>
                </select>
            </p>

            <div class="mlx-contact-whatsapp">
                <p>
                    <label><?php esc_html_e( 'WhatsApp number (country code + number):', 'modulux-chat-box' ); ?></label><br/>
                    <input type="text" class="regular-text"
                        name="<?php echo esc_attr( $key ); ?>[whatsapp_number]"
                        value="<?php echo esc_attr( $opts['whatsapp_number'] ); ?>" />
                    <span class="description"><?php esc_html_e( 'Digits only recommended. Example: 905551112233', 'modulux-chat-box' ); ?></span>
                </p>
            </div>

            <div class="mlx-contact-custom">
                <p>
                    <label><?php esc_html_e( 'Custom URL:', 'modulux-chat-box' ); ?></label><br/>
                    <input type="url" class="regular-text"
                        name="<?php echo esc_attr( $key ); ?>[custom_url]"
                        value="<?php echo esc_url( $opts['custom_url'] ); ?>" />
                    <span class="description"><?php esc_html_e( 'Used when Contact Mode = Custom URL.', 'modulux-chat-box' ); ?></span>
                </p>
            </div>
		<?php
	}

    public static function field_confirm_gate() {
        $opts = self::opts();
        $key  = MLX_Chat_Box::OPTION_KEY;
        ?>
        <p>
            <label>
                <input type="checkbox" name="<?php echo esc_attr( $key ); ?>[require_confirm]" value="1" <?php checked( 1, (int) $opts['require_confirm'] ); ?> />
                <?php esc_html_e( 'Require a confirmation checkbox before enabling the contact button.', 'modulux-chat-box' ); ?>
            </label>
        </p>
        <p>
            <label><?php esc_html_e( 'Checkbox label:', 'modulux-chat-box' ); ?></label><br/>
            <input type="text" class="regular-text"
                name="<?php echo esc_attr( $key ); ?>[confirm_text]"
                value="<?php echo esc_attr( $opts['confirm_text'] ); ?>" />
        </p>
        <?php
    }

	public static function field_messages() {
		$opts = self::opts();
		$key  = MLX_Chat_Box::OPTION_KEY;
		?>
		<p>
			<label><?php esc_html_e( 'Header title:', 'modulux-chat-box' ); ?></label><br/>
			<input type="text" class="regular-text" name="<?php echo esc_attr( $key ); ?>[header_title]" value="<?php echo esc_attr( $opts['header_title'] ); ?>" />
		</p>

		<p>
			<label><?php esc_html_e( 'Search placeholder:', 'modulux-chat-box' ); ?></label><br/>
			<input type="text" class="regular-text" name="<?php echo esc_attr( $key ); ?>[search_placeholder]" value="<?php echo esc_attr( $opts['search_placeholder'] ); ?>" />
		</p>

		<p>
			<label><?php esc_html_e( 'Offline message:', 'modulux-chat-box' ); ?></label><br/>
			<input type="text" class="regular-text" name="<?php echo esc_attr( $key ); ?>[offline_message]" value="<?php echo esc_attr( $opts['offline_message'] ); ?>" />
		</p>

		<p>
			<label><?php esc_html_e( 'Contact button label:', 'modulux-chat-box' ); ?></label><br/>
			<input type="text" class="regular-text" name="<?php echo esc_attr( $key ); ?>[contact_label]" value="<?php echo esc_attr( $opts['contact_label'] ); ?>" />
		</p>

		<p>
			<label><?php esc_html_e( 'Product page message template:', 'modulux-chat-box' ); ?></label><br/>
			<textarea name="<?php echo esc_attr( $key ); ?>[product_template]" rows="3" class="large-text"><?php echo esc_textarea( $opts['product_template'] ); ?></textarea>
			<span class="description">
				<?php esc_html_e( 'Placeholders: {product_title} {sku} {url}', 'modulux-chat-box' ); ?>
			</span>
		</p>
		<?php
	}

    public static function field_trigger_selector() {
        $opts = self::opts();
        $key  = MLX_Chat_Box::OPTION_KEY;
        ?>
        <p>
            <input type="text"
                class="regular-text"
                name="<?php echo esc_attr( $key ); ?>[trigger_selector]"
                value="<?php echo esc_attr( $opts['trigger_selector'] ); ?>" />
        </p>
        <p class="description">
            <?php esc_html_e( 'Any element matching this selector will open the chat box. Example: .mlx-chat-open or a[data-open-chat="1"]', 'modulux-chat-box' ); ?>
        </p>
        <?php
    }    

	public static function field_hours() {
		$opts = self::opts();
		$key  = MLX_Chat_Box::OPTION_KEY;

		$days = array(
			'monday'    => __( 'Monday', 'modulux-chat-box' ),
			'tuesday'   => __( 'Tuesday', 'modulux-chat-box' ),
			'wednesday' => __( 'Wednesday', 'modulux-chat-box' ),
			'thursday'  => __( 'Thursday', 'modulux-chat-box' ),
			'friday'    => __( 'Friday', 'modulux-chat-box' ),
			'saturday'  => __( 'Saturday', 'modulux-chat-box' ),
			'sunday'    => __( 'Sunday', 'modulux-chat-box' ),
		);
		?>
		<p>
			<label class="mlx-use-hours-label">
				<input type="checkbox" name="<?php echo esc_attr( $key ); ?>[use_hours]" value="1" <?php checked( 1, (int) $opts['use_hours'] ); ?> />
				<?php esc_html_e( 'Enable working hours limitation (outside hours shows Offline message and disables contact).', 'modulux-chat-box' ); ?>
			</label>
		</p>

		<table class="widefat striped mlx-hours">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Day', 'modulux-chat-box' ); ?></th>
					<th><?php esc_html_e( 'Enabled', 'modulux-chat-box' ); ?></th>
					<th><?php esc_html_e( 'Start', 'modulux-chat-box' ); ?></th>
					<th><?php esc_html_e( 'End', 'modulux-chat-box' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $days as $day_key => $day_label ) : 
					$row = $opts['hours'][ $day_key ] ?? array( 'enabled' => 0, 'start' => '09:00', 'end' => '18:00' );
				?>
				<tr>
					<td><?php echo esc_html( $day_label ); ?></td>
					<td>
						<input type="checkbox" name="<?php echo esc_attr( $key ); ?>[hours][<?php echo esc_attr( $day_key ); ?>][enabled]" value="1" <?php checked( 1, (int) ( $row['enabled'] ?? 0 ) ); ?> />
					</td>
					<td>
						<input type="time" name="<?php echo esc_attr( $key ); ?>[hours][<?php echo esc_attr( $day_key ); ?>][start]" value="<?php echo esc_attr( $row['start'] ?? '09:00' ); ?>" />
					</td>
					<td>
						<input type="time" name="<?php echo esc_attr( $key ); ?>[hours][<?php echo esc_attr( $day_key ); ?>][end]" value="<?php echo esc_attr( $row['end'] ?? '18:00' ); ?>" />
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	/*public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Modulux Chat Box', 'modulux-chat-box' ); ?></h1>

			<p class="description">
				<?php esc_html_e( 'Add Q&As under “Chat Q&As” menu. The launcher shows a searchable list; if no answer, user can contact via WhatsApp (or custom URL).', 'modulux-chat-box' ); ?>
			</p>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'mlx_chat_box_group' );
					do_settings_sections( 'mlx-chat-box' );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}*/

    public static function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $current_tab = self::get_current_tab();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Modulux Chat Box', 'modulux-chat-box' ); ?></h1>

            <?php self::render_tabs_nav( $current_tab ); ?>

            <?php if ( 'settings' === $current_tab ) : ?>

                <p class="description">
                    <?php esc_html_e( 'Add Q&As under “Chat Q&As”. Users can search and optionally contact you via WhatsApp or a custom URL.', 'modulux-chat-box' ); ?>
                </p>

                <form method="post" action="options.php">
                    <?php
                        settings_fields( 'mlx_chat_box_group' );
                        do_settings_sections( 'mlx-chat-box' );
                        submit_button();
                    ?>
                </form>

            <?php elseif ( 'help' === $current_tab ) : ?>

                <?php self::render_help_tab(); ?>

            <?php elseif ( 'about' === $current_tab ) : ?>

                <?php self::render_about_tab(); ?>

            <?php endif; ?>

        </div>
        <?php
    }

    private static function get_tabs() {
        return array(
            'settings' => __( 'Settings', 'modulux-chat-box' ),
            'help'     => __( 'Help', 'modulux-chat-box' ),
            'about'    => __( 'About', 'modulux-chat-box' ),
        );
    }

    private static function get_current_tab() {
        // Tab switching is view-only (no state change). Nonce not required.
        $tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'settings'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $tabs = self::get_tabs();
        return isset( $tabs[ $tab ] ) ? $tab : 'settings';
    }

    private static function render_tabs_nav( $current_tab ) {
        $tabs = self::get_tabs();
        $base_url = admin_url( 'options-general.php?page=mlx-chat-box' );

        echo '<h2 class="nav-tab-wrapper">';

        foreach ( $tabs as $tab_key => $label ) {
            $url = add_query_arg( 'tab', $tab_key, $base_url );
            $classes = 'nav-tab' . ( $current_tab === $tab_key ? ' nav-tab-active' : '' );

            printf(
                '<a href="%1$s" class="%2$s">%3$s</a>',
                esc_url( $url ),
                esc_attr( $classes ),
                esc_html( $label )
            );
        }

        echo '</h2>';
    }

    private static function render_help_tab() {
        ?>
        <div class="mlx-tab-help">
            <h2><?php esc_html_e( 'How it works', 'modulux-chat-box' ); ?></h2>
            <ul>
                <li><?php esc_html_e( 'Create items in “Chat Q&As” (Title = Question, Content = Answer).', 'modulux-chat-box' ); ?></li>
                <li><?php esc_html_e( 'The floating button opens a searchable panel.', 'modulux-chat-box' ); ?></li>
                <li><?php esc_html_e( 'If the visitor still needs help, they can click Contact (WhatsApp or Custom URL).', 'modulux-chat-box' ); ?></li>
            </ul>

            <h2><?php esc_html_e( 'Open triggers', 'modulux-chat-box' ); ?></h2>
            <p>
                <?php esc_html_e( 'If you set “Open triggers (CSS selector)”, clicking any matching element will open the chat panel.', 'modulux-chat-box' ); ?>
            </p>
            <p>
                <code>.mlx-chat-open</code>
                &nbsp;<?php esc_html_e( 'Example button:', 'modulux-chat-box' ); ?>
            </p>
            <pre><code>&lt;button class="mlx-chat-open"&gt;Need help?&lt;/button&gt;</code></pre>

            <h2><?php esc_html_e( 'Product message placeholders', 'modulux-chat-box' ); ?></h2>
            <ul>
                <li><code>{product_title}</code></li>
                <li><code>{sku}</code></li>
                <li><code>{url}</code></li>
            </ul>

            <h2><?php esc_html_e( 'Multilingual (WPML / Polylang)', 'modulux-chat-box' ); ?></h2>
            <ul>
                <li><?php esc_html_e( 'Translate Q&As using your translation plugin (CPT is supported).', 'modulux-chat-box' ); ?></li>
                <li><?php esc_html_e( 'Option texts can be translated via WPML admin-texts (wpml-config.xml included) or string translation.', 'modulux-chat-box' ); ?></li>
            </ul>
        </div>
        <?php
    }

    private static function render_about_tab() {
        ?>
        <div class="mlx-tab-about">
            <h2><?php esc_html_e( 'About this plugin', 'modulux-chat-box' ); ?></h2>
            <p>
                <?php
                printf(
                    /* translators: %s: plugin version */
                    esc_html__( 'Modulux Chat Box version %s', 'modulux-chat-box' ),
                    esc_html( MLX_CHAT_BOX_VERSION )
                );
                ?>
            </p>

            <p>
                <?php esc_html_e( 'Built for WordPress.org quality: Settings API, sanitization, escaping, no bulky code, no ads, no external tracking.', 'modulux-chat-box' ); ?>
            </p>

            <p>
                <?php esc_html_e( 'Floating Q&A chat box with optional WhatsApp (or custom URL) contact link. Multilingual friendly (Polylang/WPML).', 'modulux-chat-box' ); ?>
            </p>

            <p>
                <?php esc_html_e( 'Modulux Chat Box adds a floating launcher button. When opened, users can search through your predefined questions & answers. If they can\'t find an answer, they can click "Contact" to open WhatsApp (or a custom URL). On WooCommerce product pages, the contact link can include a customizable product-aware template.', 'modulux-chat-box' ); ?>
            </p>

            <p>
                <strong><?php esc_html_e( 'Website:', 'modulux-chat-box' ); ?></strong>
                <a href="<?php echo esc_url( 'https://modulux.net' ); ?>" target="_blank" rel="noopener noreferrer">modulux.net</a>
            </p>
        </div>
        <?php
    }


}
