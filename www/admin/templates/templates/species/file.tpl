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
{if $results}
<form method="post" action="" enctype="multipart/form-data">
<p>
Check the results of the import below. If they look OK, press 'save' to save them: <input type="submit" value="save" /><br />
You can exclude specific taxa by unchecking the checkbox. If instead of a checkbox the third column says 'unknown rank', you are
attempting to load a rank that is not part of your project. Click <a href="ranks.php">here</a> to add or change ranks.
</p>
<table>
<tr><th>Name</th><th>Rank</th><th>Hybrid</th></tr>
{section name=i loop=$results}
<tr class="tr-highlight">
<td><label for="chk{$smarty.section.i.index}">{$results[i][0]}</label></td>
<td><label for="chk{$smarty.section.i.index}">{$results[i][1]}</label></td>
<td><label for="chk{$smarty.section.i.index}">{if $results[i][2]==1}x{/if}</label></td>
<td>{if $results[i][3]}<input  type="checkbox" name="rows[]" id="chk{$smarty.section.i.index}" value="{$smarty.section.i.index}" checked="checked"/>{else}<span class="message-error">unknown rank</span>{/if}</td>
</tr>
{/section}
</table>
</form>
{else}
<p>
To load a list of taxa from file, click the 'browse'-button above, select the file to load from your computer and click 'upload'.
The contents of the file will be displayed so you can review them before they are saved to your project's database.<br />
The file must meet the following conditions:
</p>
<ol>
	<li>The format needs to be CSV.</li>
	<li>The field separator must be , (comma), the field delimiter " (double-quote).</li>
	<li>There should be one taxon per line. No header line should be present.</li>
	<li>Each taxon consists of two or three fields:
		<ol>
		<li>Taxon name</li>
		<li>Taxon rank</li>
		<li>Hybrid ('y'; optional)</li>
		</ol>
		in that order. The first two are mandatory. Other values for the field 'Hybrid' than 'y' are ignored.
	</li>
	<li>Ranks should match the list of ranks you have selected for your project.
		{if $projectRanks|@count==0}
		<br /><span class="message-error">Currently, you have defined no ranks in this project. Go <a href="ranks.php">here</a> to do so.</span>
		{else}
		These currently are:
		<ul style="list-style:none;margin-left:0px;padding-left:20px;">		
		{section name=i loop=$projectRanks}
			<li>{$projectRanks[i].rank}</li>
		{/section}
		</ul>
		Taxa with a rank that does not appear in this list will not be loaded.
		{/if}
	</li>
	<li>Hybrids are only possible for the following ranks:
		<ul style="list-style:none;margin-left:0px;padding-left:20px;">		
		{section name=i loop=$projectRanks}
			{if $projectRanks[i].can_hybrid==1}<li>{$projectRanks[i].rank}{/if}</li>
		{/section}
		</ul>
	</li>
	<li>Parent-child relations are assumed top-down, one branch at a time. For instance, loading:
		<ul style="list-style:none;margin-left:0px;padding-left:20px;">
			<li>Ursus &rarr; Genus</li>
			<li>Ursus luteolus &rarr; Species</li>
			<li>Ursus thibetanus &rarr; Species</li>
			<li>Ursus thibetanus laniger &rarr; Infraspecies</li>
			<li>Ursus thibetanus thibetanus &rarr; Infraspecies</li>
			<li>Ursus thibetanus gedrosianus &rarr; Infraspecies</li>
			<li>Ursus maritimus &rarr; Species</li>
			<li>Ursus maritimus marinus &rarr; Infraspecies</li>
			<li>Ursus maritimus maritimus &rarr; Infraspecies</li>	
			<li>Melursus &rarr; Genus</li>
			<li>Melursus ursinus &rarr; Species</li>
			<li>Melursus ursinus inornatus &rarr; Infraspecies</li>
			<li>Melursus ursinus ursinus &rarr; Infraspecies</li>
		</ul>
		in this order will correctly maintain the relations between Genus, Species and Infraspecies.
	</li>
</ol>
<p>
You can download a sample CSV-file <a href="{$baseUrl}admin/media/system/example.csv">here</a>.
</p>
{/if}

</div>

{include file="../shared/admin-footer.tpl"}
