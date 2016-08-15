<?php

    // Use this script to extract a single project from a Linnaeus installation
    // with multiple projects. Make sure to ONLY WORK ON A BACKUP as deletion is
    // ruthless and irreversable!

	require_once( dirname(__FILE__) . '/../configuration/admin/constants.php' );
	$cfg = dirname(__FILE__) . '/../configuration/admin/configuration.php';

	// Get external settings
	if (!file_exists($cfg)) die("Unable to locate $cfg. This script should be in the root of a Linnaeus NG-installation");
	include($cfg);
	$c = new configuration;
	$s = $c->getDatabaseSettings();

 	$d = mysqli_connect($s['host'],$s['user'],$s['password'], $s['database']);
	mysqli_set_charset($d, 'utf8');
	mysqli_query($d, 'SET sql_mode = ""');

	$tables = array(
        'activity_log',
        'actors',
        'actors_addresses',
        'beelduitwisselaar_batches',
        'characteristics',
        'characteristics_chargroups',
        'characteristics_labels',
        'characteristics_labels_states',
        'characteristics_matrices',
        'characteristics_states',
        'chargroups',
        'chargroups_labels',
        'choices_content_keysteps',
        'choices_keysteps',
        'commonnames',
        'content',
        'content_free_modules',
        'content_introduction',
        'content_keysteps',
        'content_taxa',
        'diversity_index',
        'diversity_index_old',
        'dna_barcodes',
        'external_ids',
        'external_orgs',
        'free_module_media',
        'free_modules_pages',
        'free_modules_projects',
        'geodata_types',
        'geodata_types_titles',
        'glossary',
        'glossary_media',
        'glossary_media_captions',
        'glossary_synonyms',
        'gui_menu_order',
        'habitat_labels',
        'habitats',
        'hotwords',
        'introduction_media',
        'introduction_pages',
        'keysteps',
        'keysteps_taxa',
        'keytrees',
        'l2_diversity_index',
        'l2_maps',
        'l2_occurrences_taxa',
        'l2_occurrences_taxa_combi',
        'labels_languages',
        'labels_projects_ranks',
        'labels_sections',
        'languages_projects',
        'literature',
        'literature2',
        'literature2_authors',
        'literature2_publication_types',
        'literature2_publication_types_labels',
        'literature_taxa',
        'matrices',
        'matrices_names',
        'matrices_taxa',
        'matrices_taxa_states',
        'matrices_variations',
        'media',
        'media_captions',
        'media_conversion_log',
        'media_descriptions_taxon',
        'media_meta',
        'media_metadata',
        'media_modules',
        'media_tags',
        'media_taxon',
        'module_settings_values',
        'modules_projects',
        'name_types',
        'names',
        'names_additions',
        'nbc_extras',
        'nsr_ids',
        'occurrences_taxa',
        'pages_taxa',
        'pages_taxa_titles',
        'presence',
        'presence_labels',
        'presence_taxa',
        'projects_ranks',
        'projects_roles_users',
        'rdf',
        'sections',
        'settings',
        'synonyms',
        'tab_order',
        'taxa',
        'taxa_relations',
        'taxa_variations',
        'taxon_quick_parentage',
        'taxon_trend_years',
        'taxon_trends',
        'taxongroups',
        'taxongroups_labels',
        'taxongroups_taxa',
        'text_translations',
        'traits_groups',
        'traits_project_types',
        'traits_settings',
        'traits_taxon_freevalues',
        'traits_taxon_references',
        'traits_taxon_values',
        'traits_traits',
        'traits_values',
        'trash_can',
        'trend_sources',
        'user_item_access',
        'user_module_access',
        'users_taxa',
        'variation_relations',
        'variations_labels'
	);

	echo "Use this script to extract a one or more projects from a Linnaeus database.\n" .
        "Multiple projects should be comma separated.\n".
        "Please enter the project id(s) to keep: ";

	$handle = fopen("php://stdin","r");
    $ids = fgets($handle);
    // Clean up if multiple project are entered
    if (strpos($ids, ',') !== false) {
        $ids = implode(',', array_map('trim', explode(",", $ids)));
    }

    $q = "select `title` from `projects` where `id` in ($ids) order by `title`";
    $r = mysqli_query($d, $q) or die($q . mysqli_error($d));
    if (mysqli_num_rows($r) == 0) {
        die("ERROR! No projects matching id(s) $ids.\n");
    }

    while ($row = mysqli_fetch_array($r)) {
        $p[] = $row['title'];
    }
    $projects = implode(' and ', array_filter(array_merge(array(implode(', ',
        array_slice($p, 0, -1))), array_slice($p, -1)), 'strlen'));
	echo "Are you sure you want to delete all projects but $projects? (y/n): ";

    $handle = fopen("php://stdin","r");
    $line = fgets($handle);
    if (trim($line) != 'y'){
        die("Process aborted.\n");
    }
    fclose($handle);
    echo "\n\n";

    mysqli_query($d, "delete from `projects` where `id` not in ($ids)") or die($q . mysqli_error($d));
    foreach ($tables as $table) {
        echo "Clearing $table...\n";
        $q = "delete from `$table` where `project_id` not in ($ids)";
        mysqli_query($d, $q) or die($q . mysqli_error($d));
    }

    echo "\nReady!\n\n";




?>