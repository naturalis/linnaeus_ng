{include file="../shared/admin-header.tpl"}

<div id="page-main">

<form method="post" action="" name="theForm" id="theForm">
<table>
	<tr>
		<td>username:</td>
		<td>
		{if $check==true}{$data.username}{else}
			<input 
				type="text" 
				name="username" 
				id="username" 
				value="{$data.username}" 
				maxlength="16" 
				onblur="userRemoteValueCheck(this.id,[this.value],['e','f'])" 
			/>
			<span class="asterisk-required-field">*</span>
			<span id="username-message" class=""></span>	{/if}
		</td>
	</tr>
	<tr>
		<td>password:</td>
		<td>
		{if $check==true}{$data.password}{else}
			<input
				type="password"
				name="password"
				id="password"
				value="{$data.password}"
				maxlength="16"
				onblur="userRemoteValueCheck(this.id,[this.value],['f'])"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="password-message" class=""></span>	</td>	
	</tr>
	<tr>
		<td>password (repeat)</td>
		<td>
			<input
				type="password" 
				name="password_2" 
				id="password_2" 
				value="{$data.password_2}" 
				maxlength="16" 
				onblur="userRemoteValueCheck(this.id,[this.value,document.getElementById('password').value],['f','q'])"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="password_2-message" class=""></span>	{/if}
		</td>
	</tr>
	<tr>
		<td>first_name:</td>
		<td>
		{if $check==true}{$data.first_name}{else}
			<input
				type="text"
				name="first_name"
				id="first_name"
				value="{$data.first_name}" 
				maxlength="32" 
				onblur="userRemoteValueCheck(this.id,[this.value],['f'])"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="first_name-message" class=""></span>	{/if}
		</td>
	</tr>
	<tr>
		<td>last_name:</td>
		<td>
		{if $check==true}{$data.last_name}{else}
			<input
				type="text"
				name="last_name"
				id="last_name"
				value="{$data.last_name}"
				maxlength="32"
				onblur="userRemoteValueCheck(this.id,[this.value],['f'])"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="last_name-message" class=""></span>	{/if}
		</td>
	</tr>
	<tr>
		<td>email_address:</td>
		<td>
		{if $check==true}{$data.email_address}{else}
			<input
				type="text" 
				name="email_address" 
				id="email_address" 
				value="{$data.email_address}" 
				maxlength="64" 
				onblur="userRemoteValueCheck(this.id,[this.value],['f','e'])"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="email_address-message" class=""></span>
		{/if}
		</td>
	</tr>
	<tr>
		<td>role in current project:</td>
		<td>
		{if $check==true}{section name=i loop=$roles}{if $roles[i].id==$data.role_id}{$roles[i].role}{/if}{/section}{else}
			<select name="role_id">
			{section name=i loop=$roles}
				<option
					title="{$roles[i].role}: {$roles[i].description}" 
					value="{$roles[i].id}"
					{if $roles[i].id==$data.role_id} selected class="option-selected" {/if}
				>{$roles[i].role}</option>
			{/section}
			</select>
			<span class="asterisk-required-field">*</span>
		{/if}
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="submit" value="save" />&nbsp;
		{if $check==true}
			<input
				type="button" 
				value="back" 
				onclick="$('#checked').val('-1');$('#theForm').submit()" />
		{else}
			<input type="button" value="back" onclick="window.open('{$session.system.referer.url}','_top')" />
		{/if}
		</td>
	</tr>
</table>

</form>
</div>

{if $existingUser!=false}
<div id="page-block-messages">
<span class="admin-message">
{if $existingUserReason=='same name'}
A similar user, albeit with a different e-mail address, already exists in another project:<br/>
<span class="message-existing-user">{$existingUser.first_name} {$existingUser.last_name}</span> ({$existingUser.email_address})<br />
Would you like to connect that user to the current project instead of creating a new one with the same name?

<input type="button" value="yes, connect existing" onclick="userConnectExistingUser();" />
<input type="button" value="no, create new" onclick="userCreateExistingUser();" />

{else}

A user with the same e-mail address already exists in another project:<br />
<span class="message-existing-user">{$existingUser.first_name} {$existingUser.last_name}</span> ({$existingUser.email_address})<br />
You cannot create a new user with the same e-mail address, but you can connect the existing user to the current project. Would you like to do that?<br />                
<input type="button" value="yes, connect user"  onclick="userConnectExistingUser();" />
<input type="button" value="no, cancel"  onclick="window.open('{$session.system.referer.url}','_top');" />

{/if}
</span>
</div>
{/if}










{include file="../shared/admin-messages.tpl"}

{literal}
<script type="text/JavaScript">

$(document).ready(function(){

	$('#username').focus();

});

</script>
{/literal}

{include file="../shared/admin-footer.tpl"}
