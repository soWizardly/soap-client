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
}