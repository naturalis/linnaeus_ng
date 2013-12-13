{include file="../shared/head.tpl"}

  <body class='oranje conceptcard' style='cursor: default;'>
    <!-- ***changes*** Added class for color of the facets. Use any of geel a oranje, rood, blauw, groen, paars, bruin, grijs -->
    
{include file="../shared/navbar.tpl"}
	
    <div id='container'>
      <a name='top'></a>
      <div id='main'>
        <header class='groen' id='header'>
          <h1>
            {$session.app.project.title}
            <small>
              {t}identification key{/t}
            </small>
          </h1>
        </header>
        <div id='dialogRidge'>
          <div id='left'>
            <div id='quicksearch'>
              <h2>{t}Zoek op naam{/t}</h2>
              <form action='' id='inlineformsearch' method='post' name='inlineformsearch' onsubmit='nbcDoSearch();return false;'>
                <label accesskey='t' for='searchString'></label>
                <input class='searchString' id='inlineformsearchInput' name='searchString' title='{t}Zoek op naam{/t}' type='text' value=''>
                <input class='zoekknop' id='inlineformsearchButton' type='submit' value='{t}zoek{/t}'>
              </form>
            </div>
            <div id='facets'>
              <h2>{t}Zoek op kenmerken{/t}</h2>
              <span id='facet-categories-menu'></span>
            </div>
            <div class='facetCategories clearSelectionBtn ghosted' id='clearSelectionContainer'>
              <span class='icon icon-reload'></span>
              <a href='#' id='clearSelectionLink' onclick='nbcClearStateValue();return false;'>
                {t}Opnieuw beginnen{/t}
              </a>
            </div>

			{if 1==1 || $master_matrix_id}
				RESTYLE: <a href="?mtrx={$master_matrix_id}">{t}Terug naar de hoofdsleutel{/t}</a>
			{/if}
						
            <div class='left-divider'></div>
            <div id='legendContainer'>
              <h2>{t}Legenda:{/t}</h2>
              <div class='legend-icon-cell'>
                <span class='icon icon-book'></span>
                {t}meer informatie{/t}
              </div>
              <div class='legend-icon-cell'>
                <span class='icon icon-details'></span>
                {t}onderscheidende kenmerken{/t}
              </div>
              <div class='legend-icon-cell'>
                <span class='icon icon-resemblance'></span>
                {t}gelijkende soorten{/t}
              </div>
            </div>
            <div class='left-divider'></div>
            <div id='dataSourceContainer'>

				{snippet}colofon.html{/snippet}

              <div>
                <h3>{t}Geïmplementeerd door{/t}</h3>
                <p>{t}Naturalis & ETI BioInformatics.{/t}</p>
              </div>
            </div>
            <div class='left-divider'></div>
          </div>
          <div class='title-type4' id='content'>
            <div id='resultsHeader'>
              <h2>
                Species similar to
                <span id='similarSpeciesName'>
                  Dark form
                </span>
              </h2>
              <div class='headerSelectionLabel' id='result-count'>
                1-15&nbsp;of&nbsp;97
              </div>
              <div id='similarSpeciesNav'>
                <!-- *** changes *** Added div #similarSpeciesNav -->
                <a href='#' id='clearSimilarSelection'>
                  <span class='icon icon-arrow-left'></span>
                  back
                </a>
                <a href='#' id='showAllLabel'>
                  <span class='icon icon-details'></span>
                  hide all character states
                </a>
              </div>
            </div>
            <div id='results'>
              <div class='hidden' id='similarSpeciesHeader'></div>
              <div class='layout-landscapes' id='results-container'>
                <!-- *** changes *** Added 'layout-landscapes' class. Choose between layout-landscapes and layout-portrait -->
                <!-- *** changes *** Removed all BR's -->
                <!-- *** changes *** Removed result-row -->
                <div class='result' id='res-t-8860'>
                  <div class='result-result'>
                    <div class='result-image-container'>
                      <a href='images//C_ohridella_CD12014_male.jpg' ptitle='%3Ci%3ECameraria%20ohridella%3C/i%3E' rel='prettyPhoto[gallery]' title=''>
                        <img class='result-image' src='images//C_ohridella_CD12014_male.jpg'>
                      </a>
                    </div>
                    <div class='result-labels'>
                      <span class='result-name-scientific'>Cameraria ohridella</span>
                      <span class='result-name-common'></span>
                    </div>
                  </div>
                  <div class='result-icons'>
                    <div class='result-icon icon-book' onClick="window.open('http://gracillariidae.net/species/show/1549','null');" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='More information on species/taxon'>
                      <!-- *** changes *** Removed images -->
                    </div>
                    <div class='result-icon icon-details'></div>
                    <div class='result-icon icon-resemblance'></div>
                  </div>
                  <div class='result-detail hidden' id='det-v-1387' style='display: block;'>
                    <!-- *** NOTE *** use hidden-class to toggle visibility -->
                    <ul>
                      <li>
                        <span class='result-detail-label'>basal streak contour:</span>
                        <span class='result-detail-value'>costal</span>
                      </li>
                      <li>
                        <span class='result-detail-label'>costals:</span>
                        <span class='result-detail-value'>3</span>
                      </li>
                      <li>
                        <span class='result-detail-label'>dorsals:</span>
                        <span class='result-detail-value'>2</span>
                      </li>
                      <li>
                        <span class='result-detail-label'>contour markings:</span>
                        <span class='result-detail-value'>unilateral basal; bilateral</span>
                      </li>
                      <li>
                        <span class='result-detail-label'>apical marking:</span>
                        <span class='result-detail-value'>mottled</span>
                      </li>
                      <li>
                        <span class='result-detail-label'>Host genus:</span>
                        <span class='result-detail-value'>Prunus; Malus; Chaenomeles; Crataegus; Pyrus; Mespilus; Sorbus; Staphylea; Cotoneaster; Cydonia; Amelanchier</span>
                      </li>
                      <li>
                        <span class='result-detail-label'>Host genus/species:</span>
                        <span class='result-detail-value'>Malus domestica; Malus sp.; Chaenomeles sp.; Crataegus sp.; Mespilus sp.; Pyrus communis; Pyrus sp.; Sorbus sp.; Staphylea pinnata; Staphylea sp.; Cotoneaster sp.; Cydonia oblonga; Cydonia sp.; Malus sylvestris; Prunus padus; Prunus sp.; Sorbus aria; Amelanchier ovalis; Amelanchier sp.; Cotoneaster nebrodensis; Malus pumila; Malus baccata; Malus floribunda; Malus prunifolia; Malus purpurea; Prunus insitia</span>
                      </li>
                      <li>
                        <span class='result-detail-label'>Location:</span>
                        <span class='result-detail-value'>Leaf base; Leaf margin; Leaf lobe; Along main vein; Whole leaf</span>
                      </li>
                      <li>
                        <span class='result-detail-label'>Frass:</span>
                        <span class='result-detail-value'>Attached to cocoon</span>
                      </li>
                      <li>
                        <span class='result-detail-label'>Country:</span>
                        <span class='result-detail-value'>The Netherlands; Belgium; Switzerland; Germany; Denmark; Ireland; Luxembourg; Poland; Slovakia; Sweden; Finland; Great Britain; Norway</span>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class='result' id='res-v-1398'>
                  <div class='result-result'>
                    <div class='result-image-container'>
                      <a href='images//Ph_lautella_RMNH1343JCKoster.jpg' ptitle='common%20form%3Cbr%20/%3E%3Ci%3EPhyllonorycter%20lautella%3C/i%3E' rel='prettyPhoto[gallery]' title=''>
                        <img class='result-image' src='images//Ph_lautella_RMNH1343JCKoster.jpg'>
                      </a>
                    </div>
                    <div class='result-labels'>
                      <span class='result-name-scientific'>Phyllonorycter lautella</span>
                      <span class='result-name-common'>
                        common form
                      </span>
                    </div>
                  </div>
                  <div class='result-icons'>
                    <div class='result-icon' onClick="window.open('http://gracillariidae.net/species/show/2330','null');" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='More information on species/taxon'>
                      <img class='result-icon-image' src='images//information_grijs.png'>
                    </div>
                    <div class='result-icon no-content' id='tog-v-1398'></div>
                    <div class='result-icon no-content'></div>
                  </div>
                </div>
                <div class='result' id='res-v-1391'>
                  <div class='result-result'>
                    <div class='result-image-container'>
                      <a href='images//Ph_maestingella_CD012030.jpg' ptitle='dark%20form%3Cbr%20/%3E%3Ci%3EPhyllonorycter%20maestingella%3C/i%3E' rel='prettyPhoto[gallery]' title=''>
                        <img class='result-image' src='images//Ph_maestingella_CD012030.jpg'>
                      </a>
                    </div>
                    <div class='result-labels'>
                      <span class='result-name-scientific'>Phyllonorycter maestingella</span>
                      <span class='result-name-common'>
                        dark form
                      </span>
                    </div>
                  </div>
                  <div class='result-icons'>
                    <div class='result-icon' onClick="window.open('http://gracillariidae.net/species/show/2359','null');" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='More information on species/taxon'>
                      <img class='result-icon-image' src='images//information_grijs.png'>
                    </div>
                    <div class='result-icon no-content' id='tog-v-1391'></div>
                    <div class='result-icon no-content'></div>
                  </div>
                </div>
                <div class='result' id='res-v-1387'>
                  <div class='result-result'>
                    <div class='result-image-container passepartout'>
                      <a href='images//Ph_blancardella_RMNH2298JCKoster_male.jpg' ptitle='dark%20form%3Cbr%20/%3E%3Ci%3EPhyllonorycter%20blancardella%3C/i%3E' rel='prettyPhoto[gallery]' title=''>
                        <img class='result-image' src='images//226629.jpg'>
                      </a>
                    </div>
                    <div class='result-labels'>
                      <span class='result-name-scientific'>Phyllonorycter blancardella</span>
                      <span class='result-name-common'>
                        dark form
                      </span>
                    </div>
                  </div>
                  <div class='result-icons'>
                    <div class='result-icon' onClick="window.open('http://gracillariidae.net/species/show/2096','null');" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='More information on species/taxon'>
                      <img class='result-icon-image' src='images//information_grijs.png'>
                    </div>
                    <div class='result-icon no-content' id='tog-v-1387'></div>
                    <div class='result-icon' onClick="nbcShowSimilar(1387,'v');return false;" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='similar species'>
                      <img class='result-icon-image icon-similar' src='images//gelijk_grijs.png'>
                    </div>
                  </div>
                </div>
                <div class='result' id='res-v-1395'>
                  <div class='result-result'>
                    <div class='result-image-container'>
                      <a href='images//Ph_issikii_RMNH2000072EvN_male_juniuitgekomen.jpg' ptitle='early%20summer%20marked%20form%3Cbr%20/%3E%3Ci%3EPhyllonorycter%20issikii%3C/i%3E' rel='prettyPhoto[gallery]' title=''>
                        <img class='result-image' src='images//Ph_issikii_RMNH2000072EvN_male_juniuitgekomen.jpg'>
                      </a>
                    </div>
                    <div class='result-labels'>
                      <span class='result-name-scientific'>Phyllonorycter issikii</span>
                      <span class='result-name-common'>
                        early summer marked form
                      </span>
                    </div>
                  </div>
                  <div class='result-icons'>
                    <div class='result-icon' onClick="window.open('http://gracillariidae.net/species/show/2298','null');" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='More information on species/taxon'>
                      <img class='result-icon-image' src='images//information_grijs.png'>
                    </div>
                    <div class='result-icon no-content' id='tog-v-1395'></div>
                    <div class='result-icon no-content'></div>
                  </div>
                </div>
                <div class='result' id='res-v-1396'>
                  <div class='result-result'>
                    <div class='result-image-container'>
                      <a href='images//Ph_issikii_RMNH2976JCKoster_male_juliuitgekomen.jpg' ptitle='early%20summer%20mottled%20form%3Cbr%20/%3E%3Ci%3EPhyllonorycter%20issikii%3C/i%3E' rel='prettyPhoto[gallery]' title=''>
                        <img class='result-image' src='images//Ph_issikii_RMNH2976JCKoster_male_juliuitgekomen.jpg'>
                      </a>
                    </div>
                    <div class='result-labels'>
                      <span class='result-name-scientific'>Phyllonorycter issikii</span>
                      <span class='result-name-common'>
                        early summer mottled form
                      </span>
                    </div>
                  </div>
                  <div class='result-icons'>
                    <div class='result-icon' onClick="window.open('http://gracillariidae.net/species/show/2298','null');" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='More information on species/taxon'>
                      <img class='result-icon-image' src='images//information_grijs.png'>
                    </div>
                    <div class='result-icon no-content' id='tog-v-1396'></div>
                    <div class='result-icon no-content'></div>
                  </div>
                </div>
                <div class='result' id='res-v-1403'>
                  <div class='result-result'>
                    <div class='result-image-container'>
                      <a href='images//rolandi_female_crop_edit.JPG' ptitle='female%3Cbr%20/%3E%3Ci%3EPhyllonorycter%20rolandi%3C/i%3E' rel='prettyPhoto[gallery]' title=''>
                        <img class='result-image' src='images//rolandi_female_crop_edit.JPG'>
                      </a>
                    </div>
                    <div class='result-labels'>
                      <span class='result-name-scientific'>Phyllonorycter rolandi</span>
                      <span class='result-name-common'>
                        female
                      </span>
                    </div>
                  </div>
                  <div class='result-icons'>
                    <div class='result-icon' onClick="window.open('http://gracillariidae.net/species/show/2074','null');" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='More information on species/taxon'>
                      <img class='result-icon-image' src='images//information_grijs.png'>
                    </div>
                    <div class='result-icon no-content' id='tog-v-1403'></div>
                    <div class='result-icon no-content'></div>
                  </div>
                </div>
                <div class='result' id='res-v-1394'>
                  <div class='result-result'>
                    <div class='result-image-container'>
                      <a href='images//Ph_rajella_RMNH89120EvN_wittekopenthoroax_female.jpg' ptitle='female%3Cbr%20/%3E%3Ci%3EPhyllonorycter%20rajella%3C/i%3E' rel='prettyPhoto[gallery]' title=''>
                        <img class='result-image' src='images//Ph_rajella_RMNH89120EvN_wittekopenthoroax_female.jpg'>
                      </a>
                    </div>
                    <div class='result-labels'>
                      <span class='result-name-scientific'>Phyllonorycter rajella</span>
                      <span class='result-name-common'>
                        female
                      </span>
                    </div>
                  </div>
                  <div class='result-icons'>
                    <div class='result-icon' onClick="window.open('http://gracillariidae.net/species/show/2468','null');" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='More information on species/taxon'>
                      <img class='result-icon-image' src='images//information_grijs.png'>
                    </div>
                    <div class='result-icon no-content' id='tog-v-1394'></div>
                    <div class='result-icon no-content'></div>
                  </div>
                </div>
                <div class='result' id='res-v-1389'>
                  <div class='result-result'>
                    <div class='result-image-container'>
                      <a href='images//Ph_salicicolella_RMNH1277JCKoster_female.jpg' ptitle='female%3Cbr%20/%3E%3Ci%3EPhyllonorycter%20salicicolella%3C/i%3E' rel='prettyPhoto[gallery]' title=''>
                        <img class='result-image' src='images//Ph_salicicolella_RMNH1277JCKoster_female.jpg'>
                      </a>
                    </div>
                    <div class='result-labels'>
                      <span class='result-name-scientific'>Phyllonorycter salicicolella</span>
                      <span class='result-name-common'>
                        female
                      </span>
                    </div>
                  </div>
                  <div class='result-icons'>
                    <div class='result-icon' onClick="window.open('http://gracillariidae.net/species/show/2085','null');" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='More information on species/taxon'>
                      <img class='result-icon-image' src='images//information_grijs.png'>
                    </div>
                    <div class='result-icon no-content' id='tog-v-1389'></div>
                    <div class='result-icon' onClick="nbcShowSimilar(1389,'v');return false;" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='similar species'>
                      <img class='result-icon-image icon-similar' src='images//gelijk_grijs.png'>
                    </div>
                  </div>
                </div>
                <div class='result' id='res-v-1400'>
                  <div class='result-result'>
                    <div class='result-image-container'>
                      <a href='images//Ph_ulmifoliella_CD11014fem.jpg' ptitle='female%3Cbr%20/%3E%3Ci%3EPhyllonorycter%20ulmifoliella%3C/i%3E' rel='prettyPhoto[gallery]' title=''>
                        <img class='result-image' src='images//Ph_ulmifoliella_CD11014fem.jpg'>
                      </a>
                    </div>
                    <div class='result-labels'>
                      <span class='result-name-scientific'>Phyllonorycter ulmifoliella</span>
                      <span class='result-name-common'>
                        female
                      </span>
                    </div>
                  </div>
                  <div class='result-icons'>
                    <div class='result-icon' onClick="window.open('http://gracillariidae.net/species/show/2527','null');" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='More information on species/taxon'>
                      <img class='result-icon-image' src='images//information_grijs.png'>
                    </div>
                    <div class='result-icon no-content' id='tog-v-1400'></div>
                    <div class='result-icon no-content'></div>
                  </div>
                </div>
                <div class='result' id='res-v-1386'>
                  <div class='result-result'>
                    <div class='result-image-container'>
                      <a href='images//Ph_tenerella_CD12632_female2.jpg' ptitle='female%20dark%20form%3Cbr%20/%3E%3Ci%3EPhyllonorycter%20tenerella%3C/i%3E' rel='prettyPhoto[gallery]' title=''>
                        <img class='result-image' src='images//Ph_tenerella_CD12632_female2.jpg'>
                      </a>
                    </div>
                    <div class='result-labels'>
                      <span class='result-name-scientific'>Phyllonorycter tenerella</span>
                      <span class='result-name-common'>
                        female dark form
                      </span>
                    </div>
                  </div>
                  <div class='result-icons'>
                    <div class='result-icon' onClick="window.open('http://gracillariidae.net/species/show/2500','null');" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='More information on species/taxon'>
                      <img class='result-icon-image' src='images//information_grijs.png'>
                    </div>
                    <div class='result-icon no-content' id='tog-v-1386'></div>
                    <div class='result-icon no-content'></div>
                  </div>
                </div>
                <div class='result' id='res-v-1385'>
                  <div class='result-result'>
                    <div class='result-image-container'>
                      <a href='images//Ph_tenerella_CD12632_8II2013.jpg' ptitle='female%20light%20form%3Cbr%20/%3E%3Ci%3EPhyllonorycter%20tenerella%3C/i%3E' rel='prettyPhoto[gallery]' title=''>
                        <img class='result-image' src='images//Ph_tenerella_CD12632_8II2013.jpg'>
                      </a>
                    </div>
                    <div class='result-labels'>
                      <span class='result-name-scientific'>Phyllonorycter tenerella</span>
                      <span class='result-name-common'>
                        female light form
                      </span>
                    </div>
                  </div>
                  <div class='result-icons'>
                    <div class='result-icon' onClick="window.open('http://gracillariidae.net/species/show/2500','null');" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='More information on species/taxon'>
                      <img class='result-icon-image' src='images//information_grijs.png'>
                    </div>
                    <div class='result-icon no-content' id='tog-v-1385'></div>
                    <div class='result-icon' onClick="nbcShowSimilar(1385,'v');return false;" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='similar species'>
                      <img class='result-icon-image icon-similar' src='images//gelijk_grijs.png'>
                    </div>
                  </div>
                </div>
                <div class='result' id='res-v-1399'>
                  <div class='result-result'>
                    <div class='result-image-container'>
                      <a href='images//Ph_lautella_RMNH7VIII1984JCKoster_male_2fasciae.jpg' ptitle='irmella%20form%3Cbr%20/%3E%3Ci%3EPhyllonorycter%20lautella%3C/i%3E' rel='prettyPhoto[gallery]' title=''>
                        <img class='result-image' src='images//Ph_lautella_RMNH7VIII1984JCKoster_male_2fasciae.jpg'>
                      </a>
                    </div>
                    <div class='result-labels'>
                      <span class='result-name-scientific'>Phyllonorycter lautella</span>
                      <span class='result-name-common'>
                        irmella form
                      </span>
                    </div>
                  </div>
                  <div class='result-icons'>
                    <div class='result-icon' onClick="window.open('http://gracillariidae.net/species/show/2330','null');" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='More information on species/taxon'>
                      <img class='result-icon-image' src='images//information_grijs.png'>
                    </div>
                    <div class='result-icon no-content' id='tog-v-1399'></div>
                    <div class='result-icon no-content'></div>
                  </div>
                </div>
                <div class='result' id='res-v-1397'>
                  <div class='result-result'>
                    <div class='result-image-container'>
                      <a href='images//Ph_issikii_wintervorm.jpg' ptitle='late%20summer%20form%3Cbr%20/%3E%3Ci%3EPhyllonorycter%20issikii%3C/i%3E' rel='prettyPhoto[gallery]' title=''>
                        <img class='result-image' src='images//Ph_issikii_wintervorm.jpg'>
                      </a>
                    </div>
                    <div class='result-labels'>
                      <span class='result-name-scientific'>Phyllonorycter issikii</span>
                      <span class='result-name-common'>
                        late summer form
                      </span>
                    </div>
                  </div>
                  <div class='result-icons'>
                    <div class='result-icon' onClick="window.open('http://gracillariidae.net/species/show/2298','null');" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='More information on species/taxon'>
                      <img class='result-icon-image' src='images//information_grijs.png'>
                    </div>
                    <div class='result-icon no-content' id='tog-v-1397'></div>
                    <div class='result-icon no-content'></div>
                  </div>
                </div>
                <div class='result' id='res-v-1392'>
                  <div class='result-result'>
                    <div class='result-image-container'>
                      <a href='images//Ph_maestingella_CD12030.jpg' ptitle='light%20form%3Cbr%20/%3E%3Ci%3EPhyllonorycter%20maestingella%3C/i%3E' rel='prettyPhoto[gallery]' title=''>
                        <img class='result-image' src='images//Ph_maestingella_CD12030.jpg'>
                      </a>
                    </div>
                    <div class='result-labels'>
                      <span class='result-name-scientific'>Phyllonorycter maestingella</span>
                      <span class='result-name-common'>
                        light form
                      </span>
                    </div>
                  </div>
                  <div class='result-icons'>
                    <div class='result-icon' onClick="window.open('http://gracillariidae.net/species/show/2359','null');" onmouseout='nbcSwitchImagename(this)' onmouseover='nbcSwitchImagename(this,1)' title='More information on species/taxon'>
                      <img class='result-icon-image' src='images//information_grijs.png'>
                    </div>
                    <div class='result-icon no-content' id='tog-v-1392'></div>
                    <div class='result-icon no-content'></div>
                  </div>
                </div>
              </div>
            </div>
            <div class='footerPagination noline' id='footerPagination'>
              <!-- / *** changes*** removed ul -->
              <input class='ui-button' id='show-more-button' onclick='nbcPrintResults();return false;' type='button' value='show more results'>
            </div>
          </div>
        </div>
      </div>
      <div id='row-sender'>
        <a href='http:/www.naturalis.nl'>
          <img src='images/logo-naturalis.png'>
          <!-- ***CHANGES*** Added ROW-SENDER -->
        </a>
      </div>
    </div>
    <div class='navbar navbar-inverse' id='bottombar'>
      <!-- ***CHANGES***: Changed element and name from <footer> to <div id="bottombar" -->
      <div class='navbar-container'>
        <p class='navbar-text navbar-left'>
          &copy; Naturalis 2005 - 2013  -
          <a href='http://www.nederlandsesoorten.nl/nsr/nsr/colofon.html' title='Disclaimer'>Colofon &amp; Disclaimer</a>
        </p>
        <a class='up' href='#top'>up</a>
        <p class='navbar-text navbar-right' id='lng'>
          Powered by
          <a href='#'>
            <img src='images/lng.png'>
            Linnaeus NG
          </a>
        </p>
      </div>
    </div>
{include file="dialog.tpl"}
  </body>


{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

nbcImageRoot='{$nbcImageRoot}';
baseUrlProjectImages='{$projectUrls.projectMedia}';
nbcBrowseStyle='{$nbcBrowseStyle}';
matrixId={$matrix.id};
projectId={$projectId};
nbcUseEmergingCharacters={$matrix_use_emerging_characters};

{literal}
if (typeof nbcInit=='function') {
	nbcInit();
}
{/literal}
{if $nbcFullDatasetCount}nbcFullDatasetCount={$nbcFullDatasetCount};
{/if}
{if $nbcStart}nbcStart={$nbcStart};
{/if}
{if $nbcPerPage}nbcPerPage={$nbcPerPage};
{/if}
{if $nbcPerLine}nbcPerLine={$nbcPerLine};
{/if}
{if $nbcSimilar}
nbcShowSimilar({$nbcSimilar[0]},'{$nbcSimilar[1]}');
{else}
{if 1==2 && $taxaJSON}
{literal}
try {{/literal}
	nbcData = $.parseJSON('{$taxaJSON}');
	nbcFilterEmergingCharacters();
	nbcDoResults({literal}{resetStart:false}{/literal});
	nbcDoOverhead();
	nbcDoPaging();
	nbcRefreshGroupMenu();
{literal}} catch(err){
	nbcGetResults();
}
{/literal}
{else}{literal}
nbcGetResults({refreshGroups:true});
{/literal}{/if}
{/if}

{literal}
});
</script>
{/literal}

</html>
