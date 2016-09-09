{if $smarty.server.SERVER_NAME == 'identifiers.localhost'}
	{assign var=server value="/app/views"}
{else}
	{assign var=server value="/linnaeus_ng/app/views"}
{/if}
<a href="#" class="menuToggle">
	<img src="{$baseUrl}app/style/img/menutoggle.png" alt="" />
	<span class="mobileTitle">Nederlands Soortenregister</span>
</a>
<ul class="menu">
	<li class="home"><a href="{$server}/">Home</a></li>
	<li>
		<a href="{$server}/node/12">Over het soortenregister</a>
		<i class="ion-plus-round plus"></i>
		<i class="ion-minus-round min"></i>
		<ol>
			<li><a href="{$server}/node/13">Doel en uitgangspunten</a></li>
			<li><a href="{$server}/node/14">Taxonomie</a></li>
			<li><a href="{$server}/node/15">Voorkomen</a></li>
			<li><a href="{$server}/node/16">DNA Barcoding</a></li>
			<li><a href="{$server}/node/17">Wie is wie?</a></li>
			<li><a href="{$server}/content/citering">Citering</a></li>
			<li><a href="{$server}/content/gebruiksvoorwaarden-fotos">Gebruiksvoorwaarden foto's</a></li>
			<li><a href="{$server}/node/48">Natuurwidget</a></li>
		</ol>
	</li>
	<li>
		<a href="#">
			Exoten
		</a>
		<i class="ion-plus-round plus"></i>
		<i class="ion-minus-round min"></i>
		<ol>
			<li><a href="{$server}/node/21">Inleiding</a></li>
			<li><a href="{$server}/node/22">Samenwerking</a></li>
			<li><a href="{$server}/content/definitie">Definitie</a></li>
			<li><a href="{$server}/node/24">Lijsten</a></li>
			<li><a href="{$server}/node/25">Soortinformatie</a></li>
			<li><a href="{$server}/node/26">Literatuur en websites</a></li>
			<li><a href="{$server}/content/exotenpaspoort">Exotenpaspoort</a></li>
		</ol>
	</li>
	<li><a href="{$server}/node/27">Determineren</a></li>
	<li>
		<a href="{$server}/search/nsr_search.php">Zoeken</a>
		<i class="ion-plus-round plus"></i>
		<i class="ion-minus-round min"></i>
		<ol>
			<li><a href="{$server}/search/nsr_search.php">Snel zoeken</a></li>
			<li><a href="{$server}/search/nsr_search_extended.php">Uitgebreid zoeken</a></li>
			<li><a href="{$server}/search/nsr_search_pictures.php">Foto's zoeken</a></li>
			<li><a href="{$server}/species/tree.php">Taxonomische boom</a></li>
		</ol>
	</li>
</ul>
<!-- 
<div class="content">
	<ul class="menu clearfix"><li class="first leaf"><a class="active" href="{$server}/">Home</a></li>
	<li class="expanded"><a title="" href="{$server}/node/12">Over het Soortenregister</a><ul class="menu clearfix"><li class="first leaf"><a href="{$server}/node/13">Doel en uitgangspunten</a></li>
	<li class="leaf"><a href="{$server}/node/14">Taxonomie</a></li>
	<li class="leaf"><a href="{$server}/node/15">Voorkomen</a></li>
	<li class="leaf"><a href="{$server}/node/16">DNA Barcoding</a></li>
	<li class="collapsed"><a href="{$server}/node/17">Wie is wie?</a></li>
	<li class="leaf"><a href="{$server}/node/18">Citering</a></li>
	<li class="leaf"><a href="{$server}/content/gebruiksvoorwaarden-fotos">Gebruiksvoorwaarden foto's</a></li>
	<li class="last leaf"><a href="{$server}/node/48">Natuurwidget</a></li>
	</ul></li>
	<li class="expanded"><a title="" href="{$server}/node/19">Exoten</a><ul class="menu clearfix"><li class="first leaf"><a href="{$server}/node/21">Inleiding</a></li>
	<li class="leaf"><a href="{$server}/node/22">Samenwerking</a></li>
	<li class="leaf"><a href="{$server}/node/23">Definitie</a></li>
	<li class="leaf"><a href="{$server}/node/24">Lijsten</a></li>
	<li class="leaf"><a href="{$server}/node/25">Soortinformatie</a></li>
	<li class="last"><a href="{$server}/node/26">Literatuur en websites</a></li>
	<li class="last leaf"><a href="{$server}/content/exotenpaspoort">Exotenpaspoort</a></li>
	</ul></li>
	<li class="leaf"><a href="{$server}/node/27">Determineren</a></li>
	<li class="last expanded"><a title="" href="{$server}/node/28">Bronnen</a><ul class="menu clearfix"><li class="first leaf"><a href="{$server}/node/29">Bronnen A-B</a></li>
	<li class="leaf"><a href="{$server}/node/30">Bronnen C-H</a></li>
	<li class="leaf"><a href="{$server}/node/31">Bronnen I-R</a></li>
	<li class="last leaf"><a href="{$server}/node/32">Bronnen S-Z</a></li>
	</ul></li>
	</ul>
</div> -->