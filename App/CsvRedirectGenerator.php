<?php

namespace App;

use Aspera\Spreadsheet\XLSX\Reader;

class CsvRedirectGenerator
{
    function parseXml($route)
    {
        $reader = new Reader();
        $reader->open($route);
        $rows = iterator_to_array($reader);
        $reader->close();
        return $rows;
    }

    function concatDataAndGenerateCsv(){
        $magento = array_reduce($this->parseXml('source/magento.xlsx'), function (&$result, $item) {
            $result[$item[1]] = str_replace('http://netsuite.medicalsupplydepot.com', '', $item[3]);
            return $result;
        });
        $shopify = array_reduce($this->parseXml('source/shopify.xlsx'), function (&$result, $item) {
            $result[$item[2]] = "/collections/".$item[1];
            return $result;
        });
        $merged_array = array();
        $not_merged =[];
        foreach ($magento as $key => $magento_item) {
            if (array_key_exists($key, $shopify)&&!array_key_exists($magento_item, $merged_array)) {
                array_push($merged_array, array($magento_item, $shopify[$key]));
            } else{
                array_push($not_merged,[$key,$magento_item]);
            }
        }
        $fp = fopen('file_not_merged.csv', 'w');

        foreach ($not_merged as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
        $unique =  array_unique($merged_array, SORT_REGULAR);

        $this->generateCsv($unique);
    }

    public function generateCsv($items)
    {

        $list = array();
        array_push($list,array('Redirect from', 'Redirect to'));
        $merged = array_merge($list,$items);
        $fp = fopen('file.csv', 'w');

        foreach ($merged as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
    }
}
