{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
<p>
Below is a sample of the data parsed from the input file. Please verify that it looks okay, and click 'Create project'.
</p>
<p>
	Project title: "<b>{$title}</b>"<br />
    Soortgroep: {$soortgroep}<br />
</p>



<p>
<b>First five characters (of {$characters|@count}):</b><br />
{assign var=i value=0}
{foreach from=$characters item=v}
{if $i>1 && $i<7}
{$v.code}<br />
{/if}
{assign var=i value=$i+1}
{/foreach}
</p>

<p>
<b>First five species (of {$species|@count}) with some states:</b><br />
{assign var=i value=0}
{foreach from=$species item=v}
{if $i<5}
{$v.label}

(<i>{assign var=t value=0}
{foreach from=$v.states item=s}
{if $t<5}
{$s[0]}; 
{/if}
{assign var=t value=$t+1}
{/foreach}
...</i>)
<br />
{/if}
{assign var=i value=$i+1}
{/foreach}
</p>

<p>
	<form method="post" action="nbc_determinatie_3.php">
	<input type="hidden" name="rnd" value="{$rnd}" />
	<input type="submit" value="Create project">
	</form>
</p>
<p>
	<a href="nbc_determinatie_1.php">Back</a>
</p>

</div>

{include file="../shared/admin-footer.tpl"}