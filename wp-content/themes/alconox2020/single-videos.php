<?php get_template_part('parts/header'); ?>

<div class="video-wrapper">
    <div class="container">
        <?php if( get_field('vimeo_link') ): ?>
            <div class="vimeo">
                <a target="_blank" style="background-image: url('<?php the_field('vimeo_image'); ?>');" href="<?php the_field('vimeo_link'); ?>"><i class="fas fa-play"></i></a>
            </div>
        <?php endif; ?>
        <?php if( get_field('video_link') ): ?>
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="<?php the_field('video_link'); ?>" allowfullscreen></iframe>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="video-content">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h1><?php the_title(); ?></h1>
                <p><?php the_content(); ?></p>
            </div>
            <div class="col-md-4">
                <div class="vid-btns">
                    <a href="/order-sample/" class="btn spl-btn mb-2"><i class="fas fa-vial"></i> Order Sample</a>
                    <a href="<?php the_field('video_product_link'); ?>" class="btn prch-btn mb-2"><i class="fas fa-shopping-cart"></i> Purchase</a>
                    <a href="/videos" class="btn all-vid-btn"><i class="fas fa-video"></i> All Videos</a>
                </div>
            </div>
        </div>
        <div class="related mt-5">
            <div class="row">
                <div class="col-md-12">
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
                </div>
            </div>
        </div>
    </div>
</div>
  
<?php get_template_part('parts/footer'); ?>