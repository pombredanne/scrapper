<!-------------------------------------------------------------------------------
// php version 4.0
// WebSite Scrapping Based on Pattern of Table Structure [table][th][tr][td] etc
// Author: Kailash Budhathoki 
// E-mail: kailash.buki@gmail.com | Website: http://www.kailashbudhathoki.com.np
-------------------------------------------------------------------------------->

<?php

class parser{
 
    var $server_name = 'localhost';
    var $user_name = 'root';
    var $password = '';
    var $database_name = 'parsedData';
    
    var $file_name = 'table.html';
    var $patternfile_name = 'pattern.txt';
    
    var $column_count;
    var $row_count;
    var $table_header;
    var $table_column;
    var $table_data;
    var $pattern_array;
    var $data_type;
    var $data_start_depth;
    
    function parser(){
        
        $table_header  = array();
        $table_column  = array();
        $table_data    = array();
        $pattern_array = array();
        $data_type     = array();
        
    }
       
    function spider(){            
        
   	    error_reporting(0);                         // Disables Warning Messages
        
        $dom = new DomDocument();
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadHTMLFile($this->file_name);
        $dom_node = $dom->getElementsByTagName('table'); 
        
        $k=0;
        foreach($dom_node as $p){ 
            echo '<fieldset><legend style="color:blue;">Table Number '.$k.'</legend>';
            echo $dom_node->item($k)->nodeValue.'</fieldset><br/>';
            $k++;
        }         
        
        // Read the required Table number from the user preferably using FORM & AJAX
        $table_number = 16;    
         
         echo '<fieldset><legend style="color:red;"><b>Table of our Concern</b></legend>';
         echo '<fieldset><legend style="color:blue;">Table Number '.$table_number.'</legend>';
         echo '<div style="background-color:black;color:white;">'.$dom_node->item($table_number)->nodeValue.'</div></fieldset></fieldset>';
            
        // Read the pattern from the pattern file where pattern of the table is provided, e.g Pattern 
        $this->read_pattern();
        
        // Say if there is table header extract it 
        if($this->pattern_array[2] == 1)
            $this->extract_header($dom_node, $table_number);
        if($this->pattern_array[4] == 1)
            $this->extract_column($dom_node, $table_number);
        if($this->pattern_array[6] != 0)
            $this->extract_data($dom_node, $table_number);
        
        $this->putinto_database($dom_node, $table_number);    
        
    }
     
    function putinto_database($dom_node, $table_number){
        
        mysql_connect($this->server_name, $this->user_name, $this->password);
        //mysql_query("DROP DATABASE `".$this->database_name."`") or die(mysql_error()." in database");
        //mysql_query("CREATE DATABASE `".$this->database_name."`") or die(mysql_error()." in database");
        mysql_select_db($this->database_name) or die(mysql_error()." in database");
        
        if($this->table_header[0])
            $table_name = $this->table_header[0];
        else 
            $table_name = 'table'.$table_number;
        $table_name.=date("H-i-s");
        $query_elements = array();
        $_field_name = "";
        $_data_type  = "";
        
        for( $i=0; $i<$this->column_count; $i++ ){
            $_field_name = $this->table_column[$i];
            $_data_type = $this->data_type[$i];
            $query_elements[] = "`{$_field_name}` {$_data_type} NOT NULL";  
        }
        
        $query_elements = implode(", ", $query_elements);
        $query = "CREATE TABLE `$table_name` ( ". $query_elements ." ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";            
        mysql_query($query) or die(mysql_error());
        
        $fields = "`".implode("`, `", $this->table_column)."`";
        for($i = 0; $i<$this->row_count; $i++){ 
            for($j = 0; $j<$this->column_count; $j++){
                $val[$i][$j] = $dom_node->item($table_number)->childNodes->item($i+$this->data_start_depth)->childNodes->item(2*$j)->nodeValue;
                // Check if the data is a number with commas
                // Commas are not allowed in database
                if(preg_match('/[0-9\+\-][0-9\,]+/', $val[$i][$j], $match)){
                    $val[$i][$j] = explode(",", $val[$i][$j]);
                    $val[$i][$j] = implode("", $val[$i][$j]);
                }
            }                      
        }
        for($i=0;$i<$this->row_count;$i++){
            $val[$i] = "'".implode("','",$val[$i])."'";    
        }         
        $val = '('.implode("),(",$val).')';    
        $query = "INSERT INTO `$table_name` (".$fields.") VALUES ".$val.";";
        echo $query;
        mysql_query($query) or die(mysql_error());
        if(mysql_affected_rows($query))
            echo '<h3 color=RED>Database Populated</h3>';
    }        
    
    function get_datatype($dom_node, $table_number, $item_depth_start){
        
        for($i = 0; $i<$this->column_count; $i++){
            
            $data = $dom_node->item($table_number)->childNodes->item($item_depth_start)->childNodes->item(2*$i)->nodeValue;    
            echo $data;
            if(is_numeric($data) && is_int($data+0)) 
                $this->data_type[$i] = 'int';
            else if(is_numeric($data) && is_float($data+0.0))
                $this->data_type[$i] = 'float';                   
            else 
                $this->data_type[$i] = 'varchar(255)';
        }
        echo '<br/><b>Data Types</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        var_dump($this->data_type);
            
    }
    function extract_data($dom_node, $table_number){
        
        if($this->pattern_array[2] != 0 && $this->pattern_array[4] != 0) //if both header & column
            $item_depth_start = 2;
        else
            $item_depth_start = 1;
        $this->data_start_depth = $item_depth_start;
        $i = 0; 
        $count = 0;
        $k = $item_depth_start;
        if($this->pattern_array[7] == 2){
            while(strlen($dom_node->item($table_number)->childNodes->item($item_depth_start)->nodeValue) != 0){
                 $this->table_data[$i++] =  $dom_node->item($table_number)->childNodes->item($item_depth_start++)->nodeValue;
                 $this->row_count++;
            }        
        }
        echo '<br/><b>Data </b><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        var_dump($this->table_data);
        
        $this->get_datatype($dom_node, $table_number, $k);
        
    }
    
    function extract_column($dom_node, $table_number){
        
        if($this->pattern_array[2] == 1) // if header exists
            $item_depth = 1;
        else 
            $item_depth = 0; // check this depth value
        if($this->pattern_array[5] == 2){
            echo '2 depth column extracting...';
            $i = 0; 
            $j = 0;      
            echo $dom_node->item($table_number)->childNodes->item(0)->nodeValue;
            while($dom_node->item($table_number)->childNodes->item($item_depth)->childNodes->item(2*$i)->nodeValue != ''){
                $this->table_column[$j] = $dom_node->item($table_number)->childNodes->item($item_depth)->childNodes->item(2*$i)->nodeValue; 
                $i++;   $j++;          
            }
            $this->column_count = $i;
        }
        echo '<br/><b>Column Names</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        var_dump($this->table_column );
    }
    
    function extract_header($dom_node, $table_number){
        
        if($this->pattern_array[3] == 1)
            $this->table_header[] = $dom_node->item($table_number)->childNodes->item(0)->nodeValue;     
        if($this->pattern_array[3] == 2)
            $this->table_header[] = $dom_node->item($table_number)->childNodes->item(0)->childNodes->item(0)->nodeValue;     
        
        echo '<br/><b>Header</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        var_dump($this->table_header);
        
    }
    
    function read_pattern(){
        
        $i = 0;     
        $file_handle = fopen($this->patternfile_name,"r");
        if(!$file_handle)
            echo "<br/>Oops! ".$this->patternfile_name." Couldn't be Opened";
        else{   
            while(!feof($file_handle)){
                $this->pattern_array[$i++] = fgets($file_handle,2); 
            }                               
        }
        fclose($file_handle);
        
    }
    
}
    $start = new parser;
    $start->spider();
    
?>