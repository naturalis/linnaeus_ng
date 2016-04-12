<?php

class TranslatorController
{
	private $_environment;
	private $_languageid;
	private $_model;
	private $_text;
	private $_translation;
	private $_didtranslate=false;
	private $_isnewstring=false;
	private $_newStrings=array();

    public function __construct( $p )
    {
		$this->_model= isset( $p['model'] ) ? $p['model'] : null ;
		$this->_environment= isset( $p['envirnonment'] ) ? $p['envirnonment'] : null ;
		$this->_languageid= isset( $p['language_id'] ) ? $p['language_id'] : null ;
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
	
    private function doTranslate()
    {
		$this->_didtranslate=false;
		$this->_isnewstring=false;
		
		if ( empty($this->_text) )
			return;

		if ( empty($this->_languageid) )
			return;

        // get id of the text
        $i = $this->_model->freeQuery("
			select
				id
			from
				%PRE%interface_texts
			where
				text = '" .   $this->escapeString( $this->_text ) . "'
				and env = '" .  $this->escapeString( $this->_environment ) . "'
        ");

        // if not found, return unchanged
        if (empty($i[0]['id']))
		{
			$this->_isnewstring=true;
			return;
		}
	
		// fetch appropriate translation
        $it = $this->_model->freeQuery("
			select
				id,translation
			from
				%PRE%interface_translations
			where
				interface_text_id = " . $i[0]['id'] . " 
				and language_id = ". $this->_languageid ."
        ");

		// if not found, return unchanged
		if ( empty($it[0]['id']) )
			return;

		$this->_translation=$it[0]['translation'];
		$this->_didtranslate=true;

    }

	private function rememberNewString()
	{
		if (!$this->_didtranslate) array_push($this->_newStrings,$this->_text);
	}

    private function saveNewStrings()
    {
		$this->_newStrings=array_unique($this->_newStrings);

		foreach($this->_newStrings as $text)
		{
			$d=$this->_model->freeQuery("
				select
					id
				from
					%PRE%interface_texts
				where
					text = '" . $this->escapeString( $text ) . "'
					and env = '" . $this->escapeString( $this->_environment ) . "'
			");

			if (!$d)
			{
				$d=$this->_model->freeQuery("
					insert into %PRE%interface_texts
						(text,env)
					values
						('" . $this->escapeString( $text ) . "','" . $this->escapeString( $this->_environment ) . "')
				");
			}
		}
    }
	
	private function escapeString( $s )
	{
		return mysqli_real_escape_string(  $this->_model->databaseConnection, $s );
	}

}