{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if $processed}
	<form method="post" action="nbc_determinatie_6.php">
	<input type="hidden" name="rnd" value="{$rnd}" />
	<p>
    <table>
		<tr><td>Author:</td><td><input type="text" name="settings[source_author]" value="{$source_author}" /></td></tr>
		<tr><td>Title of source:</td><td><input type="text" name="settings[source_title]" value="{$source_title}" /></td></tr>
		<tr><td>Photo credit:</td><td><input type="text" name="settings[source_photocredit]" value="{$source_photocredit}" /></td></tr>
		<tr><td>Source URL:</td><td><input type="text" name="settings[source_url]" value="{$source_url}" /></td></tr>
		<tr><td>NBC image root:</td><td><input type="text" name="settings[nbc_image_root]" value="{$nbc_image_root}" /></td></tr>
		<tr><td>Skin name:</td><td><input type="text" name="settings[skin]" value="{$skin}" /></td></tr>
		<tr><td>Items per line:</td><td><input type="text" name="settings[matrix_items_per_line]" value="{$matrix_items_per_line}" /></td></tr>
		<tr><td>Items per page:</td><td><input type="text" name="settings[matrix_items_per_page]" value="{$matrix_items_per_page}" /></td></tr>
		<tr><td>State images per row:</td><td><input type="text" name="settings[matrix_state_image_per_row]" value="{$matrix_state_image_per_row}" /></td></tr>
		<tr><td>State images max height:</td><td><input type="text" name="settings[matrix_state_image_max_height]" value="{$matrix_state_image_max_height}" />(empty for no scaling)</td></tr>
		<tr style="vertical-align:top"><td>Browse style:</td>
        	<td>
                <label><input type="radio" name="settings[matrix_browse_style]" value="expand" checked="checked"/>expand</label><br />
                <label><input type="radio" name="settings[matrix_browse_style]" value="paginate" />paginate</label><br />
                <label><input type="radio" name="settings[matrix_browse_style]" value="" />(ignore)</label>
            </td>
		</tr>
	</table>
	</p>
	<p>
	<input type="submit" value="{t}Save{/t}">
	</p>
	</form>
	

{else}
<p>
	<form method="post" action="nbc_determinatie_5.php">
	Assign the correct character types and click the button to import the matrix data.
	<p>
	<table>
	{foreach from=$characters item=v}
	{if $v.group!='hidden'}
	<tr class="tr-highlight">
		<td>{if $v.group}{$v.group}: {/if}{$v.code}</td>
		<td>
			<select name="char_type[{$v.code}]">
				<option value="media">media</option>
				<option value="range">range</option>
				<option value="text">text / list</option>
			</select>
		</td>
	</tr>
	{/if}
	{/foreach}
	</table> 
	</p>
	
	<input type="hidden" name="rnd" value="{$rnd}" />
	<input type="hidden" name="action" value="matrix" />
	<input type="submit" value="Import matrix data">
	</form>
</p>
{/if}
<p>
	<a href="nbc_determinatie_4.php">Back</a>
</p>

</div>

{include file="../shared/admin-footer.tpl"}