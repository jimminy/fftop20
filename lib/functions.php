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

function image_create($file_path, $path, $photos, $keys){
  $num = 5;

  $img_count = 4;
  $offset = 53;

  $photo_array = '';

  $cnt = 0;
  while($cnt<$img_count){
    $image = @imagecreatetruecolor(262, 50);
    $bg = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $bg);

    $arr_start = $cnt*$num;

    $imgs = array_slice($photos, $arr_start, $num);
    $key_list = array_slice($keys, $arr_start, $num);

    $start = 0;
    $name = '';
    while($start<$num){
      $src = imagecreatefromjpeg($imgs[$start]);
      imagecopy($image, $src, $offset*$start, 0, 0, 0, 50, 50);
      $name.=$key_list[$start];
      imagedestroy($src);
      $start= $start+1;
    }
    $segment= date("md").$name;

    $url = $file_path.'tmp/'.$segment.'.jpg';
    $ourFileHandle = fopen($url, 'w+') or die("can't open file");
    fclose($ourFileHandle);

    imagejpeg($image, $url);
    imagedestroy($image);
    $url = $path.'tmp/'.$segment.'.jpg';

    if($cnt<$img_count){
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