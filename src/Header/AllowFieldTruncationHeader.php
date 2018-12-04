<?php


namespace Khatfield\SoapClient\Header;


class AllowFieldTruncationHeader extends AbstractHeaderOption
{
    protected $allowed_methods = [
        'convertLead',
        'create',
        'merge',
        'process',
        'undelete',
        'update',
        'upsert',
    ];

    protected $allow_field_truncation = null;

    public function __construct($allow_field_truncation)
    {
        $this->allow_field_truncation = $allow_field_truncation;
    }

    public function getHeader($namespace)
    {
        if(!is_null($this->allow_field_truncation)){
            $return = new \SoapHeader($namespace, 'AllowFieldTruncationHeader', [
                'allowFieldTruncation' => $this->allow_field_truncation,
            ]);
        } else {
            $return = null;
        }

        return $return;
    }

}