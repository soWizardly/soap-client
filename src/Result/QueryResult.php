<?php

namespace Khatfield\SoapClient\Result;

/**
 * Query result
 */
class QueryResult
{
    protected $done;
    protected $queryLocator;
    protected $records = [];
    protected $size;

    /**
     * @return boolean
     */
    public function isDone()
    {
        return $this->done;
    }

    /**
     * @return string
     */
    public function getQueryLocator()
    {
        return $this->queryLocator;
    }

    /**
     * @return array
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @param SObject[] $records
     *
     * @return $this
     */
    public function setRecords($records)
    {
        $this->records = $records;

        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    public function getRecord($index)
    {
        $return = null;

        if (isset($this->records[$index])) {
            $return = $this->records[$index];
        }

        return $return;
    }
}