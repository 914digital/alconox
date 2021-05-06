<?php
function dbConnect() {
    $server="192.168.0.222";
    $user="fw3659257982";
    $pwd="V36h1SaiuSzB3M5UZVq6QiQndfq5am";
    $db="db6100109753";   
    $conn = mysqli_connect($server, $user, $pwd) or die ('Cannot connect to server');    
    mysqli_select_db($conn,$db) or die ('Cannot open database');
    return $conn;    
}
?>
