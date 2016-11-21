		{if $activeCategory.tabname=='CTAB_TAXON_LIST'}

			{include file="_tab_taxon_list.tpl"}
1
		{elseif $activeCategory.tabname=='CTAB_CLASSIFICATION'}
2
			{include file="_tab_classificatie.tpl"}
3
		{elseif $activeCategory.tabname=='CTAB_DNA_BARCODES'}
 4       
			{include file="_tab_dna_barcodes.tpl"}
5
		{elseif $activeCategory.tabname=='CTAB_DICH_KEY_LINKS'}
6
			{include file="_tab_dich_key_links.tpl"}
7
		{elseif $activeCategory.tabname=='CTAB_LITERATURE'}
8		
			{include file="_tab_literatuur.tpl"}
9
		{elseif $activeCategory.tabname=='CTAB_MEDIA'}
0
			{include file="_tab_media.tpl"}
10			
		{elseif $activeCategory.tabname=='CTAB_NAMES'}
11					
			{include file="_tab_names.tpl"}
12
		{elseif $activeCategory.tabname=='CTAB_PRESENCE_STATUS'}
13					
			{include file="_tab_voorkomen.tpl"}

		{elseif $activeCategory.tabname=='CTAB_PRESENCE_STATUS'}
14					
			{include file="_tab_voorkomen.tpl"}
15
		{elseif $activeCategory.tabname=='TAB_VERSPREIDING'}
16
			{include file="_tab_verspreiding.tpl"}
17
		{elseif $ext_template}
18		
			{include file=$ext_template}
19
		{elseif $external_content && $external_content->template}
  20      
			{include file=$external_content->template}
21
		{elseif $external_content}
  22      
			{include file='_webservice.tpl'}

		{else}
    23    
			{if $content|@is_array}
			<ul>
				{foreach from=$content item=v key=k}
				{if $k>0}<li><a href="nsr_taxon.php?id={$v.id}">{$v.label}</a></li>{/if}
				{/foreach}
			</ul>
            {elseif !empty($content)}
            deze?
			
				{$content}
			
			{/if}

		{/if}

		{if $rdf}

			{include file="_rdf_data.tpl"}

		{/if}