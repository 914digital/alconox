<?php get_template_part('parts/header'); ?>

<div class="page-index-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="page-index-header-text">
                    <h1>COA: <span style="text-transform:uppercase;"><?php the_title(); ?></span></h1>
                    <p><?php the_field('page_header_sub_header'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="single-page-content-coa">
    <div class="container">
        <div class="row">
           <div class="col-md-12">
                 <div class="dl-coa float-right">
                    <a href="<?php the_field('upload_coa'); ?>" download="<?php the_title(); ?>"><i class="fas fa-download"></i> Download COA</a>
                </div>
           </div>
            <div class="embed-responsive embed-responsive-4by3 coa-height">
                <iframe class="embed-responsive-item" src="<?php the_field('upload_coa'); ?>"></iframe>
            </div>
        </div>
    </div>
</div>

<?php get_template_part('parts/footer'); ?>