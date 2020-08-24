<?php
    require_once ('../../../../configuration/admin/controllers/MediaConverterController.php');
    $c = new MediaConverterController();

    // Flush output immediately
    @ini_set('zlib.output_compression', 0);
    @ini_set('implicit_flush', 1);
    for ($i = 0; $i < ob_get_level(); $i++) {
        ob_end_flush();
    }
    ob_implicit_flush(1);
    set_time_limit(0);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="../../../admin/style/main.css">
		<link rel="stylesheet" type="text/css" href="../../../admin/style/media.css">
    </head>
    <style>
        .error {
        	font-weight: bold;
        	color: red;
        }
    </style>
<body>
    <?php $c->printConversionProgressAction(); ?>
</body>
</html>
