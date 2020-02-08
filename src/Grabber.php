<?php

namespace Niidpo;

class Grabber {

    public static function grab () : array {

        $grabber = new self ();

        $url = 'https://www.mtt.ru/defcodes/getDefcodes/?code=&number=&area=&s';
        $exp = '//div[@class="number-row"]//div[@class="col"]';

        $html = $grabber->html ($url);
        $dom = $grabber->dom ($html);
        $nodes = $grabber->nodes ($dom, $exp);
        $rows = $grabber->rows ($nodes);

        return $rows;
    }

    protected function html (string $url) : string {

        $page = @file_get_contents ($url);

        if ($page === false) {
            throw new \Exception ("Load page error");
        }

        $response = explode (' ', $http_response_header [0]);
        $status_code = (int) $response [1];

        if ($status_code >= 400 or $page === false) {
            throw new \Exception ("$status_code");
        }

        $data = @json_decode($page);

        if ( ! $data instanceof \stdClass or empty ($data->template)) {
            throw new \Exception ("Empty template");
        }

        return $data->template;
    }

    public function dom (string $html) : \DOMDocument {

        $dom = new \DOMDocument ();
        $internalErrors = \libxml_use_internal_errors (true);
        $dom->loadHTML ($html);
        \libxml_use_internal_errors ($internalErrors);

        return $dom;
    }

    public function nodes (\DOMDocument $dom, string $exp) : \DOMNodeList {

        $xpath = new \DomXPath ($dom);
        $nodes = $xpath->query ($exp);

        return $nodes;
    }

    public function rows (\DOMNodeList $nodes) : array {

        if ($nodes->length < 7) {
            throw new \Exception ("Empty nodes list");
        }

        $rows = [];

        for ($i = 4; $i < $nodes->length; $i += 4) {

            $def = trim ($nodes->item ($i)->nodeValue);
            list ($min, $max) = explode ('-', trim ($nodes->item ($i + 1)->nodeValue));
            list ($city, $region) = explode ('  ', trim (mb_convert_encoding ($nodes->item ($i + 2)->nodeValue, 'windows-1252')));

            $rows [] = [
                $def,
                (int) $min,
                (int) $max,
                $city,
                $region
            ];
        }

        return $rows;
    }
}
