<?php
require_once ABZ_TWITTER_FEED_PLUGIN_DIR . '/lib/twitter_api_client.php';
require_once ABZ_TWITTER_FEED_PLUGIN_DIR . '/lib/API_cache.php';
require_once ABZ_TWITTER_FEED_PLUGIN_DIR . '/lib/cached_twitter_timeline_reader.php';
//////////////////////////////////////////////////////////////////////////////
// Adding css and js files on theme
//////////////////////////////////////////////////////////////////////////////
function abz_twitter_feed_register_scripts() {
    wp_register_style('abz_twitter_feed-css', ABZ_TWITTER_FEED_PLUGIN_URL.'/includes/css/styles.css','',ABZ_TWITTER_FEED_VERSION, 'all');
    wp_register_script('abz_twitter_feed_script', ABZ_TWITTER_FEED_PLUGIN_URL.'/includes/js/scripts.js','',ABZ_TWITTER_FEED_VERSION, false);
}
add_action('init', 'abz_twitter_feed_register_scripts');

function abz_twitter_feed_enqueue_scripts() {
	wp_enqueue_style(array( 'abz_twitter_feed-css' ));// css files
	wp_enqueue_script(array( 'jquery','abz_twitter_feed_script' ));// js files
	$urls = array( 'admin_url' => admin_url('admin-ajax.php'), 'pluginUrl' => ABZ_TWITTER_FEED_PLUGIN_URL  );
	wp_localize_script( 'abz_twitter_feed_script', 'urls', $urls );
}

////////////////////////////////////////////////////////////////////
// Add Ajax Handler for test Settings 
////////////////////////////////////////////////////////////////////
function abz_test_oauth_settings(){
	//get the data from ajax() call
	global $abz_twitter_feed_settings;
	
	//TODO: Fill these values by the ones supplied on extension's settings page
	$consumer_key = 'dasdarewefs';
	$consumer_secret = $abz_twitter_feed_settings['consumer_secret'];

	$oauth_access_token = $abz_twitter_feed_settings['access_token'];
	$oauth_access_token_secret = $abz_twitter_feed_settings['access_token_secret'];

	//TODO: If values missing, send an error and if admin_logged_in send actual reason 

	$oauth = array( 'oauth_consumer_key' => $consumer_key,
					'oauth_token' => $oauth_access_token,
					'oauth_consumer_secret' => $consumer_secret,
					'oauth_access_token_secret' => $oauth_access_token_secret
					);

	$reader = new ABZ_Twitter_Api_Client($oauth);

	$results = $reader ->read_timeline();

	// Return the String
	die($results);
}
// creating Ajax call for WordPress
add_action( 'wp_ajax_nopriv_abz_test_oauth_settings', 'abz_test_oauth_settings' );
add_action( 'wp_ajax_abz_test_oauth_settings', 'abz_test_oauth_settings' );

////////////////////////////////////////////////////////////////////
// Add Ajax Handler for getting twitter timeline
////////////////////////////////////////////////////////////////////
/**
* Returns a string that contains the json representing the timeline
*/
function abz_get_twitter_feed() {
	//get the data from ajax() call
	global $abz_twitter_feed_settings;
	
	$consumer_key = $abz_twitter_feed_settings['consumer_key'];
	$consumer_secret = $abz_twitter_feed_settings['consumer_secret'];

	$oauth_access_token = $abz_twitter_feed_settings['access_token'];
	$oauth_access_token_secret = $abz_twitter_feed_settings['access_token_secret'];

	
	//*
	if (!($consumer_key && $consumer_secret && $oauth_access_token && $oauth_access_token_secret)) {
		
		//TODO: If one or more values missing, send an error and if admin_logged_in send actual reason 

				
		if (current_user_can( 'manage_options' )) {
			//TODO: l8n 
			$err_msg = "<div>" . __("AppBakerz Twitter Feed is not configured properly.") . "<br>";
			//TODO: Correct Link is not shown. 
			$err_msg = $err_msg . sprintf(__( "Visit %splugin settings page%s to complete configuration."), "<a href='" . menu_page_url( 'abz_twitter_feed', false ) . "'>", "</a>") . "</div>";
		}
		else {
			//TODO: l8n 
			$err_msg = __("<div>Twitter Feed is unavailable!</div>");
		}
		
		return '{"status": "error", "msg": "' . $err_msg . '"}';

	}	
	//*/

    $count = $_REQUEST["count"];

	$oauth = array( 'oauth_consumer_key' => $consumer_key,
					'oauth_token' => $oauth_access_token,
					'oauth_consumer_secret' => $consumer_secret,
					'oauth_access_token_secret' => $oauth_access_token_secret
					);

	$reader = new ABZ_Cached_Twitter_Timeline_Reader($oauth);

	$results = $reader ->get_json();

    // pull only as many tweet as $count

    if (isset($count) && is_numeric($count)) {
        $arr = json_decode($results);
        $arr = array_slice($arr, 0, $count);
        $results = json_encode($arr);
    }

	return $results;
}

/*******************************************************************************
* Called from ajax. The request will terminate when this function is called
********************************************************************************/
function abz_get_twitter_feed_ajax(){
	$results = abz_get_twitter_feed();

	// Return the String
	die($results);
}


// creating Ajax call for WordPress
add_action( 'wp_ajax_nopriv_abz_get_twitter_feed', 'abz_get_twitter_feed_ajax' );
add_action( 'wp_ajax_abz_get_twitter_feed', 'abz_get_twitter_feed_ajax' );
