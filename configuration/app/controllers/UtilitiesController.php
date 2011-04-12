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
		
		taxon	[taxon id]	[taxon name]
		commonname	[common name id]	[common name]	[taxon id]	[taxon name]	[language name]
		synonym	[synonym id]	[synonym]	[taxon id]	[taxon name]
		
		*/	
	
		if (!$this->rHasVal('id')) return null;
		
		$pId = $this->requestData['id'];

		$taxa = $this->models->Taxon->_get(
			array(
				'id' => array('project_id' => $pId),
				'columns' => 'id,taxon',
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

		$this->smarty->assign('taxa',$taxa);
		$this->smarty->assign('lang',$languages);
		$this->smarty->assign('common',$common);
		$this->smarty->assign('synonyms',$synonyms);

		$this->printPage();

	}


}






















