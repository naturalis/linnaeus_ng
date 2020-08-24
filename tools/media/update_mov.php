<?php

 	$cfg = dirname(__FILE__) . '/../../configuration/admin/configuration.php';
	if (!file_exists($cfg)) die("Error: unable to locate $cfg.");
	include $cfg;
	$c = new configuration;
	$s = $c->getDatabaseSettings();
 	$my = mysqli_connect($s['host'], $s['user'], $s['password'], $s['database']);
	mysqli_set_charset($my, 'utf8');

    require_once 'mov_replace.php';
	$internalLink = "showMedia('../../../shared/media/project/" . str_pad($projectId, 4, "0", STR_PAD_LEFT) . "/%s'";

    $regular = array(
        'update characteristics_states set file_name = "%s" where file_name = "%s" and project_id = ' . $projectId,
        'update glossary_media set file_name = "%s" where file_name = "%s" and project_id = ' . $projectId,
        'update media_taxon set file_name = "%s" where file_name = "%s" and project_id = ' . $projectId
    );

    $internal = array(
        1 => 'update content_free_modules set content = replace(content, "' . mysqli_real_escape_string($my, $internalLink) .
            '", "' . mysqli_real_escape_string($my, $internalLink) . '")',
        2 => 'update content_introduction set content = replace(content, "' . mysqli_real_escape_string($my, $internalLink) .
            '", "' . mysqli_real_escape_string($my, $internalLink) . '")',
        3 => 'update content_keysteps set content = replace(content, "' . mysqli_real_escape_string($my, $internalLink) .
            '", "' . mysqli_real_escape_string($my, $internalLink) . '")',
        4 => 'update content_taxa set content = replace(content, "' . mysqli_real_escape_string($my, $internalLink) .
            '", "' . mysqli_real_escape_string($my, $internalLink) . '")',
        5 => 'update glossary set `definition` = replace(`definition`, "' . mysqli_real_escape_string($my, $internalLink) .
            '", "' . mysqli_real_escape_string($my, $internalLink) . '")'
    );

    foreach ($regular as $q) {
        foreach ($replacement as $replace => $find) {
            mysqli_query($my, sprintf($q, $find, $replace)) or die(mysqli_error($my));
        }
    }

    foreach ($internal as $n => $q) {
        foreach ($replacement as $find => $replace) {
            $query = sprintf($q, $find, $replace) . " where " . ($n < 5 ? 'content' : 'definition') .
                " like '%.mov%' and project_id = $projectId";
            mysqli_query($my, $query) or die($query);
        }
    }


?>