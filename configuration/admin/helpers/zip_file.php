<?php

class ZipFile
{
	private $_tmpfile;
	private $_zip;
	private $_filename='archive';
	
    public function __construct()
    {
	}

    public function createArchive( $filename=null )
    {
		$this->_tmpfile=tempnam("tmp", "zip");
		$this->_zip = new ZipArchive();
		$this->_zip->open($this->_tmpfile, ZipArchive::OVERWRITE);
		if ( !is_null($filename) ) $this->setFileName($filename);
	}

    public function addFile( $file )
    {
		if ( is_array($file) )
		{
			foreach($file as $f)
				$this->_zip->addFile( $f );
		}
		else
		{
			$this->_zip->addFile( $file );
		}
	}

    public function downloadArchive()
    {
		$this->_zip->close();
		header('Content-Type: application/zip');
		header('Content-Length: ' . filesize($this->_tmpfile));
		header('Content-Disposition: attachment; filename="'.$this->_filename.'.zip"');
		readfile($this->_tmpfile);
		unlink($this->_tmpfile);
	}

    private function setFileName( $filename )
	{
		$this->_filename=$filename; 
		$this->_filename=strtolower(substr($this->_filename,-4))==".zip" ? substr($this->_filename,0,strlen($this->_filename)-4) : $this->_filename;
	}

}