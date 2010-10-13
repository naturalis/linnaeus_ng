{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<div class="text-block">

	Assign collaborators to work on modules:<br />

	<span id="module-table"></span>

	</div>

	<br />

	<div class="text-block">

	Assign collaborators to work on free modules:<br />

	<span id="free-module-table"></span>

	</div>

	<a href="../users/create.php">Create new collaborator</a>

</div>

<script type="text/javascript">

{section name=j loop=$users}
moduleAddUser({$users[j].id},'{$users[j].first_name|@escape} {$users[j].last_name|@escape}','{$users[j].role}');
{/section}

{section name=i loop=$modules}
moduleAddModule('regular',{$modules[i].module_id},'{$modules[i].module|@escape}','{$modules[i].active}',{$modules[i].collaborators|@count});
{section name=j loop=$users}
{assign var=x value=$users[j].id}
moduleAddModuleUser('regular',{$modules[i].module_id},{$x},{if $modules[i].collaborators[$x].user_id == $users[j].id}1{else}0{/if});
{/section}
{/section}
{section name=i loop=$free_modules}
moduleAddModule('free',{$free_modules[i].id},'{$free_modules[i].module|@escape}','{$free_modules[i].active}',{$free_modules[i].collaborators|@count});
{section name=j loop=$users}
{assign var=x value=$users[j].id}
moduleAddModuleUser('free',{$free_modules[i].id},{$x},{if $free_modules[i].collaborators[$x].user_id == $users[j].id}1{else}0{/if});
{/section}
{/section}

moduleBuildModuleUserBlock('regular');
moduleBuildModuleUserBlock('free');
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}