<?php

class OldShowMediaLinkReplacer {
	
	private $content;
	private $transformed;
	private $pattern;
	private $replacement;
		
	public function __construct()
	{
		$this->setPattern( '/\<span(\s*)class="inline-image"(\s*)onclick="showMedia\(\'(.*)\'(\s*),(\s*)\'(.*)\'\);"(\s*)\>(.*)\<\/span\>/imU' );
		$this->setReplacement( '<a data-fancybox="gallery" href="$3" class="ion-camera" data-caption="$6"> $8</a>' );
	}
	
	public function setPattern( $pattern )
	{
		$this->pattern=$pattern;
	}

	public function setReplacement( $replacement )
	{
		$this->replacement=$replacement;
	}

	public function setContent( $content )
	{
		$this->content=$content;
	}

	public function replaceLinks()
	{
		$this->transformed=preg_replace($this->pattern, $this->replacement, $this->content);
	}

	public function getTransformedContent()
	{
		return $this->transformed;
	}
	
}