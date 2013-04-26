{include file="../shared/admin-header.tpl"}

<div id="page-main">

    <p>
        {if $isHigherTaxa}
            {t _s1='<a href="index.php">' _s2='</a>'}Below are all taxa in your project that are part of the higher taxa. All lower taxa can be found in the %sspecies module%s.{/t}
        {else}
            {t _s1='<a href="../highertaxa/">' _s2='</a>'}Below are all taxa in your project that are part of the species module. All higher taxa can be found in the %shigher taxa module%s.{/t}
        {/if}
        <br />
        {t}To edit a name, rank or parent, click the taxon's name. To edit a taxon's pages, click the percentage-indicator for that taxon in the 'content' column. To edit media files, synoyms or common names, click the cell in the corresponding column.{/t}<br />
        {if $isHigherTaxa}
            {t}Please note that you can only delete taxa that have no children, in order to maintain a correct taxon structure in the species module.{/t}<br />
        {/if}

		<a href="#" onclick="taxonSortTaxaAlpha()">Permanently sort alphabetically</a>

    </p>
    
	<table>
        <tr style="vertical-align:bottom">
            <th style="width:240px;">{t}Taxon{/t}</th>
	    {if $session.admin.project.includes_hybrids==1}
    		<th style="width:25px;">{t}Hybrid{/t}</th>
		{/if}
            <th style="width:50px;text-align:right;">{t}Content{/t}</th>
    	{if !$isHigherTaxa}
            <th style="width:50px;" title="{t}images, videos, soundfiles{/t}">{t}Media{/t}</th>
	    {/if}
            <th style="width:60px;">{t}Literature{/t}</th>
            <th style="width:60px;">{t}Synonyms{/t}</th>
            <th style="width:90px;">{t}Common names{/t}</th>
            <th style="width:20px;text-align:center">{t}Delete{/t}</th>
            <th>{t}Is being edited by:{/t}</th>
        </tr>


	{assign var=prev_rank value=-1}
	{assign var=firstlevel value=-1}
	{foreach from=$taxa item=taxon key=k}
	{if (!$isHigherTaxa && $taxon.lower_taxon==1) || ($isHigherTaxa && $taxon.lower_taxon==0)}
		<tr>
			<td>
				{if $firstlevel==-1}{assign var=firstlevel value=$taxon.level}{/if}
				{assign var=dots value=$taxon.level-$firstlevel}
				{if $dots<0}{assign var=dots value=0}{/if}
				{'.'|str_repeat:$dots}

            	<a href="edit.php?id={$taxon.id}">
            	{$taxon.taxon_formatted}
                </a>
			</td>
			{if $taxon.is_hybrid>=0}
    		<td>
				{if $taxon.is_hybrid==1}<span class="taxon-hybrid-x">x</span>{/if}
			</td>
			{/if}
	
			<td style="text-align:right;">
				<a href="taxon.php?id={$taxon.id}">{$taxon.pctFinished}%</a>
			</td>
			{if !$isHigherTaxa}
			<td title="{t}media files{/t}">
				<a href="media.php?id={$taxon.id}">{$taxon.mediaCount} {$taxon.mediaCount_label}</a>
			</td>
			{/if}
			<td>
				<span class="a" onclick="window.open('literature.php?id={$taxon.id}','_self');">{$taxon.literatureCount} refs.</span>
			</td>
			<td>
				<span class="a" onclick="window.open('synonyms.php?id={$taxon.id}','_self');">{$taxon.synonymCount} syn.</span>
			</td>
			<td>
				<span class="a" onclick="window.open('common.php?id={$taxon.id}','_self');">{$taxon.commonnameCount} {$taxon.literatureCount_label}</span>
			</td>
			<td
				class="a" 
				style="text-align:center" 
				onclick="taxonDeleteData({$taxon.id},'{$taxon.taxon}');">
				x
			</td>
			<td id="usage-{$taxon.id}"></td>
        </tr>
	{/if}
    {/foreach}
    </table>


</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
