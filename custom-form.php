<?php
/*
Plugin Name: Mailchimp Subscription

Description: Mailchimp Subscription
Version: 1.0
Author: 

*/



 if( !defined('ABSPATH') ) 
 {
    echo 'what are you trying to do?';
    exit;
 }

// plugin activation hook
register_activation_hook(__FILE__, 'mytable_activation_function');

// callback function to create table
function mytable_activation_function()
{
    global $wpdb;

    if ($wpdb->get_var("show tables like '" . owt_create_my_table() . "'") != owt_create_my_table()) {

        $mytable = 'CREATE TABLE `' . owt_create_my_table() . '` (
                            `form_id` int(11) NOT NULL AUTO_INCREMENT,
							`api_key` varchar(150) NOT NULL,
							`list_id` varchar(150) NOT NULL,
                            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            PRIMARY KEY (`form_id`)
                          ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($mytable);
    }
}
// returns table name
function owt_create_my_table()
{
    global $wpdb;
    return $wpdb->prefix . "custom_api";
}
function actions_recent_bids_add_admin_page(){

    add_menu_page(
          'API key',
          'API',
          'manage_options',
          'submit-api',
          'actions_add_form_list',
          'dashicons-rest-api',
           56
    );

    add_submenu_page(
          'submit-api',               // parent slug
          'List of key',                // page title
          'List of key',                // menu title
          'manage_options',                   // capability
          'wc-acutions-list-keys',  // slug
          'action_display_key_lists' // callback
    );


}

add_action('admin_menu','actions_recent_bids_add_admin_page');


function actions_add_form_list() {
    include('file.php');
}

function action_display_key_lists() {
	include('display.php');
}
function html_form_code() {
	echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
	echo '<p>';
	echo 'Name (required) <br/>';
	echo '<input type="text" name="form_name" autocomplete="off" pattern="[a-zA-Z0-9 ]+" required value="' . ( isset( $_POST["form_name"] ) ? esc_attr( $_POST["form_name"] ) : '' ) . '" size="40" />';
	echo '</p>';
	echo '<p>';
	echo 'Email (required) <br/>';
	echo '<input type="email" name="form_email" autocomplete="off" required value="' . ( isset( $_POST["form_email"] ) ? esc_attr( $_POST["form_email"] ) : '' ) . '" size="40" />';
	echo '</p>';
	echo '<p>';
	echo 'Phone (required) <br/>';
	echo '<input type="text" name="form_phone" autocomplete="off" required' . ( isset( $_POST["form_phone"] ) ? esc_attr( $_POST["form_phone"] ) : '' ) . '" size="40" />';
	echo '</p>';
	echo '<p><input type="submit" name="cf-submitted" value="Subscribe"></p>';
	echo '</form>';
}


function deliver_mail() {


	// if the submit button is clicked, send the email
	if ( isset( $_POST['cf-submitted'] ) ) {
	 // let's start with some variables
      $form_name = $_POST['form_name'];// the user name we are going to subscribe
      $form_email = $_POST['form_email'] ;// the user email we are going to subscribe
      $form_phone = $_POST['form_phone'];// the user phone we are going to subscribe
      $regular = 'You are already a subscriber!';

		//lets call the api and list id in the database
        global $wpdb;
        global $table_prefix;
        $table=$table_prefix.'custom_api';
        $sql="select * from $table";
        $result=$wpdb->get_results($sql);

        foreach ($result as $list)
         { 
            $apiKey = $list->api_key; // api key
            $list_id =  $list->list_id; // List / Audience ID 
        }
        $server = explode( '-', $apiKey );

      $url = 'https://' . $server[1] . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/';

      $response = wp_remote_post(
    $url,
    [
        'method'      => 'POST',
        'data_format' => 'body',
        'timeout'     => 45,
        'headers'     => [
            'Authorization' => 'apikey ' . $apiKey,
            'Content-Type'  => 'application/json; charset=utf-8',
        ],
        'body'        => json_encode(
            [
                'email_address' => $form_email,//email
                'status'        => 'subscribed',
                'status_if_new' => 'subscribed',
                'status_if_regular' => $regular,
                'merge_fields'  => [
                    'FNAME' => $form_name,// first name
                   
                    'PHONE' => $form_phone//phone
                ]
            ]
        )
    ]
);
        if( 'OK' === wp_remote_retrieve_response_message( $response ) )
         {
            echo '<div class="alert alert-success">Great you have successfully subscribed</div>';
        }
        else
         {
            echo $regular;
        }


	}
  
}

function cf_shortcode() {
	ob_start();
	deliver_mail();
	html_form_code();

	return ob_get_clean();
}

add_shortcode( 'sitepoint_contact_form', 'cf_shortcode' );

?>