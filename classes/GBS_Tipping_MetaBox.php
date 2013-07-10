<?php

class GBS_Tipping_MetaBox {

	public static function init() {
		// Remove purchase limit meta box filters
		self::gbs_remove_purchase_limit_filters();

		// FUTURE per deal options
		//add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ), 10, 0 );
		//add_action( 'save_post', array( __CLASS__, 'save_meta_box' ), 10, 2 );
	}

	protected static function gbs_remove_purchase_limit_filters() {
		remove_all_filters( 'group_buying_template_meta_boxes/deal-expiration.php');
		remove_all_filters( 'group_buying_template_meta_boxes/deal-price.php');
		remove_all_filters( 'group_buying_template_meta_boxes/deal-limits.php');
	}

	public static function add_meta_box() {
		add_meta_box( 'gbs_enable_tipping', gb__( 'Enable Tipping' ), array( __CLASS__, 'show_meta_box' ), Group_Buying_Deal::POST_TYPE, 'side' );
	}

	public static function show_meta_box( $post ) {
		$threshold = (int)get_post_meta($post->ID, TODO::META_KEY_THRESHOLD, TRUE);
		$enabled = !empty($threshold);
		printf('<input type="checkbox" value="enabled" %s name="gbs_enable_tipping[enable]" /> ', checked($enabled, TRUE, FALSE));
		gb_e('Refund payments (as rewards) if deal fails.');
	}

	public static function save_meta_box( $post_id, $post ) {
		// only continue if it's a deal post
		if ( $post->post_type != Group_Buying_Deal::POST_TYPE ) {
			return;
		}
		// don't do anything on autosave, auto-draft, bulk edit, or quick edit
		if ( empty($_POST['gbs_enable_tipping']) || wp_is_post_autosave( $post_id ) || $post->post_status == 'auto-draft' || defined( 'DOING_AJAX' ) || isset( $_GET['bulk_edit'] ) ) {
			return;
		}
	}
}
