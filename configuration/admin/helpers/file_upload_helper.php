<?php

class FileUploadHelper
{
    
    private $files;
    private $errors;



    private function addError ($e)
    {
        
        $this->errors[] = $e;
    
    }



    private function getMimeType ($filename)
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



    /**
     * Handles file uploads
     *
     * @param  	array	$files	$_FILES variable
     * @param  	string	$target_dir	dir to save to
     * @param  	array	$filemask	array of allowed mime-types
     * @param  	integer	$maxSize	max allowed uplaod size
     * @return 	array	array with new name and locateion for each uploaded file
     * @todo	function uses mime_content_type, which is deprecated, but the alternative finfo_file is >= php 5.3
     */
    public function saveFiles ($files, $target_dir, $filemask, $maxSize)
    {
        
        if (substr($target_dir, strlen($target_dir) - 1) != '/')
            $target_dir .= '/';
        
        $this->files = $files;
        
        foreach ((array) $this->files as $key => $val) {
            
            $val['type'] = mime_content_type($val['tmp_name']);
            
            if ($val['type'] == '') {
                
                $val['type'] = $this->getMimeType($val['name']);
            
            }
            
            if (!in_array($val['type'], $filemask)) {
                
                $this->addError(_('Uploaded file of unallowed type: ') . $val['name'] . ' (' . ($val['type'] ? $val['type'] : 'unknown type') . ')');
            
            }
            else if ($val['size'] > $maxSize) {
                
                $this->addError(_('Uploaded file too large.'));
            
            }
            else {
                
                $new_file_name = $val['name'];
                
                $new_file_path = $target_dir . $new_file_name;
                
                $p = pathinfo($val['name']);
                
                $i = 0;
                
                while (file_exists($new_file_path)) {
                    
                    $new_file_name = $p['filename'] . ' (' . $i++ . ')' . '.' . $p['extension'];
                    
                    $new_file_path = $target_dir . $new_file_name;
                    
                    if ($i > 999999) {
                        
                        $this->addError(_('Unknown upload error.'));
                        
                        exit();
                    
                    }
                
                }
                
                if (!move_uploaded_file($val['tmp_name'], $new_file_path)) {
                    
                    $this->addError(_('Unable to move uploaded file.'));
                    
                    $this->addError($val['tmp_name'] . ' -> ' . $new_file_path);
                
                }
                else {
                    
                    $result[] = array(
                        'name' => $new_file_name, 
                        'path' => $new_file_path, 
                        'extension' => $p['extension'], 
                        'type' => $val['type'], 
                        'size' => $val['size'], 
                        'orig_name' => $val['name']
                    );
                
                }
            
            }
        
        }
        
        return array(
            'result' => isset($result) ? $result : null, 
            'error' => $this->errors
        );
    
    }

}


