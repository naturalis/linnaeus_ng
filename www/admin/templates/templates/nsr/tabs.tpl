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
                {if $v.def_language=='1'}
				<td>{$v.language} *</td>
                {/if}
                {/foreach}
                {if $languages|@count > 1}
                <td id="project-language-tabs">(languages)</td>
                {/if}
                <td></td>
                <td>{t}attributes{/t}</td>
			</tr>

            {foreach from=$pages item=v}
            <tr class="tr-highlight">
                <td>
                    <span class="{if $v.always_hide==1}hidden-tab{/if}" title="{if $v.always_hide==1}hidden tab{/if}">{$v.page}</span>
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
                <td>
                	{if $v.always_hide==1}{t}always hidden{/t} (id={$v.id}){/if}
                	{if $v.external_reference && $v.always_hide==1}; {/if}
                	{if $v.external_reference}{t}external reference{/t}{/if}
                	<a class="edit" href="tab.php?id={$v.id}">{t}edit{/t}</a>
                </td>
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