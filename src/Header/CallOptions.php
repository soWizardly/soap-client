<?php

namespace Khatfield\SoapClient\Header;


class CallOptions extends AbstractHeaderOption
{
    protected $allowed_methods = ['*'];

    protected $client            = null;
    protected $default_namespace = null;

    public function __construct($client, $default_namespace = null)
    {
        $this->client            = $client;
        $this->default_namespace = $default_namespace;
    }

    public function getHeader($namespace)
    {
        $return = null;

        if(!is_null($this->client)) {
            $return = new \SoapHeader($namespace, 'CallOptions', [
                'client'           => $this->client,
                'defaultNamespace' => $this->default_namespace,
            ]);
        }

        return $return;
    }
}