<?php

namespace Khatfield\SoapClient\Result;

/**
 * Standard object
 *
 */
class SObject
{
    /**
     * @var string
     */
    public $Id;

    public function getId()
    {
        return $this->Id;
    }

    /**
     * @param string $field
     * @param null $default
     *
     * @return mixed
     */
    public function get($field, $default = null)
    {
        if(isset($this->$field)){
            return $this->$field;
        } else {
            return $default;
        }
    }
}
