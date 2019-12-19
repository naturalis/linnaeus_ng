{include file="../shared/admin-header.tpl"}

<div id="page-main">

    <p>
    {t}Each project can have one dichotomous key. That key consists of a theoretically unlimited number of steps. Each step consists of a number, a title and a description, plus a maximum of four choices. Each choice consists of a title and a text and/or an image. Also, each choice has a target: the connection to the next element within the key. The target can either be another step, or a taxon.{/t}
    </p>
    <p>
    {t}You can edit the key from the startpoint, following its structure as the users will see it. Additionally, you can create sections of your key that are not yet connected to the main key. In that way, several people can work on different parts of the key at the same time. Once finished, a section can be hooked up to the main key by simply choosing the sections starting step as the target of a choice already part of the main key.{/t}
    </p>
	<p>
	{t}While navigating through your key or key sections, a keypath is maintained at the top of the screen, just beneath the navigational breadcrumb trail. You can navigate within your key by clicking elements in the keypath. As the keypath can become quite large, only the last few elements are show. To see the complete keypath, click the &nabla; symbol at its very beginning.{/t}
    </p>

    <ul class="admin-list">
        <li><a href="step_show.php">{t}Show key (from startpoint){/t}</a></li>
        <li><a href="section.php">{t}Edit key sections{/t}</a></li>
        <!--<li><a href="map.php">{t}Key map{/t}</a></li>-->
        <li><a href="renumber.php">{t}Renumber steps{/t}</a></li>
        <li><a href="cleanup.php">{t}Clean up empty steps, orphaned choices and ghostly targets{/t}</a></li>
    </ul>

    <ul class="admin-list">
        <li><a href="orphans.php">{t}Taxa not part of the key{/t}</a></li>
        <li><a href="dead_ends.php">{t}Key validation{/t}</a></li>
    </ul>

    <ul class="admin-list">
        <li><a href="rank.php">{t}Define ranks that can appear in key{/t}</a></li>
    </ul>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
