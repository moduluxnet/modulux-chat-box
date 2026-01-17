<?php
if ( ! defined( 'ABSPATH' ) ) exit;

final class MLX_Chat_Box_Frontend {

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'wp_footer', array( __CLASS__, 'render' ) );
	}

	public static function enqueue() {
		$opts = MLX_Chat_Box::get_options();
		if ( empty( $opts['enabled'] ) ) {
			return;
		}

        wp_enqueue_style( 'mlx-chat-box-frontend', MLX_CHAT_BOX_URL . 'assets/css/frontend.css', array(), MLX_CHAT_BOX_VERSION );
		wp_enqueue_script( 'mlx-chat-box-frontend', MLX_CHAT_BOX_URL . 'assets/js/frontend.js', array(), MLX_CHAT_BOX_VERSION, true );

		$data = array(
			'ajaxUrl'           => admin_url( 'admin-ajax.php' ), // reserved for future (optional)
			'nonce'             => wp_create_nonce( 'mlx_chat_box' ),
			'texts'             => array(
				'search'   => $opts['search_placeholder'],
				'offline'  => $opts['offline_message'],
				'contact'  => $opts['contact_label'],
				'header'   => $opts['header_title'],
			),
			'isOnline'          => self::is_online_now(),
			'contactUrl'        => self::get_contact_url(),
			'positionMode'      => $opts['position_mode'],
			'customPos'         => $opts['custom_css_pos'],
			'colors'            => array(
				'primary' => $opts['primary_color'],
				'text'    => $opts['text_color'],
				'panelBg' => $opts['panel_bg'],
			),
			'launcher'          => array(
				'type'     => $opts['launcher_icon_type'],
				'dashicon' => $opts['launcher_dashicon'],
				'imageUrl' => $opts['launcher_image_id'] ? wp_get_attachment_image_url( $opts['launcher_image_id'], 'thumbnail' ) : '',
			),
			'qas'               => self::get_qas(),
            'triggerSelector'   => $opts['trigger_selector'],
			'productMessage'    => self::get_product_message_if_any(),
            'launcherStyle' => array(
                'width'        => (int) $opts['launcher_size_width'],
                'height'       => (int) $opts['launcher_size_height'],
                'iconColor'    => $opts['launcher_icon_color'],
                'bgColor'      => $opts['launcher_bg_color'],
                'borderWidth'  => (int) $opts['launcher_border_width'],
                'borderColor'  => $opts['launcher_border_color'],
                'borderRadius' => (int) $opts['launcher_border_radius'],
            ),
		);

		wp_localize_script( 'mlx-chat-box-frontend', 'MLXChatBox', $data );
	}

	private static function get_qas() {
		$args = array(
			'post_type'      => MLX_Chat_Box_CPT::CPT,
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		);

		// Polylang: query current language content if available
		if ( function_exists( 'pll_current_language' ) ) {
			$args['lang'] = pll_current_language();
		}

		$q = new WP_Query( $args );

		$list = array();
		if ( $q->have_posts() ) {
			foreach ( $q->posts as $p ) {
				$list[] = array(
					'id'       => (int) $p->ID,
					'question' => get_the_title( $p ),
                    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
					'answer'   => apply_filters( 'the_content', $p->post_content ),
				);
			}
		}
		wp_reset_postdata();

		return $list;
	}

	private static function is_online_now() {
		$opts = MLX_Chat_Box::get_options();
		if ( empty( $opts['use_hours'] ) ) {
			return true;
		}

		$tz_string = wp_timezone_string();
		$tz        = $tz_string ? new DateTimeZone( $tz_string ) : wp_timezone();
		$now       = new DateTime( 'now', $tz );

		$day_key = strtolower( $now->format( 'l' ) ); // monday...
		$hours   = $opts['hours'][ $day_key ] ?? null;

		if ( ! is_array( $hours ) || empty( $hours['enabled'] ) ) {
			return false;
		}

		$start = $hours['start'] ?? '09:00';
		$end   = $hours['end'] ?? '18:00';

		$start_dt = DateTime::createFromFormat( 'Y-m-d H:i', $now->format( 'Y-m-d' ) . ' ' . $start, $tz );
		$end_dt   = DateTime::createFromFormat( 'Y-m-d H:i', $now->format( 'Y-m-d' ) . ' ' . $end, $tz );

		if ( ! $start_dt || ! $end_dt ) {
			return true; // fail open
		}

		return ( $now >= $start_dt && $now <= $end_dt );
	}

	private static function get_contact_url() {
		$opts = MLX_Chat_Box::get_options();

		if ( 'custom' === $opts['contact_mode'] ) {
			return $opts['custom_url'] ? esc_url_raw( $opts['custom_url'] ) : '';
		}

		$number = preg_replace( '/[^0-9]/', '', (string) $opts['whatsapp_number'] );
		if ( ! $number ) {
			return '';
		}

		// Message is added client-side (to include product context).
		return 'https://wa.me/' . $number;
	}

	private static function get_product_message_if_any() {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return '';
		}
		if ( ! function_exists( 'wc_get_product' ) ) {
			return '';
		}

		//global $product;
        $product = wc_get_product( get_the_ID() );
		if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
			$product = wc_get_product( get_the_ID() );
		}
		if ( ! $product ) {
			return '';
		}

		$opts = MLX_Chat_Box::get_options();

		$title = $product->get_name();
		$sku   = $product->get_sku();
		$url   = get_permalink( $product->get_id() );

		$msg = (string) $opts['product_template'];
		$msg = str_replace( '{product_title}', $title, $msg );
		$msg = str_replace( '{sku}', $sku ? $sku : '', $msg );
		$msg = str_replace( '{url}', $url, $msg );

		return $msg;
	}

	public static function render() {
		$opts = MLX_Chat_Box::get_options();
		if ( empty( $opts['enabled'] ) ) {
			return;
		}

		// Wrapper is injected once.
		echo '<div id="mlx-chat-box-root" class="mlx-chat-box" aria-live="polite"></div>';
	}
}
