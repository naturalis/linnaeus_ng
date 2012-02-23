{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
&nbsp;
{if $session.admin.project.id}
<a href="index.php" class="allLookupLink">{* $session.admin.project.title *}{t}Project collaborators{/t}</a>&nbsp;&nbsp;
{/if}
<a href="all.php" class="allLookupLink">{t}All collaborators{/t}</a> [A]&nbsp;&nbsp;
<a href="create.php">{t}Create collaborator{/t}</a> [A]&nbsp;&nbsp;
