<?php get_template_part('parts/header'); 

/* Template Name: Timeline */

?>

<?php error_reporting (E_ALL ^ E_NOTICE); ?>


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

<div class="timeline-intro-copy-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="timeline-intro-copy">
                    <?php the_field('timeline_intro'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="timeline-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div id="timeline">
                <?php if( have_rows('timeline') ): ?>
                <?php while ( have_rows('timeline') ) : the_row(); ?>

                <div class="row timeline-movement timeline-movement-top">
                    <div class="timeline-badge timeline-future-movement">
                            <p> <?php the_sub_field('decade'); ?></p>
                    </div>
                </div>
                <?php if( have_rows('years') ): ?>
                <?php $tlcounter == 0; ?>
                <?php while ( have_rows('years') ) : the_row(); ?>
                <?php $tlcounter++; ?>
                <?php if ($tlcounter % 2 == 0) { ?>
                <div class="row timeline-movement">
                    <div class="timeline-badge center-left" id="tl-<?php echo $tlcounter; ?>">
                        
                    </div>
                    <div class="col-sm-6  timeline-item">
                        <div class="row">
                            <div class="col-sm-11">
                                <div class="timeline-panel credits" data-aos="slide-right" data-aos-duration="500">
                                    <ul class="timeline-panel-ul">
                                        <div class="lefting-wrap">
                                            <li class="img-wraping"><a class="tl-img" title="<?php the_sub_field('year'); ?>" href="<?php the_sub_field('year_image'); ?>"><span class="zoom"><i class="fas fa-search-plus"></i></span><img src="<?php the_sub_field('year_image'); ?>" class="img-responsive" alt="user-image" /></a></li>
                                        </div>
                                        <div class="righting-wrap">
                                            
                                            <li><span class="causale-year"><?php the_sub_field('year'); ?></span> </li>
                                            <li><span class="causale"><?php the_sub_field('year_text'); ?> </span> </li>
                                            
                                        </div>
                                        <div class="clear"></div>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <?php } else { ?>
                <div class="row timeline-movement">
                    <div class="timeline-badge center-right" id="tl-<?php echo $counter; ?>">
                    
                    </div>
                    <div class="offset-sm-6 col-sm-6  timeline-item" data-aos="slide-left" data-aos-duration="500">
                        <div class="row">
                            <div class="offset-sm-1 col-sm-11">
                                <div class="timeline-panel debits">
                                    <ul class="timeline-panel-ul">
                                        <div class="lefting-wrap">
                                            <li class="img-wraping"><a title="<?php the_sub_field('year'); ?>" class="tl-img" href="<?php the_sub_field('year_image'); ?>"><span class="zoom"><i class="fas fa-search-plus"></i></span><img src="<?php the_sub_field('year_image'); ?>" class="img-responsive" alt="<?php the_sub_field('year'); ?>" /></a></li>
                                        </div>
                                        <div class="righting-wrap">
                                        <li><span class="causale-year"><?php the_sub_field('year'); ?></span> </li>
                                            <li><span class="causale"><?php the_sub_field('year_text'); ?> </span> </li>
                                            
                                        </div>
                                        <div class="clear"></div>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            
            <?php endwhile;
                else :
            // no rows found
            endif; ?>
            <?php endwhile;
                else :
            // no rows found
            endif; ?>


            </div>
        </div>
    </div>
</div>



<?php get_template_part('parts/footer'); ?>