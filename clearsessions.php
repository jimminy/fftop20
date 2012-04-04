<?php

/* Load and clear sessions */
session_start();
session_destroy();

/* Redirect to page with the connect to Friendfeed option. */
header('Location: ./connect.php');
