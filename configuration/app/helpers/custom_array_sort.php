<?php

/*

    public function customSortArray (&$array, $sortBy)
    {
		
        if (!isset($array) || !is_array($array))
            return;

		$this->sorter = new CustomArraySort;

        if (isset($sortBy['key']))
            $this->sorter->setSortField( $sortBy['key'] );

        if (isset($sortBy['dir']))
            $this->sorter->setSortDirection( $sortBy['dir'] );

        if (isset($sortBy['case']))
            $this->sorter->setSortCaseSensitivity( $sortBy['case'] );

        if (isset($sortBy['maintainKeys']))
            $this->sorter->setMaintainKeys( $sortBy['case'] );
			
	}
			
*/

class CustomArraySort
{
	private $sortField='id';
	private $sortDirection='asc';
	private $sortCaseSensitivity=false;
	private $maintainKeys=false;

    public function setSortField( $field )
    {
        $this->sortField=$field;
    }

    public function getSortField()
    {
        return $this->sortField;
    }

    public function setSortDirection( $dir )
    {
        $this->sortDirection=$dir;
    }
    public function getSortDirection()
    {
        return $this->sortDirection;
    }

    public function setSortCaseSensitivity($sens)
    {
        $this->sortCaseSensitivity=$sens;
    }

    public function getSortCaseSensitivity()
    {
        return $this->sortCaseSensitivity;
    }

    public function setMaintainKeys($maintainKeys)
    {
        $this->maintainKeys=$maintainKeys;
    }

    public function getMaintainKeys()
    {
        return $this->maintainKeys;
    }



    public function doSort(&$array)
    {
        if ($this->getMaintainKeys)
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
// HIER ONGEVEER
        usort($array, array(
            $this,
            'doCustomSortArray'
        ));

        if ($maintainKeys) {

            foreach ((array) $array as $val) {

                if (is_array($val)) {

                    $y = array();

                    foreach ($val as $key2 => $val2) {

                        if ($key2 != $f)
                            $y[$key2] = $val2;
                    }

                    $d[$keys[$val[$f]]] = $y;
                }
                else {

                    $d[$keys[$val[$f]]] = $val;
                }
            }

            $array = $d;
        }
    }



    private function doCustomSortArray ($a, $b)
    {
        $f = $this->getSortField();

        $d = $this->getSortDirection();

        $c = $this->getSortCaseSensitivity();

        if (!isset($a[$f]) || !isset($b[$f]))
            return;

        if ($c != 's') {

            $a[$f] = strtolower($a[$f]);
            $b[$f] = strtolower($b[$f]);
        }

        return ($a[$f] > $b[$f] ? ($d == 'asc' ? 1 : -1) : ($a[$f] < $b[$f] ? ($d == 'asc' ? -1 : 1) : 0));
    }



}