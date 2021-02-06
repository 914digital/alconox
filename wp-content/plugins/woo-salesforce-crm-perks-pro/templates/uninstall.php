<?php
if ( ! defined( 'ABSPATH' ) ) {
     exit;
 }                                            
 ?>  <h3><?php _e('Uninstall WooCommerce Salesforce Plugin','woocommerce-salesforce-crm'); ?></h3>
  <?php
  if(isset($_POST[$this->id.'_uninstall'])){ 
  ?>
  <div class="vxc_alert updated  below-h2">
  <h3><?php _e('Success','woocommerce-salesforce-crm'); ?></h3>
  <p><?php _e('WooCommerce Salesforce Plugin has been successfully uninstalled','woocommerce-salesforce-crm'); ?></p>
  <p>
  <a class="button button-hero button-primary" href="plugins.php"><?php _e("Go to Plugins Page",'woocommerce-salesforce-crm'); ?></a>
  </p>
  </div>
  <?php
  }else{
  ?>
  <div class="vxc_alert error below-h2">
  <h3><?php _e("Warning",'woocommerce-salesforce-crm'); ?></h3>
  <p><?php _e('This Operation will delete all Salesforce logs and feeds.','woocommerce-salesforce-crm'); ?></p>
  <p><button class="button button-hero button-secondary" id="vx_uninstall" type="submit" onclick="return confirm('<?php _e("Warning! ALL Salesforce Feeds and Logs will be deleted. This cannot be undone. OK to delete, Cancel to stop.", 'woocommerce-salesforce-crm')?>');" name="<?php echo $this->id ?>_uninstall" title="<?php _e("Uninstall",'woocommerce-salesforce-crm'); ?>" value="yes"><?php _e("Uninstall",'woocommerce-salesforce-crm'); ?></button></p>
  </div>
  <?php
  } ?>