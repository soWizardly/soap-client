<?php

namespace Khatfield\SoapClient\Request;

class MergeRequest
{
    public $masterRecord;
    public $recordToMergeIds = array();
}