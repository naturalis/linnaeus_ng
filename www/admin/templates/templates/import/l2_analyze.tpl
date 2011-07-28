{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}
{assign var=process value=true}
{literal}
<style>
.error {
	color:red;
}
.info {
	color:#308;
}
.minor {
	color:#888;
}
</style>
<script>
function toggleAllValid() {

	$("input[@name='treetops*'][type='checkbox']").attr('checked',$('#allTreetops').is(':checked'));

}
</script>
{/literal}
<div id="page-main">
{if $processed==true}
Basic data has been loaded. Click the link below to import additional data (keys, literature, glossary, etc.)<br />
<a href="l2_secondary.php">Import additional data</a>
{else}
Review the data below and press "save" to save it to the database. In the following step, data dependent on the newly saved species will be loaded. You will have to complete that step in the same session so DO NOT LOG OUT OR CLOSE YOUR BROWSER before the entire process is complete, unless you only want to load species.

<form method="post">
<input type="hidden" name="process" value="1"  />
<input type="hidden" name="rnd" value="{$rnd}" />

<p>
<b>Ranks</b><br />
{if $ranks|@count==0}
No ranks were found or could be resolved. Import cannot proceed.<br />
Go to "Projects -> [project] -> Species module -> Taxonomic ranks" to see a list of valid ranks.<br />
Alter import filre accordingly and try again.
{assign var=process value=false}
{else}
{assign var=i value=0}
{assign var=err value=false}
{foreach from=$ranks key=k item=v}
{if $v.rank_id==false}
<span class="error">Rank could not be resolved:{$k}</span><br />
{assign var=err value=true}
{/if}
{if $v.parent_id===false}
<span class="error">Could not resolve parent rank of "{$k}": {$v.parent_name}</span><br />
{assign var=err value=true}
{/if}
{if $v.parent_id==null && $i>0}
<span class="error">Parentless rank that is not top of the tree: {$k}</span><br />
{assign var=err value=true}
{/if}
{if $v.rank_id!=false && (($v.parent_id!=false && $v.parent_id!=null) || $i==0)}
Resolved rank: {$k}<br />
{/if}
{assign var=i value=$i+1}
{/foreach}
{/if}
{if $err}
<span class="info">(Ranks shown in red will not be imported, nor will species of that rank be imported.)</span>
{/if}
</p>

<p>
<b>Species</b><br />
{assign var=i value=0}
{foreach from=$species key=k item=v}
{if $v.rank_id==''}
&#149;&nbsp;"{$v.taxon}" has no rank and will not be loaded. <span class="minor">(found in: {$v.source})</span><br />
{else}
{assign var=i value=$i+1}
{/if}
{/foreach}
Found {$i} "healthy" species that will be loaded<br />
</p>

<p>

{if $treetops|@count > 1}
<b>Tree conflicts</b><br />
The following species have no parent. It is possible that the taxon tree has several treetops for which no common parent has been defined - like Animalia and Plantae; the system will create a "master taxon" for these for technical purposes. Please specify which taxa are valid "treetops" and will have the master taxon as parent. The other taxa will be loaded, but will become orphans and will have to be attached to the taxon tree by hand. You can alter the name of the master taxon by hand after importing.
<table>
<tr>
	<td style="border-bottom:1px solid #999">&nbsp;</td>
	<td style="border-bottom:1px solid #999">Species</td>
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

<input type="submit" value="save" />
</form>
{/if}
<p>
<a href="linnaeus2.php">back</a>
</p>

</div>

{include file="../shared/admin-footer.tpl"}