<?php

class GBS_Refund_Users {

	public static function init() {
		add_action( 'deal_failed', array( __CLASS__, 'maybe_refund' ) );
	}

	public function maybe_refund( Group_Buying_Deal $deal ) {
		$item_id = $deal->get_id();
		$refund = 0;
		// All purchases
		$purchases = $deal->get_purchases();
		foreach ( $purchases as $purchase ) {
			$payments = $purchase->get_payments();
			// All payments
			foreach ( $payments as $payment_id ) {
				$payment = Group_Buying_Payment::get_instance( $payment_id );
				$payment_method = $payment->get_payment_method();
				// Don't handle credit purchases
				if ( $payment_method != Group_Buying_Account_Balance_Payments::PAYMENT_METHOD || $payment_method != Group_Buying_Affiliate_Credit_Payments::PAYMENT_METHOD ) {
					$items = $payment->get_deals();
					// Loop through the payments deals
					foreach ( $items as $key_item_id => $purchase_item ) {
						// Only those deals that have failed
						if ( $item_id == $key_item_id ) {
							// loop through all items
							foreach ( $purchase_item as $key => $value ) {
								$refund += $items[$key_item_id][$key]['price'];
							}
						}
					}
				}
				// Refund the user



			}
		}
	}

}