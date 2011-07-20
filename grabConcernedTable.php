<?php
    session_start();
    include("parser.php");
    
    echo '<p style="border-bottom:#ccc thin solid; font-weight:bold;"><a style="color:#56c;" href="home.php">Home</a></p>';
    $p = unserialize( base64_decode( $_POST["str"] ) );
    $file = $_SESSION['dataString'];
	
    if($_POST['header']==0)
        $pattern="11001212";
    else if($_POST['header']==1)
        $pattern="11111212";
    else if($_POST['header']==2)
        $pattern="11121212";
    
    $flag = $_POST['flag'];
    $table_number = $_POST['table_no'];
	 
    $p->table_concern($file, $table_number, $flag, $pattern);
    
?>
