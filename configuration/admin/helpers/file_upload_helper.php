<?php
/*


array(1) {
  ["file"]=>
  array(5) {
    ["name"]=>
    string(35) "[isoHunt] sopranos season 4.torrent"
    ["type"]=>
    string(24) "application/x-bittorrent"
    ["tmp_name"]=>
    string(26) "C:\Windows\Temp\phpF14.tmp"
    ["error"]=>
    int(0)
    ["size"]=>
    int(96320)
  }

*/


	class FileUploadHelper {
	
		private $files;
		private $errors;
		
		private function addError($e) {

			$this->errors[] = $e;

		}
	
		public function saveFiles($files, $target_dir, $filemask, $maxSize) {

			if (substr($target_dir,strlen($target_dir)-1)!='/') $target_dir .= '/';
		
			$this->files = $files;

			foreach((array)$this->files as $key => $val) {

				if ($val['type']=='') {

					$val['type'] = mime_content_type($val['tmp_name']);

				}

				if (!in_array($val['type'],$filemask)) {
				
					$this->addError(_('Uploaded file of unallowed type: ').$val['name'].' ('.$val['type'].')');
	
				} else
				if ($val['size'] > $maxSize) {
				
					$this->addError(_('Uploaded file too large.'));
	
				} else {

					$new_name = $target_dir . $val['name'];
					
					$p = pathinfo($val['name']);
				
					$i = 0;
	
					while(file_exists($new_name)) {
					
						$new_name = $target_dir . $p['filename'].' ('.$i++.')'.'.'.$p['extension'];
						
						if ($i > 999999) {

							$this->addError(_('Unknown upload error.'));
							
							exit;

						}
	
					}
	
					if (!move_uploaded_file($val['tmp_name'],$new_name)) {
	
						$this->addError(_('Unable to move uploaded file.'));

						$this->addError($val['tmp_name'].' -> '.$new_name);
	
					} else {

						$result[] = 
							array(
								'name' => $new_name,
								'type' => $val['type'],
								'size' => $val['size'],
								'orig_name' => $val['name']
							);

					}

				}

			}
			
			return array('result' => $result, 'error' => $this->errors);

		}
			
	}


?>