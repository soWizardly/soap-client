<?php

namespace Khatfield\SoapClient;


use http\Exception\InvalidArgumentException;
use Khatfield\SoapClient\Header\CallOptions;
use Khatfield\SoapClient\Soap\SoapClient;

class EnterpriseClient extends Client
{
    public function __construct(SoapClient $soap_client, string $username, string $password, string $token)
    {
        parent::__construct($soap_client, $username, $password, $token);

        $this->namespace = self::ENTERPRISE_NAMESPACE;
    }

    public function addHeader($header)
    {
        if($header instanceof CallOptions){
            throw new InvalidArgumentException('CallOptions header only valid for Partner Connection');
        }
        parent::addHeader($header);
    }

    /**
     * Create a Salesforce object
     *
     * Converts PHP \DateTimes to their SOAP equivalents.
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
            $type = $this->soap_client->getSoapElementType($objectType, $field);
            if($field != 'Id' && !$type) {
                continue;
            }

            if($value === null) {
                $sObject->fieldsToNull[] = $field;
                continue;
            }

            // As PHP \DateTime to SOAP dateTime conversion is not done
            // automatically with the SOAP typemap for sObjects, we do it here.
            switch($type) {
                case 'date':
                    if($value instanceof \DateTime) {
                        $value = $value->format('Y-m-d');
                    }
                    break;
                case 'dateTime':
                    if($value instanceof \DateTime) {
                        $value = $value->format('Y-m-d\TH:i:sP');
                    }
                    break;
                case 'base64Binary':
                    $value = base64_encode($value);
                    break;
            }

            $sObject->$field = $value;
        }

        return $sObject;
    }
}