<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('NetSearcher.php');
include('DBComparator.php');

use DBComparator\DBComparator;




if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    session_start();

    $text = $_POST['text'];
    $searchType = $_POST['searchType'];
    $_SESSION['text'] = $text;


    $searcher = new NetSearcher($searchType);
    try {
        $links = $searcher->perform($text);
    } catch (Google\Service\Exception $e) {

        header("HTTP/1.1 500 Internal Server Error");
        die("'error':'limit has become empty'");
    }

//    $dbComparator = new DBComparator();
//    $articles = $dbComparator->perform($text);

//    foreach ($articles as $article) {
//        $html .= '<div> <b>ID:</b>' . $article->id .
//            ' <b>Совпадение:</b>' . $article->similarity . '%</div><br>';
//    }

    echo json_encode($links);



}

