<?php


namespace Khatfield\SoapClient\Header;


class QueryOptions extends AbstractHeaderOption
{
    protected $allowed_methods = [
        'query',
        'queryMore',
        'retrieve',
    ];

    protected $batch_size = null;

    public function __construct($batch_size)
    {
        $this->batch_size = $batch_size;
    }

    public function getHeader($namespace)
    {
        $return = null;
        if(!is_null($this->batch_size)){
            $return = new \SoapHeader($namespace, 'QueryOptions', [
                'batchSize' => $this->batch_size
            ]);
        }

        return $return;
    }
}