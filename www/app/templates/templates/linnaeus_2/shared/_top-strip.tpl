{*$session|@print_r*}
<div id="top-strip">
    <div id="language-change">
        {if $languages|@count>1}
        <select id="languageSelect" onchange="doLanguageChange()">
        {foreach from=$languages key=k item=v}
            <option value="{$v.language_id}"{if $v.language_id==$currentLanguageId} selected="selected"{/if}>{$v.language} {if $v.def_language==1}*{/if}</option>
        {/foreach}
        </select>
        {/if}
    </div>

    <p><a href="../../../">projects</a> | 
       <a href="../../../admin/views/users/login.php">login</a> | 
    	help (not yet available) | 
    	<a href="http://www.eti.uva.nl/support/contact.php">contact</a></p>
</div>


