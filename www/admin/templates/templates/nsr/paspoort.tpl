{include file="../shared/admin-header.tpl"}

<script type="text/javascript" src="../../../admin/javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../admin/javascript/nsr_passport.js"></script>

<style>
.passport-title {
	font-size:12px;
}
.passport-body {
	display:none;
	border-bottom:1px solid #999;
	-padding: 5px 10px 0 10px;
	margin:3px 0 15px 5px;
}
.passport-body-no-line {
	border:0px;

}
.button-container {
	padding:10px 0 5px 0;
}
ul {
	list-style: inside square;
}
</style>

<div id="page-main">

<h2><span style="font-size:12px;font-style:normal">concept:</span> {$concept.taxon}</h2>
<h3>paspoorten</h3>

<p>
<form>
<input type="hidden" id="taxon_id" value="{$concept.id}" />
	<ul>
	{foreach from=$tabs item=v key=k}
	<li>
		<span class="passport-title">
        	<a href="#" onclick="$('#body{$k}').toggle();return false;">{$v.title}</a>
            <span id="indicator{$k}">{if $v.content|@strlen>0} *{/if}</span>
			<a href="/linnaeus_ng/app/views/species/nsr_taxon.php?id={$concept.id}&cat={$v.id}&epi={$session.admin.project.id}" class="edit"  style="margin:0" target="nsr" title="paspoort bekijken in het Soortenregister (nieuw venster)">&rarr;</a><br />
		</span>
		<div class="passport-body" id="body{$k}">
            <span class="passport-content" id="content{$k}">{$v.content}</span>
			<a href="#" class="edit" id="edit{$k}" onclick="openeditor(this);return false;" style="margin-left:0;">edit</a>
            <div id="button-container{$k}" class="button-container" style="display:none">
            <input id="save{$k}" value="opslaan" type="button" onclick="saveeditordata(this);">
            <input id="close{$k}" value="sluiten" type="button" onclick="closeeditor(this);">
            <input id="revert{$k}" value="oorspronkelijke tekst" type="button" onclick="reverttext(this);">
            <input id="page{$k}" value="{$v.id}" type="hidden" />
            <span id="message{$k}"></span>
            </div>
        </div>
	</li>
	{/foreach}
	</ul>
</form>
</p>

<p>
	<a href="taxon.php?id={$concept.id}">terug</a>
</p>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}