<?php
/**
 * @abstract class AbstractDbObject
 * 
 * An abstract class modeling a database-access object
 */
abstract class AbstractDbObject
{

    protected $_dbHost = null;
    protected $_dbUsername = null;
    protected $_dbPassword = null;
    protected $_dbDatabase = null;
    protected static $_dbObjectInstance = null;


    /**
     * Static singleton pattern function to return (and maintain) a single instance of 
     * this object
     *
     */
    public static function getInstance()
    {
    }


    public function __construct()
    {
        if(defined('DB_HOST')) {
            $this->_dbHost = DB_HOST;
        }
        if(defined('DB_USER')) {
            $this->_dbUsername = DB_USER;
        }
        if(defined('DB_PASSWORD')) {
            $this->_dbPassword = DB_PASSWORD;
        }
        if(defined('DB_NAME')) {
            $this->_dbDatabase = DB_NAME;
        }
        $this->_connect();
    }


    public function __destruct()
    {
        $this->_dbObjectInstance = null;
    }


    abstract protected function _connect();
    abstract public function getOne($query);
    abstract public function getAll($query);
    abstract public function getRow($query);
    abstract public function getColumn($query);
    abstract public function query($query);
    abstract public function realEscapeString($string);
    abstract public function getLastInsertId();
    abstract public function getNumRowsAffected();
}