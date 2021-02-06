<?php get_template_part('parts/header'); 

/* Template Name: COAs */

?>

<div class="page-index-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="page-index-header-text">
                    <h1><?php the_title(); ?></h1>
                    <p><?php the_field('page_header_sub_header'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="single-page-content mt-5 mb-5">
    <div class="container">
        <div class="row">
        <?php // WP_Query arguments
            $args = array (
            'post_type'              => array( 'coas' ),
            'posts_per_page'         => '-1',
            'orderby'                => 'title',
            'order'                  =>'ASC'
            );

            // The Query
            $query = new WP_Query( $args );

            // The Loop
            if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
            $query->the_post(); ?>
            <div class="col-md-2 col-4">
                <a class="btn coa-btn coa-link" target="_blank" href="<?php the_permalink(); ?>"><span style="text-transform:uppercase;"><?php the_title(); ?></span> <i class="fas fa-download"></i></a> 
            </div>
            
            <?php }
            } else {
            // no posts found
            }

            // Restore original Post Data
            wp_reset_postdata(); ?>
        </div>
    </div>
</div>


<?php get_template_part('parts/footer'); ?>