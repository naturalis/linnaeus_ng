{include file="../shared/admin-header.tpl"}

<style>
.these-lists {
  list-style-type: none;
  margin: 0;
  padding: 0;
}
.these-lists li {
  margin-bottom: 2px;
  padding: 2px;
  vertical-align:top;
}

.these-lists li:hover {
	background-color:#eee;
}

.these-lists li span {
	display:inline-block;
}
.these-lists li span:nth-child(1) {
	cursor:move;
	width:10px;
}
.these-lists li span:nth-child(2) {
	width:170px;
}
.these-lists li span:nth-child(3) {
	width:170px;
}
.these-lists li span:nth-child(4) {
	width:110px;
}
.these-lists li span:nth-child(5) {
	width:75px;
}
.these-lists li span:nth-child(6) {
  width: 60px;
}
.delete {
  background-image: url(../../media/system/icons/cross.png);
  cursor: pointer;
  background-repeat: no-repeat;
}
.these-lists li span:nth-child(7) {
	width:250px;
}
.hidden-tab, .tab-redirect-label {
	color:#999;
}
.tab-redirect {
	font-size:0.9em;
}
.auto-tab {
	color:#900;
}
.suppressed-tab {
	text-decoration:line-through;
}
select {
	font-size:0.9em;
	width:50px;
}
</style>


<div id="page-main">

	<form method="post" id="theForm">
    <input type="hidden" name="rnd" value="{$rnd}" />
    <input type="hidden" name="action" value="save" />

	<!-- p>
        {t _s1=$maxCategories _s2=$pages[0].page}Each taxon page consists of one or more categories, with a maximum of %s. The first category, '%s', is mandatory.{/t}<br />
        {t}Below, you can specify the correct label of each category in the language or languages defined in your project. On the left hand side, the labels in the default language are displayed. On the right hand side, the labels in the other languages are displayed. These are shown a language at a time; you can switch between languages by clicking its name at the top of the column. The current active language is shown underlined.{/t}<br />
        {t}Text you enter is automatically saved when you leave the input field.{/t}
	</p -->

	<p>
    
    <div>

        <ul class="these-lists">
        	
			<li style="vertical-align:bottom">
                <span></span>
                <span>{t}category{/t}</span>
                <span>{t}translation{/t}</span>
                <span>{t}show when empty{/t}</span>
                <span>{t}start order{/t}</span>
                <span>{t}delete / suppress{/t}</span>
                <span>{t}attributes{/t}</span>
			</li>
		</ul>

        <ul id="order-sort" class="these-lists">
           
            {foreach $pages v}

            <li class="sortable" data-id="{$v.id}">
            	<div>
                <span>&blk14;</span>
                <span 
                    class="{if $v.type=='auto'}auto-tab{/if} {if $v.always_hide==1}hidden-tab{/if} {if $v.suppress}suppressed-tab{/if}" 
                    title="{if $v.type=='auto'}automatic tab{/if}{if $v.suppress}; suppressed{/if}{if $v.always_hide==1}hidden tab{/if}">
                    {$v.page}
                </span>
    
                <span>
                {foreach $languages l}
	                {if k>0}<br />{/if}
                    <input 
                    	placeholder="{$l.language}"
                        type="text" 
                        maxlength="64" 
                        name="pages_taxa_titles[{$v.id}][{$l.language_id}]"
                        value="{$v.page_titles[$l.language_id]}" />
                {/foreach}
                </span>

                <span>
                    <label><input type="radio" name="show_when_empty[{$v.id}]" value="1" {if $v.show_when_empty}checked="checked"{/if}/>{t}y{/t}</label>
                    <label><input type="radio" name="show_when_empty[{$v.id}]" value="0" {if !$v.show_when_empty}checked="checked"{/if}/>{t}n{/t}</label>
                </span>

                <span>
                <select class="start-order" name="start_order[{$v.id}]" onchange="uniqueifyStartOrder();"> 
                {for $foo=1 to $pages|@count}
                    <option value="{$foo}" {if $v.start_order==$foo}selected="selected"{/if}>{$foo}</option>
                {/for}
                    <option value="" {if $v.start_order==''}selected="selected"{/if}>-</option>
                </select>
                </span>

                {if $v.type=='auto'}
                <span class="auto"><input type="checkbox" name="suppress[{$v.id}]" {if $v.suppress} checked="checked"{/if}/></span>
                <span></span>
                {else}
                <span class="delete" onclick="taxonPageDelete({$v.id},'{$v.page}');">&nbsp;</span>
                <span>
                    <a class="edit" href="tab.php?id={$v.id}">{t}edit{/t}</a>&nbsp;
                    {if $v.always_hide==1}{t}always hidden{/t} (id={$v.id}){/if}
                    {if $v.external_reference && $v.always_hide==1}; {/if}
                    {if $v.external_reference}{t}external reference{/t}{/if}
                </span>
                {/if}
                </div>
            </li>
			{/foreach}
        </ul>
	</div>
    <div style="clear:both;width:500px;margin-bottom:5px"></div>

    <br />
    <input type="button" value="{t}save{/t}" onclick="saveForm();"/>
    </form>

	</p>
	<p>
        {if $languages|@count==0}
	        {t}You have to define at least one language in your project before you can add any categories.{/t} <a href="../projects/data.php">{t}Define languages now.{/t}</a>
        {else}
        <form method="post" action="" id="theForm">
            {if $pages|@count<$maxCategories}
            {t}Add a new category:{/t} 
            <input type="text" maxlength="64" id="new_page" name="new_page" value="" />
            <input type="hidden" name="rnd" value="{$rnd}" />
            <input type="submit" value="{t}add{/t}" />
            {/if}
		</form>
	</p>
{/if}

</div>

<script type="text/javascript">

function saveForm()
{
	var form = $('#theForm');

	$('li.sortable').each(function ()
	{
		var val = $(this).attr('data-id');
		form.append('<input type="hidden" name="tab_order[]" value="'+val+'">').val(val);
	})

	form.submit();
}

function uniqueifyStartOrder()
{
	var d=Array();
	$('.start-order').each(function(i,e)
	{
		var v=$(this).val();
		if (parseInt(v))
		{
			if (d.indexOf(v)!==-1)
				$(this).val("");
			else
				d.push(v);
		}
	});
}

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

	$(".sortable").sortable({
		opacity: 0.6, 
		cursor: 'move',
		items: "li:not(.ui-state-disabled)",
		connectWith: ".connectedSortable"
	});

	$( "#order-sort" ).sortable();

});

</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}