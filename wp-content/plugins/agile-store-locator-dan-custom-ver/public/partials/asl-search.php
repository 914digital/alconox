<?php

$geo_btn_class      = ($all_configs['geo_button'] == '1')?'asl-geo icon-direction-outline':'icon-search';
$search_type_class  = ($all_configs['search_type'] == '1')?'asl-search-name':'asl-search-address';

$with_categories    = (isset($all_configs['category_control']) && $all_configs['category_control'] == '0')? false: true;
$bg_color           = (isset($all_configs['bg-color']))? 'style="background-color:'.$all_configs['bg-color'].'"': '';
$btn_color          = (isset($all_configs['btn-color']))? 'style="background-color:'.$all_configs['btn-color'].'"': '';

?>
<div id="asl-search" class="asl-p-cont asl-search">
    <section class="asl-search-cont" <?php echo $bg_color ?>>
      <div class="row">
          <div class="col-lg-12">
              <div class="row asl-search-widget text-center center-block">
                  <div class="col-sm-5  p-0">
                      <button class="asl-clear-btn" type="button"><svg width="12" height="12" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg"><path d="M.566 1.698L0 1.13 1.132 0l.565.566L6 4.868 10.302.566 10.868 0 12 1.132l-.566.565L7.132 6l4.302 4.3.566.568L10.868 12l-.565-.566L6 7.132l-4.3 4.302L1.13 12 0 10.868l.566-.565L4.868 6 .566 1.698z"></path></svg></button>
                      <input data-submit="disable"  id="auto-complete-search" class="form-control asl-search-cntrl isp_ignore border-r-0" placeholder="<?php echo __( 'Search Location', 'asl_locator') ?>">
                      <span class="err-spn"><?php echo __( 'Destination Missing or Invalid', 'asl_locator') ?></span>
                  </div>
                  <?php if($with_categories): ?>
                   <div class="col-sm-4 p-0">
                      <div class="categories_filter">
                        <select class="form-control border-0" id="asl-categories"></select>
                      </div>
                  </div>
                  <?php endif; ?>
                  <div class="col-sm-3  p-0">
                      <button id="asl-btn-search" type="button" <?php echo $btn_color ?> class="asl-search-btn border-l-0 btn btn-primary"><?php echo __( 'Search', 'asl_locator') ?></button>
                  </div>
              </div>
          </div>
      </div>
    </section>
</div>