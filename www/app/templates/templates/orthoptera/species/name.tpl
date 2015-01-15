{include file="../shared/header.tpl"}

<body class="html not-front not-logged-in two-sidebars page-node page-node- page-node-17 node-type-naturalis-page section-content" >

    <!--.page -->
    <div role="document" class="page">

	{include file="../shared/page_header.tpl"}

    <main role="main" class="row l-main">

        <div class="large-6 large-push-3 main columns">
      
            <a id="main-content"></a>

			<h2>{t}Naam:{/t} {$name.name}</h2>

	        <div id="content" class="taxon-detail">
                <table>
                    <tr><td>{t _s1=$name.nametype}Is %s voor{/t}</td><td colspan="2"><a href="nsr_taxon.php?id={$taxon.id}">{$taxon.taxon}</a></td></tr>
                    {if $name.reference_label}<tr><td>{t}Referentie{/t}</td><td colspan="2"><a href="../literature2/reference.php?id={$name.reference_id}">{$name.reference_label}</a></td></tr>{/if}
                    {if $name.expert_name}<tr><td>{t}Expert{/t}</td><td colspan="2">{$name.expert_name}</td></tr>{/if}
                    {if $name.organisation_name}<tr><td>{t}Organisatie{/t}</td><td colspan="2">{$name.organisation_name}</td></tr>{/if}
                </table>
	        </div>

        </div>
        <!--/.main region -->
    
        {* include file="../shared/_left_column.tpl" *}
    
        {include file="../shared/_right_column.tpl"}
        
    </main>
    <!--/.main-->
 
  
  </div>
<!--/.page -->

<script type="text/JavaScript">
$(document).ready(function() {
	
	$('title').html('{$name.name|@strip_tags|@escape} - '+$('title').html());

});
</script>

{include file="../shared/footer.tpl"}