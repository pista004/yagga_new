<?php

class ArticleController extends Zend_Controller_Action {

    private $_articleMapper;

    public function init() {
        $this->_articleMapper = new Default_Model_ArticleMapper();
    }

    public function detailAction() {

        $url = $this->getRequest()->getParam('articleurl');
        $article = $this->_articleMapper->getArticleByUrl($url);
        if (!empty($article)) {

            $this->view->article = $article;

            $this->view->headTitle = $article->getArticle_seo_title() ? $article->getArticle_seo_title() : $article->getArticle_name();

            $StripTagsfilter = new Zend_Filter_StripTags();
            $this->view->metaDescription = $article->getArticle_seo_meta_description() ? $article->getArticle_seo_meta_description() : $StripTagsfilter->filter($article->getArticle_perex());

            $this->view->facebookImageUrl = "/images/upload_article/article_" . $article->getArticle_id() . "/thumb/" . $article->getArticle_photography()->getPhotography_path();
        }else{
            throw new ErrorException;
        }
    }

    public function indexAction() {


        $articles = $this->_articleMapper->getArticles('blog');

        $this->view->articles = $articles;
        $this->view->headTitle = "Blog";
    }

}
