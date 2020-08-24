<?php /** @noinspection PhpMissingParentCallMagicInspection */

include_once ('Controller.php');
include_once ('ModuleSettingsController.php');

class NSRFunctionsController extends Controller
{

	public $settings;

    public function __construct ()
    {
        parent::__construct();
		$this->initialise();
    }

    public function __destruct()
    {
        parent::__destruct();
    }
	
    private function initialise()
    {
		$this->moduleSettings=new ModuleSettingsController;
		$this->moduleSettings->setUseDefaultWhenNoValue( true );
		$this->moduleSettings->assignGeneralSettings( $this->settings );
    }
	
	public function formatPictureResults($data)
	{
		foreach((array)$data as $key=>$val)
		{
			$metaData=array(
				'' => '<span class="pic-meta-label title">'.(!empty($val['common_name']) ? $val['common_name'].' (<i>'.$val['nomen'].'</i>)' : '<i>'.$val['nomen'].'</i>').'</span>',
				$this->translate('Omschrijving') => $val['meta_short_desc'],
				$this->translate('Fotograaf') => $val['photographer'],
				$this->translate('Datum') => $val['meta_datum'],
				$this->translate('Locatie') => $val['meta_geografie'],
				$this->translate('Validator') => $val['meta_validator'],
				$this->translate('Geplaatst op') => $val['meta_datum_plaatsing'],
				$this->translate('Copyright') => $val['meta_copyrights'],
				$this->translate('Contactadres fotograaf') => $val['meta_adres_maker'],
			);
			
			$license = (!empty($val['meta_license']) && $val['meta_license']!='Natuur van Nederland licentie' ? $val['meta_license'] : $this->settings->picture_license_default);
			
			if ( !empty($license) )
			{
				$metaData[$this->translate('Licentie')] =
					$license . 
					(!empty($this->settings->url_to_picture_license_info) ?
						'&nbsp;<a class="help" title="' . $this->translate('klik voor help over dit onderdeel') .'" target="_blank" href="'. $this->settings->url_to_picture_license_info .'">&nbsp;</a>' : '');
			}
					

			$data[$key]['photographer']=$val['photographer'];
			$data[$key]['label']=
				trim(
					(!empty($val['photographer']) ? $val['photographer'].', ' : '') .
					(!empty($val['meta_datum']) ? $val['meta_datum'].', ' : '') .
					$val['meta_geografie'], ', '
				);
			$data[$key]['meta_data']=$this->helpers->Functions->nuclearImplode('</span>: <span class="value">','</span><br /><span class="pic-meta-label">',$metaData,true);
		}
		
		return  $data;
	}

}