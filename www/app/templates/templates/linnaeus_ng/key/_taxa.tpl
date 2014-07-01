<style>
.name_switch {
	font-size:11px;
	font-weight:normal;
}
</style>

<div id="panel">
	<div id="categories">
	<ul>
		<li><a id="rLi" href="javascript:showRemaining();" class="category-first{if $taxaState=='remaining' || $excluded|@count==0} category-active{/if}">{t}Remaining{/t}</a></li>
		<li><a id="eLi" {if $excluded|@count>0}href="javascript:showExcluded();"{/if} class="category-last{if $taxaState=='excluded'} category-active{/if}{if $excluded|@count==0} category-no-content{/if}">{t}Excluded{/t}</a></li>
	</ul>
	</div>

	<div id="taxa">
	
		<div id="remaining" style="display: {if $taxaState=='remaining' || $excluded|@count==0}block{else}none{/if};">
		{if $remaining|@count==1}{assign var=w value=taxon}{else}{assign var=w value=taxa}{/if}
		<p id="header">
			{t _s1=$remaining|@count _s2=$w}%s possible %s remaining:{/t}<br />
			<a href="#" class="name_switch" data-type="name_sci" style="display:none" onclick="keyNameswitch(this)";>{t}show scientific names{/t}</a>
			<a href="#" class="name_switch" data-type="name_common" style="display:inline" onclick="keyNameswitch(this);">{t}show common names{/t}</a>
		</p>

		<ul id="ul-remaining">
		{foreach from=$remaining key=k item=v}
			<li><a class="taxon-links" href="../species/taxon.php?id={$v.id}" name_common="{$v.commonname|@escape}" name_sci="{$v.taxon|@escape}">{$v.taxon}</a></li>
		{/foreach}
		</ul>
	
		</div>
	
		<div id="excluded" style="display: {if $taxaState=='remaining' || $excluded|@count==0}none{else}block{/if};">
		{if $excluded|@count==1}{assign var=w value=taxon}{else}{assign var=w value=taxa}{/if}
		<p id="header">{t _s1=$excluded|@count _s2=$w}%s %s excluded:{/t}</p>

		<a href="#" class="name_switch" data-type="name_sci" style="display:none" oonclick="keyNameswitch('name_sci')">{t}show scientific names{/t}</a>
		<a href="#" class="name_switch" data-type="name_common" style="display:inline" onclick="keyNameswitch('name_common')">{t}show common names{/t}</a>

		<ul id="ul-excluded">
		{foreach from=$excluded key=k item=v}
			<li><a class="taxon-links" href="../species/taxon.php?id={$v.id}" name_common="{$v.commonname|@escape}" name_sci="{$v.taxon|@escape}">{$v.taxon}</a></li>
		{/foreach}
		</ul>
		</div>
	
	</div>

</div>

