<?php

class CustomArraySort
{
	private $sortField='id';
	private $sortDirection='asc';
	private $sortCaseSensitivity=false;
	private $maintainKeys=false;
	private $array=array();
	
    public function setSortyBy( $sortBy )
	{
        if ( isset($sortBy['key']) )
		{
            $this->setSortField($sortBy['key']);
		}

        if ( isset($sortBy['dir']) )
		{
            $this->setSortDirection($sortBy['dir']);
		}

        if ( isset($sortBy['case']) )
		{
            $this->setSortCaseSensitivity($sortBy['case']);
		}

        if ( isset($sortBy['maintainKeys']) )
		{
            $this->setMaintainKeys($sortBy['maintainKeys']);
		}

	}

    public function sortArray( $array )
	{
		if (isset($array)  && is_array($array))
		{
			$this->setArray( $array );
			$this->doSort();
		}
	}

    public function getSortedArray()
	{
		return $this->getArray();
	}

    private function doSort()
    {
		$array=$this->getArray();

        if ($this->getMaintainKeys())
		{
            $keys=array();

            $f=md5(uniqid(null, true));

            foreach ((array) $array as $key => $val)
			{
                $x = md5(json_encode($val) . $key);
                $array[$key][$f] = $x;
                $keys[$x] = $key;
            }
        }

        usort($array, array($this,'doCustomSortArray'));

        if ($this->getMaintainKeys())
		{
	        $d=array();
			
            foreach ((array)$array as $val)
			{
                if (is_array($val))
				{
                    $y = array();

                    foreach ($val as $key2 => $val2)
					{
                        if ($key2 != $f)
						{
                            $y[$key2] = $val2;
						}
                    }

                    $d[$keys[$val[$f]]] = $y;
                }
                else 
				{
                    $d[$keys[$val[$f]]] = $val;
                }
            }
			
			$this->setArray( $d );
        }
		else
		{
			$this->setArray( $array );
		}
    }

    private function doCustomSortArray($a, $b)
    {
        $f = $this->getSortField();
        $d = $this->getSortDirection();
        $c = $this->getSortCaseSensitivity();

        if (!isset($a[$f]) || !isset($b[$f]))
		{
            return;
		}

        if ($c != 's')
		{
            $a[$f] = strtolower($a[$f]);
            $b[$f] = strtolower($b[$f]);
        }

        return ($a[$f] > $b[$f] ? ($d == 'asc' ? 1 : -1) : ($a[$f] < $b[$f] ? ($d == 'asc' ? -1 : 1) : 0));
    }





    private function setSortField( $p )
    {
        $this->sortField=$p;
    }

    private function getSortField()
    {
        return $this->sortField;
    }

    private function setSortDirection( $p )
    {
        $this->sortDirection=$p;
    }
    private function getSortDirection()
    {
        return $this->sortDirection;
    }

    private function setSortCaseSensitivity( $p )
    {
        $this->sortCaseSensitivity=$p;
    }

    private function getSortCaseSensitivity()
    {
        return $this->sortCaseSensitivity;
    }

    private function setMaintainKeys( $p )
    {
        $this->maintainKeys=$p;
    }

    private function getMaintainKeys()
    {
        return $this->maintainKeys;
    }

    private function setArray( $p )
    {
        $this->array=$p;
    }

    private function getArray()
    {
        return $this->array;
    }



}