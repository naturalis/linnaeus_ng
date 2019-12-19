{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
	{t}Enter the name of the taxon you wish to retrieve the taxon-tree for, and click 'retrieve'.{/t}
</p>
<p>
	{t}Be advised, the retrieval process might take quite some time, especially with higher taxa. It is recommended not to attempt to retrieve anything above the rank of genus.{/t}
</p>
<p>
	{t}If you require the data of an entire family, please check the 'retrieve a single level of child taxa only'-option. The application will retrieve only genera, the first level beneath family. Subsquently, you can retrieve data for each genus by clicking its name. The data for that genus and its child taxa will be displayed in a separate list, so you do not lose the family-data.{/t}
</p>
<p>
	<b>{t}Please be aware that only those taxa will saved that are of a rank that you have defined for your project.{/t}</b>
	<a href="ranks.php">{t}Define ranks.{/t}</a>
</p>

<p>
	{t}Taxon:{/t} <input type="text" id="taxon_name" name="taxon_name" value="{$taxon_name}" />
	<input value="{t}retrieve{/t}" type="button" onclick="taxonGetCoL()" />
	<input value="{t}abort retrieval{/t}" type="button" onclick="allAjaxAbort();allHideLoadingDiv();" /><br />
	<label><input type="checkbox" id="single-child-level" checked="checked" />{t}retrieve a single level of child taxa only{/t}</label>
</p>
<br />
<div id="col-result-instruction" class="text-block" style="visibility:hidden">
<p>
	{t}Check the taxa you wish to import, and click save:{/t} <input type="button" value="{t}save{/t}" onclick="taxonSaveCoLResult()" /><br />
	{t}Duplicates of the taxa already in your project's database will not be stored.{/t}
</p>
</div>
<div id="col-result" class="text-block"></div>
<div id="col-subresult" class="text-block" style="position:relative;"></div>

</div>

{include file="../shared/admin-messages.tpl"}

{include file="../shared/admin-footer.tpl"}
