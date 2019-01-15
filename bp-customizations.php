<?php
// Darkmatter Development custom modifications

// Allow buddypress video upload
add_filter('bp_get_the_profile_field_value','bp_enable_yt_in_profile',10,3);
function bp_enable_yt_in_profile($val,$type,$key){
  // defining field id's
  $my_keyfirst = 457; 
  $my_keysecond = 461;
  $my_keythird = 466;
  $my_keyforth = 471;  
  $my_keyfifth = 476;  
  $my_keysixth = 481; 
  $my_keyseventh = 486; 
  $my_keyeighth = 491;
  $my_keyninth = 496;
  $my_keytenth = 501;
  $my_keyeleventh = 506;  
  $my_keytwelvth = 511;  
  if($key==$my_keyfirst) {
    return wp_oembed_get($val);
  } elseif ($key==$my_keysecond) {
    return wp_oembed_get($val);
  } elseif ($key==$my_keythird) {
    return wp_oembed_get($val);
  } elseif ($key==$my_keyforth) {
    return wp_oembed_get($val);
  } elseif ($key==$my_keyfifth) {
    return wp_oembed_get($val);
  } elseif ($key==$my_keysixth) {
    return wp_oembed_get($val);
  } elseif ($key==$my_keyseventh) {
    return wp_oembed_get($val);
  } elseif ($key==$my_keyeighth) {
    return wp_oembed_get($val);
  } elseif ($key==$my_keyninth) {
    return wp_oembed_get($val);
  } elseif ($key==$my_keytenth) {
    return wp_oembed_get($val);
  } elseif ($key==$my_keyeleventh) {
    return wp_oembed_get($val);
  } elseif ($key==$my_keytwelvth) {
    return wp_oembed_get($val);
  }  
  return $val;
}


// Hide empty searchable fields (checkboxes)
add_action ('bps_field_before_search_form', 'hide_empty_fields');
function hide_empty_fields ($f)
{
	global $wpdb;

	$ids = array (76, 86, 102, 119, 129, 126, 330, 140, 136, 134, 132, 637);		// field IDs of your checkboxes
	if (isset ($f->id) && in_array ($f->id, $ids))
	{
		$query = "SELECT DISTINCT value FROM {$wpdb->prefix}bp_xprofile_data
WHERE field_id = {$f->id}";
		$values = $wpdb->get_col ($query);
		$results = array ();
		foreach ($values as $value)
		{
			if ($value == 'a:0:{}')  continue;

			$uvalues = unserialize ($value);
			$results = array_merge ($results, $uvalues);
		}
		foreach ($results as $k => $result)
			$results[$k] = str_replace ('&amp;', '&', stripslashes ($result));

		foreach ($f->options as $k => $option)
			if (!in_array ($option, $results))  unset ($f->options[$k]);

		if (count ($f->options) == 0)  $f->display = 'hidden';
	}
}


// arrange member directories/serach results alphabetically
add_filter ('bps_match_all', '__return_false');

function my_bp_loop_querystring( $query_string, $object ) {
  if ( ! empty( $query_string ) ) {
    $query_string .= '&';
  }
  $query_string .= 'type=alphabetical';
  return $query_string;
}
add_action( 'bp_ajax_querystring', 'my_bp_loop_querystring', 20, 2 );

// Friendship denied custom email
function denial_email_creation() {
 
    // Do not create if it already exists and is not in the trash
    $post_exists = post_exists( '{{denier.name}} denied your Friendship request.' );
 
    if ( $post_exists != 0 && get_post_status( $post_exists ) == 'publish' )
       return;
  
    // Create post object
    $my_post = array(
      'post_title'    => __( '{{denier.name}} denied your friendship request.', 'buddypress' ),
      'post_content'  => __( '<p>At this time, {{denier.name}} is unable to accept your friendship request. Rest of email content...</p>', 'buddypress' ),  // HTML email content.
      'post_excerpt'  => __( '<p>At this time, {{denier.name}} is unable to accept your friendship request. Rest of email content...</p>', 'buddypress' ),  // Plain text email content.
      'post_status'   => 'publish',
      'post_type' => bp_get_email_post_type() // this is the post type for emails
    );
 
    // Insert the email post into the database
    $post_id = wp_insert_post( $my_post );
 
    if ( $post_id ) {
    // add our email to the taxonomy term 'match_denied'
        // Email is a custom post type, therefore use wp_set_object_terms
 
        $tt_ids = wp_set_object_terms( $post_id, 'friendship_denied', bp_get_email_tax_type() );
        foreach ( $tt_ids as $tt_id ) {
            $term = get_term_by( 'term_taxonomy_id', (int) $tt_id, bp_get_email_tax_type() );
            wp_update_term( (int) $term->term_id, bp_get_email_tax_type(), array(
                'description' => 'Recipients Friendship Denied',
            ) );
        }
    }
 
}
add_action( 'bp_core_install_emails', 'denial_email_creation' );

add_action( 'friends_friendship_rejected', function( $friendship_id, $friendship  ) {
  $user_id = $friendship->initiator_user_id;
  $denier_id = $friendship->friend_user_id;
  $args = array(
    'tokens' => array(
      'site.name' => get_bloginfo('name'),
      'denier.name' => bp_core_get_user_displayname( $denier_id ),
    ),
  );
  bp_send_email( 'friendship_denied', $user_id, $args );    // Send your email here.
}, 10, 2 );


?>
