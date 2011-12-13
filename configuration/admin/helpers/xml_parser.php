<?php

class XmlParser
{

	private $_fileName = null;
	private $_nodeName = null;
	private $_singleNode = false;
	
	public function setFileName($fileName)
	{
		
		$this->_fileName = $fileName;
		
	}

	public function getNode($name)
	{
	
		$this->_singleNode = true;
		
		return $this->getNodes($name);

	}

	public function getNodes($name)
	{
	
		if(!isset($name)) return null;
		
		$this->setNodeName($name);

		$d = new XMLReader;
		
		$r = array();

		if ($d->open($this->_fileName)) {

			while ($d->read() && $d->name !== $this->_nodeName);
			
			while ($d->name === $this->_nodeName) {

				$fixedNode = $this->fixNode($d->readOuterXML());

				if ($xml = simplexml_load_string($fixedNode)) {

					if ($this->_singleNode===true) return $xml;

					$r[] = $xml;

					$d->next($this->_nodeName);

				}

			}

		}
		
		return $r;

	}
	


	private function setNodeName($name)
	{
		
		$this->_nodeName = $name;
		
	}

	private function fixNode ($str) {

		return $this->fixTags(html_entity_decode($str, ENT_QUOTES, "utf-8"));

	}
	
	private function fixTags($str){

		$str = htmlspecialchars($str);
		$str = preg_replace("/=/", "=\"\"", $str);
		$str = preg_replace("/&quot;/", "&quot;\"", $str);
		$tags = "/&lt;(\/|)(\w*)(\ |)(\w*)([\\\=]*)(?|(\")\"&quot;\"|)(?|(.*)?&quot;(\")|)([\ ]?)(\/|)&gt;/i";
		$replacement = "<$1$2$3$4$5$6$7$8$9$10>";
		$str = preg_replace($tags, $replacement, $str);
		$str = preg_replace("/=\"\"/", "=", $str);
		return $str;

	}
	
}

?>