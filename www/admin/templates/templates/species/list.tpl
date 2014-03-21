{include file="../shared/admin-header.tpl"}

<div id="page-main">

    <p>
        {t}To edit a name, rank or parent, click the taxon's name. To edit a taxon's pages, click the percentage-indicator for that taxon in the 'content' column. To edit media files, synoyms or common names, click the cell in the corresponding column.{/t}<br />
        {if $isHigherTaxa}
            {t}Please note that you can only delete taxa that have no children, in order to maintain a correct taxon structure in the species module.{/t}<br />
        {/if}

		<a href="#" onclick="$('tr[isHigher=1]').toggle();allSetSomething('list-show-higher',$('tr[isHigher=1]:first').css('display')!='none' ? 'on' : 'off');">Toggle higher taxa</a> | 
		<a href="#" onclick="$('[class=indent-dots]').toggle();allSetSomething('list-show-indent',$('[class=indent-dots]:first').css('display')!='none' ? 'on' : 'off');">Toggle indentation</a>
    </p>
    
<!-- table id="drag-list" class="grid" -->
<table>
	<thead>
        <tr>
            <th style="width:270px;text-align:left">{t}Taxon{/t}</th>
            <th style="width:50px;text-align:right;">{t}Content{/t}</th>
            <th style="width:50px;" title="{t}images, videos, soundfiles{/t}">{t}Media{/t}</th>
            <th style="width:60px;">{t}Literature{/t}</th>
            <th style="width:60px;">{t}Synonyms{/t}</th>
            <th style="width:90px;">{t}Common names{/t}</th>
            <th>{t}Is being edited by{/t}</th>
        </tr>
	</thead>
	<tbody>
	{foreach from=$taxa item=taxon key=k}
	    {if ($isHigherTaxa==1 && $taxon.lower_taxon==0) || !$isHigherTaxa}
		<tr class="tr-highlight" {if $taxon.lower_taxon==0 && !$isHigherTaxa} style="display:none" isHigher="1"{/if} type="drag-row" drag-id="{$taxon.id}">
			<td style="text-align:left;cursor:move">
	            <span class="indent-dots" style="display:none">{' . '|str_repeat:$taxon.depth}</span>
            	<a href="edit.php?id={$taxon.id}">{$taxon.taxon_formatted}</a>
                {if $session.admin.project.includes_hybrids==1}
                    {if $taxon.is_hybrid==1}<span class="taxon-hybrid-x">x</span>{/if}
                {/if}
			</td>
			<td style="text-align:right">
				<a href="taxon.php?id={$taxon.id}">{if $contentCount[$taxon.id].pctFinished}{$contentCount[$taxon.id].pctFinished}{else}0{/if}%</a>
			</td>
			<td ondblclick="window.open('media.php?id={$taxon.id}','_self');">
				<a type="media" href="media.php?id={$taxon.id}">{if $mediaCount[$taxon.id]}{$mediaCount[$taxon.id]}{else}0{/if}</a>
			</td>
			<td ondblclick="window.open('literature.php?id={$taxon.id}','_self');">
				<a type="literature" href="literature.php?id={$taxon.id}">{if $literatureCount[$taxon.id]}{$literatureCount[$taxon.id]}{else}0{/if}</a>
			</td>
			<td ondblclick="window.open('synonyms.php?id={$taxon.id}','_self');">
				<a type="synonyms" href="synonyms.php?id={$taxon.id}">{if $synonymsCount[$taxon.id]}{$synonymsCount[$taxon.id]}{else}0{/if}</a>
			</td>
			<td ondblclick="window.open('common.php?id={$taxon.id}','_self');">
            	<a type="common" href="common.php?id={$taxon.id}">{if $commonnameCount[$taxon.id]}{$commonnameCount[$taxon.id]}{else}0{/if}</a>
			</td>
			{*<td class="a" style="text-align:center;" onclick="taxonDeleteData({$taxon.id},'{$taxon.taxon}');">
				x
			</td>*}
			<td id="usage-{$taxon.id}"></td>
        </tr>
        {/if}
    {/foreach}
	</tbody>
    </table>
	<p>
        <form method="post" action="sort_taxa.php" id="theForm">
        <input type="hidden" name="rnd" value="{$rnd}" />
        <input type="hidden" name="taxatype" value="{if $isHigherTaxa}Ht{else}Sp{/if}" />
        <input type="button" value="save taxon order" onclick="allSaveDragOrder()"/>
        </form>
    </p>
    <p>
    	<a href="javascript:taxonSortTaxaAlpha();">Sort taxa alphabetically (this list only)</a><br />
    	<a href="javascript:taxonSortTaxaAlpha(true);">Sort taxa alphabetically (all taxa)</a><br />
    	<a href="javascript:taxonSortTaxaTaxonomic();">Sort taxa alphabetically per taxonomic level</a> (affects both species and higher taxa)
    </p>

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){

	$('a[type="media"]').each(function(i){$(this).html($(this).html()+' file'+($(this).html()!=1 ? 's' : ''))});
	$('a[type="literature"]').each(function(i){$(this).html($(this).html()+' ref'+($(this).html()!=1 ? 's' : '')+'.')});
	$('a[type="synonyms"]').each(function(i){$(this).html($(this).html()+' syn'+($(this).html()!=1 ? 's' : '')+'.')});
	$('a[type="common"]').each(function(i){$(this).html($(this).html()+' name'+($(this).html()!=1 ? 's' : ''))});
	
	allGetSomething('list-show-higher',function(state){$('tr[isHigher=1]').toggle(state=='on');});
	allGetSomething('list-show-indent',function(state){$('[class=indent-dots]').toggle(state=='on');});		
	allGetSomething('list-scroll-pos',function(scroll){$('html').scrollTop(scroll)});		
	
})
</script>
{/literal}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
