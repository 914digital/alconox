<?php
require_once "InsynchTrackingImport.class.php";
$queue = new InsynchTrackingInventory();
$conn = dbConnect();
try
{   
    $client = new WC_API_Client($queue->storeURL, $queue->consumerKey, $queue->consumerSecret, $queue->options);
}
catch ( WC_API_Client_Exception $e ) 
{
    echo $e->getMessage() . PHP_EOL;
    echo $e->getCode() . PHP_EOL;
    if ($e instanceof WC_API_Client_HTTP_Exception) 
    {
        print_r( $e->get_request() );
        print_r( $e->get_response() );
    }
} 
$queue->getInvoicesForUpdate($conn,25);
$queue->processUpdateQueue($conn,$client);
echo("Processing completed");
?>