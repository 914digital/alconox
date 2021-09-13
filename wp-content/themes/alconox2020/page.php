<?php get_template_part('parts/header'); ?>
<div class="page-index-header prod-index-header">
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
        <div class="col-md-4 dt">
            <div class="single-page-sidebar dl-sidebar">
                <h2>Downloads</h2>
                <?php wp_nav_menu( array( 'theme_location' => 'dl-sidebar' ) ); ?>
            </div>
        </div>
            <div class="col-md-8">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <?php the_content(); ?>
                    <?php endwhile; else : ?>
                <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
            <?php endif; ?>	
            <?php if(is_page(19859)) { ?>
                <div class="cvs-btns">
                    <div class="accordion" id="accordionCV">
                        <?php if( have_rows('cleaning_validation') ): ?>
                        <?php $cvcounter = 0; ?>
                        <?php while ( have_rows('cleaning_validation') ) : the_row(); ?>
                        <?php $cvcounter++; ?>
                            <div class="card">
                                <a class="card-header" data-toggle="collapse" data-target="#collapse-<?php echo $cvcounter; ?>" aria-expanded="true" aria-controls="collapse-<?php echo $cvcounter; ?>" id="heading-<?php echo $cvcounter; ?>">
                                    <div class="faq-btn"><?php the_sub_field('cleaning_validation_product_name'); ?> <span class="arrowUp"><i class="fas fa-angle-down"></i></span></div>
                                </a>
                                <div id="collapse-<?php echo $cvcounter; ?>" class="collapse col-content" aria-labelledby="heading-<?php echo $cvcounter; ?>" data-parent="#accordionCV">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4"><img src="<?php the_sub_field('cleaning_validation_image'); ?>"></div>
                                            <div class="col-md-6">
                                                <?php the_sub_field('cleaning_validation_product_content'); ?>
                                            </div>
                                        </div>
                                    
                                    </div>
                                </div>
                            </div>
                            <?php endwhile;
                            else :
                            // no rows found
                            endif; ?>
                        </div>
                    </div>
            <?php } ?>

            <?php if(is_page(5850)) { ?>
                <div class="um-btns">
                    <div class="accordion" id="accordionUM">
                        <?php if( have_rows('user_manual_steps') ): ?>
                        <?php $umcounter = 0; ?>
                        <?php while ( have_rows('user_manual_steps') ) : the_row(); ?>
                        <?php $umcounter++; ?>
                            <div class="card">
                                <a class="card-header" data-toggle="collapse" data-target="#collapse-<?php echo $umcounter; ?>" aria-expanded="true" aria-controls="collapse-<?php echo $umcounter; ?>" id="heading-<?php echo $umcounter; ?>">
                                    <div class="um-btn"><span class="um-step"><?php the_sub_field('user_manual_step'); ?></span> <?php the_sub_field('user_manual_title'); ?> <span class="arrowUp"><i class="fas fa-angle-down"></i></span></div>
                                </a>
                                <div id="collapse-<?php echo $umcounter; ?>" class="collapse col-content" aria-labelledby="heading-<?php echo $umcounter; ?>" data-parent="#accordionUM">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php the_sub_field('user_manual_content'); ?>
                                                <div class="um-cards">
                                                    <div class="row">
                                                   
                                                    <?php if( have_rows('user_manual_card') ): ?>
                                                    <?php while ( have_rows('user_manual_card') ) : the_row(); ?>
                                                    <div class="col-md-6">
                                                        <div class="card mb-3">
                                                            <div class="card-header">
                                                                <?php the_sub_field('card_title'); ?>
                                                            </div>
                                                            <div class="card-body">
                                                                <strong>Form:</strong> <?php the_sub_field('form'); ?><br>
                                                                <strong>Dilution(%):</strong> <?php the_sub_field('dilution'); ?><br>
                                                                <strong>Dose:</strong> (a)oz/gal, (b)gram/l,(c)ml/l: <?php the_sub_field('dose'); ?><br>
                                                                <strong>Min Wash Temp.:</strong> <?php the_sub_field('min_wash_temp'); ?><br>
                                                                <strong>Usual Wash Temp.:</strong> <?php the_sub_field('usual_wash_temp'); ?><br>
                                                                <strong>Protective Gloves:</strong> <?php the_sub_field('protective_gloves'); ?><br>
                                                                <strong>Eye Protection:</strong> <?php the_sub_field('eye_protection'); ?>
                                                            </div>
                                                        </div>
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
                            <?php endwhile;
                            else :
                            // no rows found
                            endif; ?>
                        </div>
                    </div>
            <?php } ?>

            <?php if(is_page(6145)) { ?>
            <div class="irts">
                <div class="row">
                    <?php if( have_rows('irts') ): ?>
                        <?php while ( have_rows('irts') ) : the_row(); ?>
                            <div class="col-md-4">
                                <a href="<?php the_sub_field('irt_upload'); ?>" class="irt-btn mb-3"><?php the_sub_field('irt_name'); ?> <i class="fas fa-download"></i></a>
                            </div>
                    <?php endwhile;
                        else :
                    // no rows found
                    endif; ?>
                </div>
            </div>
            <?php } ?>	
            <?php if(is_page(20010)) { ?>
            <div class="irts">
                <div class="row">
                    <?php if( have_rows('trace_analysis_button') ): ?>
                        <?php while ( have_rows('trace_analysis_button') ) : the_row(); ?>
                            <div class="col-md-4">
                                <a href="<?php the_sub_field('trace_analysis_upload'); ?>" class="irt-btn mb-3"><?php the_sub_field('trace_analysis_text'); ?> <i class="fas fa-download"></i></a>
                            </div>
                    <?php endwhile;
                        else :
                    // no rows found
                    endif; ?>
                </div>
            </div>
            <?php } ?>	
            </div>
            <div class="col-md-4 mb">
            <div class="single-page-sidebar dl-sidebar">
                <h2>Downloads</h2>
                <?php wp_nav_menu( array( 'theme_location' => 'dl-sidebar' ) ); ?>
            </div>
        </div>
        </div>
    </div>
</div>
                
<?php get_template_part('parts/footer'); ?>