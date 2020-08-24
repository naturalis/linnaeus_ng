<?php
/** @noinspection PhpMissingParentCallMagicInspection */
include_once (__DIR__ . "/AbstractModel.php");

final class NsrTaxonImagesModel extends AbstractModel
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

	public function getTaxonMedia( $params )
	{
		$distribution_maps=isset($params['distribution_maps']) ? $params['distribution_maps'] : null;
		$type_id_preferred=isset($params['type_id_preferred']) ? $params['type_id_preferred'] : null;
		$type_id_valid=isset($params['type_id_valid']) ? $params['type_id_valid'] : null;
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;
		$overview=isset($params['overview']) ? $params['overview'] : null;
		$media_id=isset($params['media_id']) ? $params['media_id'] : null;
		$sort=isset($params['sort']) ? $params['sort'] : null;
		$limit=isset($params['limit']) ? $params['limit'] : null;
		$offset=isset($params['offset']) ? $params['offset'] : null;
		
		if( !isset( $type_id_preferred ) || !isset( $type_id_valid )  || !isset( $language_id )  || !isset( $project_id ) )
		{
			return;
		}
		
		$query="		
			select
				SQL_CALC_FOUND_ROWS
				_m.id,
				_m.taxon_id,
				_m.overview_image,
				file_name as image,
				file_name as thumb,
				_k.taxon,
				_z.name as common_name,
				_j.name,
				trim(replace(_j.name,ifnull(_j.authorship,''),'')) as nomen,
				".($distribution_maps!==false?
					"_map1.meta_data as meta_map_source,
					 _map2.meta_data as meta_map_description,": "")."

				case
					when 
						date_format(_meta1.meta_date,'%e %M %Y') is not null
						and DAYOFMONTH(_meta1.meta_date)!='0'
					then
						date_format(_meta1.meta_date,'%e %M %Y')
					when 
						date_format(_meta1.meta_date,'%M %Y') is not null
					then
						date_format(_meta1.meta_date,'%M %Y')
					when 
						date_format(_meta1.meta_date,'%Y') is not null
						and YEAR(_meta1.meta_date)!='0000'
					then
						date_format(_meta1.meta_date,'%Y')
					when 
						YEAR(_meta1.meta_date)='0000'
					then
						null
					else
						_meta1.meta_date
				end as meta_datum,

				_meta2.meta_data as meta_short_desc,
				_meta3.meta_data as meta_geografie,

				case
					when 
						date_format(_meta4.meta_date,'%e %M %Y') is not null
						and DAYOFMONTH(_meta4.meta_date)!=0
					then
						date_format(_meta4.meta_date,'%e %M %Y')
					when 
						date_format(_meta4.meta_date,'%M %Y') is not null
					then
						date_format(_meta4.meta_date,'%M %Y')
					when 
						date_format(_meta4.meta_date,'%Y') is not null
						and YEAR(_meta4.meta_date)!='0000'
					then
						date_format(_meta4.meta_date,'%Y')
					when 
						YEAR(_meta4.meta_date)='0000'
					then
						null
					else
						_meta4.meta_date
				end as meta_datum_plaatsing,

				_meta5.meta_data as meta_copyrights,
				_meta6.meta_data as meta_validator,
				_meta7.meta_data as meta_adres_maker,
				_meta8.meta_data as photographer
			
			from  %PRE%media_taxon _m
			
			left join %PRE%media_meta _c
				on _m.project_id=_c.project_id
				and _m.id = _c.media_id
				and _c.sys_label = 'beeldbankFotograaf'
			
			left join %PRE%taxa _k
				on _m.taxon_id=_k.id
				and _m.project_id=_k.project_id
				
			left join %PRE%projects_ranks _f
				on _k.rank_id=_f.id
				and _k.project_id=_f.project_id

			left join %PRE%names _z
				on _m.taxon_id=_z.taxon_id
				and _m.project_id=_z.project_id
				and _z.type_id=".$type_id_preferred."
				and _z.language_id=".LANGUAGE_ID_DUTCH."

			left join %PRE%names _j
				on _m.taxon_id=_j.taxon_id
				and _m.project_id=_j.project_id
				and _j.type_id=".$type_id_valid."
				and _j.language_id=".LANGUAGE_ID_SCIENTIFIC."
				

			left join %PRE%media_meta _map1
				on _m.id=_map1.media_id
				and _m.project_id=_map1.project_id
				and _map1.sys_label='verspreidingsKaartBron'

			left join %PRE%media_meta _map2
				on _m.id=_map2.media_id
				and _m.project_id=_map2.project_id
				and _map2.sys_label='verspreidingsKaartTitel'

			left join %PRE%media_meta _meta1
				on _m.id=_meta1.media_id
				and _m.project_id=_meta1.project_id
				and _meta1.sys_label='beeldbankDatumVervaardiging'

			left join %PRE%media_meta _meta2
				on _m.id=_meta2.media_id
				and _m.project_id=_meta2.project_id
				and _meta2.sys_label='beeldbankOmschrijving'
			
			left join %PRE%media_meta _meta3
				on _m.id=_meta3.media_id
				and _m.project_id=_meta3.project_id
				and _meta3.sys_label='beeldbankLokatie'
			
			left join %PRE%media_meta _meta4
				on _m.id=_meta4.media_id
				and _m.project_id=_meta4.project_id
				and _meta4.sys_label='beeldbankDatumAanmaak'
			
			left join %PRE%media_meta _meta5
				on _m.id=_meta5.media_id
				and _m.project_id=_meta5.project_id
				and _meta5.sys_label='beeldbankCopyright'

			left join %PRE%media_meta _meta6
				on _m.id=_meta6.media_id
				and _m.project_id=_meta6.project_id
				and _meta6.sys_label='beeldbankValidator'
				and _meta6.language_id=".$language_id."

			left join %PRE%media_meta _meta7
				on _m.id=_meta7.media_id
				and _m.project_id=_meta7.project_id
				and _meta7.sys_label='beeldbankAdresMaker'
				and _meta7.language_id=".$language_id."

			left join %PRE%media_meta _meta8
				on _m.id=_meta8.media_id
				and _m.project_id=_meta8.project_id
				and _meta8.sys_label='beeldbankFotograaf'
			
			left join %PRE%media_meta _meta9
				on _m.id=_meta9.media_id
				and _m.project_id=_meta9.project_id
				and _meta9.sys_label='verspreidingsKaart'
			
			where
				_m.project_id=".$project_id."
				".($taxon_id ? "and _m.taxon_id=". $this->escapeString( $taxon_id ) : "")."
				".($distribution_maps!==null ? "and ifnull(_meta9.meta_data,0)!=".($distribution_maps?'0':'1') : "" )."
				".($overview ? "and _m.overview_image=1" : "")."
				".($media_id ? "and _m.id=". $this->escapeString( $media_id ) : "")."

			".(isset($sort) ? "order by ".$sort : "")."
			".(isset($limit) ? "limit ".$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".$offset : "");
			
		$data = $this->freeQuery( $query );
		$count = $this->freeQuery( 'select found_rows() as total' );
		
		return array(
			'data'=>$data,
			'count'=>$count[0]['total']
		);
	
	}

	public function getTaxonByNsrId( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$nsr_id=isset($params['nsr_id']) ? $params['nsr_id'] : null;
		
		if( !isset( $nsr_id )  || !isset( $project_id ) )
		{
			return;
		}
		
		$query="
			select
				_ids.lng_id as taxon_id,
				_a.taxon
			from %PRE%taxa _a
			
			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id = _trash.lng_id
				and _trash.item_type='taxon'

			left join %PRE%nsr_ids _ids
				on _a.id =_ids.lng_id 
				and _a.project_id = _ids.project_id
				and _ids.item_type = 'taxon'

			where
				_a.project_id =".$project_id."
				and ifnull(_trash.is_deleted,0)=0
				and substr(nsr_id,-1 * length('" .$nsr_id ."'))='" . $nsr_id ."'
		";

		/*
			and (
				nsr_id = '".$nsr_id."' or
				nsr_id = 'concept/".$nsr_id."' or
				nsr_id = 'tn.nlsr.concept/".$nsr_id."'
			)
		*/
					
		return $this->freeQuery( $query );
	
	}


}
