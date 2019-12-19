<body><div id="body-container"><a name="body-top"></a>

<div id="header-container">

	<a href="{$baseUrl}admin/views/utilities/admin_index.php">
		<img src="{$baseUrl}admin/media/system/logo_linnaeus_ng.png" id="lng-logo">
	</a>

	<div class="header-user">
		{if $session.admin.user._logged_in}
		{t}Logged in as{/t}
		<a href="{$baseUrl}admin/views/users/edit.php?id={$session.admin.user.id}">
            {$session.admin.user.first_name} {$session.admin.user.last_name}
        </a>
        (<a href="{$baseUrl}admin/views/users/logout.php">{t}Log out{/t}</a>)
		{/if}
		{if $cronNextRun}{include file="../shared/_countdown.tpl"}{/if}
	</div>
</div>

<div id="page-container">

<div id="page-header-titles">
	<span id="page-header-title">{$app.name}</span>

	{if $wikiUrl}<br>
	<div style="float:right;margin-top:auto;margin-bottom:auto">
    <a href="{$wikiUrl}" target="_blank">
    	<img src="../../media/system/qm_grey.png" style="width:15px;border:0" />
	</a>
    </div>
	{/if}

	<br />

    <div id="breadcrumbs">

        {if $hideControllerPublicName}
            <span class="crumb"><a href="{$breadcrumbs[0].url}">{$breadcrumbs[0].name}</a></span>
            <span class="crumb-arrow">&rarr;</span>
            <span class="crumb"><a href="{$breadcrumbs[1].url}">{$breadcrumbs[1].name}</a></span>
        {else}
            {foreach $breadcrumbs v k}
                {if $n==$breadcrumbs|@count || $breadcrumbs[$k+1].name==''}
	                <span id="crumb-current">{$v.name}</span>
                {else}
                    <span class="crumb"><a href="{$v.url}">{$v.name}</a></span>
                    <span class="crumb-arrow">&rarr;</span>
                {/if}
            {/foreach}
        {/if}

        {if $session.admin.project.id}
        <a href="../../../app/views/linnaeus/set_project.php?p={$session.admin.project.id}" style="color:#999;margin-left:10px" target="_project">view</a>
        {/if}

        {if $isMultiLingual && $uiLanguages|@count>1}
            <span style="float:right">
            {foreach $uiLanguages v k}
            {if $v.id==$uiCurrentLanguage}
	            <span class="active-language">{$v.language}</span>&nbsp;
            {else}
	            <span class="a" onClick="$('#uiLang').val('{$v.id}');$('#langForm').submit()">{$v.language}</span>&nbsp;
            {/if}
            {/foreach}
            </span>
            <br />
            <form id="langForm" method="post" action=""><input id="uiLang" type="hidden" name="uiLang" value="" /></form>
        {/if}

    </div>

</div>


{if $controllerMenuExists && $session.admin.user._logged_in}
<div id="page-header-localmenu">
    <div id="page-header-localmenu-content">
	    {if $controllerBaseName}{include file="../"|cat:$controllerBaseName|cat:"/_menu.tpl"}{else}{include file="_menu.tpl"}{/if}
    </div>
</div>
{/if}

{if $welcomeMessage}<div id="welcome-message">{$welcomeMessage}</div>{/if}

