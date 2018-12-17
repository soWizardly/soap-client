<?php

namespace Khatfield\SoapClient;


use Khatfield\SoapClient\Result\QueryResult;
use Khatfield\SoapClient\Result\SearchResult;
use Khatfield\SoapClient\Result\SObject;
use Khatfield\SoapClient\Soap\SoapClient;

class PartnerClient extends Client
{
    public function __construct(SoapClient $soap_client, string $username, string $password, string $token)
    {
        parent::__construct($soap_client, $username, $password, $token);

        $this->namespace = self::PARTNER_NAMESPACE;
    }

    protected function call($method, array $params = [])
    {
        $results = parent::call($method, $params);

        //check for search, query, or retrieve
        switch($method) {
            case 'search':
                /** @var SearchResult $results */
                foreach($results->searchRecords as &$record) {
                    $record->record = $this->formatSObject($record->record);
                }
                break;
            case 'queryMore':
            case 'query':
                /** @var QueryResult $results */
                $records = $results->getRecords();
                foreach($records as &$record) {
                    $record = $this->formatSObject($record);
                }
                $results->setRecords($records);
                break;
            case 'retrieve':
                foreach($results as &$record) {
                    $record = $this->formatSObject($record);
                }
                break;
        }


        return $results;
    }

    /**
     * Create a Salesforce object
     *
     * @param object $object     Any object with public properties
     * @param string $objectType Salesforce object type
     *
     * @return object
     */
    protected function createSObject($object, $objectType)
    {
        $sObject = new \stdClass();

        foreach(get_object_vars($object) as $field => $value) {

            //don't need the type field
            if($field == 'type') {
                continue;
            }

            if($value === null) {
                $sObject->fieldsToNull[] = $field;
                continue;
            }

            $sObject->$field = $value;
        }

        return $sObject;
    }

    private function formatSObject(SObject $object)
    {
        foreach($object as $key => $value) {
            if($key == 'Id' && is_array($value)) {
                $object->Id = $value[0];
            } elseif($key == 'any' && is_string($value)) {
                $converted = $this->convertAny($value);
                foreach($converted as $field => $field_val) {
                    $object->$field = $field_val;
                }
                unset($object->any);
            }
        }

        return $object;
    }

    private function convertAny($any)
    {
        $xml_array   = [];
        $parent      = [];
        $opened_tags = [];
        $arr         = [];
        $str         = preg_replace('{sf:}', '', $any);
        $any         = '<Object xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . $str . '</Object>';

        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $any, $xml_values);
        xml_parser_free($parser);

        $current = &$xml_array;

        if(!empty($xml_values)) {
            foreach($xml_values as $data) {
                $result = [];
                if(isset($data['value'])) {
                    $result = $data['value'];
                }

                //Check for nil and ignore other attributes.
                if(isset($data['attributes']) && isset($data['attributes']['xsi:nil']) && !strcasecmp($data['attributes']['xsi:nil'], 'true')) {
                    $result = null;
                }

                if($data['type'] == "open") {
                    $parent[$data['level'] - 1] = &$current;

                    if(!is_array($current) || (!in_array($data['tag'], array_keys($current)))) { //Insert New tag
                        $current[$data['tag']] = $result;
                        $current               = &$current[$data['tag']];

                    } else { //There was another element with the same tag name
                        if(isset($current[$data['tag']][0])) {
                            array_push($current[$data['tag']], $result);
                        } else {
                            $current[$data['tag']] = array($current[$data['tag']], $result);
                        }
                        $last    = count($current[$data['tag']]) - 1;
                        $current = &$current[$data['tag']][$last];
                    }

                } elseif($data['type'] == "complete") { //Tags that ends in 1 line '<tag />'
                    //See if the key is already taken.
                    if(!isset($current[$data['tag']])) { //New Key
                        $current[$data['tag']] = $result;

                    } else { //If taken, put all things inside a list(array)
                        if(isset($current[$data['tag']][0]) && is_array($current[$data['tag']][0])) {
                            // ...push the new element into that array.
                            array_push($current[$data['tag']], $result);
                        } else { //If it is not an array...
                            $current[$data['tag']] = array(
                                $current[$data['tag']],
                                $result,
                            );
                        }
                    }

                } elseif($data['type'] == 'close') { //End of tag '</tag>'
                    $current = &$parent[$data['level'] - 1];
                }
            }
        }

        if(is_array($xml_array['Object'])){
            return $xml_array['Object'];
        } else {
            return $xml_array;
        }
    }
}