<div class="menuContainer">
    <form id="inlineformsearch" name="inlineformsearch" action="/linnaeus_ng/app/views/search/nsr_search.php" method="get">
        <div class="searchInputHolder">
            <input id="name" name="search" type="text" placeholder="{t}Snel zoeken op soort/taxon...{/t}" name="search" class="searchString" title="{t}Zoek op naam{/t}" value="{$search.search}"  autocomplete="off" />
            <a href="javascript:void(0)" class="close-suggestion-list close-suggestion-list-js">
                <i class="ion-close-round"></i>
            </a>
        </div>
        <div id="name_suggestion" class="suggestList" style="display:none"></div>
    </form>
</div>
<script>
    $(document).ready(function()
    {
        bindKeys();
    });
</script>
