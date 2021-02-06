<?php get_template_part('parts/header'); 

/* Template Name: Documents */

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
                <?php if(have_rows('documents')) : ?>
                <?php $counter = 0; ?>
                <?php while(have_rows('documents')): the_row(); ?>
                <?php $counter++; ?>
                <div class="doc-section-header-wrapper">
                    <div class="doc-section-header" id="doc-sec-<?php echo $counter; ?>">
                        <?php the_sub_field('document_section_name'); ?>
                    </div>
                    <div class="doc-section-text">
                        <?php the_sub_field('document_section_text'); ?>
                    </div>
                </div>
                <div class="lang">
                    <ul class="nav nav-tabs" id="labTab" role="tablist">
                        <?php if(have_rows('language_category')) : ?>
                        <?php $langCounter = 100; ?>
                        <?php while(have_rows('language_category')): the_row(); ?>
                        <?php $langCounter++; ?>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link doc-tab" id="lang-tab-<?php echo $ounter; ?>-<?php echo $langCounter; ?>" data-toggle="tab" href="#lang-tab-<?php echo $counter; ?>-<?php echo $langCounter; ?>" role="tab"><?php the_sub_field('language'); ?></a>
                            </li>
                            <?php endwhile;
                        else :
                        // no rows found
                        endif; ?>
                    </ul>
                </div>
              
                <div class="tab-content" id="langTabContent">
                <?php if(have_rows('language_category')) : ?>
                        <?php $langCounter = 100; ?>
                        <?php while(have_rows('language_category')): the_row(); ?>
                        <?php $langCounter++; ?>
               
                    <div class="tab-pane doc-content fade" id="lang-tab-<?php echo $counter; ?>-<?php echo $langCounter; ?>" role="tabpanel">
                        <div class="row">
                        <?php if(have_rows('add_document')) : ?>
                        <?php while(have_rows('add_document')): the_row(); ?>
                            <div class="col-md-4 col-6 mb-3">
                                <a target="_blank" class="btn dl-btn" href="<?php the_sub_field('document_upload'); ?>">
                                    
                                    <?php the_sub_field('document_name'); ?> <i class="fas fa-download"></i>   
                                </a>
                            </div>
                            <?php endwhile;
                            else :
                            // no rows found
                            endif; ?>
                        </div>
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
   
<?php get_template_part('parts/footer'); ?> 