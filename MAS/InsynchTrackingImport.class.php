<?php
require_once "InsynchUtil.class.php";
class InsynchTrackingInventory extends InsynchUtil
{   
    public $updateQueue = array();
        
    function getInvoicesForUpdate($conn,$max = 200)
    {    
        $sql = "SELECT DISTINCT 
                invoice.InvoiceNo,
                DATE_FORMAT(invoice.InvoiceDate,'%Y/%m/%d') AS ShipDate,
                invoice.CustomerPONo AS OrderID,
                detail.`CommentText`,
                invoice.SalesOrderNo
                FROM  ".$this->insynchPrefix."FromMas_AR_InvoiceHistoryHeader invoice
                INNER JOIN ".$this->insynchPrefix."FromMas_AR_InvoiceHistoryDetail detail ON detail.InvoiceNo=invoice.InvoiceNo 
                AND CustomerPONo IN (SELECT entity_id FROM MasOrderHistory)  
                WHERE detail.`CommentText`<> '' AND (CommentText LIKE '1Z%') AND invoice.Processed=0 
                LIMIT $max" ;
        $result = mysqli_query($conn,$sql);
        $i = 0;
        if(mysqli_num_rows($result) > 0)
        {
            while($row = mysqli_fetch_assoc($result))
            {
                $this->updateQueue[$i] = $row;
                $i++;
            }
        }
        else 
        {
            $this->hPrint("No tracking numbers to process");
            return null;
        }
        return $this->updateQueue;
    }

    function processUpdateQueue($conn,$client)
    {   
        $updateQueue = $this->updateQueue;
        foreach ($updateQueue as $invoice) {
            $this->hPrint("Starting processing invoice: ".$invoice["InvoiceNo"]);
            $this->processInvoice($conn,$invoice,$client);      
        }
    }

    function processInvoice($conn,$invoice,$client)
    {   
        $this->updateOrder($invoice, $client,$conn);
        $this->markAsProcessed($conn,$invoice["InvoiceNo"]);
    }

    public function updateOrder($invoice,$client,$conn)
    {    
            $sql = "SELECT ItemCode,QuantityShipped,lotnum1,lotnum2,lotnum3,lotqty1,lotqty2,lotqty3
                    FROM FromMas_AR_InvoiceHistoryDetail
                    WHERE InvoiceNo = ".$invoice["InvoiceNo"]." AND QuantityShipped > 0 ";
            $result = mysqli_query($conn,$sql); 
            $i=0;
            $note = "Invoice#: ".$invoice["InvoiceNo"]."\r\n Ship Date: ".$invoice["ShipDate"]."\r\nTracking#:".$invoice["CommentText"]."\r\n Invoice Detail:\r\n";
            while($row = mysqli_fetch_assoc($result))
            { 
                $itemcode=$row["ItemCode"];
                $qty=intval($row["QuantityShipped"]);
                $lotnum1= $row["lotnum1"];
                $lotnum2=$row["lotnum2"];
                $lotnum3=$row["lotnum3"];
                $lotqty1=intval($row["lotqty1"]);
                $lotqty2=intval($row["lotqty2"]);
                $lotqty3=intval($row["lotqty3"]);
                $note .= $itemcode ." x ".$qty."\r\n";
                if($lotqty1 > 0)
                {
                    $note.= "- Lot ".$lotnum1 ." x ".$lotqty1."\r\n";                
                }
                if($lotqty2 > 0)
                {
                    $note.= "- Lot ".$lotnum2 ." x ".$lotqty2."\r\n";                
                }
                if($lotqty3 > 0)
                {
                    $note.= "- Lot ".$lotnum3 ." x ".$lotqty3."\r\n";                
                }
            }
           $client->order_notes->create($invoice["OrderID"], array("note" => $note, "customer_note" => true));  
           $client->orders->update_status($invoice["OrderID"],"completed");  
    }
    
    public function markAsProcessed($conn,$id)
    {               
        $sql = "UPDATE ".$this->insynchPrefix."FromMas_AR_InvoiceHistoryHeader SET Processed = 1 WHERE InvoiceNo = '$id'";
        mysqli_query($conn,$sql); 
    }   
}                                                        
?>