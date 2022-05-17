<?php


namespace TextComparator;


abstract class Shingles
{
    const TABS_AND_NEWLINES_REGEX = '!\s+!';

    /**
     * @var array
     */
    protected $stopSymbols = ['.', ';', '-', ','];


    /**
     * @var integer
     */
    protected $shinglesCount;

    /**
     * @param int $shinglesCount
     */
    public function __construct($shinglesCount = 3)
    {
        if (!is_int($shinglesCount)) {
            throw new \InvalidArgumentException('$shinglesCount must be int type');
        }

        $this->shinglesCount = $shinglesCount;
    }

    /**
     * @param $content
     * @return array
     */
    public function canonize($content)
    {
        $content = strtolower($content);
        $content = str_replace($this->stopSymbols, '', $content);
        $content = preg_replace(self::TABS_AND_NEWLINES_REGEX, ' ', $content);

        return explode(' ', $content);
    }

    /**
     * @param array $words
     * @return array
     */
    public function splitShingles(array $words)
    {
        $shingles = array();

        $countWords = count($words);

        for ($i = 0; $i < $countWords - $this->shinglesCount; $i++) {

            $shingles[] = $this->makeHash(implode(' ', array_slice($words, $i, $this->shinglesCount)));
        }

        return $shingles;
    }

    /**
     * @param string $shingle
     * @return mixed
     */
    abstract public function makeHash($shingle);

    /**
     * @param $content1
     * @param $content2
     * @return float
     */
    public function compare($content1, $content2)
    {
        $shingle1 = $this->getShingles($content1);
        $shingle2 = $this->getShingles($content2);

        $diff = array_diff($shingle1, $shingle2);
        $count_shingle = count($shingle1);

        $uniqueness =  count($diff) / ($count_shingle / 100); // уникальность %

        return $uniqueness;
    }

    /**
     * @param $text
     * @return string
     */
    public function getShinglesAsString($text)
    {
        $shingles = $this->splitShingles($this->canonize($text));

        return implode(' ', $shingles);
    }

    /**
     * @param $text
     * @return string
     */
    public function getShingles($text)
    {
        $shingles = $this->splitShingles($this->canonize($text));

        return $shingles;
    }

    /**
     * @return array
     */
    public function getStopSymbols()
    {
        return $this->stopSymbols;
    }

    /**
     * @param array $stopSymbols
     */
    public function addStopSymbols(array $stopSymbols)
    {
        $this->stopSymbols = $stopSymbols;
    }
}