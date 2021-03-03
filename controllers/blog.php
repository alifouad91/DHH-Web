<?php
defined('C5_EXECUTE') or die("Access Denied.");

Class BlogController Extends Controller
{

    const ITEMS_TO_LOAD = 10;

    public function view()
    {
        /** @var TextHelper $th */
        /** @var Page $newsItem */
        $th = Loader::helper('text');


        $isAjax   = (boolean)$th->sanitize($this->get('isAjax'));
        $isGet    = false;
        $page     = (int)$th->sanitize($this->get('page'));
        $page     = $page > 0 ? $page : 1;


        /* @var $pageList PageList */
        $pageList = new PageList();
        $pageList->filterByPath($this->getCollectionObject()->getCollectionPath());
        $pageList->filterByCollectionTypeHandle('blog_page');

        $pageList->sortByPublicDateDescending();

        $list = $pageList->getPage($page);

        $this->set('list', $list);
    }

}
