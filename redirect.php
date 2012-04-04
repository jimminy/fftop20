<?php

session_start();
require_once('lib/ff_oauth/friendfeedv2.php');
require_once('conf/config.php');

$connection = FriendFeed::FriendFeed_OAuth(CONSUMER_KEY, CONSUMER_SECRET);

$request_token = $connection->fetch_request_token();

$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

switch ($connection->http_code) {
    case 200:

        $url = $connection->get_authorize_URL($token);
        header('Location: ' . $url);
        break;
    default:
        echo 'Error: Could not reach the authorization URL';
}