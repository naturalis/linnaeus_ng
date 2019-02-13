<a class="filterSearchText">
  {t}Filter zoekopdracht{/t}
</a>
<form method="get" action="" id="formSearchFacetsSpecies" name="formSearchFacetsSpecies">
  <div>
     <input type="hidden" id="group_id" name="group_id" value="{$search.group_id}" />
     {*<input type="hidden" id="author_id" name="author_id" value="{$search.author_id}" />*}
     <div{if $search.display=='plain'} style="display:none;"{/if}>
     <fieldset class="block">
        
        {*
        <div class="formrow">
           <label accesskey="g" for="author">{t}Auteur{/t}</label>
           <input type="text" size="60" class="field" id="author" name="author" autocomplete="off" value="{$search.author}">
           <div id="author_suggestion" match="like" class="auto_complete" style="display:none;"></div>
        </div>
        *}
     </fieldset>
     
     <fieldset class="selectable-parameters">

        {if $search_filter_presence}
        <div class="formrow">
           <label
              for="presenceStatusList" 
              panel="presence-options-panel"
              class="clickable">
              <strong>{t}Status voorkomen{/t}</strong>
              <i class="ion-chevron-down down"></i>
              <i class="ion-chevron-up up"></i>
           </label>
            
            <ul id="presence-options-panel" class="selectableParametersPanel">
	            <li>
                    <!-- {if $search_presence_help_url}
                    <a href="{$search_presence_help_url}" target="_blank"  title="{t}klik voor help over dit onderdeel{/t}" class="help">Meer informatie</a>
                    {/if} -->
                    <a href="http://www.nederlandsesoorten.nl/content/voorkomen" target="_blank" class="help">{t}Meer informatie{/t}</a>
    	        </li>
                <li>
                    <select class="customSelect" id="presenceStatusList" name="presenceStatusList" onchange="addSearchParameter('presenceStatusList');">
                        <option value="">{t}maak een keuze{/t}</option>
                        {foreach from=$presence_statuses item=v}
                        <option id="established{$v.id}" value="presence[{$v.id}]" established="{$v.established}">
                        {$v.index_label}
                        {$v.information_short}
                        </option>
                        {/foreach}
                    </select>
                </li>
            </ul>
           <p class="options-panel" id="" style="display:none">
              
           <a href="#" onclick="addEstablished();submitSearchParams();return false;">{t}gevestigde soorten{/t}</a> / 
           <a href="#" onclick="addNonEstablished();submitSearchParams();return false;">{t}niet gevestigde soorten{/t}</a>
           </p>
        </div>
        {/if}


        {if $search_filter_multimedia}
        <div class="formrow">
           <label
              for="multimedia-options" 
              panel="multimedia-options-panel"
              class="clickable">
              <strong>{t}Multimedia{/t}</strong>
              <i class="ion-chevron-down down"></i>
              <i class="ion-chevron-up up"></i>
           </label>
           <ul id="multimedia-options-panel" class="selectableParametersPanel">
           	<li>
              <label for="multimedia-images" class="normalLabel">Foto</label>
           		<select class="customSelect" id="multimedia-images" onchange="addSearchParameter('multimedia-images');">
								<option value="">{t}Foto{/t}</option>
								<option value="images_on">{t}met foto{/t}</option>
								<option value="images_off">{t}zonder foto{/t}</option>
              </select>
           	</li>
            {if $show_nsr_specific_stuff}
           	<li>
              <label for="multimedia-distribution" class="normalLabel">{t}Verspreidingskaart{/t}</label>
           		<select class="customSelect" id="multimedia-distribution" onchange="addSearchParameter('multimedia-distribution');">
								<option value="">{t}Verspreidingskaart{/t}</option>
								<option value="distribution_on">{t}met verspreidingskaart{/t}</option>
								<option value="distribution_off">{t}zonder verspreidingskaart{/t}</option>
              </select>
           	</li>
           	<li>
              <label for="multimedia-trend" class="normalLabel">{t}Trendgrafiek{/t}</label>
           		<select class="customSelect" id="multimedia-trend" onchange="addSearchParameter('multimedia-trend');">
                <option value="">{t}Trendgrafiek{/t}</option>
                <option value="trend_on">{t}met trendgrafiek{/t}</option>
                <option value="trend_off">{t}zonder trendgrafiek{/t}</option>
              </select>
           	</li>
            {/if}
           </ul>
        </div>
        {/if}
        
        {if $search_filter_dna_barcodes}
        <div class="formrow">
           <label 
              for="dna-options"
              panel="dna-options-panel"
              class="clickable">
              <strong>{t}DNA barcoding{/t}</strong>
              <i class="ion-chevron-down down"></i>
              <i class="ion-chevron-up up"></i>
           </label>
           <ul id="dna-options-panel" class="selectableParametersPanel">
            <li>
              <a href="http://www.nederlandsesoorten.nl/content/dna-barcoding" target="_blank" title="{t}klik voor help over dit onderdeel{/t}" class="help">{t}Meer informatie{/t}</a>
            </li>
           	<li>
           		<select class="customSelect" id="dna-options" name="dna-options" onchange="addSearchParameter('dna-options');">
                 <option value="">{t}maak een keuze{/t}</option>
                 <option value="dna">{t}met een of meer exemplaren verzameld{/t}</option>
                 <option value="dna_insuff">{t}minder dan drie exemplaren verzameld{/t}</option>
              </select>
           	</li>
           </ul>
        </div>
        {/if}


      {foreach from=$traits item=t key=k1}
      <div class="formrow">
        <label class="clickable trait-panel-header" panel="traits{$k1}-options">
          <strong>{$t.name}</strong>
          <i class="ion-chevron-down down"></i>
          <i class="ion-chevron-up up"></i>
        </label>
        <ul id="traits{$k1}-options" class="selectableParametersPanel">
          {if $t.help_link_url}
            <li>
              <a href="{$t.help_link_url}" target="_blank"  title="{t}klik voor help over dit onderdeel{/t}" class="help">{t}Meer informatie{/t}</a>
              </li>
          {/if}
          
            
          
        {foreach from=$t.data item=d key=k2}
          {if $d.type_sysname!=='stringfree'}
          <li>                 
            {if $d.type_allow_values==1 && $d.value_count>0}
            <label for="trait-{$k1}{$k2}" class="normalLabel">{$d.name}</label>
            <select class="customSelect" trait-id="{$d.id}" id="trait-{$k1}{$k2}" onchange="addSearchParameter('trait-{$k1}{$k2}');">
              <option value="">{$d.name}</option>
              {foreach from=$d.values item=v}
              <option value="{$v.id}">{$v.string_value}</option>
              {/foreach}
            </select>
            <input type="hidden" value=" > " trait-id="{$d.id}" class="add-trait" onclick="addSearchParameter('trait-{$k1}{$k2}');" />
            {else if $d.type_allow_values==0}
            <label for="trait-{$k1}{$k2}" class="selectLabel">{$d.name}</label>
            <select class="operator customSelect" trait-id="{$d.id}" id="operator-{$k1}{$k2}" onchange="$('#trait-{$k1}{$k2}-2').toggle($('option:selected',this).attr('range')==1);">
              {foreach from=$operators item=v key=k}
              <option value="{$k}"{if $v.range} range="1"{/if}>{t}{$v.label}{/t}</option>
              {/foreach}
            </select>

              <!--
            <div class="openValue">
              <input type="text" id="trait-{$k1}{$k2}" trait-id="{$d.id}" placeholder="{$d.date_format_format_hr}" maxlength="{$d.date_format_format_hr|@strlen}" />
              <input id="trait-{$k1}{$k2}-2" type="text" trait-id="{$d.id}" second-value="1" placeholder="{$d.date_format_format_hr}" maxlength="{$d.date_format_format_hr|@strlen}" style="display:none;" />
              <input type="button" value=" > " trait-id="{$d.id}" class="add-trait" onclick="addSearchParameter('trait-{$k1}{$k2}');" />  
            </div>
               -->

                <div class="openValue">
                    {assign var="maxlength" value=$d.date_format_format_hr|@strlen}
                    {if $maxlength == 0 && $d.maxlength != ''}
                        {$maxlength = $d.maxlength}
                    {else}
                        {$maxlength = 10}
                    {/if}
                    <input type="text" id="trait-{$k1}{$k2}" trait-id="{$d.id}" placeholder="{$d.date_format_format_hr}" maxlength="{$maxlength}" />
                    <input id="trait-{$k1}{$k2}-2" type="text" trait-id="{$d.id}" second-value="1" placeholder="{$d.date_format_format_hr}" maxlength="{$maxlength}" style="display:none;" />
                    <input type="button" value=" > " trait-id="{$d.id}" class="add-trait" onclick="addSearchParameter('trait-{$k1}{$k2}');" />
                </div>



            {/if}
            </li>
          {/if}         
        {/foreach}
        {if $t.show_show_all_link}
          <li>
            <a href="#" onclick="setTraitGroup({$t.group_id});submitSearchParams();return;">
            {if $t.all_link_text}{$t.all_link_text}{else}{t _s1=$t.name}Alle taxa met %s tonen{/t}{/if}
            </a>
          </li>
        {/if}
        </ul>        
      </div>
      {/foreach}



        <div class="formrow selected-parameters" style="display:none">
           <label class="clickable">{t}Selected traits{/t}</label>
           <span id="remove-all" style="display:none">
              <a href="#" class="removeAllParams" onclick="removeAllSearchParameters();submitSearchParams();return;">{t}remove all{/t}</a>
              <!-- a href="nsr_search_extended.php">{t}remove all{/t}</a -->
           </span>
           <ul id="search-parameters">
           </ul>
        </div>
        {*
        <div class="formrow">
           <input type="button=" class="zoekknop" value="zoek" onclick="submitSearchParams()" />
        </div>
        *}
     </fieldset>
  </div>
  </div>
</form>