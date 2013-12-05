
<body id="body" class="conceptcard">

    <div id="container">
    <a name="top"></a>

	<div id="body">

        <div id="header">

            <div id="logo">
                <span id="soortenrgister-link" onClick="window.open('http://www.nederlandsesoorten.nl/nsr/nsr/home.html','_self');" title="Nederlands Soortenregister"></span>
                <span id="home-link" onClick="window.open('identify.php','_self');"></span>
            </div>

            <h2 id="slogan">	
                {t}Overzicht van de Nederlandse biodiversiteit{/t}
            </h2>

            <div id="menucontainer">
			{snippet}matrix_main_menu.html{/snippet}
           </div>

            <div id="logo-container">
                <a href="http://www.eis-nederland.nl/" target="_blank"><img id="logo-EIS" src="{$session.app.system.urls.systemMedia}logo-eisDEF-CMYK-2.png" title="Stichting European Invertebrate Survey Nederland" /></a>
                <a href="http://www.naturalis.nl/" target="_blank"><img id="logo-NBC" src="{$session.app.system.urls.systemMedia}nbc-logo.png" title="Naturalis Biodiversity Center" /></a>
            </div>

        </div>

