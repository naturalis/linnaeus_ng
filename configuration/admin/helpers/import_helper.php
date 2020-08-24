<?php

class ImportHelper
{


    public function setLegalMimeTypes($types)
    {

        $this->_legalMimeTypes = $types;

    }

    public function setTempDir($dir)
    {

        $this->_tempDir = $dir;

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

        if ($file['tmp_name'] == '') {

            $this->addError(_('No name of uploaded file specified.'));

        } elseif ($this->_legalMimeTypes === false) {

            $this->addError(_('No allowed MIME-types set.'));

        } elseif ($this->_storageDir === false) {

            $this->addError(_('No target directory specified.'));

        } else {

            $mt = $this->getMimeType($file['name'], $file['tmp_name']);

            $filesToSave = false;

            if ($this->isLegalMimeType($mt) == false) {

                $this->addError(_('Media type not allowed:') . ' ' . $mt);

            } elseif ($this->_currentMimeType['media_type'] == 'archive') {
                // archive with multiple files

                if ($this->_tempDir === false) {

                    $this->addError(_('No temporary directory specified (required for deflating archive).'));

                } else {

                    if ($mt = 'application/zip') {
                        // zip file

                        // create temp upload dir
                        $d = $this->createTemporaryUploadDir();

                        if ($d) {

                            // extract all the files
                            $zip = new ZipArchive;

                            if ($zip->open($file['tmp_name']) === true) {

                                $zip->extractTo($d);

                                $zip->close();

                                $iterator = new DirectoryIterator($d);

                                // iterate through extracted fild and see whether files are allowed
                                while ($iterator->valid()) {

                                    $dmtu = $this->doFileUpload($d . $iterator->getFilename(), $iterator->getFilename());

                                    if ($dmtu) $this->_result[] = $dmtu;

                                    $iterator->next();

                                }

                                // delete all remaining files in the temp upload dir
                                $iterator->rewind();

                                while ($iterator->valid()) {

                                    if ($iterator->getType() == 'file')
                                        unlink($d . $iterator->getFilename());

                                    $iterator->next();

                                }

                                // as well as the temp dir itself
                                $this->rmDirAndFiles($d);

                            } else {

                                $this->addError(_('Could not extract files from archive.'));

                            }

                        } else {

                            $this->addError(_('Could not create temporary directory in ' . $this->_tempDir));

                        }

                    }

                }

            } else {
                // normal file

                $dmtu = $this->doFileUpload($file['tmp_name'], $file['name']);

                if ($dmtu) $this->_result[] = $dmtu;

            }

        }

    }

    public function getMimeType($filename, $tmpFileName = false)
    {

        if (function_exists('finfo_open')) {

            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $tmpFileName !== false ? $tmpFileName : $filename);
            finfo_close($finfo);

            $result = $mimetype;

        } else {

            $fileParts = explode('.', $filename);
            $ext = strtolower(array_pop($fileParts));

            if (array_key_exists($ext, $this->_mime_types)) {

                $result = $this->_mime_types[$ext];

            } else {

                $result = 'application/octet-stream';
            }

        }

        $result = strtolower($result);

        if (strpos($result, 'charset') !== false) {

            $result = trim(substr($result, 0, strpos($result, 'charset')), ' ;');

        }

        return $result;

    }

    private function createTemporaryUploadDir()
    {

        $d = $this->_tempDir . substr(md5(uniqid(rand(), true)), 0, 8) . '/';

        if (mkdir($d)) {

            return $d;

        } else {

            return false;

        }

    }

    private function createUniqueNewFileName($extension)
    {

        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),

                // 16 bits for "time_mid"
                mt_rand(0, 0xffff),

                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4
                mt_rand(0, 0x0fff) | 0x4000,

                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand(0, 0x3fff) | 0x8000,

                // 48 bits for "node"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            ) . '.' . trim($extension, '. ');

    }

    private function createUniqueFileName($dir, $filename, $extension)
    {

        $extraBit = '';
        $i = 1;

        while (file_exists($dir . $filename . $extraBit . '.' . $extension)) {

            $extraBit = sprintf(' (%01d)', $i++);

        }

        return $filename . $extraBit . '.' . $extension;

    }

    private function isLegalMimeType($mimetype)
    {

        if ($this->_legalMimeTypes === false) return false;

        if ($this->_legalMimeTypes == '*') return true;

        $type = $result = false;

        foreach ((array)$this->_legalMimeTypes as $key => $val) {

            if ($mimetype == $val['mime']) {

                $this->_currentMimeType = $val;

                $result = true;;

                break;

            }

        }

        return $result;

    }

    private function cRename($from, $to)
    {

        //return rename($from,$to); // generates odd errors on some linux filesystems

        if (copy($from, $to)) {

            return unlink($from);

        } else {

            return false;

        }

    }

    private function isMacFodder($f)
    {

        return (
            substr(basename($f), 0, 1) == '.' ||
            substr(basename($f), 0, 1) == '_'
        );


    }

    private function doFileUpload($oldFileName, $currentFileName)
    {

        // resolve the mime-type
        $t = $this->getMimeType($currentFileName, $oldFileName);


        // filerting out files that start with . or _
        if ($this->isMacFodder($oldFileName) !== true) {

            // assess whether the mime-type is legal
            if ($this->isLegalMimeType($t) !== false) {


                $fs = filesize($oldFileName);

                // assess whether the uploaded file isn't too big
                if ($fs <= $this->_currentMimeType['maxSize']) {

                    // creata a new, unique filename with the original extension
                    $pi = pathinfo($currentFileName);

                    //$fn = $this->createUniqueNewFileName($pi['extension']);
                    $fn = $this->createUniqueFileName($this->_storageDir, $pi['filename'], $pi['extension']);

                    // move the file to the project's media directory
                    //if (rename($oldFileName,$this->_storageDir.$fn)) {
                    if ($this->cRename($oldFileName, $this->_storageDir . $fn)) {

                        // store data to save in temporary array
                        $fileToSave = array(
                            'name' => $fn,
                            'full_path' => $this->_storageDir . $fn,
                            'original_name' => $currentFileName,
                            'mime_type' => $t,
                            'media_name' => $this->_currentMimeType['media_name'],
                            'size' => $fs
                        );

                        return $fileToSave;

                    } else {

                        $this->addError(_('Could not move file:') . ' ' . $currentFileName);

                    }

                } else {

                    $this->addError(_('File too big:') . ' ' .
                        $currentFileName . ' (' . ceil($fs / 1000) . 'kb; ' . _('max.') . ' ' . ceil($this->_currentMimeType['maxSize'] / 1000) . 'kb)');

                }


            } else {

                if ($t != 'directory')
                    $this->addError(_('File type not allowed:') . ' ' . $currentFileName . ' (' . $t . ')');

            }

        } else {

            //$this->addError(_('Skipped (Mac fodder):').' '.$currentFileName);

        }

        return false;

    }

    private function addError($e)
    {

        $this->_errors[] = $e;

    }

    private function rmDirAndFiles($dir)
    {
        if (is_dir($dir)) {

            $objects = scandir($dir, 0);

            foreach ($objects as $object) {

                if ($object != '.' && $object != '..') {

                    if (filetype($dir . '/' . $object) == 'dir') $this->rmDirAndFiles($dir . '/' . $object); else unlink($dir . '/' . $object);

                }

            }

            reset($objects);

            rmdir($dir);

        }

    }


}


