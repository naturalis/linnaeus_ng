{include file="../shared/admin-header.tpl"}

<style>
.hidden-tab, .tab-redirect-label {
	color:#999;
}
.tab-redirect {
	font-size:0.9em;
}
</style>


<div id="page-main">
	<form method="post">
    <input type="hidden" name="rnd" value="{$rnd}" />
    <input type="hidden" name="action" value="save" />
	<p>
        {t _s1=$maxCategories _s2=$pages[0].page}Each taxon page consists of one or more categories, with a maximum of %s. The first category, '%s', is mandatory.{/t}<br />
        {t}Below, you can specify the correct label of each category in the language or languages defined in your project. On the left hand side, the labels in the default language are displayed. On the right hand side, the labels in the other languages are displayed. These are shown a language at a time; you can switch between languages by clicking its name at the top of the column. The current active language is shown underlined.{/t}<br />
        {t}Text you enter is automatically saved when you leave the input field.{/t}
	</p>
	<p>
		<table>
			<tr>
                <th>{t}category{/t}</th>
                {foreach from=$languages item=v}
				<td>{$v.language} *</td>
                {/foreach}
                <td></td>
                <td>{t}attributes{/t}</td>
			</tr>

            {foreach $pages v}
            
            <tr class="tr-highlight">
                <td>
                    <span class="{if $v.always_hide==1}hidden-tab{/if}" title="{if $v.always_hide==1}hidden tab{/if}">{$v.page}</span>
                </td>
                {foreach $languages l}
                <td>
                    <input 
                        type="text" 
                        id="other-{$v.id}" 
                        maxlength="64" 
                        name="pages_taxa_titles[{$v.id}][{$l.language_id}]"
                        value="{$v.page_titles[$l.language_id]}" />
                </td>
                {/foreach}

                <td class="cell-page-delete" onclick="taxonPageDelete({$v.id},'{$v.page}');"></td>
                <td>
                	{if $v.always_hide==1}{t}always hidden{/t} (id={$v.id}){/if}
                	{if $v.external_reference && $v.always_hide==1}; {/if}
                	{if $v.external_reference}{t}external reference{/t}{/if}
                	<a class="edit" href="tab.php?id={$v.id}">{t}edit{/t}</a>
                </td>
            </tr>

            {/foreach}
        </table>
        <input type="submit" value="{t}save{/t}" />
		</form>

	</p>
	<p>
        {if $languages|@count==0}
	        {t}You have to define at least one language in your project before you can add any categories.{/t} <a href="../projects/data.php">{t}Define languages now.{/t}</a>
        {else}
        <form method="post" action="" id="theForm">
            {if $pages|@count<$maxCategories}
            {t}Add a new category:{/t} 
            <input type="text" maxlength="32" id="new_page" name="new_page" value="" />
            <input type="hidden" name="rnd" value="{$rnd}" />
            <input type="hidden" name="show_order" value="{$nextShowOrder}" />
            <input type="submit" value="{t}add{/t}" />
            {/if}
		</form>
	</p>
{/if}

</div>

<script type="text/javascript">
$(document).ready(function()
{
	allActiveView = 'page';
	{foreach from=$languages item=v}
	allAddLanguage([{$v.language_id},'{$v.language}',{if $v.def_language=='1'}1{else}0{/if}]);
	{/foreach}
	allActiveLanguage = {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
	allDrawLanguages();
	taxonGetPageLabels(allDefaultLanguage);
	taxonGetPageLabels(allActiveLanguage);
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}