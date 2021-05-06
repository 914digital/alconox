<?php
require_once "InsynchUtil.class.php";
class InsynchOrderExport extends InsynchUtil{  
    /*Worker functions*/
    function processOrders($conn) {
        try
        {   
            $client = new WC_API_Client($this->storeURL, $this->consumerKey, $this->consumerSecret, $this->options);
            $morePages = true;
            $page = 0;
            while($morePages == true)
            {
                $page++;
                $response = $client->orders->get(null, array( 'status' => 'processing','page'=>$page,'orderby' => 'date','filter[orderby]' => 'date','filter[order]' => 'ASC','filter[created_at_min]' => date('Y-m-d',strtotime("-3 days"))." 00:00:00"));
                if($response->orders == null)
                {
                    $morePages = false;
                }
                foreach($response->orders as $order)
                {
                    if(!$this->OrderWasProcessed($conn, $order->id) && $order->id > 21440)
                    {
                        $this->hPrint("Starting processing on order: ".$order->id."");
                        $this->processOrder($conn, $order);      
                    }
                }
            }
        }
        catch ( WC_API_Client_Exception $e ) 
        {
            echo $e->getMessage() . PHP_EOL;
            echo $e->getCode() . PHP_EOL;
            if ( $e instanceof WC_API_Client_HTTP_Exception ) 
            {
                print_r( $e->get_request() );
                print_r( $e->get_response() );
            }
        }
    }
    
    function processOrder($conn, $order) {
        $MasSalesOrderNo = $this->generateMasSalesOrderNo($order->id);
        $this->preOrderCleanup($conn, $MasSalesOrderNo);
        $company='XXX';
      //  $company = $this->getCompanyCode($conn, $order->shipping_address->state);
        if(!$this->processOrderItems($conn, $order, $MasSalesOrderNo, $company))
        {
            $this->hPrint("Error processing order items");
            $this->sendErrorNotificationEmail("Item", $orderId, "Error processing items for order number  ".$order->id." \r\n");
            return false;
        }
        
         if(!$this->processComment($conn, $order, $MasSalesOrderNo, $company))
        {
            $this->hPrint("Error processing order items");
            $this->sendErrorNotificationEmail("Item", $orderId, "Error processing items for order number  ".$order->id." \r\n");
            return false;
        }
        if(!$this->processOrderHeader($conn, $order, $MasSalesOrderNo, $company))
        {
            $this->hPrint("Error processing order header");
            $this->sendErrorNotificationEmail("Order", $orderId, "Error processing order header for order ".$order->id." \r\n");
            return false;
        }
        $this->markOrderAsProcessed($conn, $order->id, $MasSalesOrderNo);
        $this->hPrint("Proccessing completed for this order");
        return true;
    }
    

             
    function markOrderAsProcessed($conn, $orderId, $MasSalesOrderNo) {
        $sql = "INSERT INTO ".$this->insynchPrefix."MasOrderHistory (entity_id, IncrementID) values ($orderId ,'$MasSalesOrderNo')";
        return (mysqli_query($conn,$sql));
    }                           
    /*End Worker functions*/

    /*Database writing functions*/
    function preOrderCleanup($conn, $MasSalesOrderNo) {
        $sql= "DELETE FROM ".$this->insynchPrefix."ToMas_SO_SalesOrderDetail WHERE SalesOrderNo='$MasSalesOrderNo'";
        $sql2="DELETE FROM ".$this->insynchPrefix."ToMas_SO_SalesOrderHeader WHERE SalesOrderNo='$MasSalesOrderNo'";
        mysqli_query($conn,$sql);
        mysqli_query($conn,$sql2);
    }
          
    function processOrderItems($conn, $order, $MasSalesOrderNo, $company) {    
                                      
        foreach($order->line_items as $item)
        {   
              if(substr($item->sku,-2)==-1)
            { 
              $UnitOfMeausure='EACH';
            }
            else
             {
             if(substr($item->sku,-2)==15 or substr($item->sku,-2)==30 or substr($item->sku,-2)==55 ) 
             {
                 
              $UnitOfMeausure='DRUM';
             }
             else
               {
                   $UnitOfMeausure='CASE';
             }
            }      
                
            $sql = "INSERT INTO ".$this->insynchPrefix."ToMas_SO_SalesOrderDetail
               (SalesOrderNo
               ,SequenceNo
               ,ItemCode
               ,ItemType
               ,QuantityOrderedOriginal
               ,OriginalUnitPrice
               ,UnitOfMeasure
               ,CompanyCode)
               VALUES
               ('$MasSalesOrderNo',
               '".$item->id."',
               '".$item->sku."',
               '1',
               ".$item->quantity.",
               ".number_format($item->subtotal/$item->quantity,2,".","").",
               '$UnitOfMeausure',
               '$company')";
            $sqlLog = str_replace("ToMas_SO_SalesOrderDetail","ToMas_SO_SalesOrderDetailLog", $sql);
            mysqli_query($conn,$sqlLog);
            mysqli_query($conn,$sql);
        }
        return true;
    }
    
    
    
           
    function processComment($conn, $order, $MasSalesOrderNo, $company) {    
                                      
        foreach($order->line_items as $item)
        {   
          if($order->note!=NULL)
          {  
            $note= $order->note;
                
            $sql = "INSERT INTO ".$this->insynchPrefix."ToMas_SO_SalesOrderDetail
              (SalesOrderNo
               ,SequenceNo
               ,ItemCode
               ,ItemType 
               ,CommentText                              
               ,CompanyCode)
               VALUES
               ('$MasSalesOrderNo',
               '999998',
               '/C',   
               '1',
              '$note',                                    
               '$company')";
            $sqlLog = str_replace("ToMas_SO_SalesOrderDetail","ToMas_SO_SalesOrderDetailLog", $sql);
            mysqli_query($conn,$sqlLog);
            mysqli_query($conn,$sql);
            
             }
           
        }
        return true;
    }
    
    
        function processOrderHeader($conn, $order, $MasSalesOrderNo, $company) {  
        //Getting customer number and division
        $sqlCustomer = "SELECT CustomerNo, ArDivisionNo
                        FROM ".$this->insynchPrefix."FromMas_AR_Customer 
                        WHERE EmailAddress = '".$order->billing_address->email."' AND CompanyCode = '$company'
                        LIMIT 1";
        $result = mysqli_query($conn,$sqlCustomer);             
          
        $ArDivisionNo = "00";
        $CustomerNo = "";
        if($row = mysqli_fetch_assoc($result))
        {
            $CustomerNo = $row["CustomerNo"];
            $ArDivisionNo = $row["ArDivisionNo"];
        }
        
        $taxSchedule = 'NONTAX';
        $state =  $order->shipping_address->state;
        if($state == 'NY' || $state == 'NJ'){
            $miscTax = "/STAX-".$state;
            if ($order->total_tax > 0) 
            {
            $sql = "INSERT INTO ".$this->insynchPrefix."ToMas_SO_SalesOrderDetail
            (SequenceNo, SalesOrderNo, ItemCode, QuantityOrderedOriginal,OriginalUnitPrice,LastExtensionAmt)
            VALUES
            (999998, '$MasSalesOrderNo', '$miscTax', 1,'".$order->total_tax."','".$order->total_tax."')";
            $sqlLog = str_replace("ToMas_SO_SalesOrderDetail","ToMas_SO_SalesOrderDetailLog", $sql);
            mysqli_query($conn, $sqlLog);
            mysqli_query($conn,$sql);
            }
        } 
        
      //Credit Card Integration

        $id = $order->id;
        $OrderStatus = "";
       

        $PaymentType ="";
        $TransactionId ="";
        $cardType=0;
        $Token = "";
        $CreditCardInfo ="";
        $CCType = "";
        $AuthorizationAmt = 0;
        $ReferenceNo = "";
        $DepositAmt = 0;
        $authCode ="";
       


        $TransactionId = $this->getTransactionId($conn,$order);
        $customerId = $order->customer_id;
        
        $paymentInfo ="";
        
           
            $authCode =  $this->getAuthCode($conn,$id);
            $Token =  $this->getToken($conn,$customerId,$id);
            $paymentInfo = $this->getPaymentInfoFromEbizAPI($Token); 
        
        $CardholderName="";
        $ExpirationDateYear="";
        $ExpirationDateMonth="";
        $Last4UnencryptedCreditCardNos="";
        
        if($paymentInfo != '' && $paymentInfo["CardType"] != null){
             $name = explode("-",$paymentInfo["MethodName"]);
             $CardholderName= $name[1];
             $card = explode("-", $paymentInfo["CardExpiration"]);
             $ExpirationDateYear=$card[0];
             $ExpirationDateMonth=$card[1];
             $Last4UnencryptedCreditCardNos= substr($paymentInfo["CardNumber"], -4);
             $cardType = $this->getCardType($paymentInfo["CardType"]);
        }
        else
        {
            $sql = "SELECT DISTINCT meta_value FROM wp_16srpg8aoc_postmeta WHERE post_id = $id AND meta_key like '_card_expiry'";
            $res = mysqli_query($conn,$sql);
            $row = mysqli_fetch_assoc($res);    
            $card = $row["meta_value"];
            $ExpirationDateYear=substr($card,2,2);
            $ExpirationDateMonth=substr($card,0,2);
            $sql = "SELECT DISTINCT meta_value FROM wp_16srpg8aoc_postmeta WHERE post_id = $id AND meta_key like '_card_type'";
            $res = mysqli_query($conn,$sql);
            $row = mysqli_fetch_assoc($res);    
            $cardType = $this->getCardType($row["meta_value"]);
            $sql = "SELECT DISTINCT meta_value FROM wp_16srpg8aoc_postmeta WHERE post_id = $id AND meta_key like '_card_holder'";
            $res = mysqli_query($conn,$sql);
            $row = mysqli_fetch_assoc($res);    
            $CardholderName = $row["meta_value"];
            $sql = "SELECT DISTINCT meta_value FROM wp_16srpg8aoc_postmeta WHERE post_id = $id AND meta_key like '_card_number'";
            $res = mysqli_query($conn,$sql);
            $row = mysqli_fetch_assoc($res);    
            $Last4UnencryptedCreditCardNos = substr($row["meta_value"],-4);
        }
        if($cardType==3){
            $PaymentType = "CCSPA";
        }
        else if($cardType == 4 || $cardType == 5){
            $PaymentType = "CCSP";
        }
        
        $AuthorizationAmt = $order->total;
            
        
        $feeAmt = 0;
        foreach($order->fee_lines as $fee)
        {
            $feeAmt += $fee->total;
        }
        
        $FreightAmt = $order->total_shipping + $feeAmt;
        
        $sql = "INSERT INTO ".$this->insynchPrefix."ToMas_SO_SalesOrderHeader
               (SalesOrderNo
               ,OrderDate
               ,OrderStatus
               ,MasterRepeatingOrderNo
               ,ARDivisionNo
               ,CustomerNo
               ,BillToName
               ,BillToAddress1
               ,BillToAddress2
               ,BillToAddress3
               ,BillToCity
               ,BillToState
               ,BillToZipCode
               ,BillToCountryCode
               ,ShipToCode
               ,ShipToName
               ,ShipToAddress1
               ,ShipToAddress2
               ,ShipToAddress3
               ,ShipToCity
               ,ShipToState
               ,ShipToZipCode
               ,ShipToCountryCode
               ,ShipVia
               ,ShipZone
               ,ShipZoneActual
               ,ShipWeight
               ,CustomerPONo
               ,EmailAddress
               ,ResidentialAddress
               ,CancelReasonCode
               ,FreightCalculationMethod
               ,FOB
               ,WarehouseCode
               ,ConfirmTo
               ,Comment
               ,TaxSchedule
               ,TermsCode
               ,TaxExemptNo
               ,RMANo
               ,JobNo
               ,LastInvoiceDate
               ,LastInvoiceNo
               ,CheckNoForDeposit
               ,LotSerialLinesExist
               ,SalespersonDivisionNo
               ,SalespersonNo
               ,SplitCommissions
               ,SalespersonDivisionNo2
               ,SalespersonNo2
               ,SalespersonDivisionNo3
               ,SalespersonNo3
               ,SalespersonDivisionNo4
               ,SalespersonNo4
               ,SalespersonDivisionNo5
               ,SalespersonNo5
               ,EBMUserType
               ,EBMSubmissionType
               ,EBMUserIDSubmittingThisOrder
               ,PaymentType
               ,OtherPaymentTypeRefNo
               ,CorporateCustIDPurchOrder
               ,CorporateTaxOverrd
               ,DepositCorporateTaxOverrd
               ,CardholderName
               ,ExpirationDateYear
               ,ExpirationDateMonth
               ,EncryptedCreditCardNo
               ,Last4UnencryptedCreditCardNos
               ,CreditCardAuthorizationNo
               ,CreditCardTransactionID
               ,AuthorizationDate
               ,AuthorizationTime
               ,AuthorizationCodeForDeposit
               ,CreditCardTransactionIDForDep
               ,PaymentTypeCategory
               ,PayBalanceByCreditCard
               ,FaxNo
               ,CRMUserID
               ,CRMCompanyID
               ,CRMPersonID
               ,CRMOpportunityID
               ,TaxableSubjectToDiscount
               ,NonTaxableSubjectToDiscount
               ,TaxSubjToDiscPrcntTotSubjTo
               ,DiscountRate
               ,DiscountAmt
               ,TaxableAmt
               ,NonTaxableAmt
               ,SalesTaxAmt
               ,CreditCardPreAuthorizationAmt
               ,CommissionRate
               ,SplitCommRate2
               ,SplitCommRate3
               ,SplitCommRate4
               ,SplitCommRate5
               ,Weight
               ,FreightAmt
               ,DepositAmt
               ,CreditCardPaymentBalanceAmt
               ,DepositCorporateSalesTax
               ,CorporateSalesTax
               ,DateCreated
               ,TimeCreated
               ,UserCreatedKey
               ,DateUpdated
               ,TimeUpdated
               ,UserUpdatedKey
               ,ShipToPhone
               ,BillToPhone
               ,WebOrderNumber
               ,CompanyCode
               ,UDF_Channel
               ,Token
               ,CardType)
               SELECT 
               '$MasSalesOrderNo' AS SalesOrderNo,
               '".date('Ymd',strtotime($order->created_at))."' AS OrderDate,
               Null AS OrderStatus,
               Null AS MasterRepeatingOrderNo,
               '$ArDivisionNo' AS ARDivisionNo,
               '$CustomerNo' AS CustomerNo,
               LEFT(CONCAT('".addslashes($order->billing_address->first_name)."',' ','".addslashes($order->billing_address->last_name)."'),30) AS BillToName,
               LEFT('".addslashes($order->billing_address->address_1)."',30) AS BillToAddress1,
               LEFT('".addslashes($order->billing_address->address_2)."',30) AS BillToAddress2,
               LEFT('".addslashes($order->billing_address->company)."',30) AS BillToAddress3,
               '".$order->billing_address->city."' AS BillToCity,
               '".$order->billing_address->state."' AS BillToState,
               '".$order->billing_address->postcode."' AS BillToZipCode,
               (SELECT MasCountryCode FROM MasCountryLookup WHERE `CODE` ='".$order->billing_address->country."') AS BillToCountryCode,
               (SELECT ShipToCode FROM FromMas_SO_ShipToAddress WHERE CustomerNo='$CustomerNo' AND ShipToAddress1= '".addslashes($order->shipping_address->address_1)."') AS ShipToCode,
               LEFT(CONCAT('".addslashes($order->shipping_address->first_name)."',' ','".addslashes($order->billing_address->last_name)."'),30) AS ShipToName,
               LEFT('".addslashes($order->shipping_address->address_1)."',30) AS ShipToAddress1,
               LEFT('".addslashes($order->shipping_address->address_2)."',30) AS ShipToAddress2,
               NULL AS ShipToAddress3,
               '".$order->shipping_address->city."' AS ShipToCity,
               '".$order->shipping_address->state."' AS ShipToState,
               '".$order->shipping_address->postcode."' AS ShipToZipCode,
               (SELECT MasCountryCode FROM MasCountryLookup WHERE `CODE` ='".$order->shipping_address->country."') AS ShipToCountryCode,
               (SELECT MasShipMethod FROM ".$this->insynchPrefix."MasShippingLookup WHERE ShipMethod = '".$order->shipping_methods."') AS ShipVia,
               Null AS ShipZone,
               Null AS ShipZoneActual,
               Null AS ShipWeight,
               '".$order->id."' AS CustomerPONo, 
               '".$order->billing_address->email."' AS EmailAddress,
               Null AS ResidentialAddress,
               Null AS CancelReasonCode,
               Null AS FreightCalculationMethod,
               Null AS FOB,
               '002' AS WarehouseCode,
               Null AS ConfirmTo,
               null AS Comment,
               '$taxSchedule' AS TaxSchedule,
               '01' AS TermsCode,
               Null AS TaxExemptNo,
               Null AS RMANo,
               Null AS JobNo,
               Null AS LastInvoiceDate,
               Null AS LastInvoiceNo,
               Null AS CheckNoForDeposit,
               Null AS LotSerialLinesExist,
               Null AS SalespersonDivisionNo,
               Null AS SalespersonNo,
               Null AS SplitCommissions,
               Null AS SalespersonDivisionNo2,
               Null AS SalespersonNo2,
               Null AS SalespersonDivisionNo3,
               Null AS SalespersonNo3,
               Null AS SalespersonDivisionNo4,
               Null AS SalespersonNo4,
               Null AS SalespersonDivisionNo5,
               Null AS SalespersonNo5,
               Null AS EBMUserType,
               Null AS EBMSubmissionType,
               Null AS EBMUserIDSubmittingThisOrder,
              '$PaymentType' AS PaymentType,
               Null OtherPaymentTypeRefNo,
               Null AS CorporateCustIDPurchOrder,
               Null AS CorporateTaxOverrd,
               Null AS DepositCorporateTaxOverrd,
              '$CardholderName' AS CardholderName,
              RIGHT('$ExpirationDateYear',2) AS ExpirationDateYear,
             '$ExpirationDateMonth' AS ExpirationDateMonth,
              Null AS EncryptedCreditCardNo,
             '$Last4UnencryptedCreditCardNos' AS Last4UnencryptedCreditCardNos,
             '$authCode' AS CreditCardAuthorizationNo,
             '$TransactionId' AS CreditCardTransactionID,
               Null AS AuthorizationDate,
               Null AS AuthorizationTime,
               Null AS AuthorizationCodeForDeposit,
               Null AS CreditCardTransactionIDForDep,
               Null AS PaymentTypeCategory,
               Null AS PayBalanceByCreditCard,
               Null AS FaxNo,
               Null AS CRMUserID,
               Null AS CRMCompanyID,
               Null AS CRMPersonID,
               Null AS CRMOpportunityID,
               Null AS TaxableSubjectToDiscount,
               Null AS NonTaxableSubjectToDiscount,
               Null AS TaxSubjToDiscPrcntTotSubjTo,
               Null AS DiscountRate,
               ".$order->total_discount." AS DiscountAmt,
               Null AS TaxableAmt,
               Null AS NonTaxableAmt,
               Null AS SalesTaxAmt,
               '$AuthorizationAmt' AS CreditCardPreAuthorizationAmt,
               Null AS CommissionRate,
               Null AS SplitCommRate2,
               Null AS SplitCommRate3,
               Null AS SplitCommRate4,
               Null AS SplitCommRate5,
               Null AS Weight,
               $FreightAmt AS FreightAmt,
               0 AS DepositAmt,
               Null AS CreditCardPaymentBalanceAmt,
               Null AS DepositCorporateSalesTax,
               Null AS CorporateSalesTax,
               Null AS DateCreated,
               Null AS TimeCreated,
               Null AS UserCreatedKey,
               Null AS DateUpdated,
               Null AS TimeUpdated,
               Null AS UserUpdatedKey,
               Null AS ShipToPhone,
               Null AS BillToPhone,
               '".$order->id."' AS WebOrderNumber,
               '$company' AS CompanyCode,
               'W' AS UDF_Channel,
               '$Token' AS Token,
               '$cardType' AS CardType";
        $sqlLog = str_replace("ToMas_SO_SalesOrderHeader","ToMas_SO_SalesOrderHeaderLog", $sql);
        mysqli_query($conn, $sqlLog);
        return mysqli_query($conn, $sql);
    }      
    /*End Database Writing Functions*/

    /*Private utility functions*/
    function generateMasSalesOrderNo($id) {
        $prefix='W';
        $result = str_pad(( int ) $id, 7-strlen($prefix), "0", STR_PAD_LEFT);
        $result = "$prefix$result";
        return $result;
    } 
    
    function OrderWasProcessed($conn,$id) {
        $sql = "SELECT * FROM ".$this->insynchPrefix."MasOrderHistory WHERE entity_id = $id";
        $results = mysqli_query($conn, $sql);
        return mysqli_num_rows($results);
    } 
    
     function getToken($conn,$customerid, $orderid){
        $token = "";
        $methodId ="";
        $custnum ="";

        if($customerid == 0)
        {
            $token = "123456789_1";
        }
        else
        {
            $sql = "SELECT DISTINCT meta_value FROM wp_16srpg8aoc_postmeta WHERE post_id = $orderid AND meta_key like '[EBIZCHARGE]|%'";
            $res = mysqli_query($conn,$sql);
            $row = mysqli_fetch_assoc($res);
            $array = explode("|", $row["meta_value"]);
            if($array != null && sizeof($array)>1){
                $methodId = $array[1];
            } 
            $sql = "SELECT meta_value FROM wp_16srpg8aoc_usermeta WHERE user_id = $customerid AND meta_key = 'CustNum'";
            $res = mysqli_query($conn,$sql);
            $row = mysqli_fetch_assoc($res);
            if($row != null){
                $custnum = $row["meta_value"];
            }
            $token = $custnum.'_'.$methodId; 
        }   
        return $token;
    }

    function getAuthCode($conn,$id){
        $authcode = "";
        $sql = "SELECT DISTINCT meta_value FROM wp_16srpg8aoc_postmeta WHERE post_id = $id AND meta_key like '[EBIZCHARGE]|%'";
        $res = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($res);
        $array = explode("|", $row["meta_value"]);
        if($array != null && sizeof($array)>3){
            $authcode = $array[3];
        } 
        return $authcode;
    }

    function getCardType($type){
        $cardType =5;
        
        if($type == "A" || $type == "American Express"){
            $cardType = 3; 
        }
        if($type == "DS" || $type == "Discover"){
            $cardType = 6;
        }
        if($type == "M" || $type == "MasterCard"){
            $cardType = 5;
        }
        if($type == "V" || $type == "Visa"){
            $cardType = 4;
        }
        return $cardType;
    }
    
    function getTransactionId($conn, $order)
    {
        $id = $order->id;
        $PaymentMethod = $order->payment_details->method_id;
        switch ($PaymentMethod) {
            case "ebizcharge":
                $sql = "SELECT meta_value AS TransactionId FROM wp_16srpg8aoc_postmeta WHERE meta_key = '_transaction_id' AND post_id = $id";
                break;
            default:
                return "";    
        }

        $res = mysqli_query($conn,$sql);
        if ($row = mysqli_fetch_assoc($res))
        {
            return $row["TransactionId"];
        }
        else
        {
            return "";
        }
    }
    /*End Private utility functions*/
    
        //Includes eBiz Charge tokenization caode

    function getPaymentInfoFromEbizAPI($token){
        $UserId="alconoxwoo";
        $Password="15Asd36";
        $SecurityId="10ff2a5f-34ea-4cb3-9ce1-5c6ec36909df";
        
        $curl = curl_init();
        $customerInfo = explode("_", (string)$token);
        if(sizeof($customerInfo)< 2){
            return "";
        }
        $token = $customerInfo[0];
        $method = $customerInfo[1];
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://soap.ebizcharge.net/eBizService.svc",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_VERBOSE => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS =>"<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ebiz=\"http://eBizCharge.ServiceModel.SOAP\">\r\n   <soapenv:Header/>\r\n   <soapenv:Body>\r\n      <ebiz:GetCustomerPaymentMethodProfile>\r\n         <!--Optional:-->\r\n         <ebiz:securityToken>\r\n            <!--Optional:-->\r\n            <ebiz:SecurityId>$SecurityId</ebiz:SecurityId>\r\n            <!--Optional:-->\r\n            <ebiz:UserId>$UserId</ebiz:UserId>\r\n            <!--Optional:-->\r\n            <ebiz:Password>$Password</ebiz:Password>\r\n         </ebiz:securityToken>\r\n         <!--Optional:-->\r\n         <ebiz:customerToken>$token</ebiz:customerToken>\r\n         <!--Optional:-->\r\n         <ebiz:paymentMethodId>$method</ebiz:paymentMethodId>\r\n      </ebiz:GetCustomerPaymentMethodProfile>\r\n   </soapenv:Body>\r\n</soapenv:Envelope>",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: text/xml;charset=UTF-8",
                "SOAPAction: http://eBizCharge.ServiceModel.SOAP/IeBizService/GetCustomerPaymentMethodProfile",
                "Cookie: ARRAffinity=2f0a74e692474b38852d52323c27ca2a31fd85ddad77eff4c8b913903f3e624f"
            ),
        ));
        $this->hprint($curl);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error ( $curl );
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = str_replace("s:","", $response);
            $response = str_replace("xmlns=\"http://schemas.xmlsoap.org/soap/envelope/\"","",$response);
            $response = str_replace("xmlnxsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlnxsd=\"http://www.w3.org/2001/XMLSchema\"","",$response);
            $response = str_replace(" xmlns=\"http://eBizCharge.ServiceModel.SOAP\"","", $response);
            $xml = simplexml_load_string($response);
            $data =  $xml->Body->GetCustomerPaymentMethodProfileResponse->GetCustomerPaymentMethodProfileResult;
            //$xml->registerXPathNamespace('s', 'http://schemas.xmlsoap.org/soap/envelope/');
           // $xml->registerXPathNamespace('xsi', 'http://www.w3.org/2001/XMLSchema-instance');
           // $xml->registerXPathNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');
            $array =[
                "MethodType" => $data->MethodType,
                 "MethodID" => $data->MethodID,
                 "MethodName" => $data->MethodName,
                 "SecondarySort" => $data->SecondarySort,
                 "Created" => $data->Created,
                 "Modified" => $data->Modified,
                 "AvsStreet" => $data->AvsStreet,
                 "AvsZip" => $data->AvsZip,
                 "CardExpiration" => $data->CardExpiration,
                 "CardNumber" => $data->CardNumber,
                 "CardType" => $data->CardType,
                 "Balance" => $data->Balance,
                 "MaxBalance" => $data->MaxBalance,
                 
                
            
            ];
            
        }
        return $array;

    }
}
?>
