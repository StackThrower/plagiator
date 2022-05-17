<?php

include('vendor/autoload.php');

use Google\Client;

class NetSearcher
{
    const GOOGLE_API_KEY = "";
    const GOOGLE_APP_NAME = "Plagiat";
    const GCSE_SEARCH_ENGINE_ID = "";

    const QUERY_WORDS_LENGTH_HARD = 5;
    const QUERY_WORDS_LENGTH_MIDDLE = 8;
    const QUERY_WORDS_LENGTH_EASY = 10;


    const SEARCH_STEP_HARD = 1;
    const SEARCH_STEP_MIDDLE = 3;
    const SEARCH_STEP_EASY = 4;


    const SEARCH_RESULT_PAGES = 1;
    const SEARCH_RESULT_ITEMS = 10; // This value is always 10, even you change it.

    private $searchType;

    public function __construct($searchType)
    {
        $this->searchType = $searchType;
    }


    function perform($text)
    {
        $links = array();
        $queries = $this->getSearchQueries($text);

        foreach ($queries as $query) {

            $res = $this->performSearchRequest($query);

//            echo "<pre>"; echo($query. "\n");  print_r($res);

            $links = array_merge($links, $res);
        }

//        echo '+++++++++++++++++++++++'; print_r($links); die();

        return $links;
    }

    function getSearchQueries($text)
    {
        $ret = array();
        $text = $this->clearTextFromSymbols($text);

        $words = explode(' ', $text);

        switch ($this->searchType) {
            case 1:
                $step = self::SEARCH_STEP_EASY;
                $wordsCount = self::QUERY_WORDS_LENGTH_EASY;
                break;
            case 2:
                $step = self::SEARCH_STEP_MIDDLE;
                $wordsCount = self::QUERY_WORDS_LENGTH_MIDDLE;
                break;
            case 3:
                $step = self::SEARCH_STEP_HARD;
                $wordsCount = self::QUERY_WORDS_LENGTH_HARD;
                break;
            default:
                $step = self::SEARCH_STEP_EASY;
                $wordsCount = self::QUERY_WORDS_LENGTH_EASY;
        }


        for ($i = 0; $i < count($words); $i += $wordsCount * $step) {
            $query = implode(' ', array_slice($words, $i, $wordsCount));
            array_push($ret, $query);
        }

//        echo '<pre>'; print_r($ret); die();

        return $ret;
    }

    function clearTextFromSymbols($text)
    {
        return str_replace("\n",  ' ', $text);
//        return str_replace(array( ',', '.', '', '—'), '', $text);
    }

    function isLinkInResult($link, $array) {
        return array_search($link, array_column($array, 'link')) != null;
    }


    private function performRequest($service, $query, $exactTerms = false) {
        $ret = array();

        for ($i = 0; $i < self::SEARCH_RESULT_PAGES; $i++) {

            $optParams = array(
                "cx" => self::GCSE_SEARCH_ENGINE_ID,
                "start" => $i * self::SEARCH_RESULT_ITEMS
            );

            if($exactTerms)
                $optParams["exactTerms"] = $query;
            else
                $optParams["q"] = $query;

            $results = $service->cse->listCse($optParams);

//            echo '<pre>'; print_r($query);
            $items = $results->getItems();

//            print_r($items);

            foreach ($items as $k => $item) {

                if(!$this->isLinkInResult($item->link, $ret)) {

                    $link = new stdClass();
                    $link->link = $item->link;
                    $link->title = $item->title;

                    array_push($ret, $link);
                }
            }

            if(count($results->getItems()) != self::SEARCH_RESULT_ITEMS)
                break;
        }

        return $ret;
    }


    function performSearchRequest($query)
    {
        $client = new Client();
        $client->setApplicationName(self::GOOGLE_APP_NAME);
        $client->setDeveloperKey(self::GOOGLE_API_KEY);

        $service = new Google_Service_Customsearch($client);

        $ret = $this->performRequest($service, $query, true);

        $notExactResult = $this->performRequest($service, $query, false);
        foreach ($notExactResult as $k => $item) {

            if(!$this->isLinkInResult($item->link, $ret)) {
                array_push($ret, $item);
            }
        }

        return $ret;
    }
}