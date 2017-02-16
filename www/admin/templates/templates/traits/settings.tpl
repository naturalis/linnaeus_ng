{include file="../shared/admin-header.tpl"}

<div id="page-main">

    these setting are in traits_settings, should be moved to module_settings

{*
INSERT INTO `module_settings` VALUES
(null,16,'column_header_species','Header of the column in the upload file containing species\' names.','Species',NOW(),NOW())
,(null,16,'column_header_taxon_id','Header of the column in the upload file containing the taxon ID.','ID',NOW(),NOW())
,(null,16,'column_header_references','Header of the column in the upload file containing references.','References',NOW(),NOW())
,(null,16,'taxon_id_query','Query to resove \'column_header_taxon_id\'.','select lng_id as id from nsr_ids where project_id = %pid% and item_type="taxon" and (nsr_id=concat("tn.nlsr.concept/",lpad("%tid%",12,"0")) and nsr_id!=lpad("%tid%",
12,"0") or nsr_id!=concat("tn.nlsr.concept/",lpad("%tid%",12,"0")) and nsr_id=lpad("%tid%",12,"0"))',NOW(),NOW())
,(null,16,'yes_values','Values that resolve to true (between accolades, separated by pipelines).','{yes|ja|y}',NOW(),NOW())
,(null,16,'no_values','Values that resolve to true (between accolades, separated by pipelines).','{no|nee|n}',NOW(),NOW())
,(null,16,'dash_values','Valid range separators (between accolades, separated by pipelines).','{-}',NOW(),NOW())
,(null,16,'input_file_field_separator','CSV fiels separator (use \'TAB\' for tab).',',',NOW(),NOW())
,(null,16,'input_file_field_encloser','CSV field encloser.','"',NOW(),NOW())
,(null,16,'input_file_reference_separator','Valid reference separators (between accolades, separated by pipelines).','{,|;}',NOW(),NOW())
;

    
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

*} 
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
