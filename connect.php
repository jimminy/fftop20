<?php
/**
 * Exit with an error message if the CONSUMER_KEY or CONSUMER_SECRET is not defined.
 */
require_once('conf/config.php');
if (CONSUMER_KEY === '' || CONSUMER_SECRET === '') {
  echo 'You need a consumer key and secret to test the sample code. Get one from <a href="https://friendfeed.com/api/register">https://friendfeed.com/api/register</a>';
  exit;
}

/* Build an image link to start the redirect process. */
$content = '<a href="./redirect.php"><img src="sign-in.png" alt="Sign in with Friendfeed"/></a>';

/* Include HTML to display on the page. */
include('template/template.php');
