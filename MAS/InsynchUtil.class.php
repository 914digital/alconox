<?php
require_once( 'lib/woocommerce-api.php' );
require_once "includes/conn.inc.php"; 
$conn = dbConnect();

class InsynchUtil
{   
    var $emailNotificationList ='patricia@simplicityc.com';
    var $insynchPrefix='';
    var $storeURL = 'https://www.alconox.com/';
    //var $storeURL = 'https://alconox.flywheelstaging.com/';
    var $consumerKey = 'ck_c93c53a99deb20a1d5c016abe5307815bb9eb705';
    var $consumerSecret = 'cs_198783539797c8c74d25f2b24cdd8f4ad5267803';
    var $options = array(
                'debug'           => true,
                'return_as_array' => false,
                'validate_url'    => false,
                'timeout'         => 30,
                'ssl_verify'      => false,
            );
    
    function hPrint($text) {
        echo(microtime(true));
        echo ("&nbsp;$text<br/>");
    }
    
    function sendErrorEmail($text)
    {    
        $subject = "Error in Woo Commerce MAS Integration";
        $message = "$text";
        $headers = 'From: website@simplicityc.com' . "\r\n" .
        'Reply-To: website@simplicityc.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
        if(mail($this->emailNotificationList, $subject, $message, $headers))
        {
            $trackingImport->hPrint("Error email was sent");
        }
        else
        {
            $trackingImport->hPrint("Error email couldn't be sent");
        }     
    }
    
    function sendErrorNotificationEmail($conn, $entityType, $entityID, $text)
    {
        $sql =    "SELECT COUNT(*) AS TotalMessages
        FROM ".$this->insynchPrefix."MasErrorNotificationLog
        WHERE EntityType='$entityType' AND EntityID = $entityID";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        if ($row["TotalMessages"]==0)
        {
            $qtext=addslashes($text);

            $sql="INSERT INTO ".$this->insynchPrefix."MasErrorNotificationLog(EntityType, EntityID, RecipientList, MessageText,DateReported)
                  VALUES ('$entityType','$entityID','".$this->emailNotificationList."','$qtext', '".date('Y-m-d-h:i:s')."')";
            mysqli_query($sql);
            $this->sendErrorEmail($text);
        }
        else
        {
            $this->hPrint("Error was documented before");
        }
    }
}
?>
