<?php

	class linnaeusNG {

		const siteTitle = 'Linnaeus NG';
		const defOutputMode = 'array';
		const defMaxResults = 5;
		const maxInitialSynonyms = 3;
		const maxInitialCommonNames = 15;
		var $usingCached = false;
		var $outputModes = array('json','array');

//		const searchURL = 'http://linnaeus/app/views/linnaeus/r_search.php?search=%s&p=64&l=26'; //26 = def english
		const searchURL = 'http://dev.eti.uva.nl/linnaeus_ng/app/views/linnaeus/r_search.php?search=%s&p=64&l=26'; //26 = def english

		function linnaeusNG($searchString,$maxResults=false,$languageID=false,$outputMode=false) {
		
			$this->searchString = stripslashes('"'.$searchString.'"');

			$this->maxResults = $maxResults ? (int)$maxResults : self::defMaxResults;

			if ($languageID) $this->languageID = $languageID;
				
			$this->setOutputMode($outputMode);

		}

		function setOutputMode($outputMode) {
			if (in_array(strtolower($outputMode),$this->outputModes)) 
				$this->outputMode = strtolower($outputMode);
			else
				$this->outputMode = self::defOutputMode;
		}

		function setOutputDataType($dataType) {

			if($dataType=='') return;

			if (in_array($dataType,$this->outputDataTypes))
				$this->outputDataType = $dataType;

		}

		function getURL($url,$timeout=10) {
			$oldTimeout = ini_set('default_socket_timeout', $timeout);

			$file = @fopen($url, 'r');
			if ($file !== false) {
				$buffer = '';
				while (!feof($file)) {
					$buffer .= fread($file, 8192);
				}
			}

			ini_set('default_socket_timeout', $oldTimeout);

			if ($file !== false) {
				stream_set_timeout($file, $timeout);
				stream_set_blocking($file, 0);
			}

			if ($file == false) {
				return false;
			} else {	
				fclose($file);
				return $buffer;
			}
		}

		function getOutput() {
			if ($this->outputMode == 'json') {
				return json_encode($this->searchResults);
			} else
			if ($this->outputMode == 'array') {
				return $this->searchResults;
			}
		}

		function getResults() {

			if (!$this->usingCached)
				$this->doSearch();

			$this->haveResults = count((array)$this->searchResults)>0;

			$this->didRespond = true;

			return $this->getOutput();
		}
		
		function doSearch() {

			$indexPage = $this->getURL(sprintf(self::searchURL,rawurlencode($this->searchString)));

			$indexPage = str_replace('&','&amp;',$indexPage);

			$this->processResults($indexPage);

		}

		function processResults($file) {

			$this->searchResults = $file;

		}

	}

	session_start();

	$s = isset($_REQUEST['search']) ? $_REQUEST['search'] : null;

	if (is_null($s)) $s = isset($_REQUEST['s']) ? $_REQUEST['s'] : null;

	$n = isset($_REQUEST['n']) ? $_REQUEST['n'] : null;
	$l = isset($_REQUEST['language']) ? $_REQUEST['language'] : null;
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : null;

	$linnaeusNG = new linnaeusNG($s,$n,$l,'array');

	$linnaeusNG->setOutputDataType($type);

	$result = $linnaeusNG->getResults();
	if (!$linnaeusNG->didRespond) {
	
		echo linnaeusNG::siteTitle.linnaeusNG::noResponseMessage;
	} else {
		$_SESSION['results']['details'][linnaeusNG::siteTitle][$s][$l][$n] = $result;
 		echo $linnaeusNG->getOutput();
	}

?>