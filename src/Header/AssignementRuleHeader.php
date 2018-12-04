<?php


namespace Khatfield\SoapClient\Header;


class AssignementRuleHeader extends AbstractHeaderOption
{
    protected $allowed_methods = [
        'create',
        'update',
    ];

    protected $assignment_rule_id = null;
    protected $use_default_rule   = null;

    public function __construct($option)
    {
        if(is_numeric($option)) {
            //if the option is an integer, set the assignment rule id
            $this->assignment_rule_id = intval($option);
            $this->use_default_rule   = null;
        } elseif(is_bool($option)) {
            //if the option is boolean, set the use default rule flag
            $this->use_default_rule   = $option;
            $this->assignment_rule_id = null;
        } else {
            //clear them both
            $this->assignment_rule_id = null;
            $this->use_default_rule   = null;
        }
    }

    public function getHeader($namespace)
    {
        $return = null;
        if(!is_null($this->assignment_rule_id) || !is_null($this - $this->use_default_rule)) {
            $return = new \SoapHeader($namespace, 'AssignmentRuleHeader', [
                'assignmentRuleId' => $this->assignment_rule_id,
                'useDefaultRole'   => $this->use_default_rule,
            ]);
        }

        return $return;
    }

}