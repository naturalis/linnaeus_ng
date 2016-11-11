<body id="body" class="conceptcard">
    <div class="menuContainer">
        <form id="inlineformsearch" name="inlineformsearch" action="../search/nsr_search.php" method="get">
            <input id="name" name="search" type="text" placeholder="Snel zoeken..." name="search" class="searchString" title="{t}Zoek op naam{/t}" value="{$search.search}"  autocomplete="off" />
            <div id="name_suggestion" class="suggestList"></div>
        </form>
        <div class="menuHolder">
        {* include file="../shared/nsr_main_menu.tpl" *}
            <a href="javascript:void(0)" class="search-toggle search-toggle-js">
                <img src="{$projectUrls.projectCSS}../img/search.svg" class="siteTitle" />
            </a>
        </div>
    </div>
	<div style="margin-top:50px;"></div>
    <div class="scrollContainer">
    <div id="container">
    <a name="top"></a>
	<div id="body">
        <div id="header">
            <img src="{$projectUrls.projectCSS}../img/siteTitle.png" class="siteTitle" />
            <img src="{$projectUrls.projectCSS}../img/logo-white.png" class="logo" />
        </div>

