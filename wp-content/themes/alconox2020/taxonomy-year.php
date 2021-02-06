<?php get_template_part('parts/header'); ?>
<div class="page-index-header prod-index-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="page-index-header-text">
                    <h1><?php $term = get_term_by( 'slug', get_query_var('term'), get_query_var('taxonomy') );
echo $term->name; ?></h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="single-page-content mt-5 mb-5">
    <div class="container">
        <div class="row">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <div class="col-md-2 col-4">
                    <a class="btn coa-btn coa-link" target="_blank" href="<?php the_permalink(); ?>"><?php the_title(); ?> <i class="fas fa-download"></i></a> 
                </div>
            <?php endwhile; else : ?>
            <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
            <?php endif; ?>		
        </div>
    </div>
</div>

<?php get_template_part('parts/footer'); ?>