<body id="body" class="conceptcard">
    <div class="logoContainer">
        <div class="container" style="cursor:pointer;" onClick="window.open('/','_top')">
            <!-- <img src="{$projectUrls.projectCSS}../img/siteTitle.png" class="siteTitle" /> -->
            <img src="{$projectUrls.projectCSS}../img/logo-small-pink.png" class="logo" />
            <div class="siteTitle">
                <h1>{$session.app.project.title}</h1>
                <h2>{$generalHeaderSubtitle}</h2>
            </div>
        </div>
    </div>
    <div class="menuContainer">
        <form id="inlineformsearch" name="inlineformsearch" action="../search/nsr_search.php" method="get">
            <div class="searchInputHolder">
                <input id="name" name="search" type="text" placeholder="{t}Snel zoeken op soort/taxon...{/t}" name="search" class="searchString" title="{t}Zoek op naam{/t}" value="{$search.search}"  autocomplete="off" />
                <a href="javascript:void(0)" class="close-suggestion-list close-suggestion-list-js">
                    <i class="ion-close-round"></i>
                </a>
            </div>
            <div id="name_suggestion" class="suggestList" style="display:none"></div>
        </form>
        <div class="menu-search">
            <div class="menuHolder">
                {snippet}static_menu.html{/snippet}
            </div>
        </div>
    </div>
    <div class="scrollContainer">
    <div id="container">
    <a name="top"></a>
	<div id="body">

