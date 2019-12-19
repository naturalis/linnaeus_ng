<h4>{include file="../shared/admin-header.tpl"}
  {include file="../shared/admin-messages.tpl"}
</h4>

<div id="page-main">
{if $processed}
	<form method="post" id="theForm" action="nbc_determinatie_6.php">
	<input type="hidden" name="rnd" value="{$rnd}" />
	<input type="hidden" id="action" name="action" value="save" />
	<p>
    
    Settings are now fixed. Click "next".
    
    {* if !$projectExists}
    <table>
		<!-- tr><td>Author:</td><td><input type="text" name="settings[source_author]" value="{$source_author}" /></td></tr>
		<tr><td>Title of source:</td><td><input type="text" name="settings[source_title]" value="{$source_title}" /></td></tr>
		<tr><td>Photo credit:</td><td><input type="text" name="settings[source_photocredit]" value="{$source_photocredit}" /></td></tr>
		<tr><td>Source URL:</td><td><input type="text" name="settings[source_url]" value="{$source_url}" /></td></tr -->
		<tr><td>NBC image root:</td><td><input type="text" name="settings[nbc_image_root]" value="{$nbc_image_root}" /></td></tr>
		<!-- tr><td>Skin name:</td><td><input type="text" name="settings[skin]" value="{$skin}" /></td></tr -->
		<tr><td>Items per line:</td><td><input type="text" name="settings[matrix_items_per_line]" value="{$matrix_items_per_line}" /></td></tr>
		<tr><td>Items per page:</td><td><input type="text" name="settings[matrix_items_per_page]" value="{$matrix_items_per_page}" /></td></tr>
		<!-- tr><td>State images per row:</td><td><input type="text" name="settings[matrix_state_image_per_row]" value="{$matrix_state_image_per_row}" /></td></tr -->
		<!-- tr><td>State images max height:</td><td><input type="text" name="settings[matrix_state_image_max_height]" value="{$matrix_state_image_max_height}" />(empty for no scaling)</td></tr -->
		<tr style="vertical-align:top"><td>Browse style:</td>
        	<td>
                <label><input type="radio" name="settings[matrix_browse_style]" value="expand" checked="checked"/>expand</label><br />
                <label><input type="radio" name="settings[matrix_browse_style]" value="paginate" />paginate</label><br />
                <label><input type="radio" name="settings[matrix_browse_style]" value="" />(ignore)</label>
            </td>
		</tr>
	</table>
    {else}
    Importing into an existing project, skipping new settings.
    {/if *}
	</p>
	<p>
	<!-- input type="submit" value="{if $projectExists}{t}Next{/t}{else}{t}Save{/t}{/if}" -->
	<input type="submit" value="{t}Next{/t}">
	</p>
	</form>
	

{else}
<p>
	<form method="post" action="nbc_determinatie_5.php">
    {if $matrixExists}
    <p>
        <span class="message-error">A matrix with the name "{$matrix}" already exists. Matrix names need to be unique; please specify how to treat this import:</span><br />
        <input type="radio" id="id1" name="data_treatment" value="replace_data" checked="checked" /></td><td><label for="id1">import into existing matrix, replacing existing data</label><br />
        <input type="radio" id="id3" name="data_treatment" value="new_matrix" /></td><td><label for="id3">create a new matrix with the title "{$suggestedTitle}"</label><br />
        If you wish to create a new project with a different title, alter the title in your CSV-file and <a href="nbc_determinatie_1.php?action=new">reload the file</a>.<br />
    </p>
    {/if}
    
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