<div id="top-strip">
    <p>
 <!--		
		{if $languages|@count>1}
		
	    <span id="language-change">
	         <select id="languageSelect" onchange="doLanguageChange()">
	        {foreach from=$languages key=k item=v}
	            <option value="{$v.language_id}"{if $v.language_id==$currentLanguageId} selected="selected"{/if}>{$v.language} {if $v.def_language==1}*{/if}</option>
	        {/foreach}
	        </select>
	    </span>
		{/if}

 -->


    
    	<a href="../../../">{t}projects{/t}</a> | 
		<a href="../../../admin/views/users/login.php">{t}login{/t}</a> | 
    	{t}help{/t} ({t}not yet available{/t}) | 
    	<a href="http://www.eti.uva.nl/support/contact.php">{t}contact{/t}</a>
 
 		{if $languages|@count>1} | 
        {foreach from=$languages key=k item=v}
        	{if $v.language_id!=$currentLanguageId}<span class="flag_link" onclick="doLanguageChange({$v.language_id})">{/if}
        	<img src="{$session.app.project.urls.systemMedia}language_flags/{$v.iso2}.png">
        	{if $v.language_id!=$currentLanguageId}</span>{/if}
         {/foreach}
 		{/if}
 		
        </p>
</div>


