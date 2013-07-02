<?php

class GBS_Refund_Users {

	public static function init() {
		add_action( 'deal_failed', array( __CLASS__, 'maybe_refund' ) );
	}

	public function maybe_refund( Group_Buying_Deal $deal ) {
		$item_id = $deal->get_id();
		$refund_amount = 0;
		// All purchases
		$purchases = $deal->get_purchases();
		foreach ( $purchases as $purchase ) {
			$payments = $purchase->get_payments();
			// All payments
			foreach ( $payments as $payment_id ) {
				$payment = Group_Buying_Payment::get_instance( $payment_id );
				$payment_method = $payment->get_payment_method();
				// Don't handle credit payments
				if ( $payment_method != Group_Buying_Account_Balance_Payments::PAYMENT_METHOD || $payment_method != Group_Buying_Affiliate_Credit_Payments::PAYMENT_METHOD ) {

					$items = $payment->get_deals();
					// Loop through the payments deals
					foreach ( $items as $deal_id => $purchase_items ) {
						// Only those deals that have failed
						if ( $item_id == $deal_id ) {
							// loop through all items
							foreach ( $purchase_items as $item ) {
								foreach ( $item['payment_method'] as $payment_method => $payment ) {
									// again don't tally credits
									if ( $payment_method != Group_Buying_Account_Balance_Payments::PAYMENT_METHOD || $payment_method != Group_Buying_Affiliate_Credit_Payments::PAYMENT_METHOD ) {
										$refund_amount += $payment;
									}
								}
							}
						}
					}

				}
			}
			// Refund the user
			$account_id = $purchase->get_account_id();
			$account = Group_Buying_Account::get_instance_by_id( $account_id );

			// Add Credit
			$credit_type = apply_filters('gbs_tipping_point_refund', GBS_Enable_Tipping_Addon::CREDIT_TYPE);
			$account->add_credit( $refund_amount, $credit_type );

			// Fire off the notification manually
			GBS_Enable_Tipping_Notification::notification( $account->get_user_id(), $item_id, $refund_amount, $credit_type );

			// Record reward
			self::reward_applied_record( $account, $purchase->get_id(), $refund_amount, $credit_type );

			// Reset for other purchases
			$refund_amount = 0;
			// continue loop of purchases
		}
	}

	public static function reward_applied_record( $account, $purchase_id, $refund_amount, $credit_type ) {
		$account_id = $account->get_ID();
		$balance = $account->get_credit_balance( $type );

		// Setup data array
		$data = array();
		$data['account_id'] = $account_id;
		$data['purchase_id'] = $purchase_id;
		$data['credits'] = $refund_amount;
		$data['type'] = $type;
		$data['current_total_'.$type] = $balance;
		$data['change_'.$type] = $refund_amount;

		// Record
		Group_Buying_Records::new_record(
			sprintf( gb__( 'Payment Refunded from Purchase #%s' ), $purchase_id ),
			$credit_type,
			sprintf( gb__( 'Payment Refunded from Purchase #%s' ), $purchase_id ),
			1,
			$account_id,
			$data );
	}
}