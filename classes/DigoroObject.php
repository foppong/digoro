<?php
abstract class DigoroObject {

    protected $_dbObject = null;
    protected $_mainTable;
    protected $_mainTablePrimaryKey;
    protected $_id;
    protected $_userIdColumn = 'id_user';

    public function __construct()
    {
        $this->_dbObject = MySQLiDbObject::getInstance();
    }

    // Function to check if user is the manager
    public function isManager($userID, $lookupID = null)
    {
        // Make the query to retreive manager id associated with team:
        $q = "SELECT {$this->_userIdColumn}
              FROM {$this->_mainTable}
              WHERE {$this->_mainTablePrimaryKey} = " . (!empty($lookupID) ? (int)$lookupID : (int)$this->_id) . "
              LIMIT 1";

        // Execute the query
        $result = $this->_dbObject->getOne($q);

        return ($result == $userID);
    } // End of isManager function
}