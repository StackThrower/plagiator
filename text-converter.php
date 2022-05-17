<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('ShinglesMd5Hash.php');
include_once('DBComparator.php');

use TextComparator\ShinglesMd5Hash;
use DBComparator\DBComparator;


$text = 'Окончив МГУ, Игорь пошёл работать в Гугл. Работая в офисе, он встретил давнего друга Валеру. Ещё будучи детьми, они мечтали быть работниками в Гугл. Но через месяц начальство приняло решение уволить их.';

$engine = new ShinglesMd5Hash();
$shingles = $engine->getShinglesAsString($text);

$DBComparator = new DBComparator();
$articles = $DBComparator->perform($text);

$dbArticleHtml = '';
foreach ($articles as $article) {
    $dbArticleHtml .= '<div> <b>ID:</b>' . $article->id .
        ' <b>Совпадение:</b> ' . $article->similarity . '%</div><br>';
}

echo "<link rel=\"stylesheet\" href=\"/style.css\">
    <div class=\"middle\">
        <div>
            <b>Текст:</b> $text
        <div>
        <div>
            <b>Шинглы:</b> $shingles
        <div>
        <br>
        <div>
            <b>Совпадение по базе:</b> <br>
            $dbArticleHtml
        <div>
    </div>
</div>";

