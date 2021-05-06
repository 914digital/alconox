<?php
require_once "InsynchOrderExport.class.php";
$conn = dbConnect();
$orderExport = new InsynchOrderExport();
$orderExport->processOrders($conn);
$orderExport->hPrint("Processing completed");
?>