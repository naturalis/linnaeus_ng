{include file="../shared/admin-header.tpl"}

<style>
.remark {
	color:#888;
	font-size:0.8em;
}
.taxon-list {
	width:300px;
	height:150px;
	overflow-y:scroll;
}
</style>

{if !$suppressPath}{include file="_keypath.tpl"}{/if}

{include file="../shared/admin-messages.tpl"}

<div id="page-main">

<fieldset>
    <legend id="key-step-title">{t}Step{/t} {$step.number}{if $step.title}: {$step.title}{/if}</legend>

    {$step.content}

    {if $step.image}
    <p>
        <img src="{$session.admin.project.urls.project_media}{$step.image}" /><br />
        <span style="color:red">Please note: this image is a legacy feature inherited from Linnaeus 2. It cannot be changed.</span><br />
        <a href="#" onclick="keyDeleteImage();return false;">{t}delete image{/t}</a> | 
        <a href="#" onclick="keyDeleteAllImages()return false;;">{t}delete all images{/t}</a>
    </p>
    {/if}

    <p>
        <a href="step_edit.php?id={$step.id}">{t}edit{/t}</a> |
        <a href="#" onclick="keyDeleteKeyStep();return false;">{t}delete{/t}</a> |
        <a href="preview.php?step={$step.id}">{t}preview{/t}</a>
    </p>

    <span class="remark">
        Steps leading to this one:
        <ul style="list-style-position:inside;padding:0;margin-top:0">
        {foreach from=$stepsLeadingToThisOne item=v}
            <li><a href="step_show.php?id={$v.id}">Step {$v.number}{if $v.title}: {$v.title}{/if}</a></li>
        {/foreach}
        </ul>
    </span>

</fieldset>

<br />

<fieldset>
    <legend id="key-step-choices">{t}Choices{/t}</legend>
        <table>
        <tr>
            <th style="text-align:right">#</th>
            <th style="width:450px;">{t}choice title{/t}</th>
            <th style="width:90px;">{t}image{/t}</th>
            <th style="width:100px;">{t}choice leads to{/t}</th>
            <th style="width:80px;" class="key-choice-arrow">{t}move{/t}</th>
            <th style="width:30px;"><!-- span class="a" onclick="keyShowChoiceDetails(this,'all')">{t}(show all){/t}</span --></th>
            <th style="width:30px;"></th>
        </tr>

        {foreach from=$choices item=v key=k}
        <tr class="tr-highlight" style="vertical-align:top">
            <td class="key-choice-number">{$v.marker}.</td>
            <td class="key-choice-title">{$v.choice_txt}</td>
            <td>
            {if $v.choice_img}
                <img
                onclick="allShowMedia('{$session.admin.project.urls.project_media}{$v.choice_img}','{$v.choice_img}');" 
                src="{$session.admin.project.urls.project_media}{$v.choice_img}"
                class="key-choice-image-small" />
            {/if}
            </td>
        
            <td class="key-choice-target">
                &rarr;
                {if $v.res_keystep_id!=''}
                    {if $v.res_keystep_id!='-1'}
                    <a href="step_show.php?choice={$v.id}&id={$v.res_keystep_id}">
                        {t}Step{/t} {if $v.target_number}{$v.target_number}: {/if}{$v.target}
                    </a>
                    {else}
                    <a href="step_edit.php?ref_choice={$v.id}">
                        {$v.target}
                    </a>
                    {/if}
                {elseif $v.res_taxon_id!=''}
                    {t}Taxon:{/t}
                    <a href="../species/taxon.php?id={$v.res_taxon_id}">
                        {$v.target}
                    </a>
                {else}
                    {$v.target}
                {/if}
            </td>
            <td class="key-choice-arrow">
                {if $k<$choices|@count-1}
                <a href="#" onclick="$('#move').val({$v.id});$('#direction').val('down');$('#moveForm').submit();return false;">&darr;</a>
                {else}
                <a href="#" onclick="$('#move').val({$v.id});$('#direction').val('up');$('#moveForm').submit();return false;">&uarr;</a>
                {/if}
            </td>
            <td class="key-choice-edit">
                <a href="#" onclick="$('#id2').val({$v.id});$('#choiceForm').submit();return false;">{t}edit{/t}</a>
            </td>
            <td class="key-choice-edit">
                <a href="#" onclick="keyChoiceDelete({$v.id});return false;">{t}delete{/t}</a>
            </td>
            {*
            <td class="key-choice-edit">
                <a href="step_edit.php?insert={$v.id}" title="{t}insert step between choice and target{/t}">{t}insert step{/t}</a>
            </td>
            *}
        </tr>
        {/foreach}
        {if $choices|@count==0}
        <tr>
            <td colspan="8"><span class="key-no-choices">{t}(none){/t}</span></td>
        </tr>
        {/if}
        <tr>
            <td colspan="8">&nbsp;</td>
        </tr>
        <tr>
            {if $choices|@count < $maxChoicesPerKeystep}
            <td colspan="8">
                <a href="#" onclick="$('#choiceForm').submit();return false;">{t}add new choice{/t}</a>
            </td>
            {else}
            <td colspan="8">
                {t _s1=$maxChoicesPerKeystep}(you have reached the maximum of %s choices per step){/t}
            </td>
            {/if}
        </tr>
    </table>
</fieldset>
</div>

<div id="key-taxa-list-remain">

    <table>
	    {if !$suppressDivision}
        <tr style="vertical-align:top">
            <td>
                <fieldset class="taxon-list">
                    <legend id="key-taxa-list-remain-header">{t}Remaining taxa{/t} ({$taxonDivision.remaining|@count})</legend>
                    {foreach from=$taxonDivision.remaining item=v key=k}
                    &#149;&nbsp;{$v}<br />
                    {/foreach}
                </fieldset>
            </td>
            <td>
                <fieldset  class="taxon-list">
                    <legend id="key-taxa-list-remain-header">{t}Excluded taxa{/t} ({$taxonDivision.excluded|@count})</legend>
                    {foreach from=$taxonDivision.excluded item=v key=k}
                    &#149;&nbsp;{$v}<br />
                    {/foreach}
                </fieldset>
            </td>
        </tr>
        {/if}
        <tr style="vertical-align:top">
            <td colspan="2">
            	<form method="post" id="suppressForm">
				<input type="hidden" name="action" value="suppress_division" />
				<label style="margin-left:2px">
                	<input type="checkbox" name="suppress_division" {if $suppressDivision}checked="checked"{/if} onchange="$('#suppressForm').submit();" />
                	{t}do not show remaining and excluded taxa (enhances performance of this page; has no effect on the front-end).{/t}
				</label>
                </form>
            </td>
        </tr>
    </table>

</div>

<form method="get" action="" id="moveForm">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" value="{$step.id}" />
<input type="hidden" name="move" id="move" value="" />
<input type="hidden" name="direction" id="direction" value="" />
</form>

<form method="get" action="choice_edit.php" id="choiceForm">
<input type="hidden" name="id" id="id2" value="" />
<input type="hidden" name="step" value="{$step.id}" />
</form>

<form method="get" action="choice_edit.php" id="delChoiceForm">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" id="id3" value="" />
<input type="hidden" name="action" value="delete" />
</form>

{include file="../shared/admin-footer.tpl"}
