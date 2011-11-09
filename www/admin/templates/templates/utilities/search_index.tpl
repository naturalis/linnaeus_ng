{include file="../shared/admin-header.tpl"}
<div id="page-main">
<form id="theForm" method="post" action="">
<fieldset><legend>Find</legend>
Search for: <input type="text" id="search" name="search" value="" />*<br />
In modules:<br />
<input type="checkbox" name="modules[]" value="all" />all<br />
{foreach from=$modules item=v}
<input type="checkbox" name="modules[]" value="{$v.controller}" />{$v.module}<br />
{/foreach}
</fieldset>
<fieldset><legend>Replace</legend>
<input type="checkbox" id="replcaeToggle" onchange="" />Replace<br />
<span>Replace with:</span><input type="text" id="replace" name="replace" value=""  /><br />
Replace options:<br />
<input type="radio" name="options" id="optionsAll" value="all"  />Replace all<br />
<input type="radio" name="options" id="optionsNext" value="next"  />Find next<br />
</fieldset>
<input type="submit" value="search" />
</form>
</div>
{include file="../shared/admin-footer.tpl"}
