<?php get_template_part('parts/header'); 

/* Template Name: Industries */

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

<div class="industries">
    <div class="container">
        <div class="row">
            <?php // WP_Query arguments
                $args = array (
                'post_type'              => array( 'industries' ),
                'posts_per_page'         => '-1',
                'order'                  => 'ASC'
                );

                // The Query
                $query = new WP_Query( $args );

                // The Loop
                if ( $query->have_posts() ) { ?>
                <?php $counter = 1; ?>
                <?php while ( $query->have_posts() ) { ?>
                <?php $counter++; ?>
                <?php $query->the_post(); ?>
                <div class="col-md-4 mb-4">
                    <div class="ind-box">
                        <?php $backgroundImg = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );?>
                        <a class="ind-thumb" href="<?php the_permalink(); ?>" style="background-image: url('<?php echo $backgroundImg[0]; ?>') "></a>
                    </div>
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
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
