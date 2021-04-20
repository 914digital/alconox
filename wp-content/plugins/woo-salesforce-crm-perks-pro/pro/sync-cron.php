<?php 
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;
if( !class_exists( 'vxcf_sales_cron' ) ) {

class vxcf_sales_cron extends vxc_sales{
    
  public static $checking_update=false;
  public $items_batch=30;
 public static $lics=array();    
 public static $users=array();    
 public static $updates=array();    
public function __construct() {
 add_action( 'add_section_tab_wc_vxc_sales', array( $this, 'output_sections' ),60 );
 add_filter( 'add_section_html_vxc_sales', array( $this, 'section_html' ) );
 add_action('wp_ajax_vxc_sales_get_sync_settings', array($this, 'get_settings_ajax'));
//add_action('wp', array($this, 'wp')); 
// add_action( 'vxc_sales_stock_cron',array($this,'stock_cron'));
 add_action( 'vxc_sales_item_cron',array($this,'item_cron'));
/// add_action( 'vx_abs2',array($this,'test'));
// add_filter('woocommerce_add_to_cart_validation', array($this, 'maybe_validate_stock'),20,5);
}   


public function wp(){
    if(isset($_GET['vx'])){
        $next=time()+10;

         $time=current_time( 'timestamp' ,1 );
        $next = $time+5;
     //   wp_schedule_single_event($next, 'vxc_sales_item_cron');
        $cron_jobs = get_option( 'cron' );
      /*  $p=wc_get_product_id_by_sku('TestVX13ddd');
        $p=new WC_Product( $p );
        $p->set_sku('TestVX13ss');
        $p->set_name('TestVX13 title is heresss');
        $p->set_status('publish');
        $p->save();
        var_dump($p); die();
        $p=new WC_Product( 10327 );
        $p->set_status('trash');
        $p->save();
        die();*/
        
     //   var_dump($cron_jobs,time());
         $this->item_cron();
  die();
    $event='vx_abs2'; $time=time()+1;
    //$time=current_time( 'timestamp' )+1;
   // wp_clear_scheduled_hook($event);
    wp_schedule_single_event($time, $event );
    $next = wp_next_scheduled( $event );
    //$time=current_time( 'timestamp' );
    var_dump($next,$time); die();
    }
}
public function set_item_cron($meta,$cron){
   
    $next=0;
    if(isset($cron['next'])){ unset($cron['next']); }
    $next = wp_next_scheduled( 'vxc_sales_item_cron' );
 if($next){
 wp_clear_scheduled_hook( 'vxc_sales_item_cron' ); 
 }
   if(!empty($meta['sync_items'])){
     $time=current_time( 'timestamp' ,1 );
        $next = $time+(int)$meta['item_cron'];
        $local_next= current_time( 'timestamp') +$meta['item_cron'];
       // $next = $time+1;
 $created=wp_schedule_single_event($next, 'vxc_sales_item_cron');
//  if($created !== false ){
 $cron['next']=array('label'=>'Next Check','time'=>$local_next);   
//}      
// }
 }

 return $cron;  
}
public function set_stock_cron($meta,$cron){
 
 if(isset($cron['next_qty'])){ unset($cron['next_qty']); }
 $next = wp_next_scheduled( 'vxc_sales_stock_cron' );
 if($next){
 wp_clear_scheduled_hook( 'vxc_sales_stock_cron' );
 }
    $next=0;
 if(!empty($meta['sync_qty'])){
 //$next = wp_next_scheduled( 'vxc_sales_stock_cron' );
 $time=current_time('timestamp',1);
        $next=$time+(int)$meta['stock_cron'];
       $local_next=current_time('timestamp')+(int)$meta['stock_cron'];
       $created=wp_schedule_single_event($next, 'vxc_sales_stock_cron');
 // if($created !== false ){
 $cron['next_qty']=array('label'=>'Next Check','time'=>$local_next);   
//}
}
return $cron;    
}
public function item_cron(){

$this->cron();    
}
public function stock_cron(){
$this->cron('stock');     
}
public function cron($cron_type='') {

ini_set('max_execution_time', 1000);
$cron=get_option('vxcf_sales_cron_status',array());
$meta=get_option('vxcf_sales_cron',array()); 
if(empty($meta['account'])){ return; }
$is_error=false;

 $log_arr=array("object"=>'',"order_id"=>'0',"crm_id"=>'0',"meta"=>'',"time"=>date('Y-m-d H:i:s'),"status"=>'6',"link"=>'',"data"=>'',"response"=>'',"extra"=>'',"feed_id"=>'','parent_id'=>'0','event'=>'');
 
$info=$this->get_info( $meta['account'] ); 
$api=$this->get_api($info);
//$res=$api->search_in_sf('Product2','ProductCode','','');
//$cid = get_term_by('name','Cat 10', 'product_cat' );
/*$img=$api->get_rec('405865','file');
$upload = wp_upload_bits( 'test.png', '', $img->content );
//$fields=$api->get_crm_fields('itemCustomField');    */      
//$fields=$api->get_crm_fields('itemOptionCustomField');
//$tx=wc_get_attribute_taxonomies();
  /* $p=new WC_Product( ); 
    $p->set_name('vx testing 0');
    $p->set_description('short description');
    $p->set_short_description('detailed descr');
    $p->set_sku(rand(0,999));
//$p->save();
    $attrs_arr=array();
      $attribute = new WC_Product_Attribute();
      $attribute->set_id( 2 );
      $attribute->set_name( 'pa_co' );
      $attribute->set_options( array(17) );
      $attribute->set_position( 0 );
      $attribute->set_visible( 1 );
      $attribute->set_variation(  1 );
   
      $attrs_arr[] = $attribute;


   $p->set_attributes($attrs_arr);
   $p->save();
   die();
    global $wpdb;
$sql="SELECT * FROM $wpdb->postmeta WHERE meta_key ='cfx_sales_id' AND  meta_value in ('".implode("','",$ids_temp)."') LIMIT 1";   
$posts = $wpdb->get_results($sql, ARRAY_A);
*/  

if($cron_type == ''){

$time_start=time();
$field_ids=array('Id','LastModifiedDate','CreatedDate','Family','IsActive');
if(!empty($meta['item_title'])){
$field_ids[]=$meta['item_title'];    
}
if(!empty($meta['item_desc'])){
$field_ids[]=$meta['item_desc'];    
}

if(!empty($meta['item_short'])){
$field_ids[]=$meta['item_short'];    
}

if(!empty($meta['item_sku'])){
$field_ids[]=$meta['item_sku'];    
}
if(!empty($meta['item_img'])){
$field_ids[]=$meta['item_img'];    
}
if(!empty($meta['attr']) && is_array($meta['attr']) ){
$field_ids=array_merge($field_ids,$meta['attr']);    
}
//var_dump($meta); die();

$q="SELECT ".implode(',',array_unique($field_ids))." FROM Product2 "; // AND LastModifiedDate > ".date('Y-m-d\TH:i:s\Z',$time).' ORDER BY LastModifiedDate ' , CreatedDate
$where=array();
if(!empty($meta['cats'])){
  $cats=array();
  foreach($meta['cats'] as $v){
 $cats[]='Family=\''.$v."'";     
  }
$where[]='('.implode(' OR ',$cats).')';    
}
if(!empty($meta['after'])){
    $where[]='LastModifiedDate >'.date('Y-m-d\TH:i:s\Z',$meta['after']);
}
if(!empty($where)){
 $q.=' WHERE '.implode(' AND ',$where);   
}
//$this->items_batch='500';
$q.=' order by LastModifiedDate ASC Limit '.$this->items_batch;
if(!empty($meta['page'])){
    $q.=' offset '.(intval($meta['page'])*$this->items_batch);
}

$items=$created=$mod=array();
$query='/services/data/'.$api->api_version.'/query?q='.urlencode($q);
$sales_response=$api->post_sales_arr($query,"get");
///echo $q.'---<hr>'.json_encode($sales_response); //die();
//echo $q.'<hr>'.json_encode($sales_response['records']); // die();
///
if(!empty($sales_response['records'])){
foreach($sales_response['records'] as $v){
    $id=$v['Id'];
    $items[$id]=$v;
} 

if(!empty($meta['price'])){
$ids=array_keys($items);
$path='/services/data/'.$api->api_version.'/query';
 $q= "SELECT Id,UnitPrice,ProductCode,Pricebook2Id,Product2Id from PricebookEntry where Product2Id IN('".implode("','",$ids)."') AND Pricebook2Id='".$meta['price']."' Limit 50";
$path.='?q='.urlencode($q);
$sales_res=$api->post_sales_arr($path,'GET');

if(!empty($sales_res['records'])){
  foreach($sales_res['records'] as $v){
   if(!empty($v['Product2Id']) && isset($items[$v['Product2Id']])){
    $items[$v['Product2Id']]['price']=$v['UnitPrice'];   
   }   
  }  
}
}
}
//echo $q; var_dump($items); die();

$attrs=$this->sync_attrs($info,$meta);

$logs=array(); $last_id=''; $after=$after_m=0; $n=0; $items_found=$items_done=$items_updated=array();
foreach($items as $k=>$item){

if( $n >= $this->items_batch ){ break; }

$item_id=$item['Id'];
$after=max($after,strtotime($item['LastModifiedDate'])); //createdDate 
$last_id=$item_id;
if( !empty($meta['last_id']) && $item_id <=  intval($meta['last_id']) ){
//continue;      
} 

$log='Salesforce Item #'.$item_id;

$title=$desc=$short=$img=$sku='';
$item_fields=!empty($info['meta']['item_fields']) ? $info['meta']['item_fields'] : array();

if(!empty($item[$meta['item_title']])){
$items_found[$item_id]['title']=$title=$item[$meta['item_title']];   
}
if(!empty($item[$meta['item_short']])){
 $items_found[$item_id]['short_description']=$short=$desc=$item[$meta['item_short']];   
}
if(!empty($item[$meta['item_desc']])){
$desc=$item[$meta['item_desc']];   
}

if(!empty($meta['item_sku'])){
 $sku=$item[$meta['item_sku']]; 
 //$sku=preg_replace("/[^a-zA-Z0-9_-]+/", "", $sku); 
 $items_found[$item_id]['sku']=$sku; 
}

 

$vars=array();
$pro_id=wc_get_product_id_by_sku($sku);

$p_status='';
if($item['IsActive'] === false){
$p_status='draft';    
}else{
$p_status='publish';   
}
if(empty($title) || empty($desc) || empty($sku) ){
    $log.=' Error: Title , SKU or description is empty';
    $logs[$item_id]=$log; $is_error=true;
   continue;
} 

    $p=new WC_Product($pro_id); 
    $p->set_name($title);
    $p->set_description($desc);
  //  $p->set_status('publish');
    $p->set_short_description($short);
    if(empty($pro_id)){
    $log.=' (new product) ';    
    try{
    $p->set_sku($sku);
    }catch(Exception $e){
    $log.=' Error: '.$e->getMessage();
    $logs[$item_id]=$log; $is_error=true;
     continue; 
} }

if(!empty($p_status)){
$p->set_status($p_status);      
}
   
/***************************sync categories*********************/    
if(!empty($item['Family'])){
     $cat_names=array($item['Family']);
    $cat_ids=$this->sync_cats($cat_names , $meta,$api );
    if(!empty($cat_ids)){
 $p->set_category_ids($cat_ids);       
    }
} 

/***************************add attributes*********************/
 foreach($item as $id=>$field){ 
  if(isset($attrs[$id])){ 
   $temp=array();
  $field_arr=explode(';',$field);

       foreach($field_arr as $v){
       $temp[]=$v;
       }
  
$attrs[$id]['term_names']=$temp; 
      }   
 }

//we have term ids and term slugs , assign attrs to product
if(!empty($attrs)){
   $n=0;  $attrs_arr=array();
foreach($attrs as $id=>$v ){
      
$slug='pa_'.$v['slug'];
$term_names=!empty($v['term_names']) ? $v['term_names'] : array(); 
     

if(!empty($term_names)){
    $v['attr_id']=(int)$v['attr_id'];
      $attribute = new WC_Product_Attribute();
      $attribute->set_id( $v['attr_id'] );
      $attribute->set_name( $slug );
      $attribute->set_options( $term_names );
      $attribute->set_position( $n );
      $attribute->set_visible( 1 );
      $attribute->set_variation( !empty($v['is_var']) ? 1 : 0 );
      $n++;
      $attrs_arr[] = $attribute;
}
}
if(!empty($attrs_arr)){
   $p->set_attributes($attrs_arr);
} }
//var_dump($attrs,$vars,$attrs_arr); die();
/************** save product ********************************/
//sync stock
if(!empty($meta['sync_qty'])){
 $p->set_manage_stock(1);   
}
if(!empty($meta['back_order'])){
 $p->set_backorders($meta['back_order']);   
}
/*$qty=$this->calc_qty($item,$meta);
$items_found[$item_id]['qty']=$qty;
$p->set_stock_quantity($qty);
*/

$price= !empty($item['price']) ? $item['price'] : 0;
$p->set_regular_price($price);
$items_found[$item_id]['price']=$price;
if(!empty($meta['item_img']) && !empty($item[$meta['item_img']])){
 $img=$item[$meta['item_img']];   
}

$upload='';
if(!empty($img)){
$upload= $this->upload_image_from_url($img); 
}else{
//product image

//if(!empty($item->storeDisplayImage->internalId)){ $file_id=$item->storeDisplayImage->internalId; }
//else if(!empty($item->storeDisplayThumbnail->internalId)){ $file_id=$item->storeDisplayThumbnail->internalId; }   
}
if(!empty($upload['url'])){
$media_id=$this->set_uploaded_image_as_attachment($upload);
if(!empty($media_id)){ $p->set_image_id($media_id); }
$items_found[$item_id]['image']=$upload['url'];
}

$p->save();
if(empty($pro_id)){
$items_done[]=$p->get_id();
}else{
$items_updated[]=$p->get_id();
}
$id=$p->get_id();
update_post_meta($id,'cfx_sales_id',$item_id);
$log.=' to WooCommerce #'.$id;    
$n++;
$logs[$item_id]=$log;

}
$time=current_time( 'timestamp' ,1 );
$save=false;
if(!empty($after)){
    
    if(!empty($meta['after']) && $meta['after'] == $after){
        if(empty($meta['page'])){ $meta['page']=0; }
     $meta['page']= $meta['page']+1;  
    }else{
     $meta['page']=0;    
    }
    
    $meta['after']=$after;
    $meta['last_id']=$last_id;
    $save=true;
}
if($save){
update_option('vxcf_sales_cron',$meta);     
}
   
if(!empty($logs)){  
$cron['item']=array('logs'=>$logs,'label'=>'Sync Items','time'=>$time);
}

$cron['last']=array('label'=>'Last Checked','time'=>$time);
//var_dump($cron); die();
$cron=$this->set_item_cron($meta,$cron);
update_option('vxcf_sales_cron_status',$cron);
if( !empty($items_found) ){
 $log_arr['object']='items_cron';   
 $log_arr['meta']= count($items_found).' items found and '.count($items_done).' products created and '.count($items_updated).' products updated';
 $temp_found=$temp_log=array();
 foreach($items_found as $it=>$item_lg){
     $temp_found['Item #'.$it]=$item_lg;
 } 
 foreach($logs as $it=>$item_lg){
     $temp_log['Item #'.$it]=$item_lg;
 }
 $log_arr['data']=json_encode($temp_found);   
 $log_arr['extra']=json_encode($temp_log);   
}

}
if(!empty($log_arr['object'])){
global $wpdb;
$table=$wpdb->prefix ."vxc_sales_log";
$wpdb->insert($table,$log_arr);
//var_dump($wpdb->insert_id,$wpdb->last_error);
}

if($is_error && !empty($logs) && !empty($info['data']['error_email']) ){
$email_info=array("msg"=>implode('<br/>',$logs),"title"=>__("Woocommerce Salesforce CRON Error - CRM Perks",'woocommerce-sales-crm'));
    $email_body=vxc_sales::format_user_info($email_info,true); 
  $error_emails=explode(",",$info['data']['error_email']); 
  $headers = array('Content-Type: text/html; charset=UTF-8');
  foreach($error_emails as $email)   
  wp_mail(trim($email),$email_info['title'], $email_body,$headers);
     
}
//die('-----cron end-----');
}

public function maybe_validate_stock($passed, $product_id, $quantity, $variation_id = '', $variations= ''){
$net_id=get_post_meta($product_id,'cfx_sales_id',true);
          $p=false;
      try{
      $p=new WC_Product($product_id); 
      }catch(Exception $e){}
   if(!$p){ return $passed; } 
     
if(!empty($net_id)){
   $meta=get_option('vxcf_sales_cron',array()); 
   if(!empty($meta['account']) && !empty($meta['stock_qty']) ){
   $info=$this->get_info($meta['account']); 
$api=$this->get_api($info);
    $item=$api->get_rec( $net_id,'inventoryItem');
    $this->sync_item($item,$meta,$product_id);
} }
    return $passed; 
}

public function sync_item( $item,$meta,$post_id,$status='' ){
    $p=false; $log='';
try{    
$p=new WC_Product( $post_id );
$save=false;
      if($p){
    if(!empty($item->internalId)){      
   $log='Netsuite #'.$item->internalId.' WooCoommerce #'.$post_id;
      $qty_db=$p->get_stock_quantity();    
      $qty=$this->calc_qty($item,$meta);
       if(!empty($status)){
           $save=true;
     $p->set_status($status); 
      $log.=' Status='.$status;   
   } 
      if($qty_db != $qty){
          $save=true;
      $p->set_stock_quantity($qty);
      $log.=' Updating Qty='.$qty;
 
    
if(empty($qty) && !empty($meta['stock'])){
 $p->set_status($meta['stock']); 
 $log.=' Updating Status='.$meta['stock'];  
}
}
$price_db=$p->get_regular_price();
$price=$this->calc_price($item,$meta);
if($price_db != $price){
$p->set_regular_price($price);  
$log.=' Updating price='.$price;
$save=true;   
} }else{
 $p->set_status('trash');  
 $save=true;  
 $log=' deleting WooCommerce #'.$post_id;
}
if($save){
$p->save();
}
}
}catch(Exception $e){}
return $log; 
}
public function sync_attrs($info, $meta){
    $arr=array(); global $wpdb;
    $re=array();
    $attrs=array();
 $sel_attrs=array();
 if(!empty($meta['attr'])){ $sel_attrs=$meta['attr']; }   
   
if( !empty($sel_attrs) && !empty($info['meta']['product_fields'])){
 foreach($info['meta']['product_fields'] as $v){
     if( in_array($v['name'],$sel_attrs) ){
    $type=!empty($v['options']) ? 'select' : 'text';     
$attrs[]=array('label'=>$v['label'],'type'=>$type,'id'=>$v['name']);         
     }
 }   
}

 if( !empty($meta['vars']) && !empty($info['meta']['attr_vars'])){
 foreach($info['meta']['attr_vars'] as $v){
     if(in_array($v['id'],$meta['vars'])){
    $type=!empty($v['options']) ? 'select' : 'text';     
$temp=array('label'=>$v['label'],'type'=>$type,'id'=>$v['id'].'_op','is_var'=>'1','name'=>$v['name']); 
if(!empty($v['options'])){ $temp['options']=$v['options'];  }
$attrs[]=$temp;        
     }
 }   
} 

    if(!empty($attrs)){
     foreach($attrs as $k=>$v){
        $slug=esc_sql(wc_sanitize_taxonomy_name($v['label']));
        if(!empty($slug)){ $arr[$slug]=$v; } 
     } 
  if(!empty($arr)){     
$sql="SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name in('".implode("','",array_keys($arr))."')";
$res=$wpdb->get_results($sql,ARRAY_A);
$db_attrs=array();
foreach($res as $v){
$db_attrs[$v['attribute_name']]=$v['attribute_id'];    
}
$is_new=false;
foreach($arr as $k=>$v){
   $attr_id='';
    if(isset($db_attrs[$k])){
    $attr_id=$db_attrs[$k]; 
    }else{ //insert
    $is_new=true;
    $wpdb->insert(
                $wpdb->prefix . 'woocommerce_attribute_taxonomies',
                array(
                    'attribute_label'   => $v['label'],
                    'attribute_name'    => $k,
                    'attribute_type'    => $v['type'],
                    'attribute_orderby' => 'menu_order',
                    'attribute_public'  => 0,
                ),
                array( '%s', '%s', '%s', '%s', '%d' )
            ); 
    $attr_id=$wpdb->insert_id;            
    }
 if(!empty($attr_id)){   
$v['attr_id']=$attr_id;
$v['slug']=$k;
$re[$v['id']]=$v; 
 }
} 
if($is_new){
//do_action( 'woocommerce_attribute_added', $wpdb->insert_id, $attribute );
//wp_schedule_single_event( time(), 'woocommerce_flush_rewrite_rules' );
delete_transient( 'wc_attribute_taxonomies' );
} 
  }
    }
    
return $re;    
}
public function sync_cats( $cats , $meta, $api ){
    $cat_ids=array();
if(!empty($cats)){
foreach($cats as $cat_id=>$cat_name) {
$wp_term='';
$cid = get_term_by('name',$cat_name, 'product_cat' );
if(!$cid && !empty($meta['sync_cats']) ){
        $cid = wp_insert_term(
        $cat_name, // the term 
        'product_cat', // the taxonomy
        array(
          //  'description'=> $data['description'],
           // 'slug' => $data['slug'],
         //   'parent' => $data['parent']
         
        ) );
if(is_array($cid) && !empty($cid['term_id'])){
 $wp_term=$cid['term_id'];    
}
}else  if(!empty($cid->term_id)){
     $wp_term=$cid->term_id;   
}
if(!empty($wp_term)){
$cat_ids[]=$wp_term;
}    
} }
return $cat_ids;
}

public function output_sections($sections) {
$sections['cron']=__('Salesforce to WooCommerce','woocommerce-sales-crm');
return $sections;
}

public function section_html($page_added){

    global $current_section;

if(!$page_added && $current_section == 'cron' && current_user_can('vxc_sales_read_settings') ){
$page_added=true;
$meta=get_option('vxcf_sales_cron',array());
$cron=get_option('vxcf_sales_cron_status',array());

if(!is_array($meta)){ $meta=array(); }
$time=current_time( 'timestamp' );

if(!empty($_POST['crm'])){
 $meta=$this->post('crm');
$after=strtotime($meta['after']);
 if(!empty($after)){
 $offset= get_option('gmt_offset');
 $meta['after']=$after-($offset*3600);
}
//$cron=array();
$cron=$this->set_item_cron($meta,$cron);
// $meta=array_merge($meta,$post);
 update_option('vxcf_sales_cron',$meta);
  update_option('vxcf_sales_cron_status',$cron);
// $meta=get_option('vxcf_sales_cron',array());
// $cron=get_option('vxcf_sales_cron_status',array());
}

 // var_dump($cron);    
      global $wpdb;
 $table=$wpdb->prefix . 'vxc_sales_accounts';

$sql='SELECT * FROM '.$table.' where  status !="9" limit 100';
$accounts = $wpdb->get_results( $sql ,ARRAY_A );
wp_enqueue_script('vxc-select2');
wp_enqueue_style('vxc-select2' );
  wp_enqueue_script('jquery-ui-datepicker' );
  wp_enqueue_script('jquery-ui-slider' );
  wp_enqueue_style('vxc-ui', self::$base_url.'css/jquery-ui.min.css');
  wp_enqueue_style('vxc-ui-time', self::$base_url.'css/jquery-ui-timepicker-addon.css');
wp_enqueue_script('vxc-ui-time', self::$base_url.'js/jquery-ui-timepicker-addon.js');

?>
<table class="form-table">
<tr>
<th><label for="vx_ac"><?php _e('Salesforce Account','woocommerce-sales-crm'); ?></label></th>
<td>  <select id="vxc_account" name="crm[account]" style="width: 100%">
<option value=""><?php _e('Select Account','woocommerce-sales-crm'); ?></option>
  <?php
  foreach($accounts as $v){
   $sel="";
   if(!empty($meta['account']) && $meta['account'] == $v['id']){
       $sel='selected="selected"';
   }
  echo '<option value="'.$v['id'].'" '.$sel.'>'.$v['name'].'</option>';     
  }   
  ?>
  </select></td>
</tr>
</table>
<div id="vx_sync_load" style="display: none; text-align: center; padding-top: 20px;"><i class="fa fa-circle-o-notch fa-spin"></i> Loading ...</div>
<div id="vx_sync_settings"><?php $this->cron_settings($meta); ?></div>
<script type="text/javascript">
jQuery(document).ready(function($){
var vx_crm_ajax='<?php echo wp_create_nonce("vx_crm_ajax") ?>';
 
  
$('#vxc_account').change(function(){
       var load=$('#vx_sync_load');
       var div=$('#vx_sync_settings');
       div.hide();
       load.show();
$.post(ajaxurl,{action:'vxc_sales_get_sync_settings',vx_crm_ajax:vx_crm_ajax,account:$(this).val()},function(res){ div.html(res); div.show(); load.hide(); add_sel2();    });
   });

$(document).on("click",".vx_refresh_data",function(e){
  e.preventDefault();  
  var btn=$(this);
  var action=$(this).data('id');
  var account=$("#vxc_account").val();
  button_state_vx("ajax",btn);
  $.post(ajaxurl,{action:'refresh_data_<?php echo $this->id ?>',vx_crm_ajax:vx_crm_ajax,vx_action:action,account:account},function(res){
  var re=$.parseJSON(res);
  button_state_vx("ok",btn);  
  if(re.status){
 if(re.status == "ok"){
  $.each(re.data,function(k,v){
   if($("#"+k).length){
   $("#"+k).html(v);    
   }else if($("."+k).length){
   $("."+k).html(v);    
   }   
  })   
 }else{
  if(re.error && re.error!=""){
      alert(re.error);
  }   
 }
  }   

  });   
});
   add_sel2();  
function add_sel2(){
jQuery('.vx_item_fields').select2({ placeholder: '<?php _e('Select Item Field','woocommerce-sales-crm') ?>'});
jQuery('#crm_sel_attr').select2({ placeholder: '<?php _e('Select Attributes','woocommerce-sales-crm') ?>'});
jQuery('#crm_sel_book').select2({ placeholder: '<?php _e('Select Price Book','woocommerce-sales-crm') ?>'});
jQuery('#crm_sel_cats').select2({ placeholder: '<?php _e('Select categories','woocommerce-sales-crm') ?>'});

$('.vxc_date').datetimepicker({
timeFormat: 'HH:mm:ss',
dateFormat: 'dd-M-yy'
});

}
function button_state_vx(state,button){
var ok=button.find('.reg_ok');
var proc=button.find('.reg_proc');
     if(state == "ajax"){
          button.attr({'disabled':'disabled'});
ok.hide();
proc.show();
     }else{
         button.removeAttr('disabled');
   ok.show();
proc.hide();      
     }
}
 var sel=$('#crm_sel_attr');
  if(!sel.find('option').length){
  sel.parents('.vx_tr').find('.vx_refresh_data').trigger('click');
  }   
  var sel=$('#crm_sel_var');
  if(!sel.find('option').length){
  sel.parents('.vx_tr').find('.vx_refresh_data').trigger('click');
  } 
})
</script>
<?php
$next_item = wp_next_scheduled( 'vxc_sales_item_cron' );

 $offset = (int) get_option('gmt_offset');
  $offset=$offset*3600;
  $item_status='Disabled';
  $stock_status='Disabled';
// if(!empty($cron['next'])){
  ?>
  <div class="updated below-h2"><h4><?php _e('Products Sync CRON','woocommerce-sales-crm'); ?></h4>
  <?php
      if(!empty($cron['last'])){
  ?>
  <p><?php echo $cron['last']['label'].' @ '.date('d-M-Y H:i:s',$cron['last']['time']+$offset); ?></p>
  <?php
      }
  if(!empty($next_item)){    
  ?>
  <p>Next Check @ <?php echo date('d-M-Y H:i:s',$offset+$next_item).' and current time is '.date('d-M-Y H:i:s',$time); ?></p>
<?php
$item_status='Active';
  }else if(!empty($cron['next'])){
   $item_status='Working ...';   
  }
  ?>
  <p><b>Cron Status: <?php echo $item_status ?></b></p>
  <?php
      if(!empty($cron['item']) && !empty($cron['item']['logs']) ){ ?>  
      <p><?php echo $cron['item']['label'].' @ '.date('d-M-Y H:i:s',$cron['item']['time']+$offset); ?></p>
      <p><?php echo implode('<hr>',$cron['item']['logs']); ?></p>
      <?php }
  ?>
  </div>
  <?php   
 //} 
   
    }
return $page_added;
}
public function get_settings_ajax(){
check_ajax_referer("vx_crm_ajax","vx_crm_ajax"); 
  if(!current_user_can('vxc_sales_edit_settings')){ 
   die('-1');  
 }
 $account=!empty($_REQUEST['account']) ? (int)$_REQUEST['account'] : '';
 
 $meta=get_option('vxcf_sales_cron',array());
 $meta['account']=$account;
 $this->cron_settings($meta);
 die();
}
public function cron_settings($meta){

if(empty($meta['account'])){ return ''; }
$info=$this->get_info($meta['account']); 
$api=$this->get_api($info);
$fields=$meta_info=array();
if(!empty($info['meta'])){
  $meta_info=$info['meta'];  
}
if(!empty($meta_info['product_fields'])){
$fields=$meta_info['product_fields'];    
}
if(!empty($_POST['refresh_fields']) || empty($fields)){
$fields=$api->get_crm_fields('Product2');
if(is_array($fields) && !empty($fields)){
$meta_info['product_fields']=$fields;
if(isset($info['id'])){
$this->update_info( array("meta"=>$meta_info) , $info['id'] );
} }else if( !empty($fields) && is_string($fields)){
  ?><div class="error"><p>Error: <?php echo $fields ?></p></div><?php   
} }

if(!is_array($fields)){  $fields=array(); }
$after='';
if(!empty($meta['after'])){
$offset=get_option('gmt_offset');
$after=date('d-M-Y H:i:s',$meta['after']+($offset*3600)); 
}
?>
<table class="form-table">
<tr>
<th><label for="vx_product"><?php _e('Sync Products','woocommerce-sales-crm');  ?></label></th>
<td><label for="vx_product"><input type="checkbox" id="vx_product" name="crm[sync_items]" value="yes" <?php if(!empty($meta['sync_items'])){echo 'checked="checked"';} ?> > <?php _e('Add Salesforce Products to WooCommerce','woocommerce-sales-crm');  ?></label></td>
</tr>
<tr>
<th><label><?php _e('Product Attributes','woocommerce-sales-crm'); ?></label></th>
<td>
  <div class="vx_tr" >
  <div class="vx_td">
<select class="vx_sel2" id="crm_sel_attr" name="crm[attr][]" multiple="multiple" style="width: 99%">
  <?php
  if(!empty($fields)){
  foreach($fields as $k=>$v){
   $sel="";  $v=$v['label'];
   if(isset($meta['attr']) && is_array($meta['attr']) &&  in_array($k,$meta['attr'])){
       $sel='selected="selected"';
   }
  echo "<option value='$k' $sel>$v</option>";     
  }   }
  ?>
  </select>
 <div class="howto"><?php _e('Product specs displayed on WooCommerce Product page. For example: Engine Type=EngineA ','woocommerce-sales-crm') ?></div> 
  </div>
  <div class="vx_td2">
  <button class="button vx_refresh_data" data-id="refresh_attr">
  <span class="reg_ok"><i class="fa fa-refresh"></i> <?php _e('Refresh','woocommerce-sales-crm') ?></span>
  <span class="reg_proc" style="display: none;"><i class="fa fa-circle-o-notch fa-spin"></i> <?php _e('Loading ...','woocommerce-sales-crm') ?></span>
  </button>
  
  </div>
  </div>

  </td>
 </tr>
<tr>
<th><label for="vx_item_cron"><?php _e('Products Cron','woocommerce-sales-crm'); ?></label></th>
<td>  <select id="vx_item_cron" name="crm[item_cron]" style="width: 100%">
  <?php
  $cron=!empty($meta['item_cron']) ? $meta['item_cron'] : '3600';
  $cache=array("60"=>"One Minute (for testing only)","300"=>"5 Minutes","600"=>"10 Minutes","1200"=>"20 Minutes","1800"=>"30 Minutes","3600"=>"One Hour","10800"=>"Three Hour","21600"=>"Six Hours","43200"=>"12 Hours","86400"=>"One Day","172800"=>"2 Days","259200"=>"3 Days","432000"=>"5 Days","604800"=>"7 Days","18144000"=>"1 Month");

  foreach($cache as $secs=>$label){
   $sel="";
   if($cron == $secs){
       $sel='selected="selected"';
   }
  echo "<option value='$secs' $sel>$label</option>";     
  }   
  ?>
  </select></td>
</tr>

<tr>
<th><label><?php _e('Sync Peoducts Updated after','woocommerce-sales-crm'); ?></label></th>
<td>
<input type="text" name="crm[after]" class="vxc_date" value="<?php echo $after; ?>" autocomplete="off" style="width: 99%">
 <div class="howto"><?php _e('Leave empty to start from 0 ','woocommerce-sales-crm') ?></div> 

  </td>
 </tr>

<tr>
<th><label><?php _e('Categories','woocommerce-sales-crm'); ?></label></th>
<td>

<select class="vx_sel2" id="crm_sel_cats"  name="crm[cats][]" multiple="multiple" style="width: 99%">
<option value=""><?php _e('Select Categories','woocommerce-sales-crm'); ?></option>
  <?php
  if(!empty($fields['Family']['options'])){
  foreach($fields['Family']['options'] as $k=>$v){
   $sel="";  $k=$v['value']; $v=$v['label'];
   if(isset($meta['cats']) && is_array($meta['cats'])  &&  in_array($k,$meta['cats']) ){
       $sel='selected="selected"';
   }
  echo "<option value='$k' $sel>$v</option>";     
  }   }
  ?>
  </select>
 <div class="howto"><?php _e('Products belonging to selected Categories will be pulled from Salesforce','woocommerce-sales-crm') ?></div> 

  </td>
 </tr>
<tr>
<th><label><?php _e('Allow BackOrders','woocommerce-sales-crm'); ?></label></th>
<td>

<select  name="crm[back_order]" style="width: 99%">
  <?php
  $opst=array('no'=>'Do not allow','notify'=>'Allow , but notify customer','yes'=>'Yes, Allow');

  foreach($opst as $k=>$v){
   $sel="";
   if(isset($meta['back_order']) && $meta['back_order'] == $k){
       $sel='selected="selected"';
   }
  echo "<option value='$k' $sel>$v</option>";     
  }   
  ?>
  </select>


  </td>
  

  
<tr>
<th><label for="vx_cats"><?php _e('Sync Categories','woocommerce-sales-crm');  ?></label></th>
<td><label for="vx_cats"><input type="checkbox" id="vx_cats" name="crm[sync_cats]" value="yes" <?php if(!empty($meta['sync_cats'])){echo 'checked="checked"';} ?> > <?php _e('Add Item Categories from Salesforce to WooCommerce','woocommerce-sales-crm');  ?></label></td>
</tr>

</table>

<h3><?php _e('Woocommerce Product fields mapping ','woocommerce-sales-crm');  ?></h3>
<table class="form-table">
<tr>
<th><label><?php _e('WooCommerce Product fields','woocommerce-sales-crm'); ?></label></th>
<td>

  <button class="button" name="refresh_fields" value="1" data-id="refresh_item_fields">
  <span class="reg_ok"><i class="fa fa-refresh"></i> <?php _e('Refresh Salesforce Product fields','woocommerce-sales-crm') ?></span>
  <span class="reg_proc" style="display: none;"><i class="fa fa-circle-o-notch fa-spin"></i> <?php _e('Loading ...','woocommerce-sales-crm') ?></span>
  </button>


  </td>
 
 <tr>
<th><label><?php _e('Product Title','woocommerce-sales-crm'); ?></label></th>
<td>
<select  name="crm[item_title]" class="vx_item_fields" style="width: 99%">
  <?php 

  foreach($fields as $k=>$v){
   $sel="";
   if(isset($meta['item_title']) && $meta['item_title'] == $k){
       $sel='selected="selected"';
   }
  echo "<option value='$k' $sel>".$v['label']."</option>";     
  }     
  ?>
  </select>
  </td>
</tr>
 <tr>
<th><label><?php _e('Product Description','woocommerce-sales-crm'); ?></label></th>
<td>
<select  name="crm[item_desc]" class="vx_item_fields" style="width: 99%">
  <?php

  foreach($fields as $k=>$v){
   $sel="";
   if(isset($meta['item_desc']) && $meta['item_desc'] == $k){
       $sel='selected="selected"';
   }
  echo "<option value='$k' $sel>".$v['label']."</option>";     
  }     
  ?>
  </select>
  </td>
</tr>

 <tr>
<th><label><?php _e('Short Description','woocommerce-sales-crm'); ?></label></th>
<td>
<select  name="crm[item_short]" class="vx_item_fields" style="width: 99%">
  <?php

  foreach($fields as $k=>$v){
   $sel="";
   if(isset($meta['item_short']) && $meta['item_short'] == $k){
       $sel='selected="selected"';
   }
  echo "<option value='$k' $sel>".$v['label']."</option>";     
  }     
  ?>
  </select>
  </td>
</tr>
 <tr>
<th><label><?php _e('Product SKU','woocommerce-sales-crm'); ?></label></th>
<td>
<select  name="crm[item_sku]" class="vx_item_fields" style="width: 99%">
  <?php
  foreach($fields as $k=>$v){
   $sel="";
   if(isset($meta['item_sku']) && $meta['item_sku'] == $k){
       $sel='selected="selected"';
   }
  echo "<option value='$k' $sel>".$v['label']."</option>";     
  }     
  ?>
  </select>
  </td>
</tr>
 <tr>
<th><label><?php _e('Product Image','woocommerce-sales-crm'); ?></label></th>
<td>
<select  name="crm[item_img]" class="vx_item_fields" style="width: 99%">
  <?php
  foreach($fields as $k=>$v){
   $sel="";
   if(isset($meta['item_img']) && $meta['item_img'] == $k){
       $sel='selected="selected"';
   }
  echo "<option value='$k' $sel>".$v['label']."</option>";     
  }    
  ?>
  </select>
  </td>
</tr>

 <tr>
<th><label><?php _e('Product Price','woocommerce-sales-crm'); ?></label></th>
<td>
  <div class="vx_tr" >
  <div class="vx_td">
<select class="vx_sel2" id="crm_sel_book" name="crm[price]"  style="width: 99%">
<?php
if(!empty($meta_info['price_books'])){
  foreach($meta_info['price_books'] as $k=>$v){
   $sel="";
   if(isset($meta['price']) && $k == $meta['price']){
       $sel='selected="selected"';
   }
  echo "<option value='$k' $sel>$v</option>";     
  }     }
  ?>
  </select>
  </div>
  <div class="vx_td2">
  <button class="button vx_refresh_data" data-id="refresh_books">
  <span class="reg_ok"><i class="fa fa-refresh"></i> <?php _e('Refresh','woocommerce-sales-crm') ?></span>
  <span class="reg_proc" style="display: none;"><i class="fa fa-circle-o-notch fa-spin"></i> <?php _e('Loading ...','woocommerce-sales-crm') ?></span>
  </button>
  
  </div>
  </div>
  </td>
</tr>
</table>

<p class="submit_vx">
  <button type="submit" value="save" class="button-primary" title="<?php _e('Save Changes','woocommerce-sales-crm'); ?>" name="save"><?php _e('Save Changes','woocommerce-sales-crm'); ?></button>
</p>    
    <?php

}
protected function upload_image_wp( $img ) {
    $upload=array();
    if(!empty($img->name) && !empty($img->content) ){
       $upload = wp_upload_bits( $img->name, '', $img->content ); 
    }
 return $upload;   
}
protected function upload_image_from_url( $image_url ) {
        $file_name = basename( current( explode( '?', $image_url ) ) );
        $parsed_url = @parse_url( $image_url );

        // Check parsed URL.
if ( ! $parsed_url || ! is_array( $parsed_url ) ) {
return 'invalid url';
}

        // Ensure url is valid.
        $image_url = str_replace( ' ', '%20', $image_url );
        // Get the file.
        $response = wp_safe_remote_get( $image_url, array(
            'timeout' => 60,
        ) );

        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
return 'error while dowloading image';
        } 

            $headers = wp_remote_retrieve_headers( $response );
if ( isset( $headers['content-disposition'] ) && strstr( $headers['content-disposition'], 'filename=' ) ) {
                $disposition = end( explode( 'filename=', $headers['content-disposition'] ) );
                $disposition = sanitize_file_name( $disposition );
                $file_name   = $disposition;
            } elseif ( isset( $headers['content-type'] ) && strstr( $headers['content-type'], 'image/' ) ) {
                $file_name = 'image.' . str_replace( 'image/', '', $headers['content-type'] );
            }
            unset( $headers );

        // Upload the file.
        $upload = wp_upload_bits( $file_name, '', wp_remote_retrieve_body( $response ) );

        if ( $upload['error'] ) {
return 'error while saving image';
}

        // Get filesize.
        $filesize = filesize( $upload['file'] );

        if ( 0 == $filesize ) {
            @unlink( $upload['file'] );
            unset( $upload );
return;
        }

        unset( $response );

        return $upload;
    }
protected function set_uploaded_image_as_attachment( $upload, $id = 0 ) {
        $info    = wp_check_filetype( $upload['file'] );
        $title   = '';
        $content = '';
include_once( ABSPATH . 'wp-admin/includes/image.php' );

        if ( $image_meta = @wp_read_image_metadata( $upload['file'] ) ) {
            if ( trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) ) {
                $title = wc_clean( $image_meta['title'] );
            }
            if ( trim( $image_meta['caption'] ) ) {
                $content = wc_clean( $image_meta['caption'] );
            }
        }

        $attachment = array(
            'post_mime_type' => $info['type'],
            'guid'           => $upload['url'],
            'post_parent'    => $id,
            'post_title'     => $title,
            'post_content'   => $content,
        );

        $attachment_id = wp_insert_attachment( $attachment, $upload['file'], $id );
        if ( ! is_wp_error( $attachment_id ) ) {
            wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
        }

        return $attachment_id;
}
}
new vxcf_sales_cron();
}
