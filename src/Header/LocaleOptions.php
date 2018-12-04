<?php


namespace Khatfield\SoapClient\Header;


class LocaleOptions extends AbstractHeaderOption
{
    protected $allowed_methods = [
        'describeSObject',
        'describeSObjects',
    ];

    protected $language = null;

    public function __construct($language)
    {
        $this->language = $language;
    }

    public function getHeader($namespace)
    {
        $return = null;

        if(!is_null($this->language)){
            $return = new \SoapHeader($namespace, 'LocaleOptions', [
                'language' => $this->language,
            ]);
        }

        return $return;
    }
}