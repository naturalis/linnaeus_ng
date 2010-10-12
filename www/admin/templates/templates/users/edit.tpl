{include file="../shared/admin-header.tpl"}

{include file="../shared/admin-messages.tpl"}

<div id="page-main">
<form method="post" action="" name="theForm" id="theForm">
	<input name="id" value="{$data.id}" type="hidden" />
	<input name="checked" id="checked" value="1" type="hidden" />
	<input name="delete" id="delete" value="0" type="hidden" />
	<input name="userProjectRole" value="{$userRole.id}" type="hidden" />
<script type="text/javascript">
	userid = '{$data.id}';
</script>
<table>
	<tr>
		<td>username</td>
		<td>
			<input
				type="text" 
				name="username" 
				id="username" 
				value="{$data.username}" 
				maxlength="16" 
				onblur="userRemoteValueCheck(this.id,[this.value],['e','f'],userid)" 
			/>
			<span class="asterisk-required-field">*</span>
			<span id="username-message" class=""></span>
		</td>
	</tr>
	<tr>
		<td>password</td>
		<td>
			<input 
				type="password" 
				name="password" 
				id="password" 
				value="" 
				maxlength="16" 
				onblur="{literal}if (this.value) { userRemoteValueCheck(this.id,[this.value],['f'],userid); }{/literal}"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="password-message" class="">(leave blank to leave unchanged)</span>
		</td>
	</tr>
	<tr>
		<td>password (repeat)</td>
		<td>
			<input 
				type="password" 
				name="password_2" 
				id="password_2" 
				value="" 
				maxlength="16" 
				onblur="{literal}if (this.value || $('#password.val().)) { userRemoteValueCheck(this.id,[this.value,document.getElementById('password').value],['f','q'],userid); }{/literal}"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="password_2-message" class="">(leave blank to leave unchanged)</span>
		</td>
	</tr>
	<tr>
		<td>first_name</td>
		<td>
			<input 
				type="text" 
				name="first_name" 
				id="first_name" 
				value="{$data.first_name}" 
				maxlength="32"
				onblur="userRemoteValueCheck(this.id,[this.value],['f'],userid)"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="first_name-message" class=""></span>
		</td>
	</tr>
	<tr>
		<td>last_name</td>
		<td>
			<input 
				type="text" 
				name="last_name" 
				id="last_name" 
				value="{$data.last_name}" 
				maxlength="32"
				onblur="userRemoteValueCheck(this.id,[this.value],['f'],userid)"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="last_name-message" class=""></span>
		</td>
	</tr>
	<tr>
		<td>email_address</td>
		<td>
			<input 
				type="text" 
				name="email_address" 
				id="email_address" 
				value="{$data.email_address}" 
				maxlength="64"
				onblur="userRemoteValueCheck(this.id,[this.value],['f','e'],userid)"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="email_address-message" class=""></span>
		</td>
	</tr>

	<tr>
		<td>timezone</td>
		<td>
			<select name="timezone_id">
			{section name=i loop=$zones}
				<option 
					value="{$zones[i].id}"
					{if $zones[i].id==$data.timezone_id} selected class="option-selected" {/if}
				>{$zones[i].timezone}: {$zones[i].locations}</option>
			{/section}
			</select>
	</td>
	</tr>
	<tr>
		<td>language</td>
		<td><input 
				type="text" 
				name="language" 
				id="language" 
				value="{$data.language}" 
				maxlength="16"
			/>
			<span id="language-message" class=""></span>
		</td>
	</td>
	</tr>
	<tr>
		<td>send e-mail notifications</td>
		<td>
			<label for="email_notifications-y">
				<input
					type="radio" 
					id="email_notifications-y" 
					name="email_notifications" 
					value="1"
					{if $data.email_notifications=='1'}checked="checked"{/if}/>y
			</label>
			<label for="email_notifications-n">
				<input
					type="radio" 
					id="email_notifications-n" 
					name="email_notifications" 
					value="0" 
					{if $data.email_notifications!='1'}checked="checked"{/if} />n
			</label>
		</td>
	</tr>
	<tr>
		<td>project role</td>
		<td>
		{if $isLeadExpert}Lead expert{else}
			<select name="role_id">
			{section name=i loop=$roles}
				<option 
					title="{$roles[i].role}: {$roles[i].description}{if $roles[i].id==$userRole.role.id} (current){/if}" 
					value="{$roles[i].id}"
					{if $roles[i].id==$userRole.role.id} selected class="option-selected" {/if}
				>{$roles[i].role}</option>
			{/section}
			</select>
		{/if}
	</td>
	</tr>

	<tr>
		<td>active</td>
		<td>
			<label for="active-y">
				<input
					type="radio" 
					id="active-y" 
					name="active" 
					value="1"
					{if $isLeadExpert} disabled="disabled"{/if} 
					{if $data.active=='1'}checked="checked"{/if}/>y
			</label>
			<label for="active-n">
				<input
					type="radio" 
					id="active-n" 
					name="active" 
					value="0" 
					{if $isLeadExpert} disabled="disabled"{/if} 
					{if $data.active!='1'}checked="checked"{/if} />n
			</label>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">Select the modules this collaborator will be assigned to:</td>
	</tr>
{section name=i loop=$modules}
<tr><td><label for="module-{$modules[i].module_id}">{$modules[i].module}</label></td><td><input id="module-{$modules[i].module_id}" type="checkbox" value="{$modules[i].module_id}" name="modules[]"  {if $modules[i].isAssigned}checked="checked"{/if}/></td></tr>
{/section}
	<tr>
	</tr>
{section name=i loop=$freeModules}
<tr><td><label for="freemodule-{$freeModules[i].id}">{$freeModules[i].module}</label></td><td><input id="freemodule-{$freeModules[i].id}" type="checkbox" value="{$freeModules[i].id}" name="freeModules[]"  {if $freeModules[i].isAssigned}checked="checked"{/if}/></td></tr>
{/section}
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>	
	<tr>
		<td colspan="2">
			<input type="submit" value="save" />
			{if $userRole.role_id != 2}
			{literal}
			<input type="button" value="delete" onclick="if (confirm('Are you sure?')) { var e = document.getElementById('delete'); e.value = '1'; e = document.getElementById('theForm'); e.submit(); } " />
			{/literal}
			{/if}
			<input type="button" value="back" onclick="window.open('{$session.system.referer.url}','_top')" />
		</td>
	</tr>
</table>
</form>

</div>

{literal}
<script type="text/JavaScript">

$(document).ready(function(){

	$('#username').focus();

});

</script>
{/literal}

{include file="../shared/admin-footer.tpl"}
