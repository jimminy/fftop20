<?php
/*----------API REQUIREMENTS------------*/
#OAuth Keys provided from Friendfeed
define('CONSUMER_KEY','xxxx');
define('CONSUMER_SECRET', 'xxxxx');

#User Agent
define('UA', 'Unknown - FFv2-API/v0.4');

/*----------APP REQUIREMENTS-----------*/
#Base Path
$path = 'http://localhost.com/';

#Commands: Used for internal requests
$command['likes'] = 'likes';
$command['comments'] = 'comments';
$command['posts'] = 'posts';

#Weights
$weight['user_like'] = 1;
$weight['post_like'] = 1;
$weight['user_comment'] = 2;
$weight['post_comment'] = 2;

#Loop Variables
$i=0;
$loop_count=3;

#Request Size
$num=100;

#Flags
$flag['num'] = 'num';
$flag['iif'] = 'iif';
?>