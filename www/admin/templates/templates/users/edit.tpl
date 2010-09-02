{include file="../shared/admin-header.tpl"}

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
		<td>gender</td>
		<td>
			<label for="gender-f">
				<input 
					type="radio" 
					id="gender-f" 
					name="gender" 
					value="f" {if $data.gender!='m'}checked="checked"{/if}
				/>f
			</label>
			<label for="gender-m">
				<input
					type="radio" 
					id="gender-m" 
					name="gender" 
					value="m" {if $data.gender=='m'}checked="checked"{/if} 
				/>m
			</label>
			<span class="asterisk-required-field">*</span>
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
		<td>role in current project:</td>
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
		<td colspan="2">
			<input type="submit" value="Save" />
			{literal}
			<input type="button" value="Delete" onclick="if (confirm('Are you sure?')) { var e = document.getElementById('delete'); e.value = '1'; e = document.getElementById('theForm'); e.submit(); } " />
			{/literal}
			<input type="button" value="Back" onclick="window.open('user_overview.php','_self');" />
		</td>
	</tr>
</table>
</form>

</div>

{include file="../shared/admin-messages.tpl"}

{literal}
<script type="text/JavaScript">

$(document).ready(function(){

	$('#username').focus();

});

</script>
{/literal}

{include file="../shared/admin-footer.tpl"}
