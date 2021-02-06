<?php


$class = '';

if($all_configs['display_list'] == '0' || $all_configs['first_load'] == '3' || $all_configs['first_load'] == '4')
  $class = ' map-full';

if($all_configs['full_width'])
  $class .= ' full-width';


if($all_configs['advance_filter'] == '0')
  $class .= ' no-asl-filters';

if($all_configs['advance_filter'] == '1' && $all_configs['layout'] == '1')
  $class .= ' asl-adv-lay1';

//add Full height
$class .= ' '.$all_configs['full_height'];


$default_addr = (isset($all_configs['default-addr']))?$all_configs['default-addr']: '';


$geo_btn_class      = ($all_configs['geo_button'] == '1')?'asl-geo icon-direction-outline':'icon-search';
$search_type_class  = ($all_configs['search_type'] == '1')?'asl-search-name':'asl-search-address';
$panel_order        = (isset($all_configs['map_top']))?$all_configs['map_top']: '2';

$ddl_class_grid = ($all_configs['search_2'])? 'col-xs-4': 'col-xs-4';
$tsw_class_grid = ($all_configs['search_2'])? 'col-xs-4 col-md-2': 'col-xs-4 col-md-2';
$adv_class_grid = ($all_configs['search_2'])? 'col-sm-8': 'col-sm-12';


?>
<link rel='stylesheet' id='asl-plugin-css' href='<?php echo ASL_URL_PATH ?>public/css/asl.css?version=<?php echo ASL_CVERSION ?>' type='text/css' media='all' />
<style type="text/css">
  #asl-storelocator.asl-p-cont .Status_filter .onoffswitch-inner::before {content: "<?php echo __('OPEN', 'asl_locator') ?>" !important}
  #asl-storelocator.asl-p-cont .Status_filter .onoffswitch-inner::after {content: "<?php echo __('ALL', 'asl_locator') ?>" !important}
  #asl-storelocator.container.storelocator-main.asl-p-cont .asl-loc-sec .asl-panel {order: <?php echo $panel_order ?>;}
</style>
<div id="asl-storelocator" class="container storelocator-main asl-p-cont asl-template-0 asl-layout-<?php echo $all_configs['layout']; ?> asl-bg-<?php echo $all_configs['color_scheme'].$class; ?> asl-text-<?php echo $all_configs['font_color_scheme'] ?>">
  <?php if($all_configs['advance_filter']): ?>    
  <div class="row Filter_section">
      <div class="col-xs-12 col-sm-4 search_filter">
          <p><?php echo __( 'Search Location', 'asl_locator') ?></p>
          <p>
            <input type="text" value="<?php echo $default_addr ?>" data-submit="disable" data-provide="typeahead" tabindex="2" id="auto-complete-search" placeholder="<?php echo __( 'Enter a Location', 'asl_locator') ?>"  class="<?php echo $search_type_class ?> form-control typeahead isp_ignore">
            <span><i class="<?php echo $geo_btn_class ?>" title="<?php echo ($all_configs['geo_button'] == '1')?__('Current Location','asl_locator'):__('Search Location','asl_locator') ?>"></i></span>
          </p>
      </div>
      <div class="col-xs-12 col-sm-8">
        <div class="<?php if($all_configs['search_2']) echo 'no-pad'; ?>">
          <div class="row">
            <?php if($all_configs['search_2']): ?>
            <div class="col-xs-12 col-md-4 search_filter asl-name-search">
              <p><?php echo __( 'Search Name', 'asl_locator') ?></p>
              <p><input type="text" tabindex="2" id="auto-complete-search" placeholder="<?php echo __( 'Search Name', 'asl_locator') ?>"  class="asl-search-name form-control isp_ignore"></p>
            </div>
            <?php endif ?>
            <div class="<?php echo $adv_class_grid ?> asl-advance-filters hide">
                <div class="row">
                  <div class="<?php echo $ddl_class_grid ?> drop_box_filter">
                      <div class="asl-filter-cntrl">
                        <label class="asl-cntrl-lbl"><?php echo $all_configs['category_title']  ?></label>
                        <div class="categories_filter" id="categories_filter">
                        </div>
                      </div>
                  </div>
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
                  <input type="radio" name="dist-type"  id="rbtn-km" value="0"> <?php echo __('KM', 'asl_locator') ?>
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
                <div class="">
                <div class="col-md-9 asl-p-0">
                  <input type="text" class="form-control" id="asl-current-loc" placeholder="<?php echo __('Your Address', 'asl_locator') ?>">
                </div>
                <div class="col-md-3 asl-p-0">
                  <button type="button" id="asl-btn-locate" class="btn btn-block btn-default"><?php echo __('LOCATE', 'asl_locator') ?></button>
                </div>
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
        <p><?php echo __( 'Search Location', 'asl_locator') ?></p>
        <div class="asl-store-search">
            <input type="text" value="<?php echo $default_addr ?>" id="auto-complete-search" class="<?php echo $search_type_class ?> form-control" placeholder="<?php echo __( 'Enter a Location', 'asl_locator') ?>">
            <span><i class="glyphicon <?php echo $geo_btn_class ?>" title="<?php echo ($all_configs['geo_button'] == '1')?__('Current Location','asl_locator'):__('Search Location','asl_locator') ?>"></i></span>
        </div>
        <div class="Num_of_store">
          <span><?php echo $all_configs['head_title'] ?>: <span class="count-result">0</span></span>
        </div>    
      </div>
      <?php else: ?>
      <div class="Num_of_store">
        <span><?php echo $all_configs['head_title'] ?>: <span class="count-result">0</span></span>
      </div> 
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
                  <ul id="p-statelist"  role="tablist" aria-multiselectable="true">
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
<!-- This plugin is developed by "Agile Store Locator by WordPress" https://agilestorelocator.com -->
<?php
////*SCRIPT TAGS STARTS FROM HERE:: FROM EVERY THING BELOW THIS LINE:: VARIALBLE LIMIT IS NOT SUPPORTING LONGER HTML*////
if($atts['no_script'] == 0):
?>
<script id="tmpl_list_item" type="text/x-jsrender">
  <div class="item" data-id="{{:id}}">
    <div class="addr-sec">
      <p class="p-title">{{:title}}</p>
    </div>
    <div class="row">
      <div class="col-md-9 col-xs-9 addr-sec">
        <p class="p-area"><span class="glyphicon icon-location"></span><span>{{:address}}</span></p>
        {{if phone}}
              <p class="p-area"><span class="glyphicon icon-phone-outline"></span><?php echo __( 'Phone','asl_locator') ?>: <a href="tel:{{:phone}}">{{:phone}}</a></p>
        {{/if}}
        {{if email}}
        <p class="p-area"><span class="glyphicon icon-at"></span><a href="mailto:{{:email}}" style="text-transform: lowercase">{{:email}}</a></p>
        {{/if}}
        {{if fax}}
          <p class="p-area"><span class="glyphicon icon-fax"></span> <?php echo __('Fax', 'asl_locator') ?>:{{:fax}}</p>
        {{/if}}
        {{if c_names}}
        <p class="p-area"><span class="glyphicon icon-tag"></span> {{:c_names}}</p>
        {{/if}}
        {{if open_hours}}
        <p class="p-area"><span class="glyphicon icon-clock-1"></span> {{:open_hours}}</p>
        {{/if}}
        {{if days_str}}
        <p class="p-area"><span class="glyphicon icon-calendar"></span> {{:days_str}}</p>
        {{/if}}
        {{if description}}
        <p class="p-description">{{:description}}</p>
        {{/if}}
      </div>
      {{if path}}
      <div class="col-md-3 col-xs-3">
        <div class="item-thumb">
            <a class="thumb-a">
                <img src="<?php echo ASL_URL_PATH ?>public/Logo/{{:path}}" alt="logo">
            </a>
        </div>
      </div>
      {{/if}}
    </div>
    <div class="row">
      <div class="mt-10 distance">
          <div class="col-xs-6">
            <p class="p-direction"><button type="button" class="btn btn-asl s-direction"><?php echo __( 'Direction','asl_locator') ?></button></p>
          </div>
          {{if dist_str}}
          <div class="col-xs-6">
              <a class="s-distance">{{:dist_str}}</a>
          </div>
          {{/if}}
      </div>
    </div>
  </div>
</script>
<script id="asl_too_tip" type="text/x-jsrender">
{{if path}}
<div class="image_map_popup" style="display:none"><img src="{{:URL}}public/Logo/{{:path}}" /></div>
{{/if}}
  <h3>{{:title}}</h3>
  <div class="infowindowContent">
    <div class="info-addr">
      <div class="address"><span class="glyphicon icon-location"></span>{{:address}}</div>
      {{if phone}}
      <div class="phone"><span class="glyphicon icon-phone-outline"></span><b><?php echo __( 'Phone','asl_locator') ?>: </b><a href="tel:{{:phone}}">{{:phone}}</a></div>
      {{/if}}
      {{if open_hours}}
      <div class="p-time"><span class="glyphicon icon-clock-1"></span> {{:open_hours}}</div>
      {{/if}}
      {{if days_str}}
      <div class="p-time"><span class="glyphicon icon-calendar-outlilne"></span> {{:days_str}}</div>
      {{/if}}
      {{if email}}
      <div class="p-time"><span class="glyphicon icon-at"></span><a href="mailto:{{:email}}" style="text-transform: lowercase">{{:email}}</a></div>
      {{/if}}
      {{if show_categories && c_names}}
      <div class="categories"><span class="glyphicon icon-tag"></span>{{:c_names}}</div>
      {{/if}}
      {{if description}}
      <div class="p-time">{{:description}}</div>
      {{/if}}
      {{if dist_str}}
      <div class="distance"><?php echo __( 'Distance','asl_locator') ?>: {{:dist_str}}</div>
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