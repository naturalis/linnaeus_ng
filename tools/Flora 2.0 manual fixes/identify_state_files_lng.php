<?php
	// Always flush output immediately
	@apache_setenv('no-gzip', 1);
	@ini_set('zlib.output_compression', 0);
	@ini_set('implicit_flush', 1);
	for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
	ob_implicit_flush(1);

	require_once 'include_me.php';

	mysql_query('update `dev_characteristics_states` set file_name = null where project_id = ' . $projectId) or die(mysql_error());
	
	$identify_files = array(
		"kruidige.adm" => array(
			"blad > 10x zo lang als breed@zwaardvormig@zwaardo2.jpg",
			"blad > 10x zo lang als breed@lijnvormig (l:b => 10)@lijnvom2.jpg",
			"blad > 10x zo lang als breed@buisvormig@BUISVORM.jpg",
			"blad > 10x zo lang als breed@naaldvormig@naaldvor.jpg",
			"blad > 10x zo lang als breed@priemvormig@priemvr2.jpg",
			"blad > 10x zo lang als breed@nvt@1X_NVT2.jpg",
			"blad < 10x lang als breed@schubvormig@schubvor.jpg",
			"blad < 10x lang als breed@lancetvormig (l:b = 3.1-10)@lancetvo.jpg",
			"blad < 10x lang als breed@langwerpig (l:b = 2.1-3.0)@langwerp.jpg",
			"blad < 10x lang als breed@breed (l:b = 0.7-2.0)@L_B7_2.jpg",
			"blad < 10x lang als breed@nvt@1X_NVT.jpg",
			"blad < 10x lang als breed@afwezig@1X_AFWEZ.jpg",
			"blad gelobd tot gedeeld@drielobbig -spletig -delig@lobdriel.jpg",
			"blad gelobd tot gedeeld@handvormig@lobhandv.jpg",
			"blad gelobd tot gedeeld@veervormig@lobveerv.jpg",
			"blad gelobd tot gedeeld@nvt@lobnvt.jpg",
			"bladvorm samengesteld@drietallig@samendri.jpg",
			"bladvorm samengesteld@handvormig@samenhan.jpg",
			"bladvorm samengesteld@veervormig@samenvee.jpg",
			"bladvorm samengesteld@meervoudig samengesteld/gedeeld@samenmee.jpg",
			"bladvorm samengesteld@nvt@samennvt.jpg",
			"bladstand@verspreidstandig@verspreidstandig.jpg",
			"bladstand@tegenoverstaand@tegenoverstaand.jpg",
			"bladstand@kransstandig@kransstandig.jpg",
			"bladstand@nvt@blad_NVT.jpg"
		),
		"houtige.adm" => array(
			"blad > 10x zo lang als breed@lijnvormig (l:b => 10)@lijnvom2.jpg",
			"blad > 10x zo lang als breed@priemvormig@priemvr2.jpg",
			"blad > 10x zo lang als breed@nvt@1X_NVT2.jpg",
			"blad < 10x lang als breed@schubvormig@schubvor.jpg",
			"blad < 10x lang als breed@lancetvormig (l:b = 3.1-10)@lancetvo.jpg",
			"blad < 10x lang als breed@langwerpig (l:b = 2.1-3.0)@langwerp.jpg",
			"blad < 10x lang als breed@breed (l:b = 0.7-2.0)@L_B7_2.jpg",
			"blad < 10x lang als breed@nvt@1X_NVT.jpg",
			"blad gelobd tot gedeeld@drielobbig -spletig -delig@lobdriel.jpg",
			"blad gelobd tot gedeeld@handvormig@lobhandv.jpg",
			"blad gelobd tot gedeeld@veervormig@lobveerv.jpg",
			"blad gelobd tot gedeeld@nvt@lobnvt.jpg",
			"bladvorm samengesteld@drietallig@samendri.jpg",
			"bladvorm samengesteld@handvormig@samenhan.jpg",
			"bladvorm samengesteld@veervormig@samenvee.jpg",
			"bladvorm samengesteld@nvt@samennvt.jpg",
			"bladstand@verspreidstandig@verspreidstandig.jpg",
			"bladstand@tegenoverstaand@tegenoverstaand.jpg",
			"bladstand@kransstandig@kransstandig.jpg",
			"bladstand@nvt@blad_NVT.jpg"
	));
		
	foreach ($identify_files as $identify_file_name => $identify_file) {
		echo "identify file <b>$identify_file_name</b><br><br>";
		foreach ($identify_file as $character_state_file) {
			list($character, $state, $file) = explode("@", $character_state_file);
			$file = strtolower($file);
			
			echo "Character: '$character'<br>State: '$state'<br>File to add: '$file'";
			$matrixId = getMatrixId($projectId, $identify_file_name);
			$characterId = getCharacterId($projectId, $matrixId, $character);
			
			if (!updateStateFile($projectId, $characterId, $state, $file)) {
				echo " -- <span style=\"color: red;\">state not found!</span>";
			} else {
				echo " -- done";
			}
			echo '<br><br>';
		}
		echo '<br><br>';
	}

	function getMatrixId ($projectId, $matrix) {
		$query = 'select `matrix_id` from `dev_matrices_names` where `project_id` = ' . $projectId . 
			' and `name` = "' . mysql_real_escape_string($matrix) .'"';
		$result = mysql_query($query) or die(mysql_error());
		return mysql_result($result, 0, 0);
	}

	function getCharacterId ($projectId, $matrixId, $character) {
		$query = 'select t1.`characteristic_id` from `dev_characteristics_labels` t1, `dev_characteristics_matrices` t2
			 where t1.`project_id` = ' . $projectId . ' and t1.`label` = "' . mysql_real_escape_string($character) .'" 
			 and t2.`matrix_id` = ' . $matrixId . ' and t1.`characteristic_id` = t2.`characteristic_id`';
		$result = mysql_query($query) or die(mysql_error());
		return mysql_result($result, 0, 0);
	}

	function updateStateFile ($projectId, $characterId, $state, $file) {
		$query = 'update `dev_characteristics_states` t1, `dev_characteristics_labels_states` t2 
			set t1.`file_name` = "' . mysql_real_escape_string(strtoupper($file)) . 
			'" where t1.`project_id` = ' . $projectId . ' and t2.`label` = "' . mysql_real_escape_string($state) .
			'" and t1.`characteristic_id` = ' . $characterId . ' and t1.`id` = t2.`state_id`';
//echo $query;
		$result = mysql_query($query) or die(mysql_error());
//echo mysql_affected_rows();
		return mysql_affected_rows() == 1 ? true : false;
	}
?>


