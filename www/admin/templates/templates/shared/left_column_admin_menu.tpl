<div style="display:none" id="admin-menu-top">
    <p>
        <a href="taxon_new.php">{t}new taxon concept{/t}</a><br />
    </p>
</div>

<div style="display:none" id="admin-menu-bottom">
    <p>
        {t}tasks:{/t}<br />
        <a href="taxon_new.php">{t}new taxon concept{/t}</a><br />
        <a href="taxon_deleted.php">{t}taxon concepts marked as deleted{/t}</a><br />
        <a href="update_parentage.php">{t}update index table{/t}</a><br />
        <a href="taxon_orphans.php">{t}orphaned taxa{/t}</a><br />
        {if $show_nsr_specific_stuff}
        <a href="nsr_id_resolver.php">{t}NSR ID resolver{/t}</a><br />
        <a href="image_meta_bulk.php">{t}image meta-data bulk upload{/t}</a><br />
        {/if}
        <a href="export_versatile.php">{t}multi-purpose export{/t}</a><br />
        <a href="import_file.php">{t}taxon import{/t}</a><br />
        <a href="import_passport_file.php">{t}passport import{/t}</a><br />
    </p>
    <p>
        <a href="tabs.php">{t}passport categories ("tabs"){/t}</a><br />
        <a href="sections.php">{t}page sections{/t}</a><br />
        <a href="ranks.php">{t}taxonomic ranks{/t}</a><br />
    </p>
</div>