	</div>
</div>

    <div class="footerContainer" id=footercontainer>
        {snippet}static_footer.html{/snippet}
    </div>

<!-- div id="allLookupList" class="allLookupListInvisible"></div -->

<div id="jDialog" title="" class="ui-helper-hidden"></div>
<div id="tmpcontent" title="" class="ui-helper-hidden"></div>
<div id="hint-box" style="display:none"></div>

{if $googleAnalyticsCode}{include file="../shared/_google_analytics_code.tpl"}{/if}

<script type="text/javascript">

$(document).ready(function()
{
	allLookupAlwaysFetch=true;

    $('.focusfirst').focus().select();
});
  
</script>

{snippet}change_footer.html{/snippet}

</body>
</html>
