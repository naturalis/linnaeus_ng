
<body id="body" class="conceptcard">
    
    <div id="ajaxloader"></div>
    <div id="filterDialogContainer">
        <div id="overlay"></div>
        <div id="dialog">
            <div class="titleContainer">
                <span class="title"></span>
                <a href="#" class="ion-close-circled closeOverlay"></a>
            </div>
            <div class="content"></div>
        </div>
    </div>
    <div class="topBarContainer responsive">
        <div class="topBar">
            {if $master_matrix.id}
                <a href="?mtrx={$master_matrix.id}" class="backLink"><i class="ion-ios-arrow-back backIcon"></i></a><br />
            {else}
                <a href="/linnaeus_ng/" class="backLink"><i class="ion-ios-arrow-back backIcon"></i></a>
            {/if}
            <div class="pageTitleContainer">
                <span class="pageTitle">
                    {$matrix.name}    
                </span>
            </div>
           
            {if $introduction_links[$settings->introduction_topic_colophon_citation] || $introduction_links[$settings->introduction_topic_versions]}
                <div class="colofonLink">
                    <a href="#" onClick="
                    overlayOpen();
    	        {if $introduction_links[$settings->introduction_topic_colophon_citation]}
                    $.get( '../introduction/topic.php?id={$introduction_links[$settings->introduction_topic_colophon_citation].page_id}&format=plain' )
                    .success(function(data) { colofonOverlay( data ,'{t}Colofon en citatie{/t}'); } ) ;
                {/if}
                {if $introduction_links[$settings->introduction_topic_versions]}
                    $.get( '../introduction/topic.php?id={$introduction_links[$settings->introduction_topic_versions].page_id}&format=plain' )
                    .success(function(data) { colofonVersion( data ,'{t}Versiegeschiedenis{/t}'); } ) ;
				{/if}
                    ">{t}Colofon en citatie{/t}</a>
                </div>
            <a href="#" onClick="
	            overlayOpen();
            {if $introduction_links[$settings->introduction_topic_colophon_citation]}
                $.get( '../introduction/topic.php?id={$introduction_links[$settings->introduction_topic_colophon_citation].page_id}&format=plain' )
                .success(function(data) { colofonOverlay( data ,'{t}Colofon en citatie{/t}'); } ) ;
			{/if}
            {if $introduction_links[$settings->introduction_topic_versions]}
                $.get( '../introduction/topic.php?id={$introduction_links[$settings->introduction_topic_versions].page_id}&format=plain' )
                .success(function(data) { colofonVersion( data ,'{t}Versiegeschiedenis{/t}'); } ) ;
			{/if}
                " class="info"><i class="ion-ios-information-outline info"></i></a>
			{/if}
        </div>
    </div>
    <span id="searchToggle">
        <i class="ion-search"></i>
    </span>
    <span class="filterToggle">
        <i class="ion-navicon-round"></i>
    </span>
    <div class="imageOverlayBackground"></div>
    <div class="imageOverlayContainer">
        <div class="overlayHeader">
            <div class="name"></div>
            <a href="#" class="ion-close-circled closeOverlay"></a>
        </div>
        <div class="image"></div>
        <div class="version"></div>
    </div>
    <div class="container" id="scrollContainer">
    <div id="menuOverlay">
        <div class="menuOverlayScroll"></div>
    </div>
    
        
    <div id="container">

    
    <a href="#top"></a>
    <div id="header">
        <!-- <div id="logo">
            <span id="soortenrgister-link" onClick="window.open('http://www.nederlandsesoorten.nl/nsr/nsr/home.html','_self');" title="Nederlands Soortenregister"></span>
            <span id="home-link" onClick="window.open('identify.php','_self');"></span>
        </div> -->
        <div id="logo-container">
            <a href="/linnaeus_ng/">
                <img width="128" height="190" alt="" src="{$session.app.system.urls.systemMedia}naturalis-logo.svg" onerror="this.onerror=null; this.src='{$session.app.system.urls.systemMedia}naturalis-logo.png'">
            </a>
        </div>
        <div class="headerImage">
            <img src="{$session.app.system.urls.systemMedia}placeholderheader.png" alt="">
            <h1 class="pageTitle">
                <span class="determinatiesleutel">{t}determinatiesleutel{/t}</span><br />
                {$matrix.name}
            </h1>
        </div>    
    </div>

