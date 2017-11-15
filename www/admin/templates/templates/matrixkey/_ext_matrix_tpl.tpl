<div id="dialog-confirm" title="action" style="display: none;">
	<p class="content"></p>
</div>

<div id="taxonListTpl" class="inline-templates">
<!--
	<ul>%ITEMS%</ul>
-->
</div>

<div id="taxonListItemTpl" class="inline-templates">
<!--
	<li>%NAME%</li>
-->
</div>

<div id="editGroupTpl" class="inline-templates">
<!--
	<form>
	%HEADER%
	<table>
	%FORM%
	</table>
	</form>
-->
</div>

<div id="editCharacterTpl" class="inline-templates">
<!--
	<form>
	%HEADER%
	<table>
	%FORM%
	</table>
	</form>
-->
</div>

<div id="newGroupHeaderTpl" class="inline-templates">
<!--
	{t}Enter the new group's names:{/t}
-->
</div>

<div id="editGroupHeaderTpl" class="inline-templates">
<!--
	{t}Edit group names:{/t}
-->
</div>

<div id="newCharacterHeaderTpl" class="inline-templates">
<!--
	{t}Enter the new character's names:{/t}
-->
</div>

<div id="editCharacterHeaderTpl" class="inline-templates">
<!--
	{t}Edit character names:{/t}
-->
</div>

<div id="labelInputTpl" class="inline-templates">
<!--
	<tr><td>%LABEL%:</td><td><input type="text" name="%NAME%" placeholder="%LABEL% {t}name{/t}" id="%ID%" value="%VALUE%" class="%CLASS%" data-savegroup="%SAVE_GROUP%" /></td></tr>
-->
</div>

<div id="groupCharacterCountTpl" class="inline-templates">
<!--
	<tr><td>{t}Grouped characters:{/t}</td><td>%COUNT%</td></tr>
-->
</div>

<div id="characterStateCountTpl" class="inline-templates">
<!--
	<tr><td>{t}States in this character:{/t}</td><td>%COUNT%</td></tr>
-->
</div>

<div id="characterTypesTpl" class="inline-templates">
<!--
	<tr>
		<td>
			{t}Character type:{/t}
		</td>
		<td>
			<select name="characterType">
			%OPTIONS%
			</select>
		</td>
	</tr>
-->
</div>

<div id="characterTypeTpl" class="inline-templates">
<!--
	<option value="%VALUE%" %SELECTED%>%LABEL% (%DESCRIPTION%)</option>
-->
</div>
