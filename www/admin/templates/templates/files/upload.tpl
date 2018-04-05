{if $response_only}
{assign 'array_merged' $messages|array_merge:$errors}
{"\n"|implode:$array_merged} 
{else}
{include file="../shared/admin-header.tpl"}
<style>
.dropzone {
	width:550px;
	height:200px;
}
td {
	vertical-align:top;
}
</style>

<script src="{$baseUrl}admin/vendor/dropzone/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="{$baseUrl}admin/vendor/dropzone/dist/min/dropzone.min.css">
<script>
	Dropzone.autoDiscover = false;
</script>
<div id="page-main">

	<table><tr><td>
        
        <p>
            <div id="groundzero" class="dropzone" style="display:inline-block"></div>
    
        </p>
        
        {t}or{/t}
    
        <p>
            <form action="upload.php" method="post" enctype="multipart/form-data">
            	<input type="hidden" name="action" value="single" />
            	<input type="file" name="file" /> <input type="submit" value="{t}upload{/t}" />
            </form>
        </p>
    
        allowed extensions:
        <ul>
        {foreach $allowed_extensions v}
            <li>{$v}</li>
        {/foreach}
        </ul>
    
        <p>
            <a href="index.php">{t}browse files{/t}</a>
        </p>    

	</td><td>

	        <ul id="list"></ul>

	</td></tr></table>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

<script>

$(document).ready(function()
{
	$("#groundzero").dropzone(
	{
		url: "upload.php",
		paramName: "file", // The name that will be used to transfer the file
		maxFilesize: 10, // MB
		maxFiles: 2000,
		createImageThumbnails: false,
		sending: function(file, xhr, formData)
		{
            formData.append('action', 'multi');
        },	
		accept: function(file, done)
		{
			done();
		},	
		complete: function(file)
		{
			this.removeFile(file);
		},
		success: function(file, response)
		{
			//console.dir(file);
			$("#list").append('<li>' + file.name +': ' + (response?response:"could\'t upload") + '</li>');
		}
	});

});
</script>

{/if}