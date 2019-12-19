<?php
 
/*
    
    currently handles only the first file of a possible mutiple-file upload. soit.

*/

class FileUploadHelper
{

    private $_result = false;
    private $_errors = false;
    private $_legalMimeTypes = false;
	private $_currentMimeType = false;
    private $_tempDir = false;
    private $_storageDir = false;
	private $_overwrite = 'rename'; // overwrite / rename / skip
    private $_mime_types = array(          
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

    public function setLegalMimeTypes($types)
    {

        $this->_legalMimeTypes = $types;
    
    }

    public function setTempDir($dir)
    {

        $this->_tempDir = $dir;
    
    }

    public function setOverwrite($state)
    {

		/*
			overwrite (overwrites when exists)
			rename (rename when exists to "file (1)")
			skip (skips when exists)
		*/
		
		if ($state!=='overwrite' && $state!=='rename' && $state!=='skip')
			return;
		
        $this->_overwrite = $state;
    
    }

    public function setStorageDir($dir)
    {

        $this->_storageDir = $dir;
    
    }

    public function getResult()
    {

        return $this->_result;

    }


    public function getErrors()
    {
        
        if ($this->_errors) {

            return $this->_errors;

        } else {
        
            return false;

        }
    
    }

    public function handleTaxonMediaUpload($files)
    {

        $file = $files[0];

        if ($file['tmp_name']=='') {

            $this->addError(_('No name of uploaded file specified.'));

        } elseif ($this->_legalMimeTypes===false) {

            $this->addError(_('No allowed MIME-types set.'));

        } elseif ($this->_storageDir===false) {

            $this->addError(_('No target directory specified.'));

        } else {

            $mt = $this->getMimeType($file['name'],$file['tmp_name']);

            $filesToSave = false;
    
            if ($this->isLegalMimeType($mt) == false) {
    
                $this->addError(_('Media type not allowed:').' '.$mt);
    
            } elseif($this->_currentMimeType['media_type'] == 'archive') {
            // archive with multiple files
    
                if ($this->_tempDir===false) {
            
                    $this->addError(_('No temporary directory specified (required for deflating archive).'));

                } else {


                    if ($mt == 'application/zip') {

						$d = $this->createTemporaryUploadDir();

						if (PHP_OS=='Linux') {

							exec('unzip '.$file['tmp_name'].' -d '.$d);

						} else {

							try {

								$phar = new PharData($file['tmp_name']);
								$phar->extractTo($d); // extract all files
							
							} catch (Exception $e) {

								$this->addError($e->getMessage());

								$this->addError('Failed PharData, attempting ZipArchive-method...');

								// extract all the files
								$zip = new ZipArchive;
			
								if ($zip->open($file['tmp_name'])===true) {
									if ($zip->extractTo($d)) {
										$zip->close();
									} else {
										$this->addError(_('Could not extract files ('.$zip->getStatusString().').'));
										$zip->close();
									}
								} else {
									$this->addError('Cannot open zip-file');
								}

							}

						}


						$iterator = new DirectoryIterator($d);
						
						// iterate through extracted fild and see whether files are allowed
						while($iterator->valid()) {
							$dmtu = $this->doFileUpload($d.$iterator->getFilename(),$iterator->getFilename());
							if ($dmtu) 
								$this->_result[] = $dmtu;
							$iterator->next();
						}
						
						// delete all remaining files in the temp upload dir
						$iterator->rewind();
						
						while($iterator->valid()) {
							if ($iterator->getType()=='file')
								unlink($d.$iterator->getFilename());
							$iterator->next();
						}
						
						// as well as the temp dir itself
						$this->rmDirAndFiles($d);
						

					}

                }
            
            } else {
            // normal file
    
                $dmtu = $this->doFileUpload($file['tmp_name'],$file['name']);
        
                if ($dmtu)  $this->_result[] = $dmtu;

            }
    
        }

    }

    public function getMimeType ($filename,$tmpFileName=false)
    {

        if (function_exists('finfo_open')) {

            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $tmpFileName!==false ? $tmpFileName : $filename);
            finfo_close($finfo);

            $result = $mimetype;

        }
		
		if (!function_exists('finfo_open') || strpos($result,'application/octet-stream')===0) {

	        $ext = strtolower(array_pop(explode('.', $filename)));

			if (array_key_exists($ext, $this->_mime_types)) {

        	    $result = $this->_mime_types[$ext];
			
			} else {

    	        $result = 'application/octet-stream';
	        }

        }

        $result = strtolower($result);

        if (strpos($result,'charset')!==false) {

            $result = trim(substr($result,0,strpos($result,'charset')),' ;');

        }        

        return $result;

    }

    private function createTemporaryUploadDir()
    {

        $d =  $this->_tempDir.substr(md5(uniqid(rand(), true)), 0, 8).'/';
		
        if (mkdir($d)) {
        
            return $d;

        } else {

            return false;

        }

    }

    private function createUniqueNewFileName($extension)
    {

        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            
            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),
            
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,
            
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,
            
            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
            ).'.'.trim($extension,'. ');

    }

    private function ensureUniqueFileName($dir,$filename,$extension)
    {
	
		if (file_exists($dir.$filename.'.'.$extension)) {

            if ($this->_overwrite=='skip') {

                return null;

            }
			if ($this->_overwrite=='overwrite') {

				$d = unlink($dir.$filename.'.'.$extension);
				
				if ($d===false) {
					$this->addError(_('Could not delete existing file '.$filename.'.'.$extension));
					return false;
				}
			}
			if ($this->_overwrite=='rename') {
				// default behaviour below
			} 
		}

		$extraBit = '';
		$i = 1;

		while(file_exists($dir.$filename.$extraBit.'.'.$extension)) {

			$extraBit = sprintf(' (%01d)',$i++);

		}
		
		return $filename.$extraBit.'.'.$extension;

    }

    private function isLegalMimeType($mimetype)
    {

		if ($this->_legalMimeTypes===false) return false;

		if ($this->_legalMimeTypes=='*') return true;

        $type = $result = false;

        foreach((array)$this->_legalMimeTypes as $key => $val) {

            if ($mimetype==$val['mime']) {
    
                $this->_currentMimeType = $val;

                $result = true;;

                break;

            }

        }

        return $result;

    }

	private function cRename($from,$to)
	{
	
		//return rename($from,$to); // generates odd errors on some linux filesystems
	
		if(copy($from,$to)) {

			return unlink($from);

		} else {

			return false;

		}

	}

	private function isMacFodder($f)
	{

		return (
			substr(basename($f),0,1)=='.' ||
			substr(basename($f),0,1)=='_'
		);
	
	
	}

    private function doFileUpload ($oldFileName,$currentFileName)
    {

		// resolve the mime-type
        $t = $this->getMimeType($currentFileName,$oldFileName);


		// filerting out files that start with . or _
		if ($this->isMacFodder($oldFileName)!==true) {

        // assess whether the mime-type is legal
			if ($this->isLegalMimeType($t)!==false) {
	
	
				$fs = filesize($oldFileName);
	
				// assess whether the uploaded file isn't too big                                
				if ($fs <= $this->_currentMimeType['maxSize']) {
					
					// creata a new, unique filename with the original extension
					$pi = pathinfo($currentFileName);
	
					//$fn = $this->createUniqueNewFileName($pi['extension']);
					$fn = $this->ensureUniqueFileName($this->_storageDir,$pi['filename'],$pi['extension']);

					if ($fn===null && $this->_overwrite=='skip') {
						
						return null;
						
					}
					if ($fn!==false) {
	
						// move the file to the project's media directory
						//if (rename($oldFileName,$this->_storageDir.$fn)) {
						if ($this->cRename($oldFileName,$this->_storageDir.$fn)) {
			
							// store data to save in temporary array
							$fileToSave = array(
								'name' => $fn,
								'full_path' => $this->_storageDir.$fn,
								'original_name' => $currentFileName,
								'mime_type' => $t,
								'media_name' => $this->_currentMimeType['media_name'],
								'size' => $fs
							); 
							
							return $fileToSave;
		
						} else {
		
							$this->addError(_('Could not move file:').' '.$currentFileName);
		
						}
						
						} else {

							$this->addError(_('Could not overwrite file:').' '.$currentFileName);

						}
	
				} else {
	
					$this->addError(_('File too big:').' '.
						$currentFileName.' ('.ceil($fs/1000).'kb; '._('max.').' '.ceil($this->_currentMimeType['maxSize']/1000).'kb)');
	
				}
								
	
	
			} else {
			
				if ($t!='directory')
					$this->addError(_('File type not allowed:').' '.$currentFileName.' ('.$t.')');
	
			}
    
        } else {
        
            //$this->addError(_('Skipped (Mac fodder):').' '.$currentFileName);

        }

        return false;

    }

    private function addError ($e)
    {
        
        $this->_errors[] = $e;
    
    }

	private function rmDirAndFiles($dir)
	{
		if (is_dir($dir)) {

			$objects = scandir($dir,0);

			foreach ($objects as $object) {

				if ($object != '.' && $object != '..') {
	
					if (filetype($dir.'/'.$object) == 'dir') $this->rmDirAndFiles($dir.'/'.$object); else unlink($dir.'/'.$object);
	
				}

			}

			reset($objects);

			rmdir($dir);

		}

	} 
	

}


