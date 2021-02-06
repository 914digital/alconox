<?php
if( !defined( 'ABSPATH' ) ) exit;
  if($api_type != "web"){ 
  ?> 
  <!-------------------------- lead owner -------------------->
<?php 
if(isset($fields['OwnerId'])){
$panel_count++;
$users=$this->post('users',$meta);
?>
<div class="vx_div vx_refresh_panel ">    
      <div class="vx_head ">
<div class="crm_head_div"> <?php echo sprintf(__('%s. Object Owner',  'woocommerce-salesforce-crm' ),$panel_count); ?></div>
<div class="crm_btn_div"><i class="fa crm_toggle_btn fa-minus" title="<?php _e('Expand / Collapse','woocommerce-salesforce-crm') ?>"></i></div>
<div class="crm_clear"></div> 
  </div>                 
    <div class="vx_group ">
   <div class="vx_row"> 
   <div class="vx_col1"> 
  <label for="crm_owner"><?php _e("Assign Owner", 'woocommerce-salesforce-crm'); $this->tooltip($tooltips['vx_owner_check']);?></label>
  </div>
  <div class="vx_col2">
  <input type="checkbox" style="margin-top: 0px;" id="crm_owner" class="crm_toggle_check <?php if(empty($users)){echo 'vx_refresh_btn';} ?>" name="meta[owner]" value="1" <?php echo !empty($feed["owner"]) ? "checked='checked'" : ""?> autocomplete="off"/>
    <label for="owner"><?php _e("Yes and Do not trigger salesforce assignment rules", 'woocommerce-salesforce-crm'); ?></label>
  </div>
<div class="clear"></div>
</div>
    <div id="crm_owner_div" style="<?php echo empty($feed["owner"]) ? "display:none" : ""?>">
  <div class="vx_row">
  <div class="vx_col1">
  <label><?php _e('Users List','woocommerce-salesforce-crm'); $this->tooltip($tooltips['vx_owners']); ?></label>
  </div>
  <div class="vx_col2">
  <button class="button vx_refresh_data" data-id="refresh_users" type="button" autocomplete="off" style="vertical-align: baseline;">
  <span class="reg_ok"><i class="fa fa-refresh"></i> <?php _e('Refresh Data','woocommerce-salesforce-crm') ?></span>
  <span class="reg_proc"><i class="fa fa-refresh fa-spin"></i> <?php _e('Refreshing...','woocommerce-salesforce-crm') ?></span>
  </button>
  </div> 
   <div class="clear"></div>
  </div> 

  <div class="vx_row">
   <div class="vx_col1">
  <label for="crm_sel_user"><?php _e('Select User','woocommerce-salesforce-crm'); $this->tooltip($tooltips['vx_sel_owner']); ?></label>
</div> 
<div class="vx_col2">

  <select id="crm_sel_user" name="meta[user]" style="width: 100%;" autocomplete="off">
  <?php echo $this->gen_select($users,$feed['user'],__('Select User','woocommerce-salesforce-crm')); ?>
  </select>

   </div>

   <div class="clear"></div>
   </div>
 
  
  </div>
  

  </div>
  </div>
<?php
}              
if(in_array($module,array("Order",'Opportunity','Quote'))){
$panel_count++;
$books=$this->post('price_books',$meta);
?>
<!-------------------------- lead products -------------------->
<div class="vx_div vx_refresh_panel">    
<div class="vx_head ">
<div class="crm_head_div"> <?php echo sprintf(__('%s. Create Order Products',  'woocommerce-salesforce-crm' ),$panel_count); ?></div>
<div class="crm_btn_div"><i class="fa crm_toggle_btn fa-minus" title="<?php _e('Expand / Collapse','woocommerce-salesforce-crm') ?>"></i></div>
<div class="crm_clear"></div> 
  </div>    
            
    <div class="vx_group ">
 
   <div class="vx_row"> 
   <div class="vx_col1"> 
  <label for="order_items"><?php _e("Line Items", 'woocommerce-salesforce-crm'); $this->tooltip($tooltips['vx_line_items']);?></label>
  </div>
  <div class="vx_col2">
  <input type="checkbox" style="margin-top: 0px;" id="crm_items" class="crm_toggle_check <?php if(empty($books) ){echo 'vx_refresh_btn';} ?>" name="meta[order_items]" value="1" <?php echo !empty($feed["order_items"]) ? "checked='checked'" : ""?> autocomplete="off"/>
    <label for="crm_items"><?php _e("Create an order product for each line item", 'woocommerce-salesforce-crm'); ?></label>
  </div>
<div class="clear"></div>
</div>
    <div id="crm_items_div" style="<?php echo empty($feed["order_items"]) ? "display:none" : ""?>">
  <div class="vx_row">
  <div class="vx_col1">
  <label for="crm_sel_book"><?php _e('Price Books','woocommerce-salesforce-crm'); $this->tooltip($tooltips['vx_price_books']); ?></label>
  </div>
  <div class="vx_col2">
  <button class="button vx_refresh_data" data-id="refresh_books" type="button" autocomplete="off" style="vertical-align: baseline;">
  <span class="reg_ok"><i class="fa fa-refresh"></i> <?php _e('Refresh Data','woocommerce-salesforce-crm') ?></span>
  <span class="reg_proc"><i class="fa fa-refresh fa-spin"></i> <?php _e('Refreshing...','woocommerce-salesforce-crm') ?></span>
  </button>
  </div> 
   <div class="clear"></div>
  </div> 

  <div class="vx_row">
   <div class="vx_col1">
  <label for="crm_sel_book"><?php _e('Select Price Book','woocommerce-salesforce-crm'); $this->tooltip($tooltips['vx_sel_price_book']); ?></label>
</div> 
<div class="vx_col2">

  <select id="crm_sel_book" name="meta[price_book]" style="width: 100%;" autocomplete="off">
  <?php echo $this->gen_select($books,$feed['price_book'],__('Select Price Book','woocommerce-salesforce-crm')); ?>
  </select>

   </div>

   <div class="clear"></div>
   </div>

 
      <div class="vx_row">
   <div class="vx_col1">
  <label for="pro_desc"><?php _e('Product Description','woocommerce-salesforce-crm'); $this->tooltip($tooltips['vx_pro_desc']); ?></label>
</div> 
<div class="vx_col2">

  <textarea id="pro_desc" name="meta[pro_desc]" style="width: 100%;"><?php echo $this->post('pro_desc',$feed); ?></textarea>

   </div>

   <div class="clear"></div>
   </div>

  
  </div>


  </div>
  </div>
<?php
} }
if(in_array($module,$camp_support)){
      $panel_count++;
      $campaigns=$this->post('campaigns',$meta);
      $member_status_arr=$this->post('member_status',$meta);
  ?>
    <div class="vx_div vx_refresh_panel ">    
      <div class="vx_head ">
<div class="crm_head_div"> <?php  echo sprintf(__('%s. Add as Campaign Member',  'woocommerce-salesforce-crm' ),$panel_count); ?></div>
<div class="crm_btn_div"><i class="fa crm_toggle_btn fa-minus" title="<?php _e('Expand / Collapse','woocommerce-salesforce-crm') ?>"></i></div>
<div class="crm_clear"></div> 
  </div>                 
    <div class="vx_group ">
   <div class="vx_row"> 
   <div class="vx_col1"> 
  <label for="crm_camp"><?php _e("Add to Campaign", 'woocommerce-salesforce-crm'); $this->tooltip($tooltips['vx_camp_check']);?></label>
  </div>
  <div class="vx_col2">
  <input type="checkbox" style="margin-top: 0px;" id="crm_camp" class="crm_toggle_check <?php if(empty($member_status_arr) && empty($campaigns)){echo 'vx_refresh_btn';} ?>" name="meta[add_to_camp]" value="1" <?php echo !empty($feed["add_to_camp"]) ? "checked='checked'" : ""?> autocomplete="off"/>
    <label for="crm_optin"><?php _e("Enable", 'woocommerce-salesforce-crm'); ?></label>
  </div>
<div class="clear"></div>
</div>
    <div id="crm_camp_div" style="<?php echo empty($feed["add_to_camp"]) ? "display:none" : ""?>">
   <?php  if($api_type != "web"){  ?> 
  <div class="vx_row">
  <div class="vx_col1">
  <label><?php _e('Campaign & Status Lists','woocommerce-salesforce-crm'); $this->tooltip($tooltips['vx_camps']); ?></label>
  </div>
  <div class="vx_col2">
  <button class="button vx_refresh_data" data-id="refresh_campaigns" type="button" autocomplete="off" style="vertical-align: baseline;">
  <span class="reg_ok"><i class="fa fa-refresh"></i> <?php _e('Refresh Data','woocommerce-salesforce-crm') ?></span>
  <span class="reg_proc"><i class="fa fa-refresh fa-spin"></i> <?php _e('Refreshing...','woocommerce-salesforce-crm') ?></span>
  </button>
  </div> 
   <div class="clear"></div>
  </div> 
  <?php } ?>
  <div class="vx_row">
   <div class="vx_col1">
  <label for="crm_sel_camp"><?php _e('Select Campaign','woocommerce-salesforce-crm'); $this->tooltip($tooltips['vx_sel_camp']); ?></label>
</div> <div class="vx_col2">
<?php   if($api_type != "web"){   ?>
<div style="width: 100%; display: table;">
<div style="display: table-cell; width: 85%;">
  <select id="crm_sel_camp" name="meta[campaign]" style="width: 100%; <?php if($this->post('camp_type',$feed) != ""){echo 'display: none';} ?>" autocomplete="off">
  <?php echo $this->gen_select($campaigns,$feed['campaign'],__('Select Campaign','woocommerce-salesforce-crm')); ?>
  </select>
  <input type="text" name="meta[campaign_id]" id="crm_camp_id" value="<?php echo esc_attr($this->post('campaign_id',$feed)); ?>" style="vertical-align: middle; width: 100%; <?php if($this->post('camp_type',$feed) == ""){echo 'display: none';} ?>" placeholder="<?php _e('Enter Campaign ID','woocommerce-salesforce-crm') ?>" autocomplete="off">
   </div>
   
  <div style="display: table-cell; padding-left: 10px;">
  <input type="hidden" name="meta[camp_type]" id="crm_camp_type" value="<?php echo $this->post('camp_type',$feed);  ?>" autocomplete="off">
    <button class="button" id="toggle_camp" type="button" style="vertical-align: middle; width: 140px">
  <span class="reg_ok" style="<?php if($this->post('camp_type',$feed) != ""){echo 'display: none';} ?>"><?php _e('Enter Campaign ID','woocommerce-salesforce-crm') ?></span>
  <span class="reg_proc" style="<?php if($this->post('camp_type',$feed) == ""){echo 'display: none';}else{echo 'display:block;';} ?>"><?php _e('Select Campaign','woocommerce-salesforce-crm') ?></span>
  </button>
  </div>
  </div>
  <?php }else{ ?>
  <input type="text" id="crm_sel_camp" class="vx_input_100" name="meta[web_camp_id]" placeholder="<?php _e('Enter Campaign Id','woocommerce-salesforce-crm'); ?>" value="<?php echo esc_attr($this->post('web_camp_id',$feed)); ?>">
  <?php } ?>
  </div>
   <div class="clear"></div>
   </div>
  <div class="vx_row">
   <div class="vx_col1">
  <label for="crm_sel_status"><?php _e('Member Status','woocommerce-salesforce-crm'); $this->tooltip($tooltips['vx_sel_status']); ?></label>
  </div> 
  <div class="vx_col2">
  <?php   if($api_type != "web"){   ?>
  <select id="crm_sel_status" name="meta[member_status]" class="vx_sel" autocomplete="off">
  <?php echo $this->gen_select($member_status_arr,$feed['member_status'],__('Select Status','woocommerce-salesforce-crm')); ?>
  </select> 
   <?php }else{ ?>
  <input type="text" id="crm_sel_status" class="vx_input_100" name="meta[web_mem_status]" placeholder="<?php _e('Enter Member Status','woocommerce-salesforce-crm'); ?>" value="<?php echo esc_attr($this->post('web_mem_status',$feed)); ?>">
  <?php } ?> 
  </div>
   <div class="clear"></div>
  </div>
  
  </div>
  

  </div>
  </div>
    <?php
  }
  ?>