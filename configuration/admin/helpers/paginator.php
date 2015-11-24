<?php

class Paginator
{
	private $_itemsPerPage=25;
	private $_start=0;
	private $_items;
	private $_result;

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


}
