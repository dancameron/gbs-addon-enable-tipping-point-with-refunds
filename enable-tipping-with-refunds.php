<?php
/*
Plugin Name: Group Buying Addon - Enable Tipping, with Refunds
Version: 1.0
Description: Enable tipping points on all payment processors, if the deal fails to tip than the customer will be refunded via their account balance.
Plugin URI: http://groupbuyingsite.com/marketplace
Author: Sprout Venture
Author URI: http://sproutventure.com/wordpress
Plugin Author: Dan Cameron
Contributors: Dan Cameron
Text Domain: group-buying
Domain Path: /lang
*/


// Load after all other plugins since we need to be compatible with groupbuyingsite
add_action( 'plugins_loaded', 'gb_load_enable_tipping_refunds' );
function gb_load_enable_tipping_refunds() {
	$gbs_min_version = '4.4';
	if ( class_exists( 'Group_Buying_Controller' ) && version_compare( Group_Buying::GB_VERSION, $gbs_min_version, '>=' ) ) {
		require_once 'classes/GBS_Enable_Tipping_Addon.php';

		// Hook this plugin into the GBS add-ons controller
		add_filter( 'gb_addons', array( 'GBS_Enable_Tipping_Addon', 'gb_addon' ), 10, 1 );
	}
}
