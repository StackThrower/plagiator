<?php


namespace TextComparator;

include('Shingles.php');


class ShinglesMd5Hash extends Shingles
{
    public function makeHash($shingle)
    {
        return md5($shingle);
    }
}