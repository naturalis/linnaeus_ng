<?php

include_once ('Controller.php');
class ImportController extends Controller
{
    public $usedModels = array(
        'content_taxon', 
        'page_taxon', 
        'page_taxon_title', 
        'commonname', 
        'synonym', 
        'media_taxon', 
        'media_descriptions_taxon', 
        'content', 
        'literature', 
        'literature_taxon', 
        'keystep', 
        'content_keystep', 
        'choice_keystep', 
        'choice_content_keystep', 
        'matrix', 
        'matrix_name', 
        'matrix_taxon', 
        'matrix_taxon_state', 
        'characteristic', 
        'characteristic_matrix', 
        'characteristic_label', 
        'characteristic_state', 
        'characteristic_label_state', 
        'glossary', 
        'glossary_synonym', 
        'glossary_media', 
        'glossary_media_captions', 
        'free_module_project', 
        'free_module_project_user', 
        'free_module_page', 
        'content_free_module', 
        'occurrence_taxon', 
        'geodata_type', 
        'geodata_type_title', 
        'content_introduction', 
        'introduction_page', 
        'introduction_media', 
        'user_taxon', 
        'l2_occurrence_taxon', 
        'l2_map',
        'chargroup_label', 
        'chargroup', 
        'characteristic_chargroup',
		'nbc_extras'		
    );
    public $usedHelpers = array(
        'file_upload_helper', 
        'xml_parser'
    );
    public $cssToLoad = array(
        'import.css'
    );
    public $jsToLoad = array();

    public function __construct ()
    {
        parent::__construct();
        
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    /**
     * Index
     *
     * @access    public
     */
    public function indexAction ()
    {
        $this->isAuthorisedForImport();
        
        $this->setPageName($this->translate('Data import options'));
        
        $this->printPage();
    }
	
	public function isAuthorisedForImport()
	{

		$d = $this->models->ProjectRoleUser->_get(
		array(
			'id' => array(
				'user_id' => $this->getCurrentUserId(),
				'role_id' => ID_ROLE_LEAD_EXPERT
			),
			'columns' => 'count(*) as total'
		));
		
		return $d[0]['total']>0;
				
	}


    protected function mimeContentType ($filename)
    {
        $mime_types = array(
            
            'txt' => 'text/plain', 
            'htm' => 'text/html', 
            'html' => 'text/html', 
            'php' => 'text/html', 
            'css' => 'text/css', 
            'js' => 'application/javascript', 
            'json' => 'application/json', 
            'xml' => 'application/xml', 
            'swf' => 'application/x-shockwave-flash', 
            'flv' => 'video/x-flv', 
            
            // images
            'png' => 'image/png', 
            'jpe' => 'image/jpeg', 
            'jpeg' => 'image/jpeg', 
            'jpg' => 'image/jpeg', 
            'gif' => 'image/gif', 
            'bmp' => 'image/bmp', 
            'ico' => 'image/vnd.microsoft.icon', 
            'tiff' => 'image/tiff', 
            'tif' => 'image/tiff', 
            'svg' => 'image/svg+xml', 
            'svgz' => 'image/svg+xml', 
            
            // archives
            'zip' => 'application/zip', 
            'rar' => 'application/x-rar-compressed', 
            'exe' => 'application/x-msdownload', 
            'msi' => 'application/x-msdownload', 
            'cab' => 'application/vnd.ms-cab-compressed', 
            
            // audio/video
            'mp3' => 'audio/mpeg', 
            'qt' => 'video/quicktime', 
            'mov' => 'video/quicktime', 
            
            // adobe
            'pdf' => 'application/pdf', 
            'psd' => 'image/vnd.adobe.photoshop', 
            'ai' => 'application/postscript', 
            'eps' => 'application/postscript', 
            'ps' => 'application/postscript', 
            
            // ms office
            'doc' => 'application/msword', 
            'rtf' => 'application/rtf', 
            'xls' => 'application/vnd.ms-excel', 
            'ppt' => 'application/vnd.ms-powerpoint', 
            
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text', 
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet'
        );
        
        $ext = strtolower(array_pop(explode('.', $filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }

	protected function createMatrix($p)
	{
		
		$matrixname=isset($p['matrixname']) ? $p['matrixname'] : null;
		
		if (empty($matrixname))
			return null;

		$this->models->Matrix->save(array(
			'id' => null, 
			'project_id' => $this->getCurrentProjectId(), 
			'got_names' => 1
		));
	
		$mId = $this->models->Matrix->getNewId();
	
		$this->models->MatrixName->save(
		array(
			'id' => null, 
			'project_id' => $this->getCurrentProjectId(), 
			'matrix_id' => $mId, 
			'language_id' => $this->getDefaultProjectLanguage(), 
			'name' => $matrixname 
		));
		
		return $mId;
		
	}
	
	protected function createMatrixCharacter($p)
	{

		$type=isset($p['type']) ? $p['type'] : null;
		$label=isset($p['label']) ? $p['label'] : null;
		$matrixId=isset($p['matrix_id']) ? $p['matrix_id'] : null;
		$showOrder=isset($p['showOrder']) ? $p['showOrder'] : 0;
		
		if (empty($type) || empty($label) || empty($matrixId))
			return null;

		$this->models->Characteristic->save(
		array(
			'id' => null, 
			'project_id' => $this->getCurrentProjectId(), 
			'type' => $type, 
			'got_labels' => 1
		));
	
		$id=$this->models->Characteristic->getNewId();

		$this->models->CharacteristicLabel->save(
		array(
			'id' => null, 
			'project_id' => $this->getCurrentProjectId(), 
			'characteristic_id' => $id, 
			'language_id' => $this->getDefaultProjectLanguage(), 
			'label' => $label
		));
					
		$this->models->CharacteristicMatrix->save(
		array(
			'id' => null, 
			'project_id' => $this->getCurrentProjectId(), 
			'matrix_id' => $matrixId, 
			'characteristic_id' => $id, 
			'show_order' => $showOrder
		));
		
		return $id;
		
	}





}
