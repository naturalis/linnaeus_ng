{include file="../shared/admin-header.tpl"}

{include file="../shared/left_column_tree.tpl"}
{include file="../shared/left_column_admin_menu.tpl"}

<script type="text/javascript" src="{$baseUrl}admin/vendor/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="{$baseUrl}admin/javascript/nsr_passport.js"></script>

<div id="page-container-div">

<div id="page-main">

<h2><span style="font-size:12px;font-style:normal">{t}concept{/t}:</span> {$concept.taxon}</h2>
<h3>{t}passport{/t}</h3>
<a class="toggle-all" href="#" onclick="$('.passport-toggles').trigger('click');$('.toggle-all').toggle();return false;">{t}show all{/t}</a>
<a class="toggle-all" href="#" onclick="$('.passport-toggles').trigger('click');$('.toggle-all').toggle();return false;" style="display:none" href="">{t}hide all{/t}</a>

<p>

<form>
<input type="hidden" id="taxon_id" value="{$concept.id}" />
<input type="hidden" id="rnd" name="rnd" value="{$rnd}" />
<input type="hidden" id="language_id" name="activeLanguage" value="{$activeLanguage}" />
	<ul>
    {assign var=hasObsolete value=false}
	{foreach from=$tabs item=v key=k}
	{if !($v.obsolete && $v.content|@strlen==0) && $v.suppress!=1}
	<li>
		<span class="passport-title">

        {if $v.type=='auto'}
        <span title="{t}automatic tab{/t}">&lt; {if $v.page}{$v.page}{else}{$v.tabname}{/if} &gt;</span>
        {else}

        	{if $v.always_hide}( {/if}<a href="#" class="passport-toggles" onclick="$('#body{$k}').toggle();return false;" {if $v.always_hide}title="{t}hidden tab{/t}"{/if}>{$v.title}</a>{if $v.always_hide} ){/if}
            {if $v.obsolete}{assign var=hasObsolete value=true}<span class="passport-waarschuwing">{t}Obsolete passport entry{/t}</span>{/if}
            <span id="indicator{$k}">
	            {if $v.content|@strlen>0 && $v.publish==1}
                <span title="{t}has content, is published{/t}" class="passport-published">{$v.content|@strlen} {t}characters in field{/t}</span>
                {elseif $v.content|@strlen>0 && $v.publish!=1}
                <span title="{t}has content, not published (invisible){/t}" class="passport-unpublished">{$v.content|@strlen} {t}characters in field{/t}</span>
                {else}
                <span title="{t}no content (invisible){/t}" class="passport-leeg">{t}empty{/t}</span>
                {/if}
            </span>
			<a
            	href="/linnaeus_ng/app/views/species/nsr_taxon.php?id={$concept.id}&cat={$v.id}&epi={$session.admin.project.id}"
                class="edit"
                style="margin:0;{if $v.external_reference}font-size:1em;{/if}"
                target="view"
                title="{t}view in site (new window){/t}{if $v.external_reference}; {t}external reference{/t}{/if}">{if $v.external_reference}&nearr;{else}&rarr;{/if}</a><br />
		{/if}

		</span>
		<div class="passport-body" id="body{$k}">

            <span class="passport-content" id="content{$k}">{$v.content}</span>

			<a href="#" class="edit" id="edit{$k}" onclick="openeditor(this);return false;" style="margin-left:0;">edit</a>
            <div id="button-container{$k}" class="button-container" style="display:none">
            {if $can_publish}
            <input id="publish{$k}" type="checkbox" value="publiceren" {if $v.publish==1}checked="checked"{/if} />{t}publish{/t}?
            {else}
            ({if $v.publish==1}{t}published{/t}{else}{t}unpublished{/t}{/if})
			{/if}
            </p>
            <p>
            <input id="save{$k}" value="opslaan" type="button" onclick="saveeditordata(this);">
            <input id="close{$k}" value="sluiten" type="button" onclick="closeeditor(this);">
            {*<!--input id="revert{$k}" value="oorspronkelijke tekst" type="button" onclick="reverttext(this);"-->*}
            <input id="page{$k}" value="{$v.id}" type="hidden" />
            <span id="message{$k}"></span>
            </p>
            </div>
        </div>
	</li>
    {/if}
	{/foreach}
	</ul>
</form>

{if $hasObsolete}
<p>
<span class="passport-waarschuwing">{t}Obsolete passport entries{/t}</span><br/>
{t}These are old passport entries that overlap with newer ones, or are obsolete.{/t}
<br />
{t}For the sake of consistency, please move the content from old to new entries following this schema:{/t}
<ul>
{foreach $obsolete_tabs v k}
    <li>{$v.old} > {if $v.new}{$v.new}{else}{t}(obsolete){/t}{/if}</li>
{/foreach}
</ul>
</p>
{/if}


</p>

<!--
<p>
	<a href="paspoort_meta.php?id={$concept.id}" class="edit"  style="margin:0">{t}metadata{/t}</a><br />
	<a id="media-overlay-links" href="#" class="edit" style="margin:0">{t}media overlay{/t}</a><br>

</p>
-->
<p>
	<br>Language:
	<select name="languageid" id="languagechanger" onchange="changeLanguage(this);">
        {foreach from=$languages item=l key=i}
			<option value="{$l.language_id}" {if ($l.language_id == $activeLanguage)}selected="selected"{/if}>{$l.language}</option>
        {/foreach}
	</select></p>

<p>
	<a href="taxon.php?id={$concept.id}&amp;noautoexpand=1">{t}back{/t}</a>
</p>

</div>

<script>
$(document).ready(function(e)
{
	{if $adminMessageFadeOutDelay}
	messageFadeOutDelay={$adminMessageFadeOutDelay};
	{/if}

	{foreach from=$tabs item=v key=k}
	currentpublish[{$k}]={if $v.publish==1}true{else}false{/if};
	{/foreach}

	$('#media-overlay-links').click(function(e)
	{
		e.preventDefault();
		$.get("../media/media-overlay.php", function(r)
		{
			prettyDialog({
				content : r,
				width: 1000,
				height: 800,
				title: _('Browse media')
			});
		});
	});
});
</script>

{include file="../shared/admin-messages.tpl"}

</div>

{include file="../shared/admin-footer.tpl"}