<?php


namespace Khatfield\SoapClient\Header;


abstract class AbstractHeaderOption
{
    protected $allowed_methods = [];

    public function validFor($call)
    {
        return (in_array('*', $this->allowed_methods) || in_array($call, $this->allowed_methods));
    }

    public function __get($name)
    {
        return $this->$name;
    }

    abstract function getHeader($namespace);
}