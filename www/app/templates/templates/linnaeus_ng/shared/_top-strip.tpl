<div id="top-strip">
    <p>
    	<a href="../../../">{t}projects{/t}</a> |
		<a href="../../../admin/views/users/login.php">{t}login{/t}</a> |
		<span class="decode">{$contact}</span>

 		{if $languages|@count>1} |
        {foreach $languages v k}
        	{if $v.language_id!=$currentLanguageId}<span class="flag_link" onclick="doLanguageChange({$v.language_id})">{/if}
        	<img src="{$projectUrls.systemMedia}language_flags/{$v.iso2}.png">
        	{if $v.language_id!=$currentLanguageId}</span>{/if}
         {/foreach}
 		{/if}
   </p>
</div>


