{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form method="post" action="" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
	<input name="uploadedfile" type="file" /><br />
	<input type="submit" value="upload" />
</form>
</div>

{include file="../shared/admin-messages.tpl"}

<div class="page-generic-div">
<p>
{if $results}
<form method="post" action="" enctype="multipart/form-data">
Check the results of the import below. If they look OK, press 'save' to save them: <input type="submit" value="save" /><br />
You can exclude specific taxa by unchecking the checkbox.
<table>
{section name=i loop=$results}
<tr>
{assign var=dummy value=$results[i]}
{section name=j loop=$dummy}
<td><label for="chk{$smarty.section.i.index}">{$dummy[j]}</label></td>
{/section}
<td><input  type="checkbox" name="rows[]" id="chk{$smarty.section.i.index}" value="{$smarty.section.i.index}" checked="checked"/></td>
</tr>
{/section}
</table>
</form>
{else}
You can load a list of taxa from file. The file must meet the following conditions:
<ol>
	<li>The format needs to be CSV.</li>
	<li>The field separator must be , (comma), the field delimiter " (double-quote).</li>
	<li>There should be one taxon per line.</li>
	<li>Each taxon consists of two fields:
		<ol>
		<li>Rank</li>
		<li>Name</li>
		</ol>
		in that order. Both are mandatory.</li>
	<li>Parent-child relations are assumed top-down, one branch at a time. For instance, loading:
		<ul style="list-style:none;margin-left:0px;padding-left:20px;">
			<li>Genus: Ursus</li>
			<li>Species: Ursus luteolus</li>
			<li>Species: Ursus thibetanus</li>
			<li>Infraspecies: Ursus thibetanus laniger</li>
			<li>Infraspecies: Ursus thibetanus thibetanus</li>
			<li>Infraspecies: Ursus thibetanus gedrosianus</li>
			<li>Species: Ursus maritimus</li>
			<li>Infraspecies: Ursus maritimus marinus</li>
			<li>Infraspecies: Ursus maritimus maritimus</li>	
			<li>Genus: Melursus</li>
			<li>Species: Melursus ursinus</li>
			<li>Infraspecies: Melursus ursinus inornatus</li>
			<li>Infraspecies: Melursus ursinus ursinus</li>
		</ul>
		in this order will correctly maintain the relations between Genus, Species and Infraspecies.
	</li>
</ol>
Click the 'browse'-button above, select the file to load from your computer and click 'upload'.
The contents of the file will be displayed so you can review them before they are saved to your project's database.<br />
You can download a sample CSV-file <a href="{$baseUrl}admin/media/system/example.csv">here</a>.

{/if}
</p>
</form>
</div>

{include file="../shared/admin-footer.tpl"}
