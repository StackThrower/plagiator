<?php

include('ShinglesMd5Hash.php');
include('NetGrabber.php');

use Netlib\NetGrabber;
use TextComparator\ShinglesMd5Hash;

session_start();

$url = urldecode($_GET['url']);
$verifyText = $_SESSION['text'];

$grabber = new NetGrabber();
$siteText = $grabber->perform($url);

$engine = new ShinglesMd5Hash();
$uniqueness = $engine->compare($verifyText, $siteText);



$ret = array(
    'uniqueness' => $uniqueness,
    'siteUrl' => $url
);

header('Content-type: application/json');
echo json_encode($ret);