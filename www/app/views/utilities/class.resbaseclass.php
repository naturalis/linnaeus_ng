<?php

	class resbaseclass {
	
		var $languageID = 'EN';
		var $domainsToIgnore = array(
			'www.nlbif.nl',
			'www.nl.gbif.net'
		);
		
		var $outputModes = array('json','html','array');
		var $usingCached = false;
		
		var $haveResults = false;
		var $didRespond = false;

		const defOutputMode = 'html';
		const externalLinkIcon = 'images/link.gif';
		const noResponseMessage = ' did not respond.';
		const noDataMessage = 'no data available';
		const showAllResultsMessage = 'show all %s results';
		const showHideResultsMessage = 'hide';

		function setOutputMode($outputMode) {
			if (in_array(strtolower($outputMode),$this->outputModes)) 
				$this->outputMode = strtolower($outputMode);
			else
				$this->outputMode = self::defOutputMode;
		}

		function setResults($set) {
			if ($set!='') {
				$this->searchResults = $set;
				$this->usingCached = true;
			} else {
				$this->searchResults = false;
				$this->usingCached = false;
			}
		}
		
		function setGetTextFunction($functionName) {
			$this->getTextFunction = $functionName;
			// call_user_func($this->getTextFunction,i)
		}
	
		function getText($i) {
			if ($this->getTextFunction!='') {
				return call_user_func($this->getTextFunction,$i);
			} else {
				return $i;
			}
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

		function getCurlURL($url,$timeout=10) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,$timeout);
			$buffer = curl_exec($ch);
			curl_close($ch);
			return $buffer ? $buffer : false;
		}

		function getRemoteImgSize($url,$timeout=10) {
			$oldTimeout = ini_set('default_socket_timeout',$timeout);
			$dummy = @fopen($url, 'r');
			if ($dummy !== false) {
				$dummy = getimagesize($url);
			}
			ini_set('default_socket_timeout', $oldTimeout);
			return $dummy;
		}
	
		function getOutput() {
			if ($this->outputMode == 'json') {
				return json_encode($this->searchResults);
			} else
			if ($this->outputMode == 'array') {
				return $this->searchResults;
			} else
			if ($this->outputMode == 'html') {
				return $this->getHTMLOutput();
			}
		}

		function hasNoResults() {
			return $this->haveResults == false;
//			return $this->searchResults == self::noDataMessage;
		}
		
		function shortenText($txt,$maxWords=6,$returnFalse=false) {
			$tmp = explode(' ',trim($txt));
			if (count($tmp) > $maxWords)
				return implode(' ',array_slice($tmp,0,6)).'...';
			else
				return $returnFalse ? false : $txt;
		}
		
	}

?>