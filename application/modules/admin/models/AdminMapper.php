<?php

class Admin_Model_AdminMapper {

    const SALT = '';
    
    protected $_dbTable;
    public $_paginator;

    public function setDbTable($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    /**
     * Get registered Zend_Db_Table instance, if param is filled return Zend_db_table instance and no Save this
     *
     * Lazy loads Default_Model_DbTable_Nabidka if no instance registered
     *
     * @return Zend_Db_Table_Abstract
     */
    public function getDbTable() {

        if (!$this->_dbTable instanceof Admin_Model_Admin) {

            $this->setDbTable("Admin_Model_DbTable_Admin");
        }

        return $this->_dbTable;
    }

    //save or update object
    public function save(Admin_Model_Admin $admin) {
        $salt = $this->generateSalt();
        $data = array(
            'admin_id' => $admin->getAdmin_id(),
            'admin_email' => $admin->getAdmin_email(),
            'admin_password' => sha1($admin->getAdmin_password().$salt),
            'admin_salt' => $salt,
            'admin_is_active' => 0,
        );

        if (null === ($id = $admin->getAdmin_id())) {
//            print_r($data);die;
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
    
    //salt pro bezpecnejsi heslo - nahodne generuje retezec o delce 16 znaku
    private function generateSalt(){
        return substr(sha1(rand(0, 999).uniqid()), 0, 16);
    }
    
    

}
