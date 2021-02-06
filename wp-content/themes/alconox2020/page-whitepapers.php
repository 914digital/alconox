<?php get_template_part('parts/header'); 

/* Template Name: White Papers */

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
            <div class="col-md-9">
                  <div class="doc-section-header-wrapper">
                <div class="doc-section-header" id="doc-sec-3">
                   <?php the_field('white_papers_header'); ?>
                </div>
                <div class="doc-section-text clearfix">
                    <img class="wp-image" src="<?php the_field('white_papers_image'); ?>">
                    <?php the_field('white_papers_text'); ?>
                </div>
            </div>
            <div class="wp-form">
                <?php the_field('white_papers_form'); ?>
            </div>
              </div>
              <div class="col-md-3">
                <div class="single-page-sidebar dl-sidebar">
                    <h2>Downloads</h2>
                    <?php wp_nav_menu( array( 'theme_location' => 'dl-sidebar' ) ); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_template_part('parts/footer'); ?> 