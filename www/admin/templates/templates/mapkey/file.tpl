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
<a href="download_csv.php">{t}Download template CSV-file{/t}</a> {t}(with species names, species ID's and data types from your project, but no example geographical data){/t}<br />
<a href="../../media/system/geo-example.csv">{t}Download example CSV-file{/t}</a> {t}(general example, including geographical data){/t}
</p>
<p>
{t}You can upload geographical data by means of a CSV-file. Above, you can download a file that can function as a template.{/t}
</p>
<p>
{t}In the file, the first and second columns hold the ID and the name of the lower taxa in your database. ID and name belong together, and should not be removed or moved to other columns than the first two. The third columns specifies the type of data, represented by an ID. At the top of the file, there is a list of data types and ID's as they are currently defined in your project.{/t} {t}The same list is shown here:{/t}<br />
<table>
<tr><th>{t}DATA TYPE{/t}</th><th>{t}DATA TYPE ID{/t}</th></tr>
{foreach from=$geodataTypes key=k item=v name=x}
<tr><td>{$v.title}</td><td>{$v.id}</td></tr>
{/foreach}
</table>
{if $smarty.foreach.x.index==-1}{t _s1='<a href="data_types.php">' _s2='</a>'}No data types have been defined yet. %sDefine data types%s{/t}{/if}
</p>
<p>
{t}The following columns in each row specify the coordinates of a geographical point on the map. These always come in pairs of latitude and longitude.{/t}<br />
{t}For points on the map, only specify coordinates for the first node (columns four and five; labeled "NODE 1"). For several points of the same species, copy the ID and TAXON of that species to a new, empty line, and add the data type and coordinates for the first node.{/t}<br />
{t}To define areas, represented by polygons, specify the coordinates for all the nodes in the polygon in pairs of latitude and longitude. There is no limit to the number of nodes in a polygon, but displaying polygons with a large number of nodes might be slow. Polygons do not need to be closed (i.e., have identical first and last nodes); the application will automatically connect the first and last nodes. For several polygons of the same species, copy the ID and TAXON of that species to a new, empty line, and specify the data type ID, and the nodes of the polygon.{/t}
</p>
</div>
{include file="../shared/admin-footer.tpl"}
