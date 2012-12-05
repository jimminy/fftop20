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

#Feature Flags
function features_set($query=null, $username){
  $flag = split($query, '&');
  foreach($flags as $flag){
    $values = split($flag, '=');
    $key = $values['0'];
    $val = $values['1'];
  }
  return array();
}

function image_create($path, $img_array, $keys){
  $width=262;
  $height=50;
  $num = 5;
  $padding = 3;

  $img_count = count($img_array)/$num;
  $img_width = ($width - ($padding*($num-1)))/$num;
  $offset = $img_width + $padding;

  $photo_array = '';

  $cnt = 0;
  while($cnt<$img_count){
    $image = @imagecreatetruecolor($width, $height);
    $bg = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $bg);

    $arr_start = $cnt*$num;

    $imgs = array_slice($img_arry, $arr_start, $num);
    $key_list = array_slice($keys, $arr_start, $num);

    $start = 0;
    $name = '';
    while($start<$num){
      $src = imagecreatefromjpeg($key_list[$start]);
      imagecopy($image, $src, $offset*$start, 0, 0, 0, 50, 50);
      $name.=$key_list[$start];
      imagedestroy($src);
    }

    $date = date("md");
    $url = $path.'tmp/'.$date.$name.'jpg';
    imagejpeg($image, $url);
    imagedestroy($image);

    if($cnt<$img_count-1){
      $photo_array.= $url.',';
    }
    else{
      $photo_array.= $url;
    }

    $cnt = $cnt+1;
  }

  return $photo_array;
}
?>