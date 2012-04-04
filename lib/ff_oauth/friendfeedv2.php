<?php
// Copyright 2008 FriendFeed
// Updated to v2 API methods by James Fuller
//
// Licensed under the Apache License, Version 2.0 (the "License"); you may
// not use this file except in compliance with the License. You may obtain
// a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
// WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
// License for the specific language governing permissions and limitations
// under the License.
    


// Includes the  OAUTH protocol for authenticating users
include_once('OAuth.php');

// This module requires the Curl PHP module, available in PHP 4 and 5
assert(function_exists("curl_init"));

class FriendFeed
{
    //Oauth hasn't been added and couldn't be tested do to the inability 
    //of registering an app. 

    protected $consumer = null;
    protected $consumer_key = '';
    protected $consumer_secret = '';
    protected $access_token = null;

    protected $ua = '';


    function FriendFeed_OAuth($consumer_key, $consumer_secret, $access_token = null, $ua = 'FFv2-API/v0.4') {
        $ff = new FriendFeed;
        $ff->consumer_key = $consumer_key;
        $ff->consumer_secret = $consumer_secret;
        $ff->ua = $ua;
        $ff->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
        $ff->consumer = new OAuthConsumer($consumer_key, $consumer_secret);
        $ff->access_token = $access_token;

        return $ff;
    }
    
    function FriendFeed_Basic($auth_nickname=null, $auth_key=null, $ua = 'FFv2-API/v0.4') {
        $ff = new FriendFeed();
        $ff->auth_nickname = $auth_nickname;
        $ff->auth_key = $auth_key;
        $ff->ua;
        
        return $ff;
    }
    
    //Resource Fetching Functions

    // Returns the public feed with everyone's public entries.
    //
    // Authentication is not required.
    function fetch_public_feed($service=null, $start=0, $num=30) {
        return $this->fetch_feed("/feed/public", $service, $start, $num);
    }
    

    // Returns the entries the authenticated user sees on their home page.
    //
    // Authentication is always required.
    function fetch_home_feed($service=null, $start=0, $num=30) {
        return $this->fetch_feed("/feed/home", $service, $start, $num);
    }

    // Returns the entries shared by the user with the given nickname.
    //
    // Authentication is required if the user's feed is not public.
    function fetch_user_feed($nickname, $service=null, $start=0, $num=30) {
        return $this->fetch_feed("/feed/" . urlencode($nickname), 
                $service, $start, $num);
    }

    // Returns the entries shared by the users with the given nicknames.
    //
    // Authentication is required if the user's feed is not public.
     function fetch_multi_user_feed($nicknames, $service=null, $start=0,
                   $num=30) {
        return $this->fetch_feed("/api/feed/user", $service, $start, $num,
                 join(",", $nicknames));
    }

    // Returns the most recent entries the given user has commented on.
    function fetch_user_comments_feed($nickname, $service=null, $start=0,
            $num=30) {
        return $this->fetch_feed("/feed/" . urlencode($nickname) . "/comments",
                $service, $start, $num);
    }
    
    // Returns the most recent entries the given user has "liked."
    function fetch_user_likes_feed($nickname, $service=null, $start=0,
            $num=30) {
        return $this->fetch_feed("/feed/" . urlencode($nickname) . "/likes",
                $service, $start, $num);
    }

    // Returns the most recent entries the given user has commented on or "liked."
    function fetch_user_discussion_feed($nickname, $service=null, $start=0,
            $num=30) {
        return $this->fetch_feed("/feed/" . urlencode($nickname) . "/discussion",
                $service, $start, $num);
    }

    // Searches over entries in FriendFeed.
    //
    // If the request is authenticated, the default scope is over all of the
    // entries in the authenticated user's Friends Feed. If the request is
    // not authenticated, the default scope is over all public entries.
    //
    // The query syntax is the same syntax as
    // http://friendfeed.com/search/advanced
    function search($nickname, $query, $num = 30) {
        return $this->fetch_feed("/search", null, 0, $num, urlencode($nickname), urlencode($query), null);
    }

    //Resource Modifying Functions
    //Authentication Required

    // Publishes the given textual message to the authenticated user's feed.
    //
    // See publish_link for additional options.
    function publish_message($message, $link=null, $comment=null,
                $image_urls=array(), $audio_urls=array(), $rooms= array()) {
        return $this->publish_link($message, $link, $comment,
                join(",", $image_urls), join(",", $audio_urls), join(",", $rooms));
    }
    
    // Updates the textual message with the given ID.
    function edit_message($entry_id, $body) {
        $this->fetch("/entry", null, array(
            "id" => $entry_id,
            "body" => $body,
        ));
    }
    
    // Deletes the message with the given ID.
    function delete_message($entry_id) {
        $this->fetch("/entry/delete", null, array(
            "id" => $entry_id,
        ));
    }

    // Un-deletes the message with the given ID.
    function undelete_message($entry_id) {
        $this->fetch("/entry/delete", null, array(
            "id" => $entry_id, 
            "undelete" => 1,
        ));
    }

    // Adds the given comment to the entry with the given ID.
    //
    // We return the ID of the new comment, which can be used to edit or
    // delete the comment.
    function add_comment($entry_id, $body) {
        $result = $this->fetch("/comment", null, array(
            "entry" => $entry_id,
            "body" => $body,
        ));
        return $result->id;
    }

    // Updates the comment with the given ID.
    function edit_comment($comment_id, $body) {
        $this->fetch("/comment", null, array(
            "id" => $comment_id,
            "body" => $body,
        ));     
    }

    // Deletes the comment with the given ID.
    function delete_comment($comment_id) {
        $this->fetch("/comment/delete", null, array(
            "id" => $comment_id,
        ));
    }

    // Un-deletes the comment with the given ID.
    function undelete_comment($comment_id) {
        $this->fetch("/comment/delete", null, array(
            "id" => $comment_id,
            "undelete" => 1,
        ));
    }

    // 'Likes' the entry with the given ID.
    function add_like($entry_id) {
        $this->fetch("/like", null, array(
            "entry" => $entry_id,
        ));
    }
    
    // Deletes the 'Like' for the entry with the given ID (if any).
    function delete_like($entry_id) {
        $this->fetch("/like/delete", null, array(
            "entry" => $entry_id,
        ));
    }
    //Allows user to modify their theme settings.
    function edit_theme($theme, $bg, $box, $bar)
    {
        $this->fetch("/theme", null, array(
            "theme" => $theme,
            "bg" => $bg,
            "box" => $box,
            "bar" => $bar,
        ));
    }
    // Returns the user's subscribers, subscriptions, and services. 
    function fetch_feedinfo($nickname) {
        return $this->fetch("/feedinfo/" . urlencode($nickname));
    }
    // Return the feeds displayed on the right hand side of the FriendFeed website for the authenticated user.
    function fetch_feedlist() {
        return $this->fetch("/feedlist");
    }
    //Get the profile picture for the specified feed
    function fetch_picture($nickname, $size='medium'){
        $picture = "http://friendfeed-api.com/v2/picture/" . urlencode($nickname).'?size='.$size;
        return $picture;
    }

    // Publishes the given link/title to the authenticated user's feed.
    //
    // Authentication is always required.
    //
    // image_urls is a list of URLs that will be downloaded and included as
    // thumbnails beneath the link. The thumbnails will all link to the
    // destination link. If you would prefer that the images link somewhere
    // else, you can specify images instead, which should be an array of
    // name-associated arrays of the form array("url"=>...,"link"=>...).
    // The thumbnail with the given url will link to the specified link.
    //
    // audio_urls is a list of MP3 URLs that will show up as a play
    // button beneath the link. You can optionally supply audio[]
    // instead, which should be a list of name-associated arrays of the 
    // form ("url"=> ..., "title"=> ...). The given title will appear when
    // the audio file is played.
    //
    // We return the parsed/published entry as returned from the server,
    // which includes the final thumbnail URLs as well as the ID for the
    // new entry.
    function publish_link($body, $link, $comment=null,
                $image_urls=null, $audio_urls=null, $rooms=null) {
        $post_args = array("body" => $body);
        if ($link) $post_args["link"] = $link;
        if ($comment) $post_args["comment"] = $comment;
        if ($image_urls) $post_args["image_url"] = $image_urls;
        if ($audio_urls) $post_args["audio_url"] = $audio_urls;
        if ($rooms) $post_args["to"] = $rooms;

        $feed = $this->fetch_feed("/entry", null, null, null,
            null, null, $post_args);
        return $feed->entries[0];
    }
    
    // Internal function to download, parse, and process FriendFeed feeds.
    function fetch_feed($uri, $service, $start, $num, $nickname=null,
            $query=null, $post_args=null) {
        $url_args = array(
            "service" => $service,
            "start" => $start,
            "num" => $num,
        );
        if ($nickname) $url_args["nickname"] = $nickname;
        if ($query) $url_args["q"] = $query;
        $feed = $this->fetch($uri, $url_args, $post_args);
        if (!$feed) return null;

        return $feed;
    }
    
    // Performs an authenticated FF request, parsing the JSON response.
    function fetch($uri, $url_args=null, $post_args=null) {
        if (!$url_args) $url_args = array();
        if (!$post_args) $post_args = array();
        if($url_args['service']==null){$url_args['service']=0;}
        if($url_args['start']==null){$url_args['start']=0;}
        $url_args["format"] = "json";
        $method = null;
        $url = "http://friendfeed-api.com/v2" . $uri;

        $curl = curl_init();

        if (isset($this->access_token['oauth_token'])){
            
                if($post_args != array()) {
                    $method = "POST";
                    $args = $post_args;
                }
                else {
                    $method = "GET";
                    $args = $url_args;
                }
            $token = new OAuthToken($this->access_token['oauth_token'], $this->access_token['oauth_token_secret']);
            $request = OAuthRequest::from_consumer_and_token($this->consumer, $token, $method, $url, $args);
            $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->consumer, $token);
            $args = array_merge($args, $request->get_parameters());

            if($method == "GET") {
                $url .= "?" . OAuthUtil::build_http_query($args);
            }
            else {
                curl_setopt($curl, CURLOPT_POST,count($args)); 
                $data_string = OAuthUtil::build_http_query($args);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            }
        }
        else {
            $pairs = array();
            foreach ($url_args as $name => $value) {
                $pairs[] = $name . "=" . urlencode($value);
            }
            $url .= "?" . join("&", $pairs);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->ua);

        if ($this->auth_nickname && $this->auth_key) {
            curl_setopt($curl, CURLOPT_USERPWD,
                $this->auth_nickname . ":" . $this->auth_key);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }

        if ($post_args && $method == null) {
            
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_args);
        }
        $response = curl_exec($curl);
        //print_r($response);
        $info = curl_getinfo($curl);
        curl_close($curl);
        if ($info["http_code"] != 200) {
            return null;
        }
        return $this->json_decode($response);
    }
    
    // JSON decoder that uses the PHP 5.2+ functionality if available
    function json_decode($str) {
        if(function_exists("json_decode")) {
            return json_decode($str);
        } 
        else {
            require_once("JSON.php");
            $json = new Services_JSON();
            return $json->decode($str);
        }
    }

    function test($session) {

        $feed=$session->search(null,"#dpt", 100);
        foreach($feed->entries as $entry){
            print "<div>$entry->body </div>
                    <div> $entry->id </div>";
        }

        //$session->edit_theme("custom","FFFFFF","FFFFFF","FFFFFF");

        $feed = $session->fetch_public_feed();
        // $feed = $session->fetch_user_feed("bret");
        // $feed = $session->fetch_user_feed("paul", "twitter");
        // $feed = $session->fetch_user_discussion_feed("bret");
        // $feed = $session->fetch_multi_user_feed(array("bret", "paul", "jim"));
        // $feed = $session->search("who:bret friendfeed");
        foreach ($feed->entries as $entry) {
            print("<div>$entry->body </div> \n");
        }

        // The feed that the authenticated user would see on their home page
        $feed = $session->fetch_home_feed();
        foreach ($feed->entries as $entry) {
            print("<div>$entry->body </div> \n");
        }
        // Post a message on this user's feed
        $entry = $session->publish_message(
            "Testing the FriendFeed API",
            null,     //Link
            null,     //Comment
            array(),  //Images or Video
            array(),  //Audio
            array()); //Rooms defaults to 'me'
        print("Posted new message at http://friendfeed.com/e/" . $entry->id . "\n");

        // Post a link on this user's feed
        $entry = $session->publish_message(
            "Testing the FriendFeed API",
            "http://friendfeed.com/", 
            null,
            array(), 
            array(),
            array());
        print("Posted new link at http://friendfeed.com/e/" . $entry->id . "\n");

        // Post a link with two thumbnails on this user's feed
        $entry = $session->publish_message(
            "Testing the Friendfeed API.",
            "http://friendfeed.com/",
            "Comment Test.",
            array("http://friendfeed.com/static/images/jim-superman.jpg",
                "http://friendfeed.com/static/images/logo.png"), 
            array(),
            array()
        );
        print("$entry");
        print("Posted images at http://friendfeed.com/e/" . $entry->id . "\n");

}
    
// OAUTH RELATED FUNCTIONS

public $http_code;
public $http_info;

function access_TokenURL() {
return 'http://friendfeed.com/account/oauth/access_token';}
function authenticate_URL() {
return 'http://friendfeed.com/account/oauth/authenticate';}
function authorize_URL() {
return 'http://friendfeed.com/account/oauth/authorize';}
function request_TokenURL() {
return 'http://friendfeed.com/account/oauth/request_token';}


    function fetch_request_token() {
        $url = $this->get_request_token();
        $request = $this->http($url, 'GET', NULL);
        return $this->parse_response($request);   
    }

    function get_request_token() {
        $url = $this->request_TokenURL();
        $request = OAuthRequest::from_consumer_and_token($this->consumer, false, 'GET', $url, array());
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->consumer, false);
        return $request->to_url();
    }

    function get_authorize_URL($token)
    {
        return $this->authorize_URL() .'?oauth_token=' . $token;
    }

    function fetch_access_token($request_token) {
        $url = $this->get_access_token($request_token);
        $request = $this->http($url, 'GET');
        $access_token = $this->parse_response($request);
        $this->access_token = $access_token;
        return $access_token;
    }
    function get_access_token($request_token) {
        $url = $this->access_TokenURL();
        $token = new OAuthToken($request_token['oauth_token'], $request_token['oauth_token_secret']);
        $request = OAuthRequest::from_consumer_and_token($this->consumer, $token, 'GET', $url, array());
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->consumer, $token);
        return $request->to_url();
    }

    function parse_response($response) {
        $params = array();
        parse_str($response, $params);
        return $params;
    }

    function http($url, $method, $params=null){
        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_USERAGENT, $this->ua);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, array($this, 'get_header'));
        curl_setopt($curl, CURLOPT_HEADER, FALSE);

        if(!empty($params)) {
            $url = $url.'?'.$params;
            print 'http'.$url;
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        $response = curl_exec($curl);
        $this->http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $this->url = $url;
        curl_close ($curl);
  
        return $response;
    }

    function get_header($ch, $header) {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->http_header[$key] = $value;
        }
        return strlen($header);
    }
}
?>