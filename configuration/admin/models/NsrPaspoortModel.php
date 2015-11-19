<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

final class NsrPaspoortModel extends AbstractModel
{

    public function __construct ()
    {

        parent::__construct();

        $this->connectToDatabase() or die(_('Failed to connect to database '.
            $this->databaseSettings['database'].
        	' with user ' . $this->databaseSettings['user'] . '. ' .
            mysqli_connect_error() . '. Correct the getDatabaseSettings() settings
        	in configuration/admin/config.php.'));

     }

    public function __destruct ()
    {
        if ($this->databaseConnection)
		{
            $this->disconnectFromDatabase();
        }
        parent::__destruct();
    }

	public function getPassportCategories( $params )
	{
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;
		
		if( !isset( $language_id ) || !isset( $project_id )  || !isset( $taxon_id ) )
		{
			return;
		}
		
		$query="
			select
				_a.id,
				ifnull(_b.title,_a.page) as title,
				concat('TAB_',replace(upper(_a.page),' ','_')) as tabname,
				_a.show_order,
				_c.content,
				_c.id as content_id,
				_c.publish,
				_a.def_page,
				_a.always_hide

			from 
				%PRE%pages_taxa _a
				
			left join %PRE%pages_taxa_titles _b
				on _a.project_id=_b.project_id
				and _a.id=_b.page_id
				and _b.language_id = ". $language_id ."
				
			left join %PRE%content_taxa _c
				on _a.project_id=_c.project_id
				and _a.id=_c.page_id
				and _c.taxon_id =" . $taxon_id . "
				and _c.language_id = " . $language_id . "

			where 
				_a.project_id=" . $project_id . "

			order by 
				_a.show_order
		";
			
		return $this->freeQuery( $query );
	
	}

}
