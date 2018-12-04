<?php
namespace Khatfield\SoapClient;

use Khatfield\SoapClient\Header\PackageVersionHeader;
use Khatfield\SoapClient\Soap\SoapClient;
use Khatfield\SoapClient\Result;
use Khatfield\SoapClient\Event;
use Khatfield\SoapClient\Exception;

/**
 * A client for the Salesforce SOAP API
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class Client extends AbstractHasDispatcher implements ClientInterface
{
    /*
     * SOAP namespace
     *
     * @var string
     */
    const ENTERPRISE_NAMESPACE = 'urn:enterprise.soap.sforce.com';
    const PARTNER_NAMESPACE  = 'urn:partner.soap.sforce.com';

    protected $namespace;

    /**
     * SOAP session header
     *
     * @var \SoapHeader
     */
    protected $session_header = false;

    /**
     * PHP SOAP client for interacting with the Salesforce API
     *
     * @var SoapClient
     */
    protected $soap_client;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $token;

    /**
     * Type collection as derived from the WSDL
     *
     * @var array
     */
    protected $types = [];

    /**
     * Soap headers to set
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Login result
     *
     * @var Result\LoginResult
     */
    protected $login_result;

    /**
     * Construct Salesforce SOAP client
     *
     * @param SoapClient $soap_client SOAP client
     * @param string     $username    Salesforce username
     * @param string     $password    Salesforce password
     * @param string     $token       Salesforce security token
     * @param string     $type        Partner or Enterprise connection
     */
    public function __construct(SoapClient $soap_client, $username, $password, $token)
    {
        $this->soap_client = $soap_client;
        $this->username    = $username;
        $this->password    = $password;
        $this->token       = $token;

    }

    public function addHeader($header)
    {
        $class = get_class($header);
        if($class !== false){
            if($class == 'PackageVersion'){
                if(!array_key_exists('PackageVersionHeader', $this->headers)){
                    $this->headers['PackageVersionHeader'] = [];
                }
                $this->headers['PackageVersionHeader'][] = $header;
            } else {
                $this->headers[$class] = $header;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertLead(array $leadConverts)
    {
        return $this->call(
            'convertLead',
            array(
                'leadConverts' => $leadConverts
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function emptyRecycleBin(array $ids)
    {
        $result = $this->call(
            'emptyRecycleBin',
            array('ids'   => $ids)
        );

        return $this->checkResult($result, $ids);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $objects, $type)
    {
        $result = $this->call(
            'create',
            array('sObjects' => $this->createSoapVars($objects, $type))
        );

        return $this->checkResult($result, $objects);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(array $ids)
    {
        $result = $this->call(
            'delete',
            array('ids' => $ids)
        );

        return $this->checkResult($result, $ids);
    }

    /**
     * {@inheritdoc}
     */
    public function describeGlobal()
    {
        return $this->call('describeGlobal');
    }

    /**
     * {@inheritdoc}
     */
    public function describeSObjects(array $objects)
    {
        return $this->call('describeSObjects', $objects);
    }

    /**
     * {@inheritdoc}
     */
    public function describeTabs()
    {
        return $this->call('describeTabs');
    }

    /**
     * {@inheritdoc}
     */
    public function getDeleted($objectType, \DateTime $startDate, \DateTime $endDate)
    {
        return $this->call(
            'getDeleted',
            array(
                'sObjectType'   => $objectType,
                'startDate'     => $startDate,
                'endDate'       => $endDate
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdated($objectType, \DateTime $startDate, \DateTime $endDate)
    {
        return $this->call(
            'getUpdated',
            array(
                'sObjectType'   => $objectType,
                'startDate'     => $startDate,
                'endDate'       => $endDate
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfo()
    {
        return $this->call('getUserInfo');
    }

    /**
     * {@inheritdoc}
     */
    public function invalidateSessions(array $sessionIds)
    {
        throw new \BadMethodCallException('Not yet implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function doLogin($username, $password, $token)
    {
        $this->setSoapHeaders('login');
        $result = $this->soap_client->login(
            array(
                'username'  => $username,
                'password'  => $password.$token
            )
        );

        $this->setLoginResult($result->result);

        return $result->result;
    }

    /**
     * {@inheritdoc}
     */
    public function login($username, $password, $token)
    {
        return $this->doLogin($username, $password, $token);
    }

    /**
     * Get login result
     *
     * @return Result\LoginResult
     */
    public function getLoginResult()
    {
        if (null === $this->login_result) {
            $this->login($this->username, $this->password, $this->token);
        }

        return $this->login_result;
    }

    /**
     * {@inheritdoc}
     */
    public function logout()
    {
        $this->call('logout');
        $this->session_header = null;
        $this->setSessionId(null);
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $mergeRequests, $type)
    {
        foreach ($mergeRequests as $mergeRequest) {
            if (!($mergeRequest instanceof Request\MergeRequest)) {
                throw new \InvalidArgumentException(
                    'Each merge request must be an instance of MergeRequest'
                );
            }

            if (!$mergeRequest->masterRecord || !is_object($mergeRequest->masterRecord)) {
                throw new \InvalidArgumentException('masterRecord must be an object');
            }

            if (!$mergeRequest->masterRecord->Id) {
                throw new \InvalidArgumentException('Id for masterRecord must be set');
            }

            if (!is_array($mergeRequest->recordToMergeIds)) {
                throw new \InvalidArgumentException('recordToMergeIds must be an array');
            }

            $mergeRequest->masterRecord = new \SoapVar(
                $this->createSObject($mergeRequest->masterRecord, $type),
                SOAP_ENC_OBJECT,
                $type,
                $this->namespace
            );
        }

        return $this->call(
            'merge',
            array('request' => $mergeRequests)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function process(array $processResults)
    {
        throw new \BadMethodCallException('Not yet implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function query($query)
    {
        $result = $this->call(
            'query',
            array('queryString' => $query)
        );

        return new Result\RecordIterator($this, $result);
    }

    /**
     * {@inheritdoc}
     */
    public function queryAll($query)
    {
        $result = $this->call(
            'queryAll',
            array('queryString' => $query)
        );

        return new Result\RecordIterator($this, $result);
    }

    /**
     * {@inheritdoc}
     */
    public function queryMore($queryLocator)
    {
        return $this->call(
            'queryMore',
            array('queryLocator' => $queryLocator)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function retrieve(array $fields, array $ids, $objectType)
    {
        return $this->call(
            'retrieve',
            array(
                'fieldList'   => implode(',', $fields),
                'sObjectType' => $objectType,
                'ids'         => $ids
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function search($searchString)
    {
        $records = [];
        $result = $this->call(
            'search',
            array(
                'searchString'  => $searchString
            )
        );

        //fix the odd xml layout
        if(!empty($result->searchRecords)){
            foreach($result->searchRecords as $record){
                $records[] = $record->record;
            }
        }

        $result->searchRecords = $records;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function undelete(array $ids)
    {
        $result = $this->call(
            'undelete',
            array('ids' => $ids)
        );

        return $this->checkResult($result, $ids);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $objects, $type)
    {
        $result = $this->call(
            'update',
            array('sObjects' => $this->createSoapVars($objects, $type))
        );

        return $this->checkResult($result, $objects);
    }

    /**
     * {@inheritdoc}
     */
    public function upsert($externalIdFieldName, array $objects, $type)
    {
        return $this->call(
            'upsert',
            array(
                'externalIDFieldName' => $externalIdFieldName,
                'sObjects'            => $this->createSoapVars($objects, $type)
            )
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return Result\GetServerTimestampResult
     */
    public function getServerTimestamp()
    {
        return $this->call('getServerTimestamp');
    }

    /**
     * {@inheritdoc}
     */
    public function resetPassword($userId)
    {
        throw new \BadMethodCallException('Not yet implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function sendEmail(array $emails)
    {
        $result = $this->call(
            'sendEmail',
            array(
                'messages' => $this->createSoapVars($emails, 'SingleEmailMessage')
            )
        );

        return $this->checkResult($result, $emails);
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword($userId, $password)
    {
        return $this->call(
            'setPassword',
            array(
                'userId'    => $userId,
                'password'  => $password
            )
        );
    }

    /**
     * Turn Sobjects into \SoapVars
     *
     * @param array  $objects Array of objects
     * @param string $type    Object type
     *
     * @return \SoapVar[]
     */
    protected function createSoapVars(array $objects, $type)
    {
        $soapVars = array();

        foreach ($objects as $object) {

            $sObject = $this->createSObject($object, $type);

            $xml = '';
            if (isset($sObject->fieldsToNull)) {
                foreach ($sObject->fieldsToNull as $fieldToNull) {
                    $xml .= '<fieldsToNull>' . $fieldToNull . '</fieldsToNull>';
                }
                $fieldsToNullVar = new \SoapVar(new \SoapVar($xml, XSD_ANYXML), SOAP_ENC_ARRAY);
                $sObject->fieldsToNull = $fieldsToNullVar;
            }

            $soapVar = new \SoapVar($sObject, SOAP_ENC_OBJECT, $type, $this->namespace);
            $soapVars[] = $soapVar;
        }

        return $soapVars;
    }

    /**
     * Fix the fieldsToNull property for sObjects
     *
     * @param \SoapVar $object
     * @return \SoapVar
     */
    protected function fixFieldsToNullXml(\SoapVar $object)
    {
        if (isset($object->enc_value->fieldsToNull)
            && is_array($object->enc_value->fieldsToNull)
            && count($object->enc_value->fieldsToNull) > 0)
        {
            $xml = '';
            foreach ($object->enc_value->fieldsToNull as $fieldToNull) {
                $xml .= '<fieldsToNull>' . $fieldToNull . '</fieldsToNull>';
            }
            return new \SoapVar(new \SoapVar($xml, XSD_ANYXML), SOAP_ENC_ARRAY);
        }
    }

    /**
     * Check response for errors
     *
     * Add each submitted object to its corresponding success or error message
     *
     * @param array $results Results
     * @param array $params  Parameters
     *
     * @throws Exception\SaveException  When Salesforce returned an error
     * @return array
     */
    protected function checkResult(array $results, array $params)
    {
        $exceptions = new Exception\SaveException();

        for ($i = 0; $i < count($results); $i++) {

            // If the param was an (s)object, set it’s Id field
            if (is_object($params[$i])
                && (!isset($params[$i]->Id) || null === $params[$i]->Id)
                && $results[$i] instanceof Result\SaveResult) {
                $params[$i]->Id = $results[$i]->getId();
            }

            if (!$results[$i]->isSuccess()) {
                $results[$i]->setParam($params[$i]);
                $exceptions->add($results[$i]);
            }
        }

        if ($exceptions->count() > 0) {
            throw $exceptions;
        }

        return $results;
    }

    /**
     * Issue a call to Salesforce API
     *
     * @param string $method SOAP operation name
     * @param array  $params SOAP parameters
     *
     * @return array | \Traversable An empty array or a result object, such
     *                              as QueryResult, SaveResult, DeleteResult.
     *
     * @throws \SoapFault;
     */
    protected function call($method, array $params = array())
    {
        $this->init();

        // Prepare headers
        $this->setSoapHeaders($method);

        $request_event = new Event\RequestEvent($method, $params);
        $this->dispatch(Events::REQUEST, $request_event);

        try {
            $result = $this->soap_client->$method($params);
        } catch (\SoapFault $soap_fault) {
            $fault_event = new Event\FaultEvent($soap_fault, $request_event);
            $this->dispatch(Events::FAULT, $fault_event);

            throw $soap_fault;
        }

        // No result e.g. for logout, delete with empty array
        if (!isset($result->result)) {
            return array();
        }

        $this->dispatch(
            Events::RESPONSE,
            new Event\ResponseEvent($request_event, $result->result)
        );

        return $result->result;
    }

    /**
     * Initialize connection
     *
     */
    protected function init()
    {
        // If there’s no session header yet, this means we haven’t yet logged in
        if (!$this->getSessionHeader()) {
            $this->doLogin($this->username, $this->password, $this->token);
        }
    }

    /**
     * Set soap headers
     *
     * @param string $call
     */
    protected function setSoapHeaders($call)
    {
        $headers   = [];
        if($call != 'login'){
            //set the session header
            $headers[] = $this->getSessionHeader();
        }

        if(!empty($this->headers)){
            foreach($this->headers as $class => $option){
                $header = null;
                if($class == 'PackageVersionHeader'){
                    $header  = new PackageVersionHeader($option);
                } else {
                    $header = $option;
                }

                if(!is_null($header) && $header->validFor($call)){
                    $headers[] = $header->getHeader($this->namespace);;
                }
            }
        }

        $this->soap_client->__setSoapHeaders($headers);
    }

    /**
     * Get session header
     *
     * @return \SoapHeader
     */
    protected function getSessionHeader()
    {
        return $this->session_header;
    }

    /**
     * Save session id to SOAP headers to be used on subsequent requests
     *
     * @param string $session_id
     */
    protected function setSessionId($session_id)
    {
        $this->session_header = new \SoapHeader(
            $this->namespace,
            'SessionHeader',
            [
                'sessionId' => $session_id
            ]
        );
    }

    protected function setLoginResult(Result\LoginResult $login_result)
    {
        $this->login_result = $login_result;
        $this->setEndpointLocation($login_result->getServerUrl());
        $this->setSessionId($login_result->getSessionId());
    }

    /**
     * After successful log in, Salesforce wants us to change the endpoint
     * location
     *
     * @param string $location
     */
    protected function setEndpointLocation($location)
    {
        $this->soap_client->__setLocation($location);
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

        foreach (get_object_vars($object) as $field => $value) {
            $type = $this->soap_client->getSoapElementType($objectType, $field);
            if ($field != 'Id' && !$type) {
                continue;
            }

            if ($value === null) {
                $sObject->fieldsToNull[] = $field;
                continue;
            }

            // As PHP \DateTime to SOAP dateTime conversion is not done
            // automatically with the SOAP typemap for sObjects, we do it here.
            switch ($type) {
                case 'date':
                    if ($value instanceof \DateTime) {
                        $value  = $value->format('Y-m-d');
                    }
                    break;
                case 'dateTime':
                    if ($value instanceof \DateTime) {
                        $value  = $value->format('Y-m-d\TH:i:sP');
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

