<?php

session_start();

require_once('lib/ff_oauth/friendfeedv2.php');
require_once('conf/config.php');

if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
	$_SESSION['oauth_status'] = 'oldtoken';
  	session_destroy();
  	header('Location: ./redirect.php');
}

$connection = FriendFeed::FriendFeed_OAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION);

$access_token = $connection->fetch_access_token($_SESSION);

$_SESSION['access_token'] = $access_token;

unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

if(200 == $connection->http_code) {
    $_SESSION['status'] = 'verified';
    header('Location: ./index.php');
}
else {
    echo "Error: Failure to verify Access Token." ;
}