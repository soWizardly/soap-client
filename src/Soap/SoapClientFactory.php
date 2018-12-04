<?php

namespace Khatfield\SoapClient\Soap;

use Khatfield\SoapClient\Soap\TypeConverter;

/**
 * Factory to create a \SoapClient properly configured for the Salesforce SOAP
 * client
 */
class SoapClientFactory
{
    /**
     * Default classmap
     *
     * @var array
     */
    protected $classmap = array(
        'ChildRelationship'           => 'Khatfield\SoapClient\Result\ChildRelationship',
        'DeleteResult'                => 'Khatfield\SoapClient\Result\DeleteResult',
        'DeletedRecord'               => 'Khatfield\SoapClient\Result\DeletedRecord',
        'DescribeGlobalResult'        => 'Khatfield\SoapClient\Result\DescribeGlobalResult',
        'DescribeGlobalSObjectResult' => 'Khatfield\SoapClient\Result\DescribeGlobalSObjectResult',
        'DescribeSObjectResult'       => 'Khatfield\SoapClient\Result\DescribeSObjectResult',
        'DescribeTab'                 => 'Khatfield\SoapClient\Result\DescribeTab',
        'EmptyRecycleBinResult'       => 'Khatfield\SoapClient\Result\EmptyRecycleBinResult',
        'Error'                       => 'Khatfield\SoapClient\Result\Error',
        'Field'                       => 'Khatfield\SoapClient\Result\DescribeSObjectResult\Field',
        'GetDeletedResult'            => 'Khatfield\SoapClient\Result\GetDeletedResult',
        'GetServerTimestampResult'    => 'Khatfield\SoapClient\Result\GetServerTimestampResult',
        'GetUpdatedResult'            => 'Khatfield\SoapClient\Result\GetUpdatedResult',
        'GetUserInfoResult'           => 'Khatfield\SoapClient\Result\GetUserInfoResult',
        'LeadConvert'                 => 'Khatfield\SoapClient\Request\LeadConvert',
        'LeadConvertResult'           => 'Khatfield\SoapClient\Result\LeadConvertResult',
        'LoginResult'                 => 'Khatfield\SoapClient\Result\LoginResult',
        'MergeResult'                 => 'Khatfield\SoapClient\Result\MergeResult',
        'QueryResult'                 => 'Khatfield\SoapClient\Result\QueryResult',
        'SaveResult'                  => 'Khatfield\SoapClient\Result\SaveResult',
        'SearchResult'                => 'Khatfield\SoapClient\Result\SearchResult',
        'SendEmailError'              => 'Khatfield\SoapClient\Result\SendEmailError',
        'SendEmailResult'             => 'Khatfield\SoapClient\Result\SendEmailResult',
        'SingleEmailMessage'          => 'Khatfield\SoapClient\Request\SingleEmailMessage',
        'sObject'                     => 'Khatfield\SoapClient\Result\SObject',
        'UndeleteResult'              => 'Khatfield\SoapClient\Result\UndeleteResult',
        'UpsertResult'                => 'Khatfield\SoapClient\Result\UpsertResult',
    );

    /**
     * Type converters collection
     *
     * @var TypeConverter\TypeConverterCollection
     */
    protected $typeConverters;

    /**
     * @param string $client_type
     * @param string $wsdl Path to WSDL file
     * @param array  $soapOptions
     *
     * @return bool|SoapClient
     */
    public function factory($wsdl, array $soapOptions = [])
    {
        $defaults = array(
            'trace'      => 1,
            'features'   => \SOAP_SINGLE_ELEMENT_ARRAYS,
            'classmap'   => $this->classmap,
            'typemap'    => $this->getTypeConverters()->getTypemap(),
            'cache_wsdl' => \WSDL_CACHE_MEMORY,
        );

        $options = array_merge($defaults, $soapOptions);

        return new SoapClient($wsdl, $options);
    }

    /**
     * test
     *
     * @param string $soap SOAP class
     * @param string $php  PHP class
     */
    public function setClassmapping($soap, $php)
    {
        $this->classmap[$soap] = $php;
    }

    /**
     * Get type converter collection that will be used for the \SoapClient
     *
     * @return TypeConverter\TypeConverterCollection
     */
    public function getTypeConverters()
    {
        if(null === $this->typeConverters) {
            $this->typeConverters = new TypeConverter\TypeConverterCollection(
                array(
                    new TypeConverter\DateTimeTypeConverter(),
                    new TypeConverter\DateTypeConverter(),
                )
            );
        }

        return $this->typeConverters;
    }

    /**
     * Set type converter collection
     *
     * @param TypeConverter\TypeConverterCollection $typeConverters Type converter collection
     *
     * @return SoapClientFactory
     */
    public function setTypeConverters(TypeConverter\TypeConverterCollection $typeConverters)
    {
        $this->typeConverters = $typeConverters;

        return $this;
    }
}
