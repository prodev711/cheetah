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

function custom_plugin_rewrite_rules() {
    add_rewrite_rule(
        '^cheetah/?$',
        'wp-content/plugins/cheetah/cryptohome/step1.php',
        'top'
    );
}

add_action('init', 'custom_plugin_rewrite_rules');

function custom_plugin_query_vars($query_vars) {
    $query_vars[] = 'cheetah';
    return $query_vars;
}
add_filter('query_vars', 'custom_plugin_query_vars');

function custom_plugin_template_include($template) {
    if (get_query_var('cheetah')) {
        $template = plugin_dir_path(__FILE__) . 'cryptohome/sep1.php';
    }
    return $template;
}
add_filter('template_include', 'custom_plugin_template_include');

function add_bootstrap() {
    wp_enqueue_style( 'bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css' );
    wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js', array( 'jquery' ), '', true );
}
add_action( 'admin_enqueue_scripts', 'add_bootstrap' );