<div id="panel">
	<div id="categories">
	<p>
	{literal}
		<a href="#" onclick="$('.taxon-links').each(function(){$(this).html($(this).attr('name_sci'));});">wetenschappelijke namen</a>
		<a href="#" onclick="$('.taxon-links').each(function(){$(this).html($(this).attr('name_common').length>0 ? $(this).attr('name_common') : $(this).attr('name_sci'));});">gewone namen</a>
	{/literal}
	</p>
	<ul>
		<li><a id="rLi" href="javascript:showRemaining();" class="category-first{if $taxaState=='remaining' || $excluded|@count==0} category-active{/if}">{t}Remaining{/t}</a></li>
		<li><a id="eLi" {if $excluded|@count>0}href="javascript:showExcluded();"{/if} class="category-last{if $taxaState=='excluded'} category-active{/if}{if $excluded|@count==0} category-no-content{/if}">{t}Excluded{/t}</a></li>
	</ul>
	</div>

	<div id="taxa">
	
		<div id="remaining" style="display: {if $taxaState=='remaining' || $excluded|@count==0}block{else}none{/if};">
		{if $remaining|@count==1}{assign var=w value=taxon}{else}{assign var=w value=taxa}{/if}
		<p id="header">{t _s1=$remaining|@count _s2=$w}%s possible %s remaining:{/t}</p>
	
		<ul id="ul-remaining">
		{foreach from=$remaining key=k item=v}
			<li><a class="taxon-links" href="../species/taxon.php?id={$v.id}" name_common="{$v.commonname|@escape}" name_sci="{$v.taxon|@escape}">{$v.taxon}</a></li>
		{/foreach}
		</ul>
	
		</div>
	
		<div id="excluded" style="display: {if $taxaState=='remaining' || $excluded|@count==0}none{else}block{/if};">
		{if $excluded|@count==1}{assign var=w value=taxon}{else}{assign var=w value=taxa}{/if}
		<p id="header">{t _s1=$excluded|@count _s2=$w}%s %s excluded:{/t}</p>

		<ul id="ul-excluded">
		{foreach from=$excluded key=k item=v}
			<li><a class="taxon-links" href="../species/taxon.php?id={$v.id}" name_common="{$v.commonname|@escape}" name_sci="{$v.taxon|@escape}">{$v.taxon}</a></li>
		{/foreach}
		</ul>
		</div>
	
	</div>

</div>

