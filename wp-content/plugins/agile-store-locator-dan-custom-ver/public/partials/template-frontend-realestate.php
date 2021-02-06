<?php

$all_configs['infobox_layout'] = '0';
$all_configs['template']       = '2';


$class = '';


//if time_switch and distance is off then no-advance filter
if($all_configs['time_switch'] == '0' && $all_configs['distance_slider'] == '0' && $all_configs['show_categories'] == '0')
  $all_configs['advance_filter'] = '0';


if($all_configs['display_list'] == '0' || $all_configs['first_load'] == '3'  || $all_configs['first_load'] == '4')
  $class = ' map-full';

if($all_configs['full_width'])
  $class .= ' full-width';


if($all_configs['advance_filter'] == '0')
  $class .= ' no-asl-filters';

if($all_configs['layout'] == '1' || $all_configs['advance_filter'] == '0'){
  $class .= ' asl-no-advance';
}
else if($all_configs['show_categories'] == '0') {
 $class .= ' asl-no-categories'; 
}


$default_addr = (isset($all_configs['default-addr']))?$all_configs['default-addr']: '';

//add Full height
if($all_configs['full_height'])
  $class .= ' '.$all_configs['full_height'];


$distance_control = ($all_configs['distance_control'] == '1')?'1':'0';



$geo_btn_class      = ($all_configs['geo_button'] == '1')?'asl-geo icon-direction-outline':'icon-search';
$search_type_class  = ($all_configs['search_type'] == '1')?'asl-search-name':'asl-search-address';
$panel_order        = (isset($all_configs['map_top']))?$all_configs['map_top']: '2';
?>
<link rel='stylesheet' id='asl-plugin-css'  href='<?php echo ASL_URL_PATH ?>public/css/asl-2.css?v=<?php echo ASL_CVERSION; ?>' type='text/css' media='all' />
<style type="text/css">
#asl-storelocator.asl-p-cont .Status_filter .onoffswitch-inner::before{content: "<?php echo __('OPEN', 'asl_locator') ?>" !important}
#asl-storelocator.asl-p-cont .Status_filter .onoffswitch-inner::after{content: "<?php echo __('ALL', 'asl_locator') ?>" !important}
#asl-storelocator.container.storelocator-main.asl-p-cont .asl-loc-sec .asl-panel {order: <?php echo $panel_order ?>;}
</style>
<div id="asl-storelocator" class="container asl-template-2 storelocator-main asl-p-cont asl-layout-<?php echo $all_configs['layout']; ?>  asl-bg-<?php echo $all_configs['color_scheme_2'].$class; ?> asl-text-<?php echo $all_configs['font_color_scheme'] ?> asl-realestate">
  <div class="row asl-loc-sec">
    <div class="col-sm-4 col-xs-12 asl-panel-box">
      <?php if($all_configs['advance_filter'] && $all_configs['layout'] != '1'): ?> 
      <div class="row">
          <div class="filter-box asl-dist-ctrl-<?php echo $distance_control ?>">
            <div class="col-sm-4 col-xs-4 col-md-4 Status_filter">
                <div class="asl-filter-cntrl">
                    <label class="asl-cntrl-lbl"><?php echo __('Status', 'asl_locator') ?></label>
                    <div class="onoffswitch">
                      <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="asl-open-close" checked>
                      <label class="onoffswitch-label" for="asl-open-close">
                          <span class="onoffswitch-inner"></span>
                          <span class="onoffswitch-switch asl-ico"></span>
                      </label>
                    </div>
                </div>
            </div>
            <div class="col-xs-8 col-sm-8 col-md-8 pull-right">
                <div class="range_filter hide">
                    <p class="rangeFilter">
                      <span><?php echo __( 'Distance Range','asl_locator') ?></span>
                      <input id="asl-radius-slide" type="text" class="span2" />
                      <span class="rad-unit"><?php echo __( 'Radius','asl_locator') ?>: <span id="asl-radius-input"></span> <span id="asl-dist-unit"><?php echo __( 'KM','asl_locator') ?></span></span>
                    </p>
                </div>
            </div>
          </div>
      </div>
      <?php else: ?>
        <div class="Num_of_store">
          <div class="calign"><?php echo $all_configs['head_title']  ?> <span class="count-result">0</span></div>
        </div>
      <?php endif; ?>
      <div class="asl-m--15">
        <div class="col-sm-12 col-xs-12 asl-panel">
          <?php if($all_configs['advance_filter'] && $all_configs['layout'] != '1'): ?> 

              <?php
              //if show_categories false
              if($all_configs['show_categories'] == '1'): ?> 
              <div class="Num_of_store hide">
                <span class="icon asl-p-0 col-xs-2"><img src="<?php echo ASL_URL_PATH ?>public/img/icon-1.png"></span>
                <span class="col-xs-8 asl-cat-name"><span class="sele-cat"></span> <span class="count-result">0</span></span>
                <span class="back-button col-xs-2"><i class="glyphicon icon-left-open"></i></span>
              </div>   
              <div class="cats-title">
                <span class="icon"><img src="<?php echo ASL_URL_PATH ?>public/img/category_icon.png"></span>
                <span><?php echo $all_configs['category_title']  ?></span>
              </div>
              <?php else: ?>
                <div class="Num_of_store">
                <div class="calign"> <?php echo $all_configs['head_title']  ?> <span class="count-result">0</span></div>
              </div>   
              <?php endif; ?>

           <?php endif; ?>
          <!--  Panel Listing -->
          <?php if($all_configs['advance_filter'] == '1' && $all_configs['layout'] != '1' && $all_configs['show_categories'] == '1'): ?>
          <div class="categories-panel">
          </div>
          <?php endif ?>

          <div id="asl-list" class="storelocator-panel <?php if($all_configs['advance_filter'] && $all_configs['layout'] != '1' && $all_configs['show_categories'] == '1') echo 'hide'; ?>">
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
            <div class="directions-cont hide" style="padding-top:12px">
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
    <div class="col-sm-8 col-xs-12 asl-map">
      <div class="store-locator">
          
        <div class=" search_filter inside-map">
            <p>
              <input type="text" value="<?php echo $default_addr ?>" id="auto-complete-search" class="form-control <?php echo $search_type_class ?>" placeholder="<?php echo __( 'Find A Store','asl_locator') ?>">
              <span><i class="glyphicon <?php echo $geo_btn_class ?>" title="<?php echo ($all_configs['geo_button'] == '1')?__('Current Location','asl_locator'):__('Search Location','asl_locator') ?>"></i></span>
            </p>
        </div>
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
                <input readonly="true" type="text"  class="directions-to form-control" id="to-lbl" placeholder="<?php echo __( 'Prepopulated Destination Address','asl_locator') ?>">
              </div>
              <div class="form-group">
                <span for="frm-lbl"><?php echo __('Show Distance In', 'asl_locator') ?></span>
                <label class="checkbox-inline">
                  <input type="radio" name="dist-type"  id="rbtn-km" value="0"> <?php echo __( 'KM','asl_locator') ?>
                </label>
                <label class="checkbox-inline">
                  <input type="radio" name="dist-type" checked id="rbtn-mile" value="1"> <?php echo __( 'Mile','asl_locator') ?>
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
  </div>
</div>

<script id="tmpl_list_item" type="text/x-jsrender">
  <div class="item" data-id="{{:id}}">
    <div class="row">
    <div class="col-xs-4 img-section">
      {{if path}}
        <a class="thumb-a">
            <img src="<?php echo ASL_URL_PATH ?>public/Logo/{{:path}}" alt="logo">
        </a>
      {{/if}}
      <p class="asl-view-more"><a href="{{:website}}"><?php echo __('View More', 'asl_locator') ?></a></p>
    </div>
    <div class="col-xs-8 data-section">
          <div class="title-item"><p class="p-title"><a href="{{:website}}">{{:title}}</a></p></div>
          <div class="clear"></div>
          <span class="asl-price">{{:description}}</span>
          <div class="addr-sec">
              <p class="item-info">{{:description_2}}</p>
              <p class="p-area">{{:address}}</p>
              {{if distance}}
                <p class="p-area p-distance"><?php echo __( 'Distance','asl_locator') ?>: {{:dist_str}}</p>
              {{/if}}
              <div class="sec-1">
                  {{if phone}}
                  <p class="p-area"><span class="glyphicon glyphicon-phone"></span> {{:phone}}</p>
                  {{/if}}
                  {{if email}}
                  <p class="p-area"><span class="glyphicon icon-at"></span><a href="{{:email}}">{{:email}}</a></p>
                  {{/if}}
              </div>
              <div class="sec-1">
                  {{if end_time && start_time}}
                  <p class="p-time"><span class="glyphicon icon-clock-1"></span><?php echo __('Visit Time', 'asl_locator') ?>: {{:start_time}} - {{:end_time}}</p>
                  {{/if}}
                  {{if days_str}}
                  <p class="p-time"><span class="glyphicon icon-calendar-outlilne"></span>{{:days_str}}</p>
                  {{/if}}
              </div>
          </div>
      </div>
    </div>
  <div>
</script>



<script id="asl_too_tip" type="text/x-jsrender">
<div class="image_map_popup" style="display:none"><img src="{{:URL}}public/Logo/{{:path}}" /></div>
  <h3>{{:title}}</h3>
  <div class="infowindowContent">
    <div class="info-addr">
      <div class="address"><span class="glyphicon icon-location"></span>{{:address}}</div>
      {{if phone}}
      <div class="phone"><span class="glyphicon icon-phone-outline"></span><b><?php echo __('Phone', 'asl_locator') ?>: </b><a href="tel:{{:phone}}">{{:phone}}</a></div>
      {{/if}}
      {{if end_time && start_time}}
      <div class="p-time"><span class="glyphicon icon-clock-1"></span> {{:start_time}} - {{:end_time}}</div>
      {{/if}}
      {{if days_str}}
      <div class="p-time"><span class="glyphicon icon-calendar-outlilne"></span> {{:days_str}}</div>
      {{/if}}
      {{if distance}}
      <div class="distance"><?php echo __('Distance', 'asl_locator') ?>: {{:dist_str}}</div>
      {{/if}}
    </div>
    <div class="img_box" style="display:none">
    <img src="{{:URL}}public/Logo/{{:path}}" alt="logo">
  </div>
  <div class="asl-buttons"></div>
</div><div class="arrow-down"></div>
</script>
