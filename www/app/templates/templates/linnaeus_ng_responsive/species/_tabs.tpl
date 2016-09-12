		{if $activeCategory.tabname=='CTAB_TAXON_LIST'}

			{include file="_tab_taxon_list.tpl"}

		{elseif $activeCategory.tabname=='CTAB_CLASSIFICATION'}

			{include file="_tab_classificatie.tpl"}

		{elseif $activeCategory.tabname=='CTAB_DNA_BARCODES'}
        
			{include file="_tab_dna_barcodes.tpl"}

		{elseif $activeCategory.tabname=='CTAB_DICH_KEY_LINKS'}

			{include file="_tab_dich_key_links.tpl"}

		{elseif $activeCategory.tabname=='CTAB_LITERATURE'}
		
			{include file="_tab_literatuur.tpl"}

		{elseif $activeCategory.tabname=='CTAB_MEDIA'}

			{include file="_tab_media.tpl"}
			
		{elseif $activeCategory.tabname=='CTAB_NAMES'}
					
			{include file="_tab_names.tpl"}

		{elseif $activeCategory.tabname=='CTAB_PRESENCE_STATUS'}
					
			{include file="_tab_voorkomen.tpl"}

		{elseif $activeCategory.tabname=='CTAB_PRESENCE_STATUS'}
					
			{include file="_tab_voorkomen.tpl"}

		{elseif $activeCategory.tabname=='TAB_VERSPREIDING'}

			{include file="_tab_verspreiding.tpl"}

		{elseif $ext_template}
		
			{include file=$ext_template}

		{elseif $external_content && $external_content->template}
        
			{include file=$external_content->template}

		{elseif $external_content}
        
			{include file='_webservice.tpl'}

		{else}
        
			{if $content|@is_array}
			<ul>
				{foreach from=$content item=v key=k}
				{if $k>0}<li><a href="nsr_taxon.php?id={$v.id}">{$v.label}</a></li>{/if}
				{/foreach}
			</ul>
            {elseif !empty($content)}
			<p>
				{$content}
			</p>
			{/if}

		{/if}

		{if $rdf}

			{include file="_rdf_data.tpl"}

		{/if}