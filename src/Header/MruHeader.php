<?php


namespace Khatfield\SoapClient\Header;


class MruHeader extends AbstractHeaderOption
{
    protected $allowed_methods = [
        'create',
        'merge',
        'query',
        'retrieve',
        'update',
        'upsert',
    ];

    protected $update_mru_flag = null;

    public function __construct($update_mru_flag)
    {
        $this->update_mru_flag = $update_mru_flag;
    }

    public function getHeader($namespace)
    {
        $return = null;

        if(!is_null($this->update_mru_flag)) {
            $return = new \SoapHeader($namespace, 'MruHeader', [
                'updateMru' => $this->update_mru_flag,
            ]);
        }

        return $return;
    }
}