<?php

class Admin_Model_ArticleMapper {

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

        if (!$this->_dbTable instanceof Admin_Model_Article) {

            $this->setDbTable("Admin_Model_DbTable_Article");
        }

        return $this->_dbTable;
    }
    
    
//    SELECT * FROM article a LEFT JOIN photography ph ON a.article_id = ph.photography_article_id

    
     public function getArticles($page = 0, $ipp = -1) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('a' => 'article'))
                ->joinLeft(array('ph' => 'photography'), 'a.article_id = ph.photography_article_id')
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
    
    
    public function getArticleById($articleId) {

        $select = $this->getDbTable()
                ->select()
                ->setIntegrityCheck(false)
                ->from(array('a' => 'article'))
                ->joinLeft(array('ph' => 'photography'), 'a.article_id = ph.photography_article_id')
                ->where('a.article_id = ?', $articleId);

//echo $select->__toString();die;
     
        $result = $this->getDbTable()->fetchRow($select);
        
        $entries = array();

        if($result){
            $article = new Admin_Model_Article();
            $photography = new Admin_Model_Photography();
            $photography->setOptions(current($result));
            $article->setOptions(current($result));
            $article->setArticle_photography($photography);
            $entries = $article;
        }

        return $entries;
    }
    
    
    
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
    public function save(Admin_Model_Article $article) {

        $data = array(
            'article_id' => $article->getArticle_id(),
            'article_name' => $article->getArticle_name(),
            'article_insert_date' => $article->getArticle_insert_date(),
            'article_active_from_date' => $article->getArticle_active_from_date(),
            'article_url' => $article->getArticle_url(),
            'article_seo_title' => $article->getArticle_seo_title(),
            'article_seo_meta_description' => $article->getArticle_seo_meta_description(),
            'article_perex' => $article->getArticle_perex(),
            'article_text' => $article->getArticle_text(),
            'article_is_active' => $article->getArticle_is_active(),
            'article_article_type_id' => $article->getArticle_article_type_id(),
        );

        if (null === ($id = $article->getArticle_id())) {
            unset($data['article_id']);
            $this->getDbTable()->insert($data);
        } else {
            
            //projdu ukladana data a pokud obsahuji prazdny retezec nebo null, tak je odstranim, zbytek ulozim
            foreach ($data as $key => $d) {
                if (in_array($d, array("", null))) {
                    unset($data[$key]);
                }
            }
            $this->getDbTable()->update($data, array('article_id = ?' => $id));
        }
    }
    
    
    
    public function urlExists($url) {

        $existsUrl = false;
        // kontrola, zda zaznam existuje, pokud ano, vracim true, jinak false
        if ($url != "") {
            $select = $this->getDbTable()
                    ->select()
                    ->from(array('a' => 'article'), array('row_count' => 'COUNT(1)'))
                    ->where('a.article_url = ?', $url);

            $row = $this->getDbTable()->fetchRow($select);

            if ($row['row_count']) {
                $existsUrl = true;
            }
        }

        return $existsUrl;
    }
    
    
    
    
    public function delete($id) {
        $dbAdapter = $this->getDbTable()->getDefaultAdapter();
        $where = $dbAdapter->quoteInto('article_id = ?', (int) $id);
        $dbAdapter->delete("article", $where);
    }



}
