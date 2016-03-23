{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h2>Webservices</h2>

<ul>
{foreach $services v k}
<li>
    <b><a href="{$base_url|@replace:'%AUTH%':''}{$k}" target="_service">{$base_url|@replace:'%AUTH%':''}{$k}</a></b><br />
    description: {$v.description}<br />
	parameters:
    <ul>
        {foreach $v.parameters p a}
        <li><b>{$a}</b>: {$p.description}{if $p.mandatory} * {/if}</li>
        {/foreach}
        {foreach $general_parameters p a}
        <li><b>{$a}</b>: {$p.description}{if $p.mandatory} * {/if}</li>
        {/foreach}
    </ul>
    <br />
</li>
{/foreach}
</ul>

authentication done with Basic Authentication, checked against the LNG user table (sysadmin-level users only). 
for non-interactive usage, call:
{$base_url|@replace:'%AUTH%':'username:password@'}{$k}
</div>



{include file="../shared/admin-footer.tpl"}
