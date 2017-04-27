<?php

class Default_Model_ArticleMapper {

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

        if (!$this->_dbTable instanceof Default_Model_Article) {

            $this->setDbTable("Default_Model_DbTable_Article");
        }

        return $this->_dbTable;
    }

//    SELECT * FROM article a LEFT JOIN photography ph ON a.article_id = ph.photography_article_id


    public function getArticles($articleType, $page = 0, $ipp = -1) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('a' => 'article'))
                ->joinLeft(array('ph' => 'photography'), 'a.article_id = ph.photography_article_id')
                ->joinLeft(array('at' => 'article_type'), 'a.article_article_type_id = at.article_type_id')
                ->where('a.article_is_active = 1')
                ->where('a.article_active_from_date <= ?', time())
                ->where('at.article_type_name = ?', $articleType)
                ->order('a.article_active_from_date DESC');

//        echo $select->__toString();die;

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage($ipp);
        $result = $paginator->getCurrentItems();
        $entries = array();

        $this->_paginator = $paginator;

        foreach ($result as $row) {
            $article = new Admin_Model_Article();
            $photography = new Admin_Model_Photography();
            $photography->setOptions($row->toArray());
            $article->setOptions($row->toArray());
            $article->setArticle_photography($photography);
            $entries[$row['article_id']] = $article;
        }

        return $entries;
    }

    public function getArticleByUrl($url) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('a' => 'article'))
                ->joinLeft(array('ph' => 'photography'), 'a.article_id = ph.photography_article_id')
                ->where("a.article_url = ?", $url)
                ->where("a.article_is_active = 1")
                ->where("a.article_active_from_date <= ?", time());
//        echo $select->__toString();die;

        $row = $this->getDbTable()->fetchRow($select);

        $entries = array();


        $article = new Default_Model_Article();
        $photography = new Default_Model_Photography();

        if ($row) {

            $photography->setOptions($row->toArray());
            $article->setOptions($row->toArray());
            $article->setArticle_photography($photography);

            $entries = $article;
        }

        return $entries;
    }

//    public function getArticleById($articleId) {
//
//        $select = $this->getDbTable()
//                ->select()
//                ->setIntegrityCheck(false)
//                ->from(array('a' => 'article'))
//                ->joinLeft(array('ph' => 'photography'), 'a.article_id = ph.photography_article_id')
//                ->where('a.article_id = ?', $articleId);
//
////echo $select->__toString();die;
//     
//        $result = $this->getDbTable()->fetchRow($select);
//        
//        $entries = array();
//
//        if($result){
//            $article = new Admin_Model_Article();
//            $photography = new Admin_Model_Photography();
//            $photography->setOptions(current($result));
//            $article->setOptions(current($result));
//            $article->setArticle_photography($photography);
//            $entries = $article;
//        }
//
//        return $entries;
//    }
//    
//    
//    public function getPageById($pageId) {
//
//        $select = $this->getDbTable()
//                ->select()
//                ->from(array('p' => 'page'))
//                ->where('p.page_id = ?', $pageId);
//
////        echo $select->__toString();die;
//
//        $result = $this->getDbTable()->fetchRow($select);
//        
//        $entries = array();
//
//        if($result){
//            $page = new Admin_Model_Page();
//            $page->setOptions(current($result));
//            $entries = $page;
//        }
//
//        return $entries;
//    }
//    
//
}
