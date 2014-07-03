	<div id="right">

		<div id="quicksearch">
			<h2>Zoek op naam</h2>
	
				<form id="inlineformsearch" name="inlineformsearch" action="../search/nsr_search.php" method="get">
				<fieldset>
					<label accesskey="t" for="searchString">Zoek op naam</label>
					<input id="inlineformsearchInput" type="text" name="search" class="searchString" title="{t}Zoek op naam{/t}" value="{$search.search}" />
					<input id="inlineformsearchButton" type="submit" value="{t}zoek{/t}" class="zoekknop" /><br>
					<div id="suggestList"></div>
				</fieldset>
				<ul>
					<li class="searchdb">
						<a href="../search/nsr_search_extended.php"><b>Uitgebreid zoeken</b></a>
					</li>
					<li class="level2">
						<a href="../search/nsr_search_pictures.php"><b>Foto's zoeken</b></a>
					</li>
					<li class="level2">
						<a href="../species/tree.php"><b>Taxonomische boom</b></a>
					</li>
				</ul>
				</form>
				<script type="text/JavaScript">
				$(document).ready(function() {
					$( '#inlineformsearchInput' ).focus();
				});
				</script>				
		</div>
		
		<div id="block-boxes-aside-contribute" class="block block-boxes block-boxes-simple" style="margin-top:170px">
		<div class="content">
		<div id="boxes-box-aside_contribute" class="boxes-box">
		<div class="boxes-box-content">
		<p>
		<a href="/content/fotosysteem-wordt-vernieuwd">
		<img src="/sites/all/modules/nlsoort_aside_contribute/banner.jpg">
		</a>
		</p>
		</div>
		</div>
		</div>
		</div>
		<div id="block-boxes-aside-widget" class="block block-boxes block-boxes-simple" style="margin-top:10px">
		<div class="content">
		<div id="boxes-box-aside_widget" class="boxes-box">
		<div class="boxes-box-content">
		<p>
		<a href="/node/48">
		<img src="/sites/all/modules/nlsoort_aside_widget/banner.jpg">
		</a>
		</p>
		</div>
		</div>
		</div>
		<div id="block-boxes-aside-widget" class="block block-boxes block-boxes-simple" style="margin-top:10px">
		<div class="content">
		<div id="boxes-box-aside_naturalis" class="boxes-box">
		<div class="boxes-box-content">
		<p><a href="http://www.naturalis.nl"><img src="/sites/all/modules/nlsoort_aside_naturalis/banner.jpg"></a></p>
		</div>
		</div>
		</div>
		</div>
		</div>


	</div>
	
