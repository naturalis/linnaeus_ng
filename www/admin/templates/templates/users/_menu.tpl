{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
&nbsp;
{if $session.admin.project.id}
<a href="index.php" class="allLookupLink">{t}Project collaborators{/t}</a>&nbsp;&nbsp;
{/if}
{if $session.admin.user.currentRole == $smarty.const.ID_ROLE_SYS_ADMIN || $session.admin.user.currentRole == $smarty.const.ID_ROLE_LEAD_EXPERT}
<a href="all.php" class="allLookupLink">{t}All collaborators{/t}</a>&nbsp;&nbsp;
<a href="create.php">{t}Create collaborator{/t}</a>
{/if}