<?php
/*
Plugin Name: Automatic product plugin
Description: Automatic replenishment of the number of products after closing orders
Version: 1.0
Author: JP
*/

function handle_default_quantity_query_var($query, $query_vars)
{
    if (!empty($query_vars['default_quantity_exists'])) {
        $query['meta_query'][] = array(
            'key' => 'default_quantity_field',
            'compare' => 'EXISTS',
        );

        $query['meta_query'][] = array(
            'key' => 'default_quantity_field',
            'compare' => '!=',
            'value' => '',
        );
    }

    return $query;
}
add_filter('woocommerce_product_data_store_cpt_get_products_query', 'handle_default_quantity_query_var', 10, 2);

add_action('product_refresh_event', 'product_refresh_products');

function product_refresh_products()
{
    // Get downloadable products created in the year 2016.
    $products = wc_get_products(
        array(
            'manage_stock' => true,
            'default_quantity_exists' => true,
        ));
    foreach ($products as $product) {
        // Aktualizace množství produktu
        wc_update_product_stock($product->id, (int) $product->get_meta('default_quantity_field'));
    }
}

function custom_admin_page_content()
{
    $products = wc_get_products(
        array(
            'manage_stock' => true,
            'default_quantity_exists' => true,
        ));

    foreach ($products as $product) {
        // Aktualizace množství produktu
        print_r($product->id);
        echo ' ';

        print_r((int) $product->get_meta('default_quantity_field'));
        echo '</br>';
    }
}


// Přidání akce pro stránku administrace
add_action('admin_menu', 'products_admin_menu');
function products_admin_menu()
{
    add_management_page('Product', 'Product', 'administrator', 'products', 'custom_admin_page_content');
}
?>