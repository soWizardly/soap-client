<?php


namespace Khatfield\SoapClient\Header;


class EmailHeader extends AbstractHeaderOption
{
    protected $allowed_methods = [
        'create',
        'resetPassword',
        'update',
        'upsert',
    ];

    protected $trigger_auto_response_email = null;
    protected $trigger_other_email         = null;
    protected $trigger_user_email          = null;

    public function __construct($trigger_auto_response_email = false, $trigger_other_email = false, $trigger_user_email = false)
    {
        $this->trigger_auto_response_email = $trigger_auto_response_email;
        $this->trigger_other_email         = $trigger_other_email;
        $this->trigger_user_email          = $trigger_user_email;
    }

    public function getHeader($namespace)
    {
        $return = null;

        if(!is_null($this->trigger_auto_response_email) || !is_null($this->trigger_other_email) || !is_null($this->trigger_user_email)) {
            $return = new \SoapHeader($namespace, 'EmailHeader', [
                'triggerAutoResponseEmail' => $this->trigger_auto_response_email,
                'triggerOtherEmail'        => $this->trigger_other_email,
                'triggerUserEmail'         => $this->trigger_user_email,
            ]);
        }

        return $return;
    }
}