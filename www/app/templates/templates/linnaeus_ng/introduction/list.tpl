{include file="../shared/header.tpl"}

<div id="page-main">
    {foreach $refs v}
    <a class="topic" href="../introduction/topic.php?id={$v.id}">{$v.topic}</a><br />
    {/foreach}
</div>

{include file="../shared/footer.tpl"}
