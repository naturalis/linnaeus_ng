<?php
    require_once ('../../../../configuration/admin/controllers/MediaConverterController.php');
    $c = new MediaConverterController();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="../../../admin/style/main.css">
		<link rel="stylesheet" type="text/css" href="../../../admin/style/media.css">
    </head>
<body>
    <?php $c->printConversionProgressAction(); ?>
</body>
</html>