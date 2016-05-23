<?php

class Paginator
{
	private $_itemsPerPage=25;
	private $_start=0;
	private $_items;
	private $_result;

	private $numPages;
    private $currentPage;
	private $maxPagesToShow = 10;
	private $pageUrl;


	public function setItemsPerPage( $int )
	{
		if ( is_numeric($int) && $int > 0 ) $this->_itemsPerPage=$int;
	}

	public function setStart( $start )
	{
	    // determines first item to show
		if ( is_numeric($start) && $start > 0 ) $this->_start=$start;
	}

	public function setItems( $items )
	{
		if ( is_array($items) ) $this->_items=$items;
	}

	public function paginate( )
	{
	    if ( empty($this->_items) ) return;

        //determine index of the first item to show on the previous page (if any)
        $prevStart = $this->_start==0 ? -1 : (($this->_start - $this->_itemsPerPage < 1) ? 0 : ($this->_start - $this->_itemsPerPage));

        //determine index of the first taxon to show on the next page (if any)
        $nextStart = ($this->_start + $this->_itemsPerPage >= count((array)$this->_items)) ? -1 : ($this->_start + $this->_itemsPerPage);

        // slice out only the taxa we need
        $slice = array_slice($this->_items, $this->_start, $this->_itemsPerPage);

        $this->_result=
			array(
				'items' => $slice,
				'prevStart' => $prevStart,
				'currStart' => $this->_start,
				'nextStart' => $nextStart
			);

	}

	public function getItems( )
	{
		return $this->_result;
	}





    /*
     * Ruud 23-05-16: added pager
     */

	public function getItemsWithPrintedPager ()
	{
        $this->paginate();
        $this->setNumPages();
        $this->setCurrentPage();
        $this->_result['pager'] = $this->printPaginator();
        return $this->_result;
	}

	protected function setCurrentPage ()
	{
        $this->currentPage = (int) ceil($this->_start/$this->_itemsPerPage) + 1;
	}

	protected function getPageUrl ($pageNum)
	{
        parse_str($_SERVER['QUERY_STRING'], $q);
        $q['start'] = $pageNum;
        return (isset($_SERVER['HTTPS']) ? "https" : "http") .
            "://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]" . '?' . http_build_query($q);
	}

	protected function setNumPages ()
	{
        $this->numPages = ($this->_itemsPerPage == 0 ? 0 : (int) ceil(count($this->_items)/$this->_itemsPerPage));
	}

    public function getNextPage()
    {
        if ($this->currentPage < $this->numPages) {
            return $this->pageNumToOffset($this->currentPage + 1);
        }

        return null;
    }

    public function getPrevPage()
    {
        if ($this->currentPage > 1) {
            return $this->pageNumToOffset($this->currentPage - 1);
        }

        return null;
    }

    public function getNextUrl()
    {
        if (!$this->getNextPage()) {
            return null;
        }

        return $this->getPageUrl($this->getNextPage());
    }

    public function getPrevUrl()
    {
        if (is_null($this->getPrevPage())) {
            return null;
        }

        return $this->getPageUrl($this->getPrevPage());
    }

	public function getPages()
    {
        $pages = array();

        if ($this->numPages <= 1) {
            return array();
        }

        if ($this->numPages <= $this->maxPagesToShow) {
            for ($i = 1; $i <= $this->numPages; $i++) {
                $pages[] = $this->createPage($i, $i == $this->currentPage);
            }
        } else {

            // Determine the sliding range, centered around the current page.
            $numAdjacents = (int) floor(($this->maxPagesToShow - 3) / 2);

            if ($this->currentPage + $numAdjacents > $this->numPages) {
                $slidingStart = $this->numPages - $this->maxPagesToShow + 2;
            } else {
                $slidingStart = $this->currentPage - $numAdjacents;
            }
            if ($slidingStart < 2) $slidingStart = 2;

            $slidingEnd = $slidingStart + $this->maxPagesToShow - 3;
            if ($slidingEnd >= $this->numPages) $slidingEnd = $this->numPages - 1;

            // Build the list of pages.
            $pages[] = $this->createPage(1, $this->currentPage == 1);
            if ($slidingStart > 2) {
                $pages[] = $this->createPageEllipsis();
            }
            for ($i = $slidingStart; $i <= $slidingEnd; $i++) {
                $pages[] = $this->createPage($i, $i == $this->currentPage);
            }
            if ($slidingEnd < $this->numPages - 1) {
                $pages[] = $this->createPageEllipsis();
            }
            $pages[] = $this->createPage($this->numPages, $this->currentPage == $this->numPages);
        }

        return $pages;
    }

    protected function createPage($pageNum, $isCurrent = false)
    {
        return array(
            'num' => $pageNum,
            'url' => $this->getPageUrl($this->pageNumToOffset($pageNum)),
            'isCurrent' => $isCurrent,
        );
    }

    protected function createPageEllipsis()
    {
        return array(
            'num' => '...',
            'url' => null,
            'isCurrent' => false,
        );
    }

    protected function pageNumToOffset ($pageNum)
    {
        return ($pageNum - 1) * $this->_itemsPerPage;
    }

    public function printPaginator ()
    {
        if ($this->numPages <= 1) {
            return '';
        }

        $html = '<ul class="paginator">';
        if ($this->getPrevUrl()) {
            $html .= '<li><a href="' . $this->getPrevUrl() . '">&lt;</a></li>';
        }

        foreach ($this->getPages() as $page) {
            if ($page['url']) {
                $html .= '<li' . ($page['isCurrent'] ? ' class="current-page"' : '') . '><a href="' . $page['url'] . '">' . $page['num'] . '</a></li>';
            } else {
                $html .= '<li class="disabled"><span>' . $page['num'] . '</span></li>';
            }
        }

        if ($this->getNextUrl()) {
            $html .= '<li><a href="' . $this->getNextUrl() . '">&gt;</a></li>';
        }
        $html .= '</ul>';

        return $html;
    }


}
