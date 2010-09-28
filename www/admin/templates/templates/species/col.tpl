{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
	Enter the name of the taxon you wish to retrieve the taxon-tree for, and click 'retrieve'.
</p>
<p>
	Be advised, the retrieval process might take quite some time, especially with higher taxa.
	It is recommended not to attempt to retrieve anything above the rank of genus.
</p>
<p>
	If you require the data of an entire family, please check the 'retrieve a single level of child taxa only'-option. The application will retrieve only genera, the first level beneath family. Subsquently, you can retrieve data for each genus by clicking its name. The
	data for that genus and its child taxa will be displayed in a separate list, so you do not lose the family-data.
</p>
<p>
	Taxon: <input type="text" id="taxon_name" name="taxon_name" value="{$taxon_name}" />
	<input value="retrieve" type="button" onclick="taxonGetCoL()" />
	<input value="abort retrieval" type="button" onclick="allAjaxAbort();allHideLoadingDiv();" /><br />
	<label><input type="checkbox" id="single-child-level" checked="checked" />retrieve a single level of child taxa only</label>
</p>
<br />
<div id="col-result-instruction" class="text-block" style="visibility:hidden">
<p>
	Check the taxa you wish to import, and click save: <input type="button" value="save" onclick="taxonSaveCoLResult()" /><br />
	Duplicates of the taxa already in your project's database will not be stored.
</p>
</div>
<div id="col-result" class="text-block"></div>
<div id="col-subresult" class="text-block" style="position:relative;"></div>

</div>

{include file="../shared/admin-messages.tpl"}

{include file="../shared/admin-footer.tpl"}
