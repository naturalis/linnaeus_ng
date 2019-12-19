{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<p>
        {t _s1=$maxCategories _s2=$pages[0].page}Each taxon page consists of one or more categories, with a maximum of %s. The first category, '%s', is mandatory.{/t}<br />
        {t}Below, you can specify the correct label of each category in the language or languages defined in your project. On the left hand side, the labels in the default language are displayed. On the right hand side, the labels in the other languages are displayed. These are shown a language at a time; you can switch between languages by clicking its name at the top of the column. The current active language is shown underlined.{/t}<br />
        {t}Text you enter is automatically saved when you leave the input field.{/t}
	</p>
	<p>
		<table>
			<tr>
                <th style="width:150px">{t}Category{/t}</th>
                {foreach from=$languages item=v}
                {if $v.def_language=='1'}
                    <td>{$v.language} *</td>
                {/if}
                {/foreach}
                {if $languages|@count > 1}
	                <td colspan="2" id="project-language-tabs">(languages)</td>
                {/if}
			</tr>

            {foreach from=$pages item=v}
            <tr class="tr-highlight">
                <td>
                    {$v.page}
                </td>
                <td>
                    <input 
                        type="text" 
                        id="default-{$v.id}" 
                        maxlength="64" 
                        onblur="taxonSavePageTitle({$v.id},this.value,'default')" />
                </td>
                {if $languages|@count > 1}
                <td>
                    <input 
                        type="text" 
                        id="other-{$v.id}" 
                        maxlength="64" 
                        onblur="taxonSavePageTitle({$v.id},this.value,'other')" />
                </td>
                {/if}
                <td class="cell-page-delete" onclick="taxonPageDelete({$v.id},'{$v.page}');"></td>
                <td>{if $v.redirect_to}{t}redirects to:{/t} {$v.redirect_to|substr:0:25}...{/if}</td>
                <td>{if $v.always_hide==1}(hidden; id={$v.id}){/if}</td>
            </tr>

            {/foreach}
        </table>
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
            <input type="submit" value="{t}save{/t}" />
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