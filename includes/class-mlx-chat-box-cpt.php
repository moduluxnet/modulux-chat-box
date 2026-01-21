<?php
/* 
 * Custom Post Type class
 * @since 1.0.0
 * @package Modulux_Chat_Box
*/
if ( ! defined( 'ABSPATH' ) ) exit;

final class MLX_Chat_Box_CPT {

	const CPT = 'mlx_chat_qa';

	/* Initialize */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );

		// Polylang: allow translating this CPT if Polylang is active and user enables it.
		add_filter( 'pll_get_post_types', array( __CLASS__, 'polylang_add_cpt' ), 10, 2 );
	}

	/* Register CPT */
	public static function register() {
		$labels = array(
			'name'               => __( 'Chat Q&As', 'modulux-chat-box' ),
			'singular_name'      => __( 'Chat Q&A', 'modulux-chat-box' ),
			'add_new'            => __( 'Add New', 'modulux-chat-box' ),
			'add_new_item'       => __( 'Add New Q&A', 'modulux-chat-box' ),
			'edit_item'          => __( 'Edit Q&A', 'modulux-chat-box' ),
			'new_item'           => __( 'New Q&A', 'modulux-chat-box' ),
			'view_item'          => __( 'View Q&A', 'modulux-chat-box' ),
			'search_items'       => __( 'Search Q&As', 'modulux-chat-box' ),
			'not_found'          => __( 'No Q&As found', 'modulux-chat-box' ),
			'not_found_in_trash' => __( 'No Q&As found in Trash', 'modulux-chat-box' ),
			'menu_name'          => __( 'Chat Q&As', 'modulux-chat-box' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'menu_position'      => 26,
			'menu_icon'          => 'dashicons-format-chat',
			'supports'           => array( 'title', 'editor' ), // title = question, editor = answer
			'capability_type'    => 'post',
			'has_archive'        => false,
			'rewrite'            => false,
			'show_in_rest'       => true, // useful for modern editors + possible future block integration
		);

		register_post_type( self::CPT, $args );
	}

	/* Polylang integration: add CPT to translatable post types */
	public static function polylang_add_cpt( $post_types, $is_settings ) {
		if ( $is_settings ) {
			$post_types[ self::CPT ] = self::CPT;
		}
		return $post_types;
	}
}
