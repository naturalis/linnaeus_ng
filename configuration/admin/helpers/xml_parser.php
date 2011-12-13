<?php

class XmlParser
{

	private $_fileName = null;
	private $_node = null;
	
	public function setFileName($fileName)
	{
		
		$this->_fileName = $fileName;
		
	}

	public function getNode($node)
	{
	
		if(!isset($node)) return null;

		$this->setNode($node);

		$d = new XMLReader;

		if ($d->open($this->_fileName)) {

			while ($d->read() && $d->name !== $this->_node);

			while ($d->name === $this->_node) {

				$fixedNode = $this->fixNode($d->readOuterXML());

				if ($xml = simplexml_load_string($fixedNode)) {

					return $xml;
					$d->next($this->_node);

				}

			}

		}

	}
	
	private function setNode($node)
	{
		
		$this->_node = $node;
		
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