<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
{t}Resultaten{/t} 1-{$results.count} {t}van{/t} {$results.count}{if $searchHR || $searchTraitsHR} {t}voor{/t} '{if $searchHR}{$searchHR}{/if}{if $searchTraitsHR}{$searchTraitsHR}{/if}'{/if}

<table>
<tr>
	<td><b>{t}wetenschappelijke naam{/t}</b></td>
	<td><b>{t}nederlandse naam{/t}</b></td>
{if $imageExport}	
	<td><b>{t}fotograaf{/t}</b></td>
	<td><b>{t}validator{/t}</b></td>
{else}
	<td><b>{t}status voorkomen{/t}</b></td>
	<td><b>{t}barcode exemplaren{/t}</b></td>	
{/if}
	<td><b>{t}link naar soortenregister{/t}</b></td>
	<td><b>{t}link naar afbeelding{/t}</b></td>
</tr>
<tbody>
{foreach from=$results.data item=v}
<tr>
	<td>{$v.taxon}</td>
	<td>{$v.common_name}</td>
{if $imageExport}
	<td>{$v.photographer}</td>
	<td>{$v.validator}</td>
	<td>{$url_taxon_detail}{$v.taxon_id}</td>
	<td>{$taxon_base_url_images_main}{$v.image}</td>
{else}
	<td>{$v.presence_information_index_label} {$v.presence_information_title}</td>
	<td>{$v.number_of_barcodes}</td>
	<td>{$url_taxon_detail}{$v.taxon_id}</td>
	<td>
		{if $v.overview_image}
			{$taxon_base_url_images_main}{$v.overview_image}
		{elseif $v.image}
			{$taxon_base_url_images_main}{$v.image}
		{/if}
	</td>
{/if}
</tr>
{/foreach}
</tbody>
</table>
</body>
</html>