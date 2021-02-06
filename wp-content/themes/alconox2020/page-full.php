<?php get_template_part('parts/header'); 

/* Template Name: Full Page */

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
            <div class="col-md-12">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <?php the_content(); ?>
                    <?php endwhile; else : ?>
                <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
            <?php endif; ?>		
            </div>
        </div>
    </div>
</div>





<?php get_template_part('parts/footer'); ?>