<?php

class NSRFunctionsController extends Controller
{

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
		$this->defaultNvNLicenseText = $this->getSetting( "photo_NvN_license_text", "geen (alle rechten voorbehouden)" );
    }
	
	public function formatPictureResults($data)
	{
		foreach((array)$data as $key=>$val)
		{
			$metaData=array(
				'' => '<span class="pic-meta-label">'.(!empty($val['common_name']) ? $val['common_name'].' (<i>'.$val['nomen'].'</i>)' : '<i>'.$val['nomen'].'</i>').'</span>',
				$this->translate('Omschrijving') => $val['meta_short_desc'],
				$this->translate('Fotograaf') => $val['photographer'],
				$this->translate('Datum') => $val['meta_datum'],
				$this->translate('Locatie') => $val['meta_geografie'],
				$this->translate('Validator') => $val['meta_validator'],
				$this->translate('Geplaatst op') => $val['meta_datum_plaatsing'],
				$this->translate('Copyright') => $val['meta_copyrights'],
				$this->translate('Contactadres fotograaf') => $val['meta_adres_maker'],
				$this->translate('Licentie') => !empty($val['meta_license']) && $val['meta_license']!='Natuur van Nederland licentie' ? $val['meta_license'] : $this->defaultNvNLicenseText,
			);

			$data[$key]['photographer']=$val['photographer'];
			$data[$key]['label']=
				trim(
					(!empty($val['photographer']) ? $val['photographer'].', ' : '') .
					(!empty($val['meta_datum']) ? $val['meta_datum'].', ' : '') .
					$val['meta_geografie'], ', '
				);
			$data[$key]['meta_data']=$this->helpers->Functions->nuclearImplode('</span>: ','<br /><span class="pic-meta-label">',$metaData,true);
			
		}
		
		return  $data;
	
	}
}
