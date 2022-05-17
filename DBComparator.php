<?php

namespace DBComparator;

include_once("ShinglesMd5Hash.php");

use TextComparator\ShinglesMd5Hash;

class DBComparator
{
    const DB_SERVER_NAME = 'localhost';
    const DB_USER_NAME = '';
    const DB_PASSWORD = '';
    const DB_NAME = '';

    const SIMILARITY_PERCENTS = 65;

    public function __construct()
    {
    }

    public function perform($text)
    {
        $engine = new ShinglesMd5Hash();
        $shingles = $engine->getShingles($text);

        $articles = $this->search($shingles);

        return $articles;
    }

    private function search($shingles)
    {
        $ret = array();

        $conn = new \mysqli(self::DB_SERVER_NAME, self::DB_USER_NAME,
            self::DB_PASSWORD, self::DB_NAME);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sqlParams = '';
        $shinglesLength = 0;
        for ($i = 0; $i < count($shingles); $i++) {
            if ($i != count($shingles) - 1)
                $sqlParams .= 'length(content)-length(replace(content, \'' . $shingles[$i] . '\', \'\')) + ';
            else
                $sqlParams .= 'length(content)-length(replace(content, \'' . $shingles[$i] . '\', \'\')) ';

            $shinglesLength += strlen($shingles[$i]);
        }

        $shinglesRange = $shinglesLength / 100 * self::SIMILARITY_PERCENTS;

        $sql = "SELECT id, CASE WHEN COALESCE(content, '') = '' THEN 0 
            ELSE 
            $sqlParams
            END as shingle_length 
            FROM texts HAVING shingle_length > $shinglesRange";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {
                $article = new \stdClass();

                $article->id = $row["id"];
                $article->similarity = $row["shingle_length"] / ($shinglesLength / 100);

                array_push($ret, $article);
            }
        }
        $conn->close();


        return $ret;
    }


}