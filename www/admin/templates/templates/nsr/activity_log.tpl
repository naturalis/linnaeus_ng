{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h2>Ovezicht Soortenregister edits{if $search.search} - "{$search.search}"{/if}</h2>
<h3>Aantal edits: {$results.count}</h3>

<p>
<form method="post" action="activity_log.php">
<input type="text" value="{$search.search}" name="search" id="search"  style="width:300px;" />
<input type="submit" value="zoeken" />
{if $search.search} <a href="activity_log.php">alles tonen</a>{/if}
</form>
</p>


{function menu}
  <ul class="compare">
  {if $data1!==false}
  {foreach $data1 as $k1=>$v1}
    {if $v1|@is_array}
    <li class="inner-header">
        <b>{$k1}:</b>
        {menu data1=$v1 data2=$data2[$k1]}
    </li>
    {else}
    <li class="{if $data2[$k1]!==$v1}different{else}same{/if}">
    {$k1}: {$v1}<br />
    {/if}
    </li>
  {/foreach}
  {else}
  (-)
  {/if}
  </ul>
{/function}

<div id="activity-log">

<table>
{foreach from=$results.data item=v key=k}
    <tr>
    	<td style="width:400px;">
		    {$v.note}
		</td>
    	<td style="width:250px;">
        	{if $v.user_user_id}
			<span title="{$v.user_username}; {$v.user_email_address}">{$v.user_first_name} {$v.user_last_name}</span>
            {else}
			<span title="{$v.user.username}{if $v.user.email_address!=$v.user.username}; {$v.user.email_address}{/if}">{$v.user.name}</span> <span title="gebruiker bestaat niet meer in de usertabel">(&notin;)</span>
            {/if}
		</td>
    	<td>
		    {$v.last_change_hr}
		</td>
    	<td>
		    <a href="#" onclick="$('.alldata{$k}').toggle();return false;">
            	<span class="alldata{$k}">toon data</span>
                <span class="alldata{$k}" style="display:none">data verbergen</span>
			</a>
		</td>
    </tr>
    
    <tr class="alldata{$k}" style="display:none;border-bottomn:1px solid #666">
    	<td colspan="4">
        	<table class="inline-table"><tr><td>
				{menu data1=$v.data_before data2=$v.data_after}
            </td>
            <td>
				{menu data2=$v.data_before data1=$v.data_after}
			</td></tr></table>
		</td>
    </tr>
{/foreach}
</table>

</div>

<p>
	{assign var=pgnResultCount value=$results.count}
	{assign var=pgnResultsPerPage value=$results.perpage}
	{assign var=pgnCurrPage value=$search.page}
	{assign var=pgnURL value=$smarty.server.PHP_SELF}
	{assign var=pgnQuerystring value=$querystring}
	{include file="../shared/_paginator.tpl"}
</p>

<p>
	<a href="index.php">terug</a>
</p>

</div>

<script>
$(document).ready(function(e) {
	{foreach from=$tabs item=v key=k}
	currentpublish[{$k}]={if $v.publish==1}true{else}false{/if};
	{/foreach}
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}