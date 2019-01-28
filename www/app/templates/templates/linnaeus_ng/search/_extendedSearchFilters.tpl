<p class="filterSearchText">
  {t}Filter query{/t}
</p>
<form method="get" action="" id="formSearchFacetsSpecies" name="formSearchFacetsSpecies">
  <div>
     <input type="hidden" id="group_id" name="group_id" value="{$search.group_id}" />
     {*<input type="hidden" id="author_id" name="author_id" value="{$search.author_id}" />*}
     <div{if $search.display=='plain'} style="display:none;"{/if}>
     <fieldset class="block">
        
        {*
        <div class="formrow">
           <label accesskey="g" for="author">{t}Author{/t}</label>
           <input type="text" size="60" class="field" id="author" name="author" autocomplete="off" value="{$search.author}">
           <div id="author_suggestion" match="like" class="auto_complete" style="display:none;"></div>
        </div>
        *}
     </fieldset>
     
     <fieldset class="selectable-parameters">

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
              <a href="{$t.help_link_url}" target="_blank"  title="{t}click for help{/t}" class="help">{t}Additional information{/t}</a>
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
            {if $t.all_link_text}{$t.all_link_text}{else}{t _s1=$t.name}Show all taxa with %s{/t}{/if}
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