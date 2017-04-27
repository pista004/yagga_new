<?php

/**
 * This is the DbTable class for the guestbook table.
 */
class Admin_Model_DbTable_UserProfile extends Zend_Db_Table_Abstract {

    /** Table name */
    protected $_name = "user_profile";
    protected $_primary = "user_profile_id";

}