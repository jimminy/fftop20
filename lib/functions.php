<?php
#Construct URL for making requests
function build_url($path, $type, $data){
  $URI = '';
  if(isset($data)){
    foreach($data as $key=>$val){
      $URI .= '&'.$key.'='.$val;
    }
  }
  $url = $path.'lib/api.php?command='.$type.$URI;
  return $url;
}
?>