{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<p>

    <h3>Data types</h3>

      <ul>
      {foreach $datatypes as $datatype}
        <li>
        	<div>
            	{if $datatype.name}{$datatype.name}{else}{$datatype.sysname}{/if}

                {if $datatype.project_type_id}
                <a class="edit" href="?action=remove&id={$datatype.project_type_id}">remove</a>
                {else}
                <a class="edit" href="?action=add&id={$datatype.id}">add</a>
                {/if}
                
            </div>
            <div class='datatype-comment'>{$datatype.description}</div>
        </li>
      {/foreach}
      </ul>

    </p>
    <p>
		Removing a data type from the project will hide it from various menu's; 
        it will not cause deletion of any existing data of that type.
    </p>
    <p>
    	<a href="index.php">back</a><br />
    </p>
</div>


<script type="text/JavaScript">
$(document).ready(function()
{
	$('#page-block-messages').fadeOut(2000);
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}