<?php

class GBS_Enable_Tipping_Notification {
	const NOTIFICATION_TYPE = 'free_deal_notification';

	public static function init() {
		add_filter( 'gb_notification_types', array( get_class(), 'register_notification_type' ), 10, 1 );
		add_action( 'gb_apply_free_deal_reward', array( get_class(), 'notification' ), 10, 2 );
	}

	public function register_notification_type( $notifications ) {
		$notifications[self::NOTIFICATION_TYPE] = array(
			'name' => gb__( 'Free Deal Notification' ),
			'description' => gb__( "Customize the notification sent to the customer after receiving a free deal." ),
			'shortcodes' => array( 'date', 'name', 'username', 'deal_url', 'deal_title', 'site_title', 'site_url' ),
			'default_title' => gb__( 'Free Deal at ' . get_bloginfo( 'name' ) ),
			'default_content' => sprintf( 'You just received a free deal at %s.', get_bloginfo( 'name' ) ),
			'allow_preference' => TRUE
		);
		return $notifications;
	}

	public function notification( $user_id, $deal_id ) {
		$deal = Group_Buying_Deal::get_instance( $deal_id );
		$to = Group_Buying_Notifications::get_user_email( $user_id );
		$data = array(
			'user_id' => $user_id,
			'deal' => $deal
		);
		Group_Buying_Notifications::send_notification( self::NOTIFICATION_TYPE, $data, $to );
	}

}
