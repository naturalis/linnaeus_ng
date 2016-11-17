<body id="body" class="conceptcard">
    <div class="logoContainer">
        <div class="container">
            <!-- <img src="{$projectUrls.projectCSS}../img/siteTitle.png" class="siteTitle" /> -->
            <img src="{$projectUrls.projectCSS}../img/logo-small-pink.png" class="logo" />
            <div class="siteTitle">
                <h1>Nederlands Soortenregister</h1>
                <h2>Overzicht van de Nederlandse biodiversiteit</h2>
            </div>
        </div>
    </div>
    <div class="menuContainer">
        <form id="inlineformsearch" name="inlineformsearch" action="../search/nsr_search.php" method="get">
            <div class="searchInputHolder">
                <input id="name" name="search" type="text" placeholder="Snel zoeken..." name="search" class="searchString" title="{t}Zoek op naam{/t}" value="{$search.search}"  autocomplete="off" />
                <a href="javascript:void(0)" class="close-suggestion-list close-suggestion-list-js">
                    <i class="ion-close-round"></i>
                </a>
            </div>
            <div id="name_suggestion" class="suggestList"></div>
        </form>
        <div class="menu-search">
            
            <div class="menuHolder">
                {* include file="../shared/nsr_main_menu.tpl" *}
                <a href="javascript:void(0)" class="search-toggle search-toggle-js">
                    <img src="{$projectUrls.projectCSS}../img/search.svg" class="siteTitle" />
                </a>
            </div>
        </div>
    </div>
    <div class="scrollContainer">
    <div id="container">
    <a name="top"></a>
	<div id="body">

