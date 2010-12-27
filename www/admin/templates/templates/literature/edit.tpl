{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form action="" method="post" id="theForm">
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
			<label><input type="radio" name="auths" value="1" onchange="litToggleAuthorTwo()" {if $num==1}checked="checked"{/if} />one</label>
			<label><input type="radio" name="auths" value="2" onchange="litToggleAuthorTwo()" {if $num==2}checked="checked"{/if} />two</label>
			<label><input type="radio" name="auths" value="n" onchange="litToggleAuthorTwo()" {if $num==99}checked="checked"{/if}/>more</label>
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
		<td>{t}Year:{/t}</td>
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
			<span id="msgYear"></span>
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Reference:{/t}</td>
		<td>
			<textarea
				name="text"
				id="text"
				style="width:500px;height:250px;font-size:13px">{$ref.text}</textarea><br />
			{t}You can use italics in your reference by enclosing the text to be italicised with &lt;i&gt; and &lt;/i&gt; tags. Other tags are not allowed.{/t}
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Taxa this reference pertains to:{/t}</td>
		<td>
			<select name="taxa" id="taxa">
			{section name=i loop=$taxa}
			<option value="{$taxa[i].id}" {if $data.parent_id==$taxa[i].id}selected="selected"{/if}>
			{section name=foo loop=$taxa[i].level-$taxa[0].level}
			&nbsp;
			{/section}		
			{$taxa[i].taxon}</option>
			{/section}
			</select>
			<span class="pseudo-a" style="padding: 0px 10px 0px 10px;cursor:pointer" onclick="litAddTaxonToList()">{t}add{/t}</span>
			<div id="selected-taxa"></div>
		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>		
	<tr>
		<td colspan="2">
			<input type="button" value="{t}save{/t}" onclick="litCheckForm(this)" />
			{if $session.system.literature.taxon.taxon_id!=''}
			<input type="button" value="{t}back{/t}" onclick="window.open('../species/literature.php?id={$session.system.literature.taxon.taxon_id}','_top')" />&nbsp;&nbsp;
			{else}
			<input type="button" value="{t}back{/t}" onclick="window.open('index.php','_top')" />&nbsp;&nbsp;
			{/if}
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

var off = $('#taxa').offset();
f.offset({left : off.left + $('#taxa').width() + 50, top: off.top});

});
{/literal}

{section name=i loop=$ref.taxa}
	litAddTaxonToList([{$ref.taxa[i].taxon_id},'{$ref.taxa[i].taxon}']);
{/section}

litThisReference = ['{$ref.author_first|escape:'quotes'} ({$ref.year})'];

</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
