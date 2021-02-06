<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
define("EBIZCHARGE_VERSION", "3.9.2");

/**
 * EbizCharge Transaction Class
 *
 */
class WC_Gateway_EBizCharge
{
    public $enableEconnect = false;
    // Required for all transactions
    public $key;   // Source key
    public $userid;   // User Id
    public $pin;   // Source pin (optional)
    public $amount;  // the entire amount that will be charged to the customers card
    // (including tax, shipping, etc)
    public $invoice;  // invoice number.  must be unique.  limited to 10 digits.  use orderid if you need longer.
    // Required for Commercial Card support
    public $ponum;   // Purchase Order Number
    public $tax;   // Tax
    public $nontaxable; // Order is non taxable
    // Amount details (optional)
    public $tip;    // Tip
    public $shipping = 0;  // Shipping charge
    public $discount = 0;  // Discount amount (ie gift certificate or coupon code)
    public $subtotal = 0;  // if subtotal is set, then
    // subtotal + tip + shipping - discount + tax must equal amount
    // or the transaction will be declined.  If subtotal is left blank
    // then it will be ignored
    public $currency;  // Currency of $amount
    // Required Fields for Card Not Present transacitons (Ecommerce)
    public $card;   // card number, no dashes, no spaces
    public $cardtype;  //type of the card
    public $exp;   // expiration date 4 digits no /
    public $cardholder;  // name of card holder
    public $street;  // street address
    public $zip;   // zip code
    // Fields for Card Present (POS)
    public $magstripe;   // mag stripe data.  can be either Track 1, Track2  or  Both  (Required if card,exp,cardholder,street and zip aren't filled in)
    public $cardpresent;   // Must be set to true if processing a card present transaction  (Default is false)
    public $termtype;   // The type of terminal being used:  Optons are  POS - cash register, StandAlone - self service terminal,  Unattended - ie gas pump, Unkown  (Default:  Unknown)
    public $magsupport;   // Support for mag stripe reader:   yes, no, contactless, unknown  (default is unknown unless magstripe has been sent)
    public $contactless;   // Magstripe was read with contactless reader:  yes, no  (default is no)
    public $dukpt;   // DUK/PT for PIN Debit
    public $signature;     // Signature Capture data
    // fields required for check transactions
    public $account;  // bank account number
    public $routing;  // bank routing number
    public $ssn;   // social security number
    public $dlnum;   // drivers license number (required if not using ssn)
    public $dlstate;  // drivers license issuing state
    public $checknum;  // Check Number
    public $accounttype;       // Checking or Savings
    public $checkformat; // Override default check record format
    public $checkimage_front;    // Check front
    public $checkimage_back;  // Check back
    // Fields required for Secure Vault Payments (Direct Pay)
    public $svpbank;  // ID of cardholders bank
    public $svpreturnurl; // URL that the bank should return the user to when tran is completed
    public $svpcancelurl;  // URL that the bank should return the user if they cancel
    // Option parameters
    public $origauthcode; // required if running postauth transaction.
    public $command;  // type of command to run; Possible values are:
    // sale, credit, void, preauth, postauth, check and checkcredit.
    // Default is sale.
    public $orderid;  // Unique order identifier.  This field can be used to reference
    // the order for which this transaction corresponds to. This field
    // can contain up to 64 characters and should be used instead of
    // UMinvoice when orderids longer that 10 digits are needed.
    public $custid;   // Alpha-numeric id that uniquely identifies the customer.
    public $description; // description of charge
    public $cvv2;   // cvv2 code
    public $custemail;  // customers email address
    public $custreceipt = false; // send customer a receipt
    public $custreceipt_template; // select receipt template
    public $ignoreduplicate; // prevent the system from detecting and folding duplicates
    public $ip;   // ip address of remote host
    public $testmode;  // test transaction but don't process it
    public $timeout;       // transaction timeout.  defaults to 90 seconds
    public $gatewayurl;    // url for the gateway
    public $proxyurl;  // proxy server to use (if required by network)
    public $ignoresslcerterrors;  // Bypasses ssl certificate errors.  It is highly recommended that you do not use this option.  Fix your openssl installation instead!
    public $cabundle;      // manually specify location of root ca bundle (useful of root ca is not in default location)
    public $transport;     // manually select transport to use (curl or stream), by default the library will auto select based on what is available
    // Card Authorization - Verified By Visa and Mastercard SecureCode
    public $cardauth;     // enable card authentication
    public $pares;   //
    // Third Party Card Authorization
    public $xid;
    public $cavv;
    public $eci;
    // Recurring Billing
    public $recurring;  //  Save transaction as a recurring transaction:  yes/no
    public $schedule;  //  How often to run transaction: daily, weekly, biweekly, monthly, bimonthly, quarterly, annually.  Default is monthly.
    public $numleft;   //  The number of times to run. Either a number or * for unlimited.  Default is unlimited.
    public $start;   //  When to start the schedule.  Default is tomorrow.  Must be in YYYYMMDD  format.
    public $end;   //  When to stop running transactions. Default is to run forever.  If both end and numleft are specified, transaction will stop when the ealiest condition is met.
    public $billamount; //  Optional recurring billing amount.  If not specified, the amount field will be used for future recurring billing payments
    public $billtax;
    public $billsourcekey;
    // Billing Fields
    public $billfname;
    public $billlname;
    public $billcompany;
    public $billstreet;
    public $billstreet2;
    public $billcity;
    public $billstate;
    public $billzip;
    public $billcountry;
    public $billphone;
    public $email;
    public $fax;
    public $website;
    // Shipping Fields
    public $delivery;  // type of delivery method ('ship','pickup','download')
    public $shipfname;
    public $shiplname;
    public $shipcompany;
    public $shipstreet;
    public $shipstreet2;
    public $shipcity;
    public $shipstate;
    public $shipzip;
    public $shipcountry;
    public $shipphone;
    // Custom Fields
    public $custom1;
    public $custom2;
    public $custom3;
    public $custom4;
    public $custom5;
    public $custom6;
    public $custom7;
    public $custom8;
    public $custom9;
    public $custom10;
    // Line items  (see addLine)
    public $lineitems;
    // Line items for tokenization (see addLineItem())
    public $lineItems;
    public $comments; // Additional transaction details or comments (free form text field supports up to 65,000 chars)
    public $software; // Allows developers to identify their application to the gateway (for troubleshooting purposes)
    // response fields
    public $rawresult;  // raw result from gateway
    public $result;  // full result:  Approved, Declined, Error
    public $resultcode;  // abreviated result code: A D E
    public $authcode;  // authorization code
    public $refnum;  // reference number
    public $batch;  // batch number
    public $avs_result;  // avs result
    public $avs_result_code;  // avs result
    public $avs;       // obsolete avs result
    public $cvv2_result;  // cvv2 result
    public $cvv2_result_code;  // cvv2 result
    public $vpas_result_code;      // vpas result
    public $isduplicate;      // system identified transaction as a duplicate
    public $convertedamount;  // transaction amount after server has converted it to merchants currency
    public $convertedamountcurrency;  // merchants currency
    public $conversionrate;  // the conversion rate that was used
    public $custnum;  //  gateway assigned customer ref number for recurring billing
    // Cardinal Response Fields
    public $acsurl; // card auth url
    public $pareq;  // card auth request
    public $cctransid; // cardinal transid
    // Errors Response Feilds
    public $error;   // error message if result is an error
    public $errorcode;  // numerical error code
    public $blank;   // blank response
    public $transporterror;  // transport error
    public $methodID;  // transport error

    function __construct()
    {
        // Set default values.
        $this->command = "sale";
        $this->result = "Error";
        $this->resultcode = "E";
        $this->error = "Transaction not processed yet.";
        $this->timeout = 90;
        $this->cardpresent = false;
        $this->lineitems = array();
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $this->ip = $_SERVER['REMOTE_ADDR'];
        }
        $this->software = "woocommerce";
    }


    protected function write_log($message)
    {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }

    function ebiz_log($message)
    {
        if (is_array($message) || is_object($message)) {
            $message = json_encode($message);
            $message = print_r($message, true);
        }
        $file = fopen(WP_CONTENT_DIR . "/ebizcharge.log", "a");
        fwrite($file, "\n" . date('d-M-Y h:i:s') . " :: " . $message);
        fclose($file);
    }

    function _getGatewayBaseUrl()
    {
        return 'https://soap.ebizcharge.net';
    }

    function _getWsdlUrl()
    {
        return 'https://soap.ebizcharge.net/eBizService.svc?singleWsdl';
    }

    // Set Soap Client Parameters
    public function SoapParams()
    {
        return array(
            'cache_wsdl' => false
        );
    }

    function _getUeSecurityToken()
    {
        return array(
            'SecurityId' => $this->key,
            'UserId' => $this->userid,
            'Password' => $this->pin
        );
    }

    /**
     * Add a line item to the transaction
     *
     * @param string $sku
     * @param string $name
     * @param string $description
     * @param double $cost
     * @param string $taxable
     * @param int $qty
     */
    function addLine($sku, $name, $description, $cost, $qty, $taxAmount)
    {
        $this->lineitems[] = array(
            'sku' => $sku,
            'name' => $name,
            'description' => $description,
            'cost' => $cost,
            'taxable' => ($taxAmount > 0) ? 'Y' : 'N',
            'qty' => $qty
        );
    }

    /**
     * Add line items to the transaction used in tokenization
     *
     * @param string $sku
     * @param string $name
     * @param string $description
     * @param double $cost
     * @param int $qty
     * @param double $taxAmount
     */
    function addLineItem($sku, $name, $description, $cost, $qty, $taxAmount)
    {
        $this->lineItems[] = array(
            'SKU' => $sku,
            'ProductName' => $name,
            'Description' => $description,
            'UnitPrice' => $cost,
            'Taxable' => ($taxAmount > 0) ? 'Y' : 'N',
            'TaxAmount' => $taxAmount,
            'Qty' => $qty
        );
    }

    function clearLines()
    {
        $this->lineitems = array();
    }

    function clearLineItems()
    {
        $this->lineItems = array();
    }

    /**
     * Verify that all required data has been set
     *
     * @return string
     */
    function CheckData()
    {
        if (!$this->key) {
            return "Source Key is required";
        }
        if (in_array(strtolower($this->command), array(
            "quickcredit",
            "quicksale",
            "cc:capture",
            "cc:refund",
            "refund",
            "check:refund",
            "capture",
            "creditvoid"
        ))) {
            if (!$this->refnum) {
                return "Reference Number is required";
            }
        } else if (in_array(strtolower($this->command), array("svp"))) {
            if (!$this->svpbank) {
                return "Bank ID is required";
            }
            if (!$this->svpreturnurl) {
                return "Return URL is required";
            }
            if (!$this->svpcancelurl) {
                return "Cancel URL is required";
            }
        } else {
            if (in_array(strtolower($this->command), array(
                "check:sale",
                "check:credit",
                "check",
                "checkcredit",
                "reverseach"
            ))) {
                if (!$this->account) {
                    return "Account Number is required";
                }
                if (!$this->routing) {
                    return "Routing Number is required";
                }
            } else {
                if (!$this->magstripe) {
                    if (!$this->card) {
                        return "Credit Card Number is required ({$this->command})";
                    }
                    if (!$this->exp) {
                        return "Expiration Date is required";
                    }
                }
            }
            $this->amount = preg_replace('/[^\d.]+/', '', $this->amount);
            if (!$this->amount) {
                return "Amount is required";
            }
            if (!$this->invoice && !$this->orderid) {
                return "Invoice number or Order ID is required";
            }
            if (!$this->magstripe) {
                //if(!$this->cardholder) return "Cardholder Name is required";
                //if(!$this->street) return "Street Address is required";
                //if(!$this->zip) return "Zipcode is required";
            }
        }

        return 0;
    }

    //------------------- Developer New Functions Added Start -------------------

    /**
     * This function set gateway transaction result
     */
    public function setTransactionResult($transaction)
    {

        $this->result = $transaction->Result;
        $this->resultcode = $transaction->ResultCode;
        $this->authcode = $transaction->AuthCode;
        $this->refnum = $transaction->RefNum;
        $this->batch = $transaction->BatchNum;
        $this->avs_result = $transaction->AvsResult;
        $this->avs_result_code = $transaction->AvsResultCode;
        $this->cvv2_result = $transaction->CardCodeResult;
        $this->cvv2_result_code = $transaction->CardCodeResultCode;
        $this->vpas_result_code = $transaction->VpasResultCode;
        $this->convertedamount = $transaction->ConvertedAmount;
        $this->convertedamountcurrency = $transaction->ConvertedAmountCurrency;
        $this->conversionrate = $transaction->ConversionRate;
        $this->error = $transaction->Error;
        $this->errorcode = $transaction->ErrorCode;
        $this->custnum = $transaction->CustNum;
        // Obsolete variable (for backward compatibility) At some point they will no longer be set.
        $this->avs = $transaction->AvsResult;
        $this->cvv2 = $transaction->CardCodeResult;

        $this->cctransid = $transaction->RefNum;
        $this->acsurl = $transaction->AcsUrl;
        $this->pareq = $transaction->Payload;

        if ($this->resultcode == 'A') {
            $this->ebiz_log('RefNum: ' . $this->refnum . ' Result: ' . $this->result);
            return TRUE;
        } else {
            $message = $this->error . '(' . $this->errorcode . '). RefNum (' . $this->refnum . ') is "' . $this->result . '".';
            $this->write_log($message);
            $this->ebiz_log($message);
            return false;
        }
    }

    /**
     * Return customer billing address
     */
    private function getBillingAddress()
    {
        return array(
            'FirstName' => $this->billfname,
            'LastName' => $this->billlname,
            'Company' => $this->billcompany,
            'Street' => $this->billstreet,
            'Street2' => $this->billstreet2,
            'City' => $this->billcity,
            'State' => $this->billstate,
            'Zip' => $this->billzip,
            'Country' => $this->billcountry,
            'Phone' => $this->billphone,
            'Fax' => $this->fax,
            'Email' => $this->email
        );
    }

    /**
     * Return customer shipping address
     */
    private function getShippingAddress()
    {
        return array(
            'FirstName' => $this->shipfname,
            'LastName' => $this->shiplname,
            'Company' => $this->shipcompany,
            'Street' => $this->shipstreet,
            'Street2' => $this->shipstreet2,
            'City' => $this->shipcity,
            'State' => $this->shipstate,
            'Zip' => $this->shipzip,
            'Country' => $this->shipcountry,
            'Phone' => $this->shipphone,
            'Fax' => $this->fax,
            'Email' => $this->email
        );
    }

    /**
     * Customer billing address
     * @return array
     */
    private function getBillingAddressAddCust()
    {
        return array(
            'FirstName' => $this->billfname,
            'LastName' => $this->billlname,
            'Company' => $this->billcompany,
            'Address1' => $this->billstreet,
            'Address2' => $this->billstreet2,
            'City' => $this->billcity,
            'State' => $this->billstate,
            'ZipCode' => $this->billzip,
            'Country' => $this->billcountry,
            'Phone' => $this->billphone,
            'Fax' => $this->fax,
            'Email' => $this->email
        );
    }

    /**
     * New shipping for add customer
     */
    private function getShippingAddressAddCust()
    {
        return array(
            'FirstName' => $this->shipfname,
            'LastName' => $this->shiplname,
            'Company' => $this->shipcompany,
            'Address1' => $this->shipstreet,
            'Address2' => $this->shipstreet2,
            'City' => $this->shipcity,
            'State' => $this->shipstate,
            'ZipCode' => $this->shipzip,
            'Country' => $this->shipcountry,
            'Phone' => $this->shipphone,
            'Fax' => $this->fax,
            'Email' => $this->email
        );
    }

    /**
     * Return payment details
     */
    private function getTransactionDetails()
    {
        return array(
            'OrderID' => $this->orderid,
            'Invoice' => $this->invoice,
            'PONum' => $this->ponum,
            'Description' => $this->description,
            'Amount' => $this->amount,
            'Tax' => $this->tax,
            'Currency' => $this->currency,
            'Shipping' => $this->shipping,
            'ShipFromZip' => $this->shipzip,
            'Discount' => $this->discount,
            'Subtotal' => $this->subtotal,
            'AllowPartialAuth' => false,
            'Tip' => 0,
            'NonTax' => false,
            'Duty' => 0,
        );
    }

    /**
     * Return transaction object for API
     */
    private function getTransactionRequest()
    {
        $customerId = empty($this->custid) ? 'Guest' : $this->custid;
        return array(
            'CustReceipt' => $this->custreceipt,
            'CustReceiptName' => $this->custreceipt_template,
            'Software' => $this->software,
            'LineItems' => $this->lineItems,
            'IsRecurring' => false,
            'IgnoreDuplicate' => false,
            'Details' => $this->getTransactionDetails(),
            'CustomerID' => $customerId,
            'CreditCardData' => array(
                'InternalCardAuth' => false,
                'CardPresent' => true,
                'CardNumber' => $this->card,
                'CardExpiration' => $this->exp,
                'CardCode' => $this->cvv2,
                'AvsStreet' => $this->billstreet,
                'AvsZip' => $this->billzip
            ),
            'Command' => $this->command,
            'ClientIP' => $this->ip,
            'AccountHolder' => $this->cardholder,
            'RefNum' => $this->refnum,
            'BillingAddress' => $this->getBillingAddress(),
            'ShippingAddress' => $this->getShippingAddress(),
        );
    }

    /**
     * Get Customer Data for Add New Customer #8 (use #4, #5)
     */
    public function getCustomerData($customer_id)
    {
        return array(
            'CustomerId' => $customer_id,
            'FirstName' => $this->billfname,
            'LastName' => $this->billlname,
            'CompanyName' => $this->billcompany,
            'Phone' => $this->billphone,
            'CellPhone' => $this->billphone,
            'Fax' => $this->fax,
            'Email' => $this->email,
            'WebSite' => $this->website,
            'ShippingAddress' => $this->getShippingAddressAddCust(),
            'BillingAddress' => $this->getBillingAddressAddCust(),
        );
    }

    /**
     * Get Customer Payment data
     */
    private function getCustomerPayment()
    {
        /*$paymentTypes = $this->_paymentConfig->getCcTypes();
        $MethodName = $this->cardtype;

        foreach ($paymentTypes as $code => $text)
        {
            if ($code == $this->cardtype)
            {
                $MethodName = $text;
            }
        }*/

        return array(
            'MethodName' => $this->cardtype . ' ' . substr($this->card, -4) . ' - ' . $this->cardholder, # . ' - Expires on: ' . $this->exp,
            'SecondarySort' => 1,
            'Created' => date('Y-m-d\TH:i:s'),
            'Modified' => date('Y-m-d\TH:i:s'),
            'AvsStreet' => $this->billstreet,
            'AvsZip' => $this->billzip,
            'CardCode' => $this->cvv2,
            'CardExpiration' => $this->exp,
            'CardNumber' => $this->card,
            'CardType' => $this->cardtype,
            'Balance' => $this->amount,
            'MaxBalance' => $this->amount,
        );
    }

    /**
     * Save getCustomerTransactionRequest data
     */
    private function getCustomerTransactionRequest()
    {
        return array(
            'isRecurring' => false,
            'IgnoreDuplicate' => true,
            'Details' => $this->getTransactionDetails(),
            'Software' => $this->software,
            'MerchReceipt' => true,
            'CustReceiptName' => $this->custreceipt_template,
            'CustReceiptEmail' => '',
            'CustReceipt' => $this->custreceipt,
            'ClientIP' => $this->ip,
            'CardCode' => $this->cvv2,
            'Command' => $this->command,
            'LineItems' => $this->lineItems
        );
    }

    // This function is used for Void, Cancel and Refund
    public function executeTransaction()
    {
        try {
            $client = new SoapClient($this->_getWsdlUrl(), $this->SoapParams());
            $transaction = $client->runTransaction(
                array(
                    'securityToken' => $this->_getUeSecurityToken(),
                    'tran' => array(
                        'Command' => $this->command,
                        'RefNum' => $this->refnum,
                        'IsRecurring' => false,
                        'IgnoreDuplicate' => false,
                        'CustReceipt' => $this->custreceipt,
                    )
                )
            );
            // setTransactionResult called for success
            if (!empty($transactionResult = $transaction->runTransactionResult)) {
                return $this->setTransactionResult($transactionResult);
            }
        } catch (SoapFault $ex) {
            $this->error = $ex->getMessage();
            $this->ebiz_log('Error: ' . $this->error);
            return false;
        }

        return false;
    }

    // This function is used for manual Refund
    public function refundTransaction()
    {
        try {
            $client = new SoapClient($this->_getWsdlUrl(), $this->SoapParams());
            $transaction = $client->runTransaction(
                array(
                    'securityToken' => $this->_getUeSecurityToken(),
                    'tran' => array(
                        'Command' => $this->command,
                        'RefNum' => $this->refnum,
                        'IsRecurring' => false,
                        'IgnoreDuplicate' => false,
                        'CustReceipt' => $this->custreceipt,
                        'Details' => $this->getTransactionDetails()
                    )
                )
            );
            // setTransactionResult called for success
            if (!empty($transactionResult = $transaction->runTransactionResult)) {
                return $this->setTransactionResult($transactionResult);
            }
        } catch (SoapFault $ex) {
            $this->error = $ex->getMessage();
            $this->ebiz_log('Error: ' . $this->error);
            return false;
        }

        return false;
    }

    /**
     * Search Ebiz customer
     * @param int $customerId - wp customer id
     * Return false | customer object
     */
    function SearchCustomer($customerId)
    {
        $client = new SoapClient($this->_getWsdlUrl(), $this->SoapParams());
        $ebizCustomer = false;

        try {
            // find CustomerInternalId using SearchCustomers ebiz method
            $searchCustomer = $client->SearchCustomers(array(
                    'securityToken' => $this->_getUeSecurityToken(),
                    'customerId' => $customerId,
                    'start' => 0,
                    'limit' => 1
                )
            );

            if (isset($searchCustomer->SearchCustomersResult->Customer)) {
                $ebizCustomer = $searchCustomer->SearchCustomersResult->Customer;

                $this->ebiz_log(__METHOD__ . ', customer found. WP customerId: ' . $customerId);
            }

        } catch (SoapFault $ex) {
            $this->ebiz_log(__METHOD__ . ', Error: ' . $ex->getMessage());

            return $this->error = 'SoapFault: SearchCustomers' . $ex->getMessage();
        }

        return $ebizCustomer;
    }

    public function getCustomerPaymentMethods($userId)
    {
        $ebizCustomerNumber = get_user_meta($userId, 'CustNum', true);
        if (!empty($ebizCustomerNumber)) {
            $client = new SoapClient($this->_getWsdlUrl(), $this->SoapParams());

            try {
                $methodProfiles = $client->getCustomerPaymentMethodProfiles(
                    array(
                        'securityToken' => $this->_getUeSecurityToken(),
                        'customerToken' => $ebizCustomerNumber
                    ));

                if (!isset($methodProfiles->GetCustomerPaymentMethodProfilesResult->PaymentMethodProfile)) {
                    $paymentMethods = array();
                } else if (count($methodProfiles->GetCustomerPaymentMethodProfilesResult->PaymentMethodProfile) > 1) {
                    $paymentMethods = $methodProfiles->GetCustomerPaymentMethodProfilesResult->PaymentMethodProfile;
                } else {
                    $paymentMethods[] = $methodProfiles->GetCustomerPaymentMethodProfilesResult->PaymentMethodProfile;
                }

                return $paymentMethods;

            } catch (Exception $ex) {
                $this->error = $ex->getMessage();
                $this->ebiz_log('Error: ' . $this->error);
                return array();
            }
        }

        return array();
    }

    //------------------- Developer New Functions Added End -------------------

    /**
     * Tokenization customer checkout.<br>
     * Add customer to gateway and process the transaction
     *
     * @param $wpCustomerId current customer ID
     * @param $saveInfo
     *
     * @return boolean
     */
    function TokenProcess($wpCustomerId, $saveInfo)
    {
        $this->ebiz_log(__METHOD__ . '  customerId: ' . $wpCustomerId);

        $securityToken = $this->_getUeSecurityToken();
        $client = new SoapClient($this->_getWsdlUrl(), $this->SoapParams());

        try {

            if ($ebizCustomer = $this->SearchCustomer($wpCustomerId)) {
                // customer already exist
                if (!empty($saveInfo)) { // want to save payment method
                    wc_add_notice(__('Customer already exist, Unable to save payment method.', 'ebizcharge'), 'error');
                } else {
                    return $this->RunTransaction();
                }

            } else {
                //  If customer not exist in the gateway, add customer
                $this->ebiz_log(__METHOD__ . ' customer not exist in the gateway, add customer.');

                $customerResult = $client->AddCustomer(array(
                    'securityToken' => $securityToken,
                    'customer' => $this->getCustomerData($wpCustomerId)
                ));

                $ebizCustomer = isset($customerResult->AddCustomerResult) ? $customerResult->AddCustomerResult : false;

                if ($ebizCustomer && $this->enableEconnect) {
                    $ebiz = new WC_ebizcharge();
                    $econnect = $ebiz->_initTransaction(true);

                    $econnect->updateWPTable($econnect->user_table, $ebizCustomer, array('ID' => (int)$wpCustomerId));

                    $this->ebiz_log(__METHOD__ . ' Econnect enabled. sync customer.');
                }

                //add customer payment method
                $paymentMethod = $client->addCustomerPaymentMethodProfile(
                    array(
                        'securityToken' => $securityToken,
                        'customerInternalId' => $ebizCustomer->CustomerInternalId,
                        'paymentMethodProfile' => $this->getCustomerPayment()
                    ));

                $paymentMethodId = $paymentMethod->AddCustomerPaymentMethodProfileResult;
                $this->methodID = $paymentMethodId;

                // The  ebiz cusNum should be available in API customer Object to save this request
                if ($ebizCustomerNumber = $this->getCustomerToken($ebizCustomer)) {
                    update_user_meta($wpCustomerId, 'CustNum', $ebizCustomerNumber);

                    $this->ebiz_log(__METHOD__ . ' CustNum updated: ' . $ebizCustomerNumber);
                }

                $transactionResult = $client->runCustomerTransaction(
                    array(
                        'securityToken' => $securityToken,
                        'custNum' => $ebizCustomerNumber,
                        'paymentMethodID' => $paymentMethodId,
                        'tran' => $this->getCustomerTransactionRequest()
                    ));

                $transaction = $transactionResult->runCustomerTransactionResult;

                if (isset($transaction)) {
                    return $this->setTransactionResult($transaction);
                }
            }

        } catch (SoapFault $ex) {
            $this->error = $ex->getMessage();
            $this->ebiz_log('Error: ' . $this->error);
            return $this->error;
        }

        return false;
    }

    /**
     * Add new payment method and process the transaction
     *
     * @param int $ebizCustomerId eBizCharge Customer ID
     * @return boolean
     */
    function NewPaymentProcess($ebizCustomerId, $wpCustomerId, $saveCardInfo)
    {
        $this->ebiz_log('Transaction using ' . __METHOD__);

        $securityToken = $this->_getUeSecurityToken();
        $client = new SoapClient($this->_getWsdlUrl(), $this->SoapParams());

        try {
            // customer don't want to save payment method. use RunTransaction
            if (empty($saveCardInfo)) {
                $this->ebiz_log(__METHOD__ . ' save card info empty.');
                return $this->RunTransaction();

            } else {
                // find customer using SearchCustomers ebiz method
                $ebizCustomer = $this->SearchCustomer($wpCustomerId);
                if ($ebizCustomer) {
                    // If local and live CustNum match add payment method start
                    if ($this->getCustomerToken($ebizCustomer) == $ebizCustomerId) {
                        $this->ebiz_log(__METHOD__ . ' local and live CustNum matched.');

                        try {
                            $paymentMethod = $client->addCustomerPaymentMethodProfile(
                                array(
                                    'securityToken' => $securityToken,
                                    'customerInternalId' => $ebizCustomer->CustomerInternalId,
                                    'paymentMethodProfile' => $this->getCustomerPayment()
                                )
                            );

                            $paymentMethodId = $paymentMethod->AddCustomerPaymentMethodProfileResult;
                            $this->methodID = $paymentMethodId;

                            $transactionResult = $client->runCustomerTransaction(
                                array(
                                    'securityToken' => $securityToken,
                                    'custNum' => $ebizCustomerId,
                                    'paymentMethodID' => $paymentMethodId,
                                    'tran' => $this->getCustomerTransactionRequest()
                                ));

                            $transaction = $transactionResult->runCustomerTransactionResult;

                            if (isset($transaction)) {
                                return $this->setTransactionResult($transaction);
                            }
                        } catch (SoapFault $ex) {
                            $this->error = $ex->getMessage();
                            $this->ebiz_log('Error: ' . $this->error);
                            return $this->error;
                        }

                    } else {
                        // Local and live CustNum not matched, use RunTransaction
                        $this->ebiz_log(__METHOD__ . ' Local and live CustNum not matched, use RunTransaction.');
                        return $this->RunTransaction();
                    }

                } else {
                    //Ebiz customer not found, add customer and add payment method
                    $this->ebiz_log(__METHOD__ . ' Ebiz customer not found, add customer and add payment method.');

                    $customerResult = $client->AddCustomer(array(
                        'securityToken' => $securityToken,
                        'customer' => $this->getCustomerData($wpCustomerId)
                    ));

                    $ebizCustomer = $customerResult->AddCustomerResult;
                    if ($ebizCustomer && $this->enableEconnect) {
                        $this->ebiz_log(__METHOD__ . ' Econnect enabled. sync customer.');

                        $ebiz = new WC_ebizcharge();
                        $econnect = $ebiz->_initTransaction(true);
                        $econnect->updateWPTable($econnect->user_table, $ebizCustomer, array('ID' => (int)$wpCustomerId));
                    }

                    //add customer payment method
                    $paymentMethod = $client->addCustomerPaymentMethodProfile(
                        array(
                            'securityToken' => $securityToken,
                            'customerInternalId' => $ebizCustomer->CustomerInternalId,
                            'paymentMethodProfile' => $this->getCustomerPayment()
                        ));

                    $paymentMethodId = $paymentMethod->AddCustomerPaymentMethodProfileResult;
                    $this->methodID = $paymentMethodId;

                    if ($ebizCustomerNumber = $this->getCustomerToken($ebizCustomer)) {
                        update_user_meta($wpCustomerId, 'CustNum', $ebizCustomerNumber);
                        update_user_meta($wpCustomerId, 'customer_vault_ids', '');

                        $this->ebiz_log(__METHOD__ . ' Customer token updated to. ' . $ebizCustomerNumber);
                    }

                    $transactionResult = $client->runCustomerTransaction(
                        array(
                            'securityToken' => $securityToken,
                            'custNum' => $ebizCustomerNumber,
                            'paymentMethodID' => $paymentMethodId,
                            'tran' => $this->getCustomerTransactionRequest()
                        ));

                    $transaction = $transactionResult->runCustomerTransactionResult;

                    if (isset($transaction)) {
                        return $this->setTransactionResult($transaction);
                    }
                }
            }

        } catch (SoapFault $ex) {
            $this->error = $ex->getMessage();
            $this->ebiz_log('Error: ' . $this->error);
            return $this->error;
        }

        return false;
    }

    /**
     * Process a transaction from saved payment method
     *
     * @param int $ebizCustomerId eBizCharge Customer ID
     * @param int $ebizMethodId eBizCharge Payment method ID
     *
     * @return boolean
     */
    function SavedProcess($ebizCustomerId, $ebizMethodId)
    {
        $this->ebiz_log('Transaction using ' . __METHOD__);
        $client = new SoapClient($this->_getWsdlUrl(), $this->SoapParams());
        try {
            $transactionResult = $client->runCustomerTransaction(
                array(
                    'securityToken' => $this->_getUeSecurityToken(),
                    'custNum' => $ebizCustomerId,
                    'paymentMethodID' => $ebizMethodId,
                    'tran' => $this->getCustomerTransactionRequest()
                ));

            $transaction = $transactionResult->runCustomerTransactionResult;

            if (isset($transaction)) {
                return $this->setTransactionResult($transaction);
            }

        } catch (SoapFault $ex) {
            $this->error = $ex->getMessage();
            $this->ebiz_log('Error: ' . $this->error);
            return $this->error;
        }

        return false;
    }

    function RunTransaction()
    {
        $this->ebiz_log('Transaction using ' . __METHOD__);
        try {
            $client = new SoapClient($this->_getWsdlUrl(), $this->SoapParams());
            $transaction = $client->runTransaction(
                array(
                    'securityToken' => $this->_getUeSecurityToken(),
                    'tran' => $this->getTransactionRequest()
                )
            );
            // setTransactionResult called for success
            $transactionResult = $transaction->runTransactionResult;
            if (!empty($transactionResult)) {
                return $this->setTransactionResult($transactionResult);
            }
        } catch (SoapFault $ex) {
            $this->error = $ex->getMessage();
            $this->ebiz_log('Error: ' . $this->error);
            return $this->error;
        }

        return false;
    }

    public function getCustomerToken($ebizCustomer)
    {
        $securityToken = $this->_getUeSecurityToken();
        $client = new SoapClient($this->_getWsdlUrl(), $this->SoapParams());
        // The  ebiz cusNum should be available in API customer Object to save this request
        try {
            $customerToken = $client->GetCustomerToken(
                array(
                    'securityToken' => $securityToken,
                    'customerInternalId' => $ebizCustomer->CustomerInternalId,
                    'CustomerId' => $ebizCustomer->CustomerId
                ));

            return isset($customerToken->GetCustomerTokenResult) ? $customerToken->GetCustomerTokenResult : false;

        } catch (SoapFault $ex) {
            return false;
        }
    }
}
