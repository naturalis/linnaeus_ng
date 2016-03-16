{include file="../shared/header.tpl"}

<div id="header-titles">
	<span id="header-title" style="white-space: nowrap;">{t}Literature{/t}</span>
</div>

<div id="page-main" class="template-index">

	<p>
    {t}Browse by title:{/t}<br />
    {foreach from=$titleAlphabet item=v}
    <a href="#" class="click-letter">{$v.letter|@strtoupper}</a>
    {/foreach}
    </p>

	<p>
    {t}Browse by author:{/t}<br />
    {foreach from=$authorAlphabet item=v}
    {if $v.letter}
    <a href="#" class="click-letter">{$v.letter|@strtoupper}</a>
    {/if}
    {/foreach}
	</p>

</div>

{include file="../shared/footer.tpl"}
