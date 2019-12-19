{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if $mId}
<span class="a" onclick="$('#id').val({$mId});$('#action').val('activate');$('#theForm').submit()">go to new matrix</span>
<form id="theForm" method="post" action="matrices.php">
<input type="hidden" name="id" id="id" value="" />
<input type="hidden" name="action" id="action" value="" />
</form>
{else}
<p>
	NEXUS IMPORT<br />
	This importer allows you to import Nexus-files exported from Linnaeus 2. It has not been tested with 
	Nexus-files from other sources. When exporting from Linnaeus 2, make sure to choose the version with tabs, not the "standard" version.
</p>
<p>
	Import creates a new matrix with the name of the .NEX file minus its extension. You can later change this.
	The importer does not check whether another matrix with the same name already exists, but simply creates
	another one when there is.
</p>
<p>
	The importer checks the taxon names in the matrix against the list of taxa already present in the species module
	of the project.<br />
	If that fails, it attempts to clean up the taxon name by stripping out any ranks that might be present in the 
	taxon name (for instance reducing "Genus Flickingeria" to "Flickingeria"), as LNG stores <i>all</i> taxa without the
	rank. While cleaning up, the importer only attempts to remove English names from the taxon names. If the ranks in
	your file are in another language, you will have to remove the ranks' names manually from the matrix in the file.<br />
	If the cleansed name cannot be found either, it is assumed that it does not exist in the species module. The
	user is notified and the taxon, as well as its states, are discarded. The importer does <u>not</u> create new taxa.
</p>
<p>
	The file contains some meta-information on the matrix, which is only partly used:
	<ul>
		<li><b>DIMENSIONS  NTAX=<i>x</i> NCHAR=<i>y</i></b>: importer checks whether the actual number of taxa and 
		characters match these numbers. If they do not, a warning is generated, but import continues.</li>
		<li><b>FORMAT MISSING=?  GAP=- SYMBOLS= " 0 1 2 3 4";</b>: the importer does not match the actual values 
		against these standards. The use of '?' for missing values is hardcoded in the code, the symbol for GAP has
		never been encountered (or noticed), the valid values for SYMBOLS are simply ignored.</li>
	</ul>
	Multiple states for one combination of taxon and character are specified as "{023}" (for 0,2 & 3). It is unclear 
	what notation is used when the index becomes larger than 9; the importer will always split the string into
	single digits.
</p>
<form method="post" action="" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
	<input name="uploadedfile" type="file" />
	<p>
	<input type="submit" value="{t}upload{/t}" />
	</p>
</form>
{/if}
</div>
{include file="../shared/admin-footer.tpl"}
