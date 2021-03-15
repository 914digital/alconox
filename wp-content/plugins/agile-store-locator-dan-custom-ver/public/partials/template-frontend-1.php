<?php

//$all_configs['infobox_layout'] = '0';

$class = '';


if($all_configs['advance_filter'] == '0')
  $class .= ' no-asl-filters';

if($all_configs['display_list'] == '0' || $all_configs['first_load'] == '3'  || $all_configs['first_load'] == '4')
  $class = ' map-full';

if($all_configs['full_width'])
  $class .= ' full-width';


if($all_configs['advance_filter'] == '1' && $all_configs['layout'] == '1')
  $class .= ' asl-adv-lay1';

$default_addr = (isset($all_configs['default-addr']))?$all_configs['default-addr']: '';


//add Full height
$class .= ' '.$all_configs['full_height'];

$geo_btn_class      = ($all_configs['geo_button'] == '1')?'asl-geo icon-direction-outline':'icon-search';
$search_type_class  = ($all_configs['search_type'] == '1')?'asl-search-name':'asl-search-address';
$panel_order        = (isset($all_configs['map_top']))?$all_configs['map_top']: '2';

$ddl_class_grid = ($all_configs['search_2'])? 'col-md-12 col-lg-6': 'col-md-12 col-lg-6';
$tsw_class_grid = ($all_configs['search_2'])? 'col-xs-4 col-md-2': 'col-xs-4 col-md-2';
$adv_class_grid = ($all_configs['search_2'])? 'col-sm-12': 'col-sm-12';

?>
<link rel='stylesheet' id='asl-plugin-css'  href='<?php echo ASL_URL_PATH ?>public/css/blue/asl-1-<?php echo $all_configs['color_scheme_1'] ?>.css?v=<?php echo ASL_CVERSION; ?>' type='text/css' media='all' />
<style type="text/css">
#asl-storelocator.asl-p-cont .Status_filter .onoffswitch-inner::before{content: "<?php echo __('OPEN', 'asl_locator') ?>" !important}
#asl-storelocator.asl-p-cont .Status_filter .onoffswitch-inner::after{content: "<?php echo __('ALL', 'asl_locator') ?>" !important}
#asl-storelocator.container.storelocator-main.asl-p-cont .asl-loc-sec .asl-panel {order: <?php echo $panel_order ?>;}
</style>
<div id="asl-storelocator" class="container asl-template-1 storelocator-main asl-p-cont asl-layout-<?php echo $all_configs['layout'] ?> asl-bg-0 asl-text-<?php echo $all_configs['font_color_scheme'].$class ?>">
<?php if($all_configs['advance_filter']): ?>    
  <div class="row Filter_section">
      <div class="col-xs-12 col-sm-4">
        <div class="<?php if($all_configs['search_2']) echo 'no-pad'; ?>">
          <div class="row">
            <div class="<?php echo $adv_class_grid ?> asl-advance-filters hide">
                <div class="row">
                  <div class="<?php echo $ddl_class_grid ?> drop_box_filter">
                      <div class="asl-filter-cntrl">
                        <label class="asl-cntrl-lbl"><?php echo __( 'Countries','asl_locator') ?></label>
                        <div class="categories_filter" id="countries_filter">
                        </div>
                      </div>
                  </div>

                  <div class="<?php echo $ddl_class_grid ?> asl-states-filter drop_box_filter">
                      <div class="asl-filter-cntrl">
                        <label class="asl-cntrl-lbl"><?php echo __( 'States','asl_locator') ?></label>
                        <div class="categories_filter" id="states_filter">
                        </div>
                      </div>
                  </div>
                  <div class="<?php echo $ddl_class_grid ?> range_filter hide">
                      <div class="rangeFilter asl-filter-cntrl">
                        <label class="asl-cntrl-lbl"><?php echo __( 'Distance Range','asl_locator') ?></label>
                        <input id="asl-radius-slide" type="text" class="span2" />
                        <span class="rad-unit"><?php echo __( 'Radius','asl_locator') ?>: <span id="asl-radius-input"></span> <span id="asl-dist-unit"><?php echo __( 'KM','asl_locator') ?></span></span>
                      </div>
                  </div>
                  <div class="<?php echo $tsw_class_grid ?> Status_filter">
                    <div class="asl-filter-cntrl">
                      <label class="asl-cntrl-lbl"><?php echo __('Status', 'asl_locator') ?></label>
                      <div class="onoffswitch">
                        <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="asl-open-close" checked>
                        <label class="onoffswitch-label" for="asl-open-close">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-4 search_filter">
          <p><?php echo __( 'Search Location', 'asl_locator') ?></p>
          <p>
            <input type="text" value="<?php echo $default_addr ?>" data-submit="disable" data-provide="typeahead" tabindex="2" id="auto-complete-search" placeholder="<?php echo __( 'Enter a Location', 'asl_locator') ?>"  class="<?php echo $search_type_class ?> form-control typeahead isp_ignore">
            <span><i class="<?php echo $geo_btn_class ?>" title="<?php echo ($all_configs['geo_button'] == '1')?__('Current Location','asl_locator'):__('Search Location','asl_locator') ?>"></i></span>
          </p>
      </div>
      <div class="col-xs-12 col-sm-4">
        <div class="row asl-advance-filters hide" style="padding-left: 0px;">
          <div class="col-md-12 col-lg-6 search_filter asl-name-search">
            <p><?php echo __( 'Search Name', 'asl_locator') ?></p>
            <p><input type="text" tabindex="2"  placeholder="<?php echo __( 'Search Name', 'asl_locator') ?>"  class="asl-search-name form-control isp_ignore"></p>
          </div>
          <div class="col-md-12 col-lg-6 drop_box_filter">
              <div class="asl-filter-cntrl">
                <label class="asl-cntrl-lbl"><?php echo $all_configs['category_title']  ?></label>
                <div class="categories_filter" id="categories_filter">
                </div>
              </div>
          </div>
        </div>
      </div>
  </div>
  <?php endif; ?>
<div class="row asl-loc-sec">
  <div class="col-sm-8 col-xs-12 asl-map">
    <div class="store-locator">
      <div id="asl-map-canv"></div>
      <!-- agile-modal -->
      <div id="agile-modal-direction" class="agile-modal fade">
        <div class="agile-modal-backdrop-in"></div>
        <div class="agile-modal-dialog in">
          <div class="agile-modal-content">
            <div class="agile-modal-header">
              <button type="button" class="close-directions close" data-dismiss="agile-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4><?php echo __('Get Your Directions', 'asl_locator') ?></h4>
            </div>
            <div class="form-group">
              <label for="frm-lbl"><?php echo __('From', 'asl_locator') ?>:</label>
              <input type="text" class="form-control frm-place" id="frm-lbl" placeholder="<?php echo __('Enter a Location', 'asl_locator') ?>">
            </div>
            <div class="form-group">
              <label for="frm-lbl"><?php echo __('To', 'asl_locator') ?>:</label>
              <input readonly="true" type="text"  class="directions-to form-control" id="to-lbl" placeholder="<?php echo __('Prepopulated Destination Address', 'asl_locator') ?>">
            </div>
            <div class="form-group">
              <span for="frm-lbl"><?php echo __('Show Distance In', 'asl_locator') ?></span>
              <label class="checkbox-inline">
                <input type="radio" name="dist-type" id="rbtn-km" value="0"> <?php echo __('KM', 'asl_locator') ?>
              </label>
              <label class="checkbox-inline">
                <input type="radio" name="dist-type" checked id="rbtn-mile" value="1"> <?php echo __('Mile', 'asl_locator') ?>
              </label>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-default btn-submit"><?php echo __('GET DIRECTIONS', 'asl_locator') ?></button>
            </div>
          </div>
        </div>
      </div>

      <div id="asl-geolocation-agile-modal" class="agile-modal fade">
        <div class="agile-modal-backdrop-in"></div>
        <div class="agile-modal-dialog in">
          <div class="agile-modal-content">
            <button type="button" class="close-directions close" data-dismiss="agile-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php if($all_configs['prompt_location'] == '2'): ?>
              <div class="form-group">
                <h4><?php echo __('LOCATE YOUR GEOPOSITION', 'asl_locator') ?></h4>
              </div>
              <div class="form-group">
                <div class="col-md-9">
                  <input type="text" class="form-control" id="asl-current-loc" placeholder="<?php echo __('Your Address', 'asl_locator') ?>">
                </div>
                <div class="col-md-3">
                  <button type="button" id="asl-btn-locate" class="btn btn-block btn-default"><?php echo __('LOCATE', 'asl_locator') ?></button>
                </div>
              </div>
              <?php else: ?>
              <div class="form-group">
                <h4><?php echo __('Use my location to find the closest Service Provider near me', 'asl_locator') ?></h4>
              </div>
              <div class="form-group text-center">
                <button type="button" id="asl-btn-geolocation" class="btn btn-block btn-default"><?php echo __('USE LOCATION', 'asl_locator') ?></button>
              </div>
              <?php endif; ?>
          </div>
        </div>
      </div>
      <!-- agile-modal end -->
    </div>
  </div>
  <div class="col-sm-4 col-xs-12 asl-panel">
    <?php if(!$all_configs['advance_filter']): ?>    
    <div class="col-xs-12 inside search_filter">
      <div class="asl-store-search">
          <input type="text" value="<?php echo $default_addr ?>" id="auto-complete-search" class="form-control <?php echo $search_type_class ?>" placeholder="">
          <span><i class="glyphicon <?php echo $geo_btn_class ?>" title="<?php echo ($all_configs['geo_button'] == '1')?__('Current Location','asl_locator'):__('Search Location','asl_locator') ?>"></i></span>
      </div>
    </div>
    <?php else: ?>
    
    <?php endif; ?>
    <!--  Panel Listing -->
    <div id="asl-list" class="storelocator-panel">
      <div class="asl-overlay" id="map-loading">
        <div class="white"></div>
        <div class="loading"><img style="margin-right: 10px;" class="loader" src="<?php echo ASL_URL_PATH ?>public/Logo/loading.gif"><?php echo __('Loading...', 'asl_locator') ?></div>
      </div>
      <div class="panel-cont">
          <div class="panel-inner">
            <div class="col-md-12 asl-p-0">
                  <ul id="p-statelist" role="tablist" aria-multiselectable="true">
                </ul>
            </div>
          </div>
      </div>
      <div class="directions-cont hide">
        <div class="agile-modal-header">
          <button type="button" class="close"><span aria-hidden="true">&times;</span></button>
          <h4><?php echo __('Directions', 'asl_locator') ?></h4>
        </div>
        <div class="rendered-directions"></div>
      </div>
    </div>
  </div> 
</div>
</div>
<!-- This Plugin is developed by "Agile Store Locator for WordPress" https://agilestorelocator.com-->
<?php
////*SCRIPT TAGS STARTS FROM HERE:: FROM EVERY THING BELOW THIS LINE:: VARIALBLE LIMIT IS NOT SUPPORTING LONGER HTML*////
if($atts['no_script'] == 0):
?>
<script id="tmpl_list_item" type="text/x-jsrender">
  <div class="item" data-id="{{:id}}">
    <div class="row">
      <div class="col-md-12 addr-sec">
        <p class="p-title">{{:title}}</p>
        <p class="p-area">{{:address}}</p>
        {{if phone}}
        <p class="p-area"><span class="glyphicon icon-phone-outline"></span> <?php echo __( 'Phone','asl_locator') ?>: {{:phone}}</p>
        {{/if}}
        {{if email}}
        <p class="p-area"><span class="glyphicon icon-at"></span><a href="mailto:{{:email}}" style="text-transform: lowercase;color:inherit;">{{:email}}</a></p>
        {{/if}}
        {{if c_names}}
        <p class="p-category"><span class="glyphicon icon-tag"></span> {{:c_names}}</p>
        {{/if}}
        {{if open_hours}}
        <p class="p-time"><span class="glyphicon icon-clock-1"></span> {{:open_hours}}</p>
        {{/if}}
        {{if days_str}}
        <p class="p-time"><span class="glyphicon icon-calendar"></span>{{:days_str}}</p>
        {{/if}}
      </div>
    </div>
    <div class="row">
      <div class="mt-10 distance">
          <div class="col-xs-6">
            <p class="p-direction"><span class="s-direction"><?php echo __( 'Directions','asl_locator') ?></span></p>
          </div>
          {{if dist_str}}
          <div class="col-xs-6">
              <span class="s-distance"><?php echo __( 'Distance','asl_locator') ?>: {{:dist_str}}</span>
          </div>
          {{/if}}
      </div>
    </div>
    {{if description_2}}
    <div class="row">
      <div class="col-xs-12">
        <button type="button" onclick="javascript:asl_jQuery(this).next().next().slideToggle(100);asl_jQuery(this).next().show();asl_jQuery(this).hide()" class="asl_Readmore_button"><?php echo __( 'Read more','asl_locator') ?></button>
        <button type="button" onclick="javascript:asl_jQuery(this).next().slideToggle(100);asl_jQuery(this).prev().show();asl_jQuery(this).hide()" style="display:none" class="asl_Readmore_button"><?php echo __( 'Hide Details','asl_locator') ?></button>
        <p class="more_info" style="display:none">{{:description_2}}</p>
      </div>
    </div>
    {{/if}}
  </div>
</script>

<script id="asl_too_tip" type="text/x-jsrender">
  {{if path}}
  <div class="image_map_popup" style="display:none"><img src="{{:URL}}public/Logo/{{:path}}" /></div>
  {{/if}}
  <h3>{{:title}}</h3>
  <div class="infowindowContent">
    <div class="info-addr">
      <div class="address"><span class="glyphicon icon-location"></span><span>{{:address}}</span></div>
      {{if phone}}
      <div class="phone"><span class="glyphicon icon-phone-outline"></span><b><?php echo __( 'Phone','asl_locator') ?>: </b><a href="tel:{{:phone}}">{{:phone}}</a></div>
      {{/if}}
      {{if show_categories && c_names}}
      <div class="categories"><span class="glyphicon icon-tag"></span>{{:c_names}}</div>
      {{/if}}
      {{if dist_str}}
      <div class="distance"><?php echo __('Distance', 'asl_locator') ?>: {{:dist_str}}</div>
      {{/if}}
    </div>
    {{if path}}
    <div class="img_box" style="display:none">
      <img src="{{:URL}}public/Logo/{{:path}}" alt="logo">
    </div>
    {{/if}}
    <div class="asl-buttons"></div>
  </div><div class="arrow-down"></div>
</script>
<?php endif; ?>