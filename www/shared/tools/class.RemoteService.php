<?php

	class RemoteService {

		/*
			class to retrieve data from a remote webservice
			designed to circumvent AJAX cross-domain policies.
			
			known issues: no attempt is made to 
			
			usage:
	
			$r = new RemoteService;
			$r->setUrl( 'http://domain.com/path/to/remote/webservice.asp' );
			$r->fetchData();
			$r->sendHeaders();
			$r->printData();
				or
			$data = $r->getData();  // without the headers

		*/
		
		private $url;
		private $timeout=10;
		private $data;
		private $headers;
		private $requestHeaders=["Connection: close"];
		
		public function setUrl( $url )
		{
			$this->url=$url;
		}

		public function getUrl()
		{
			return $this->url;
		}

		public function setTimeout( $timeout )
		{
			if ( !is_null($timeout) && is_numeric($timeout) )
				$this->timeout=$timeout;
		}

		public function getTimeout()
		{
			return $this->timeout;
		}

		public function setData( $data )
		{
			$this->data=$data;
		}

		public function fetchData()
		{
			try
			{
				$this->initialize();
				$this->fetchRemoteData();
			}
			catch (Exception $e)
			{
				die( "failed: " . $e->getMessage() );
			}
		}

		public function sendHeaders()
		{
			foreach((array)$this->headers as $val)
			{
				header( $val );
			}
		}

		public function printData()
		{
			echo $this->data;
		}

		public function getData()
		{
			return $this->data;
		}

		public function setRequestHeaders( $h )
		{
			if ( is_array($h) )
			{
				$this->requestHeaders=array_merge($this->requestHeaders,$h);
			}
			else
			{
				$this->requestHeaders[]=$h;
			}
		}

		private function initialize()
		{
			if ( empty($this->getUrl() ) )  throw new Exception( "no URL" );
		}

		private function fetchRemoteData()
		{
			$this->setData(
				file_get_contents(
					$this->getUrl(), 
					false, 
					stream_context_create( [
						"http" => [
							"header"=>implode("\r\n",$this->requestHeaders)."\r\n", 
							"ignore_errors" => true,
							"timeout" => $this->getTimeout() 
						] 
					] 
					)
				)
			);

			$this->setHeaders( $http_response_header );
			/*
			getTimeout
			$this->setData( file_get_contents( $this->getUrl() ) );
			$this->setHeaders( $http_response_header );
			*/
		}

		private function setHeaders( $headers )
		{
			$this->headers=$headers;
		}

	}
