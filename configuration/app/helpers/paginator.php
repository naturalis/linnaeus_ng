<?php

/*
	add paginator.php to $usedHelpersBase
	
	$paginator = new Paginator;
	
    public function getPagination( $items )
    {
		$paginator->setItemsPerPage( $this->controllerSettings['termsPerPage'] );
		$paginator->setStart( $this->rHasVal('start') ? $this->rGetVal('start') : 0 );
	
		$paginator->setItems( $taxa );
		return $paginator->paginate();
			or
		return $paginator->paginate( $taxa );
	}

*/

class Paginator
{
	private $_itemsPerPage=25;
	private $_start=0;
	private $_items;

	public function setItemsPerPage( $int )
	{
		if ( is_int($int) && $int > 0 ) $this->_itemsPerPage=$int;
	}

	public function setStart( $start )
	{
		// determines first item to show
		if ( is_int($start) && $start > 0 ) $this->_start=$start;
	}

	public function setItems( $items )
	{
		if ( is_array($items) ) $this->_items=$items;
	}

	public function paginate( $items=null )
	{
        if ( isset($items) ) $this->setItems( $items );

        if ( empty($this->_items) ) return;

        //determine index of the first item to show on the previous page (if any)
        $prevStart = $this->_start==0 ? -1 : (($this->_start - $this->_itemsPerPage < 1) ? 0 : ($this->_start - $this->_itemsPerPage));

        //determine index of the first taxon to show on the next page (if any)
        $nextStart = ($this->_start + $this->_itemsPerPage >= count((array)$this->_items)) ? -1 : ($this->_start + $this->_itemsPerPage);

        // slice out only the taxa we need
        $slice = array_slice($this->_items, $this->_start, $this->_itemsPerPage);

        return
			array(
				'items' => $slice,
				'prevStart' => $prevStart,
				'currStart' => $this->_start,
				'nextStart' => $nextStart
			);
	
	}
	
}
