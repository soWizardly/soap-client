<?php
namespace Khatfield\SoapClient;

use Khatfield\SoapClient\Exception\SaveException;
use Khatfield\SoapClient\Result;

/**
 * Salesforce API client interface
 *
 * @author David de Boer <david@ddeboer.nl>
 */
interface ClientInterface
{
    /**
     * Converts a Lead into an Account, Contact, or (optionally) an Opportunity
     *
     * @param array $leadConverts LeadConvert[]
     *
     * @throws \SoapFault
     *
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_convertlead.htm
     */
    public function convertLead(array $leadConverts);

    /**
     * Create one or more Salesforce objects
     *
     * @param array  $objects    Array of Salesforce objects
     * @param string $objectType Object type, e.g., account or contact
     *
     * @throws \SoapFault
     * @throws SaveException
     *
     * @return Result\SaveResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_create.htm
     */
    public function create(array $objects, $objectType);

    /**
     * Deletes one or more records from your organization’s data
     *
     * @param array $ids Salesforce object IDs
     *
     * @throws \SoapFault
     * @throws SaveException
     *
     * @return Result\DeleteResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_delete.htm
     */
    public function delete(array $ids);

    /**
     * Retrieves a list of available objects for your organization’s data
     *
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_describeglobal.htm
     *
     * @throws \SoapFault
     *
     * @return Result\DescribeGlobalResult
     */
    public function describeGlobal();

    /**
     * Describes metadata (field list and object properties) for the specified object or array of objects
     *
     * @param array $objectNames
     *
     * @throws \SoapFault
     *
     * @return Result\DescribeSObjectResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_describesobjects.htm
     */
    public function describeSObjects(array $objectNames);

    /**
     * Returns information about the standard and custom apps available to the
     * logged-in
     *
     * @throws \SoapFault
     *
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_invalidatesessions.htm
     */
    public function describeTabs();

    /**
     * Delete records from the recycle bin immediately
     *
     * @throws \SoapFault
     * @throws SaveException
     *
     * @param array $ids Object ids
     */
    public function emptyRecycleBin(array $ids);

    /**
     * Retrieves the list of individual records that have been deleted within
     * the given timespan for the specified object
     *
     * @param string    $objectType Object type
     * @param \DateTime $startDate  Start date
     * @param \DateTime $endDate    End date
     *
     * @throws \SoapFault
     *
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_getdeleted.htm
     * @return Result\GetDeletedResult
     */
    public function getDeleted($objectType, \DateTime $startDate, \DateTime $endDate);

    /**
     * Retrieves the list of individual objects that have been updated (added or
     * changed) within the given timespan for the specified object
     *
     * @param string    $objectType Object type
     * @param \DateTime $startDate  Start date
     * @param \DateTime $endDate    End date
     *
     * @throws \SoapFault
     *
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_getupdated.htm
     * @return Result\GetUpdatedResult
     */
    public function getUpdated($objectType, \DateTime $startDate, \DateTime $endDate);

    /**
     * Ends one or more sessions specified by a sessionId
     *
     * @param array $sessionIds Array of session ids
     *
     * @throws \BadMethodCallException
     *
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_invalidatesessions.htm
     */
    public function invalidateSessions(array $sessionIds);

    /**
     * Logs in to the login server and starts a client session
     *
     * @param string $username Salesforce username
     * @param string $password Salesforce password
     * @param string $token    Salesforce security token
     *
     * @throws \SoapFault
     *
     * @return Result\LoginResult
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_login.htm
     */
    public function login($username, $password, $token);

    /**
     * Ends the session of the logged-in user
     *
     * @throws \SoapFault
     *
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_logout.htm
     */
    public function logout();

    /**
     * Merge a Salesforce lead, contact or account with one or two other
     * Salesforce leads, contacts or accounts
     *
     * @param array  $mergeRequests Array of merge request objects
     * @param string $objectType    Object type, e.g., account or contact
     *
     * @throws \SoapFault
     *
     * @return Result\MergeResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_merge.htm
     */
    public function merge(array $mergeRequests, $objectType);

    /**
     * Submits an array of approval process instances for approval, or processes
     * an array of approval process instances to be approved, rejected, or
     * removed
     *
     * @param array $processRequests
     *
     * @throws \BadMethodCallException
     *
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_process.htm
     */
    public function process(array $processRequests);

    /**
     * Query salesforce API and return results as record iterator
     *
     * @param string $query
     *
     * @throws \SoapFault
     *
     * @return Result\RecordIterator
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_query.htm
     */
    public function query($query);

    /**
     * Retrieves data from specified objects, whether or not they have been
     * deleted
     *
     * @param string $query
     *
     * @throws \SoapFault
     *
     * @return Result\QueryResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_queryall.htm
     */
    public function queryAll($query);

    /**
     * Retrieves the next batch of objects from a query
     *
     * @param string $queryLocator
     *
     * @throws \SoapFault
     *
     * @return Result\QueryResult
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_querymore.htm
     */
    public function queryMore($queryLocator);

    /**
     * Retrieves one or more records based on the specified IDs
     *
     * @param array  $fields     Fields to retrieve on the object
     * @param array  $ids        IDs of objects to retrieve
     * @param string $objectType Object type, e.g., account or contact
     *
     * @throws \SoapFault
     *
     * @return Result\SObject[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_retrieve.htm
     */
    public function retrieve(array $fields, array $ids, $objectType);

    /**
     * Executes a text search in your organization’s data
     *
     * @param string $searchString
     *
     * @throws \SoapFault
     *
     * @return Result\SearchResult
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_search.htm
     */
    public function search($searchString);

    /**
     * Undeletes records from the Recycle Bin
     *
     * @param array $ids
     *
     * @throws \SoapFault
     * @throws SaveException
     *
     * @return Result\UndeleteResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_undelete.htm
     */
    public function undelete(array $ids);

    /**
     * Updates one or more existing records in your organization’s data
     *
     * @param array  $objects    Array of objects
     * @param string $objectType Object type, e.g., account or contact
     *
     * @throws \SoapFault
     * @throws SaveException
     *
     * @return Result\SaveResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_update.htm
     */
    public function update(array $objects, $objectType);

    /**
     * Creates new records and updates existing records; uses a custom field to
     * determine the presence of existing records
     *
     * @param string $externalFieldName Name of external field (must be id
     *                                  or external id)
     * @param array  $objects           Array of objects
     * @param string $objectType        Object type, e.g., account or contact
     *
     * @throws \SoapFault
     *
     * @return Result\UpsertResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_upsert.htm
     */
    public function upsert($externalFieldName, array $objects, $objectType);

    /**
     * Retrieves the current system timestamp (Coordinated Universal Time (UTC)
     * time zone) from the API
     *
     * @throws \SoapFault
     *
     * @return Result\GetServerTimestampResult
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_getservertimestamp.htm
     */
    public function getServerTimestamp();

    /**
     * Get user info
     *
     * @throws \SoapFault
     *
     * @return Result\GetUserInfoResult
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_getuserinfo.htm
     */
    public function getUserInfo();

    /**
     * Changes a user’s password to a temporary, system-generated value
     *
     * @param string $userId
     *
     * @throws \BadMethodCallException
     */
    public function resetPassword($userId);

    /**
     * Immediately sends an email message
     *
     * @param array $emails
     *
     * @throws \SoapFault
     */
    public function sendEmail(array $emails);

    /**
     * Sets the specified user’s password to the specified value
     *
     * @param string $userId   User id
     * @param string $password Password
     *
     * @throws \SoapFault
     */
    public function setPassword($userId, $password);
}

