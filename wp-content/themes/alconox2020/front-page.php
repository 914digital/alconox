<?php get_template_part('parts/header'); ?>
<div class="hp-slider">
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <?php if(have_rows('slider')) : ?>
        <?php $counter = -1; ?>
        <?php while(have_rows('slider')) : the_row(); ?>
        <?php $counter++; ?>
        <li data-target="#carouselExampleIndicators" data-slide-to="<?php echo $counter; ?>"></li>
        <?php endwhile; ?>
        <?php endif; ?>
    </ol>
    <div class="carousel-inner">
    <?php if(have_rows('slider')) : ?>
        <?php while(have_rows('slider')) : the_row(); ?>
        <div class="carousel-item">
            <div class="carousel-image" style="background-image:url('<?php the_sub_field('slider_image'); ?>');"></div>
            <div class="mask"></div>
            <div class="carousel-text">
                <h1><?php the_sub_field('slider_header'); ?></h1>
                <?php if(get_sub_field('slider_sub_header')) : ?>
                <p><?php the_sub_field('slider_sub_header'); ?></p>
                <?php endif; ?>
                <a href="<?php the_sub_field('slider_link'); ?>" class="btn pls-btn mr-2"><i class="fas fa-plus"></i> Learn More</a>
                <a href="<?php the_sub_field('slider_video_link'); ?>" class="btn pls-btn"><i class="fas fa-play"></i> Watch Video</a>
            </div>
        </div>
        <?php endwhile; ?>
        <?php endif; ?>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
    </div>
</div> <!-- /slider -->

<div class="hp-icons">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1000">
                <a href="/order-sample/" class="icon-box" >
                    <i class="fas fa-vial"></i>
                    <h2>Get Samples</h2>
                </a>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1000">
                <a href="/ask-alconox" class="icon-box" >
                    <i class="fas fa-comments"></i>
                    <h2>Ask Alconox</h2>
                </a>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="500" data-aos-duration="1000">
                <a href="/dealers" class="icon-box" >
                    <i class="fas fa-globe"></i>
                    <h2>Locate Dealers</h2>
                </a>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="700" data-aos-duration="1000">
                <a href="/industries" class="icon-box" >
                    <i class="fas fa-industry"></i>
                    <h2>Industries</h2>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- /hp-icons -->

<div class="feat-video">
    <div class="row m-0">
        <div class="col-md-6 p-0" data-aos="fade-in" data-aos-delay="100" data-aos-duration="1000">
            <a href="#0" class="feat-vid-img" style="background-image:url('<?php the_field('home_video_image'); ?>');" data-toggle="modal" data-target="#featVidModal">
                <span class="pl-btn"><i class="fas fa-play"></i></span>
            </a>
        </div>
        <div class="col-md-6 p-0" data-aos="fade-in" data-aos-delay="100" data-aos-duration="1000">
            <div class="vid-text">
                <div class="feat-vid-header">
                    Featured Video
                </div>
                <div class="feat-vid-title">
                    <?php the_field('home_video_header'); ?>
                </div>
                <div class="feat-vid-text">
                    <?php the_field('home_video_text'); ?>
                </div>
                <div class="feat-vid-btns">
                    <a href="#0" data-toggle="modal" data-target="#featVidModal" class="btn wtc-vid-btn"><i class="fas fa-play"></i> Watch Video</a> <a href="/videos" class="btn all-vid-btn"><i class="fas fa-video"></i> All Videos </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="featVidModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="embed-responsive embed-responsive-16by9">
            <iframe class="embed-responsive-item" src="<?php the_field('home_video_link'); ?>" allowfullscreen></iframe>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="rss">
    <div class="row m-0">
        <div class="col-md-6 p-0" data-aos="fade-in" data-aos-delay="100" data-aos-duration="1000">
            <div class="rss-padding dt">
                <?php dynamic_sidebar('RSS'); ?> 
                <a href="https://technotes.alconox.com/" class="tn-btn btn">Read More</a>
            </div>
            <img class="rss_img mb" src="<?php the_field('rss_image'); ?>">
        </div>
        <div class="col-md-6 p-0" data-aos="fade-in" data-aos-delay="100" data-aos-duration="1000">
            <img class="rss_img dt" src="<?php the_field('rss_image'); ?>">
            <div class="rss-padding mb">
                <?php dynamic_sidebar('RSS'); ?> 
                <a href="https://technotes.alconox.com/" class="tn-btn btn">Read More</a>
            </div>
        </div>
    </div>
</div>

<div class="about">
    <div class="container">
        <div class="row">
            <div class="col-md-4" data-aos="fade-right" data-aos-delay="100" data-aos-duration="1000">
                <img class="d-block m-auto" src="<?php the_field('home_page_about_image'); ?>">
            </div>
            <div class="col-md-8" data-aos="fade-left" data-aos-delay="100" data-aos-duration="1000">
                <h3><?php the_field('home_page_about_text'); ?></h3>
            </div>

        </div>
    </div>
</div>
	
<?php get_template_part('parts/footer'); ?>