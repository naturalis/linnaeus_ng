<?php

class TranslatorController
{

    public $usedModels = array(
		'interface_texts',
		'interface_translations'
    );

	private $_environment=null;
	private $_languageid=null;
	private $_db_connection=null;
	private $models;
	private $_text=null;
	private $_translation=null;
	private $_didtranslate=false;
	private $_isnewstring=false;
	private $_newStrings=array();

    public function __construct( $env, $languageid, $db_connection )
    {
		$this->_environment=$env;
		$this->_languageid=$languageid;
		$this->loadModels();
    }

    public function __destruct ()
    {
		//$this->saveNewStrings();
    }

    public function translate( $s )
    {
		if ( empty($s) ) return;
		
		if ( is_array($s) )
		{
			$translations=array();
			foreach($s as $key=>$val)
			{
				$this->_text=$val;
				$this->doTranslate();
				$this->rememberNewString();

			$this->saveNewStrings();
			$this->_newStrings=array();

				if ( $this->_didtranslate )
				{
					$translations[$key]=$this->_translation;
				}
				else
				{
					$translations[$key]=$this->_text;
				}				
			}
			return $translations;
		}
		else
		{
			$this->_text=$s;
			$this->doTranslate();
			$this->rememberNewString();
			
		$this->saveNewStrings();
		$this->_newStrings=array();

			if ( $this->_didtranslate )
			{
				return $this->_translation;
			}
			else
			{
				return $this->_text;
			}
		}
    }
	
    private function loadModels ()
    {
        $this->models = new stdClass();

        require_once dirname(__FILE__) . '/../models/Table.php';

        foreach ((array) $this->usedModels as $key)
		{
            $t = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            $this->models->$t = new Table($key);
        }
    }

    private function doTranslate()
    {
		$this->_didtranslate=false;
		$this->_isnewstring=false;
		
		if ( empty($this->_text) )
			return;

		if ( empty($this->_languageid) )
			return;

        // get id of the text
        $i = $this->models->InterfaceTexts->_get(array(
            'id' => array(
                'text' => $this->_text,
                'env' => $this->_environment
            ),
            'columns' => 'id'
        ));

        // if not found, return unchanged
        if (empty($i[0]['id']))
		{
			$this->_isnewstring=true;
			return;
		}
	
		// fetch appropriate translation
		$it = $this->models->InterfaceTranslations->_get(
			array(
				'id' => array(
					'interface_text_id' => $i[0]['id'],
					'language_id' => $this->_languageid
				),
				'columns' => 'id,translation',
				'limit' => 1
			));
			
		// if not found, return unchanged
		if ( empty($it[0]['id']) )
			return;

		$this->_translation=$it[0]['translation'];
		$this->_didtranslate=true;

    }

	private function rememberNewString()
	{
		if (!$this->_didtranslate)
			array_push($this->_newStrings,$this->_text);
	}

    private function saveNewStrings()
    {
		$this->_newStrings=array_unique($this->_newStrings);

		foreach($this->_newStrings as $text)
		{
			$d=$this->models->InterfaceTexts->_get(array('id'=>array('text' => $text,'env' => $this->_environment)));

			if (!$d)
			{
				$this->models->InterfaceTexts->save(array('id' => null,'text' => $text,'env' => $this->_environment));
			}
		}
    }

}