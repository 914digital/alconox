<?php

class WC_Gateway_EBizCharge_Econnect
{
    public $enableEconnect = false;
    public $key;   // Source key
    public $userid;   // User Id
    public $pin;   // Source pin (optional)
    public $software;
    public $user_table;
    public $user_meta_table;
    public $posts_table;
    public $wc_order_item_table;
    public $wc_order_item_meta_table;

    public function __construct()
    {
        global $wpdb;
        $this->user_table = $wpdb->prefix . 'users';
        $this->user_meta_table = $wpdb->prefix . 'usermeta';
        $this->posts_table = $wpdb->prefix . 'posts';
        $this->wc_order_item_table = $wpdb->prefix . 'woocommerce_order_items';
        $this->wc_order_item_meta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
        $this->software = "woocommerce";
    }

    private function _getWsdlUrl()
    {
        return 'https://soap.ebizcharge.net/eBizService.svc?singleWsdl';
    }

    /**
     * Get Security Token array
     *
     * @return array
     */
    function _getSecurityToken()
    {
        return array(
            'SecurityId' => $this->key,
            'UserId' => $this->userid,
            'Password' => $this->pin
        );
    }

    // Set Soap Client Parameters
    public function SoapParams()
    {
        return array(
            'cache_wsdl' => false
        );
    }

    /**
     * Get Response message HTML string
     * @return string
     */
    private function _prepareMessages($messages)
    {
        $messagesHtml = '';
        foreach ($messages as $message) {
            switch ($message['type']) {
                case 'error':
                    $messagesHtml .= '<div class="notice notice-error is-dismissible"> <p>' . $message['message'] . '</p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                    </div>';
                    break;
                case 'notice':
                    $messagesHtml .= '<div class="notice notice-info is-dismissible"> <p>' . $message['message'] . '</p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>

                    </div>';
                    break;
                case 'success':
                    $messagesHtml .= '<div class="notice notice-success is-dismissible"> <p>' . $message['message'] . '</p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                    </div>';
                    break;
                default:
                    break;
            }
        }

        return $messagesHtml;
    }


    private function getCustomerObject($customer)
    {
        $billingAddress = array(
            'FirstName' => $customer['billing_first_name'],
            'LastName' => $customer['billing_last_name'],
            'CompanyName' => isset($customer['billing_company']) ? $customer['billing_company'] : '',
            'Address1' => $customer['billing_address_1'],
            'City' => $customer['billing_city'],
            'State' => $customer['billing_state'],
            'ZipCode' => $customer['billing_postcode'],
            'Country' => $customer['billing_country']
        );

        $shippingAddress = array(
            'FirstName' => $customer['shipping_first_name'],
            'LastName' => $customer['shipping_last_name'],
            'CompanyName' => '',
            'Address1' => $customer['shipping_address_1'],
            'City' => $customer['shipping_city'],
            'State' => $customer['shipping_state'],
            'ZipCode' => $customer['shipping_postcode'],
            'Country' => $customer['shipping_country']
        );

        return array(
            'CustomerId' => $customer['ID'],
            'FirstName' => $customer['first_name'],
            'LastName' => $customer['last_name'],
            'CompanyName' => isset($customer['billing_company']) ? $customer['billing_company'] : '',
            'Phone' => $customer['billing_phone'],
            'Fax' => '',
            'Email' => $customer['user_email'],
            'BillingAddress' => $billingAddress,
            'ShippingAddress' => $shippingAddress
        );

    }

    private function formatApiResponse($table, $responseObj)
    {
        $now = new DateTime(null);
        $now->modify("+10 seconds"); //add 10 seconds to make sure sync last_modified_date is greater than object update date
        $data = array();
        if ($responseObj->Status == "Success") {

            if ($table == $this->user_table) {
                $data['ec_internal_id'] = $responseObj->CustomerInternalId;
            } elseif ($table == $this->posts_table) {
                // product internal id or invoice internal id
                $data['ec_internal_id'] = isset($responseObj->ItemInternalId) ?
                    $responseObj->ItemInternalId :
                    $responseObj->InvoiceInternalId;
            }
        }

        $data['ec_status'] = $responseObj->Status;
        $data['ec_status_code'] = $responseObj->StatusCode;
        $data['ec_error'] = $responseObj->Error;
        $data['ec_error_code'] = $responseObj->ErrorCode;
        $data['ec_last_modified_date'] = $now->format('Y-m-d H:i:s');

        return $data;
    }

    public function updateWPTable($table, $apiResponse, $where)
    {
        global $wpdb;
        $data = $this->formatApiResponse($table, $apiResponse);

        $wpdb->update($table, $data, $where);
    }

    /**
     * Sync Customers to eConnect
     *
     * @return string
     */
    function syncCustomer($id = null)
    {
        $_messages = array();
        $_errorCount = 0;
        $_processedCount = 0;

        $securityToken = $this->_getSecurityToken();

        try {
            $client = new SoapClient($this->_getWsdlUrl(), array('trace' => true, 'exceptions' => true));
            //filter if update is for a single record
            $user_arg['role'] = 'customer';
            if ($id != null) {
                $user_arg['include'] = array($id);
            }

            foreach (get_users($user_arg) as $user) {

                $userMeta = array_map(function ($a) {
                    return $a[0];
                }, get_user_meta($user->ID));

                $customer = array_merge((array)$user->data, $userMeta);

                if (!empty($customer['last_update']) && !empty($customer['ec_last_modified_date']) &&
                    date(strtotime($customer['ec_last_modified_date'])) > $customer['last_update']
                ) {

                    continue; // don't need to process
                }

                try {
                    $customerObj = $this->getCustomerObject($customer);

                    if (empty($customer['ec_internal_id'])) {
                        $parameters = array(
                            'securityToken' => $securityToken,
                            'customer' => $customerObj
                        );

                        $this->log('Add customer with params: ');
                        $this->log($parameters);

                        $addCustomerResponse = $client->AddCustomer($parameters);
                        $obj = $addCustomerResponse->AddCustomerResult;

                        $this->log('Added customer Response: ');

                        $this->log($addCustomerResponse);

                    } else {
                        $parameters = array(
                            'securityToken' => $securityToken,
                            'customer' => $customerObj,
                            'customerId' => $customer['ID'],
                            'customerInternalId' => $customer['ec_internal_id']
                        );
                        $this->log('Update customer with params: ');
                        $this->log($parameters);

                        $updateCustomerResponse = $client->UpdateCustomer($parameters);
                        $obj = $updateCustomerResponse->UpdateCustomerResult;

                        $this->log('Updated customer Response:');
                        $this->log($updateCustomerResponse);
                    }

                    if ($obj->Status == "Error") {
                        if ($_errorCount == 0)
                            $_messages[] = array("type" => 'error', "message" => "Errors have occurred during the sync Customers process, please review the logs. First error occurred is: " . $obj->Error);

                        $_errorCount++;
                    }

                    $whereCondition = array('ID' => (int)$customer['ID']);
                    $this->updateWPTable($this->user_table, $obj, $whereCondition);

                    $this->log("Customer info updated in WP tables.");

                } catch (Exception $e) {
                    $this->log($e->getMessage());

                    if ($_errorCount == 0)
                        $_messages[] = array("type" => 'error', "message" => "Errors have occurred during the sync Customers process, please review the logs. First error occurred is: " . $e->getMessage());

                    $_errorCount++;
                } finally {
                    $_processedCount++;
                }
            }

            array_unshift($_messages, array("type" => 'notice', "message" => sprintf("Sync Customers process has been completed. %s record(s) processed.", $_processedCount)));

            if ($_errorCount == 0)
                $_messages[] = array("type" => 'success', "message" => "Completed the sync Customers process with no errors.");
        } catch (Exception $e) {
            $this->log($e->getMessage());
            $_messages[] = array("type" => 'error', "message" => $e->getMessage());
        }

        $this->log($_messages);

        return $this->_prepareMessages($_messages);
    }

    private function insertWpCustomer($customer)
    {
        global $wpdb;
        $customerBilling = $customer->BillingAddress;
        $customerShipping = $customer->ShippingAddress;

        $wpCustomer = [
            'first_name' => $customer->FirstName,
            'last_name' => $customer->LastName,
            'billing_company' => $customer->CompanyName,
            'billing_phone' => $customer->Phone,
            'user_email' => $customer->Email,
            'role' => 'customer',
            'user_login' => $customer->Email,
            'user_pass' => $customer->FirstName . '*0334',
            // shipping details
            'shipping_first_name' => $customerShipping->FirstName,
            'shipping_last_name' => $customerShipping->LastName,
            'shipping_company' => $customerShipping->CompanyName,
            'shipping_address_1' => $customerShipping->Address1,
            'shipping_city' => $customerShipping->City,
            'shipping_state' => $customerShipping->State,
            'shipping_postcode' => $customerShipping->ZipCode,
            'shipping_country' => $customerShipping->Country,
            // billing details
            'billing_first_name' => $customerBilling->FirstName,
            'billing_last_name' => $customerBilling->LastName,
            'billing_address_1' => $customerBilling->Address1,
            'billing_city' => $customerBilling->City,
            'billing_state' => $customerBilling->State,
            'billing_postcode' => $customerBilling->ZipCode,
            'billing_country' => $customerShipping->Country,
        ];

        $customerId = wp_insert_user($wpCustomer);
        if (is_wp_error($customerId)) {
            $this->log('error: ' . $customer->Email . ' ' . $customerId->get_error_message());

        } else if ($customerId) {
            $now = new DateTime(null);
            $customerApiInfo = [
                'ec_internal_id' => $customer->CustomerInternalId,
                'ec_status' => 'Success',
                'ec_status_code' => 1,
                'ec_last_modified_date' => $now->format('Y-m-d H:i:s'),
            ];

            $wpdb->update($this->user_table, $customerApiInfo, ['ID' => $customerId]);
        }
    }

    private function getWpCustomers()
    {
        $user_arg['role'] = 'customer';
        $customers = [];
        foreach (get_users($user_arg) as $user) {
            $customer = (array)$user->data;
            if (!empty($customer['ec_internal_id'])) {
                $customers[$customer['ec_internal_id']] = $customer['ID'];
            }
        }

        return $customers;
    }

    /**
     * download Customer from eConnect
     * @return string
     */
    public function downloadCustomers()
    {
        $_messages = array();
        $_errorCount = 0;
        $_processedCount = 0;

        $itemsCount = 1;
        $start = 1;
        $limit = 1000;

        try {
            $wpCustomers = $this->getWpCustomers();

            while ($itemsCount > 0 && $limit >= 1000) {

                $customers = $this->searchCustomers($start, $limit);
                $itemsCount = count($customers);

                if ($itemsCount > 0) {
                    foreach ($customers as $customer) {
                        // insert only if customer not already exists
                        if (array_key_exists($customer->CustomerInternalId, $wpCustomers)) {
                            $this->insertWpCustomer($customer);
                        }
                        $_processedCount++;
                    }
                }

                $start += $itemsCount;
                $limit = $start + 1000;
            }

        } catch (Exception $e) {
            $this->log($e->getMessage());

            if ($_errorCount == 0)
                $_messages[] = array("type" => 'error', "message" => "Errors have occurred during the download customers process, please review the logs. First error occurred is: " . $e->getMessage());

            $_errorCount++;
        } finally {
            $_processedCount++;
        }

        if ($_errorCount == 0) {
            $_messages[] = array(
                "type" => 'success',
                "message" => sprintf("Download customers has been completed. %s record(s) processed.", $_processedCount)
            );
        }

        $this->log($_messages);

        return $this->_prepareMessages($_messages);
    }


    /**
     * @param $start
     * @param $limit
     * @return bool|string
     */
    private function searchCustomers($start = 0, $limit = 1000)
    {
        $client = new SoapClient($this->_getWsdlUrl(), $this->SoapParams());
        $ebizCustomers = [];

        try {
            $api = $client->SearchCustomers(
                [
                    'securityToken' => $this->_getSecurityToken(),
                    'customerId' => '',
                    'start' => $start,
                    'limit' => $limit
                ]
            );

            if (isset($api->SearchCustomersResult->Customer)) {
                $ebizCustomers = $api->SearchCustomersResult->Customer;
                $this->log(__METHOD__ . ', Customers found: ' . count($ebizCustomers));
            }

        } catch (SoapFault $ex) {
            $this->log(__METHOD__ . ', Error: ' . $ex->getMessage());
        }

        return $ebizCustomers;
    }

    /**
     * @param int $start
     * @param int $limit
     * @return array|string
     */
    private function searchItems($start = 0, $limit = 1000)
    {
        $client = new SoapClient($this->_getWsdlUrl(), $this->SoapParams());
        $results = array();

        try {
            $searchItems = $client->SearchItems(
                [
                    'securityToken' => $this->_getSecurityToken(),
                    'start' => $start,
                    'limit' => $limit,
                    'filters' => [
                        'FieldName' => 'SoftwareId',
                        'ComparisonOperator' => 'notequal',
                        'FieldValue' => $this->software,
                    ],
                ]
            );

            if (isset($searchItems->SearchItemsResult->ItemDetails)) {
                $results = $searchItems->SearchItemsResult->ItemDetails;

                $this->log(__METHOD__ . ', search item result count ' . count($results));
            }

        } catch (SoapFault $ex) {
            $this->log(__METHOD__ . ', Error: ' . $ex->getMessage());
            return $this->error = 'SoapFault: SearchItems' . $ex->getMessage();
        }

        return $results;
    }

    private function searchWpProducts()
    {
        global $wpdb;
        $productsQuery = "SELECT p.ID, p.ec_internal_id
            FROM " . $this->posts_table . " p
            WHERE p.post_type = 'product' AND p.ec_internal_id <> ''
            ";
        return $wpdb->get_results($productsQuery, OBJECT_K);
    }

    /**
     * @param $item
     * @return int|WP_Error
     */
    private function insertWpProduct($item)
    {
        global $wpdb;

        $postData = [
            'post_title' => $item->Name,
            'post_content' => '',
            'post_content_filtered' => '',
            'post_excerpt' => $item->Description,
            'post_status' => ($item->Active == 1) ? 'published' : 'draft',
            'post_type' => 'product',
            'meta_input' => [
                '_price' => $item->UnitPrice,
                '_stock' => $item->QtyOnHand,
                '_sku' => $item->SKU,
                '_tax_status' => ($item->Taxable == 1) ? 'taxable' : '',
            ]
        ];

        $itemId = wp_insert_post($postData);

        if (is_wp_error($itemId)) {
            $this->log('error: ' . $itemId->get_error_message());

        } else if ($itemId) {

            $now = new DateTime(null);
            $itemApiInfo = [
                'ec_internal_id' => $item->ItemInternalId,
                'ec_status' => 'Success',
                'ec_status_code' => 1,
                'ec_last_modified_date' => $now->format('Y-m-d H:i:s'),
            ];

            $wpdb->update($this->posts_table, $itemApiInfo, ['ID' => $itemId]);

            $this->log('Item inserted successfully. ItemId: ' . $itemId);
        }

        return $itemId;

    }

    /**
     * download Products to eConnect
     * @return string
     */
    public function downloadItems()
    {
        $_messages = array();
        $_errorCount = 0;
        $_processedCount = 0;

        $itemsCount = 1;
        $start = 1;
        $limit = 1000;

        try {
            $wpProducts = $this->searchWpProducts();
            while ($itemsCount > 0 && $limit >= 1000) {

                $items = $this->searchItems($start, $limit);
                $itemsCount = count($items);

                if ($itemsCount > 0) {
                    foreach ($items as $item) {

                        if (array_key_exists($item->ItemInternalId, $wpProducts)) {
                            // ignore it
                        } else {
                            $this->insertWpProduct($item);
                        }

                        $_processedCount++;
                    }
                }

                $start += $itemsCount;
                $limit = $start + 1000;
            }
        } catch (Exception $e) {
            $this->log($e->getMessage());

            if ($_errorCount == 0)
                $_messages[] = array("type" => 'error', "message" => "Errors have occurred during the downlaod products process, please review the logs. First error occurred is: " . $e->getMessage());

            $_errorCount++;
        } finally {
            $_processedCount++;
        }

        if ($_errorCount == 0) {
            $_messages[] = array(
                "type" => 'success',
                "message" => sprintf("Download Products has been completed. %s record(s) processed.", $_processedCount)
            );
        }

        $this->log($_messages);

        return $this->_prepareMessages($_messages);
    }

    /**
     * @param int $start
     * @param int $limit
     * @return array|string
     */
    private function searchOrders($start = 0, $limit = 1000)
    {
        $client = new SoapClient($this->_getWsdlUrl(), $this->SoapParams());
        $results = array();

        try {
            $search = $client->SearchSalesOrders(
                [
                    'securityToken' => $this->_getSecurityToken(),
                    'start' => $start,
                    'limit' => $limit,
                    'includeItems' => true,
                    'filters' => [
                        'FieldName' => 'Software',
                        'ComparisonOperator' => 'notequal',
                        'FieldValue' => $this->software,
                    ],
                ]
            );

            if (isset($search->SearchSalesOrdersResult->SalesOrder)) {
                $results = $search->SearchSalesOrdersResult->SalesOrder;

                $this->log(__METHOD__ . ', search orders result count ' . count($results));
            }

        } catch (SoapFault $ex) {
            $this->log(__METHOD__ . ', Error: ' . $ex->getMessage());
            return $this->error = 'SoapFault: SearchItems' . $ex->getMessage();
        }

        return $results;
    }

    private function getWpOrders()
    {
        global $wpdb;

        $query = "SELECT p.ec_order_internal_id, p.ID
            FROM " . $this->posts_table . " p
            WHERE p.post_type = 'shop_order'
            AND p.post_status IN ('wc-processing', 'wc-completed', 'wc-pending')
            AND (p.ec_order_internal_id <> '' OR p.ec_order_internal_id is not null)
            ";

        return $wpdb->get_results($query, OBJECT_K);
    }

    /**
     * @param $order
     * @return int|WP_Error
     */
    private function insertWpOrder($order)
    {
        global $wpdb;

        $postData = [
            'post_title' => 'Order# - ' . $order->SalesOrderNumber . ' - ' . $order->Date,
            'post_content' => '',
            'post_content_filtered' => '',
            'post_excerpt' => '',
            'post_status' => 'wc-completed',
            'post_type' => 'shop_order',
            'meta_input' => [
                '_customer_user' => is_numeric($order->CustomerId) ? $order->CustomerId : 0, // guest
                'post_date' => $order->Date,
                '_order_currency' => $order->Currency,
                '_order_total' => $order->Amount,
                '_order_tax' => $order->TotalTaxAmount,
                '_created_via' => 'Download Orders'
            ]
        ];

        $orderId = wp_insert_post($postData);

        if (is_wp_error($orderId)) {
            $this->log('error: ' . $orderId->get_error_message());

        } else if ($orderId) {

            $now = new DateTime(null);
            $itemApiInfo = [
                'ec_order_internal_id' => $order->SalesOrderInternalId,
                'ec_order_status' => 'Success',
                'ec_order_last_modified_date' => $now->format('Y-m-d H:i:s'),
            ];
            // wp_insert_post can't insert the custom columns--
            $wpdb->update($this->posts_table, $itemApiInfo, ['ID' => $orderId]);

            $this->insertWpOrderItems($orderId, $order);

            $this->log('Order inserted successfully. ItemId: ' . $orderId);
        }

        return $orderId;

    }

    private function insertWpOrderItems($orderId, $apiOrder)
    {
        global $wpdb;

        $orderItems = isset($apiOrder->Items->Item) ? $apiOrder->Items->Item : array();
        if (count($orderItems) == 1) { // API result is different when items count is 1
            $orderItems = [$orderItems];
        }

        foreach ($orderItems as $item) {
            $orderItemId = wc_add_order_item($orderId, array(
                'order_item_name' => $item->Name,
                'order_item_type' => 'line_item', // product
                'order_id' => $orderId
            ));

            $itemMeta = [
                '_qty' => $item->Qty,
                '_product_id' => $item->ItemId,
                '_line_subtotal' => $item->TotalLineAmount,
                '_line_total' => $item->TotalLineAmount,
                'cost' => $item->UnitPrice,
                'total_tax' => $item->TotalLineTax,
            ];

            foreach ($itemMeta as $key => $value) {
                $wpdb->insert($this->wc_order_item_meta_table, [
                    'order_item_id' => $orderItemId,
                    'meta_key' => $key,
                    'meta_value' => $value,
                ]);
            }

        }
    }

    public function downloadOrders()
    {
        $_messages = array();
        $_errorCount = 0;
        $_processedCount = 0;

        $itemsCount = 1;
        $start = 1;
        $limit = 1000;

        try {
            $wpOrders = $this->getWpOrders();
            $wpCustomers = $this->getWpCustomers();

            while ($itemsCount > 0 && $limit >= 1000) {

                $salesOrders = $this->searchOrders($start, $limit);

                $itemsCount = count($salesOrders);
                if ($itemsCount > 0) {
                    foreach ($salesOrders as $order) {

                        if (array_key_exists($order->SalesOrderInternalId, $wpOrders)) {
                            // ignore it
                        } else if (is_numeric($order->CustomerId)) {
                            if (in_array($order->CustomerId, $wpCustomers)) { // insert order only if customer exists in WP
                                $this->insertWpOrder($order);
                            }
                        } else {
                            $this->insertWpOrder($order);
                        }

                        $_processedCount++;
                    }
                }

                $start += $itemsCount;
                $limit = $start + 1000;
            }
        } catch (Exception $e) {
            $this->log($e->getMessage());

            if ($_errorCount == 0)
                $_messages[] = array("type" => 'error', "message" => "Errors have occurred during the downlaod Orders process, please review the logs. First error occurred is: " . $e->getMessage());

            $_errorCount++;
        } finally {
            $_processedCount++;
        }

        if ($_errorCount == 0) {
            $_messages[] = array(
                "type" => 'success',
                "message" => sprintf("Download Orders has been completed. %s record(s) processed.", $_processedCount)
            );
        }

        $this->log($_messages);

        return $this->_prepareMessages($_messages);

    }


    /**
     * Sync Products to eConnect
     *
     * @return string
     */
    public function syncItem($id = null)
    {
        global $wpdb;
        $_messages = array();
        $_errorCount = 0;
        $_processedCount = 0;

        $securityToken = $this->_getSecurityToken();

        try {
            $client = new SoapClient($this->_getWsdlUrl(), array('trace' => true, 'exceptions' => true));

            $productsQuery = "SELECT p.*
            FROM " . $this->posts_table . " p
            WHERE p.post_type = 'product'
            AND ((p.ec_last_modified_date = '' OR p.ec_last_modified_date is null)
            OR TIMESTAMPDIFF(minute, p.ec_last_modified_date, IFNULL( p.post_modified, p.post_date )) > 0)
            ";

            //filter if update is for a single record
            if ($id != null) {
                $productsQuery .= " AND p.ID = {$id}";
            }

            foreach ($wpdb->get_results($productsQuery) as $post) {

                $postMeta = array_map(
                    function ($a) {
                        return $a[0];
                    }, get_post_meta($post->ID)
                );

                $product = array_merge((array)$post, $postMeta);

                try {
                    $itemDetails = array(
                        'ItemId' => $product['ID'],
                        'Name' => $product['post_title'],
                        'SKU' => isset($product['_sku']) ? $product['_sku'] : '',
                        'Description' => isset($product['post_excerpt']) ? $product['post_excerpt'] : $product['post_name'],
                        'UnitPrice' => isset($product['_price']) ? floatval($product['_price']) : 0,
                        'UnitCost' => '0',
                        'UnitOfMeasure' => '',
                        'Active' => ($product['post_status'] == 'published') ? true : false,
                        'ItemType' => $product['post_type'],
                        'QtyOnHand' => isset($product['_stock']) ? floatval($product['_stock']) : 0,
                        'UPC' => '',
                        'Taxable' => (isset($product['_tax_status']) && $product['_tax_status'] == 'taxable') ? 1 : 0,
                        'TaxRate' => '0',
                        'ItemCategoryId' => '',
                        'TaxCategoryID' => '',
                        'SoftwareId' => $this->software,
                        'ImageUrl' => '',
                        'ItemNotes' => '',
                        'GrossPrice' => 0,
                        'WarrantyDiscount' => 0,
                        'SalesDiscount' => 0,
                    );
                    // insert product
                    if (empty($product['ec_internal_id'])) {
                        $parameters = array(
                            'securityToken' => $securityToken,
                            'itemDetails' => $itemDetails
                        );

                        $this->log($parameters);

                        $addItemResponse = $client->AddItem($parameters);
                        $obj = $addItemResponse->AddItemResult;

                        $this->log('Added product item response: ');
                        $this->log($addItemResponse);

                    } else { // update product
                        $parameters = array(
                            'securityToken' => $securityToken,
                            'itemDetails' => $itemDetails,
                            'itemId' => $product['ID'],
                            'itemInternalId' => $product['ec_internal_id']
                        );

                        $this->log('update product: ');
                        $this->log($parameters);

                        $updateItemResponse = $client->UpdateItem($parameters);
                        $obj = $updateItemResponse->UpdateItemResult;

                        $this->log('Updated product response: ');
                        $this->log($updateItemResponse);
                    }

                    if ($obj->Status == "Error") {
                        if ($_errorCount == 0)
                            $_messages[] = array("type" => 'error', "message" => "Errors have occurred during the sync products process, please review the logs. First error occurred is: " . $obj->Error);

                        $_errorCount++;
                    }

                    $whereCondition = array('ID' => (int)$product['ID']);
                    $this->updateWPTable($this->posts_table, $obj, $whereCondition);

                    $this->log('Product info saved');

                } catch (Exception $e) {
                    $this->log($e->getMessage());

                    if ($_errorCount == 0)
                        $_messages[] = array("type" => 'error', "message" => "Errors have occurred during the sync products process, please review the logs. First error occurred is: " . $e->getMessage());

                    $_errorCount++;
                } finally {
                    $_processedCount++;
                }
            }
            array_unshift($_messages, array("type" => 'notice', "message" => sprintf("Sync Products process has been completed. %s record(s) processed.", $_processedCount)));

            if ($_errorCount == 0)
                $_messages[] = array("type" => 'success', "message" => "Completed the sync product process with no errors.");
        } catch (Exception $e) {
            $this->log($e->getMessage());
            $_messages[] = array("type" => 'error', "message" => $e->getMessage());
        }

        $this->log($_messages);

        return $this->_prepareMessages($_messages);
    }

    /**
     * Sync order Invoices to eConnect
     *
     * @return string
     */
    public function syncInvoice($id = null)
    {
        global $wpdb;
        $_messages = array();
        $_errorCount = 0;
        $_processedCount = 0;

        $securityToken = $this->_getSecurityToken();

        try {
            $client = new SoapClient($this->_getWsdlUrl(), array('trace' => true, 'exceptions' => true));

            $query = "SELECT p.*
            FROM " . $this->posts_table . " p
            WHERE p.post_type = 'shop_order'
            AND p.post_status IN ('wc-completed', 'wc-pending')
            AND ((p.ec_last_modified_date = '' OR p.ec_last_modified_date is null)
            OR TIMESTAMPDIFF(minute, p.ec_last_modified_date, IFNULL( p.post_modified, p.post_date )) > 0)
            ";

            //filter if update is for a single record
            if ($id != null) {
                $query .= " AND p.ID = {$id}";
            }

            foreach ($wpdb->get_results($query) as $post) {

                $postMeta = array_map(function ($a) {
                    return $a[0];
                }, get_post_meta($post->ID));

                $order = array_merge((array)$post, $postMeta);

                try {
                    $invoice = array(
                        'InvoiceNumber' => $order['ID'],
                        'CustomerId' => $order['_customer_user'],
                        'InvoiceDate' => $order['post_date'],
                        'Currency' => $order['_order_currency'],
                        'InvoiceAmount' => $order['_order_total'],
                        'InvoiceDueDate' => $order['post_date'],
                        'AmountDue' => ($order['post_status'] == 'wc-pending') ? $order['_order_total'] : '0',
                        'SoNum' => $order['ID'],
                        'TotalTaxAmount' => $order['_order_tax'],
                        'InvoiceUniqueId' => $order['ID'],
                        'InvoiceDescription' => $order['_billing_address_index'],
                        'NotifyCustomer' => 'false',
                        'Software' => $this->software,
                    );

                    $lineItems = $this->getOrderItems($order['ID']);

                    if (count($lineItems) > 0) {
                        $invoice['Items'] = $lineItems;
                    }
                    // add invoice
                    if (empty($order['ec_internal_id'])) {

                        $parameters = array(
                            'securityToken' => $securityToken,
                            'invoice' => $invoice
                        );

                        $this->log("Add invoice item params: ");
                        $this->log($parameters);

                        $addInvoiceResponse = $client->AddInvoice($parameters);
                        $obj = $addInvoiceResponse->AddInvoiceResult;

                        $this->log("Added invoice response: ");
                        $this->log($addInvoiceResponse);

                    } else { // update invoice
                        $parameters = array(
                            'securityToken' => $securityToken,
                            'invoice' => $invoice,
                            'invoiceNumber' => $order['ID'],
                            'invoiceInternalId' => $order['ec_internal_id']
                        );

                        $this->log('Update invoice item with params: ');
                        $this->log($parameters);

                        $updateInvoiceResponse = $client->UpdateInvoice($parameters);
                        $obj = $updateInvoiceResponse->UpdateInvoiceResult;

                        $this->log('updated item response: ');
                        $this->log($updateInvoiceResponse);
                    }

                    if ($obj->Status == "Error") {
                        if ($_errorCount == 0)
                            $_messages[] = array("type" => 'error', "message" => "Errors have occurred during the Invoice sync process, please review the logs. First error occurred is: " . $obj->Error);

                        $_errorCount++;
                    }

                    $whereCondition = array('ID' => (int)$order['ID']);
                    $this->updateWPTable($this->posts_table, $obj, $whereCondition);

                    $this->log('Invoice info saved');


                } catch (Exception $e) {
                    $this->log($e->getMessage());

                    if ($_errorCount == 0)
                        $_messages[] = array("type" => 'error', "message" => "Errors have occurred during the Invoice sync process, please review the logs.
                        First error occurred is: " . $e->getMessage());

                    $_errorCount++;
                } finally {
                    $_processedCount++;
                }
            }
            array_unshift($_messages, array("type" => 'notice', "message" => sprintf("Sync Invoices process has been completed. %s record(s) processed.", $_processedCount)));

            if ($_errorCount == 0)
                $_messages[] = array("type" => 'success', "message" => "Completed the Invoice sync process with no errors.");

        } catch (Exception $e) {
            $this->log($e->getMessage());
            $_messages[] = array("type" => 'error', "message" => $e->getMessage());
        }

        $this->log($_messages);
        return $this->_prepareMessages($_messages);
    }

    /**
     * Sync order to eConnect
     *
     * @return string
     */
    public function syncOrder($id = null)
    {
        global $wpdb;
        $_messages = array();
        $_errorCount = 0;
        $_processedCount = 0;

        $securityToken = $this->_getSecurityToken();

        try {
            $client = new SoapClient($this->_getWsdlUrl(), array('trace' => true, 'exceptions' => true));

            $query = "SELECT p.*
            FROM " . $this->posts_table . " p
            WHERE p.post_type = 'shop_order'
            AND p.post_status IN ('wc-processing', 'wc-completed')
            AND ((p.ec_order_last_modified_date = '' OR p.ec_order_last_modified_date is null)
            OR TIMESTAMPDIFF(minute, p.ec_order_last_modified_date, IFNULL( p.post_modified, p.post_date )) > 0)
            ";

            //filter if update is for a single record
            if ($id != null) {
                $query .= " AND p.ID = {$id}";
            }

            foreach ($wpdb->get_results($query) as $post) {

                $postMeta = array_map(function ($a) {
                    return $a[0];
                }, get_post_meta($post->ID));

                $order = array_merge((array)$post, $postMeta);
                // if No customer: Adding Record error. Cannot insert the value NULL into column 'PayerInternalId', table 'ebizportalgroup1-db.dbo.SalesOrders'; column does not allow nulls. INSERT fails.

                try {
                    $salesOrder = array(
                        'SalesOrderNumber' => $order['ID'],
                        'CustomerId' => $order['_customer_user'],
                        'Date' => $order['post_date'],
                        'Currency' => $order['_order_currency'],
                        'Amount' => $order['_order_total'],
                        'DueDate' => $order['post_date'],
                        'DateUploaded' => $order['post_date'],
                        'AmountDue' => '0',
                        'PoNum' => $order['ID'],
                        'TotalTaxAmount' => $order['_order_tax'],
                        'Description' => $order['_billing_address_index'],
                        'NotifyCustomer' => 'false',
                        'billingAddress' => $this->getOrderBillingAddress($order),
                        'shippingAddress' => $this->getOrderShippingAddress($order),
                        'Memo' => 'No',
                        'ShipDate' => $order['post_date'],
                        'ShipVia' => 'NA',
                        'IsToBeEmailed' => 'false',
                        'IsToBePrinted' => 'false',
                        'UniqueId' => $order['ID'],
                        'Software' => $this->software,
                    );

                    $lineItems = $this->getOrderItems($order['ID']);

                    if (count($lineItems) > 0) {
                        $salesOrder['Items'] = $lineItems;
                    }
                    // add sales order
                    if (empty($order['ec_order_internal_id'])) {

                        $parameters = array(
                            'securityToken' => $securityToken,
                            'salesOrder' => $salesOrder
                        );

                        $this->log("Add sales order params: ");
                        $this->log($parameters);

                        $addOrderResponse = $client->AddSalesOrder($parameters);
                        $responseObj = $addOrderResponse->AddSalesOrderResult;

                        $this->log("Added sales order response: ");
                        $this->log($addOrderResponse);

                    } else { // update invoice
                        $parameters = array(
                            'securityToken' => $securityToken,
                            'salesOrder' => $salesOrder,
                            'SalesOrderNumber' => $order['ID'],
                            'salesOrderInternalId' => $order['ec_order_internal_id']
                        );

                        $this->log('Update sales order with params: ');
                        $this->log($parameters);

                        $updateOrderResponse = $client->UpdateSalesOrder($parameters);
                        $responseObj = $updateOrderResponse->UpdateSalesOrderResult;

                        $this->log('updated Sales order response: ');
                        $this->log($updateOrderResponse);
                    }

                    if ($responseObj->Status == "Error") {
                        if ($_errorCount == 0)
                            $_messages[] = array(
                                'type' => 'error',
                                'message' => 'Errors have occurred during the Sales order sync process, please review the logs. First error occurred is: ' . $responseObj->Error
                            );

                        $_errorCount++;
                    }

                    $now = new DateTime(null);
                    $now->modify("+10 seconds"); //add 10 seconds to make sure sync last_modified_date is greater than object update date
                    $data = array();
                    $data['ec_order_internal_id'] = $responseObj->SalesOrderInternalId;
                    $data['ec_order_status'] = $responseObj->Status;
                    $data['ec_order_error'] = $responseObj->Error;
                    $data['ec_order_last_modified_date'] = $now->format('Y-m-d H:i:s');
                    $whereCondition = array('ID' => (int)$order['ID']);

                    $wpdb->update($this->posts_table, $data, $whereCondition);

                    $this->log('Sales order info saved. Order ID:' . (int)$order['ID']);

                } catch (Exception $e) {
                    $this->log($e->getMessage());

                    if ($_errorCount == 0)
                        $_messages[] = array("type" => 'error', "message" => "Errors have occurred during the Sales orders sync process, please review the logs.
                        First error occurred is: " . $e->getMessage());

                    $_errorCount++;
                } finally {
                    $_processedCount++;
                }

            }
            array_unshift($_messages, array("type" => 'notice', "message" => sprintf("Sync Orders process has been completed. %s record(s) processed.", $_processedCount)));

            if ($_errorCount == 0)
                $_messages[] = array("type" => 'success', "message" => "Completed the Sales order sync process with no errors.");

        } catch (Exception $e) {
            $this->log($e->getMessage());
            $_messages[] = array("type" => 'error', "message" => $e->getMessage());
        }

        $this->log($_messages);
        return $this->_prepareMessages($_messages);
    }

    function getOrderBillingAddress($order)
    {
        return array(
            'FirstName' => $order['_billing_first_name'],
            'LastName' => $order['_billing_last_name'],
            'CompanyName' => isset($order['_billing_company']) ? $order['_billing_company'] : '',
            'Address1' => $order['_billing_address_1'],
            'Address2' => $order['_billing_address_2'],
            'City' => $order['_billing_city'],
            'State' => $order['_billing_state'],
            'ZipCode' => $order['_billing_postcode'],
            'Country' => $order['_billing_country']
        );
    }

    function getOrderShippingAddress($order)
    {
        return array(
            'FirstName' => $order['_shipping_first_name'],
            'LastName' => $order['_shipping_last_name'],
            'CompanyName' => isset($order['_shipping_company']) ? $order['_shipping_company'] : '',
            'Address1' => $order['_shipping_address_1'],
            'Address2' => $order['_shipping_address_2'],
            'City' => $order['_shipping_city'],
            'State' => $order['_shipping_state'],
            'ZipCode' => $order['_shipping_postcode'],
            'Country' => $order['_shipping_country']
        );
    }

    function getOrderItems($orderId)
    {
        $lineItems = array();
        $lineNumber = 0;

        $order = wc_get_order($orderId);
        // Iterating through each WC_Order_Item_Product objects
        foreach ($order->get_items() as $key => $item) {

            $product = $item->get_product(); // the WC_Product object
            ## Access Order Items data properties (in an array of values) ##
            $item_data = $item->get_data();

            $lineItems[] = array(
                'ItemId' => $item_data['product_id'],
                'Name' => $item_data['name'],
                'Description' => $item_data['name'],
                'UnitPrice' => $product->get_price(),
                'Qty' => $item_data['quantity'],
                'Taxable' => ($item_data['total_tax']) > 0 ? true : false,
                'TaxRate' => '0',
                'TotalLineAmount' => $item_data['total'],
                'TotalLineTax' => $item_data['total_tax'],
                'ItemLineNumber' => ++$lineNumber,
                'GrossPrice' => 0,
                'WarrantyDiscount' => 0,
                'SalesDiscount' => 0,
            );
        }

        return $lineItems;
    }

    public function log($msg)
    {
        try {
            if (!is_string($msg)) {
                $msg = print_r($msg, true);
            }
            $msg = $msg . "\r\n";
            $file = plugin_dir_path(__FILE__) . 'econnect.log';
            $fp = @fopen($file, 'a');
            @fwrite($fp, $msg);
            @fclose($fp);

        } catch (Exception $e) {

        }
    }

}