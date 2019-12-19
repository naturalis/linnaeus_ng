{include file="../shared/admin-header.tpl"}
{assign var=map value=$maps[$mapId]}

<div id="page-main">

{foreach item=v from=$maps}
<p>
<table>
	<tr style="vertical-align:top;">
    	<td>
            <a rel="prettyPhoto[gallery]" title="{$v.name}" href="{$v.imageFullName|replace:' ':'%20'}">
	            <img src="{$v.imageFullName|replace:' ':'%20'}" style="height:75px;cursor:pointer"/>
            </a>        
        </td>
    	<td>
        	<b>{$v.name}</b><br />
            coordinates: {$v.coordinates.topLeft.long}, {$v.coordinates.topLeft.lat} (top left) x {$v.coordinates.bottomRight.long}, {$v.coordinates.bottomRight.lat} (bottom right)<br />
            cells: {$v.cols}x{$v.rows} (columns x rows)
        </td>
	</tr>
</table>
</p>
{/foreach}

<form enctype="multipart/form-data" action="" method="post">
<input type="hidden" name="id" value="{$id}" />  
<input type="hidden" name="rnd" value="{$rnd}" />
<table>
	<tr><td>{t}Choose a file:{/t}</td><td><input name="uploadedfile" type="file" /></td></tr>
	<tr><td>{t}Map name:{/t}</td><td><input type="text" name="name" /></td></tr>
	<tr><td>{t}Coordinates:{/t}</td><td>
    	<input type="text" name="name" style="width:25px;text-align:right" maxlength="3"/>
        <input type="text" name="name" style="width:25px;text-align:right" maxlength="3"/>
        <input type="text" name="name" style="width:25px;text-align:right" maxlength="3"/>
        <input type="text" name="name" style="width:25px;text-align:right" maxlength="3"/>
    </td></tr>
	<tr><td>{t}Cells:{/t}</td><td>
    	<input type="text" name="name" style="width:25px;text-align:right" maxlength="2"/>
        <input type="text" name="name" style="width:25px;text-align:right" maxlength="2"/>
    </td></tr>
<input type="submit" value="{t}upload{/t}" />&nbsp;
</form>


</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}