<?php
	// Always flush output immediately
	@apache_setenv('no-gzip', 1);
	@ini_set('zlib.output_compression', 0);
	@ini_set('implicit_flush', 1);
	for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
	ob_implicit_flush(1);

	// $multimedia array with all multimedia files
	require_once 'include_me.php';
	
	$n = 0;
	foreach ($multimedia as $species => $speciesMultimedia) {
		// Lookup species id
		$id = getSpeciesId($species);
		if ($id) {
			$i = 1;
			foreach ($speciesMultimedia as $file => $caption) {
				// Check overview; set if empty
				if ($i == 1 && strtolower($file) != strtolower(getOverview($id)) && file_exists($pathToMM . $file)) {
					setOverview($id, $projectId, $pathToMM, $file);
					echo 'Set overview to ' . $file . ' for species ' . $species . ' (' . $id . ')<br>';
				// Check if image exists; if so add
				} else if (!getMultimedia($id, $projectId, $file) && file_exists($pathToMM . $file)) {
					setMultimedia($id, $projectId, $pathToMM, $file, $caption);
					echo 'Added ' . $file . ' to species ' . $species . ' (' . $id . ')<br>';
					$n++;
				} else {
					if (!file_exists($pathToMM . $file)) {
						echo 'Could not locate file at ' . $pathToMM . $file . '<br>';
					}				
				}
				$i++;
			}
		}
	}
	echo "<br><br>Added $n multimedia files to Flora";
	
	function getSpeciesId($species) {
		// Trim ssp marker from name
		$elements = explode(' ', $species);
		if (count($elements) > 2) {
			$species = $elements[0] . ' ' . $elements[1] . ' ' . $elements[3];
		}
		$query = 'select `id` from `dev_taxa` where `taxon` = "' . mysql_real_escape_string($species) . '"';
		$result = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result) == 1) {
			return mysql_result($result, 0, 0);
		}
		echo '<span style="color: red; font-weight: bold;">Could not retrieve id for species ' . $species . '</span><br>';
		return false;
	}
	
	function getOverview($id) {
		$query = 'select `file_name` from `dev_media_taxon` where `taxon_id` = ' . mysql_real_escape_string($id) . ' and `overview_image` = 1';
		$result = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result) == 1) {
			return mysql_result($result, 0, 0);
		}
		return false;
	}

	function setOverview($id, $projectId, $pathToMM, $file) {
		$query = 'insert into `dev_media_taxon` (`project_id`, `taxon_id`, `file_name`, `original_name`, 
			`mime_type`, `file_size`, `sort_order`, `overview_image`) values (' .
			mysql_real_escape_string($projectId) . ', ' .
			mysql_real_escape_string($id) . ', "' .
			mysql_real_escape_string($file) . '", "' .
			mysql_real_escape_string($file) . '", ' .
			'"image/jpeg", ' . filesize($pathToMM . $file) . ', 0, 1)';
		$result = mysql_query($query) or die(mysql_error());
	}
	
	function getMultimedia($id, $projectId, $file) {
		$query = 'select `id` from `dev_media_taxon` where `project_id` = ' . mysql_real_escape_string($projectId) .
			' and `taxon_id` = ' . mysql_real_escape_string($id) . ' and file_name = "' . mysql_real_escape_string($file) . '"';
		$result = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result) > 0) {
			return true;;
		}
		return false;
	}

	function setMultimedia($id, $projectId, $pathToMM, $file, $caption) {
		// First get highest sort order
		$query = 'select max(`sort_order`) from `dev_media_taxon` where `taxon_id` = ' . mysql_real_escape_string($id);
		$result = mysql_query($query) or die(mysql_error());
		$sortOrder = mysql_result($result, 0, 0) + 1;
		
		// Add file
		$query = 'insert into `dev_media_taxon` (`project_id`, `taxon_id`, `file_name`, `original_name`, 
			`mime_type`, `file_size`, `sort_order`, `overview_image`) values (' .
			mysql_real_escape_string($projectId) . ', ' .
			mysql_real_escape_string($id) . ', "' .
			mysql_real_escape_string($file) . '", "' .
			mysql_real_escape_string($file) . '", ' .
			'"image/jpeg", ' . filesize($pathToMM . $file) . ', ' . $sortOrder .', 0)';
		$result = mysql_query($query) or die(mysql_error());
		$mmId = mysql_insert_id();
		
		// Add caption
		$query = 'insert into `dev_media_descriptions_taxon` (`project_id`, `media_id`, `description`) values (' .
			mysql_real_escape_string($projectId) . ', ' .
			mysql_real_escape_string($mmId) . ', "' .
			mysql_real_escape_string($caption) . '")';
		$result = mysql_query($query) or die(mysql_error());
	}
?>