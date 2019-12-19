<a href="contents.php">{t}Contents{/t}</a>&nbsp;&nbsp;&nbsp;
<a href="index.php">{t}Management{/t}</a>&nbsp;&nbsp;&nbsp;
{if $step.id && !$hideInsertMenu}{assign var=i value=$keyPath|@count}{assign var=i value=$i-2}<a href="insert.php?id={$step.id}">{t}Insert new step{/t}</a>&nbsp;&nbsp;&nbsp;{/if}
<a href="../search/index.php">{t}Extensive search{/t}</a>
&nbsp;&nbsp;
<a href="../../../app/views/{$controllerBaseName}/" style="color:#999;margin-left:10px" target="_project">view project</a>
<span id="message-container" style="float:right"></span>
