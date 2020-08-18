<?php
include_once ('Controller.php');

class ImportController extends Controller
{
    public $usedModels = array(
        'characteristics',
        'characteristics_chargroups',
        'characteristics_labels',
        'characteristics_labels_states',
        'characteristics_matrices',
        'characteristics_states',
        'chargroups',
        'chargroups_labels',
        'choices_content_keysteps',
        'choices_keysteps',
        'commonnames',
        'content',
        'content_free_modules',
        'content_introduction',
        'content_keysteps',
        'content_taxa',
        'free_modules_pages',
        'free_modules_projects',
        'geodata_types',
        'geodata_types_titles',
        'glossary',
        'glossary_media',
        'glossary_media_captions',
        'glossary_synonyms',
        'introduction_media',
        'introduction_pages',
        'keysteps',
        'l2_maps',
        'l2_occurrences_taxa',
        'literature',
        'literature_taxa',
        'matrices',
        'matrices_names',
        'matrices_taxa',
        'matrices_taxa_states',
        'media_descriptions_taxon',
        'media_taxon',
		'nbc_extras',
        'occurrences_taxa',
        'pages_taxa',
        'pages_taxa_titles',
        'synonyms',
        'users_taxa'

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
		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
        $this->checkAuthorisation();
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
        $this->setPageName($this->translate('Data import options'));
        $this->printPage();
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

        $fileParts = explode('.', $filename);

        $ext = strtolower(array_pop($fileParts));
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

		$this->models->Matrices->save(array(
			'id' => null,
			'project_id' => $this->getCurrentProjectId()
		));

		$mId = $this->models->Matrices->getNewId();

		$this->models->MatricesNames->save(
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

		$this->models->Characteristics->save(
		array(
			'id' => null,
			'project_id' => $this->getCurrentProjectId(),
			'type' => $type,
			'got_labels' => 1
		));

		$id=$this->models->Characteristics->getNewId();

		$this->models->CharacteristicsLabels->save(
		array(
			'id' => null,
			'project_id' => $this->getCurrentProjectId(),
			'characteristic_id' => $id,
			'language_id' => $this->getDefaultProjectLanguage(),
			'label' => $label
		));

		$this->models->CharacteristicsMatrices->save(
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
