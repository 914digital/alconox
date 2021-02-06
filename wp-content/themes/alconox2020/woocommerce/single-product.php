<?php get_template_part('parts/header'); ?>

<div class="single-page-wrapper header-top">
    <div class="single-page-content">
        <div class="container">
            <div class="row">
				<div class="col-md-1 dt">
					<div class="prod-gallery">
					<?php if( have_rows('product_gallery') ): ?>
					<?php while ( have_rows('product_gallery') ) : the_row(); ?>
						<a href="<?php the_sub_field('product_gallery_image'); ?>" title="<?php the_sub_field('product_gallery_image_description'); ?>"><img src="<?php the_sub_field('product_gallery_image'); ?>"></a>
						<?php endwhile;
							else :
						// no rows found
						endif; ?>
						</div> <!-- Product Gallery -->
					</div>
					<div class="col-md-4">
						<div class="main-prod-img">
							<?php echo the_post_thumbnail(); ?>
						</div>
						<div class="prod-btns dt">
							<div class="row">
							<?php if( have_rows('product_buttons') ): ?>
							<?php while ( have_rows('product_buttons') ) : the_row(); ?>
								<div class="col-md-12 mb-2">
									<a target="_blank" href="<?php the_sub_field('product_button_upload'); ?>" class="btn pdt-btn"><i class="fas fa-file-pdf mr-2"></i> <?php the_sub_field('product_button_text'); ?></a>
								</div>
							<?php endwhile;
								else :
							// no rows found
							endif; ?>
							</div>
						</div> <!-- Product Buttons desktop -->
					</div>
					<div class="col-md-4 mb">
					<div class="prod-gallery">
						<div class="row">
						<?php if( have_rows('product_gallery') ): ?>
					<?php while ( have_rows('product_gallery') ) : the_row(); ?>
						<div class="col-3"><a href="<?php the_sub_field('product_gallery_image'); ?>" title="<?php the_sub_field('product_gallery_image_description'); ?>"><img src="<?php the_sub_field('product_gallery_image'); ?>"></a></div>
						<?php endwhile;
							else :
						// no rows found
						endif; ?>
						</div>
					
						</div> <!-- Product Gallery -->
					</div>
					<div class="col-md-7">	
					<?php while ( have_posts() ) : the_post(); ?>
					<?php wc_get_template_part( 'content', 'single-product' ); ?>
					<?php endwhile; // end of the loop. ?>
					<div class="prod-tabs">
						<ul class="nav nav-tabs" id="prodTab" role="tablist">
							<?php if( have_rows('product_tabs') ): ?>
							<?php $counter = 0; ?>
							<?php while ( have_rows('product_tabs') ) : the_row(); ?>
							<?php $counter++; ?>
								<li class="nav-item">
									<a class="nav-link prod-tab" id="prod-tab-<?php echo $counter; ?>" data-toggle="tab" href="#tab-<?php echo $counter; ?>" role="tab" aria-selected="true"><?php the_sub_field('product_tab_name'); ?></a>
								</li>
								
								<?php endwhile;
								else :
							// no rows found
							endif; ?>
							<?php if(is_single(20801)) { ?>
							<li class="nav-item">
								<a class="nav-link prod-tab" id="prod-tab-meas" data-toggle="tab" href="#tab-meas" role="tab" aria-selected="true">Measurements</a>
							</li>
							<?php } else { ?>
							<?php } ?>
							<li class="nav-item">
								<a class="nav-link prod-tab" id="prod-tab-cat" data-toggle="tab" href="#tab-cat" role="tab" aria-selected="true">Catalog Numbers</a>
							</li>
							
						</ul>
						<div class="tab-content" id="myTabContent">
							<?php if( have_rows('product_tabs') ): ?>
							<?php $concounter = 0; ?>
							<?php while ( have_rows('product_tabs') ) : the_row(); ?>
							<?php $concounter++; ?>
			
								<div class="tab-pane prod-content fade" id="tab-<?php echo $concounter; ?>" ><?php the_sub_field('product_tab_content'); ?></div>
							
							<?php endwhile;
								else :
							// no rows found
							endif; ?>

						<?php if(is_single(20801)) { ?>
						<div class="tab-pane prod-content fade" id="tab-meas">
							<table class="table table-bordered table-striped">
								<thead style="background-color:#1e81c6;color:#fff;">
									<tr>
										<th scope="col">Container Size</th>
										<th scope="col">Gal. of 2% Solution Made</th>
										<th scope="col">Square Feet Cleaned</th>
									</tr>
								</thead>
								<tbody>
								<?php if( have_rows('measurements_table') ): ?>
								<?php while ( have_rows('measurements_table') ) : the_row(); ?>
									<tr>
										<td><?php the_sub_field('container_size'); ?></td>
										<td><?php the_sub_field('gal_of_2_solution_made'); ?></td>
										<td><?php the_sub_field('square_feet_cleaned'); ?></td>
									</tr>
								<?php endwhile;
								else :
							// no rows found
							endif; ?>
							
								</tbody>
							</table>

							</div>

						<?php } ?>

							<div class="tab-pane prod-content fade" id="tab-cat">
							<table class="table table-bordered table-striped">
								<thead style="background-color:#1e81c6;color:#fff;">
									<tr>
										<th scope="col">Size</th>
										<th scope="col">Catalog Number</th>
									</tr>
								</thead>
								<tbody>
								<?php if( have_rows('catalogue_table') ): ?>
								<?php while ( have_rows('catalogue_table') ) : the_row(); ?>
									<tr>
										<td><?php the_sub_field('size'); ?></td>
										<td><?php the_sub_field('catalog_number'); ?></td>
									</tr>
								<?php endwhile;
								else :
							// no rows found
							endif; ?>
							
								</tbody>
							</table>

							</div>
						</div>
					</div> <!-- Product Tabs -->

					<div class="prod-btns mb">
						<div class="row">
						<?php if( have_rows('product_buttons') ): ?>
						<?php while ( have_rows('product_buttons') ) : the_row(); ?>
							<div class="col-md-12 mb-2">
								<a target="_blank" href="<?php the_sub_field('product_button_upload'); ?>" class="btn pdt-btn"><i class="fas fa-file-pdf mr-2"></i> <?php the_sub_field('product_button_text'); ?></a>
							</div>
						<?php endwhile;
							else :
						// no rows found
						endif; ?>
						</div>
						</div> <!-- Product Buttons desktop -->
				
				</div>
			</div>
		</div>
	</div>
</div>

<div class="related">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
			
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

	<?php
		/**
		 * woocommerce_after_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>

<?php get_template_part('parts/footer'); ?>