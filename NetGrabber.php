<?php

namespace Netlib;

class NetGrabber
{
    const BODY_REGEX = '/<body.*\/body>/si';
    const ALL_SCRIPTS_REGEX = '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/is';
    const ALL_STYLES_REGEX = '/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/is';
    const ALL_NEWLINES_REGEX = '!\s+!';
    const ALL_TAGS_REGEX = '/<[^>]*>/is';
    const ALL_STOP_SYMBOLS = ['.', ';', '-', ','];
    const NON_WHITESPACE_REGEX = '/\xc2\xa0/';


    const USER_AGENT = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

    public function __construct()
    {
    }


    public function perform($url)
    {
        $html = $this->performRequest($url);

        $text = $this->removeBrakingLines(
            $this->replaceHTMLCodes(
                $this->replaceStopSymbols(
                    $this->replaceNonWhitespace(
                        $this->removeAllTags(
                            $this->removeAllStyles(
                                $this->removeAllScripts(
                                    $this->getBody($html)
                                )
                            )
                        )
                    )
                )
            )
        );

        return $text;
    }


    private function performRequest($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($ch, CURLOPT_URL, $url);

        $html = curl_exec($ch);

        return $html;
    }

    private function getBody($data)
    {
        preg_match(self::BODY_REGEX, $data, $matches);

        return count($matches) > 0 ? $matches[0] : '';
    }

    private function removeAllScripts($data)
    {
        return preg_replace(self::ALL_SCRIPTS_REGEX, '', $data);
    }

    private function removeAllStyles($data)
    {
        return preg_replace(self::ALL_STYLES_REGEX, '', $data);
    }

    private function removeBrakingLines($data)
    {
        return preg_replace(self::ALL_NEWLINES_REGEX, ' ', $data);
    }

    private function removeAllTags($data)
    {
        return preg_replace(self::ALL_TAGS_REGEX, ' ', $data);
    }


    private function replaceHTMLCodes($data)
    {
        return str_replace('&nbsp;', ' ', $data);
    }

    private function replaceStopSymbols($data)
    {
        return str_replace(self::ALL_STOP_SYMBOLS, '', $data);
    }

    private function replaceNonWhitespace($data)
    {
        return preg_replace(self::NON_WHITESPACE_REGEX, ' ', $data);
    }

}