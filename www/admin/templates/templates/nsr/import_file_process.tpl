{include file="../shared/admin-header.tpl"}

<style>
.small {
	color:#666;
}

ul {
   list-style: none;
   margin-left: 0;
   padding-left: 1em;
   text-indent: -1em;
}
</style>

<div id="page-main">

<h4>{t}Import results:{/t}</h4>

{if $lines}

<ul>
{foreach $lines v}
	<li>
    	{if $v.saved && $v.taxon_id}
        &#10004; <a href="taxon.php?id={$v.taxon_id}" target="_new">{$v[$importColumns['conceptName']]}</a>:
        <span class="messages">
        {foreach $v.import_messages m k}{if $k>0}, {/if}{$m}{/foreach}
        </span>
        {else}
		&#10008; {$v[$importColumns['conceptName']]}:
        <span class="messages">
        {foreach $v.import_messages m k}{if $k>0}, {/if}{$m}{/foreach}
        </span>
        {/if}
	</li>
{/foreach}
</ul>
{else}

no data

{/if}

<p>

	<a href="import_file_reset.php">{t}load a new file{/t}</a>

</p>

</div>

{include file="../shared/admin-footer.tpl"}
