<?php
/*
Plugin Name:  Cheetah
Plugin URI:   
Description:  A custom payment gateway that allows your customers to pay with cryptocurrency like bitcoin or etherium
Version:      
Author:       
Author URI:   
License:      
License URI:  
Text Domain:  
Domain Path: 
*/

function woocommerce_pay_gateways_filter($methods) {
        $methods[] = 'WC_Custom_Cheetah';
        return $methods;
}

function init_cheetah() {
    if ( in_array('woocommerce/woocommerce.php',apply_filters('active_plugins',get_option('active_plugins')))){
        require_once 'class-wc-custom-cheetah.php';
        add_filter('woocommerce_payment_gateways','woocommerce_pay_gateways_filter');
    }
}

add_action('plugins_loaded', 'init_cheetah');

function custom_plugin_rewrite_rules1() {
    add_rewrite_rule(
        '^cheetah/?$',
        'wp-content/plugins/cheetah/cryptohome/step1.php',
        'top'
    );
}

function custom_plugin_rewrite_rules2() {
    add_rewrite_rule(
        '^payment/?$',
        'wp-content/plugins/cheetah/cryptohome/step3.php',
        'top'
    );
}

add_action('init', 'custom_plugin_rewrite_rules1');
add_action('init', 'custom_plugin_rewrite_rules2');

function custom_plugin_query_vars1($query_vars) {
    $query_vars[] = 'cheetah';
    return $query_vars;
}
function custom_plugin_query_vars2($query_vars){
    $query_vars[] = 'payment';
    return $query_vars;
}
add_filter('query_vars', 'custom_plugin_query_vars1');
add_filter('query_vars', 'custom_plugin_query_vars2');

function custom_plugin_template_include1($template) {
    if (get_query_var('cheetah')) {
        $template = plugin_dir_path(__FILE__) . 'cryptohome/step1.php';
    }
    return $template;
}

function custom_plugin_template_include2($template){
    if ( get_query_var('payment')) {
        $template = plugin_dir_path(__FILE__). 'cryptohome/step3.php';
    }
    return $template;
}
add_filter('template_include', 'custom_plugin_template_include1');
add_filter('template_include', 'custom_plugin_template_include2');

function add_bootstrap() {
    wp_enqueue_style( 'bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css' );
    wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js', array( 'jquery' ), '', true );
}
add_action( 'admin_enqueue_scripts', 'add_bootstrap' );


function get_basket_total_amount( $basket_id ) {
    // Get the order object for the given basket_id
    $order = wc_get_order( $basket_id );
    
    // Check if the order exists and is not empty
    if ( $order && $order->get_item_count() > 0 ) {
        // Get the total amount for the order
        $total_amount = $order->get_total();
        
        // Return the total amount
        return $total_amount;
    }
    
    // If the order does not exist or is empty, return 0
    return 0;
}



function user_api_endpoint($request) {
    $user_id = $request['user_id'];
    $apiKey = $request['api_key'];
    if ( ! $apiKey ){
        echo json_encode(['error' => 'API key is missing']);
        exit;
    }
    if ( $apiKey != get_option('custom_cheetah_api_key') ){
        echo json_encode(["error" => "API key is invalid"]);
        exit;
    }
    $user = get_user_by( 'ID', $user_id );
    $data = $user->data;
    $email = $data->user_email;
    header( 'Content-Type: application/json' );
    echo json_encode( ['email' => $email] );
}

function basket_api_endpoint($request) {
    $orderId = $request['order_id'];
    $apiKey = $request['api_key'];
    if ( ! $apiKey ){
        echo json_encode(['error' => 'API key is missing']);
        exit;
    }
    if ( $apiKey != get_option('custom_cheetah_api_key') ){
        echo json_encode(["error" => "API key is invalid"]);
        exit;
    }
    $order = json_decode(wc_get_order($orderId));
    $amount = $order->total;
    header ('Content-Type: application/json');
    echo json_encode( ['total' => $amount]);
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'cheetah/v1', '/user', array(
        'methods' => 'GET',
        'callback' => 'user_api_endpoint',
    ) );
    register_rest_route( 'cheetah/v1','/orderPrice',array(
        'methods' => 'GET',
        'callback' => 'basket_api_endpoint'
    ));
} );