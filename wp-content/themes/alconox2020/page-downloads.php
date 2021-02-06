<?php get_template_part('parts/header'); 

/* Template Name: Downloads */

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
                <h2>Product Images</h2>
                <div class="row">
                    <div class="col-md-8">
                        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                        <?php the_content(); ?>
                            <?php endwhile; else : ?>
                        <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
                        <?php endif; ?>		
                    </div>
                </div>
                <hr>
                <div class="row">
                    <?php if( have_rows('product_images') ): ?>
                        <?php while ( have_rows('product_images') ) : the_row(); ?>
                            <div class="col-md-3 mb-5">
                                <a download="<?php the_sub_field('product_image_name'); ?>" href="<?php the_sub_field('product_image'); ?>"><img class="dl-img" src="<?php the_sub_field('product_image'); ?>"></a>
                                <a class="d-block text-center mt-2" download="<?php the_sub_field('product_image_name'); ?>" href="<?php the_sub_field('product_image'); ?>"><?php the_sub_field('product_image_name'); ?> <i class="fas fa-arrow-circle-down"></i></a>
                            </div>
                    <?php endwhile;
                        else :
                    // no rows found
                    endif; ?>
                </div>
                    <h3>Logos</h3>
                    <hr>
                    <div class="row">
                        <?php if( have_rows('logos') ): ?>
                            <?php while ( have_rows('logos') ) : the_row(); ?>
                                <div class="col-md-6 mb-5">
                                    <a download="<?php the_sub_field('logo_image'); ?>" href="<?php the_sub_field('logo_image'); ?>"><img class="dl-img-logo" src="<?php the_sub_field('logo_image'); ?>"></a>
                                    <a class="d-block text-center mt-2" download="<?php the_sub_field('logo_image_name'); ?>" href="<?php the_sub_field('logo_image'); ?>"><?php the_sub_field('logo_image_name'); ?> <i class="fas fa-arrow-circle-down"></i></a>
                                </div>
                        <?php endwhile;
                            else :
                        // no rows found
                        endif; ?>
                    <div class="logo-content">
                        <?php the_field('logo_content'); ?>
                    </div>
                </div>
              </div>
             
        </div>
    </div>
</div>

<?php get_template_part('parts/footer'); ?>