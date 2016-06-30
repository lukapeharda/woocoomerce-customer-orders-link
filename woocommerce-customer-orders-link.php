<?php

/*
Plugin Name: WooCommerce Customer Orders Link
Plugin URI: http://lukapeharda.com
Description: Show link to view all customer orders on WordPress users screen.
Author: luka.peharda@gmail.com
Version: 1.0
Author URI: http://lukapeharda.com
Text Domain: e6n-wcol
Domain Path: /languages/
*/

/**
 * Register "Orders" column in users table.
 * @param  array $columns
 * @return array
 */
function e6n_wcol_register_column($columns)
{
    $columns['orders'] = __('Orders', 'e6n-wcol');

    return $columns;
}
add_filter('manage_users_columns', 'e6n_wcol_register_column');

/**
 * Display link to view all customer orders and order number (if customer has any order).
 * @param  string $empty
 * @param  string $columnName
 * @param  integer $userId
 * @return string
 */
function e6n_wcol_display_column($empty, $columnName, $userId)
{
    if ('orders' !== $columnName) {
        return $empty;
    }

    $customerOrdersCount = e6n_wcol_customer_orders_count($userId);

    if ($customerOrdersCount > 0) {
        return sprintf(__('<a href="%s">View all orders (%d) &rarr;</a>', 'e6n-wcol'), add_query_arg(array('post_status' => 'all', 'post_type' => 'shop_order', '_customer_user' => $userId), admin_url('edit.php')), $customerOrdersCount);
    }

    return $empty;
}
add_filter('manage_users_custom_column', 'e6n_wcol_display_column', 10, 3);

/**
 * Return number of order for given customer.
 * @param  integer $userId
 * @return integer
 */
function e6n_wcol_customer_orders_count($userId)
{
    $orders = get_posts(array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => $userId,
        'post_type'   => wc_get_order_types(),
        'post_status' => array_keys(wc_get_order_statuses()),
    ));

    return count($orders);
}

/**
 * Register translation domain.
 * @return void
 */
function e6n_wcol_textdomain()
{
    load_plugin_textdomain('e6n-wcol', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'e6n_wcol_textdomain');