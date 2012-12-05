<?php
#Make sure errors don't leak sensitive data.
error_reporting(0);
session_start();
require_once('lib/ff_oauth/friendfeedv2.php');
require_once('lib/parallel_requests.php');
require_once('lib/functions.php');
require_once('conf/config.php');

// Check that session variables are set or clear them and restart.
if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
  header('Location: ./clearsessions.php');
}

    $access_token = $_SESSION['access_token'];
    //NOTE: Pass-thru to get data to api.php. Fixed bug with private accounts having empty variables.
    $xx = base64_encode(json_encode($access_token));

  $session= FriendFeed::FriendFeed_OAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token, UA);

  $feed= $session->fetch_home_feed();
  if($feed){
    $logged_in= TRUE;
    $username = $access_token['username'];

    //  Create request URL's for parallel requests.
    while($i<$loop_count){
        $data = array(
          'user' => $username,
          'start' => $i*$num,
          'num' => $num,
          'x' => $xx);

        $urls[] = build_url($path, $command['likes'], $data);
        $urls[] = build_url($path, $command['comments'], $data);
        $urls[] = build_url($path, $command['posts'], $data);
        $i= $i+1;
    }

    $array = array();
    $pg = new ParallelGet($urls);
    $result = $pg->result();
    foreach($result as $key => $val){
        $val = json_decode($val, true);
        if($key===0){
            $array = $val;
        }
        else{
            foreach($val as $id => $value){
                if(!isset($array[$id])){
                    $array[$id]=0;
                }
                $array[$id] = $array[$id] + $value;
              }
        }
    }

    unset($array[$username]);
    arsort($array);

    $n = 20;

    $counts =array_slice($array, 0, $n);
    $k=1;

    $photos=array();
    $comment='';
    $content='';
    $photo_array='';
    $keys=array();

    foreach ($counts as $key => $val) {
      $img = $session->fetch_picture($key);

      $name = $session->fetch_user_feed($key,null,0,1);
      $name = $name->name;

      $comment.=$name .' http://friendfeed.com/'.$key.', ';
      $photos[]= $img;
      $keys[]= $key;

      $content .= '<article class="one-third column alpha item"><a href="http://friendfeed.com/'. $key .'" title="'. $name .'" target="_blank"><img src="'.$img.'" /></a><h2>'. $k .'.</h2> <p>'. $name .'</p> <p>Points: '. $val .'</p></article>';

      $k = $k+1;
    }

    $photo_array=image_create($path, $photos, $keys);
}
else{
  session_destroy();
  session_start();
  header('Location: ./connect.php');
}

include('template/template.php');

?>