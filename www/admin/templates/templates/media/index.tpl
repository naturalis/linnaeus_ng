{include file="../shared/admin-header.tpl"}

{include file="../shared/admin-messages.tpl"}

<div id="media-main">

    <p>
	    <a href="upload.php">upload media</a><br/>
	    <a href="select.php">browse media for this project</a><br/>
	    <a href="search.php">search media for this project</a><br/>
    </p>

	{if $CRUDstates.can_update}
    <p>
 		{t}System maintenance{/t}
 	    <a href="converter.php">convert existing local media</a><br/>
 	    <!-- <a href="select_rs.php">browse media on ResourceSpace server</a><br/> -->
		{if $action != '' && $username != '' && $password != ''}
		 	<a id="rsLogin" href="">login to ResourceSpace</a>
		{/if}
	</p>
    {/if}

</div>

{if $CRUDstates.can_update}
{if $action != '' && $username != '' && $password != ''}
	<form id="rsLoginForm" method="post" action="{$action}">
	<input type="hidden" name="username" value="{$username}">
	<input type="hidden" name="password" value="{$password}">
	</form>
{/if}
{/if}

{include file="../shared/admin-footer.tpl"}


<script>
{literal}
$("a#rsLogin").click(function(e) {
	e.preventDefault();
	$("form#rsLoginForm").submit();
});
{/literal}
</script>
