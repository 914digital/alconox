<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Handle admin orders interface + status transitions
 */
class WC_Gateway_EBizCharge_Admin
{
    /**
     * Constructor
     */
    public function __construct()
    {
        global $post, $wpdb;

        add_action('add_meta_boxes', array($this, 'meta_box'));
        add_action('wp_ajax_ebiz_order_action', array($this, 'order_actions'));
        add_action('woocommerce_order_status_completed', array($this, 'capture_on_completed'));

        add_action('admin_footer-edit.php', array(&$this, 'capture_bulk_admin_footer'));
        add_action('load-edit.php', array(&$this, 'capture_bulk_action'));
        add_action('admin_notices', array(&$this, 'capture_bulk_admin_notices'));
        // for admin css
        add_action('admin_enqueue_scripts', array(&$this, 'add_ebizcharge_css'));

        if (!class_exists('Backwards_Compatible_Order')) {
            include_once 'class-wc-gateway-ebizcharge-migration-helper.php';
        }
    }

    /**
     * Include admin side CSS
     */
    function add_ebizcharge_css()
    {
        wp_enqueue_style('admin-styles', PLUGIN_DIR . 'assets/css/ebizcharge.css', '', 1.0);
    }

    /**
     * Add 'Capture' to bulk actions lists.
     */
    public function capture_bulk_admin_footer()
    {
        global $post_type;

        if ($post_type == 'shop_order') {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('<option>').val('capturePayment').text('<?php _e( 'Capture ' )?>').appendTo("select[name='action']");
                    jQuery('<option>').val('capturePayment').text('<?php _e( 'Capture ' )?>').appendTo("select[name='action2']");
                });
            </script>
            <?php
        }
    }

    /**
     * Process bulk capture.
     */
    public function capture_bulk_action()
    {
        global $typenow;
        $post_type = $typenow;

        if ($post_type == 'shop_order') {
            // get the action
            $wp_list_table = _get_list_table('WP_Posts_List_Table');
            $action = $wp_list_table->current_action();

            $allowed_actions = array("capturePayment");
            if (!in_array($action, $allowed_actions)) {
                return;
            }

            // security check
            check_admin_referer('bulk-posts');

            // make sure ids are submitted
            if (isset($_REQUEST['post'])) {
                $post_ids = array_map('intval', $_REQUEST['post']);
            }

            if (empty($post_ids)) {
                return;
            }

            // this is based on wp-admin/edit.php
            $sendback = remove_query_arg(array('exported', 'untrashed', 'deleted', 'ids'), wp_get_referer());

            if (!$sendback) {
                $sendback = admin_url("edit.php?post_type=$post_type");
            }

            $pagenum = $wp_list_table->get_pagenum();
            $sendback = add_query_arg('paged', $pagenum, $sendback);

            switch ($action) {
                case 'capturePayment':
                    $captured = 0;
                    foreach ($post_ids as $post_id) {
                        if (!$this->perform_capture_payment($post_id)) {
                            wp_die(__('Error capturing payment.'));
                        }

                        $captured++;
                    }

                    $sendback = add_query_arg(array(
                        'captured' => $captured,
                        'ids' => join(',', $post_ids)
                    ), $sendback);
                    break;
                default:
                    return;
            }

            $sendback = remove_query_arg(array(
                'action',
                'action2',
                'tags_input',
                'post_author',
                'comment_status',
                'ping_status',
                '_status',
                'post',
                'bulk_edit',
                'post_view'
            ), $sendback);

            wp_redirect($sendback);
            exit();
        }
    }

    /**
     * Display notice after capturing payments.
     */
    public function capture_bulk_admin_notices()
    {
        global $post_type, $pagenow;

        if ($pagenow == 'edit.php' && $post_type == 'shop_order' && isset($_REQUEST['captured']) && ( int )$_REQUEST['captured']) {

            if (( int )$_REQUEST['captured'] == 1) {
                $message = sprintf('Capture successful. %s payment captured.', number_format_i18n($_REQUEST['captured']));
            } else {
                $message = sprintf('Capture successful. %s payments captured.', number_format_i18n($_REQUEST['captured']));
            }

            echo "<div class=\"updated\"><p>{$message}</p></div>";
        }
    }

    public function perform_capture_payment($post_id)
    {
        global $woocommerce;

        $order_id = absint($post_id);
        $order = (WC()->version < '2.7.0') ? new WC_Order($order_id) : new Backwards_Compatible_Order($order_id);
        $transaction_id = get_post_meta($order->get_id(), '_transaction_id', true);
        $amount = get_post_meta($order->get_id(), '_order_total', true);
        $paymentStatus = get_post_meta($order->get_id(), '_payment_status', true);

        if (!empty($transaction_id) && $paymentStatus == "Authorized") {
            if(!$this->captureTransaction($order, $transaction_id, $amount)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Captures the payment when the order status is changed to 'Completed'.
     */
    public function capture_on_completed()
    {
        global $post, $wpdb, $theorder, $woocommerce;

        $completecapture = $GLOBALS['completecapture'];

        if ($completecapture == 'no') {
            return false;
        }

        $order_id = absint($post->ID);
        $order = (WC()->version < '2.7.0') ? new WC_Order($order_id) : new Backwards_Compatible_Order($order_id);
        $transaction_id = get_post_meta($order->get_id(), '_transaction_id', true);
        $amount = get_post_meta($order->get_id(), '_order_total', true);
        $paymentStatus = get_post_meta($order->get_id(), '_payment_status', true);

        if (!empty($transaction_id) && $paymentStatus == "Authorized") {
            $this->captureTransaction($order, $transaction_id, $amount);
        }
    }

    public function captureTransaction($order, $transactionId, $amount)
    {
        $ebiz = new WC_ebizcharge();
        $tran = $ebiz->_initTransaction();
        $tran->refnum = $transactionId;
        $tran->amount = $amount;
        $tran->command = 'capture';

        if ($tran->executeTransaction()) {
            $order->add_order_note(__('EbizCharge payment captured online. Transaction ID: ' . $transactionId, 'woocommerce'));
            update_post_meta($order->get_id(), '_payment_status', 'Captured');
            return true;
        } else {
            $order->add_order_note(__('EbizCharge payment capture failed. Transaction ID: ' . $transactionId . ' Error: ' . $tran->error, 'woocommerce'));
            return false;
        }
    }

    /**
     * Perform order actions for ebizcharge
     */
    public function order_actions()
    {
        global $post, $wpdb, $theorder, $woocommerce;
        check_ajax_referer('ebiz_order_action', 'security');

        $order_id = absint($_POST['order_id']);
        $transaction_id = isset($_POST['ebiz_id']) ? wc_clean($_POST['ebiz_id']) : '';
        $action = sanitize_title($_POST['ebiz_action']);

        $order = (WC()->version < '2.7.0') ? new WC_Order($order_id) : new Backwards_Compatible_Order($order_id);
        $ebiz = new WC_ebizcharge();
        $tran = $ebiz->_initTransaction();
        $tran->refnum = $transaction_id;
        $orderNote = '';

        switch ($action) {
            case 'capture':
                $tran->command = 'capture';
                if ($tran->executeTransaction()) {
                    $order->add_order_note(__('EbizCharge payment captured online. Transaction ID: ' . $transaction_id, 'woocommerce'));
                    update_post_meta($order->get_id(), '_payment_status', 'Captured');
                } else {
                    $order->add_order_note(__('EbizCharge payment capture failed. Transaction ID: ' . $transaction_id . ' Error: ' . $tran->error, 'woocommerce'));
                }
                break;

            case 'void':
                $tran->command = 'creditvoid';
                if ($tran->executeTransaction()) {
                    $order->add_order_note(__('EbizCharge payment Voided online. Transaction ID: ' . $transaction_id, 'woocommerce'));
                    update_post_meta($order->get_id(), '_payment_status', 'Voided');
                } else {
                    $order->add_order_note(__('EbizCharge payment Void failed. Transaction ID: ' . $transaction_id . ' Error: ' . $tran->error, 'woocommerce'));
                }
                break;

            case 'refund':

                $tran->command = 'refund';
                $tran->amount = $_POST['ebiz_refund_amount'];
                $orderNote = $_POST['ebiz_refund_note'];
                if ($tran->executeTransaction()) {
                    $transaction_id = isset($tran->refnum) ? $tran->refnum : $transaction_id;
                    update_post_meta($order->get_id(), '_payment_status', 'Refunded');
                    $order->add_order_note(__('EbizCharge payment Refunded online amount of ' . $tran->amount . '. Transaction ID: ' . $transaction_id . ' Note: ' . $orderNote, 'woocommerce'));
                    // Update order refund amount
                    //update_post_meta( $order->get_id(), '_refund_amount', $tran->amount );
                } else {
                    $order->add_order_note(__('EbizCharge payment Refund failed. Transaction ID: ' . $transaction_id . ' Error: ' . $tran->error, 'woocommerce'));
                }
                break;

            case 'payment':
                $result = $this->process_and_save_payment($_POST);
                break;

        }

        die();
    }

    public function process_and_save_payment($getPost)
    {
        global $WC_ebizcharge;

        $command = $getPost['ebiz_id'];
        $order_id = $getPost['order_id'];
        $order = (WC()->version < '2.7.0') ? new WC_Order($order_id) : new Backwards_Compatible_Order($order_id);
        $user = new WP_User((WC()->version < '2.7.0') ? $order->user_id : $order->get_user_id());

        $ebiz = new WC_ebizcharge();
        $tran = $ebiz->_initTransaction();
        $tran->command = $command;
        $custId = $user->ID;
        $orderid = $order->get_id();

        $tran->invoice = $orderid;
        $tran->orderid = $orderid;
        $tran->ponum = $orderid;
        $tran->ip = $_SERVER['REMOTE_ADDR'];
        $tran->custid = $custId;
        $tran->email = $order->billing_email;
        $tran->tax = $order->get_cart_tax();
        $tran->shipping = $order->get_shipping_total();

        // avs data
        $tran->street = $order->billing_address_1;
        $tran->zip = $order->billing_postcode;
        $tran->description = 'description';
        //items = $order->get_items();

        // line item data
        $order_items = $order->get_items(apply_filters('woocommerce_admin_order_item_types', array(
            'line_item',
            'fee'
        )));
        $totalAmount = 0;

        foreach ($order_items as $item_id => $cart_item) {

            $totalAmount = $cart_item['line_total'] + $totalAmount;

            $_product = (WC()->version < '2.7.0') ? $order->get_product_from_item($cart_item) : $cart_item->get_product();

            $productmeta = new WC_Product($cart_item['product_id']);

            $sku = $productmeta->get_sku();

            if (empty($sku)) {
                $sku = $_product->get_title();
            }

            $prod_description = (!empty($productmeta->get_short_description())) ? $productmeta->get_short_description() : $productmeta->get_description();

            if (empty($prod_description)) {
                $prod_description = $_product->get_title();
            }

            $row_price = (WC()->version < '2.7.0') ? $_product->get_price_excluding_tax(1) : wc_get_price_excluding_tax($_product, array('qty' => 1));
            $tran->addLine($sku, $_product->get_title(), $prod_description, $row_price, $cart_item['qty'], $cart_item['line_tax']);
            // for tokenization
            $tran->addLineItem($sku, $_product->get_title(), $prod_description, $row_price, $cart_item['qty'], $cart_item['line_tax']);
        }

        $tran->amount = $totalAmount;
        $CustNum = get_user_meta((WC()->version < '2.7.0') ? $order->user_id : $order->get_user_id(), 'CustNum', true);
        $customer_vault_ids = get_user_meta((WC()->version < '2.7.0') ? $order->user_id : $order->get_user_id(), 'customer_vault_ids', true);
        $id = $customer_vault_ids[$getPost['ebiz_payment_method']];

        if ($id > 0) {
            $response = $tran->SavedProcess($CustNum, $id);
            if (!empty($tran->refnum)) {
                update_post_meta($order->get_id(), '_transaction_id', $tran->refnum, false);
                $payType = ($command == 'sale') ? 'Captured' : 'Authorized';
                update_post_meta($order->get_id(), '_payment_status', $payType, true);
                // Success
            }
            $order->add_order_note(__('EbizCharge payment ' . $payType . '. Transaction ID: ', 'woocommerce') . $tran->refnum);
        } else {
            $errormsg = ($tran->error) ? $tran->error : '';
            $order->add_order_note(__('Sorry, there was an error: ', 'woocommerce') . $errormsg);
        }

        return true;

    }

    /**
     * meta_box function.
     *
     * @access public
     * @return void
     */
    function meta_box()
    {
        global $post, $wpdb;
        if (!$post) {
            return;
        }
        if (!in_array($post->post_type, array('shop_order'))) {
            return;
        }

        $order_id = absint($post->ID);
        $order = (WC()->version < '2.7.0') ? new WC_Order($order_id) : new Backwards_Compatible_Order($order_id);

        if ($order->payment_method == 'ebizcharge') {
            add_meta_box('woocommerce-ebizcharge-payments', __('EbizCharge Payment Actions', 'woocommerce'),
                array(
                    $this,
                    'authorization_box'
                ), 'shop_order', 'side');
        }

    }

    /**
     * pre_auth_box function.
     *
     * @access public
     * @return void
     */
    function authorization_box()
    {
        global $post, $wpdb, $theorder, $woocommerce;

        if (!$post) {
            return;
        }
        if (!in_array($post->post_type, array('shop_order'))) {
            return;
        }

        $actions = array();
        $order_id = absint($post->ID);

        $order = (WC()->version < '2.7.0') ? new WC_Order($order_id) : new Backwards_Compatible_Order($order_id);

        $transID = get_post_meta($order->get_id(), '_transaction_id', true);
        $amount = get_post_meta($order->get_id(), '_order_total', true);
        $paymentStatus = get_post_meta($order->get_id(), '_payment_status', true);

        $ebiz = new WC_ebizcharge();
        $tran = $ebiz->_initTransaction();

        if (!empty($transID) && !empty($paymentStatus)) {
            $tran->refnum = $transID;
            $tran->amount = $amount;

            echo "<b>Current Status: </b>" . $paymentStatus;
            if ($paymentStatus == "Authorized") {

                $actions['capture'] = array( //capture
                    'id' => $tran->refnum,
                    'button' => __('Capture', 'woocommerce')
                );
                $actions['void'] = array( //void transaction
                    'id' => $tran->refnum,
                    'button' => __('Void', 'woocommerce')
                );

            } elseif ($paymentStatus == "Captured") {
                echo '<a href="#" class="toggle_refund">' . __(' Make a refund?', 'woocommerce') . '</a>';

                // Refund form
                ?>
                <form class="refund_form" style="display:none">
                    <input type="number" step="any" class="ebiz_refund_amount full-width"
                           value="<?php echo $theorder->get_total(); ?>"/>
                    <input type="text" class="ebiz_refund_note full-width"
                           placeholder="<?php _e('Add a note about this refund', 'woocommerce'); ?>"/><br/>
                    <a href="#" class="button" data-action="refund" data-id="<?php echo $tran->refnum; ?>">
                        <?php _e('Refund', 'woocommerce'); ?>
                    </a>
                </form>
                <?php
            }
        } else {
            global $WC_ebizcharge;
            $userid = (WC()->version < '2.7.0') ? $order->user_id : $order->get_user_id();

            if ($WC_ebizcharge->user_has_stored_data($userid)) {

                foreach ($tran->getCustomerPaymentMethods($userid) as $index => $paymentMethod) {
                    if (is_object($paymentMethod)) {
                        ?>
                        <p id="ebiz-payment">
                            <input type="radio" name="ebiz-payment-method"
                                   id="<?php echo $paymentMethod->MethodID; ?>"
                                   value="<?php echo $paymentMethod->MethodID; ?>" <?php if ($index == 0) {
                                echo 'checked';
                            } ?>
                            /> &nbsp;
                            <?php
                            $exp = $paymentMethod->CardExpiration;
                            echo $paymentMethod->CardNumber . ' (' . substr($exp, 2, 2) . '/' . substr($exp, -2) . ')';
                            ?>
                            <br/>
                        </p>
                        <?php
                    }
                }
                ?>
                <a href="#" class="button" data-action="payment"
                   data-id="<?php echo $WC_ebizcharge->salemethod; ?>"> <?php _e($WC_ebizcharge->_paymentType[$WC_ebizcharge->salemethod], 'woocommerce'); ?> </a>
                <?php
            } else {
                echo "<b>Current Status: </b> No payment method is found for this user.";
            }
        }

        if (!empty($actions)) {

            echo '<p class="buttons">';

            foreach ($actions as $action_name => $action) {
                echo '<a href="#" class="button" data-action="' . $action_name . '" data-id="' . $action['id'] . '">' . $action['button'] . '</a> ';
            }

            echo '</p>';

        }

        $js = "
			jQuery('#woocommerce-ebizcharge-payments').on( 'click', 'a.button, a.refresh', function(){
				jQuery('#woocommerce-ebizcharge-payments').block(
				{ message: null, overlayCSS: { background: '#fff url(" . $woocommerce->plugin_url() . "/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

				var data = {
					action: 		'ebiz_order_action',
					security: 		'" . wp_create_nonce("ebiz_order_action") . "',
					order_id: 		'" . $order_id . "',
					ebiz_action: 	jQuery(this).data('action'),
					ebiz_id: 		jQuery(this).data('id'),
					ebiz_refund_amount: jQuery('.ebiz_refund_amount').val(),
					ebiz_refund_note: jQuery('.ebiz_refund_note').val(),
					ebiz_payment_method: jQuery('input:radio[name=ebiz-payment-method]:checked').val(),
				};
				// Ajax action
				jQuery.ajax({
					url: '" . admin_url('admin-ajax.php') . "',
					data: data,
					type: 'POST',
					success: function( result ) {
						location.reload();
					}
				});

				return false;
			});

			jQuery('#woocommerce-ebizcharge-payments').on( 'click', 'a.toggle_refund', function(){
				jQuery('.refund_form').slideToggle();
				return false;
			});

		";

        if (function_exists('wc_enqueue_js')) {
            wc_enqueue_js($js);
        } else {
            $woocommerce->add_inline_js($js);
        }
    }


}

$GLOBALS['wc_ebizcharge_pa_order_handler'] = new WC_Gateway_EBizCharge_Admin();
