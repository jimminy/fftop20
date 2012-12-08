<?php
// Copyright 2011 James Fuller <jimminyfuller@gmail.com>

// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at

//    http://www.apache.org/licenses/LICENSE-2.0

// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
session_start();
require_once('ff_oauth/friendfeedv2.php');
require_once('ff_oauth/JSON.php');
require_once('../conf/config.php');

if (isset($_SESSION['access_token'])){
    $access_token = $_SESSION['access_token'];
}
else{
$at_v = json_decode(base64_decode($_REQUEST['x']));
$access_token = array('oauth_token_secret'=>$at_v->oauth_token_secret,
                        'oauth_token'=>$at_v->oauth_token,
                        'username'=>$at_v->username);
}

$session= FriendFeed::FriendFeed_OAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token, UA);

$username = $_REQUEST['user'];
$start = $_REQUEST['start'];
$num = $_REQUEST['num'];

if($_REQUEST['command']=='likes'){
  $feed = $session->fetch_user_likes_feed($username, null, $start, $num);

  foreach($feed->entries as $entry){
    $user = $entry->from;
    if(!isset($array[$user->id])){
      $array[$user->id]=0;
    }
    $array[$user->id]=$array[$user->id]+$weight['user_like'];
  }
  echo json_encode($array);
}

if($_REQUEST['command']=='comments'){
  $feed = $session->fetch_user_comments_feed($username, null, $start, $num);

  foreach($feed->entries as $entry){
    $user = $entry->from;
    if($entry->comments){
      foreach($entry->comments as $comment){
        $me = $comment->from;
        if($me->id==$username){
          if(!isset($array[$user->id])){
            $array[$user->id]=0;
          }
          $array[$user->id]=$array[$user->id]+$weight['user_comment'];
        }
      }
    }
  }
  echo json_encode($array);
}

if($_REQUEST['command']=='posts'){
  $feed = $session->fetch_user_feed($username, null, $start, $num);

  foreach($feed->entries as $entry){
    if($entry->likes){
      foreach($entry->likes as $like){
        $user = $like->from;
        if(!isset($array[$user->id])){
          $array[$user->id]=0;
        }
        $array[$user->id]=$array[$user->id]+$weight['post_like'];
      }
    }

    if($entry->comments){
      foreach($entry->comments as $comment){
        $user = $comment->from;
        if(!isset($array[$user->id])){
          $array[$user->id]=0;
        }
        $array[$user->id]=$array[$user->id]+$weight['post_comment'];
      }
    }
  }
  echo json_encode($array);
}

if($_REQUEST['command']=='p2ff'){

  if(trim($_REQUEST['comment'])===''){
    $comment = null;
  }
  else{
    $comment = stripslashes(preg_replace('/\%26/', '&', $_REQUEST['comment']));
  }

  if(trim($_REQUEST['body'])===''){
    $body = '#fftop20';
  }
  else{
    $body = stripslashes(preg_replace('/\%26/', '&', $_REQUEST['body']));
  }

  $session->publish_message($body, null, $comment, array($_REQUEST['images']));
}
?>