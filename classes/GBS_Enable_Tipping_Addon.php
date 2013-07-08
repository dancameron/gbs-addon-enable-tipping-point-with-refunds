<?php

class GBS_Enable_Tipping_Addon {
	const CREDIT_TYPE = 'balance'; // match up with Group_Buying_Accounts::CREDIT_TYPE
	const META_KEY_THRESHOLD = '_gbs_refund_deals';
	const NOTIFICATION_TYPE = 'refunded_user_notification';

	private static $tracker = NULL;
	public static function init() {
		require_once('GBS_Refund_Users.php');
		GBS_Refund_Users::init();

		if ( is_admin() ) {
			require_once('GBS_Enable_Tipping_MetaBox.php');
			GBS_Enable_Tipping_MetaBox::init();
		}

		require_once('GBS_Enable_Tipping_Notification.php');
		GBS_Enable_Tipping_Notification::init();
	}

	public static function gb_addon( $addons ) {
		$addons['enable_tipping_refunds'] = array(
			'label' => gb__( 'Enable tipping, with refunds.' ),
			'description' => gb__( 'Enable tipping points on all payment processors, if the deal fails to tip than the customer will be refunded via their account balance.' ),
			'files' => array(),
			'callbacks' => array(
				array( __CLASS__, 'init' ),
			),
			'active' => TRUE,
		);
		return $addons;
	}

}
