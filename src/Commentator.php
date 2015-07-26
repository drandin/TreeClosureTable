<?php namespace TreeClosureTable;


/**
 * Class Commentator
 */
class Commentator extends ClosureTableManagement
{

    /**
     * @param $parameters
     * @param \PDO $db
     */
    public function __construct($parameters, $db)
    {
        parent::__construct($parameters, $db);
    }


}