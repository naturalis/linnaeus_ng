{include file="../shared/admin-header.tpl"}

<div id="page-main">

{if !$results}
<form method="post" action="" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
	<input type="hidden" name="rnd" value="{$rnd}" />
	<input name="uploadedfile" type="file" /><br />
	<table>
		<tr>
			<td>{t}CSV field delimiter:{/t}</td>
			<td><label><input type="radio" name="delimiter" value="comma" checked="checked" />, {t}(comma){/t}</label></td>
			<td><label><input type="radio" name="delimiter" value="semi-colon" />; {t}(semi-colon){/t}</label></td>
			<td><label><input type="radio" name="delimiter" value="tab" />{t}tab stop{/t}</label></td>
		</tr>
	</table>
	<input type="submit" value="{t}upload{/t}" />
</form>
{/if}
</div>

{include file="../shared/admin-messages.tpl"}

<div class="page-generic-div">
<p>
<a href="download_csv.php">Download sample CSV-file</a>
</p>
<p>
For single occurrences ("spots" on the map), only fill in coordinates for NODE 1
For several "spots" of the same species, copy the ID and TAXON of that species to a new line, and add the coordinates for NODE 1

EXAMPLE
</p>
<p>
For polygons ("areas" on the map), fill in the coordinates for all the nodes in the polygon.
If there are more than three nodes, continue filling in pairs of latitude and longitude on the same line. There is no limit, but displaying polygons with a large number of nodes might be slow.
Polygons do not need to be closed; the application will automatically connect the first and last nodes.
Several polygons: copy id & taxon to new line, and fill in the coordinates.

EXAMPLE
</p>
<p>
Dots or comma's, but NO thousand etc.
Remove the lines of the taxa you do not wish to include, or leave them empty.
</p>
</div>
{include file="../shared/admin-footer.tpl"}
