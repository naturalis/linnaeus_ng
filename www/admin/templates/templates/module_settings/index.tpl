{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h3>Settings</h3>

<p>
    <a href="values.php?id={$smarty.const.GENERAL_SETTINGS_ID}">General settings</a>
    {if $isSysAdmin} (<a href="settings.php?id={$smarty.const.GENERAL_SETTINGS_ID}">edit settings</a>){/if}
</p>

Modules:
<ul>
{foreach $modules v}
   	{if $v.id!=$smarty.const.GENERAL_SETTINGS_ID}
   	{if $v.num_of_settings>0 || $isSysAdmin}
	<li>
    	{if $v.num_of_settings==0}
	    	<span title="module has no settings">{$v.module}</span>
		{else}    
    		<a href="values.php?id={$v.id}" title="edit setting values">{$v.module}</a>
        {/if}

       	{if $isSysAdmin} (<a href="settings.php?id={$v.id}">edit settings</a>){/if}
	</li>
	{/if}
	{/if}
{/foreach}
</ul>

<p>
<a href="convert_old_settings.php">convert old settings</a>
</p>


</div>

{include file="../shared/admin-footer.tpl"}
