<?php
/**
 * class MySQLDbObject
 *
 * A MySQL database-access object
 */
class MySQLiDbObject extends AbstractDbObject
{

    protected $_dbConn = null;
    private $_charSet = 'utf8';


    public function __construct()
    {
        parent::__construct();

        if(defined('DB_CHARSET')) {
            $this->_charSet = DB_CHARSET;
        }
        $this->_dbConn->set_charset($this->_charSet);
    }


    /**
     * Static singleton pattern function to return (and maintain) a single instance of 
     * this object
     *
     */
    public static function getInstance()
    {
        if(!isset(self::$_dbObjectInstance))
        {
            $className = get_class();
            self::$_dbObjectInstance = new $className;
        }

        return self::$_dbObjectInstance;
    }


    /**
     * Destructor to close the MySQL connection
     *
     */
    public function __destruct()
    {
        if($this->_isConnected()) {
            mysqli_close($this->_dbConn);
            $this->_dbConn = null;
        }
    }


    protected function _connect()
    {
        if(!$this->_isConnected()) {
            $this->_dbConn = mysqli_connect($this->_dbHost, $this->_dbUsername, $this->_dbPassword, $this->_dbDatabase);
            if($this->_dbConn === false) {
                $this->_error("Unable to connect to database at host '{$this->_dbHost}'.");
            }
        }
    }


    private function _isConnected()
    {
        return (isset($this->_dbConn) && $this->_dbConn !== false && mysqli_ping($this->_dbConn));
    }


    private function _error($msg)
    {
        throw new Exception("ERROR: {$msg}" . ($this->_isConnected() ? "\n" . mysqli_error($this->_dbConn) : ''));
    }


    public function getOne($query)
    {
        $this->_connect();
        $result = $this->query($query);
        if(!is_bool($result)) {
            if($result->num_rows == 1) {
                $data = $result->fetch_row();
                if(count($data) == 1) {
                    return $data[0];
                }
                else {
                    $this->_error("Query resultset contains more than one column.\nQuery: {$query}");
                }
            }
            else if($result->num_rows == 0) {
                return false;
            }
            else {
                $this->_error("Query resultset contains more than one row.\nQuery: {$query}"); 
            }
        }
        else {
            $this->_error("Query does not result a resultset.\nQuery: {$query}");
        }
    }


    public function getAll($query)
    {
        $this->_connect();
        $result = $this->query($query);
        if(!is_bool($result)) {
            $data = array();
            $row = $result->fetch_assoc();
            while($row !== null) {
                $data[] = $row;
                $row = $result->fetch_assoc();
            }
            return $data;
        }
        else {
            $this->_error("Query does not result a resultset.\nQuery: {$query}");
        }
    }


    public function getRow($query)
    {
        $this->_connect();
        $result = $this->query($query);
        if(!is_bool($result)) {
            if($result->num_rows == 1) {
                return $result->fetch_assoc();
            }
            else if($result->num_rows == 0) {
                return false;
            }
            else {
                $this->_error("Query resultset contains more than one row.\nQuery: {$query}"); 
            }
        }
        else {
            $this->_error("Query does not result a resultset.\nQuery: {$query}");
        }
    }


    public function getColumn($query)
    {
        $this->_connect();
        $result = $this->query($query);
        if(!is_bool($result)) {
            $data = array();
            while($row = $result->fetch_row() !== null) {
                if($count($row) == 1) {
                    $data[] = $row[0];
                }
                else {
                    $this->_error("Query resultset contains more than one column.\nQuery: {$query}");
                }
            }
            return $data;
        }
        else {
            $this->_error("Query does not result a resultset.\nQuery: {$query}");
        }
    }


    public function query($query)
    {
        $this->_connect();
        $result = mysqli_query($this->_dbConn, $query);

        if($result === false) {
            $this->_error("SQL query error: {$this->_dbConn->error}\nQuery: {$query}");
        }
        return $result;
    }


    public function realEscapeString($string)
    {
        return $this->_dbConn->real_escape_string($string);
    }


    public function getNextPrimaryKey($table, $primaryKeyColumn)
    {
        return $this->getOne("SELECT IFNULL(MAX(`{$primaryKeyColumn}`)+1, 1) FROM `{$table}`");
    }


    public function getLastInsertId()
    {
        return $this->_dbConn->insert_id;
    }


    public function getNumRowsAffected()
    {
        return $this->_dbConn->affected_rows;
    }
}