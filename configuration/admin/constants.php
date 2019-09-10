<?php

	if (!defined('GENERAL_SETTINGS_ID')) define('GENERAL_SETTINGS_ID',-1);

	if (!defined('ID_ROLE_EDITOR')) define('ID_ROLE_EDITOR',3);
	if (!defined('ID_ROLE_SYS_ADMIN')) define('ID_ROLE_SYS_ADMIN',1);
	if (!defined('ID_ROLE_LEAD_EXPERT')) define('ID_ROLE_LEAD_EXPERT',2);

	if (!defined('TIMEOUT_COL_RETRIEVAL')) define('TIMEOUT_COL_RETRIEVAL',600); // secs.

	/*
	if (!defined('LANGUAGE_ID_DUTCH')) define('LANGUAGE_ID_DUTCH',24);
	if (!defined('LANGUAGE_ID_ENGLISH')) define('LANGUAGE_ID_ENGLISH',26);
	if (!defined('LANGUAGE_ID_SCIENTIFIC')) define('LANGUAGE_ID_SCIENTIFIC',123);
	if (!defined('LANGUAGECODE_DUTCH')) define('LANGUAGECODE_DUTCH',LANGUAGE_ID_DUTCH);
    */
	
	// Circumvent problem with configuration...
	if (!defined('LANGUAGECODE_ENGLISH')) define('LANGUAGECODE_ENGLISH',26);
	if (!defined('LANGUAGECODE_DUTCH')) define('LANGUAGECODE_DUTCH',24);
	
	if (!defined('MODCODE_INTRODUCTION')) define('MODCODE_INTRODUCTION',1);
	if (!defined('MODCODE_GLOSSARY')) define('MODCODE_GLOSSARY',2);
	if (!defined('MODCODE_LITERATURE')) define('MODCODE_LITERATURE',3);
	if (!defined('MODCODE_SPECIES')) define('MODCODE_SPECIES',4);
	if (!defined('MODCODE_HIGHERTAXA')) define('MODCODE_HIGHERTAXA',5);
	if (!defined('MODCODE_KEY')) define('MODCODE_KEY',6);
	if (!defined('MODCODE_MATRIXKEY')) define('MODCODE_MATRIXKEY',7);
	if (!defined('MODCODE_DISTRIBUTION')) define('MODCODE_DISTRIBUTION',8);
	if (!defined('MODCODE_CONTENT')) define('MODCODE_CONTENT',10);
	if (!defined('MODCODE_INDEX')) define('MODCODE_INDEX',11);
	if (!defined('MODCODE_UTILITIES')) define('MODCODE_UTILITIES',12);

	/*
	if (!defined('EMPIRE_RANK_ID')) define('EMPIRE_RANK_ID',1);
	if (!defined('KINGDOM_RANK_ID')) define('KINGDOM_RANK_ID',2);
	if (!defined('PHYLUM_RANK_ID')) define('PHYLUM_RANK_ID',7);
	if (!defined('CLASS_RANK_ID')) define('CLASS_RANK_ID',16);
	if (!defined('ORDO_RANK_ID')) define('ORDO_RANK_ID',38);
	if (!defined('FAMILY_RANK_ID')) define('FAMILY_RANK_ID',56);
	if (!defined('GENUS_RANK_ID')) define('GENUS_RANK_ID',63);
	if (!defined('SUBGENUS_RANK_ID')) define('SUBGENUS_RANK_ID',64);
	if (!defined('SPECIES_RANK_ID')) define('SPECIES_RANK_ID',74);
	if (!defined('SUBSPECIES_RANK_ID')) define('SUBSPECIES_RANK_ID',77);
	if (!defined('GRAFT_CHIMERA_RANK_ID')) define('GRAFT_CHIMERA_RANK_ID',88);
	*/

	if (!defined('PREDICATE_VALID_NAME')) define('PREDICATE_VALID_NAME','isValidNameOf');
	if (!defined('PREDICATE_PREFERRED_NAME')) define('PREDICATE_PREFERRED_NAME','isPreferredNameOf');
	if (!defined('PREDICATE_HOMONYM')) define('PREDICATE_HOMONYM','isHomonymOf');
	if (!defined('PREDICATE_BASIONYM')) define('PREDICATE_BASIONYM','isBasionymOf');
	if (!defined('PREDICATE_SYNONYM')) define('PREDICATE_SYNONYM','isSynonymOf');
	if (!defined('PREDICATE_SYNONYM_SL')) define('PREDICATE_SYNONYM_SL','isSynonymSLOf');
	if (!defined('PREDICATE_MISSPELLED_NAME')) define('PREDICATE_MISSPELLED_NAME','isMisspelledNameOf');
	if (!defined('PREDICATE_INVALID_NAME')) define('PREDICATE_INVALID_NAME','isInvalidNameOf');
	if (!defined('PREDICATE_ALTERNATIVE_NAME')) define('PREDICATE_ALTERNATIVE_NAME','isAlternativeNameOf');
	if (!defined('PREDICATE_NOMEN_NUDEM')) define('PREDICATE_NOMEN_NUDEM','isNomenNudemOf');
	if (!defined('PREDICATE_MISIDENTIFICATION')) define('PREDICATE_MISIDENTIFICATION','isMisidentificationOf');
	
	if (!defined('MATRIX_STYLE_DEFAULT')) define('MATRIX_STYLE_DEFAULT','default');
	if (!defined('MATRIX_STYLE_NBC')) define('MATRIX_STYLE_NBC','nbc');

	// test4