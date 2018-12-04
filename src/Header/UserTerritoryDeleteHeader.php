<?php


namespace Khatfield\SoapClient\Header;


class UserTerritoryDeleteHeader extends AbstractHeaderOption
{
    protected $allowed_methods = ['delete'];

    protected $transfer_to_user_id = null;

    public function __construct($transfer_to_user_id)
    {
        $this->transfer_to_user_id = $transfer_to_user_id;
    }

    public function getHeader($namespace)
    {
        $return = null;
        if(!is_null($this->transfer_to_user_id)) {
            $return = new \SoapHeader($namespace, 'UserTerrirtoryDeleteHeader', [
                'transferToUserId' => $this->transfer_to_user_id,
            ]);
        }

        return $return;
    }
}