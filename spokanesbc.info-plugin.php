<?php
/*
Plugin Name: SpokaneSBC.INFO Plugin
Plugin URI: http://pksoft.info
Description: Common SpokaneSBC.INFO plugin functionality by Pavel Kozubenko
Author: Pavel Kozubenko
Version: 1.0
Author URI: http://pkosoft.info
*/

function SSBC_STREAM_CONTEXT_IGNORE_SSL() {
	return stream_context_create(array(
    "ssl"=>array(
        "verify_peer" => false,
        "verify_peer_name"=>false,
    )));
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'pk-api/v1', '/(?P<handler>[\w-]+)', [
    // register_rest_route( 'pk-api/v1', '/utils/(?P<id>\d+)', [
      'methods' => 'GET, POST',
      'callback' => 'pk_api_handler',
    ] );

    register_rest_route( 'pk-api/v1', '/(?P<api>[\w-]+)/(?P<handler>[\w-]+)', [
      'methods' => 'GET, POST',
      'callback' => 'pk_api_handler',
    ] );
} );

function latestNotifications() {
	$posts_raw = get_posts([
		'post_type'   => 'notification',
		'numberposts' => 10,
		'orderby'     => 'date',
		'order'       => 'DESC',
	]);
	
	$posts = [];
	
	// get_field('title', $post->ID);
	foreach($posts_raw as $post) {
		
		$active = get_field('active', $post->ID);
		
		if($active) {
			$posts[] = $post;
		}
	}

	return [ 'status' => "OK", 'data' => $posts ];
}

function pk_api_handler( WP_REST_Request $request ) {
  // You can access parameters via direct array access on the object:
  $param = $request['some_param'];

  // Or via the helper method:
  $param = $request->get_param( 'some_param' );

  // You can get the combined, merged set of parameters:
  $parameters = $request->get_params();

  // The individual sets of parameters are also available, if needed:
  $url_params = (object)$request->get_url_params();

  if($url_params->api == null) {
      $url_params->api = "default";
  }

  if($url_params->handler == null) {
      return [ 'status' => "Api handler for {$url_params->api} not found" ];
  }
  
  switch($url_params->api) {
	  case "notification":
		switch($url_params->handler) {
			case "latest":
				return latestNotifications();
		}
		break;
  }

  /*
  $q_params = $request->get_query_params();
  $body_params = $request->get_body_params();
  $json_params = $request->get_json_params();
  $default_params = $request->get_default_params();

  // Uploads aren't merged in, but can be accessed separately:
  $file_params = $request->get_file_params();
  */

  return [ 'status' => "Api handler for {$url_params->api} not found" ];
}


?>