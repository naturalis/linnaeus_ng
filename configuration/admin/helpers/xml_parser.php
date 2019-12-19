<?php

class XmlParser
{

	private $_fileName = null;
	private $_nodeName = null;
	private $_getSingleNode = false;
	private $_callbackFunction = null;
	private $_doReturnValues = false;
	
	public function setFileName($fileName)
	{
		
		$this->_fileName = $fileName;
		
	}

	public function setDoReturnValues($state)
	{

		if (is_bool($state)) $this->_doReturnValues = $state;
	
	}

	public function setCallbackFunction($function)
	{

		if (is_callable($function)) {

			$this->_callbackFunction = $function;

			$this->setDoReturnValues(false);

		}
	
	}

	public function getNode($name)
	{
	
		$this->_getSingleNode = true;
		
		return $this->getNodes($name);

	}

	public function getNodes($name)
	{
	
		if(!isset($name)) return null;
		
		$this->setNodeName($name);

		$d = new XMLReader;
		
		$r = array();

		if ($d->open($this->_fileName)) {

			while ($d->read() && $d->name !== $this->_nodeName)
                ;
			
			while ($d->name === $this->_nodeName) {
				
				$fixedNode = $this->fixNode($d->readOuterXML());

				libxml_use_internal_errors(true);

				$xml = simplexml_load_string($fixedNode);

				if ($xml === false) {
				
					echo '<p><b>XML-parser failed</b></p><p>simplexml_load_string() returned the following error:<br/>';

						foreach(libxml_get_errors() as $error)
							echo '&#149; "'.$error->message.'" in line '.$error->line.' at column '.$error->column.'<br />';
					
					die('</p><p>(abnormal program termination)</p>');
				
				} else {
				
					if (isset($this->_callbackFunction)) call_user_func($this->_callbackFunction,$xml,$this->_nodeName);

					if ($this->_getSingleNode===true) {

						if ($this->_doReturnValues===true)
							return $xml;
						else
							return;

					}

					if ($this->_doReturnValues===true) $r[] = $xml;

					$d->next($this->_nodeName);

				}

			}

		}
		
		if ($this->_doReturnValues===true) return $r;

	}
	


	private function setNodeName($name)
	{
		
		$this->_nodeName = $name;
		
	}

	private function fixNode ($str)
	{

		return $this->fixTags(html_entity_decode($str, ENT_QUOTES, "utf-8"));

	}
	
	private function fixTags($str)
	{

		$str = htmlspecialchars($str);

		$find = array(
			"/=/",
			"/&quot;/", 
			"/&lt;(\/|)(\w*)(\ |)(\w*)([\\\=]*)(?|(\")\"&quot;\"|)(?|(.*)?&quot;(\")|)([\
]?)(\/|)&gt;/i",
			"/=\"\"/"
		);

		$replace = array(
			"=\"\"", 
			"&quot;", 
			"<$1$2$3$4$5$6$7$8$9$10>", 
			"="
		);

		$str = preg_replace($find, $replace, $str);

		$str = trim(str_replace(array(PHP_EOL, "\t"), '', $str));

		return $str;

	}
	
}

?>