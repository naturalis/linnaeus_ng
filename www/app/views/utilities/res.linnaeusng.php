<?php

	require_once 'class.resbaseclass.php';

	class linnaeusNG extends resbaseclass {

		const siteTitle = 'Linnaeus NG';
		const defOutputMode = 'array';
		const defMaxResults = 5;
		const maxInitialSynonyms = 3;
		const maxInitialCommonNames = 15;
//		const searchURL = 'http://dev.eti.uva.nl/linnaeus_ng/app/views/linnaeus/search.php?search=%s&format=plain';
//		const searchURL = 'http://linnaeus/app/views/linnaeus/r_search.php?search=%s&p=2&l=26'; //26 = def english

//		const searchURL = 'http://linnaeus/app/views/linnaeus/r_search.php?search=%s&p=64&l=26'; //26 = def english
		const searchURL = 'http://dev.eti.uva.nl/linnaeus_ng/app/views/linnaeus/r_search.php?search=%s&p=64&l=26'; //26 = def english

		function linnaeusNG($searchString,$maxResults=false,$languageID=false,$outputMode=false) {
		
			$this->searchString = stripslashes($searchString);

			$this->maxResults = $maxResults ? (int)$maxResults : self::defMaxResults;

			if ($languageID)
				$this->languageID = $languageID;
				
			$this->setOutputMode($outputMode);

		}

		function setOutputDataType($dataType) {

			if($dataType=='') return;

			if (in_array($dataType,$this->outputDataTypes))
				$this->outputDataType = $dataType;

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

	$s = isset($_REQUEST['s']) ? $_REQUEST['s'] : null;
	$n = isset($_REQUEST['n']) ? $_REQUEST['n'] : null;
	$l = isset($_REQUEST['l']) ? $_REQUEST['l'] : null;
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