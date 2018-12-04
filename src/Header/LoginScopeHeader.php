<?php


namespace Khatfield\SoapClient\Header;


class LoginScopeHeader extends AbstractHeaderOption
{
    protected $allowed_methods = ['login'];

    protected $organization_id = null;
    protected $portal_id = null;

    public function __construct($org_id = null, $portal_id = null)
    {
        $this->organization_id = $org_id;
        $this->portal_id = $portal_id;
    }

    public function getHeader($namespace)
    {
        $return = null;

        if(!is_null($this->organization_id) || !is_null($this->portal_id)){
            $return = new \SoapHeader($namespace, 'LoginScopeHeader', [
                'organizationId' => $this->organization_id,
                'portalId' => $this->portal_id,
            ]);
        }


        return $return;
    }
}