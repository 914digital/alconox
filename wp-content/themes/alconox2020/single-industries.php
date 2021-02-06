<?php get_template_part('parts/header'); ?>

<?php $backgroundImg = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );?>
<div class="page-single-header" style="background-image: url('<?php echo $backgroundImg[0]; ?>') ">
    <div class="page-single-header-text">
        <h1><?php the_title(); ?></h1>
        <p><?php the_field('page_header_sub_header'); ?></p>
    </div>
    <div class="mask"></div>
</div>

<div class="single-page-wrapper">
    <div class="single-page-content">
        <div class="container">
            <div class="row">
                <div class="col-md-3" data-aos="fade-right" data-aos-delay="300" data-aos-duration="1000">
                    <div class="single-page-sidebar">
                    <h2>Industries</h2>
                    <?php wp_nav_menu( array( 'theme_location' => 'ind-sidebar' ) ); ?>
                    </div>
                </div>
                <div class="col-md-9" data-aos="fade-left" data-aos-delay="300" data-aos-duration="1000">
                    <div class="single-page-content-text">
                        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                            <?php the_content(); ?>
                                <?php endwhile; else : ?>
                            <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
                        <?php endif; ?>
						<?php if( get_field('before_after_image') ): ?>
                        <div class="bef-aft">
						<span class="ba-header">Before & After</span>
                            <div class="row">
                                <div class="col-md-5">
									<a class="ba-img" href="<?php the_field('before_after_image'); ?>">
										<span class="zoom"><i class="fas fa-search-plus"></i></span>
										<img src="<?php the_field('before_after_image'); ?>">
									</a>
								 </div>
								 <div class="col-md-7">
									 <p class="small"><?php the_field('before_after_text'); ?></p>
                                </div>
                            </div>
                        </div>	
						<?php endif; ?>	
                        
                    </div>
                    <div class="related rel-ind">
                    <h2>Related Articles</h2>
				<ul>
					<?php if( have_rows('tech_notes') ): ?>
					<?php while ( have_rows('tech_notes') ) : the_row(); ?>
						<li><a target="_blank" href="<?php the_sub_field('technote_link'); ?>"><?php the_sub_field('technote_header'); ?></a></li>
					<?php endwhile;
						else :
					// no rows found
					endif; ?>
				</ul>

				<h2>Related Videos</h2>
				<div class="row">
				<?php if( have_rows('related_videos') ): ?>
					<?php while ( have_rows('related_videos') ) : the_row(); ?>
					<?php $post_object = get_sub_field('related_video_post');

						if( $post_object ): 

							// override $post
							$post = $post_object;
							setup_postdata( $post ); 
							?>
							<div class="col-md-4 mb-3">
								<a href="<?php the_permalink(); ?>">
									<?php echo the_post_thumbnail(); ?>
									<h3><?php the_title(); ?></h3>
								</a>
							</div>
							<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
						<?php endif; ?>
												
					<?php endwhile;
						else :
					// no rows found
					endif; ?>
				</div>

			<h2>Related Products</h2>
				<div class="row">
				<?php if( have_rows('related_products') ): ?>
					<?php while ( have_rows('related_products') ) : the_row(); ?>
					<?php $post_object = get_sub_field('product');

						if( $post_object ): 

							// override $post
							$post = $post_object;
							setup_postdata( $post ); 
							?>
							<div class="col-md-4 mb-3">
								<a href="<?php the_permalink(); ?>">
									<?php echo the_post_thumbnail(); ?>
									<h3><?php the_title(); ?></h3>
								</a>
							</div>
							<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
						<?php endif; ?>
												
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