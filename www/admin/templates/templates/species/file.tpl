{include file="../shared/admin-header.tpl"}

<div id="page-main">
{if !$results}
<form method="post" action="" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
	<input name="uploadedfile" type="file" /><br />
	<table>
		<tr>
			<td>{t}CSV field delimiter:{/t}</td>
			<td><label><input type="radio" name="delimiter" value="comma" checked="checked" />, {t}(comma){/t}</label></td>
			<td><label><input type="radio" name="delimiter" value="semi-colon" />; {t}(semi-colon){/t}</label></td>
			<td><label><input type="radio" name="delimiter" value="tab" />{t}tab stop{/t}</label></td>
		</tr>
		<!-- tr>
			<td>CSV field enclosure:</td>
			<td><label><input type="radio" name="enclosure" value="double" checked="checked" />" (double qoutes)</label></td>
			<td><label><input type="radio" name="enclosure" value="single" />' (quote)</label></td>
			<td><label><input type="radio" name="enclosure" value="none" />none</label><br /></td>
		</tr -->
	</table>
	<input type="submit" value="{t}upload{/t}" />
</form>
{/if}
</div>

{if !$results}
{include file="../shared/admin-messages.tpl"}
{/if}

<form method="post" action="">
<input type="hidden" name="rnd" value="{$rnd}" />
<div class="page-generic-div">
{if $results}
<p>
{t}Check the results of the import below. You can exclude specific taxa by unchecking the checkbox. If instead of a checkbox there an error message, you are attempting to load a taxon that can not be loaded.{/t}<br />
{t}If the results look OK, click 'save':{/t}
</p>
</div>

{include file="../shared/admin-messages.tpl"}

<div class="page-generic-div">

{if $connectToTaxonIds}
{t}parent taxon:{/t} 
<select name="connectToTaxonId">
{section name=i loop=$connectToTaxonIds}
<option value="{$connectToTaxonIds[i].id}">{$connectToTaxonIds[i].taxon}</option>
{/section}
</select>
{/if}
{if $connectToTaxonId}<input type="hidden" name="connectToTaxonId" value="{$connectToTaxonId}" />{/if}


<table>
<tr><th>{t}Name{/t}</th><th>{t}Rank{/t}</th>
{if $session.admin.project.includes_hybrids==1}
<th>{t}Hybrid{/t}</th></tr>
{/if}
<th>{t _s=$session.admin.project.languageList[$session.admin.project.default_language_id].language}Common name in %s{/t}</th></tr>
{section name=i loop=$results}
<tr class="tr-highlight">
<td>{$results[i][0]}</td>
<td>{$results[i][1]}</td>
{if $session.admin.project.includes_hybrids==1}
<td><label for="chk{$smarty.section.i.index}">{if $results[i][2]==1}x{/if}</label></td>
<td>{$results[i][3]}</td>
<td>{if $results[i][4]=='ok'}<input  type="checkbox" name="rows[]" id="chk{$smarty.section.i.index}" value="{$smarty.section.i.index}" checked="checked"/>{else}<span class="message-error">{$results[i][4]}</span>{/if}</td>
{else}
<td>{$results[i][2]}</td>
<td>{if $results[i][3]=='ok'}<input  type="checkbox" name="rows[]" id="chk{$smarty.section.i.index}" value="{$smarty.section.i.index}" checked="checked"/>{else}<span class="message-error">{$results[i][3]}</span>{/if}</td>
{/if}
</tr>
{/section}
</table>

<input type="submit" value="{t}save{/t}" />
 
{else}
<p>
{t}To load a list of taxa from file, click the 'browse'-button above, select the file to load from your computer and click 'upload'.
The contents of the file will be displayed so you can review them before they are saved to your project's database.{/t}<br />
{t}The file must meet the following conditions:{/t}
</p>
<ol>
	<li>{t}The format needs to be CSV.{/t}</li>
	<li>{t}The field delimiter must be a comma, semi-colon or tab stop, and can be selected above.{/t}</li>
	<li>{t}The fields in the CSV-file *may* be enclosed by " (double-quotes), but this is not mandatory.{/t}</li>
	<li>{t}There should be one taxon per line. No header line should be present.{/t}</li>
	<li>{t}Each taxon consists of the following fields:{/t}
		<ol>
		<li>{t}Taxon name{/t}</li>
            <li>{t}Taxon rank{/t}</li>
        {if $session.admin.project.includes_hybrids==1}
	        <li>{t}Hybrid ('y'; optional){/t}</li>
        {/if}
            <li>{t _s=$session.admin.project.languageList[$session.admin.project.default_language_id].language}Common name in %s (optional){/t}</li>
		</ol>
		{t}in that order. The first two are mandatory. {/t}
{if $session.admin.project.includes_hybrids==1}
		{t}Other values for the field 'Hybrid' than 'y' are ignored.{/t}
{/if}		
	</li>
	<li>{t}Ranks should match the list of ranks you have selected for your project.{/t}
		{if $projectRanks|@count==0}
		<br /><span class="message-error">{t}Currently, you have defined no ranks in this project.{/t} <a href="ranks.php">{t}Define ranks{/t}</a>.</span>
		{else}
		{t}These currently are:{/t}
		<ul style="list-style:none;margin-left:0px;padding-left:20px;">		
		{section name=i loop=$projectRanks}
			<li>{$projectRanks[i].rank}</li>
		{/section}
		</ul>
		{t}Taxa with a rank that does not appear in this list will not be loaded.{/t}
		{/if}
	</li>
	<li>{t}Hybrids are only possible for the following ranks:{/t}
		<ul style="list-style:none;margin-left:0px;padding-left:20px;">		
		{section name=i loop=$projectRanks}
			{if $projectRanks[i].can_hybrid==1}<li>{$projectRanks[i].rank}{/if}</li>
		{/section}
		</ul>
	</li>
	<li>{t}Parent-child relations are assumed top-down, one branch at a time. For instance, loading:{/t}
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
		{t}in this order will correctly maintain the relations between Genus, Species and Infraspecies.{/t}
	</li>
</ol>
<p>
<a href="{$baseUrl}admin/media/system/example.csv">{t}Download a sample CSV-file{/t}</a>.
</p>
{/if}
</div>
</form>

{include file="../shared/admin-footer.tpl"}
