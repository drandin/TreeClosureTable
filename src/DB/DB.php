<?php namespace TreeClosureTable\DB;

/**
 * Class DB
 * @package Smart\File\DB
 */
final class DB
{
    /**
     * @var null|\PDO
     */
    private static $PDO = null;

    private function __clone() {}
    private function __construct() {}

    /**
     * @param $param
     * @param string $db
     * @return null|\PDO
     */
    final public static function getPDO($param, $db = "mysql")
    {
        if (empty(self::$PDO)) {
            self::$PDO = new \PDO(
                "{$db}:host={$param['host']};dbname={$param['dbname']};charset={$param['charset']}",
                $param['user'],
                $param['password']
            );
        }

        return self::$PDO;
    }
}