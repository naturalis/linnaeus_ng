<?php
/**
 * Original controller for NSR is NsrTaxonImagesController.php, which is now
 * called through images_nsr.php/.tpl
 *
 * This controller is used to upload new images or attach existing images to a certain Taxon.
 *
 */

include_once ('NsrController.php');
include_once ('ModuleSettingsReaderController.php');
include_once ('MediaController.php');

class NsrTaxonMediaController extends NsrController
{
	private $_lookupListMaxResults=99999;

	public $controllerPublicName = 'Taxon editor';

    public $usedModels = array(
		'name_types',
		'nsr_ids',
		'media_taxon',
		'media_meta',
		'trash_can',
    );
    public $usedHelpers = array();
    public $cssToLoad = array(
        'lookup.css',
		'nsr_taxon_beheer.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'lookup.js',
			'nsr_taxon_beheer.js'
        )
    );

    private $_mc;
    private $taxonId;
    private $languageId;
    private $taxon;

    /*
    public $modelNameOverride = 'NsrTaxonMediaModel';
    public $controllerPublicName = 'Taxon editor';
    public $includeLocalMenu = false;
	private $_nameTypeIds;
	private $conceptId=null;
	private $_resPicsPerPage=100;
	private $sys_label_NSR_ID='NSR ID';
	private $sys_label_file_name='file name';


    private
		$_mime_types = array(
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
		);
*/

    /**
     * NsrTaxonMediaController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->initialize();
	}

    /**
     * NsrTaxonMediaController destructor.
     */
    public function __destruct()
    {
        parent::__destruct();
    }

   public function mediaAction()
    {
        $this->checkAuthorisation();

        if (!$this->rHasId()) {
			$this->redirect('index.php');
		}

		$this->taxon = $this->getTaxonById($this->rGetId());
		$this->setPageName(sprintf($this->translate('Media for "%s"'), $this->taxon['taxon']), $this->translate('Media'));

		if ($this->rHasVal('action','delete')) {
			$r = $this->detachMedia();
			$this->addMessage($r);
		}

		if ($this->rHasVal('action','save')) {
			$this->setOverviewImage();
			$this->saveCaptions();
			$this->addMessage('Saved');
		}

		if ($this->rHasVal('action','up') || $this->rHasVal('action','down')) {
		    if ($this->moveImageInOrder()) {
				$this->addMessage('New media order saved');
			}
		}

		$this->smarty->assign('media', $this->_mc->getItemMediaFiles());
		$this->smarty->assign('taxon', $this->taxon);
		$this->smarty->assign('languages', $this->getProjectLanguages());
		$this->smarty->assign('defaultLanguage', $this->getDefaultProjectLanguage());
		$this->smarty->assign('language_id', $this->languageId);
		$this->smarty->assign('module_id', $this->getCurrentModuleId());
		$this->smarty->assign('item_id', $this->taxon['id']);

        $this->printPage();
    }

    /**
     * Initialize the controller
     */
    private function initialize()
    {
		$this->moduleSettings = new ModuleSettingsReaderController;

        $this->taxonId = $this->rHasId() ? $this->rGetId() :
            ($this->rHasVal('taxon_id') ? $this->rGetVal('taxon_id') : false);

        $this->languageId = $this->rHasVar('language_id') ?
            $this->rGetVal('language_id') : $this->getDefaultProjectLanguage();

        $this->setMediaController();
	}

    /**
     * Set the media controller
     */
    private function setMediaController()
	{
        $this->_mc = new MediaController();
        $this->_mc->setModuleId($this->getCurrentModuleId());
        $this->_mc->setItemId($this->taxonId);
        $this->_mc->setLanguageId($this->languageId);
	}

    /**
     * Change the order of images
     *
     * @return bool|null|void
     */
    private function moveImageInOrder()
    {
		$mediaId = $this->rHasVal('subject') ? $this->rGetVal('subject') : false;
		$direction = $this->rHasVal('action') ? $this->rGetVal('action') : false;

		if (!$mediaId || !$direction || ($direction!='up' && $direction!='down')) {
			return;
		}

		$media =  $this->_mc->getItemMediaFiles();

		foreach ($media as $key => $val) {

		    $this->_mc->setSortOrder(array(
                'media_id' => $val['id'],
                'order' => $key
		    ));

		}

		$r = null;

		foreach ($media as $key => $val) {

		    if ($val['id'] == $mediaId) {

				if ($key == 0 && $direction == 'up') continue;
				if ($key == (count($media)-1) && $direction == 'down') continue;

    		    $this->_mc->setSortOrder(array(
                    'media_id' => $val['id'],
                    'order' => ($key+($direction=='up'?-1:1))
    		    ));

    		    $this->_mc->setSortOrder(array(
                    'media_id' => $media[$key+($direction=='up'?-1:1)]['id'],
                    'order' => ($key+($direction=='up'?1:-1))
    		    ));

				$r = true;

			}
		}

		return $r;

    }

    /**
     * Detach the media from a Taxon
     */
    private function detachMedia ()
    {
    	$mediaId = $this->rHasVal('subject') ? $this->rGetVal('subject') : false;

		if (!$mediaId) {
			return;
		}

		return $this->deleteItemMedia($mediaId);
    }

    /**
     * Set the captions to a connected media
     */
    private function saveCaptions ()
    {
		$captions = $this->rHasVal('captions') ? $this->rGetVal('captions') : array();

		foreach((array)$captions as $mediaId => $caption) {

		    $this->_mc->saveCaption(array(
		        'media_id' => $mediaId,
                'caption' => $caption
		    ));

		}
    }

    /**
     * Set the overview image of a certain taxon
     */
    private function setOverviewImage ()
    {
        $mediaId = $this->rHasVal('overview-image') ?
            $this->rGetVal('overview-image') : -1;

        $this->_mc->setOverviewImage($mediaId);

    }

    /**
     * Delete the connection to a media item
     *
     * @param $mediaId
     * @return string
     */
    private function deleteItemMedia ($mediaId)
    {
        $r = $this->_mc->deleteItemMedia($mediaId);

        if ($r) {
            return $this->translate('Detached file');
        }

        return $this->translate('Could not detach file');
    }


}

