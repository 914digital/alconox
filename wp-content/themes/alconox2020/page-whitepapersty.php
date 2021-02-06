<?php get_template_part('parts/header'); 

/* Template Name: WP Thank You */

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
            <div class="col-md-3">
                <div class="single-page-sidebar dl-sidebar">
                    <h2>Downloads</h2>
                    <?php wp_nav_menu( array( 'theme_location' => 'dl-sidebar' ) ); ?>
                </div>
            </div>
            <div class="col-md-9">
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    <?php the_content(); ?>
                        <?php endwhile; else : ?>
                        <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
                    <?php endif; ?>		

                <div class="wp-downloads">
                    <?php if( have_rows('white_paper_downloads') ): ?>
                    <?php while ( have_rows('white_paper_downloads') ) : the_row(); ?>

                        <h2><?php the_sub_field('white_paper_category_name'); ?></h2>
                    
                    <div class="row">
                        <?php if( have_rows('upload_white_paper') ): ?>
                        <?php while ( have_rows('upload_white_paper') ) : the_row(); ?>
                        <div class="col-md-4 col-6 mb-3">
                            <a class="btn dl-btn" href="<?php the_sub_field('white_paper_link'); ?>">
                                <?php the_sub_field('white_paper_name'); ?> <i class="fas fa-download"></i>
                            </a>
                        </div>
                        
                        <?php endwhile;
                        else :
                        // no rows found
                        endif; ?>
                    </div>

                    <?php endwhile;
                        else :
                    // no rows found
                    endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_template_part('parts/footer'); ?> 