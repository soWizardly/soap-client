<?php

namespace Phpforce\SoapClient\Result;

/**
 * Standard object
 *
 */
class SObject
{
    /**
     * @var string
     */
    protected $Id;

    public function getId()
    {
        return $this->Id;
    }

}