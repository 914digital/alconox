<?php 

// Chane dropdown from options to size

add_filter( 'woocommerce_dropdown_variation_attribute_options_args', 'cinchws_filter_dropdown_args', 10 );

function cinchws_filter_dropdown_args( $args ) {
    $args['show_option_none'] = 'Choose your size';
    return $args;
}

//Hide Price Range for WooCommerce Variable Products

add_filter( 'woocommerce_variable_sale_price_html', 
'lw_variable_product_price', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 
'lw_variable_product_price', 10, 2 );

function lw_variable_product_price( $v_price, $v_product ) {

// Product Price
$prod_prices = array( $v_product->get_variation_price( 'min', true ), 
                            $v_product->get_variation_price( 'max', true ) );
$prod_price = $prod_prices[0]!==$prod_prices[1] ? sprintf(__('From: %1$s', 'woocommerce'), 
                       wc_price( $prod_prices[0] ) ) : wc_price( $prod_prices[0] );

// Regular Price
$regular_prices = array( $v_product->get_variation_regular_price( 'min', true ), 
                          $v_product->get_variation_regular_price( 'max', true ) );
sort( $regular_prices );
$regular_price = $regular_prices[0]!==$regular_prices[1] ? sprintf(__('From: %1$s','woocommerce')
                      , wc_price( $regular_prices[0] ) ) : wc_price( $regular_prices[0] );

if ( $prod_price !== $regular_price ) {
$prod_price = '<del>'.$regular_price.$v_product->get_price_suffix() . '</del> <ins>' . 
                       $prod_price . $v_product->get_price_suffix() . '</ins>';
}
return $prod_price;
}

//Hide “From:$X”
add_filter('woocommerce_get_price_html', 'lw_hide_variation_price', 10, 2);
function lw_hide_variation_price( $v_price, $v_product ) {
$v_product_types = array( 'variable');
if ( in_array ( $v_product->get_type(), $v_product_types )  ) {
return '';
}
// return regular price
return $v_price;
}

/**
 * Change number of products that are displayed per page (shop page)
 */
add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );

function new_loop_shop_per_page( $cols ) {
  // $cols contains the current number of products per page based on the value stored on Options –> Reading
  // Return the number of products you wanna show per page.
  $cols = 20;
  return $cols;
}

?>