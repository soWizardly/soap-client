<?php


namespace Khatfield\SoapClient\Header;


class PackageVersion extends AbstractHeaderOption
{
    protected $allowed_methods = [];

    protected $major_number;
    protected $minor_number;
    protected $namespace;

    /**
     * @return mixed
     */
    public function getMajorNumber()
    {
        return $this->major_number;
    }

    /**
     * @return mixed
     */
    public function getMinorNumber()
    {
        return $this->minor_number;
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    public function __construct($major_number, $minor_number, $namespace)
    {
        $this->major_number = $major_number;
        $this->minor_number = $minor_number;
        $this->namespace    = $namespace;
    }

    public function getHeader($namespace)
    {
        //these headers are generated from PackageVersionHeader
        return null;
    }

}