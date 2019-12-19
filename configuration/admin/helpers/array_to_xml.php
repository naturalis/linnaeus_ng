<?php

class SimpleXMLExtended extends SimpleXMLElement // http://coffeerings.posterous.com/php-simplexml-and-cdata
{
  public function addCData($cdata_text)
  {
    $node= dom_import_simplexml($this); 
    $no = $node->ownerDocument; 
    $node->appendChild($no->createCDATASection($cdata_text)); 
  } 
}


class ArrayToXml
{

	public function toXml($data, $rootNodeName = 'data', $xml=null)
	{
		
		if ($xml == null)
			/*$xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");*/
			$xml= new SimpleXMLExtended("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
		 
		// loop through the data passed in.
		foreach($data as $key => $value)
		{
			// no numeric keys in our xml please!
			if (is_numeric($key))
			{
				// make string key...
				$key = "unknownNode_". (string) $key;
			}
			
			// replace anything not alpha numeric
			$key = preg_replace('/[^a-z_]/i', '', $key);

			// if there is another array found recrusively call this function
			if (is_array($value))
			{
				$node = $xml->addChild($key);
				// recrusive call.
				ArrayToXML::toXml($value, $rootNodeName, $node);
			}
			else
			{
				// add single node.
				//$value = htmlentities($value);
				//$xml->addChild($key,$value);
				$xml->addChild($key)->addCData($value);
			}
		 
		}
		/*
		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($xml->asXML());
		return $dom->saveXML();
		*/
		// pass back as string. or simple xml object if you want!
		return str_replace('><','>'.chr(10).'<',$xml->asXML());

	}


}

?>