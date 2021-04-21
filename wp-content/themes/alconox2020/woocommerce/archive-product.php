<?php get_template_part('parts/header'); ?>

<?php if(is_product_category()) { ?> 
	<div class="page-index-header prod-index-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="page-index-header-text">
					<h1><?php single_term_title(); ?></h1>
					<a class="ret-products" href="/products"><i class="fas fa-arrow-left"></i> Return To All Products</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } else { ?>

<div class="page-index-header prod-index-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="page-index-header-text">
                    <h1>Products</h1>
                    <p>MANY OF OUR CUSTOMERS PURCHASE FROM DEALERS, BUT FOR YOUR CONVENIENCE, YOU CAN SHOP ONLINE WITH US.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } ?>
<div class="single-page-content prod-index-cont mt-5">
    <div class="container">
<?php
if ( woocommerce_product_loop() ) {

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @hooked woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */
	do_action( 'woocommerce_before_shop_loop' );

	woocommerce_product_loop_start();

	if ( wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();

			/**
			 * Hook: woocommerce_shop_loop.
			 */
			do_action( 'woocommerce_shop_loop' );

			wc_get_template_part( 'content', 'product' );
		}
	}

	woocommerce_product_loop_end();

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	do_action( 'woocommerce_after_shop_loop' );
} else {
	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10
	 */
	do_action( 'woocommerce_no_products_found' );
}

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' ); ?>

</div>
</div>

<?php get_template_part('parts/footer'); ?>
