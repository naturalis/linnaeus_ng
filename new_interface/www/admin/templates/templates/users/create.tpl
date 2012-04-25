{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">

<form method="post" action="" name="theForm" id="theForm">
<input type="hidden" name="action" value="create"  />
<input type="hidden" name="rnd" value="{$rnd}" />
<table>
	<tr style="vertical-align:top">
		<td>
			<table>
				<tr>
					<th colspan="2">{t}Collaborator data{/t}</th>
				</tr>
				<tr>
					<td style="width:200px">{t}Username:{/t}</td>
					<td>
					{if $check==true}{$data.username}{else}
						<input 
							type="text" 
							name="username" 
							id="username" 
							value="{$data.username}" 
							maxlength="{$maxLengths.username}" 
							onblur="userRemoteValueCheck(this.id,[this.value],['e','f'])" 
						/>
						<span class="asterisk-required-field">*</span>
						<span id="username-message" class="password-neutral"></span>	{/if}
					</td>
				</tr>
				<tr>
					<td>{t}Password:{/t}</td>
					<td>
						<input
							type="password"
							name="password"
							id="password"
							value="{$data.password}"
							maxlength="{$maxLengths.password}"
							onkeyup="userRemoteValueCheck(this.id,[this.value],['f'])"
						/>
						<span class="asterisk-required-field">*</span>
						<span id="password-message" class="password-neutral"></span>	
					</td>	
				</tr>
				<tr>
					<td>{t}Password (repeat):{/t}</td>
					<td>
						<input
							type="password" 
							name="password_2" 
							id="password_2" 
							value="{$data.password_2}" 
							maxlength="{$maxLengths.password}" 
							onblur="userRemoteValueCheck(this.id,[this.value,document.getElementById('password').value],['f','q'])"
						/>
						<span class="asterisk-required-field">*</span>
						<span id="password_2-message" class=""></span>
					</td>
				</tr>
				<tr>
					<td>{t}First name:{/t}</td>
					<td>
					{if $check==true}{$data.first_name}{else}
						<input
							type="text"
							name="first_name"
							id="first_name"
							value="{$data.first_name}" 
							maxlength="{$maxLengths.first_name}" 
							onblur="userRemoteValueCheck(this.id,[this.value],['f'])"
						/>
						<span class="asterisk-required-field">*</span>
						<span id="first_name-message" class=""></span>	{/if}
					</td>
				</tr>
				<tr>
					<td>{t}Last name:{/t}</td>
					<td>
					{if $check==true}{$data.last_name}{else}
						<input
							type="text"
							name="last_name"
							id="last_name"
							value="{$data.last_name}"
							maxlength="{$maxLengths.last_name}"
							onblur="userRemoteValueCheck(this.id,[this.value],['f'])"
						/>
						<span class="asterisk-required-field">*</span>
						<span id="last_name-message" class=""></span>	{/if}
					</td>
				</tr>
				<tr>
					<td>{t}E-mail address:{/t}</td>
					<td>
					{if $check==true}{$data.email_address}{else}
						<input
							type="text" 
							name="email_address" 
							id="email_address" 
							value="{$data.email_address}" 
							maxlength="{$maxLengths.email_address}" 
							onblur="userRemoteValueCheck(this.id,[this.value],['f','e'])"
						/>
						<span class="asterisk-required-field">*</span>
						<span id="email_address-message" class=""></span>
					{/if}
					</td>
				</tr>
			
				<tr>
					<td>{t}Timezone:{/t}</td>
					<td>
						<select name="timezone_id">
						{section name=i loop=$zones}
							<option 
								value="{$zones[i].id}"
							>{$zones[i].timezone}: {$zones[i].locations}</option>
						{/section}
						</select>
				</td>
				</tr>
				<tr>
					<td>{t}Send e-mail notifications:{/t}</td>
					<td>
						<label for="email_notifications-y">
							<input
								type="radio" 
								id="email_notifications-y" 
								name="email_notifications" 
								value="1"
								checked="checked" />y
						</label>
						<label for="email_notifications-n">
							<input
								type="radio" 
								id="email_notifications-n" 
								name="email_notifications" 
								value="0" />n
						</label>
					</td>
				</tr>
				<tr>
					<td>{t}Role in current project:{/t}</td>
					<td>
					{if $check==true}{section name=i loop=$roles}{if $roles[i].id==$data.role_id}{$roles[i].role}{/if}{/section}{else}
						<select name="role_id">
						{section name=i loop=$roles}
							<option
								title="{$roles[i].role}: {$roles[i].description}" 
								value="{$roles[i].id}"
							>{$roles[i].role}</option>
						{/section}
						</select>
						<span class="asterisk-required-field">*</span>
					{/if}
					</td>
				</tr>
				<tr>
					<td>{t}Active:{/t}</td>
					<td>
						<label for="active-y">
							<input
								type="radio" 
								id="active-y" 
								name="active" 
								value="1"
								checked="checked" />y
						</label>
						<label for="active-n">
							<input
								type="radio" 
								id="active-n" 
								name="active" 
								value="0" />n
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" value="{t}save{/t}" />&nbsp;
						<input type="button" value="{t}back{/t}" onclick="window.open('{$session.admin.system.referer.url}','_top')" />
					</td>
				</tr>
			</table>
		</td>
		<td>
			{if $session.admin.project.id}
			<table>
				<tr>
					<th colspan="2">{t}Select the modules that will be assigned to this collaborator{/t}</th>
				</tr>
			{section name=i loop=$modules}
				<tr class="tr-highlight">
					<td style="width:200px">
						<label for="module-{$modules[i].module_id}">{$modules[i].module}</label>
					</td>
					<td>
						<input id="module-{$modules[i].module_id}" type="checkbox" value="{$modules[i].module_id}" name="modules[]"  checked="checked"/>
					</td>
				</tr>
			{/section}
				<tr>
				</tr>
			{section name=i loop=$freeModules}
			<tr><td><label for="freemodule-{$freeModules[i].id}">{$freeModules[i].module}</label></td><td><input id="freemodule-{$freeModules[i].id}" type="checkbox" value="{$freeModules[i].id}" name="freeModules[]"  checked="checked"/></td></tr>
			{/section}
			</table>
			{/if}
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
