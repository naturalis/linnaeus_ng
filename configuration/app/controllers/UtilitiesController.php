<?php

/*

http://linnaeus/app/views/utilities/get_names.php?id=64

inc/class.html.php
			2 => array('url' => 'lin2/tanbif_linnaeus.php?menuentry=zoeken', 'textID' => 1171, 'imgLabel' => 'browse', 'textWidth' => 49),
			3 => array('url' => 'lin2/tanbif_linnaeus.php?menuentry=sleutel', 'textID' => 994, 'imgLabel' => 'identify', 'textWidth' => 51),


FOOTER


CHANGE URLS IN RES.LINNAEUSNG.PHP & database

INSERT INTO `_pages` ( `id` , `name` , `text_id` , `show_order` )VALUES (NULL , 'Linnaeus NG', '500', '10');
id = 10

insert into _page_sections (page_id,name,text_id,show_order) values(10,'Scientific name',501,0);
insert into _page_sections (page_id,name,text_id,show_order) values(10,'Common names',502,1);
insert into _page_sections (page_id,name,text_id,show_order) values(10,'Description',503,2);
insert into _page_sections (page_id,name,text_id,show_order) values(10,'Images',504,3);
insert into _page_sections (page_id,name,text_id,show_order) values(10,'Literary references',505,4);
insert into _page_sections (page_id,name,text_id,show_order) values(10,'Dichotomous key',506,5);
insert into _page_sections (page_id,name,text_id,show_order) values(10,'Matrix key',507,6);


INSERT INTO `_sources` ( `id` , `name` , `url` , `execnumb` , `execavg` , `execfail` , `language_exclusive` , `active` )
VALUES ('20', 'Linnaeus NG', 'http://145.18.162.103/TanBIF/inc2/res.linnaeusng.php?language=[%lang%]&amp;search=[%search%]', '0', NULL , '0', NULL , '1');

INSERT INTO `_page_sources` ( `page_id` , `source_id` ) VALUES ('10', '20');


check LNG-links in
	LNG header
	TanBIF header
	importNamesAction()




*/

include_once ('Controller.php');

class UtilitiesController extends Controller
{
    
    public $usedModels = array(
		'commonname',
		'synonym'
    );
    
    public $controllerPublicName = 'Utilities';



    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        
        parent::__construct();
    
    }



    /**
     * Destroys!
     *
     * @access     public
     */
    public function __destruct ()
    {
        
        parent::__destruct();
    
    }


    /**
     * AJAX interface for this class
     *
     * @access    public
     */
    public function ajaxInterfaceAction ()
    {
        
        if (!isset($this->requestData['action'])) return;
        
		if ($this->requestData['action'] == 'translate') {
            
			$this->smarty->assign('returnText',$this->javascriptTranslate($this->requestData['text']));

        }
        
        $this->printPage();
    
    }

	public function getNamesAction()
	{
	
		/*
		
		taxon	[taxon id]	[taxon name]	[taxon rank]
		commonname	[common name id]	[common name]	[language name]	[taxon id]	[taxon name]	[taxon rank]
		synonym	[synonym id]	[synonym]	[remark]	[taxon id]	[taxon name]	[taxon rank]
		
		*/	
	
		if (!$this->rHasVal('id')) return null;
		
		$pId = $this->requestData['id'];
		
		$ranks = $this->models->ProjectRank->_get(
			array(
				'id' => array('project_id' => $pId),
				'columns' => 'id,rank_id',
				'fieldAsIndex' => 'id'
			)
		);

		foreach((array)$ranks as $key => $val) {

			$r = $this->models->Rank->_get(
				array(
					'id' => array('id' => $val['rank_id']),
					'columns' => 'rank'
				)
			);
			
			$ranks[$key]['rank'] = $r[0]['rank'];

		}

		$taxa = $this->models->Taxon->_get(
			array(
				'id' => array('project_id' => $pId),
				'columns' => 'id,taxon,rank_id',
				'fieldAsIndex' => 'id'
			)
		);

		$languages = $this->models->Language->_get(
			array(
				'id' => '*',
				'columns' => 'language,id',
				'fieldAsIndex' => 'id'
			)
		);


		$common = $this->models->Commonname->_get(
			array(
				'id' => array('project_id' => $pId),
				'columns' => 'id,commonname,language_id,taxon_id'
			)
		);

		$synonyms = $this->models->Synonym->_get(
			array(
				'id' => array('project_id' => $pId),
				'columns' => 'id,synonym,remark,taxon_id'
			)
		);

		$this->smarty->assign('ranks',$ranks);
		$this->smarty->assign('taxa',$taxa);
		$this->smarty->assign('lang',$languages);
		$this->smarty->assign('common',$common);
		$this->smarty->assign('synonyms',$synonyms);

		$this->printPage();

	}

	public function importNamesAction()
	{

		$pId = 2; //64
		$dataLink = 'http://linnaeus/app/views/utilities/get_names.php?id='.$pId;
		$sourceName = 'Linaneus NG';
		$obsLink = 'http://linnaeus/app/views/species/taxon.php?id=[id]';
	
		$fp = @fopen($dataLink,'r');

		if ($fp) {

			while (($d = fgetcsv($fp,8096,chr(9))) !== false) $b[] = $d;

			fclose($fp);
		}

		if ($b) {

/*


	"select id from sources where source_name = '".$sourceName."'"

	$sourceId = $id;

	if (!$sourceId) {

		"insert into sources
			(source_name,observation_link,show_in_geo_search,nlbif_source)
		values
			('".$sourceName."','".$obsLink."',0,0)"

		$sourceId = mysql_insert_id();

	} else {
	
		"select search_index_id from search_index_sources where source_id = ".$sourceId;
		get->$existing (array)

		foreach((array)$existing as $key => $val) "delete from search_index where id = ".$val;

		"delete from search_index_sources where source_id = ".$sourceId;

	}

*/			

			foreach((array)$b as $val) {

				if (count((array)$val)>=3 && $val[0]=='accepted') {
/*

EERST NAAM SELECTEREN OF NIET AL BESTAAT!


					"insert into search_index
						(name,name_displayed,name_record,name_type,category)
					values
						('".$val[2].'","<i>'.$val[2].'</i>","'.$val[2].'",'accepted',1)"
	
					$id = mysql_insert_id();
	
					"insert into search_index (search_index_id,source_id) values (".$id.",".$sourceId.")"
	
					"insert into search_index_descriptions
						(search_index_id ,language_id,description)
					values
						(".$id ." ,'EN','".$val[3]."')"

*/
				} else
				if (count((array)$val)>=3 && $val[0]=='synonym') {
/*

					"insert into search_index
						(name,name_displayed,name_record,name_type,category)
					values
						('".$val[2].'","<i>'.$val[2].'</i>'.(!empty($val[3]) ? ' ('.$val[3].')' : '').'","'.$val[5].'",'synonym',1)"
	
					$id = mysql_insert_id();
	
					"insert into search_index (search_index_id,source_id) values (".$id.",".$sourceId.")"
	
					"insert into search_index_descriptions
						(search_index_id ,language_id,description)
					values
						(".$id ." ,'EN','synonym for ".$val[6]." <i>\"".$val[5]."\"</i>')"

*/

				} else
				if (count((array)$val)>=6 && $val[0]=='common') {

/*

					"insert into search_index
						(name,name_displayed,name_record,name_type,category)
					values
						('".$val[2].'","<i>'.$val[2].'</i>","'.$val[5].'",'common',1)"
	
					$id = mysql_insert_id();
	
					"insert into search_index (search_index_id,source_id) values (".$id.",".$sourceId.")"
	
					"insert into search_index_descriptions
						(search_index_id ,language_id,description)
					values
						(".$id ." ,'EN','Common name ('.$val[3].') for ".$val[6]." <i>\"".$val[5]."\"</i>')"

*/
				}

			}
			
		}

	}

}






















