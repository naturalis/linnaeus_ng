{include file="../shared/admin-header.tpl"}
<script type="text/javascript" src="{$baseUrl}admin/vendor/tinymce/jquery.tinymce.min.js" ></script>
{include file="_keypath.tpl"}

<div id="page-main">


<fieldset>
<legend>{t}Editing step{/t} {$step.number}, {t}choice{/t} {$choice.marker}</legend>

<form method="post" action="" id="theForm" enctype="multipart/form-data">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" value="{$choice.id}" />
<input type="hidden" name="action" id="action" value="save" />


{foreach $session.admin.project.languages v k}
	<p>
    {$v.language}
    <textarea name="choice_txt[{$v.language_id}]" style="width:400px;height:250px;"/>
    {$choice.content[$v.language_id].choice_txt}
	</textarea>
    </p>
{/foreach}


	{if $use_media}

        <p>
        {t}Image{/t}<br />
        {if $choice.choice_img}
            <img
                onclick="allShowMedia('{$choice.choice_img}?rnd={$rnd}','');"
                src="{$choice.choice_img}?rnd={$rnd}"
                class="key-choice-image-normal" /><br />
            <span class="a" onclick="keyDeleteImage();">{t}detach image{/t}</span>
            {if $choice.choice_image_params!=''}
                <br />
                <span style="color:red">
                    {t}Please note: this image has specific attributes for size and positioning, which were inherited from Linnaeus 2. These cannot be changed, and will be erased if you delete the image.{/t}
                </span>
            {/if}
        {else}
            <a href="../media/upload.php?item_id={$item_id}&amp;module_id={$module_id}">{t}Upload{/t}</a> or
            <a href="../media/select.php?item_id={$item_id}&amp;module_id={$module_id}">{t}attach media{/t}</a> to this page.
        {/if}
        </p>

    {/if}

	<p>
    {t}Target{/t}
    {t}step{/t}
    <select name="res_keystep_id" id="res_keystep_id" onchange="keyCheckTargetIntegrity(this)">
        <option value="-1">{t}new step{/t}</option>
        <option value="0"{if $choice.res_taxon_id!=null} selected="selected"{/if}>{t}(none){/t}</option>
    {if $steps|@count>0}
        <option value="-1" disabled="disabled">
        </option>
    {/if}
        {foreach $steps v i}
        <option value="{$v.id}"{if $v.id==$choice.res_keystep_id} selected="selected"{/if}>{$v.number}. {$v.title}</option>
        {/foreach}
    </select>

	or

	{t}taxon{/t}

        <select name="res_taxon_id" id="res_taxon_id" onchange="keyCheckTargetIntegrity(this)">
            <option value="0">{t}(none){/t}</option>
            <option disabled="disabled">
            </option>
            {foreach from=$taxa key=k item=v}
            {if $v.keypath_endpoint==1}
            <option value="{$v.id}"{if $v.id==$choice.res_taxon_id} selected="selected"{/if} class="key-taxa-list{if $v.id==$v.res_taxon_id}-remain{/if}">
                {$v.taxon} ({$v.rank})
            </option>
            {/if}
            {/foreach}
        </select>
	</p>

    <input type="submit" value="{t}save{/t}" />
    <input type="button" onclick="window.open('step_show.php?id={$choice.keystep_id}','_self');" value="{t}back{/t}" />
</fieldset>
</form>

</div>

{include file="../shared/admin-messages.tpl"}


<script type="text/javascript">
$(document).ready(function()
{
	initTinyMce(false,false);
	{if $choice.res_keystep_id!=null}keyCurrentTargetStep = {$choice.res_keystep_id};{/if}
	{if $choice.res_taxon_id!=null}keyCurrentTargetTaxon = {$choice.res_taxon_id};{/if}
	allPrevValSetUp('res_keystep_id');
	allPrevValSetUp('res_taxon_id');
});

</script>

{include file="../shared/admin-footer.tpl"}
