<div id="header-container">
{if $session.project.logo}
	<div id="image">
	<a href="{$session.project.urls.project_start}"><img src="{$session.project.urls.project_media}{$session.project.logo|escape:'url'}" id="project-logo" /></a>
	</div>
{/if}
</div>

<div id="tanbif-menu">
<a href="http://test.eti.uva.nl/tanbif/" title="Home page"><span class="mainmenuitem" alt="Home page">Home page</span></a>
<span class="mainmenuseparator">|</span>
<a href="http://test.eti.uva.nl/tanbif/search.php" title="Search"><span class="mainmenuitem" alt="Search">Search</span></a>
<span class="mainmenuseparator">|</span>
<a href="/lng/app/views/species/" title="Browse species"><span class="mainmenuitem{if $controllerBaseName=='species'}_selected{/if}" alt="Browse species">Browse species</span></a>
<span class="mainmenuseparator">|</span>
<a href="/lng/app/views/matrixkey/identify.php" title="Identify"><span class="mainmenuitem{if $controllerBaseName=='matrixkey'}_selected{/if}" alt="Identify">Identify</span></a>
<span class="mainmenuseparator">|</span>
<a href="http://test.eti.uva.nl/tanbif/news.php" title="News and events"><span class="mainmenuitem" alt="Biodiversity news">News and events</span></a>
<span class="mainmenuseparator">|</span>
<a href="http://test.eti.uva.nl/tanbif/forum/index.php" title="Forum"><span class="mainmenuitem" alt="Forum">Forum</span></a>
<span class="mainmenuseparator">|</span>
<a href="http://test.eti.uva.nl/tanbif/gallery.php" title="Gallery"><span class="mainmenuitem" alt="Gallery">Gallery</span></a>
<span class="mainmenuseparator">|</span>
<a href="http://test.eti.uva.nl/tanbif/contentpage.php?cat=bio-facts" title="Bio facts"><span class="mainmenuitem" alt="Bio facts">Bio facts</span></a>
<span class="mainmenuseparator">|</span>
<a href="http://test.eti.uva.nl/tanbif/contentpage.php?cat=partners" title="Partners"><span class="mainmenuitem" alt="Partners">Partners</span></a>
<span class="mainmenuseparator">|</span>
<a href="http://test.eti.uva.nl/tanbif/contentpage.php?cat=about-tanbif" title="About TanBIF"><span class="mainmenuitem" alt="About TanBIF">About TanBIF</a>
<span class="mainmenuseparator">|</span>
<a href="http://test.eti.uva.nl/tanbif/gbifwidget.php" title="GBIF Widget"><span class="mainmenuitem" alt="GBIF Widget">GBIF Widget</span></a>
<span class="mainmenuseparator"></span>
</div>