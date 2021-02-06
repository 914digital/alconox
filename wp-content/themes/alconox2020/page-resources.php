<?php get_template_part('parts/header'); 

/* Template Name: Resources */

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
            <div class="col-md-8 offset-md-2">
                <div class="res-content">
                    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    <?php the_content(); ?>
                        <?php endwhile; else : ?>
                        <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
                    <?php endif; ?>		
                </div>
                <div class="row">
                    <?php if(have_rows('resources')) : ?>
                    <?php $counter = 1; ?>
                        <?php while(have_rows('resources')): the_row(); ?>
                        <?php $counter++; ?>
                        <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="<?php echo $counter; ?>00" data-aos-duration="700">
                            <a href="<?php the_sub_field('resources_link'); ?>" class="icon-box">
                                <?php the_sub_field('resource_icon'); ?>
                                <h2><?php the_sub_field('resources_header'); ?></h2> 
                            </a>
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

<?php get_template_part('parts/footer'); ?>