<div id="top-strip">
    <span id="language-change">
        {if $languages|@count>1}
        <select id="languageSelect" onchange="doLanguageChange()">
        {foreach from=$languages key=k item=v}
            <option value="{$v.language_id}"{if $v.language_id==$currentLanguageId} selected="selected"{/if}>{$v.language} {if $v.def_language==1}*{/if}</option>
        {/foreach}
        </select>
        {/if}
    </span>

    <p><a href="../../../">{t}projects{/t}</a> | 
       <a href="../../../admin/views/users/login.php">{t}login{/t}</a> | 
    	{t}help{/t} ({t}not yet available{/t}) | 
    	<a href="http://www.eti.uva.nl/support/contact.php">{t}contact{/t}</a></p>
</div>


