<a href="contents.php">{t}Contents{/t}</a>&nbsp;&nbsp;&nbsp;
<a href="index.php">{t}Management{/t}</a>&nbsp;&nbsp;&nbsp;
{if $step.id && !$hideInsertMenu}{assign var=i value=$keyPath|@count}{assign var=i value=$i-2}<a href="insert.php?id={$step.id}&c={$keyPath[$i].id}">Insert new step</a>&nbsp;&nbsp;&nbsp;{/if}
<a href="../utilities/search_index.php">Extensive search</a>