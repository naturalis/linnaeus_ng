{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}
{assign var=process value=true}
{literal}
<script>
function toggleAllValid() {

	$('input[name*="treetops"][type="checkbox"]').attr('checked',$('#allTreetops').is(':checked'));

}

function checkForm() {

	var proceed = true;

	$("select option:selected").each(function () {
		proceed = proceed && $(this).val()!='';
	});

	if (!proceed && !confirm('You have not mapped all unknown ranks. Do you want to save?')) return;

	$('#process').val('1');
	$('#theForm').submit();

}
</script>
{/literal}
<div id="page-main">
{if $processed==true}
Basic data has been loaded. Click the link below to import additional data (keys, literature, glossary, etc.)<br />
<a href="l2_species_data.php">Import additional data</a>
{else}
Review the data below and press "save" to save it to the database. In the following step, data dependent on the newly saved species will be loaded. You will have to complete that step in the same session so DO NOT LOG OUT OR CLOSE YOUR BROWSER before the entire process is complete, unless you only want to load species.

<form method="post" id="theForm">
<input type="hidden" id="process" name="process" value="0"  />
<input type="hidden" name="rnd" value="{$rnd}" />

<p>
<b>Ranks</b><br />
{if $ranks|@count==0}
    No ranks were found or could be resolved. Import cannot proceed.<br />
    Go to "Projects -> [project] -> Species module -> Taxonomic ranks" to see a list of valid ranks.<br />
    Alter import file accordingly and try again.
    {assign var=process value=false}
{else}
    {assign var=i value=0}
    {assign var=err value=false}
    {foreach from=$ranks key=k item=v}
        {if $v.rank_id==false}
        <span class="{if $substRanks[$k]!=''}fixed-{/if}error">Rank could not be resolved: "{$k}"{if $substRanks[$k]!=''}; substituting:{/if}</span>
            <select name="substRanks[{$k}]" id="substRanks-{$i}">
                <option value="">select appropriate rank:</option>
                <option value="" disabled="disabled">&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;</option>
                {foreach from=$projectRanks item=pr}
                <option value="{$pr.id}" {if $substRanks[$k]==$pr.id}selected="selected"{/if}>{$pr.rank}</option>
                {/foreach}
            </select>
        <br />
        {assign var=err value=true}
        {/if}
        {if $v.parent_id===false}
        <span class="{if $substParentRanks[$k]!=''}fixed-{/if}error">Parent rank of "{$k}" could not be resolved: "{$v.parent_name}"{if $substParentRanks[$k]!=''}; substituting:{/if}</span>
            <select name="substParentRanks[{$k}]" id="substParentRanks-{$i}">
                <option value="">select appropriate rank:</option>
                <option value="" disabled="disabled">&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;</option>
                {foreach from=$projectRanks item=pr}
                <option value="{$pr.id}" {if $substParentRanks[$k]==$pr.id}selected="selected"{/if}>{$pr.rank}</option>
                {/foreach}
            </select>
        <br />
        {assign var=err value=true}
        {/if}

        {if $v.parent_id==null && $i>0}
        	{* <span class="error">Parentless rank that is not top of the tree: {$k}</span><br /> {assign var=err value=true} *}
	            Resolved rank: {$k}<br />
        {/if}
        
        {if $v.rank_id!=false && (($v.parent_id!=false && $v.parent_id!=null) || $i==0)}
            Resolved rank: {$k}<br />
        {/if}
        {assign var=i value=$i+1}
    {/foreach}

    {if $err}
        <span class="info">(Ranks shown in red will not be imported, nor will species of that rank be imported.)</span>
    {/if}

    {if $multiples|@count>0}
	<p>
    The import contains one or more ambigious ranks. Select the correct match:<br />
	<ul>
    {foreach from=$multiples key=k item=v}
		<li>{$k}
        	<ul style="list-style-type:none;padding:0">
    	    {foreach from=$v key=kk item=vv}
                <li><label><input name="multiRankChoice[{$k}]" value="{$vv.id}" type="radio"{if $kk==0} checked="checked"{/if}>{$vv.rank|@strtolower}{if $vv.additional} ({$vv.additional}){/if} [child of {$vv.parent_rank|@strtolower}]</label></li>
        	{/foreach}
	        </ul>
        </li>
    {/foreach}
    </ul>
	</p>    
    {/if}

{/if}


</p>
{if $substRanks|@count > 0 || $substParentRanks|@count > 0}
<p>
After selecting substitute ranks above, click "Update". Your selection will not be saved until you click "Save"
at the bottom of this page.<br />
PLEASE CHOOSE CAREFULLY. The application will <u>not</u> check the logic of your choices before saving them.<br /><br />
<input type="submit" value="{t}Update{/t}" />
</p>
{/if}

	<hr />


<p>
<b>Species</b><br />
{assign var=i value=0}
{foreach from=$species key=k item=v}
{if $v.rank_id==''}
&#149;&nbsp;"{$v.taxon}" has no valid rank and will not be loaded. <span class="minor">(found in: {$v.source})</span><br />
{else}
{assign var=i value=$i+1}
{/if}
{/foreach}
Found {$i} "healthy" taxa that will be loaded<br />
</p>

<p>
	{if $treetops|@count > 1}
	<b>Tree conflicts</b><br />
	The following taxa have no parent. It is possible that the taxon tree has several treetops for which no common parent has been defined - like Animalia and Plantae; the system will create a "master taxon" for these for technical purposes. Please specify which taxa are valid "treetops" and will have the master taxon as parent. The other taxa will be loaded, but will become orphans and will have to be attached to the taxon tree by hand. You can alter the name of the master taxon by hand after importing.
	<table>
	<tr>
		<td style="border-bottom:1px solid #999">&nbsp;</td>
		<td style="border-bottom:1px solid #999">Taxon</td>
		<td style="border-bottom:1px solid #999">source</td>
		<td style="border-bottom:1px solid #999"><label><input type="checkbox" id="allTreetops" value="{$v.taxon}" onclick="toggleAllValid()" />select all</label></td>
	</tr>
	{assign var=i value=0}
	{foreach from=$treetops key=k item=v}
	<tr>
		<td>&#149;&nbsp;</td>
		<td>{$v.taxon}</td>
		<td><span class="minor">(found in: {$v.source})</span></td>
		<td><label><input type="checkbox" name="treetops[]" value="{$v.taxon}" />valid</label></td>
	</tr>
	{/foreach}
	</table>
</p>
{/if}

<input type="button" value="{t}Save{/t}" onclick="checkForm()" />
</form>
{/if}
</div>

{include file="../shared/admin-footer.tpl"}