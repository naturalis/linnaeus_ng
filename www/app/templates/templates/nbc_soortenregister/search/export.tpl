<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
Resultaten 1-{$results.count} van {$results.count}{if $searchHR} voor '{$searchHR}'{/if}
<table>
<tr>
	<td><b>wetenschappelijke naam</b></td>
	<td><b>nederlandse naam</b></td>
{if $imageExport}	
	<td><b>fotograaf</b></td>
	<td><b>validator</b></td>
{else}
	<td><b>status voorkomen</b></td>
	<td><b>barcode exemplaren</b></td>	
{/if}
	<td><b>link naar soortenregister</b></td>
	<td><b>link naar afbeelding</b></td>
</tr>
<tbody>
{foreach from=$results.data item=v}
<tr>
	<td>{$v.taxon}</td>
	<td>{$v.dutch_name}</td>
{if $imageExport}
	<td>{$v.photographer}</td>
	<td>{$v.validator}</td>
	<td>{$url_taxon_detail}{$v.taxon_id}</td>
	<td>http://images.ncbnaturalis.nl/comping/{$v.taxon_id}</td>
{else}
	<td>{$v.presence_information_index_label} {$v.presence_information_title}</td>
	<td>{$v.number_of_barcodes}</td>
	<td>{$url_taxon_detail}{$v.taxon_id}</td>
	<td>{if $v.overview_image}http://images.ncbnaturalis.nl/thumb/{$v.overview_image}{/if}</td>
{/if}
</tr>
{/foreach}
</tbody>
</table>
</body>
</html>