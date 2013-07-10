<?php

class GBS_Tipping_Notification {
	const NOTIFICATION_TYPE = 'refunded_user_notification';

	public static function init() {
		add_filter( 'gb_notification_types', array( get_class(), 'register_notification_type' ), 10, 1 );
		add_action( 'gb_apply_refund', array( get_class(), 'notification' ), 10, 4 );
	}

	public function register_notification_type( $notifications ) {
		$notifications[self::NOTIFICATION_TYPE] = array(
			'name' => gb__( 'Payment Refunded Notification' ),
			'description' => gb__( "Customize the notification sent to the customer after being refunded for a deal that failed." ),
			'shortcodes' => array( 'date', 'name', 'username', 'deal_url', 'deal_title', 'site_title', 'site_url', 'reward' ),
			'default_title' => gb__( 'Payment Refunded at ' . get_bloginfo( 'name' ) ),
			'default_content' => sprintf( 'A payment of yours has been refunded for a failed item at %s.', get_bloginfo( 'name' ) ),
			'allow_preference' => TRUE
		);
		return $notifications;
	}

	public function notification( $user_id, $deal_id, $refund_amount, $credit_type ) {
		$deal = Group_Buying_Deal::get_instance( $deal_id );
		$to = Group_Buying_Notifications::get_user_email( $user_id );
		$data = array(
			'user_id' => $user_id,
			'deal' => $deal,
			'applied_credits' => $refund_amount,
			'type' => $credit_type

		);
		Group_Buying_Notifications::send_notification( self::NOTIFICATION_TYPE, $data, $to );
	}

}
