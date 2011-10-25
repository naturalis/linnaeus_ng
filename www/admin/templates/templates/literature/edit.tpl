{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form action="" method="post" id="theForm" action="edit.php">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" value="{$ref.id}" />
<input type="hidden" name="action" id="action" value="" />

{if $ref.multiple_authors==0 && $ref.author_second!=''}
{assign var=num value=2}
{elseif $ref.multiple_authors==1}
{assign var=num value=99}
{else}
{assign var=num value=1}
{/if}

<table>
	<tr>
		<td>{t}Number of authors:{/t}</td>
		<td>
			<label><input type="radio" name="auths" id="auths-1" value="1" onchange="litToggleAuthorTwo()" {if $num==1}checked="checked"{/if} />{t}one{/t}</label>
			<label><input type="radio" name="auths" id="auths-2" value="2" onchange="litToggleAuthorTwo()" {if $num==2}checked="checked"{/if} />{t}two{/t}</label>
			<label><input type="radio" name="auths" id="auths-n" value="n" onchange="litToggleAuthorTwo()" {if $num==99}checked="checked"{/if}/>{t}more{/t}</label>
		</td>
	</tr>
	<tr>
		<td id="auth-label">
			{if $num==1}{t}Author:{/t}{else}{t}Authors:{/t}{/if}
		</td>
		<td>
			<input
				type="text"
				name="author_first"
				id="author_first"
				value="{$ref.author_first}"
				autocomplete="off"
				maxlength="32"
				onkeyup="litShowAuthList(this)" />
				<span id="auth-two" class="lit-author-two-{if $num!=2}hidden{/if}">
				&amp;
			<input 
				type="text" 
				name="author_second" 
				id="author_second" 
				value="{$ref.author_second}" 
				autocomplete="off"
				maxlength="32" 
				onkeyup="litShowAuthList(this)"/>
			</span>
			<span id="auth-etal" class="lit-author-etal-{if $num!=99}hidden{/if}">{t}et al.{/t}</span>
		</td>
	</tr>
	<tr>
		<td>{t}Year &amp; suffix (optional):{/t}</td>
		<td>
			<input
				type="text" 
				name="year" 
				id="year" 
				value="{$ref.year}" 
				maxlength="4" 
				style="width:50px" 
				onfocus="litHideAuthList();" 
				onkeyup="litCheckYear(this)"/>
			<input
				type="text" 
				name="suffix" 
				id="suffix" 
				value="{$ref.suffix}" 
				maxlength="3" 
				style="width:25px" />
			<span id="msgYear"></span>
		</td>
	</tr>
</table>
<table>
	<tr style="vertical-align:top">
		<td>{t}Reference:{/t} *</td>
	</tr>
	<tr style="vertical-align:top">
		<td>
			<textarea
				name="text"
				id="text">{$ref.text}</textarea>
		</td>
	</tr>
</table>
<br />
<table>
	<tr style="vertical-align:top">
		<td style="white-space:nowrap">{t}Taxa this reference pertains to:{/t}</td>
		<td>
			<select id="taxa">
            {foreach from=$taxa key=k item=v}
            {if $v.id && (($isHigherTaxa && $v.lower_taxon==0) || !$isHigherTaxa)}
            <option value="{$v.id}" {if $data.parent_id==$v.id}selected="selected"{/if}>
            {section name=foo loop=$v.level-$taxa[0].level}
            &nbsp;
            {/section}
            {$v.taxon}</option>
            {/if}
            {/foreach}
			</select>
			<span id="add-button" class="pseudo-a" onclick="litAddTaxonToList()">{t}add{/t}</span>
			<div id="selected-taxa"></div>
		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>		
	<tr>
		<td colspan="2">
			<input type="button" value="{t}save{/t}" onclick="litCheckForm(this)" />
			{* if $session.system.literature.taxon.taxon_id!=''}
			<input type="button" value="{t}back{/t}" onclick="window.open('../species/literature.php?id={$session.system.literature.taxon.taxon_id}','_top')" />
			{else}
			<input type="button" value="{t}back{/t}" onclick="window.open('index.php','_top')" />
			{/if *}
			{if $ref.id}
			<input type="button" value="{t}delete{/t}" onclick="litDelete()" />
			{/if}
		</td>
	</tr>
</table>
</form>
</div>

<p id="dropdown" class="lit-dropdown-invisible"></p>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){

$('body').click(function(e) {

	if(e.target.id!='dropdown') litHideAuthList();

});

var f = $('#selected-taxa');

var off = $('#add-button').offset();
f.offset({left : off.left + $('#add-button').width() + 25, top: off.top});

initTinyMce(false,false);


});
{/literal}

{foreach from=$ref.taxa item=v}
	litAddTaxonToList({$v},true);
{/foreach}
litUpdateTaxonSelection();
{if $ref}
litThisReference = ['{$ref.author_first|escape:'quotes'} ({$ref.year})'];
{/if}
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
