{include file="../shared/admin-header.tpl"}

<div id="page-main">

    these setting are in traits_settings, should be moved to module_settings
    
<pre>
setting                        | value
------------------------------------------------
column header species          | Species
column header taxon id         | ID SRTregister
column header references       | References
yes values                     | {yes|ja|y}
no values                      | {no|nee|n}
dash values                    | {-}
taxon id query                 | select lng_id as id from nsr_ids where project_id = %pid% and item_type="taxon" and (nsr_id=concat("tn.nlsr.concept/",lpad("%tid%",12,"0")) and nsr_id!=lpad("%tid%",
12,"0") or nsr_id!=concat("tn.nlsr.concept/",lpad("%tid%",12,"0")) and nsr_id=lpad("%tid%",12,"0")) |
input file field separator     |
input file field encloser      | "
input file reference separator | {,|;}
</pre>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
