<?php
  function my_select_db($conn,$dbname){  
      if(!function_exists('mysql_select_db')){
        return mysqli_select_db($conn,$dbname);  }
      else{
         return mysql_select_db($dbname); 
      }
    }
if(!function_exists('mysql_connect')){  
    function mysql_connect($host,$user,$passwd){  
        return mysqli_connect($host,$user,$passwd);  
    } 
    function mysql_fetch_field($result){
        return mysqli_fetch_field($result);
    }
    function mysql_insert_id($link){
        return mysqli_insert_id($link);
    }
    
   function mysql_pconnect($host,$user,$passwd){  
        return mysqli_connect($host,$user,$passwd);  
    }  
    function mysql_close($cxn=null){  
        return $cxn->close();  
    }  
  function mysql_affected_rows($cxn=null){  
        return mysqli_affected_rows($cxn);  
    }  
    function mysql_errno($cxn=null){  
        return mysqli_errno($cxn);  
    }  
  
  
    function mysql_error($cxn=null){  
        return mysqli_error($cxn);  
    }  
  function mysql_free_result($re){
      return mysqli_free_result($re);
  }
  function mysql_list_fields($dbName,$tbname,$link){
      $query = "SHOW COLUMNS FROM ".$tbname;
      $finfo=array();
      if ($result = mysqli_query($link, $query)) {      
    /* Get field information for all columns */
    $finfo = mysqli_fetch_fields($result);
      mysqli_free_result($result);}
      return $finfo;
  }
    function mysql_fetch_array($result){  
        return mysqli_fetch_array($result);  
    }  
    function mysql_fetch_object($result){  
        return mysqli_fetch_object($result);  
    }  
    function mysql_list_tables($result){  
        return mysqli_list_tables($result);  
    }  
    function mysql_fetch_assoc($result){  
        return mysqli_fetch_assoc($result);  
    }  
  
  
    function mysql_fetch_row($result){  
        return mysqli_fetch_row($result);  
    }  
  
   
   
    function mysql_num_rows($result){  
        return mysqli_num_rows($result);  
    }  
  
  
    function mysql_query($sql,$cxn){  
        return mysqli_query($cxn,$sql);  
    }  
  
  
    function mysql_real_escape_string($data,$link=NULL){  
        
        return mysqli_real_escape_string($link,$data);  
    }  
  
  
    function  mysql_get_server_info($cxn){  
        return  mysqli_get_server_info($cxn);  
    }  
  
  
    function mysql_ping($cxn){  
        return mysqli_ping($cxn);  
    }  
}  