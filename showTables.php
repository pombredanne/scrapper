<?php
    session_start();
    include("parser.php");
    ob_start(); 
    $url = $_POST['url'];
            
    if($url == NULL)
        header("Location: home.php");// redirect the page
    echo '<p style="border-bottom:#ccc thin solid; font-weight:bold;"><a style="color:#56c;" href="home.php">Home</a></p>';
    echo '<blockquote align="right"><pre style="color:#F00"><b>URL</b> => '.$url.'</pre></blockquote>';
    
    $auth = base64_encode('063bct512:k');
    $context = array (
         'http' => array (
               'proxy' => '10.200.0.1:8080', 'request_fulluri' => true,
                'header' => "Proxy-Authorization: Basic $auth",
         ),
    );
    
    $context = stream_context_create ($context); 
    $data = file_get_contents($url,0,$context);
    $_SESSION['dataString'] = $data;
       
    $p = new parser;
    $p->spider($data); 
    
    echo '<br/><br/><br/><fieldset><legend style="color:#00F;"><b>Please Provide The Data Below</b></legend>';
    echo '<form method="post" action="grabConcernedTable.php">
        <label>
            <strong>Table Number</strong>
            <select name="table_no">';
                for($i=0;$i<$p->total_table_no;$i++)
                    echo "<option value="."$i>".$i."</option>";
    echo '<input type = "hidden" name = "str" value = "'.base64_encode( serialize( $p ) ).'"';
    echo '</select>
        </label>
        <label>&nbsp;&nbsp;    &nbsp;&nbsp;&nbsp;    &nbsp;<strong> Header     Pattern  </strong></label>
        <label><input type="radio" name="header" value="0" checked="1" />None</label>
        <label><input type="radio" name="header" value="1" />Single</label>
        <label><input type="radio" name="header" value="2" />Double</label> 
        <label>&nbsp;&nbsp;    &nbsp;&nbsp;&nbsp;    &nbsp;<strong> Save As CSV </strong></label>
        <label><input type="radio" name="flag" value="1" />Yes</label> 
        <label><input type="radio" name="flag" value="0" />No</label> 
        <label><br /><br /></label>
        <input type="submit" name="submit" value="Scrap it!" style="-webkit-appearance:button;padding:0 8px;border:1px solid #999;-webkit-border-radius:2px;background:-webkit-gradient(linear,left top,left bottom,from(#fff),to(#ddd));font-size:15px;height:1.85em!important;margin:.2em;" />
    </form>';
    echo '</fieldset>';
    ob_end_flush();
?>
