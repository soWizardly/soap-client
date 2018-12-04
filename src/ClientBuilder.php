<?php

namespace Khatfield\SoapClient;

use Khatfield\SoapClient\Soap\SoapClientFactory;
use Khatfield\SoapClient\Plugin\LogPlugin;
use Psr\Log\LoggerInterface;

/**
 * Salesforce SOAP client builder
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class ClientBuilder
{
    const PARTNER = 'partner';
    const ENTERPRISE = 'enterprise';

    protected $log;
    protected $wsdl;
    protected $username;
    protected $password;
    protected $token;
    protected $soap_options;


    /**
     * Construct client builder with required parameters
     *
     * @param string $wsdl         Path to your Salesforce WSDL
     * @param string $username     Your Salesforce username
     * @param string $password     Your Salesforce password
     * @param string $token        Your Salesforce security token
     * @param array  $soap_options Further options to be passed to the SoapClient
     */
    public function __construct($wsdl, $username, $password, $token, array $soap_options = array())
    {
        $this->wsdl         = $wsdl;
        $this->username     = $username;
        $this->password     = $password;
        $this->token        = $token;
        $this->soap_options = $soap_options;
    }

    /**
     * Enable logging
     *
     * @param LoggerInterface $log Logger
     *
     * @return ClientBuilder
     */
    public function withLog(LoggerInterface $log)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * Build the Salesforce SOAP client
     * @param string $type
     * @return Client
     */
    public function build($type = self::ENTERPRISE)
    {

        $soap_client_factory = new SoapClientFactory();
        $soap_client         = $soap_client_factory->factory($this->wsdl, $this->soap_options);

        if($type == self::PARTNER){
            $client = new PartnerClient($soap_client, $this->username, $this->password, $this->token);
        } else {
            $client = new EnterpriseClient($soap_client, $this->username, $this->password, $this->token);
        }

        if($this->log) {
            $log_plugin = new LogPlugin($this->log);
            $client->getEventDispatcher()->addSubscriber($log_plugin);
        }

        return $client;
    }
}
