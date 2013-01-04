<?php
	$projectId = '0584'; // pad with zeroes
	$tablePrefix = 'dev_';
	$pathToMM = '/Users/ruud/ETI/Zend workbenches/Current/Linnaeus NG/www/shared/media/project/' . $projectId . '/';
	
	$connect = mysql_connect('localhost', 'root', 'root') or die(mysql_error());
	mysql_select_db('linnaeus_ng') or die(mysql_error());

	$multimedia = array(
		'Huperzia selago' => array(
	
			'Huperzia selago.jpg' => 'overzicht; a= sporangium op vruchtbaar blad',
	
			'Huperzia selago 2.jpg' => 'vergroot',
	
			'Huperzia selago 3.jpg' => 'foto',
	
			'Huperzia selago 4.jpg' => 'foto'
	
		),
	
		'Lycopodiella inundata' => array(
	
			'Lycopodiella inundata.jpg' => 'overzicht; a = sporangium op vruchtbaar blad',
	
			'Lycopodiella inundata 2.jpg' => 'vergroot',
	
			'Lycopodiella inundata 3.jpg' => 'foto',
	
			'Lycopodiella inundata 4.jpg' => 'foto'
	
		),
	
		'Lycopodium tristachyum' => array(
	
			'Lycopodium tristachyum.jpg' => 'overzicht',
	
			'Lycopodium tristachyum 2.jpg' => 'vergroot'
	
		),
	
		'Lycopodium annotinum' => array(
	
			'Lycopodium annotinum.jpg' => 'overzicht; a = sporangium op vruchtbaar blad',
	
			'Lycopodium annotinum 2.jpg' => 'vergroot',
	
			'Lycopodium annotinum 3.jpg' => 'foto',
	
			'Lycopodium annotinum 4.jpg' => 'foto'
	
		),
	
		'Lycopodium clavatum' => array(
	
			'Lycopodium clavatum.jpg' => 'overzicht; a = sporangium op vruchtbaar blad',
	
			'Lycopodium clavatum 2.jpg' => 'vergroot',
	
			'Lycopodium clavatum 3.jpg' => 'foto',
	
			'Lycopodium clavatum 4.jpg' => 'foto',
	
			'Lycopodium complanatum 3.jpg' => 'foto, Vlakke wolfsklauw, zie opmerking'
	
		),
	
		'Isoetes lacustris' => array(
	
			'Isoetes lacustris.jpg' => 'overzicht; a = bladvoet met sporangium',
	
			'Isoetes lacustris 2.jpg' => 'vergroot; a = bladvoet met sporangium, b = macrosporen',
	
			'Isoetes lacustris 3.jpg' => 'foto, habitus',
	
			'Isoetes lacustris a.jpg' => 'foto met scanning electronen microscoop, macrospore',
	
			'Isoetes lacustris b.jpg' => 'foto met scanning electronen microscoop, macrospore'
	
		),
	
		'Isoetes echinospora' => array(
	
			'Isoetes echinospora.jpg' => 'overzicht; macrosporen',
	
			'Isoetes echinospora 3.jpg' => 'foto, habitus',
	
			'Isoetes echinospora 4.jpg' => 'foto, habitus',
	
			'Isoetes echinospora a.jpg' => 'foto met scanning electronen microscoop, macrospore',
	
			'Isoetes echinospora b.jpg' => 'foto met scanning electronen microscoop, macrospore'
	
		),
	
		'Ophioglossum vulgatum' => array(
	
			'Ophioglossum vulgatum.jpg' => 'overzicht',
	
			'Ophioglossum vulgatum 2.jpg' => 'vergroot',
	
			'Ophioglossum vulgatum 3.jpg' => 'foto',
	
			'Ophioglossum vulgatum 4.jpg' => 'foto'
	
		),
	
		'Ophioglossum azoricum' => array(
	
			'Ophioglossum azoricum.jpg' => 'overzicht',
	
			'Ophioglossum azoricum 2.jpg' => 'foto',
	
			'Ophioglossum azoricum 3.jpg' => 'foto'
	
		),
	
		'Botrychium lunaria' => array(
	
			'Botrychium lunaria.jpg' => 'overzicht; a = bladnervatuur, b = sporangi‘n',
	
			'Botrychium lunaria 2.jpg' => 'vergroot',
	
			'Botrychium lunaria 3.jpg' => 'foto'
	
		),
	
		'Botrychium matricariifolium' => array(
	
			'Botrychium matricariifolium.jpg' => 'overzicht foto',
	
			'Botrychium matricariifol 3.jpg' => 'foto'
	
		),
	
		'Equisetum ramosissimum' => array(
	
			'Equisetum ramosissimum.jpg' => 'overzicht foto',
	
			'Equisetum ramosissimum 3.jpg' => 'foto, fertiele stengels met aarvormige sporangioforen'
	
		),
	
		'Equisetum hyemale' => array(
	
			'Equisetum hyemale.jpg' => 'overzicht',
	
			'Equisetum hyemale 2.jpg' => 'vergroot',
	
			'Equisetum hyemale 3.jpg' => 'foto, fertiele stengels met aarvormige sporangioforen'
	
		),
	
		'Equisetum variegatum' => array(
	
			'Equisetum variegatum.jpg' => 'overzicht',
	
			'Equisetum variegatum 2.jpg' => 'vergroot',
	
			'Equisetum variegatum 3.jpg' => 'foto, fertiele stengel met aarvormige sporangiofoor',
	
			'Equisetum variegatum 4.jpg' => 'foto, fertiele stengels met aarvormige sporangioforen',
	
			'Equisetum variegatum a.jpg' => 'dwarsdoorsnede stengel'
	
		),
	
		'Equisetum trachyodon(x)' => array(
	
			'Equisetum trachyodon(x).jpg' => 'overzicht',
	
			'Equisetum trachyodon(x) 2.jpg' => 'vergroot'
	
		),
	
		'Equisetum telmateia' => array(
	
			'Equisetum telmateia.jpg' => 'overzicht',
	
			'Equisetum telmateia 2.jpg' => 'vergroot',
	
			'Equisetum telmateia 3.jpg' => 'foto, steriele stengels',
	
			'Equisetum telmateia 4.jpg' => 'foto, fertiele stengels met aarvormige sporangioforen',
	
			'Equisetum telmateia 5.jpg' => 'foto, fertiele stengels met aarvormige sporangioforen',
	
			'Equisetum telmateia 6.jpg' => 'foto, fertiele stengels met aarvormige sporangioforen'
	
		),
	
		'Equisetum sylvaticum' => array(
	
			'Equisetum sylvaticum.jpg' => 'overzicht',
	
			'Equisetum sylvaticum 2.jpg' => 'vergroot',
	
			'Equisetum sylvaticum 3.jpg' => 'foto, steriele stengel',
	
			'Equisetum sylvaticum 4.jpg' => 'foto, fertiele stengels met aarvormige sporangioforen'
	
		),
	
		'Equisetum fluviatile' => array(
	
			'Equisetum fluviatile.jpg' => 'overzicht',
	
			'Equisetum fluviatile 2.jpg' => 'vergroot',
	
			'Equisetum fluviatile 3.jpg' => 'foto, steriele en fertiele stengels',
	
			'Equisetum fluviatile 4.jpg' => 'foto, fertiele stengel met aarvormige sporangiofoor',
	
			'Equisetum fluviatile a.jpg' => 'dwarsdoorsnede stengel'
	
		),
	
		'Equisetum litorale(x)' => array(
	
			'Equisetum litorale(x).jpg' => 'overzicht',
	
			'Equisetum litorale(x) 2.jpg' => 'foto, steriele stengels',
	
			'Equisetum litorale(x) 3.jpg' => 'foto, steriele stengels',
	
			'Equisetum litorale(x) a.jpg' => 'dwarsdoorsnede stengel'
	
		),
	
		'Equisetum palustre' => array(
	
			'Equisetum palustre.jpg' => 'overzicht',
	
			'Equisetum palustre 2.jpg' => 'vergroot; a = uitzonderingsvorm',
	
			'Equisetum palustre 3.jpg' => 'foto, fertiele stengel met aarvormige sporangiofoor',
	
			'Equisetum palustre 4.jpg' => 'foto, fertiele stengels met aarvormige sporangioforen',
	
			'Equisetum palustre a.jpg' => 'dwarsdoorsnede stengel'
	
		),
	
		'Equisetum arvense' => array(
	
			'Equisetum arvense.jpg' => 'overzicht',
	
			'Equisetum arvense 2.jpg' => 'vergroot',
	
			'Equisetum arvense 3.jpg' => 'foto, steriele stengel',
	
			'Equisetum arvense 4.jpg' => 'foto, fertiele stengels met aarvormige sporangioforen',
	
			'Equisetum arvense a.jpg' => 'dwarsdoorsnede stengel'
	
		),
	
		'Osmunda regalis' => array(
	
			'Osmunda regalis.jpg' => 'overzicht',
	
			'Osmunda regalis 2.jpg' => 'vergroot',
	
			'Osmunda regalis 3.jpg' => 'foto'
	
		),
	
		'Salvinia natans' => array(
	
			'Salvinia natans.jpg' => 'overzicht; a = papil op bovenzijde blad',
	
			'Salvinia natans 2.jpg' => 'vergroot'
	
		),
	
		'Salvinia molesta' => array(
	
			'Salvinia molesta.jpg' => 'overzicht foto',
	
			'Salvinia molesta 2.jpg' => 'vergroot; papil op bovenzijde blad',
	
			'Salvinia molesta 3.jpg' => 'foto, habitus'
	
		),
	
		'Azolla filiculoides' => array(
	
			'Azolla filiculoides.jpg' => 'overzicht foto',
	
			'Azolla filiculoides 2.jpg' => 'detail; glochidi‘n',
	
			'Azolla filiculoides 3.jpg' => 'foto',
	
			'Azolla filiculoides 4.jpg' => 'foto'
	
		),
	
		'Azolla cristata' => array(
	
			'Azolla cristata.jpg' => 'overzicht; glochidi‘n'
	
		),
	
		'Pilularia globulifera' => array(
	
			'Pilularia globulifera.jpg' => 'overzicht',
	
			'Pilularia globulifera 2.jpg' => 'vergroot; a = sporocarp, l.dsn., b = dws. dsn.',
	
			'Pilularia globulifera 3.jpg' => 'foto'
	
		),
	
		'Marsilea quadrifolia' => array(
	
			'Marsilea quadrifolia.jpg' => 'overzicht',
	
			'Marsilea quadrifolia 2.jpg' => 'vergroot, met detail sporocarpen',
	
			'Marsilea quadrifolia 3.jpg' => 'habitus'
	
		),
	
		'Pteridium aquilinum' => array(
	
			'Pteridium aquilinum.jpg' => 'overzicht',
	
			'Pteridium aquilinum 2.jpg' => "vergroot; a = vruchtbare bladslip, b = detail bladrand met sporangi‘n, c = sporangi‘n verwijderd, 'binnenste' dekvliesje zichtbaar",
	
			'Pteridium aquilinum 3.jpg' => 'foto'
	
		),
	
		'Pteris cretica' => array(
	
			'Pteris cretica.jpg' => 'overzicht',
	
			'Pteris cretica 2.jpg' => 'blad',
	
			'Pteris cretica 3.jpg' => 'foto, habitus'
	
		),
	
		'Adiantum diaphanum' => array(
	
			'Adiantum diaphanum.jpg' => 'overzicht',
	
			'Adiantum diaphanum 2.jpg' => 'foto, habitus',
	
			'Adiantum diaphanum 3.jpg' => 'foto, habitus'
	
		),
	
		'Adiantum raddianum' => array(
	
			'Adiantum raddianum.jpg' => 'overzicht',
	
			'Adiantum raddianum 2.jpg' => 'foto, habitus'
	
		),
	
		'Adiantum capillus-veneris' => array(
	
			'Adiantum capillus-veneris.jpg' => 'overzicht',
	
			'Adiantum capillus-veneris 2.jpg' => 'habitus',
	
			'Adiantum capillus-veneris 3.jpg' => 'foto, habitus'
	
		),
	
		'Polypodium vulgare' => array(
	
			'Polypodium vulgare.jpg' => 'overzicht',
	
			'Polypodium vulgare 2.jpg' => 'vergroot',
	
			'Polypodium vulgare 3.jpg' => 'foto',
	
			'Polypodium vulgare 4.jpg' => 'foto'
	
		),
	
		'Polypodium interjectum' => array(
	
			'Polypodium interjectum.jpg' => 'overzicht foto',
	
			'Polypodium interjectum 3.jpg' => 'foto',
	
			'Polypodium interjectum 4.jpg' => 'foto'
	
		),
	
		'Polystichum lonchitis' => array(
	
			'Polystichum lonchitis.jpg' => 'overzicht; a = bladslip met sporangi‘n',
	
			'Polystichum lonchitis 2.jpg' => 'vergroot',
	
			'Polystichum lonchitis 3.jpg' => 'foto'
	
		),
	
		'Polystichum aculeatum' => array(
	
			'Polystichum aculeatum.jpg' => 'overzicht; a = bladslip met sporangi‘n',
	
			'Polystichum aculeatum 2.jpg' => 'vergroot',
	
			'Polystichum aculeatum 3.jpg' => 'foto',
	
			'Polystichum aculeatum 4.jpg' => 'foto'
	
		),
	
		'Polystichum setiferum' => array(
	
			'Polystichum setiferum.jpg' => 'overzicht; a = bladslip met sporangi‘n',
	
			'Polystichum setiferum 2.jpg' => 'vergroot'
	
		),
	
		'Dryopteris cristata' => array(
	
			'Dryopteris cristata.jpg' => 'overzicht; a = bladslip met sporangiän',
	
			'Dryopteris cristata 2.jpg' => 'vergroot',
	
			'Dryopteris cristata 3.jpg' => 'foto'
	
		),
	
		'Dryopteris filix-mas' => array(
	
			'Dryopteris filix-mas.jpg' => 'overzicht',
	
			'Dryopteris filix-mas 2.jpg' => 'vergroot',
	
			'Dryopteris filix-mas 3.jpg' => 'foto',
	
			'Dryopteris filix-mas 4.jpg' => 'foto'
	
		),
	
		'Dryopteris affinis' => array(
	
			'Dryopteris affinis.jpg' => 'overzicht foto',
	
			'Dryopteris affinis 3.jpg' => 'foto',
	
			'Dryopteris affinis 4.jpg' => 'foto'
	
		),
	
		'Dryopteris carthusiana' => array(
	
			'Dryopteris carthusiana.jpg' => 'overzicht; a = bladslip met sporangi‘n, b = bladsteel-schub',
	
			'Dryopteris carthusiana 2.jpg' => 'vergroot'
	
		),
	
		'Dryopteris dilatata' => array(
	
			'Dryopteris dilatata.jpg' => 'overzicht; a = bladslip met sporangi‘n, b = bladsteel-schub',
	
			'Dryopteris dilatata 2.jpg' => 'vergroot',
	
			'Dryopteris dilatata 3.jpg' => 'foto',
	
			'Dryopteris dilatata 4.jpg' => 'foto',
	
			'Dryopteris expansa 3.jpg' => 'foto, Tere stekelvaren (zie opmerking)',
	
			'Dryopteris expansa 4.jpg' => 'foto, Tere stekelvaren (zie opmerking)'
	
		),
	
		'Cyrtomium falcatum' => array(
	
			'Cyrtomium falcatum.jpg' => 'overzicht foto',
	
			'Cyrtomium falcatum 3.jpg' => 'foto, habitus',
	
			'Cyrtomium falcatum 4.jpg' => 'foto, habitus',
	
			'Cyrtomium falcatum 5.jpg' => 'foto, detail blad',
	
			'Cyrtomium falcatum 6.jpg' => 'foto, onderzijde blad',
	
			'Cyrtomium falcatum 7.jpg' => 'foto, detail dekvliesjes over sporangia'
	
		),
	
		'Blechnum spicant' => array(
	
			'Blechnum spicant.jpg' => 'overzicht',
	
			'Blechnum spicant 2.jpg' => 'vergroot; a = bladslip, b = bladslip met sporangi‘nhoopjes, c = dws.dsn. van b.',
	
			'Blechnum spicant 3.jpg' => 'foto'
	
		),
	
		'Onoclea sensibilis' => array(
	
			'Onoclea sensibilis.jpg' => 'overzicht',
	
			'Onoclea sensibilis 2.jpg' => 'vergroot',
	
			'Onoclea sensibilis 3.jpg' => 'foto',
	
			'Onoclea sensibilis 4.jpg' => 'foto'
	
		),
	
		'Matteuccia struthiopteris' => array(
	
			'Matteuccia struthiopteris.jpg' => 'overzicht foto',
	
			'Matteuccia struthiopteris 3.jpg' => 'foto',
	
			'Matteuccia struthiopteris 4.jpg' => 'foto'
	
		),
	
		'Asplenium scolopendrium' => array(
	
			'Asplenium scolopendrium.jpg' => 'overzicht',
	
			'Asplenium scolopendrium 2.jpg' => 'vergroot; a = detail met sporangi‘n',
	
			'Asplenium scolopendrium 3.jpg' => 'foto',
	
			'Asplenium scolopendrium 4.jpg' => 'foto'
	
		),
	
		'Asplenium ceterach' => array(
	
			'Asplenium ceterach.jpg' => 'overzicht; a = bladslip met schubben, b = schubben verwijderd, de sporangi‘n zichtbaar',
	
			'Asplenium ceterach 2.jpg' => 'vergroot',
	
			'Asplenium ceterach 3.jpg' => 'foto'
	
		),
	
		'Asplenium trichomanes' => array(
	
			'Asplenium trichomanes.jpg' => 'overzicht; a = bladslip met sporangi‘n',
	
			'Asplenium trichomanes 2.jpg' => 'vergroot',
	
			'Asplenium trichomanes 3.jpg' => 'foto'
	
		),
	
		'Asplenium viride' => array(
	
			'Asplenium viride.jpg' => 'overzicht; a = bladslip met sporangi‘n',
	
			'Asplenium viride 2.jpg' => 'vergroot',
	
			'Asplenium viride 3.jpg' => 'foto'
	
		),
	
		'Asplenium septentrionale' => array(
	
			'Asplenium septentrionale.jpg' => 'overzicht; a = bladslip met sporangi‘n',
	
			'Asplenium septentrionale 2.jpg' => 'vergroot',
	
			'Asplenium septentrionale 3.jpg' => 'foto',
	
			'Asplenium septentrionale 4.jpg' => 'foto'
	
		),
	
		'Asplenium ruta-muraria' => array(
	
			'Asplenium ruta-muraria.jpg' => 'overzicht; a = bladslippen met sporangi‘n',
	
			'Asplenium ruta-muraria 2.jpg' => 'vergroot',
	
			'Asplenium ruta-muraria 3.jpg' => 'foto'
	
		),
	
		'Asplenium adiantum-nigrum' => array(
	
			'Asplenium adiantum-nigrum.jpg' => 'overzicht; a = bladslip met sporangi‘n',
	
			'Asplenium adiantum-nigrum 2.jpg' => 'vergroot',
	
			'Asplenium adiantum-nigrum 3.jpg' => 'foto'
	
		),
	
		'Asplenium fontanum' => array(
	
			'Asplenium fontanum.jpg' => 'overzicht; a = bladslip met sporangi‘n',
	
			'Asplenium fontanum 2.jpg' => 'vergroot'
	
		),
	
		'Asplenium foreziense' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Oreopteris limbosperma' => array(
	
			'Oreopteris limbosperma.jpg' => 'overzicht',
	
			'Oreopteris limbosperma 2.jpg' => 'vergroot; a = bladslip met sporangi‘n, b = detail',
	
			'Oreopteris limbosperma 3.jpg' => 'foto',
	
			'Oreopteris limbosperma 4.jpg' => 'foto'
	
		),
	
		'Phegopteris connectilis' => array(
	
			'Phegopteris connectilis.jpg' => 'overzicht',
	
			'Phegopteris connectilis 2.jpg' => 'vergroot; a = bladslip met sporangi‘n, b= detail',
	
			'Phegopteris connectilis 3.jpg' => 'foto'
	
		),
	
		'Thelypteris palustris' => array(
	
			'Thelypteris palustris.jpg' => 'overzicht',
	
			'Thelypteris palustris 2.jpg' => 'vergroot; a = bladslip met sporangi‘n, b = detail',
	
			'Thelypteris palustris 3.jpg' => 'foto, habitus'
	
		),
	
		'Athyrium filix-femina' => array(
	
			'Athyrium filix-femina.jpg' => 'overzicht',
	
			'Athyrium filix-femina 2.jpg' => 'vergroot; a = bladslip met sporangi‘n, b = detail van a',
	
			'Athyrium filix-femina 3.jpg' => 'foto',
	
			'Athyrium filix-femina 4.jpg' => 'foto'
	
		),
	
		'Cystopteris fragilis' => array(
	
			'Cystopteris fragilis.jpg' => 'overzicht; a = bladslip met sporangi‘n',
	
			'Cystopteris fragilis 2.jpg' => 'vergroot',
	
			'Cystopteris fragilis 3.jpg' => 'foto',
	
			'Cystopteris fragilis 4.jpg' => 'foto',
	
			'Cystopteris fragilis 5.jpg' => 'foto'
	
		),
	
		'Gymnocarpium dryopteris' => array(
	
			'Gymnocarpium dryopteris.jpg' => 'overzicht; a = bladslip met sporangi‘n',
	
			'Gymnocarpium dryopteris 2.jpg' => 'vergroot',
	
			'Gymnocarpium dryopteris 3.jpg' => 'foto'
	
		),
	
		'Gymnocarpium robertianum' => array(
	
			'Gymnocarpium robertianum.jpg' => 'overzicht; a = bladslip met sporangi‘n',
	
			'Gymnocarpium robertianum 2.jpg' => 'vergroot',
	
			'Gymnocarpium robertianum 3.jpg' => 'foto',
	
			'Gymnocarpium robertianum 4.jpg' => 'foto'
	
		),
	
		'Abies grandis' => array(
	
			'Abies grandis.jpg' => 'overzicht; a = kegel',
	
			'Abies grandis 2.jpg' => 'vergroot; a = kegel, b = tak, bovenaanzicht',
	
			'Abies grandis 3.jpg' => 'foto, bovenzijde naalden',
	
			'Abies grandis 4.jpg' => 'foto, onderzijde naalden'
	
		),
	
		'Abies alba' => array(
	
			'Abies alba.jpg' => 'overzicht',
	
			'Abies alba 2.jpg' => 'vergroot; a = buitenzijde kegelschub, b =  binnenzijde kegelschub met 2 gevleugelde zaden',
	
			'Abies alba 3.jpg' => 'foto, bovenzijde naalden',
	
			'Abies alba 4.jpg' => 'foto, onderzijde naalden'
	
		),
	
		'Abies veitchii' => array(
	
			'Abies veitchii.jpg' => 'overzicht; a = kegel',
	
			'Abies veitchii 2.jpg' => 'vergroot; a = kegel, b = tak, bovenaanzicht'
	
		),
	
		'Abies nordmanniana' => array(
	
			'Abies nordmanniana.jpg' => 'overzicht; a = kegel, b = tak, bovenaanzicht',
	
			'Abies nordmanniana 2.jpg' => 'vergroot',
	
			'Abies nordmanniana 3.jpg' => 'foto, bovenzijde naalden',
	
			'Abies nordmanniana 4.jpg' => 'foto, onderzijde naalden'
	
		),
	
		'Pseudotsuga menziesii' => array(
	
			'Pseudotsuga menziesii.jpg' => 'overzicht; a = kegel, b = tak',
	
			'Pseudotsuga menziesii 2.jpg' => 'vergroot',
	
			'Pseudotsuga menziesii 3.jpg' => 'foto'
	
		),
	
		'Tsuga heterophylla' => array(
	
			'Tsuga heterophylla.jpg' => 'overzicht; a = tak + kegel',
	
			'Tsuga heterophylla 2.jpg' => 'vergroot; a = tak + kegel, b = blad, onderzijde + detail',
	
			'Tsuga heterophylla 3.jpg' => 'foto, tak bovenzijde naalden',
	
			'Tsuga heterophylla 4.jpg' => 'foto, tak onderzijde naalden'
	
		),
	
		'Tsuga canadensis' => array(
	
			'Tsuga canadensis.jpg' => 'overzicht; a = kegel, b = tak',
	
			'Tsuga canadensis 2.jpg' => 'vergroot; a = kegel, b = tak, c = blad, onderzijde + detail',
	
			'Tsuga canadensis 3.jpg' => 'foto, tak bovenzijde naalden',
	
			'Tsuga canadensis 4.jpg' => 'foto, tak onderzijde naalden'
	
		),
	
		'Picea sitchensis' => array(
	
			'Picea sitchensis.jpg' => 'overzicht; a = kegel, b = tak',
	
			'Picea sitchensis 2.jpg' => 'vergroot',
	
			'Picea sitchensis 3.jpg' => 'foto'
	
		),
	
		'Picea omorika' => array(
	
			'Picea omorika.jpg' => 'overzicht; a = kegel, b = tak',
	
			'Picea omorika 2.jpg' => 'vergroot',
	
			'Picea omorika 3.jpg' => 'foto',
	
			'Picea omorika 4.jpg' => 'foto'
	
		),
	
		'Picea orientalis' => array(
	
			'Picea orientalis.jpg' => 'overzicht; a = kegel, b = tak',
	
			'Picea orientalis 2.jpg' => 'vergroot',
	
			'Picea orientalis 3.jpg' => 'foto',
	
			'Picea orientalis 4.jpg' => 'foto'
	
		),
	
		'Picea abies' => array(
	
			'Picea abies.jpg' => 'overzicht',
	
			'Picea abies 2.jpg' => 'vergroot; a = kegelschub met 2 gevleugelde zaden',
	
			'Picea abies 3.jpg' => 'foto'
	
		),
	
		'Picea pungens' => array(
	
			'Picea pungens.jpg' => 'overzicht; a = kegel, b = tak',
	
			'Picea pungens 2.jpg' => 'vergroot'
	
		),
	
		'Larix decidua' => array(
	
			'Larix decidua.jpg' => 'overzicht; a = in bloei, b = kegel',
	
			'Larix decidua 2.jpg' => 'vergroot',
	
			'Larix decidua 3.jpg' => 'foto',
	
			'Larix decidua a.jpg' => 'kegel',
	
			'Larix marschlinsii(x) a.jpg' => '(= L. decidua x L. kaempferi)  kegel'
	
		),
	
		'Larix kaempferi' => array(
	
			'Larix kaempferi.jpg' => 'overzicht; a = kegel, b = tak',
	
			'Larix kaempferi 2.jpg' => 'vergroot',
	
			'Larix kaempferi 3.jpg' => 'foto',
	
			'Larix kaempferi 4.jpg' => 'foto',
	
			'Larix kaempferi 5.jpg' => 'foto',
	
			'Larix kaempferi a.jpg' => 'kegel',
	
			'Larix marschlinsii(x) a.jpg' => '(= L. decidua x L. kaempferi)  kegel'
	
		),
	
		'Pinus strobus' => array(
	
			'Pinus strobus.jpg' => 'overzicht; a = kegel, b = tak',
	
			'Pinus strobus 2.jpg' => 'vergroot',
	
			'Pinus strobus 3.jpg' => 'foto',
	
			'Pinus strobus 4.jpg' => 'foto'
	
		),
	
		'Pinus pinaster' => array(
	
			'Pinus pinaster.jpg' => 'overzicht; a = kegel, b = tak',
	
			'Pinus pinaster 2.jpg' => 'vergroot',
	
			'Pinus pinaster 3.jpg' => 'foto',
	
			'Pinus pinaster 4.jpg' => 'foto',
	
			'Pinus pinaster 5.jpg' => 'foto',
	
			'Pinus pinaster 6.jpg' => 'foto'
	
		),
	
		'Pinus nigra' => array(
	
			'Pinus nigra.jpg' => 'overzicht; a = tak met manlijke bloeiwijzen',
	
			'Pinus nigra 2.jpg' => 'vergroot; a = tak met manlijke bloeiwijzen, b = kegel',
	
			'Pinus nigra 3.jpg' => 'foto',
	
			'Pinus nigra 4.jpg' => 'foto'
	
		),
	
		'Pinus nigra var. nigra' => array(
	
			'Pinus nigra nigra.jpg' => 'overzicht',
	
			'Pinus nigra nigra 2.jpg' => 'foto, boom',
	
			'Pinus nigra nigra 3.jpg' => 'foto, bast'
	
		),
	
		'Pinus nigra var. maritima' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Pinus sylvestris' => array(
	
			'Pinus sylvestris.jpg' => 'overzicht; a = tak met manlijke en vrouwelijke bloeiwijzen, b = jonge kegel, c = rijpe kegel',
	
			'Pinus sylvestris 2.jpg' => 'vergroot',
	
			'Pinus sylvestris 3.jpg' => 'foto',
	
			'Pinus sylvestris 4.jpg' => 'foto',
	
			'Pinus sylvestris 5.jpg' => 'foto',
	
			'Pinus sylvestris 6.jpg' => 'foto',
	
			'Pinus sylvestris 7.jpg' => 'foto, bast'
	
		),
	
		'Pinus mugo' => array(
	
			'Pinus mugo.jpg' => 'overzicht; a = manlijke bloeiwijzen, b = kegel',
	
			'Pinus mugo 2.jpg' => 'vergroot',
	
			'Pinus mugo 3.jpg' => 'foto'
	
		),
	
		'Pinus banksiana' => array(
	
			'Pinus banksiana.jpg' => 'overzicht foto',
	
			'Pinus banksiana 2.jpg' => 'vergroot; a = kegel, b = tak',
	
			'Pinus banksiana 3.jpg' => 'overzicht',
	
			'Pinus banksiana 4.jpg' => 'vergroot'
	
		),
	
		'Chamaecyparis lawsoniana' => array(
	
			'Chamaecyparis lawsoniana.jpg' => 'overzicht foto',
	
			'Chamaecyparis lawsoniana 3.jpg' => 'foto',
	
			'Chamaecyparis lawsoniana 4.jpg' => 'foto'
	
		),
	
		'Thuja plicata' => array(
	
			'Thuja plicata.jpg' => 'overzicht foto',
	
			'Thuja plicata 3.jpg' => 'foto, tak met kegels',
	
			'Thuja plicata 4.jpg' => 'foto, tak met kegel',
	
			'Thuja plicata 5.jpg' => 'foto, bast'
	
		),
	
		'Thuja occidentalis' => array(
	
			'Thuja occidentalis.jpg' => 'overzicht',
	
			'Thuja occidentalis 3.jpg' => 'foto, tak',
	
			'Thuja occidentalis 4.jpg' => 'foto, tak'
	
		),
	
		'Juniperus communis' => array(
	
			'Juniperus communis.jpg' => 'overzicht; a = manlijke, b = vrouwelijke plant, c = manlijke kegel, d = kegelbes',
	
			'Juniperus communis 2.jpg' => 'vergroot',
	
			'Juniperus communis 3.jpg' => 'foto, mannelijke plant',
	
			'Juniperus communis 4.jpg' => 'foto, vrouwelijke plant',
	
			'Juniperus communis 5.jpg' => 'foto, mannelijke plant',
	
			'Juniperus communis 6.jpg' => 'foto, vrouwelijke plant'
	
		),
	
		'Taxus baccata' => array(
	
			'Taxus baccata.jpg' => 'overzicht; a = vrouwelijke, b = manlijke plant',
	
			'Taxus baccata 2.jpg' => 'vergroot',
	
			'Taxus baccata 3.jpg' => 'foto, rijpe zaden met bekervormig vlezig omhulsel',
	
			'Taxus baccata 4.jpg' => 'foto, jonge vrouwelijke kegels',
	
			'Taxus baccata 5.jpg' => 'foto, mannelijke bloeiwijze',
	
			'Taxus baccata 6.jpg' => 'foto, rijpe en onrijpe zaden met bekervormig vlezig omhulsel'
	
		),
	
		'Nymphaea alba' => array(
	
			'Nymphaea alba.jpg' => 'overzicht',
	
			'Nymphaea alba 2.jpg' => 'vergroot; a = dws.dsn. bloem, b = vrucht',
	
			'Nymphaea alba 3.jpg' => 'foto',
	
			'Nymphaea alba 4.jpg' => 'foto, bloem en drijvende zaden',
	
			'Nymphaea alba 5.jpg' => 'foto, drijvende zaden'
	
		),
	
		'Nuphar lutea' => array(
	
			'Nuphar lutea.jpg' => 'overzicht',
	
			'Nuphar lutea 2.jpg' => 'vergroot; a =  dws.dsn. bloem, b = vrucht',
	
			'Nuphar lutea 3.jpg' => 'foto'
	
		),
	
		'Acorus calamus' => array(
	
			'Acorus calamus.jpg' => 'overzicht',
	
			'Acorus calamus 2.jpg' => 'vergroot',
	
			'Acorus calamus 3.jpg' => 'foto, vruchtkolf',
	
			'Acorus calamus 4.jpg' => 'foto, bladen',
	
			'Acorus calamus 5.jpg' => 'foto, bloeikolf'
	
		),
	
		'Calla palustris' => array(
	
			'Calla palustris.jpg' => 'overzicht',
	
			'Calla palustris 2.jpg' => 'vergroot',
	
			'Calla palustris 3.jpg' => 'foto'
	
		),
	
		'Lysichiton amerianus' => array(
	
			'Lysichiton americanus.jpg' => 'overzicht',
	
			'Lysichiton americanus 2.jpg' => 'vergroot'
	
		),
	
		'Arum maculatum' => array(
	
			'Arum maculatum.jpg' => 'overzicht',
	
			'Arum maculatum 2.jpg' => 'vergroot; a = bloeikolf, b = dsn. vrouwelijke bloem, c = vruchten',
	
			'Arum maculatum 3.jpg' => 'foto, bloeikolven met schutblad',
	
			'Arum maculatum 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Arum italicum' => array(
	
			'Arum italicum.jpg' => 'overzicht',
	
			'Arum italicum 2.jpg' => 'vergroot; a = bloeikolf',
	
			'Arum italicum 3.jpg' => 'foto, bloeiwijze',
	
			'Arum italicum 4.jpg' => 'foto, habitus',
	
			'Arum italicum 5.jpg' => 'foto, bloeikolf met schutblad'
	
		),
	
		'Pistia stratiotes' => array(
	
			'Pistia stratiotes.jpg' => 'overzicht foto',
	
			'Pistia stratiotes 3.jpg' => 'foto'
	
		),
	
		'Wolffia arrhiza' => array(
	
			'Wolffia arrhiza.jpg' => 'overzicht foto',
	
			'Wolffia arrhiza 2.jpg' => 'habitus',
	
			'Wolffia arrhiza 3.jpg' => 'foto, habitus',
	
			'Wolffia arrhiza 4.jpg' => 'foto, habitus'
	
		),
	
		'Lemna trisulca' => array(
	
			'Lemna trisulca.jpg' => 'overzicht',
	
			'Lemna trisulca 2.jpg' => 'habitus',
	
			'Lemna trisulca 3.jpg' => 'foto'
	
		),
	
		'Lemna gibba' => array(
	
			'Lemna gibba.jpg' => 'overzicht',
	
			'Lemna gibba 2.jpg' => 'habitus',
	
			'Lemna gibba 3.jpg' => 'foto'
	
		),
	
		'Lemna minuta' => array(
	
			'Lemna minuta.jpg' => 'overzicht',
	
			'Lemna minuta 2.jpg' => 'habitus'
	
		),
	
		'Lemna turionifera' => array(
	
			'Lemna turionifera.jpg' => 'overzicht',
	
			'Lemna turionifera 2.jpg' => 'habitus'
	
		),
	
		'Lemna minor' => array(
	
			'Lemna minor.jpg' => 'overzicht',
	
			'Lemna minor 2.jpg' => 'habitus',
	
			'Lemna minor 3.jpg' => 'foto'
	
		),
	
		'Spirodela polyrhiza' => array(
	
			'Spirodela polyrhiza.jpg' => 'overzicht',
	
			'Spirodela polyrhiza 2.jpg' => 'habitus',
	
			'Spirodela polyrhiza 3.jpg' => 'foto, habitus'
	
		),
	
		'Butomus umbellatus' => array(
	
			'Butomus umbellatus.jpg' => 'overzicht',
	
			'Butomus umbellatus 2.jpg' => 'vergroot; a = dsn. bloem, b = vrucht',
	
			'Butomus umbellatus 3.jpg' => 'foto',
	
			'Butomus umbellatus 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Hydrocharis morsus-ranae' => array(
	
			'Hydrocharis morsus-ranae.jpg' => 'overzicht',
	
			'Hydrocharis morsus-ranae 2.jpg' => 'vergroot; a = vrouwelijke bloem, b = manlijke bloem',
	
			'Hydrocharis morsus-ranae 3.jpg' => 'foto'
	
		),
	
		'Stratiotes aloides' => array(
	
			'Stratiotes aloides.jpg' => 'overzicht',
	
			'Stratiotes aloides 2.jpg' => 'vergroot',
	
			'Stratiotes aloides 3.jpg' => 'foto, habitus bloeiend',
	
			'Stratiotes aloides 4.jpg' => 'foto, vrouwelijke bloem',
	
			'Stratiotes aloides 5.jpg' => 'foto, habitus'
	
		),
	
		'Egeria densa' => array(
	
			'Egeria densa.jpg' => 'overzicht',
	
			'Egeria densa 2.jpg' => 'habitus'
	
		),
	
		'Elodea canadensis' => array(
	
			'Elodea canadensis.jpg' => 'overzicht',
	
			'Elodea canadensis 2.jpg' => 'vergroot; a = blad, b = bloem',
	
			'Elodea canadensis 3.jpg' => 'foto'
	
		),
	
		'Elodea nuttallii' => array(
	
			'Elodea nuttallii.jpg' => 'overzicht; a = blad, b = bloem',
	
			'Elodea nuttallii 2.jpg' => 'vergroot',
	
			'Elodea nuttallii 3.jpg' => 'foto'
	
		),
	
		'Vallisneria spiralis' => array(
	
			'Vallisneria spiralis.jpg' => 'overzicht',
	
			'Vallisneria spiralis 2.jpg' => 'vergroot; a = vrouwelijke plant, b = manlijke plant, c = vrouwelijke bloem'
	
		),
	
		'Najas marina' => array(
	
			'Najas marina.jpg' => 'overzicht',
	
			'Najas marina 2.jpg' => 'vergroot; a = blad, b = manlijke bloem in schede, c = vrouwelijke bloem, d = zaad'
	
		),
	
		'Najas minor' => array(
	
			'Najas minor.jpg' => 'overzicht',
	
			'Najas minor 2.jpg' => 'vergroot; a = blad, b = manlijke bloem in schede, c = vrouwelijke bloem, d = zaad'
	
		),
	
		'Sagittaria sagittifolia' => array(
	
			'Sagittaria sagittifolia.jpg' => 'overzicht',
	
			'Sagittaria sagittifolia 2.jpg' => 'vergroot; a = dsn. bloem, b = vruchthoofdje',
	
			'Sagittaria sagittifolia 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Sagittaria latifolia' => array(
	
			'Sagittaria latifolia.jpg' => 'overzicht',
	
			'Sagittaria latifolia 2.jpg' => 'foto, bloeiend'
	
		),
	
		'Baldellia ranunculoides' => array(
	
			'Baldellia ranuncul ran.jpg' => 'overzicht',
	
			'Baldellia ranuncul ran 2.jpg' => 'vergroot; a = bloem, b = vruchtje',
	
			'Baldellia ranuncul repens 2.jpg' => 'vergroot; a = bloem, b = vruchtje'
	
		),
	
		'Baldellia ranunculoides subsp. ranunculoides' => array(
	
			'Baldellia ranuncul ran.jpg' => 'overzicht',
	
			'Baldellia ranuncul ran 2.jpg' => 'vergroot; a = bloem, b = vruchtje',
	
			'Baldellia ranuncul ran 3.jpg' => 'foto',
	
			'Baldellia ranuncul ran 4.jpg' => 'foto'
	
		),
	
		'Baldellia ranunculoides subsp. repens' => array(
	
			'Baldellia ranuncul repens.jpg' => 'overzicht',
	
			'Baldellia ranuncul repens 2.jpg' => 'vergroot; a = bloem, b = vruchtje',
	
			'Baldellia ranuncul repens 3.jpg' => 'foto'
	
		),
	
		'Luronium natans' => array(
	
			'Luronium natans.jpg' => 'overzicht',
	
			'Luronium natans 2.jpg' => 'vergroot',
	
			'Luronium natans 3.jpg' => 'foto, habitus',
	
			'Luronium natans 4.jpg' => 'foto, habitus',
	
			'Luronium natans 5.jpg' => 'foto, bloem'
	
		),
	
		'Alisma gramineum' => array(
	
			'Alisma gramineum.jpg' => 'overzicht foto',
	
			'Alisma gramineum 2.jpg' => 'vergroot; a = vruchtbeginsel, b+c = vruchtje',
	
			'Alisma gramineum 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Alisma plantago-aquatica' => array(
	
			'Alisma plantago-aquatica.jpg' => 'overzicht',
	
			'Alisma plantago-aquatica 2.jpg' => 'vergroot; a = vruchtbeginsel, b + c = vruchtje',
	
			'Alisma plantago-aquatica 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Alisma lanceolatum' => array(
	
			'Alisma lanceolatum.jpg' => 'overzicht foto',
	
			'Alisma lanceolatum 2.jpg' => 'vergroot; a = vruchtbeginsel, b-d = vruchtje',
	
			'Alisma lanceolatum 3.jpg' => 'foto, habitus bloeiend',
	
			'Alisma lanceolatum 4.jpg' => 'foto, bloeiwijze',
	
			'Alisma lanceolatum 5.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Scheuchzeria palustris' => array(
	
			'Scheuchzeria palustris.jpg' => 'overzicht',
	
			'Scheuchzeria palustris 2.jpg' => 'vergroot',
	
			'Scheuchzeria palustris 3.jpg' => 'foto, habitus bloeiend',
	
			'Scheuchzeria palustris 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Triglochin maritima' => array(
	
			'Triglochin maritima.jpg' => 'overzicht',
	
			'Triglochin maritima 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Triglochin maritima 3.jpg' => 'foto, in vrucht',
	
			'Triglochin maritima 4.jpg' => 'foto, in bloei (links) en in vrucht (rechts)',
	
			'Triglochin maritima 5.jpg' => 'foto, bloeiende habitus'
	
		),
	
		'Triglochin palustris' => array(
	
			'Triglochin palustris.jpg' => 'overzicht',
	
			'Triglochin palustris 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Triglochin palustris 3.jpg' => 'foto, habitus bloeiend',
	
			'Triglochin palustris 4.jpg' => 'foto, in bloei en in vruchten (om en om van links naar rechts)',
	
			'Triglochin palustris 5.jpg' => 'foto, bloeiende habitus'
	
		),
	
		'Ruppia maritima' => array(
	
			'Ruppia maritima.jpg' => 'overzicht',
	
			'Ruppia maritima 2.jpg' => 'vergroot; a = bloeiwijze, b = deelvruchtje, c = bladtop',
	
			'Ruppia maritima 3.jpg' => 'foto, habitus'
	
		),
	
		'Ruppia cirrhosa' => array(
	
			'Ruppia cirrhosa.jpg' => 'overzicht',
	
			'Ruppia cirrhosa 2.jpg' => 'vergroot; a = bladtop, b = deelvruchtje'
	
		),
	
		'Zannichellia palustris' => array(
	
			'Zannichellia palustris.jpg' => 'overzicht',
	
			'Zannichellia palustris 2.jpg' => 'vergroot; a = bloem,  deelvruchtjes, b = subsp. palustris, c = subsp. pedicellata, d = subsp. major'
	
		),
	
		'Zannichellia palustris subsp. major' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Zannichellia palustris subsp. palustris' => array(
	
			'Zannichellia palustris pal.jpg' => 'overzicht',
	
			'Zannichellia palustris pal 3.jpg' => 'foto, habitus',
	
			'Zannichellia palustris pal 4.jpg' => 'foto, in bloei'
	
		),
	
		'Zannichellia palustris subsp. pedicellata' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Groenlandia densa' => array(
	
			'Groenlandia densa.jpg' => 'overzicht',
	
			'Groenlandia densa 2.jpg' => 'vergroot; a = bloeiwijze, b+c = bladen, d = bladrand, e = dsn. stengel, f = vrucht',
	
			'Groenlandia densa 3.jpg' => 'foto',
	
			'Groenlandia densa 4.jpg' => 'foto'
	
		),
	
		'Potamogeton crispus' => array(
	
			'Potamogeton crispus.jpg' => 'overzicht',
	
			'Potamogeton crispus 2.jpg' => 'vergroot; a = bladvoet, b = bladmidden, c = bladtop, d = vrucht',
	
			'Potamogeton crispus 3.jpg' => 'foto',
	
			'Potamogeton lintonii(x) 2.jpg' => '(= Potamogeton mucronatus x crispus) vergroot; a = bladvoet, b = bladmidden, c = bladtop'
	
		),
	
		'Potamogeton pectinatus' => array(
	
			'Potamogeton pectinatus.jpg' => 'overzicht',
	
			'Potamogeton pectinatus 2.jpg' => 'vergroot; a = bladtop, b = dsn. blad, c = dsn. steunblad, d = vrucht'
	
		),
	
		'Potamogeton filiformis' => array(
	
			'Potamogeton filiformis.jpg' => 'overzicht',
	
			'Potamogeton filiformis 2.jpg' => 'foto, habitus'
	
		),
	
		'Potamogeton acutifolius' => array(
	
			'Potamogeton acutifolius.jpg' => 'overzicht',
	
			'Potamogeton acutifolius 2.jpg' => 'vergroot; a = bladmidden, b = bladtop, c = dsn. steunblad, d = winterknop, e = vrucht'
	
		),
	
		'Potamogeton compressus' => array(
	
			'Potamogeton compressus.jpg' => 'overzicht',
	
			'Potamogeton compressus 2a.jpg' => 'vergroot; a+b = habitus',
	
			'Potamogeton compressus 2b.jpg' => 'vergroot; c = bladmidden, d = bladtop, e = dsn. blad, f = dsn. stengel + steunblad, g = winterknop, h = vrucht'
	
		),
	
		'Potamogeton obtusifolius' => array(
	
			'Potamogeton obtusifolius.jpg' => 'overzicht',
	
			'Potamogeton obtusifolius 2.jpg' => 'vergroot; a = bladmidden, b = bladtop, c = dsn. blad, d = dsn. steunblad, e = winterknop, f = vrucht'
	
		),
	
		'Potamogeton mucronatus' => array(
	
			'Potamogeton mucronatus.jpg' => 'overzicht',
	
			'Potamogeton mucronatus 2.jpg' => 'vergroot; a = bladmidden, b = bladtop, c = winterknop, d = vrucht',
	
			'Potamogeton lintonii(x) 2.jpg' => '(= Potamogeton mucronatus x crispus) vergroot; a = bladvoet, b = bladmidden, c = bladtop'
	
		),
	
		'Potamogeton trichoides' => array(
	
			'Potamogeton trichoides.jpg' => 'overzicht',
	
			'Potamogeton trichoides 2.jpg' => 'vergroot; a = bladmidden, b = bladtop, c = dsn. blad, d = dsn. stengel + steunblad, e = winterknop, f = vrucht'
	
		),
	
		'Potamogeton berchtoldii' => array(
	
			'Potamogeton berchtoldii.jpg' => 'overzicht',
	
			'Potamogeton berchtoldii 2.jpg' => 'vergroot; a = bladmidden, b = bladtop, c = dsn. steunblad, d = winterknop, e = vrucht'
	
		),
	
		'Potamogeton pusillus' => array(
	
			'Potamogeton pusillus.jpg' => 'overzicht',
	
			'Potamogeton pusillus 2.jpg' => 'vergroot; a = bladmidden, b = bladtop, c = dsn. steunblad, d = winterknop, e = vrucht',
	
			'Potamogeton pusillus 3.jpg' => 'foto'
	
		),
	
		'Potamogeton natans' => array(
	
			'Potamogeton natans.jpg' => 'overzicht',
	
			'Potamogeton natans 2.jpg' => 'vergroot; a = dsn. onderwaterblad, b = dsn. stengel + steunblad, c = vrucht',
	
			'Potamogeton natans 3.jpg' => 'foto',
	
			'Potamogeton fluitans(x) 2.jpg' => '(= Potamogeton lucens x natans) vergroot; a-c = onderwaterblad, d = vrucht',
	
			'Potamogeton sparganifolius(x) 2.jpg' => '(= Potamogeton gramineus x natans) vergroot; a = bladvoet, b = blad nabij bladvoet, c = bladmidden, d = bladtop, e = dsn. stengel + steunblad'
	
		),
	
		'Potamogeton perfoliatus' => array(
	
			'Potamogeton perfoliatus.jpg' => 'overzicht',
	
			'Potamogeton perfoliatus 2.jpg' => 'vergroot; a = niet-bloeiend stengelstuk, b = bladvoet, c = bladmidden, d = bladtop, e = bladrand, f = vrucht',
	
			'Potamogeton perfoliatus 3.jpg' => 'foto',
	
			'Potamogeton decipiens(x) 2.jpg' => '(= Potamogeton lucens x perfoliatus) vergroot; a = bladvoet, b = bladmidden, c = bladtop, d = bladrand, e = dsn. stengel + steunblad'
	
		),
	
		'Potamogeton praelongus' => array(
	
			'Potamogeton praelongus.jpg' => 'overzicht',
	
			'Potamogeton praelongus 2.jpg' => 'vergroot; a = bladtop, b = bladmidden, c+d = bladtop, e = vrucht',
	
			'Potamogeton praelongus 3.jpg' => 'foto'
	
		),
	
		'Potamogeton coloratus' => array(
	
			'Potamogeton coloratus.jpg' => 'overzicht',
	
			'Potamogeton coloratus 2.jpg' => 'vergroot; a = bladmidden, b = dsn. stengel + steunblad, c = vrucht',
	
			'Potamogeton coloratus 3.jpg' => 'foto; linksboven samen met Potamogeton gramineus',
	
			'Potamogeton coloratus 4.jpg' => 'foto'
	
		),
	
		'Potamogeton polygonifolius' => array(
	
			'Potamogeton polygonifolius.jpg' => 'overzicht',
	
			'Potamogeton polygonifolius 2.jpg' => 'vergroot; a = habitus watervorm, b = habitus landvorm, c = bladvoet, d = bladmidden, e = bladtop, f = vrucht',
	
			'Potamogeton polygonifolius 3.jpg' => 'foto'
	
		),
	
		'Potamogeton nodosus' => array(
	
			'Potamogeton nodosus.jpg' => 'overzicht',
	
			'Potamogeton nodosus 2.jpg' => 'vergroot; a = onderwaterblad, b = bladvoet, c = bladmidden, d = bladtop, e = bladrand, f = dsn. stengel + steunblad, g = vrucht',
	
			'Potamogeton nodosus 3.jpg' => 'foto',
	
			'Potamogeton nodosus 4.jpg' => 'foto'
	
		),
	
		'Potamogeton alpinus' => array(
	
			'Potamogeton alpinus.jpg' => 'overzicht',
	
			'Potamogeton alpinus 2.jpg' => 'vergroot; a = habitus, b-d= onderwaterblad, e = vrucht',
	
			'Potamogeton alpinus 3.jpg' => 'foto'
	
		),
	
		'Potamogeton gramineus' => array(
	
			'Potamogeton gramineus.jpg' => 'overzicht',
	
			'Potamogeton gramineus 2.jpg' => 'vergroot; a-c = onderwaterblad, d = onderwaterblad + steunblad, e = bladrand, f = dsn. stengel + steunblad, g = vrucht',
	
			'Potamogeton angustifolius(x).jpg' => '(= Potamogeton gramineus x lucens) vergroot; a = habitus, b = bladvoet, c = bladmidden, d = bladtop, e = bladrand, f = dsn. stengel + steunblad, g = vrucht',
	
			'Potamogeton gramineus 3.jpg' => 'foto; gemengd met Potamongeton coloratus: de bruine bladen',
	
			'Potamogeton gramineus 4.jpg' => 'foto; gemengd met Potamongeton coloratus: de bruine bladen',
	
			'Potamogeton sparganifolius(x) 2.jpg' => '(= Potamogeton gramineus x natans) vergroot; a = bladvoet, b = blad nabij bladvoet, c = bladmidden, d = bladtop, e = dsn. stengel + steunblad'
	
		),
	
		'Potamogeton lucens' => array(
	
			'Potamogeton lucens.jpg' => 'overzicht',
	
			'Potamogeton lucens 2.jpg' => 'vergroot; a = blad met steunblad, b = bladrand, c = dsn. stengel + steunblad, d = vrucht',
	
			'Potamogeton lucens 3.jpg' => 'foto',
	
			'Potamogeton fluitans(x) 2.jpg' => '(= Potamogeton lucens x natans) vergroot; a-c = onderwaterblad, d = vrucht',
	
			'Potamogeton angustifolius(x).jpg' => '(= Potamogeton gramineus x lucens) vergroot; a = habitus, b = bladvoet, c = bladmidden, d = bladtop, e = bladrand, f = dsn. stengel + steunblad, g = vrucht',
	
			'Potamogeton decipiens(x) 2.jpg' => '(= Potamogeton lucens x perfoliatus) vergroot; a = bladvoet, b = bladmidden, c = bladtop, d = bladrand, e = dsn. stengel + steunblad'
	
		),
	
		'Zostera marina' => array(
	
			'Zostera marina.jpg' => 'overzicht',
	
			'Zostera marina 2.jpg' => 'vergroot; a = bloeiwijze, b = idem, vergroot',
	
			'Zostera marina 3.jpg' => 'foto, habitus'
	
		),
	
		'Zostera noltei' => array(
	
			'Zostera noltei.jpg' => 'overzicht',
	
			'Zostera noltei 2.jpg' => 'vergroot; a = bladtop, b = meeldraad, c + d = bloeiwijze',
	
			'Zostera noltei 3.jpg' => 'foto, habitus'
	
		),
	
		'Narthecium ossifragum' => array(
	
			'Narthecium ossifragum.jpg' => 'overzicht',
	
			'Narthecium ossifragum 2.jpg' => 'vergroot; a = bloem zonder bloemkroon',
	
			'Narthecium ossifragum 3.jpg' => 'foto',
	
			'Narthecium ossifragum 4.jpg' => 'foto',
	
			'Narthecium ossifragum 5.jpg' => 'foto'
	
		),
	
		'Paris quadrifolia' => array(
	
			'Paris quadrifolia.jpg' => 'overzicht',
	
			'Paris quadrifolia 2.jpg' => 'vergroot; a = dsn. bloem, b = stijl + stempels, c = vruchtkelk',
	
			'Paris quadrifolia 3.jpg' => 'foto',
	
			'Paris quadrifolia 4.jpg' => 'foto',
	
			'Paris quadrifolia 5.jpg' => 'foto, vrucht'
	
		),
	
		'Tulipa sylvestris' => array(
	
			'Tulipa sylvestris.jpg' => 'overzicht',
	
			'Tulipa sylvestris 2.jpg' => 'vergroot; a = dsn. bloem, b = meeldraad, c = stamper, d = dsn. vruchtbeginsel',
	
			'Tulipa sylvestris 3.jpg' => 'foto, bloem',
	
			'Tulipa sylvestris 4.jpg' => 'foto, habtius bloeiend',
	
			'Tulipa sylvestris 5.jpg' => 'foto, habtius bloeiend'
	
		),
	
		'Tulipa -hybriden' => array(
	
			'Tulipa-hybriden.jpg' => 'overzicht',
	
			'Tulipa-hybriden 2.jpg' => 'foto, bloeiend'
	
		),
	
		'Fritillaria meleagris' => array(
	
			'Fritillaria meleagris.jpg' => 'overzicht',
	
			'Fritillaria meleagris 2.jpg' => 'vergroot',
	
			'Fritillaria meleagris 3.jpg' => 'foto'
	
		),
	
		'Fritillaria imperialis' => array(
	
			'Fritillaria imperialis.jpg' => 'overzicht foto',
	
			'Fritillaria imperialis 3.jpg' => 'foto',
	
			'Fritillaria imperialis 4.jpg' => 'foto, in bloei, geel',
	
			'Fritillaria imperialis 5.jpg' => 'foto, in bloei, geel',
	
			'Fritillaria imperialis 6.jpg' => 'foto, in bloei, oranje',
	
			'Fritillaria imperialis 7.jpg' => 'foto, in vrucht'
	
		),
	
		'Lilium bulbiferum subsp. croceum' => array(
	
			'Lilium bulbiferum.jpg' => 'overzicht foto',
	
			'Lilium bulbiferum 3.jpg' => 'foto',
	
			'Lilium bulbiferum 4.jpg' => 'foto'
	
		),
	
		'Lilium martagon' => array(
	
			'Lilium martagon.jpg' => 'overzicht',
	
			'Lilium martagon 2.jpg' => 'vergroot; a = stamper, b = vrucht, c = dsn. vrucht',
	
			'Lilium martagon 3.jpg' => 'foto'
	
		),
	
		'Gagea minima' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Gagea villosa' => array(
	
			'Gagea villosa.jpg' => 'overzicht',
	
			'Gagea villosa 2.jpg' => 'vergroot',
	
			'Gagea villosa 3.jpg' => 'foto',
	
			'Gagea villosa 4.jpg' => 'foto'
	
		),
	
		'Gagea spathacea' => array(
	
			'Gagea spathacea.jpg' => 'overzicht',
	
			'Gagea spathacea 2.jpg' => 'vergroot; a = knolletjes',
	
			'Gagea spathacea 3.jpg' => 'foto'
	
		),
	
		'Gagea pratensis' => array(
	
			'Gagea pratensis.jpg' => 'overzicht',
	
			'Gagea pratensis 2.jpg' => 'vergroot',
	
			'Gagea pratensis 3.jpg' => 'foto',
	
			'Gagea pratensis 4.jpg' => 'foto'
	
		),
	
		'Gagea lutea' => array(
	
			'Gagea lutea.jpg' => 'overzicht',
	
			'Gagea lutea 2.jpg' => 'vergroot; a = dsn. bloem, b = stamper, c = dsn. vruchtbeginsel',
	
			'Gagea lutea 3.jpg' => 'foto',
	
			'Gagea lutea 4.jpg' => 'foto'
	
		),
	
		'Colchicum autumnale' => array(
	
			'Colchicum autumnale.jpg' => 'overzicht',
	
			'Colchicum autumnale 2.jpg' => 'vergroot; a = plant in knop, b = bloeiende plant met dsn. knol, c = plant in vrucht, d = rijpe vrucht',
	
			'Colchicum autumnale 3.jpg' => 'foto',
	
			'Colchicum autumnale 4.jpg' => 'foto',
	
			'Colchicum autumnale 5.jpg' => 'foto'
	
		),
	
		'Colchicum byzantinum' => array(
	
			'Colchicum byzantinum.jpg' => 'overzicht foto',
	
			'Colchicum byzantinum 3.jpg' => 'foto',
	
			'Colchicum byzantinum 4.jpg' => 'foto'
	
		),
	
		'Hammarbya paludosa' => array(
	
			'Hammarbya paludosa.jpg' => 'overzicht; a = lip',
	
			'Hammarbya paludosa 2.jpg' => 'vergroot',
	
			'Hammarbya paludosa 3.jpg' => 'foto',
	
			'Hammarbya paludosa 4.jpg' => 'foto',
	
			'Hammarbya paludosa 5.jpg' => 'foto'
	
		),
	
		'Liparis loeselii' => array(
	
			'Liparis loeselii.jpg' => 'overzicht; a = bloem',
	
			'Liparis loeselii 2.jpg' => 'vergroot',
	
			'Liparis loeselii 3.jpg' => 'foto, habitus, in bloei',
	
			'Liparis loeselii 4.jpg' => 'foto, bloeiwijze',
	
			'Liparis loeselii 5.jpg' => 'foto, bloemen',
	
			'Liparis loeselii 6.jpg' => 'foto, habitus, in bloei',
	
			'Liparis loeselii 7.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Corallorrhiza trifida' => array(
	
			'Corallorrhiza trifida.jpg' => 'overzicht foto',
	
			'Corallorrhiza trifida 2.jpg' => 'detail; bloem',
	
			'Corallorrhiza trifida 3.jpg' => 'foto',
	
			'Corallorrhiza trifida 4.jpg' => 'foto'
	
		),
	
		'Epipactis palustris' => array(
	
			'Epipactis palustris.jpg' => 'overzicht foto',
	
			'Epipactis palustris 2.jpg' => 'detail; lip',
	
			'Epipactis palustris 3.jpg' => 'foto',
	
			'Epipactis palustris 4.jpg' => 'foto',
	
			'Epipactis palustris 5.jpg' => 'foto'
	
		),
	
		'Epipactis atrorubens' => array(
	
			'Epipactis atrorubens.jpg' => 'overzicht foto',
	
			'Epipactis atrorubens 2.jpg' => 'detail; lip',
	
			'Epipactis atrorubens 3.jpg' => 'foto',
	
			'Epipactis atrorubens 4.jpg' => 'foto',
	
			'Epipactis atrorubens 5.jpg' => 'foto'
	
		),
	
		'Epipactis muelleri' => array(
	
			'Epipactis muelleri.jpg' => 'overzicht foto',
	
			'Epipactis muelleri 3.jpg' => 'foto',
	
			'Epipactis muelleri 4.jpg' => 'foto',
	
			'Epipactis muelleri 5.jpg' => 'foto'
	
		),
	
		'Epipactis helleborine' => array(
	
			'Epipactis helleborine.jpg' => 'overzicht; a = lip',
	
			'Epipactis helleborine 2.jpg' => 'vergroot',
	
			'Epipactis helleborine 3.jpg' => 'foto',
	
			'Epipactis helleborine 4.jpg' => 'foto',
	
			'Epipactis helleborine 5.jpg' => 'foto'
	
		),
	
		'Epipactis helleborine subsp. helleborine' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Epipactis helleborine subsp. neerlandica' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Neottia nidus-avis' => array(
	
			'Neottia nidus-avis.jpg' => 'overzicht foto',
	
			'Neottia nidus-avis 2.jpg' => 'details; a = wortels, b = bloem',
	
			'Neottia nidus-avis 3.jpg' => 'foto',
	
			'Neottia nidus-avis 4.jpg' => 'foto',
	
			'Neottia nidus-avis 5.jpg' => 'foto'
	
		),
	
		'Neottia ovata' => array(
	
			'Neottia ovata.jpg' => 'overzicht',
	
			'Neottia ovata 2.jpg' => 'vergroot; a = bloem',
	
			'Neottia ovata 3.jpg' => 'foto',
	
			'Neottia ovata 4.jpg' => 'foto',
	
			'Neottia ovata 5.jpg' => 'foto'
	
		),
	
		'Neottia cordata' => array(
	
			'Neottia cordata.jpg' => 'overzicht foto',
	
			'Neottia cordata 2.jpg' => 'detail; bloem',
	
			'Neottia cordata 3.jpg' => 'foto',
	
			'Neottia cordata 4.jpg' => 'foto',
	
			'Neottia cordata 5.jpg' => 'foto'
	
		),
	
		'Cephalanthera rubra' => array(
	
			'Cephalanthera rubra.jpg' => 'overzicht; a = lip',
	
			'Cephalanthera rubra 2.jpg' => 'vergroot',
	
			'Cephalanthera rubra 3.jpg' => 'foto, bloemen',
	
			'Cephalanthera rubra 4.jpg' => 'foto, bloeiwijze',
	
			'Cephalanthera rubra 5.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Cephalanthera damasonium' => array(
	
			'Cephalanthera damasonium.jpg' => 'overzicht foto',
	
			'Cephalanthera damasonium 2.jpg' => 'detail; lip',
	
			'Cephalanthera damasonium 3.jpg' => 'foto',
	
			'Cephalanthera damasonium 4.jpg' => 'foto'
	
		),
	
		'Cephalanthera longifolia' => array(
	
			'Cephalanthera longifolia.jpg' => 'overzicht foto',
	
			'Cephalanthera longifolia 2.jpg' => 'detail; lip',
	
			'Cephalanthera longifolia 3.jpg' => 'foto',
	
			'Cephalanthera longifolia 4.jpg' => 'foto',
	
			'Cephalanthera longifolia 5.jpg' => 'foto'
	
		),
	
		'Spiranthes spiralis' => array(
	
			'Spiranthes spiralis.jpg' => 'overzicht; a = bloem',
	
			'Spiranthes spiralis 2.jpg' => 'vergroot',
	
			'Spiranthes spiralis 3.jpg' => 'foto, habitus bloeiend',
	
			'Spiranthes spiralis 4.jpg' => 'foto, deel bloeiwijzen',
	
			'Spiranthes spiralis 5.jpg' => 'foto, bloemen',
	
			'Spiranthes spiralis 6.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Spiranthes aestivalis' => array(
	
			'Spiranthes aestivalis.jpg' => 'overzicht foto',
	
			'Spiranthes aestivalis 2.jpg' => 'tekening',
	
			'Spiranthes aestivalis 3.jpg' => 'foto, bloeiwijzen',
	
			'Spiranthes aestivalis 4.jpg' => 'foto, bloemen'
	
		),
	
		'Goodyera repens' => array(
	
			'Goodyera repens.jpg' => 'overzicht; a = bloem',
	
			'Goodyera repens 2.jpg' => 'vergroot',
	
			'Goodyera repens 3.jpg' => 'foto',
	
			'Goodyera repens 4.jpg' => 'foto',
	
			'Goodyera repens 5.jpg' => 'foto'
	
		),
	
		'Herminium monorchis' => array(
	
			'Herminium monorchis.jpg' => 'overzicht; a = bloem, b = lip',
	
			'Herminium monorchis 2.jpg' => 'vergroot',
	
			'Herminium monorchis 3.jpg' => 'foto',
	
			'Herminium monorchis 4.jpg' => 'foto',
	
			'Herminium monorchis 5.jpg' => 'foto'
	
		),
	
		'Dactylorhiza viridis' => array(
	
			'Dactylorhiza viridis.jpg' => 'overzicht foto',
	
			'Dactylorhiza viridis 2.jpg' => 'detail; bloem',
	
			'Dactylorhiza viridis 3.jpg' => 'foto',
	
			'Dactylorhiza viridis 4.jpg' => 'foto'
	
		),
	
		'Dactylorhiza maculata' => array(
	
			'Dactylorhiza maculata.jpg' => 'overzicht; a = fuchsii-type, b = maculata-type',
	
			'Dactylorhiza maculata 2.jpg' => 'vergroot'
	
		),
	
		'Dactylorhiza maculata subsp. fuchsii' => array(
	
			'Dactylorhiza maculata fuch.jpg' => 'overzicht',
	
			'Dactylorhiza maculata fuch 2.jpg' => 'tekening, bloem',
	
			'Dactylorhiza maculata fuch 3.jpg' => 'foto',
	
			'Dactylorhiza maculata fuch 4.jpg' => 'foto',
	
			'Dactylorhiza maculata fuch 5.jpg' => 'foto'
	
		),
	
		'Dactylorhiza maculata subsp. maculata' => array(
	
			'Dactylorhiza maculata mac.jpg' => 'overzicht foto',
	
			'Dactylorhiza maculata mac 2.jpg' => 'foto',
	
			'Dactylorhiza maculata mac 3.jpg' => 'foto',
	
			'Dactylorhiza maculata mac 4.jpg' => 'foto'
	
		),
	
		'Dactylorhiza incarnata' => array(
	
			'Dactylorhiza incarnata.jpg' => 'overzicht foto',
	
			'Dactylorhiza incarnata 2.jpg' => 'detail; bloem',
	
			'Dactylorhiza incarnata 3.jpg' => 'foto',
	
			'Dactylorhiza incarnata 4.jpg' => 'foto',
	
			'Dactylorhiza incarnata 5.jpg' => 'foto'
	
		),
	
		'Dactylorhiza majalis' => array(
	
			'Dactylorhiza majalis majalis.jpg' => 'overzicht',
	
			'Dactylorhiza majalis maja 2.jpg' => 'vergroot; a = bloem, b = lengte doorsnede bloem',
	
			'Dactylorhiza maj praeterm 2.jpg' => 'vergroot; tekening, subsp. praetermissa',
	
			'Dactylorhiza maj sphagnic 3.jpg' => 'foto, subsp. sphagnicola'
	
		),
	
		'Dactylorhiza majalis subsp. majalis' => array(
	
			'Dactylorhiza majalis majalis.jpg' => 'overzicht',
	
			'Dactylorhiza majalis maja 2.jpg' => 'vergroot; a = bloem, b = lengte doorsnede bloem',
	
			'Dactylorhiza majalis maj 2.jpg' => 'tekening, habitus en bloem',
	
			'Dactylorhiza majalis maj 3.jpg' => 'foto',
	
			'Dactylorhiza majalis maj 4.jpg' => 'foto',
	
			'Dactylorhiza majalis maj 5.jpg' => 'foto'
	
		),
	
		'Dactylorhiza majalis subsp. praetermissa' => array(
	
			'Dactylorhiza maj praeterm.jpg' => 'overzicht foto',
	
			'Dactylorhiza maj praeterm 2.jpg' => 'vergroot; tekening',
	
			'Dactylorhiza maj praeterm 3.jpg' => 'foto',
	
			'Dactylorhiza maj praeterm 4.jpg' => 'foto',
	
			'Dactylorhiza maj praet jun 3.jpg' => 'foto',
	
			'Dactylorhiza maj praet jun 4.jpg' => 'foto',
	
			'Dactylorhiza maj praet jun 5.jpg' => 'foto',
	
			'Dactylorhiza maj praeterm 5.jpg' => 'foto'
	
		),
	
		'Dactylorhiza majalis subsp. sphagnicola' => array(
	
			'Dactylorhiza maj sphagnic.jpg' => 'overzicht',
	
			'Dactylorhiza maj sphagnic 3.jpg' => 'foto',
	
			'Dactylorhiza maj sphagnic 4.jpg' => 'foto',
	
			'Dactylorhiza maj sphagnic 5.jpg' => 'foto'
	
		),
	
		'Gymnadenia conopsea' => array(
	
			'Gymnadenia conopsea.jpg' => 'overzicht foto',
	
			'Gymnadenia conopsea 2.jpg' => 'detail; bloem',
	
			'Gymnadenia conopsea 3.jpg' => 'foto',
	
			'Gymnadenia conopsea 4.jpg' => 'foto'
	
		),
	
		'Gymnadenia conopsea subsp. conopsea' => array(
	
			'Gymnadenia conopsea conopsea.jpg' => 'overzicht',
	
			'Gymnadenia conopsea conops 3.jpg' => 'foto'
	
		),
	
		'Gymnadenia conopsea subsp. densiflora' => array(
	
			'Gymnadenia conopsea densifl.jpg' => 'overzicht',
	
			'Gymnadenia conopsea dens 3.jpg' => 'foto'
	
		),
	
		'Platanthera bifolia' => array(
	
			'Platanthera bifolia.jpg' => 'overzicht',
	
			'Platanthera bifolia 2.jpg' => 'vergroot; a = bloem, b = positie pollini‘n',
	
			'Platanthera bifolia 3.jpg' => 'foto',
	
			'Platanthera bifolia 4.jpg' => 'foto'
	
		),
	
		'Platanthera montana' => array(
	
			'Platanthera chlorantha.jpg' => 'overzicht',
	
			'Platanthera chlorantha 2.jpg' => 'detail; positie pollini‘n',
	
			'Platanthera chlorantha 3.jpg' => 'foto',
	
			'Platanthera chlorantha 4.jpg' => 'foto',
	
			'Platanthera chlorantha 5.jpg' => 'foto'
	
		),
	
		'Pseudorchis albida' => array(
	
			'Pseudorchis albida.jpg' => 'overzicht; a = bloem van voren en opzij',
	
			'Pseudorchis albida 2.jpg' => 'vergroot',
	
			'Pseudorchis albida 3.jpg' => 'foto',
	
			'Pseudorchis albida 4.jpg' => 'foto',
	
			'Pseudorchis albida 5.jpg' => 'foto',
	
			'Pseudorchis albida 6.jpg' => 'foto'
	
		),
	
		'Orchis anthropophora' => array(
	
			'Orchis anthropophora.jpg' => 'overzicht',
	
			'Orchis anthropophora 2.jpg' => 'vergroot',
	
			'Orchis anthropophora 3.jpg' => 'foto, bloeiwijze',
	
			'Orchis anthropophora 4.jpg' => 'foto, deel bloeiwijze',
	
			'Orchis anthropophora 5.jpg' => 'foto, bloemen'
	
		),
	
		'Orchis mascula' => array(
	
			'Orchis mascula.jpg' => 'overzicht foto',
	
			'Orchis mascula 2.jpg' => 'detail; bloem',
	
			'Orchis mascula 3.jpg' => 'foto',
	
			'Orchis mascula 4.jpg' => 'foto'
	
		),
	
		'Orchis simia' => array(
	
			'Orchis simia.jpg' => 'overzicht foto',
	
			'Orchis simia 2.jpg' => 'detail; bloem',
	
			'Orchis simia 3.jpg' => 'foto',
	
			'Orchis simia 4.jpg' => 'foto',
	
			'Orchis simia 5.jpg' => 'foto'
	
		),
	
		'Orchis purpurea' => array(
	
			'Orchis purpurea.jpg' => 'overzicht',
	
			'Orchis purpurea 2.jpg' => 'vergroot; a = bloem',
	
			'Orchis purpurea 3.jpg' => 'foto',
	
			'Orchis purpurea 4.jpg' => 'foto',
	
			'Orchis purpurea 5.jpg' => 'foto'
	
		),
	
		'Orchis militaris' => array(
	
			'Orchis militaris.jpg' => 'overzicht foto',
	
			'Orchis militaris 2.jpg' => 'detail; bloem',
	
			'Orchis militaris 3.jpg' => 'foto',
	
			'Orchis militaris 4.jpg' => 'foto',
	
			'Orchis militaris 5.jpg' => 'foto'
	
		),
	
		'Neotinea ustulata' => array(
	
			'Neotinea ustulata.jpg' => 'overzicht foto',
	
			'Neotinea ustulata 3.jpg' => 'foto, habitus in bloei',
	
			'Neotinea ustulata 4.jpg' => 'foto, bloeiwijzen',
	
			'Neotinea ustulata 5.jpg' => 'foto, bloemen',
	
			'Neotinea ustulata 6.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Himantoglossum hircinum' => array(
	
			'Himantoglossum hircinum.jpg' => 'overzicht',
	
			'Himantoglossum hircinum 2.jpg' => 'vergroot; a = bloem',
	
			'Himantoglossum hircinum 3.jpg' => 'foto',
	
			'Himantoglossum hircinum 4.jpg' => 'foto',
	
			'Himantoglossum hircinum 5.jpg' => 'foto'
	
		),
	
		'Anacamptis pyramidalis' => array(
	
			'Anacamptis pyramidalis.jpg' => 'overzicht foto',
	
			'Anacamptis pyramidalis 2.jpg' => 'detail; bloem',
	
			'Anacamptis pyramidalis 3.jpg' => 'foto, bloeiwijze',
	
			'Anacamptis pyramidalis 4.jpg' => 'foto, detail bloemen',
	
			'Anacamptis pyramidalis 5.jpg' => 'foto, bloeiwijze',
	
			'Anacamptis pyramidalis 6.jpg' => 'foto, detail bloemen'
	
		),
	
		'Anacamptis morio' => array(
	
			'Anacamptis morio.jpg' => 'overzicht foto',
	
			'Anacamptis morio 2.jpg' => 'detail; bloem',
	
			'Anacamptis morio 3.jpg' => 'foto, habitus bloeiend',
	
			'Anacamptis morio 4.jpg' => 'foto, bloeiwijze',
	
			'Anacamptis morio 5.jpg' => 'foto, detail bloeiwijze',
	
			'Anacamptis morio 6.jpg' => 'foto, habitus bloeiend',
	
			'Anacamptis morio 7.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Anacamptis coriophora' => array(
	
			'Anacamptis coriophora.jpg' => 'overzicht foto',
	
			'Anacamptis coriophora 3.jpg' => 'foto, habitus bloeiend',
	
			'Anacamptis coriophora 4.jpg' => 'foto, detail bloeiwijze'
	
		),
	
		'Ophrys insectifera' => array(
	
			'Ophrys insectifera.jpg' => 'overzicht foto',
	
			'Ophrys insectifera 2.jpg' => 'detail; bloem',
	
			'Ophrys insectifera 3.jpg' => 'foto',
	
			'Ophrys insectifera 4.jpg' => 'foto'
	
		),
	
		'Ophrys apifera' => array(
	
			'Ophrys apifera.jpg' => 'overzicht',
	
			'Ophrys apifera 2.jpg' => 'vergroot; a = bloem',
	
			'Ophrys apifera 3.jpg' => 'foto',
	
			'Ophrys apifera 4.jpg' => 'foto',
	
			'Ophrys apifera 5.jpg' => 'foto'
	
		),
	
		'Iris pseudacorus' => array(
	
			'Iris pseudacorus.jpg' => 'overzicht',
	
			'Iris pseudacorus 2.jpg' => 'vergroot; a = stamper, b = meeldraad, c = vrucht',
	
			'Iris pseudacorus 3.jpg' => 'foto, habitus bloeiend',
	
			'Iris pseudacorus 4.jpg' => 'foto, bloeiend',
	
			'Iris pseudacorus 5.jpg' => 'foto, in vrucht'
	
		),
	
		'Iris foetidissima' => array(
	
			'Iris foetidissima.jpg' => 'overzicht',
	
			'Iris foetidissima 2.jpg' => 'foto, bloem',
	
			'Iris foetidissima 3.jpg' => 'foto, bloem',
	
			'Iris foetidissima 4.jpg' => 'foto, bloem',
	
			'Iris foetidissima 5.jpg' => 'foto, vrucht en zaden',
	
			'Iris foetidissima 6.jpg' => 'foto, vrucht en zaden'
	
		),
	
		'Crocus stellaris(x)' => array(
	
			'Crocus stellaris(x).jpg' => 'overzicht',
	
			'Crocus stellaris(x) 2.jpg' => 'vergroot',
	
			'Crocus stellaris(x) 3.jpg' => 'foto'
	
		),
	
		'Crocus chrysanthus' => array(
	
			'Crocus chrysanthus.jpg' => 'overzicht',
	
			'Crocus chrysanthus 3.jpg' => 'bloeiend'
	
		),
	
		'Crocus vernus' => array(
	
			'Crocus vernus.jpg' => 'overzicht',
	
			'Crocus vernus 2.jpg' => 'vergroot; a = stamper + meeldraden, b = zaad',
	
			'Crocus vernus 3.jpg' => 'foto'
	
		),
	
		'Crocus tommasinianus' => array(
	
			'Crocus tommasinianus.jpg' => 'overzicht',
	
			'Crocus tommasinianus 2.jpg' => 'vergroot',
	
			'Crocus tommasinianus 3.jpg' => 'foto, habitus bloeiend',
	
			'Crocus tommasinianus 4.jpg' => 'foto, habitus bloeiend',
	
			'Crocus tommasinianus 5.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Sisyrinchium bermudiana' => array(
	
			'Sisyrinchium bermudiana.jpg' => 'overzicht foto',
	
			'Sisyrinchium bermudiana 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Anthericum liliago' => array(
	
			'Anthericum liliago.jpg' => 'overzicht',
	
			'Anthericum liliago 2.jpg' => 'vergroot; a = vrucht',
	
			'Anthericum liliago 3.jpg' => 'foto, bloeiwijze',
	
			'Anthericum liliago 4.jpg' => 'foto, bloemen'
	
		),
	
		'Asparagus officinalis' => array(
	
			'Asparagus off officinalis.jpg' => 'overzicht',
	
			'Asparagus off officinalis 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem'
	
		),
	
		'Asparagus officinalis subsp. prostratus' => array(
	
			'Asparagus off prostratus.jpg' => 'overzicht foto',
	
			'Asparagus off prostratus 3.jpg' => 'foto',
	
			'Asparagus off prostratus 4.jpg' => 'foto'
	
		),
	
		'Asparagus officinalis subsp. officinalis' => array(
	
			'Asparagus off officinalis.jpg' => 'overzicht',
	
			'Asparagus off officinalis 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem',
	
			'Asparagus off officinalis 3.jpg' => 'foto'
	
		),
	
		'Convallaria majalis' => array(
	
			'Convallaria majalis.jpg' => 'overzicht',
	
			'Convallaria majalis 2.jpg' => 'vergroot; a = dsn. bloem, b = vrucht',
	
			'Convallaria majalis 3.jpg' => 'foto'
	
		),
	
		'Polygonatum verticillatum' => array(
	
			'Polygonatum verticillatum.jpg' => 'overzicht',
	
			'Polygonatum verticillatum 2.jpg' => 'vergroot; a = vrucht + dsn.',
	
			'Polygonatum verticillatum 3.jpg' => 'foto',
	
			'Polygonatum verticillatum 4.jpg' => 'foto'
	
		),
	
		'Polygonatum odoratum' => array(
	
			'Polygonatum odoratum.jpg' => 'overzicht',
	
			'Polygonatum odoratum 2.jpg' => 'vergroot; a = bloem, b = meeldraad',
	
			'Polygonatum odoratum 3.jpg' => 'foto'
	
		),
	
		'Polygonatum multiflorum' => array(
	
			'Polygonatum multiflorum.jpg' => 'overzicht',
	
			'Polygonatum multiflorum 2.jpg' => 'vergroot; a = dsn. bloem, b = meeldraad, c = dsn. vrucht',
	
			'Polygonatum multiflorum 3.jpg' => 'foto',
	
			'Polygonatum multiflorum 4.jpg' => 'foto'
	
		),
	
		'Polygonatum hybridum(x)' => array(
	
			'Polygonatum hybridum(x).jpg' => 'overzicht; a = bloem, b = meeldraad',
	
			'Polygonatum hybridum(x) 2.jpg' => 'vergroot'
	
		),
	
		'Maianthemum bifolium' => array(
	
			'Maianthemum bifolium.jpg' => 'overzicht; a = bloem',
	
			'Maianthemum bifolium 2.jpg' => 'vergroot',
	
			'Maianthemum bifolium 3.jpg' => 'foto',
	
			'Maianthemum bifolium 4.jpg' => 'foto'
	
		),
	
		'Ornithogalum umbellatum' => array(
	
			'Ornithogalum umbellatum.jpg' => 'overzicht',
	
			'Ornithogalum umbellatum 2.jpg' => 'vergroot; a = bloem zonder kroon, b = dsn. vruchtbeginsel',
	
			'Ornithogalum umbellatum 3.jpg' => 'foto',
	
			'Ornithogalum umbellatum 4.jpg' => 'foto'
	
		),
	
		'Ornithogalum umbellatum subsp. campestre' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Ornithogalum umbellatum subsp. umbellatum' => array(
	
			'Ornithogalum umbellatum.jpg' => 'overzicht',
	
			'Ornithogalum umbellatum 2.jpg' => 'vergroot; a = bloem zonder kroon, b = dsn. vruchtbeginsel'
	
		),
	
		'Ornithogalum nutans' => array(
	
			'Ornithogalum nutans.jpg' => 'overzicht',
	
			'Ornithogalum nutans 2.jpg' => 'vergroot; a = dsn. bloem, b = buitenste + binnenste meeldraad, c = vruchtkelk',
	
			'Ornithogalum nutans 3.jpg' => 'foto',
	
			'Ornithogalum nutans 4.jpg' => 'foto'
	
		),
	
		'Ornithogalum pyramidale' => array(
	
			'Ornithogalum pyramidale.jpg' => 'overzicht foto',
	
			'Ornithogalum pyramidale 3.jpg' => 'foto'
	
		),
	
		'Scilla bifolia' => array(
	
			'Scilla bifolia.jpg' => 'overzicht',
	
			'Scilla bifolia 2.jpg' => 'vergroot; a = dsn. bloem, b = stamper, c = vrucht',
	
			'Scilla bifolia 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Scilla siberica' => array(
	
			'Scilla siberica.jpg' => 'overzicht foto',
	
			'Scilla siberica 3.jpg' => 'foto, bloeiend',
	
			'Scilla siberica 4.jpg' => 'foto, bloeiend',
	
			'Scilla siberica 5.jpg' => 'foto, bloeiend'
	
		),
	
		'Hyacinthoides non-scripta' => array(
	
			'Hyacinthoides non-scripta.jpg' => 'overzicht',
	
			'Hyacinthoides non-scripta 2.jpg' => 'vergroot; a = vruchtbeginsel',
	
			'Hyacinthoides non-scripta 3.jpg' => 'foto',
	
			'Hyacinthoides x massartiana 3.jpg' => '(= Hyacinthoides non-scripta x Hyacinthoides hispanica) foto',
	
			'Hyacinthoides x massartiana 4.jpg' => '(= Hyacinthoides non-scripta x Hyacinthoides hispanica) foto'
	
		),
	
		'Hyacinthus orientalis' => array(
	
			'Hyacinthus orientalis.jpg' => 'overzicht foto',
	
			'Hyacinthus orientalis 3.jpg' => 'foto'
	
		),
	
		'Chionodoxa siehei' => array(
	
			'Chionodoxa siehei.jpg' => 'overzicht foto',
	
			'Chionodoxa siehei 3.jpg' => 'foto, bloeiwijze',
	
			'Chionodoxa siehei 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Chionodoxa sardensis' => array(
	
			'Chionodoxa sardensis.jpg' => 'overzicht foto',
	
			'Chionodoxa sardensis 3.jpg' => 'foto, habitus bloeiend',
	
			'Chionodoxa sardensis 4.jpg' => 'foto, bloemen'
	
		),
	
		'Muscari comosum' => array(
	
			'Muscari comosum.jpg' => 'overzicht',
	
			'Muscari comosum 2.jpg' => 'vergroot',
	
			'Muscari comosum 3.jpg' => 'foto',
	
			'Muscari comosum 4.jpg' => 'foto',
	
			'Muscari comosum 5.jpg' => 'foto'
	
		),
	
		'Muscari botryoides' => array(
	
			'Muscari botryoides.jpg' => 'overzicht',
	
			'Muscari botryoides 2.jpg' => 'vergroot',
	
			'Muscari botryoides 3.jpg' => 'foto',
	
			'Muscari botryoides 4.jpg' => 'foto'
	
		),
	
		'Muscari armeniacum' => array(
	
			'Muscari armeniacum.jpg' => 'overzicht foto',
	
			'Muscari armeniacum 3.jpg' => 'foto',
	
			'Muscari armeniacum 4.jpg' => 'foto'
	
		),
	
		'Leucojum vernum' => array(
	
			'Leucojum vernum.jpg' => 'overzicht',
	
			'Leucojum vernum 2.jpg' => 'vergroot; a = dsn. bloem, b = bloem zonder kroonbladen',
	
			'Leucojum vernum 3.jpg' => 'foto',
	
			'Leucojum vernum 4.jpg' => 'foto'
	
		),
	
		'Leucojum aestivum' => array(
	
			'Leucojum aestivum.jpg' => 'overzicht',
	
			'Leucojum aestivum 2.jpg' => 'vergroot',
	
			'Leucojum aestivum 3.jpg' => 'foto',
	
			'Leucojum aestivum 4.jpg' => 'foto'
	
		),
	
		'Galanthus nivalis' => array(
	
			'Galanthus nivalis.jpg' => 'overzicht',
	
			'Galanthus nivalis 2.jpg' => 'vergroot; a = dsn. bloem',
	
			'Galanthus nivalis 3.jpg' => 'foto',
	
			'Galanthus nivalis 4.jpg' => 'foto',
	
			'Galanthus nivalis 5.jpg' => 'foto',
	
			'Galanthus elwesii 1.jpg' => 'foto, Galanthus elwesii, zie opmerking'
	
		),
	
		'Narcissus poeticus' => array(
	
			'Narcissus poeticus.jpg' => 'overzicht; a = dsn. bloem',
	
			'Narcissus poeticus 2.jpg' => 'vergroot',
	
			'Narcissus poeticus 3.jpg' => 'foto',
	
			'Narcissus poeticus 4.jpg' => 'foto'
	
		),
	
		'Narcissus pseudonarcissus' => array(
	
			'Narcissus pseudonarcissus.jpg' => 'overzicht foto',
	
			'Narcissus pseudonarcissus 3.jpg' => 'foto',
	
			'Narcissus pseudonarcissus 4.jpg' => 'foto',
	
			'Narcissus pseudonarcissus 5.jpg' => 'foto',
	
			'Narcissus pseudonarcissus 6.jpg' => 'foto, habitus, in bloei',
	
			'Narcissus pseudonar major 3.jpg' => 'foto'
	
		),
	
		'Allium cepa' => array(
	
			'Allium cepa.jpg' => 'overzicht',
	
			'Allium cepa 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Allium cepa 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Allium schoenoprasum' => array(
	
			'Allium schoenoprasum.jpg' => 'overzicht',
	
			'Allium schoenoprasum 2.jpg' => 'vergroot',
	
			'Allium schoenoprasum 3.jpg' => 'foto, bloeiend',
	
			'Allium schoenoprasum 4.jpg' => 'foto, bloeiend',
	
			'Allium schoenoprasum 5.jpg' => 'foto, bloeiend'
	
		),
	
		'Allium vineale' => array(
	
			'Allium vineale.jpg' => 'overzicht',
	
			'Allium vineale 2.jpg' => 'vergroot; a = meeldraden',
	
			'Allium vineale 3.jpg' => 'foto, bloeiwijzen',
	
			'Allium vineale 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Allium ursinum' => array(
	
			'Allium ursinum.jpg' => 'overzicht',
	
			'Allium ursinum 2.jpg' => 'vergroot',
	
			'Allium ursinum 3.jpg' => 'foto, habitus bloeiend',
	
			'Allium ursinum 4.jpg' => 'foto, detail bloeiwijze'
	
		),
	
		'Allium paradoxum' => array(
	
			'Allium paradoxum.jpg' => 'overzicht foto',
	
			'Allium paradoxum 3.jpg' => 'foto, habitus bloeiend',
	
			'Allium paradoxum 4.jpg' => 'foto, detail',
	
			'Allium paradoxum 5.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Allium triquetrum' => array(
	
			'Allium triquetrum.jpg' => 'overzicht foto',
	
			'Allium triquetrum 3.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Allium porrum' => array(
	
			'Allium porrum.jpg' => 'overzicht',
	
			'Allium porrum 2.jpg' => 'vergroot; a = geopende bloem',
	
			'Allium porrum 3.jpg' => 'foto, bloeiwijzen in schutblad'
	
		),
	
		'Allium zebdanense' => array(
	
			'Allium zebdanense.jpg' => 'overzicht foto',
	
			'Allium zebdanense 3.jpg' => 'foto, bloeiwijze',
	
			'Allium zebdanense 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Allium scorodoprasum' => array(
	
			'Allium scorodoprasum.jpg' => 'overzicht',
	
			'Allium scorodoprasum 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem, c = binnenste meeldraad, d = buitenste meeldraad',
	
			'Allium scorodoprasum 3.jpg' => 'foto, habitus bloeiend',
	
			'Allium scorodoprasum 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Allium oleraceum' => array(
	
			'Allium oleraceum.jpg' => 'overzicht',
	
			'Allium oleraceum 2.jpg' => 'vergroot',
	
			'Allium oleraceum 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Allium carinatum' => array(
	
			'Allium carinatum.jpg' => 'overzicht',
	
			'Allium carinatum 2.jpg' => 'vergroot; a = vrucht',
	
			'Allium carinatum 3.jpg' => 'foto, bloeiwijze',
	
			'Allium carinatum 4.jpg' => 'foto, bloemen'
	
		),
	
		'Sparganium erectum' => array(
	
			'Sparganium erectum.jpg' => 'overzicht',
	
			'Sparganium erectum 2.jpg' => 'vergroot; a = manlijke bloem, b = vruchthoofdje, c = vrouwelijke bloem',
	
			'Sparganium erectum erectum.jpg' => 'detail; nootje',
	
			'Sparganium erectum neglectum.jpg' => 'detail; nootje',
	
			'Sparganium erectum sl 3.jpg' => 'foto, bloeiend',
	
			'Sparganium erectum sl 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Sparganium natans' => array(
	
			'Sparganium natans.jpg' => 'overzicht',
	
			'Sparganium natans 2.jpg' => 'vergroot',
	
			'Sparganium natans 3.jpg' => 'foto, bloeiend',
	
			'Sparganium natans 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Sparganium emersum' => array(
	
			'Sparganium emersum.jpg' => 'overzicht',
	
			'Sparganium emersum 2.jpg' => 'vergroot; a = vrucht',
	
			'Sparganium emersum 3.jpg' => 'foto, bloeiwijze',
	
			'Sparganium emersum 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Sparganium angustifolium' => array(
	
			'Sparganium angustifolium.jpg' => 'overzicht',
	
			'Sparganium angustifolium 2.jpg' => 'vergroot'
	
		),
	
		'Typha minima' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Typha latifolia' => array(
	
			'Typha latifolia.jpg' => 'overzicht',
	
			'Typha latifolia 2.jpg' => 'vergroot; a = dsn. manlijke bloeiwijze, b = manlijke bloem, c = vouwelijke bloem',
	
			'Typha latifolia 3.jpg' => 'foto, bloeiwijzen, mannelijk boven, vrouwelijk onder',
	
			'Typha latifolia 4.jpg' => 'foto, bloeiwijze, mannelijk boven, vrouwelijk onder'
	
		),
	
		'Typha angustifolia' => array(
	
			'Typha angustifolia.jpg' => 'overzicht',
	
			'Typha angustifolia 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem',
	
			'Typha angustifolia 3.jpg' => 'foto, bloeiwijzen, mannelijk boven, vrouwelijk onder'
	
		),
	
		'Juncus bulbosus' => array(
	
			'Juncus bulbosus.jpg' => 'overzicht; a = vruchtkelk',
	
			'Juncus bulbosus 2.jpg' => 'vergroot',
	
			'Juncus bulbosus 3.jpg' => 'foto'
	
		),
	
		'Juncus pygmaeus' => array(
	
			'Juncus pygmaeus.jpg' => 'overzicht; a = vruchtkelk',
	
			'Juncus pygmaeus 2.jpg' => 'vergroot',
	
			'Juncus pygmaeus 3.jpg' => 'foto'
	
		),
	
		'Juncus capitatus' => array(
	
			'Juncus capitatus.jpg' => 'overzicht; a = vruchtkelk',
	
			'Juncus capitatus 2.jpg' => 'vergroot',
	
			'Juncus capitatus 3.jpg' => 'foto',
	
			'Juncus capitatus 4.jpg' => 'foto'
	
		),
	
		'Juncus tenageia' => array(
	
			'Juncus tenageia.jpg' => 'overzicht; a = vruchtkelk',
	
			'Juncus tenageia 2.jpg' => 'vergroot',
	
			'Juncus tenageia 3.jpg' => 'foto',
	
			'Juncus tenageia 4.jpg' => 'foto'
	
		),
	
		'Juncus foliosus' => array(
	
			'Juncus foliosus.jpg' => 'overzicht',
	
			'Juncus foliosus 2.jpg' => 'vergroot, vruchtkelk'
	
		),
	
		'Juncus ambiguus' => array(
	
			'Juncus ambiguus.jpg' => 'overzicht; a = vruchtkelk',
	
			'Juncus ambiguus 3.jpg' => 'foto',
	
			'Juncus ambiguus 4.jpg' => 'foto'
	
		),
	
		'Juncus bufonius' => array(
	
			'Juncus bufonius.jpg' => 'overzicht; a = vruchtkelk',
	
			'Juncus bufonius 2.jpg' => 'vergroot',
	
			'Juncus bufonius 3.jpg' => 'foto'
	
		),
	
		'Juncus subnodulosus' => array(
	
			'Juncus subnodulosus.jpg' => 'overzicht; a = vruchtkelk',
	
			'Juncus subnodulosus 2.jpg' => 'vergroot',
	
			'Juncus subnodulosus 3.jpg' => 'foto',
	
			'Juncus subnodulosus 4.jpg' => 'foto'
	
		),
	
		'Juncus canadensis' => array(
	
			'Juncus canadensis.jpg' => 'overzicht; vruchtkelk'
	
		),
	
		'Juncus ensifolius' => array(
	
			'Juncus ensifolius.jpg' => 'overzicht; vruchtkelk',
	
			'Juncus ensifolius 3.jpg' => 'foto',
	
			'Juncus ensifolius 4.jpg' => 'foto'
	
		),
	
		'Juncus acutiflorus' => array(
	
			'Juncus acutiflorus.jpg' => 'overzicht; a = vruchtkelk',
	
			'Juncus acutiflorus 2.jpg' => 'vergroot',
	
			'Juncus acutiflorus 3.jpg' => 'foto'
	
		),
	
		'Juncus articulatus' => array(
	
			'Juncus articulatus.jpg' => 'overzicht; a = vruchtkelk',
	
			'Juncus articulatus 2.jpg' => 'vergroot',
	
			'Juncus articulatus 3.jpg' => 'foto'
	
		),
	
		'Juncus alpinoarticulatus' => array(
	
			'Juncus alpinoarticulatus.jpg' => 'overzicht; a = subsp. atricapillus, b = subsp. alpinoarticulatus, c = vruchtkelk',
	
			'Juncus alpinoarticulatus 2.jpg' => 'vergroot',
	
			'Juncus alpinoarticulatus 3.jpg' => 'foto',
	
			'Juncus alpinoarticulatus 4.jpg' => 'foto',
	
			'Juncus alpinoarticulatus 5.jpg' => 'foto',
	
			'Juncus alpinoarticulatus 6.jpg' => 'foto'
	
		),
	
		'Juncus maritimus' => array(
	
			'Juncus maritimus.jpg' => 'overzicht; a = vruchtkelk',
	
			'Juncus maritimus 2.jpg' => 'vergroot',
	
			'Juncus maritimus 3.jpg' => 'foto'
	
		),
	
		'Juncus filiformis' => array(
	
			'Juncus filiformis.jpg' => 'overzicht; a = vruchtkelk',
	
			'Juncus filiformis 2.jpg' => 'vergroot',
	
			'Juncus filiformis 3.jpg' => 'foto'
	
		),
	
		'Juncus balticus' => array(
	
			'Juncus balticus.jpg' => 'overzicht; a = vruchtkelk',
	
			'Juncus balticus 2.jpg' => 'vergroot',
	
			'Juncus balticus 3.jpg' => 'foto',
	
			'Juncus balticus 4.jpg' => 'foto'
	
		),
	
		'Juncus inflexus' => array(
	
			'Juncus inflexus.jpg' => 'overzicht; a = vruchtkelk',
	
			'Juncus inflexus 2.jpg' => 'vergroot',
	
			'Juncus inflexus 3.jpg' => 'foto'
	
		),
	
		'Juncus effusus' => array(
	
			'Juncus effusus.jpg' => 'overzicht; a = vruchtkelk',
	
			'Juncus effusus 2.jpg' => 'vergroot'
	
		),
	
		'Juncus conglomeratus' => array(
	
			'Juncus conglomeratus.jpg' => 'overzicht; a = vruchtkelk',
	
			'Juncus conglomeratus 2.jpg' => 'vergroot',
	
			'Juncus conglomeratus 3.jpg' => 'foto'
	
		),
	
		'Juncus squarrosus' => array(
	
			'Juncus squarrosus.jpg' => 'overzicht; a = vruchtkelk',
	
			'Juncus squarrosus 2.jpg' => 'vergroot',
	
			'Juncus squarrosus 3.jpg' => 'foto'
	
		),
	
		'Juncus tenuis' => array(
	
			'Juncus tenuis.jpg' => 'overzicht; a = vruchtkelk',
	
			'Juncus tenuis 2.jpg' => 'vergroot',
	
			'Juncus tenuis 3.jpg' => 'foto'
	
		),
	
		'Juncus compressus' => array(
	
			'Juncus compressus.jpg' => 'overzicht; a+b = bloem, c = vruchtkelk',
	
			'Juncus compressus 2.jpg' => 'vergroot',
	
			'Juncus compressus 3.jpg' => 'foto'
	
		),
	
		'Juncus gerardii' => array(
	
			'Juncus gerardii.jpg' => 'overzicht; a+b = bloem, c = vruchtkelk',
	
			'Juncus gerardii 2.jpg' => 'vergroot',
	
			'Juncus gerardii 3.jpg' => 'foto'
	
		),
	
		'Luzula pilosa' => array(
	
			'Luzula pilosa.jpg' => 'overzicht foto',
	
			'Luzula pilosa 3.jpg' => 'foto',
	
			'Luzula pilosa 4.jpg' => 'foto'
	
		),
	
		'Luzula campestris' => array(
	
			'Luzula campestris.jpg' => 'overzicht; a = vruchtkelk, b= zaad',
	
			'Luzula campestris 2.jpg' => 'vergroot',
	
			'Luzula campestris 3.jpg' => 'foto',
	
			'Luzula campestris 4.jpg' => 'foto'
	
		),
	
		'Luzula multiflora' => array(
	
			'Luzula multiflora.jpg' => 'overzicht',
	
			'Luzula multiflora 2.jpg' => 'vergroot; a = subsp. congesta, b = subsp. multiflora, c = vruchtkelk, d = zaad',
	
			'Luzula multiflora 3.jpg' => 'foto'
	
		),
	
		'Luzula multiflora subsp. congesta' => array(
	
			'Luzula multiflora congesta.jpg' => 'overzicht',
	
			'Luzula multiflora congesta 2.jpg' => 'vergroot'
	
		),
	
		'Luzula multiflora subsp. multiflora' => array(
	
			'Luzula multiflora multiflora.jpg' => 'overzicht',
	
			'Luzula multiflora multifl 2.jpg' => 'vergroot',
	
			'Luzula multiflora multifl 3.jpg' => 'foto'
	
		),
	
		'Luzula sylvatica' => array(
	
			'Luzula sylvatica.jpg' => 'overzicht foto',
	
			'Luzula sylvatica 3.jpg' => 'foto'
	
		),
	
		'Luzula luzuloides' => array(
	
			'Luzula luzuloides.jpg' => 'overzicht foto',
	
			'Luzula luzuloides 3.jpg' => 'foto'
	
		),
	
		'Eriophorum vaginatum' => array(
	
			'Eriophorum vaginatum.jpg' => 'overzicht',
	
			'Eriophorum vaginatum 2.jpg' => 'vergroot; a = habitus in vruchttijd, b = idem in bloei, c = top bovenste stengelblad, d = bloem, e = nootjes, f = vrucht',
	
			'Eriophorum vaginatum 3.jpg' => 'foto, habitus bloeiend',
	
			'Eriophorum vaginatum 4.jpg' => 'foto, bloeiwijze',
	
			'Eriophorum vaginatum 5.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Eriophorum angustifolium' => array(
	
			'Eriophorum angustifolium.jpg' => 'overzicht',
	
			'Eriophorum angustifolium 2.jpg' => 'vergroot; a = habitus in bloei, b = idem in vruchttijd, c = bloeiende aar, d = bloem, e = vrucht',
	
			'Eriophorum angustifolium 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Eriophorum latifolium' => array(
	
			'Eriophorum latifolium.jpg' => 'overzicht',
	
			'Eriophorum latifolium 2.jpg' => 'vergroot; a = habitus in bloei, b = idem in vruchttijd, c = bloeiende aar, d = nootje, e = bloem, f = vrucht, g = dsn. nootje',
	
			'Eriophorum latifolium 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Eriophorum gracile' => array(
	
			'Eriophorum gracile.jpg' => 'overzicht',
	
			'Eriophorum gracile 2.jpg' => 'vergroot; a = habitus in bloei, b = idem in vrucht, c = dsn. blad, d = bloeiende aar, e = vrucht, f = nootjes, g = bloem',
	
			'Eriophorum gracile 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Trichophorum cespitosum subsp. germanicum' => array(
	
			'Trichophorum cespitosum germ.jpg' => 'overzicht',
	
			'Trichophorum cespitosum ge 2.jpg' => 'vergroot; a = aar, b = bloem, c = vrucht',
	
			'Trichophorum cespitosum ge 3.jpg' => 'detail bovenste bladschede; a = subsp. cespitosum, b = subsp. germanicum',
	
			'Trichophorum cespitosum ge 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Eleocharis acicularis' => array(
	
			'Eleocharis acicularis.jpg' => 'overzicht',
	
			'Eleocharis acicularis 2.jpg' => 'tekening habitus en nootje',
	
			'Eleocharis acicularis 3.jpg' => 'foto',
	
			'Eleocharis acicularis 4.jpg' => 'tekening kafje en nootje'
	
		),
	
		'Eleocharis quinqueflora' => array(
	
			'Eleocharis quinqueflora.jpg' => 'overzicht',
	
			'Eleocharis quinqueflora 2.jpg' => 'tekening habitus, aartje, bloem, en nootje',
	
			'Eleocharis quinqueflora 3.jpg' => 'foto',
	
			'Eleocharis quinqueflora 4.jpg' => 'tekening kafje en nootje'
	
		),
	
		'Eleocharis ovata' => array(
	
			'Eleocharis ovata.jpg' => 'overzicht',
	
			'Eleocharis ovata 2.jpg' => 'tekening habitus, aartje, bloem, nootje, bovenste bladschede'
	
		),
	
		'Eleocharis multicaulis' => array(
	
			'Eleocharis multicaulis.jpg' => 'overzicht',
	
			'Eleocharis multicaulis 2.jpg' => 'tekening habitus, aartje, bloem en nootje',
	
			'Eleocharis multicaulis 3.jpg' => 'foto',
	
			'Eleocharis multicaulis 4.jpg' => 'tekening habitus, kafje, nootje, bovenste bladschede'
	
		),
	
		'Eleocharis uniglumis' => array(
	
			'Eleocharis uniglumis.jpg' => 'overzicht',
	
			'Eleocharis uniglumis 2.jpg' => 'tekening habitus, aartje, bloem, en nootje',
	
			'Eleocharis uniglumis 3.jpg' => 'foto',
	
			'Eleocharis uniglumis 4.jpg' => 'tekening habitus, aartje, bovenste bladschede, onderste kafje, nootje'
	
		),
	
		'Eleocharis palustris' => array(
	
			'Eleocharis palustris.jpg' => 'overzicht',
	
			'Eleocharis palustris 2.jpg' => 'tekening habitus en nootje',
	
			'Eleocharis palustris 3.jpg' => 'foto',
	
			'Eleocharis palustris 4.jpg' => 'tekening aartje, bovenste bladschede, postitie onderste 2 kafjes, nootje'
	
		),
	
		'Bolboschoenus maritimus' => array(
	
			'Bolboschoenus maritimus.jpg' => 'overzicht',
	
			'Bolboschoenus maritimus 2.jpg' => 'vergroot; a = bloem, b = kafjes, c = helmknop',
	
			'Bolboschoenus maritimus 3.jpg' => 'foto'
	
		),
	
		'Scirpus sylvaticus' => array(
	
			'Scirpus sylvaticus.jpg' => 'overzicht',
	
			'Scirpus sylvaticus 2.jpg' => 'vergroot; a = aar, b = bloem, c = vrucht, d = nootje',
	
			'Scirpus sylvaticus 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Scirpoides holoschoenus' => array(
	
			'Scirpoides holoschoenus.jpg' => 'overzicht',
	
			'Scirpoides holoschoenus 2.jpg' => 'vergroot; a = dsn. blad, b = bloem, c = vrucht',
	
			'Scirpoides holoschoenus 3.jpg' => 'foto, bloeiwijze',
	
			'Scirpoides holoschoenus 4.jpg' => 'foto, bloeiwijze',
	
			'Scirpoides holoschoenus 5.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Schoenoplectus lacustris' => array(
	
			'Schoenoplectus lacustris.jpg' => 'overzicht',
	
			'Schoenoplectus lacustris 2.jpg' => 'vergroot; a = aar, b = bloem, c = vrucht, d = kafjes',
	
			'Schoenoplectus lacustris 3.jpg' => 'foto, habitus, bloeiend',
	
			'Schoenoplectus lacustris 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Schoenoplectus tabernaemontani' => array(
	
			'Schoeno tabernaemontani.jpg' => 'overzicht',
	
			'Schoeno tabernaemontani 2.jpg' => 'vergroot; a = dsn. stengel, b = aar, c = bloem + borstel, d = kafje, e = vrucht',
	
			'Schoeno tabernaemontani 3.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Schoenoplectus mucronatus' => array(
	
			'Schoenoplectus mucronatus.jpg' => 'overzicht',
	
			'Schoenoplectus mucronatus 2.jpg' => 'vergroot'
	
		),
	
		'Schoenoplectus pungens' => array(
	
			'Schoenoplectus pungens.jpg' => 'overzicht',
	
			'Schoenoplectus pungens 2.jpg' => 'vergroot; a = bloem, b = kafje, c = dsn. stengel, d = aar',
	
			'Schoenoplectus pungens 3.jpg' => 'foto, habitus, bloeiend'
	
		),
	
		'Schoenoplectus triqueter' => array(
	
			'Schoenoplectus triqueter.jpg' => 'overzicht',
	
			'Schoenoplectus triqueter 2.jpg' => 'vergroot; a = dsn. stengel, b = aar, c = vrucht, d = bloem + borstel, e = bloem + kafje',
	
			'Schoenoplectus triqueter 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Isolepis setacea' => array(
	
			'Isolepis setacea.jpg' => 'overzicht',
	
			'Isolepis setacea 2.jpg' => 'vergroot; a = detail bloeiwijze, b = vrucht, c = kafjes, d = bloem',
	
			'Isolepis setacea 3.jpg' => 'foto'
	
		),
	
		'Eleogiton fluitans' => array(
	
			'Eleogiton fluitans.jpg' => 'overzicht',
	
			'Eleogiton fluitans 2.jpg' => 'vergroot; a = aar, b = helmknop, c = bloem, d = nootje + dsn.',
	
			'Eleogiton fluitans 3.jpg' => 'foto'
	
		),
	
		'Blysmus compressus' => array(
	
			'Blysmus compressus.jpg' => 'overzicht',
	
			'Blysmus compressus 2.jpg' => 'vergroot; a = bloem',
	
			'Blysmus compressus 3.jpg' => 'foto'
	
		),
	
		'Blysmus rufus' => array(
	
			'Blysmus rufus.jpg' => 'overzicht',
	
			'Blysmus rufus 2.jpg' => 'vergroot; a = bloem',
	
			'Blysmus rufus 3.jpg' => 'foto'
	
		),
	
		'Cyperus fuscus' => array(
	
			'Cyperus fuscus.jpg' => 'overzicht',
	
			'Cyperus fuscus 2.jpg' => 'vergroot; a = bloem, b = nootje, c = aar',
	
			'Cyperus fuscus 3.jpg' => 'foto'
	
		),
	
		'Cyperus flavescens' => array(
	
			'Cyperus flavescens.jpg' => 'overzicht',
	
			'Cyperus flavescens 2.jpg' => 'vergroot; a = aartje, b = kafje, c = bloem, d = nootje + dsn.'
	
		),
	
		'Cyperus longus' => array(
	
			'Cyperus longus.jpg' => 'overzicht foto',
	
			'Cyperus longus 3.jpg' => 'foto',
	
			'Cyperus longus 4.jpg' => 'foto'
	
		),
	
		'Cyperus esculentus' => array(
	
			'Cyperus esculentus.jpg' => 'overzicht',
	
			'Cyperus esculentus 2.jpg' => 'vergroot',
	
			'Cyperus esculentus 3.jpg' => 'foto',
	
			'Cyperus esculentus 4.jpg' => 'foto'
	
		),
	
		'Cyperus eragrostis' => array(
	
			'Cyperus eragrostis.jpg' => 'overzicht',
	
			'Cyperus eragrostis 3.jpg' => 'foto',
	
			'Cyperus eragrostis 4.jpg' => 'foto'
	
		),
	
		'Cladium mariscus' => array(
	
			'Cladium mariscus.jpg' => 'overzicht',
	
			'Cladium mariscus 2.jpg' => 'vergroot; a = bloemen, b = detail blad, c = aartje, d = nootje + dsn.',
	
			'Cladium mariscus 3.jpg' => 'foto'
	
		),
	
		'Rhynchospora alba' => array(
	
			'Rhynchospora alba.jpg' => 'overzicht',
	
			'Rhynchospora alba 2.jpg' => 'vergroot; a = aar, b = bloem',
	
			'Rhynchospora alba 3.jpg' => 'foto, habitus in bloei',
	
			'Rhynchospora alba 4.jpg' => 'foto, habitus in bloei'
	
		),
	
		'Rhynchospora fusca' => array(
	
			'Rhynchospora fusca.jpg' => 'overzicht',
	
			'Rhynchospora fusca 2.jpg' => 'vergroot; a = aar, b = bloem, c = stamper, d = dsn. nootje',
	
			'Rhynchospora fusca 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Schoenus nigricans' => array(
	
			'Schoenus nigricans.jpg' => 'overzicht',
	
			'Schoenus nigricans 2.jpg' => 'vergroot; a = aar, b = bloem, c = stamper, d = nootjes',
	
			'Schoenus nigricans 3.jpg' => 'foto, habitus, bloeiend'
	
		),
	
		'Carex dioica' => array(
	
			'Carex dioica.jpg' => 'overzicht',
	
			'Carex dioica 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem, c = urntje',
	
			'Carex dioica 3.jpg' => 'foto',
	
			'Carex dioica a.jpg' => 'habitus met details'
	
		),
	
		'Carex pulicaris' => array(
	
			'Carex pulicaris.jpg' => 'overzicht',
	
			'Carex pulicaris 2.jpg' => 'vergroot; a = urntje, b = vrouwelijke bloem, c = manlijke bloem',
	
			'Carex pulicaris 3.jpg' => 'foto',
	
			'Carex pulicaris a.jpg' => 'habitus met details'
	
		),
	
		'Carex hirta' => array(
	
			'Carex hirta.jpg' => 'overzicht',
	
			'Carex hirta 2.jpg' => 'vergroot; a = manlijke bloem, b = nootje, c = urntje',
	
			'Carex hirta 3.jpg' => 'foto',
	
			'Carex hirta a.jpg' => 'habitus met details'
	
		),
	
		'Carex pallescens' => array(
	
			'Carex pallescens.jpg' => 'overzicht',
	
			'Carex pallescens 2.jpg' => 'vergroot; a = nootje + dsn, b = vrouwelijke bloem, c = urntje, d = manlijke bloem',
	
			'Carex pallescens 3.jpg' => 'foto',
	
			'Carex pallescens a.jpg' => 'habitus met details'
	
		),
	
		'Carex digitata' => array(
	
			'Carex digitata.jpg' => 'overzicht',
	
			'Carex digitata 2.jpg' => 'vergroot; a = manlijke bloem, b = urntje',
	
			'Carex digitata 3.jpg' => 'foto',
	
			'Carex digitata 4.jpg' => 'foto',
	
			'Carex digitata 5.jpg' => 'foto',
	
			'Carex digitata a.jpg' => 'habitus met details'
	
		),
	
		'Carex lasiocarpa' => array(
	
			'Carex lasiocarpa.jpg' => 'overzicht',
	
			'Carex lasiocarpa 2.jpg' => 'vergroot; a = urntje, b = manlijke bloem, c = vrouwelijke bloem',
	
			'Carex lasiocarpa 3.jpg' => 'foto',
	
			'Carex lasiocarpa 4.jpg' => 'foto',
	
			'Carex lasiocarpa a.jpg' => 'habitus met details'
	
		),
	
		'Carex caryophyllea' => array(
	
			'Carex caryophyllea.jpg' => 'overzicht',
	
			'Carex caryophyllea 2.jpg' => 'vergroot; a = manlijke bloem, b + c = vrouwelijke bloem',
	
			'Carex caryophyllea 3.jpg' => 'foto',
	
			'Carex caryophyllea 4.jpg' => 'foto',
	
			'Carex caryophyllea a.jpg' => 'habitus met details'
	
		),
	
		'Carex ericetorum' => array(
	
			'Carex ericetorum.jpg' => 'overzicht',
	
			'Carex ericetorum 2.jpg' => 'vergroot; a = manlijke bloem, b = manlijke aar, c = nootje + dsn., d = vrouwelijke bloem',
	
			'Carex ericetorum 3.jpg' => 'foto',
	
			'Carex ericetorum a.jpg' => 'habitus met details'
	
		),
	
		'Carex pilulifera' => array(
	
			'Carex pilulifera.jpg' => 'overzicht',
	
			'Carex pilulifera 2.jpg' => 'vergroot; a = vrouwelijke bloem, b = urntje, c = manlijke bloem',
	
			'Carex pilulifera 3.jpg' => 'foto',
	
			'Carex pilulifera a.jpg' => 'habitus met details'
	
		),
	
		'Carex tomentosa' => array(
	
			'Carex tomentosa.jpg' => 'overzicht',
	
			'Carex tomentosa 2.jpg' => 'vergroot; a = nootje + dsn., b = urntje, c = manlijke bloem, d = vrouwelijke bloem',
	
			'Carex tomentosa 3.jpg' => 'foto'
	
		),
	
		'Carex hartmanii' => array(
	
			'Carex hartmanii.jpg' => 'overzicht foto',
	
			'Carex hartmanii 3.jpg' => 'foto',
	
			'Carex hartmanii 4.jpg' => 'foto'
	
		),
	
		'Carex buxbaumii' => array(
	
			'Carex buxbaumii.jpg' => 'overzicht',
	
			'Carex buxbaumii 2.jpg' => 'vergroot; a = urntje, b = vrouwelijke bloem, c = manlijke bloem',
	
			'Carex buxbaumii 3.jpg' => 'foto',
	
			'Carex buxbaumii a.jpg' => 'habitus met details'
	
		),
	
		'Carex pendula' => array(
	
			'Carex pendula.jpg' => 'overzicht',
	
			'Carex pendula 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem',
	
			'Carex pendula 3.jpg' => 'foto',
	
			'Carex pendula 4.jpg' => 'foto',
	
			'Carex pendula a.jpg' => 'habitus met details'
	
		),
	
		'Carex strigosa' => array(
	
			'Carex strigosa.jpg' => 'overzicht',
	
			'Carex strigosa 2.jpg' => 'vergroot; a = nootje, b = manlijke bloem, c = urntje',
	
			'Carex strigosa 3.jpg' => 'foto',
	
			'Carex strigosa a.jpg' => 'habitus met details'
	
		),
	
		'Carex limosa' => array(
	
			'Carex limosa.jpg' => 'overzicht',
	
			'Carex limosa 2.jpg' => 'vergroot; a = urntje, b = vrouwelijke bloem, c = manlijke bloem',
	
			'Carex limosa 3.jpg' => 'foto',
	
			'Carex limosa a.jpg' => 'habitus met details'
	
		),
	
		'Carex panicea' => array(
	
			'Carex panicea.jpg' => 'overzicht',
	
			'Carex panicea 2.jpg' => 'vergroot; a = urntje, b = nootje + dsn., c = manlijke bloem, d = vrouwelijke bloem',
	
			'Carex panicea 3.jpg' => 'foto',
	
			'Carex panicea a.jpg' => 'habitus met details'
	
		),
	
		'Carex flacca' => array(
	
			'Carex flacca.jpg' => 'overzicht',
	
			'Carex flacca 2.jpg' => 'vergroot; a = manlijke bloem, b = urntje',
	
			'Carex flacca 3.jpg' => 'foto',
	
			'Carex flacca a.jpg' => 'habitus met details'
	
		),
	
		'Carex rostrata' => array(
	
			'Carex rostrata.jpg' => 'overzicht',
	
			'Carex rostrata 2.jpg' => 'vergroot; a = manlijke bloem, b = dsn. nootje, c = urntje',
	
			'Carex rostrata 3.jpg' => 'foto',
	
			'Carex rostrata 4.jpg' => 'foto',
	
			'Carex rostrata a.jpg' => 'habitus met details'
	
		),
	
		'Carex vesicaria' => array(
	
			'Carex vesicaria.jpg' => 'overzicht',
	
			'Carex vesicaria 2.jpg' => 'vergroot; a = vrouwelijke bloem, b = manlijke bloem',
	
			'Carex vesicaria 3.jpg' => 'foto'
	
		),
	
		'Carex acutiformis' => array(
	
			'Carex acutiformis.jpg' => 'overzicht',
	
			'Carex acutiformis 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem, c = urntje, d = nootjes',
	
			'Carex acutiformis 3.jpg' => 'foto',
	
			'Carex acutiformis a.jpg' => 'habitus met details'
	
		),
	
		'Carex riparia' => array(
	
			'Carex riparia.jpg' => 'overzicht',
	
			'Carex riparia 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem, c = urntje',
	
			'Carex riparia 3.jpg' => 'foto',
	
			'Carex riparia 4.jpg' => 'foto',
	
			'Carex riparia a.jpg' => 'habitus met details'
	
		),
	
		'Carex pseudocyperus' => array(
	
			'Carex pseudocyperus.jpg' => 'overzicht',
	
			'Carex pseudocyperus 2.jpg' => 'vergroot; a = urntje, b = vrouwelijke bloem, c = manlijke bloem',
	
			'Carex pseudocyperus 3.jpg' => 'foto',
	
			'Carex pseudocyperus 4.jpg' => 'foto',
	
			'Carex pseudocyperus a.jpg' => 'habitus met details'
	
		),
	
		'Carex sylvatica' => array(
	
			'Carex sylvatica.jpg' => 'overzicht',
	
			'Carex sylvatica 2.jpg' => 'vergroot; a = manlijke bloem, b = urntje, c = nootje',
	
			'Carex sylvatica 3.jpg' => 'foto',
	
			'Carex sylvatica a.jpg' => 'habitus met details'
	
		),
	
		'Carex laevigata' => array(
	
			'Carex laevigata.jpg' => 'overzicht',
	
			'Carex laevigata 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem, c = urntje',
	
			'Carex laevigata 3.jpg' => 'foto',
	
			'Carex laevigata 4.jpg' => 'foto',
	
			'Carex laevigata a.jpg' => 'habitus met details'
	
		),
	
		'Carex extensa' => array(
	
			'Carex extensa.jpg' => 'overzicht',
	
			'Carex extensa 2.jpg' => 'vergroot; a = manlijke bloem, b = urntje, c = dsn. nootje',
	
			'Carex extensa 3.jpg' => 'foto',
	
			'Carex extensa a.jpg' => 'habitus met details',
	
			'Carex extensa 4.jpg' => 'foto'
	
		),
	
		'Carex distans' => array(
	
			'Carex distans.jpg' => 'overzicht',
	
			'Carex distans 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem',
	
			'Carex distans 3.jpg' => 'foto',
	
			'Carex distans 4.jpg' => 'foto',
	
			'Carex distans a.jpg' => 'habitus met details'
	
		),
	
		'Carex hostiana' => array(
	
			'Carex hostiana.jpg' => 'overzicht',
	
			'Carex hostiana 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem',
	
			'Carex hostiana 3.jpg' => 'foto',
	
			'Carex hostiana a.jpg' => 'habitus met details'
	
		),
	
		'Carex punctata' => array(
	
			'Carex punctata.jpg' => 'overzicht',
	
			'Carex punctata 2.jpg' => 'vergroot; a = urntje, b = nootje, c = manlijke bloem',
	
			'Carex punctata 3.jpg' => 'foto',
	
			'Carex punctata a.jpg' => 'habitus met details',
	
			'Carex punctata 4.jpg' => 'foto',
	
			'Carex punctata 5.jpg' => 'foto'
	
		),
	
		'Carex flava' => array(
	
			'Carex flava.jpg' => 'overzicht',
	
			'Carex flava 2.jpg' => 'vergroot; a =manlijke bloem, b = urntje, c = nootje',
	
			'Carex flava 3.jpg' => 'foto',
	
			'Carex flava 4.jpg' => 'foto',
	
			'Carex flava 5.jpg' => 'foto',
	
			'Carex flava a.jpg' => 'habitus met details'
	
		),
	
		'Carex lepidocarpa' => array(
	
			'Carex lepidocarpa.jpg' => 'overzicht',
	
			'Carex lepidocarpa 2.jpg' => 'vergroot; a = manlijke bloem, b = urntje',
	
			'Carex lepidocarpa 3.jpg' => 'foto',
	
			'Carex lepidocarpa a.jpg' => 'habitus met details',
	
			'Carex lepidocarpa 4.jpg' => 'foto',
	
			'Carex lepidocarpa 5.jpg' => 'foto'
	
		),
	
		'Carex oederi' => array(
	
			'Carex oederi oederi.jpg' => 'overzicht',
	
			'Carex oederi oederi a.jpg' => 'habitus met details',
	
			'Carex oederi oedocarpa 2.jpg' => 'vergroot; a = vrouwelijke bloem, b = urntje, c = nootje + dsn., d = manlijke bloem'
	
		),
	
		'Carex oederi subsp. oedocarpa' => array(
	
			'Carex oederi oedocarpa.jpg' => 'overzicht',
	
			'Carex oederi oedocarpa 2.jpg' => 'vergroot; a = vrouwelijke bloem, b = urntje, c = nootje + dsn., d = manlijke bloem',
	
			'Carex oederi oedocarpa 3.jpg' => 'foto',
	
			'Carex oederi oedocarpa a.jpg' => 'habitus met details'
	
		),
	
		'Carex oederi subsp. oederi' => array(
	
			'Carex oederi oederi.jpg' => 'overzicht',
	
			'Carex oederi oederi 3.jpg' => 'foto',
	
			'Carex oederi oederi a.jpg' => 'habitus met details'
	
		),
	
		'Carex elata' => array(
	
			'Carex elata.jpg' => 'overzicht',
	
			'Carex elata 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem, c = urntje',
	
			'Carex elata 3.jpg' => 'foto',
	
			'Carex elata 4.jpg' => 'foto',
	
			'Carex elata a.jpg' => 'habitus met details'
	
		),
	
		'Carex cespitosa' => array(
	
			'Carex cespitosa .jpg' => 'overzicht',
	
			'Carex cespitosa 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem',
	
			'Carex cespitosa 3.jpg' => 'foto'
	
		),
	
		'Carex trinervis' => array(
	
			'Carex trinervis.jpg' => 'overzicht',
	
			'Carex trinervis 2.jpg' => 'vergroot; a = urntje',
	
			'Carex trinervis 3.jpg' => 'foto',
	
			'Carex trinervis a.jpg' => 'habitus met details',
	
			'Carex trinervis 4.jpg' => 'foto'
	
		),
	
		'Carex aquatilis' => array(
	
			'Carex aquatilis.jpg' => 'overzicht',
	
			'Carex aquatilis 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem, c = urntje',
	
			'Carex aquatilis 3.jpg' => 'foto',
	
			'Carex aquatilis a.jpg' => 'habitus met details'
	
		),
	
		'Carex nigra' => array(
	
			'Carex nigra.jpg' => 'overzicht',
	
			'Carex nigra 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem, c = urntje',
	
			'Carex nigra a.jpg' => 'habitus met details'
	
		),
	
		'Carex acuta' => array(
	
			'Carex acuta.jpg' => 'overzicht',
	
			'Carex acuta 2.jpg' => 'vergroot; a = vrouwelijke bloem, b = manlijke bloem, c = urntje',
	
			'Carex acuta 3.jpg' => 'foto',
	
			'Carex acuta a.jpg' => 'habitus met details'
	
		),
	
		'Carex disticha' => array(
	
			'Carex disticha.jpg' => 'overzicht',
	
			'Carex disticha 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem, c = urntje',
	
			'Carex disticha 3.jpg' => 'foto',
	
			'Carex disticha 4.jpg' => 'foto',
	
			'Carex disticha a.jpg' => 'habitus met details'
	
		),
	
		'Carex divisa' => array(
	
			'Carex divisa.jpg' => 'overzicht',
	
			'Carex divisa 2.jpg' => 'vergroot; a = vrouwelijke bloem, b = urntje, c = nootje',
	
			'Carex divisa a.jpg' => 'habitus met details'
	
		),
	
		'Carex praecox' => array(
	
			'Carex praecox.jpg' => 'overzicht',
	
			'Carex praecox 2.jpg' => 'vergroot; a = manlijke bloem, b = urntje',
	
			'Carex praecox 3.jpg' => 'foto'
	
		),
	
		'Carex brizoides' => array(
	
			'Carex brizoides.jpg' => 'overzicht',
	
			'Carex brizoides 2.jpg' => 'vergroot; a = manlijke bloem, b = urntje'
	
		),
	
		'Carex arenaria' => array(
	
			'Carex arenaria.jpg' => 'overzicht',
	
			'Carex arenaria 2.jpg' => 'vergroot; a = manlijke bloem, b = urntje',
	
			'Carex arenaria 3.jpg' => 'foto',
	
			'Carex arenaria 4.jpg' => 'foto',
	
			'Carex arenaria a.jpg' => 'habitus met details',
	
			'Carex arenaria b.jpg' => 'bloeiwijze, urntje en nootje'
	
		),
	
		'Carex ligerica' => array(
	
			'Carex ligerica.jpg' => 'overzicht',
	
			'Carex ligerica 2.jpg' => 'bloeiwijze, urntje en nootje'
	
		),
	
		'Carex reichenbachii' => array(
	
			'Carex reichenbachii.jpg' => 'overzicht',
	
			'Carex reichenbachii 2.jpg' => 'bloeiwijze, urntje en nootje'
	
		),
	
		'Carex ovalis' => array(
	
			'Carex ovalis.jpg' => 'overzicht',
	
			'Carex ovalis 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem, c = urntje',
	
			'Carex ovalis 3.jpg' => 'foto',
	
			'Carex ovalis a.jpg' => 'habitus met details'
	
		),
	
		'Carex crawfordii' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Carex remota' => array(
	
			'Carex remota.jpg' => 'overzicht',
	
			'Carex remota 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem, c = urntje',
	
			'Carex remota 3.jpg' => 'foto',
	
			'Carex remota 4.jpg' => 'foto',
	
			'Carex boenninghausiana(x) 2.jpg' => '(= Carex paniculata x Carex remota) vergroot; a = vrouwelijke bloem, b = manlijke bloem, c = urntje',
	
			'Carex remota a.jpg' => 'habitus met details'
	
		),
	
		'Carex curta' => array(
	
			'Carex curta.jpg' => 'overzicht',
	
			'Carex curta 2.jpg' => 'vergroot; a = aar, b = kafje, c = urntje',
	
			'Carex curta 3.jpg' => 'foto',
	
			'Carex curta a.jpg' => 'habitus met details'
	
		),
	
		'Carex echinata' => array(
	
			'Carex echinata.jpg' => 'overzicht',
	
			'Carex echinata 2.jpg' => 'vergroot; a = nootje, b+c = urntje, d = kafje',
	
			'Carex echinata 3.jpg' => 'foto',
	
			'Carex echinata a.jpg' => 'habitus met details'
	
		),
	
		'Carex elongata' => array(
	
			'Carex elongata.jpg' => 'overzicht',
	
			'Carex elongata 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem',
	
			'Carex elongata 3.jpg' => 'foto',
	
			'Carex elongata a.jpg' => 'habitus met details'
	
		),
	
		'Carex appropinquata' => array(
	
			'Carex appropinquata.jpg' => 'overzicht',
	
			'Carex appropinquata 2.jpg' => 'a = manlijke bloem, b = vrouwelijke bloem, c = urntje',
	
			'Carex appropinquata 3.jpg' => 'foto',
	
			'Carex appropinquata a.jpg' => 'habitus met details'
	
		),
	
		'Carex diandra' => array(
	
			'Carex diandra.jpg' => 'overzicht',
	
			'Carex diandra 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem',
	
			'Carex diandra 3.jpg' => 'foto',
	
			'Carex diandra a.jpg' => 'habitus met details'
	
		),
	
		'Carex paniculata' => array(
	
			'Carex paniculata.jpg' => 'overzicht',
	
			'Carex paniculata 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem, c = urntje',
	
			'Carex paniculata 3.jpg' => 'foto',
	
			'Carex boenninghausiana(x) 2.jpg' => '(= Carex paniculata x Carex remota) vergroot; a = vrouwelijke bloem, b = manlijke bloem, c = urntje',
	
			'Carex paniculata a.jpg' => 'habitus met details'
	
		),
	
		'Carex otrubae' => array(
	
			'Carex otrubae.jpg' => 'overzicht',
	
			'Carex otrubae 2.jpg' => 'vergroot; a = urntje, b = kafje',
	
			'Carex otrubae 3.jpg' => 'foto',
	
			'Carex otrubae 4.jpg' => 'foto',
	
			'Carex otrubae a.jpg' => 'habitus met details'
	
		),
	
		'Carex vulpina' => array(
	
			'Carex vulpina.jpg' => 'overzicht',
	
			'Carex vulpina 2.jpg' => 'vergroot; a = manlijke bloem, b = urntje, c = dsn. urntje',
	
			'Carex vulpina a.jpg' => 'habitus met details'
	
		),
	
		'Carex vulpinoidea' => array(
	
			'Carex vulpinoidea.jpg' => 'overzicht foto',
	
			'Carex vulpinoidea 2.jpg' => 'detail; bladschede',
	
			'Carex vulpinoidea 3.jpg' => 'foto',
	
			'Carex vulpinoidea 4.jpg' => 'foto'
	
		),
	
		'Carex spicata' => array(
	
			'Carex spicata.jpg' => 'overzicht',
	
			'Carex spicata 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem, c = urntje + kafje',
	
			'Carex spicata 3.jpg' => 'foto',
	
			'Carex spicata a.jpg' => 'habitus met details'
	
		),
	
		'Carex muricata' => array(
	
			'Carex muricata.jpg' => 'overzicht',
	
			'Carex muricata 2.jpg' => 'vergroot',
	
			'Carex muricata 3.jpg' => 'foto',
	
			'Carex muricata 4.jpg' => 'foto',
	
			'Carex muricata a.jpg' => 'habitus met details'
	
		),
	
		'Carex divulsa' => array(
	
			'Carex divulsa.jpg' => 'overzicht',
	
			'Carex divulsa 2.jpg' => 'vergroot; a = urntje',
	
			'Carex divulsa 3.jpg' => 'foto',
	
			'Carex divulsa a.jpg' => 'habitus met details'
	
		),
	
		'Pseudosasa japonica' => array(
	
			'Pseudosasa japonica.jpg' => 'overzicht foto',
	
			'Pseudosasa japonica 3.jpg' => 'foto'
	
		),
	
		'Leersia oryzoides' => array(
	
			'Leersia oryzoides.jpg' => 'overzicht',
	
			'Leersia oryzoides 2.jpg' => 'vergroot',
	
			'Leersia oryzoides 3.jpg' => 'foto',
	
			'Leersia oryzoides 4.jpg' => 'foto'
	
		),
	
		'Nardus stricta' => array(
	
			'Nardus strica.jpg' => 'overzicht',
	
			'Nardus strica 2.jpg' => 'vergroot; e = aartje, f = bloem',
	
			'Nardus stricta 3.jpg' => 'foto',
	
			'Nardus stricta 4.jpg' => 'foto',
	
			'Nardus stricta a.jpg' => 'habitus met details'
	
		),
	
		'Milium vernale' => array(
	
			'Milium vernale.jpg' => 'overzicht',
	
			'Milium vernale 2.jpg' => 'vergroot',
	
			'Milium vernale 3.jpg' => 'foto',
	
			'Milium vernale 4.jpg' => 'foto',
	
			'Milium vernale a.jpg' => 'habitus met details'
	
		),
	
		'Milium effusum' => array(
	
			'Milium effusum.jpg' => 'overzicht',
	
			'Milium effusum 2.jpg' => 'vergroot',
	
			'Milium effusum 3.jpg' => 'foto',
	
			'Milium effusum a.jpg' => 'habitus met details'
	
		),
	
		'Festuca gigantea' => array(
	
			'Festuca gigantea.jpg' => 'overzicht',
	
			'Festuca gigantea 2.jpg' => 'vergroot',
	
			'Festuca gigantea 3.jpg' => 'foto',
	
			'Festuca gigantea 4.jpg' => 'foto',
	
			'Festuca gigantea a.jpg' => 'habitus met details'
	
		),
	
		'Festuca arundinacea' => array(
	
			'Festuca arundinacea.jpg' => 'overzicht',
	
			'Festuca arundinacea 3.jpg' => 'foto',
	
			'Festuca arundinacea a.jpg' => 'habitus met details'
	
		),
	
		'Festuca pratensis' => array(
	
			'Festuca pratensis.jpg' => 'overzicht',
	
			'Festuca pratensis 2.jpg' => 'vergroot',
	
			'Festuca pratensis 3.jpg' => 'foto',
	
			'Festuca pratensis 4.jpg' => 'foto',
	
			'Festuca pratensis 5.jpg' => 'foto',
	
			'Festuca pratensis a.jpg' => 'habitus met details'
	
		),
	
		'Festuca heterophylla' => array(
	
			'Festuca heterophylla.jpg' => 'overzicht',
	
			'Festuca heterophylla a.jpg' => 'habitus met details',
	
			'Festuca heterophylla 3.jpg' => 'foto',
	
			'Festuca heterophylla 4.jpg' => 'foto'
	
		),
	
		'Festuca arenaria' => array(
	
			'Festuca arenaria.jpg' => 'overzicht',
	
			'Festuca arenaria 3.jpg' => 'foto',
	
			'Festuca arenaria a.jpg' => 'habitus met details'
	
		),
	
		'Festuca rubra' => array(
	
			'Festuca rubra.jpg' => 'overzicht',
	
			'Festuca rubra 2.jpg' => 'vergroot',
	
			'Festuca rubra 3.jpg' => 'foto',
	
			'Festuca rubra commutata a.jpg' => 'habitus met details',
	
			'Festuca rubra rubra a.jpg' => 'habitus met details'
	
		),
	
		'Festuca filiformis' => array(
	
			'Festuca filifomis.jpg' => 'overzicht',
	
			'Festuca filiformis 3.jpg' => 'foto',
	
			'Festuca filiformis a.jpg' => 'habitus met details'
	
		),
	
		'Festuca ovina' => array(
	
			'Festuca ovina.jpg' => 'overzicht',
	
			'Festuca ovina a.jpg' => 'habitus met details'
	
		),
	
		'Festuca ovina subsp. hirtula' => array(
	
			'Festuca ovina hirtula.jpg' => 'overzicht',
	
			'Festuca ovina hirtula 2.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Festuca ovina subsp. guestphalica' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Festuca brevipila' => array(
	
			'Festuca brevipila.jpg' => 'overzicht',
	
			'Festuca brevipila 2.jpg' => 'vergroot',
	
			'Festuca brevipila 3.jpg' => 'foto',
	
			'Festuca brevipila 4.jpg' => 'foto',
	
			'Festuca brevipila 5.jpg' => 'foto'
	
		),
	
		'Festuca lemanii' => array(
	
			'Festuca lemanii.jpg' => 'doorsnede blad'
	
		),
	
		'Festuca pallens' => array(
	
			'Festuca pallens.jpg' => 'doorsnede blad'
	
		),
	
		'Festulolium(X) loliaceum' => array(
	
			'Festulolium(X) loliaceum.jpg' => 'overzicht',
	
			'Festulolium(X) loliaceum 2.jpg' => 'vergroot',
	
			'Festulolium(X) loliaceum 3.jpg' => 'foto',
	
			'Festulolium(X) loliaceum 4.jpg' => 'foto',
	
			'Festulolium(X) loliaceum a.jpg' => 'habitus met details'
	
		),
	
		'Festuca rubra-X-Vulpia-myuros' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Festuca rubra-X-Vulpia-bromoides' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Lolium temulentum' => array(
	
			'Lolium temulentum.jpg' => 'overzicht',
	
			'Lolium temulentum 2.jpg' => 'vergroot',
	
			'Lolium temulentum 3.jpg' => 'foto',
	
			'Lolium temulentum a.jpg' => 'habitus met details'
	
		),
	
		'Lolium remotum' => array(
	
			'Lolium remotum.jpg' => 'overzicht',
	
			'Lolium remotum 2.jpg' => 'vergroot; a = tongetje'
	
		),
	
		'Lolium perenne' => array(
	
			'Lolium perenne.jpg' => 'overzicht',
	
			'Lolium perenne 2.jpg' => 'vergroot',
	
			'Lolium perenne 3.jpg' => 'foto',
	
			'Lolium perenne 4.jpg' => 'foto',
	
			'Lolium perenne a.jpg' => 'habitus met details'
	
		),
	
		'Lolium multiflorum' => array(
	
			'Lolium multiflorum.jpg' => 'overzicht',
	
			'Lolium multiflorum 2.jpg' => 'vergroot',
	
			'Lolium multiflorum 3.jpg' => 'foto',
	
			'Lolium multiflorum a.jpg' => 'habitus met details'
	
		),
	
		'Vulpia fasciculata' => array(
	
			'Vulpia fasciculata.jpg' => 'overzicht',
	
			'Vulpia fasciculata 2.jpg' => 'top vruchtje',
	
			'Vulpia fasciculata a.jpg' => 'habitus met details',
	
			'Vulpia fasciculata 3.jpg' => 'tongeltje, aartje, vrucht'
	
		),
	
		'Vulpia membranacea' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Vulpia bromoides' => array(
	
			'Vulpia bromoides.jpg' => 'overzicht',
	
			'Vulpia bromoides 2.jpg' => 'vergroot',
	
			'Vulpia bromoides 3.jpg' => 'foto, in bloei',
	
			'Vulpia bromoides a.jpg' => 'habitus met details'
	
		),
	
		'Vulpia myuros' => array(
	
			'Vulpia myuros.jpg' => 'overzicht',
	
			'Vulpia myuros 2.jpg' => 'vergroot',
	
			'Vulpia myuros 3.jpg' => 'foto, habitus',
	
			'Vulpia myuros 4.jpg' => 'foto, detail bloeiwijze',
	
			'Vulpia myuros 5.jpg' => 'foto, bloeiwijzen',
	
			'Vulpia myuros a.jpg' => 'habitus met details'
	
		),
	
		'Vulpia ciliata' => array(
	
			'Vulpia ciliata ciliata.jpg' => 'overzicht foto',
	
			'Vulpia ciliata ciliata 2.jpg' => 'tekening, subsp. ciliata',
	
			'Vulpia ciliata ambigua a.jpg' => 'habitus met details, subsp. ambigua'
	
		),
	
		'Vulpia ciliata subsp. ambigua' => array(
	
			'Vulpia ciliata ambigua.jpg' => 'overzicht foto',
	
			'Vulpia ciliata ambigua 3.jpg' => 'foto, in bloei',
	
			'Vulpia ciliata ambigua 4.jpg' => 'foto, in bloei',
	
			'Vulpia ciliata ambigua a.jpg' => 'habitus met details'
	
		),
	
		'Vulpia ciliata subsp. ciliata' => array(
	
			'Vulpia ciliata ciliata.jpg' => 'overzicht foto',
	
			'Vulpia ciliata ciliata 2.jpg' => 'Heukels'
	
		),
	
		'Micropyrum tenellum' => array(
	
			'Micropyrum tenellum.jpg' => 'overzicht',
	
			'Micropyrum tenellum 2.jpg' => 'vergroot; a = onderste kelkkafje, b = bovenste kelkkafje, c = lemma, d = palea, e = tongetje, f = aartje',
	
			'Micropyrum tenellum 3.jpg' => 'foto'
	
		),
	
		'Cynosurus cristatus' => array(
	
			'Cynosurus cristatus.jpg' => 'overzicht',
	
			'Cynosurus cristatus 2.jpg' => 'vergroot; f = aartje',
	
			'Cynosurus cristatus 3.jpg' => 'foto',
	
			'Cynosurus cristatus 4.jpg' => 'foto',
	
			'Cynosurus cristatus a.jpg' => 'habitus met details'
	
		),
	
		'Cynosurus echinatus' => array(
	
			'Cynosurus echinatus.jpg' => 'overzicht',
	
			'Cynosurus echinatus a.jpg' => 'habitus met details',
	
			'Cynosurus echinatus 3.jpg' => 'foto'
	
		),
	
		'Lamarckia aurea' => array(
	
			'Lamarckia aurea.jpg' => 'overzicht',
	
			'Lamarckia aurea 2.jpg' => 'vergroot',
	
			'Lamarckia aurea 3.jpg' => 'foto',
	
			'Lamarckia aurea a.jpg' => 'habitus met details'
	
		),
	
		'Puccinellia maritima' => array(
	
			'Puccinellia maritima.jpg' => 'overzicht',
	
			'Puccinellia maritima 2.jpg' => 'vergroot',
	
			'Puccinellia maritima 3.jpg' => 'foto',
	
			'Puccinellia maritima a.jpg' => 'habitus met details'
	
		),
	
		'Puccinellia distans' => array(
	
			'Puccinellia distans distans.jpg' => 'overzicht',
	
			'Puccinellia distans dist a.jpg' => 'habitus met details, subsp. distans',
	
			'Puccinellia distans bor a.jpg' => 'habitus met details, subsp. borealis'
	
		),
	
		'Puccinellia distans subsp. distans' => array(
	
			'Puccinellia distans distans.jpg' => 'overzicht',
	
			'Puccinellia distans dist a.jpg' => 'habitus met details',
	
			'Puccinellia distans dist 3.jpg' => 'foto'
	
		),
	
		'Puccinellia distans subsp. borealis' => array(
	
			'Puccinellia distans borealis.jpg' => 'overzicht',
	
			'Puccinellia distans bor a.jpg' => 'habitus met details',
	
			'Puccinellia distans bor 3.jpg' => 'foto'
	
		),
	
		'Puccinellia rupestris' => array(
	
			'Puccinellia rupestris.jpg' => 'overzicht',
	
			'Puccinellia rupestris 2.jpg' => 'vergroot',
	
			'Puccinellia rupestris 3.jpg' => 'foto',
	
			'Puccinellia rupestris 4.jpg' => 'foto',
	
			'Puccinellia rupestris a.jpg' => 'habitus met details'
	
		),
	
		'Puccinellia fasciculata' => array(
	
			'Puccinellia fasciculata.jpg' => 'overzicht',
	
			'Puccinellia fasciculata a.jpg' => 'habitus met details'
	
		),
	
		'Briza media' => array(
	
			'Briza media.jpg' => 'overzicht',
	
			'Briza media 2.jpg' => 'vergroot',
	
			'Briza media 3.jpg' => 'foto',
	
			'Briza media a.jpg' => 'habitus met details'
	
		),
	
		'Poa compressa' => array(
	
			'Poa compressa.jpg' => 'overzicht',
	
			'Poa compressa 2.jpg' => 'vergroot',
	
			'Poa compressa 3.jpg' => 'foto',
	
			'Poa compressa 4.jpg' => 'foto',
	
			'Poa compressa a.jpg' => 'habitus met details'
	
		),
	
		'Poa angustifolia' => array(
	
			'Poa angustifolia.jpg' => 'overzicht',
	
			'Poa angustifolia 2.jpg' => 'vergroot',
	
			'Poa angustifolia a.jpg' => 'habitus met details'
	
		),
	
		'Poa pratensis' => array(
	
			'Poa pratensis.jpg' => 'overzicht',
	
			'Poa pratensis 2.jpg' => 'vergroot',
	
			'Poa pratensis 3.jpg' => 'foto',
	
			'Poa pratensis a.jpg' => 'habitus met details',
	
			'Poa pratensis b.jpg' => 'habitus met details'
	
		),
	
		'Poa bulbosa' => array(
	
			'Poa bullbosa.jpg' => 'overzicht',
	
			'Poa bulbosa a.jpg' => 'habitus met details',
	
			'Poa bullbosa 2.jpg' => 'vergroot',
	
			'Poa bulbosa bulbosa 3.jpg' => 'foto',
	
			'Poa bulbosa vivipara 3.jpg' => 'foto'
	
		),
	
		'Poa chaixii' => array(
	
			'Poa chaixii.jpg' => 'overzicht',
	
			'Poa chaixii 2.jpg' => 'vergroot',
	
			'Poa chaixii a.jpg' => 'habitus met details',
	
			'Poa chaixii 3.jpg' => 'foto'
	
		),
	
		'Poa nemoralis' => array(
	
			'Poa nemoralis.jpg' => 'overzicht',
	
			'Poa nemoralis 3.jpg' => 'foto',
	
			'Poa nemoralis a.jpg' => 'habitus met details'
	
		),
	
		'Poa trivialis' => array(
	
			'Poa trivialis.jpg' => 'overzicht',
	
			'Poa trivialis 2.jpg' => 'vergroot',
	
			'Poa trivialis 3.jpg' => 'foto',
	
			'Poa trivialis 4.jpg' => 'foto',
	
			'Poa trivialis a.jpg' => 'habitus met details'
	
		),
	
		'Poa annua' => array(
	
			'Poa annua.jpg' => 'overzicht',
	
			'Poa annua 2.jpg' => 'vergroot',
	
			'Poa annua 3.jpg' => 'foto',
	
			'Poa annua a.jpg' => 'habitus met details'
	
		),
	
		'Poa palustris' => array(
	
			'Poa palustris.jpg' => 'overzicht',
	
			'Poa palustris 2.jpg' => 'vergroot',
	
			'Poa palustris 3.jpg' => 'foto',
	
			'Poa palustris a.jpg' => 'habitus met details'
	
		),
	
		'Dactylis glomerata' => array(
	
			'Dactylis glomerata.jpg' => 'overzicht',
	
			'Dactylis glomerata 2.jpg' => 'vergroot',
	
			'Dactylis glomerata 3.jpg' => 'foto',
	
			'Dactylis glomerata 4.jpg' => 'foto',
	
			'Dactylis glomerata a.jpg' => 'habitus met details'
	
		),
	
		'Catabrosa aquatica' => array(
	
			'Catabrosa aquatica.jpg' => 'overzicht',
	
			'Catabrosa aquatica 2.jpg' => 'vergroot',
	
			'Catabrosa aquatica 3.jpg' => 'foto',
	
			'Catabrosa aquatica 4.jpg' => 'foto',
	
			'Catabrosa aquatica a.jpg' => 'habitus met details'
	
		),
	
		'Catapodium marinum' => array(
	
			'Catapodium marinum.jpg' => 'overzicht',
	
			'Catapodium marinum 2.jpg' => 'vergroot',
	
			'Catapodium marinum 3.jpg' => 'foto',
	
			'Catapodium marinum a.jpg' => 'habitus met details',
	
			'Catapodium mar en rig.jpg' => 'bloeiwijzen'
	
		),
	
		'Catapodium rigidum' => array(
	
			'Catapodium rigidum.jpg' => 'overzicht',
	
			'Catapodium rigidum 2.jpg' => 'vergroot',
	
			'Catapodium rigidum 3.jpg' => 'foto',
	
			'Catapodium rigidum 4.jpg' => 'foto, bloeiwijzen',
	
			'Catapodium rigidum a.jpg' => 'habitus met details',
	
			'Catapodium mar en rig.jpg' => 'bloeiwijzen'
	
		),
	
		'Sesleria albicans' => array(
	
			'Sesleria albicans.jpg' => 'overzicht',
	
			'Sesleria albicans 2.jpg' => 'vergroot; a = onderste kelkkafje, b = bovenste kelkkafje, c = lemma, d = palea, e = tongetje, f = aartje',
	
			'Sesleria albicans 3.jpg' => 'foto, habitus bloeiend',
	
			'Sesleria albicans 4.jpg' => 'foto, bloeiwijze',
	
			'Sesleria albicans 5.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Parapholis strigosa' => array(
	
			'Parapholis strigosa.jpg' => 'overzicht',
	
			'Parapholis strigosa 3.jpg' => 'foto',
	
			'Parapholis strigosa 4.jpg' => 'foto',
	
			'Parapholis strigosa 5.jpg' => 'foto',
	
			'Parapholis strigosa 6.jpg' => 'foto',
	
			'Parapholis strigosa a.jpg' => 'habitus met details'
	
		),
	
		'Glyceria maxima' => array(
	
			'Glyceria maxima.jpg' => 'overzicht',
	
			'Glyceria maxima 2.jpg' => 'vergroot',
	
			'Glyceria maxima 3.jpg' => 'foto',
	
			'Glyceria maxima a.jpg' => 'habitus met details'
	
		),
	
		'Glyceria pedicellata(x)' => array(
	
			'Glyceria pedicellata(x).jpg' => 'overzicht',
	
			'Glyceria pedicellata(x) 2.jpg' => 'vergroot; a = onderste kelkkafje, b = bovenste kelkkafje, c = lemma, d = palea, e = aartje'
	
		),
	
		'Glyceria fluitans' => array(
	
			'Glyceria fluitans.jpg' => 'overzicht',
	
			'Glyceria fluitans 2.jpg' => 'vergroot',
	
			'Glyceria fluitans 3.jpg' => 'foto',
	
			'Glyceria fluitans a.jpg' => 'habitus met details'
	
		),
	
		'Glyceria notata' => array(
	
			'Glyceria notata.jpg' => 'overzicht',
	
			'Glyceria notata 2.jpg' => 'vergroot; f = aartje',
	
			'Glyceria notata 3.jpg' => 'foto',
	
			'Glyceria notata 4.jpg' => 'foto',
	
			'Glyceria notata 5.jpg' => 'foto',
	
			'Glyceria notata 6.jpg' => 'foto',
	
			'Glyceria notata 7.jpg' => 'foto',
	
			'Glyceria notata a.jpg' => 'habitus met details'
	
		),
	
		'Glyceria declinata' => array(
	
			'Glyceria declinata.jpg' => 'overzicht',
	
			'Glyceria declinata 3.jpg' => 'foto',
	
			'Glyceria declinata a.jpg' => 'habitus met details'
	
		),
	
		'Melica uniflora' => array(
	
			'Melica uniflora.jpg' => 'overzicht',
	
			'Melica uniflora 2.jpg' => 'vergroot',
	
			'Melica uniflora 3.jpg' => 'foto',
	
			'Melica uniflora a.jpg' => 'habitus met details'
	
		),
	
		'Melica nutans' => array(
	
			'Melica nutans.jpg' => 'overzicht',
	
			'Melica nutans 2.jpg' => 'vergroot',
	
			'Melica nutans 3.jpg' => 'foto',
	
			'Melica nutans 4.jpg' => 'foto',
	
			'Melica nutans a.jpg' => 'habitus met details'
	
		),
	
		'Helictotrichon pubescens' => array(
	
			'Helictotrichon pubescens.jpg' => 'overzicht',
	
			'Helictotrichon pubescens 2.jpg' => 'vergroot',
	
			'Helictotrichon pubescens 3.jpg' => 'foto',
	
			'Helictotrichon pubescens a.jpg' => 'habitus met details'
	
		),
	
		'Helictotrichon pratense' => array(
	
			'Helictotrichon pratense.jpg' => 'overzicht',
	
			'Helictotrichon pratense 2.jpg' => 'vergroot',
	
			'Helictotrichon pratense 3.jpg' => 'foto',
	
			'Helictotrichon pratense 4.jpg' => 'foto',
	
			'Helictotrichon pratense 5.jpg' => 'foto',
	
			'Helictotrichon pratense 6.jpg' => 'foto',
	
			'Helictotrichon pratense a.jpg' => 'habitus met details'
	
		),
	
		'Arrhenatherum elatius' => array(
	
			'Arrhenatherum elatius.jpg' => 'overzicht',
	
			'Arrhenatherum elatius 2.jpg' => 'vergroot',
	
			'Arrhenatherum elatius 3.jpg' => 'foto, bloeiwijze',
	
			'Arrhenatherum elatius 4.jpg' => 'foto, bloeiwijze',
	
			'Arrhenatherum elatius a.jpg' => 'habitus met details'
	
		),
	
		'Arrhenatherum elatius subsp. elatius' => array(
	
			'Arrhenatherum elatius elat.jpg' => 'overzicht',
	
			'Arrhenatherum elatius elat 2.jpg' => 'blad en bladschede'
	
		),
	
		'Arrhenatherum elatius subsp. bulbosum' => array(
	
			'Arrhenatherum elatius bulb.jpg' => 'overzicht',
	
			'Arrhenatherum elatius bulb 2.jpg' => 'voet van stengel met knollen'
	
		),
	
		'Avena sterilis' => array(
	
			'Avena sterilis.jpg' => 'overzicht',
	
			'Avena sterilis a.jpg' => 'habitus met details'
	
		),
	
		'Avena fatua' => array(
	
			'Avena fatua.jpg' => 'overzicht',
	
			'Avena fatua 3.jpg' => 'foto',
	
			'Avena fatua 4.jpg' => 'foto',
	
			'Avena fatua 5.jpg' => 'tekening Heukels',
	
			'Avena fatua a.jpg' => 'habitus met details'
	
		),
	
		'Avena sativa' => array(
	
			'Avena sativa.jpg' => 'overzicht foto',
	
			'Avena sativa 3.jpg' => 'foto'
	
		),
	
		'Avena strigosa' => array(
	
			'Avena strigosa.jpg' => 'overzicht',
	
			'Avena strigosa a.jpg' => 'habitus met details'
	
		),
	
		'Trisetum flavescens' => array(
	
			'Trisetum flavescens.jpg' => 'overzicht',
	
			'Trisetum flavescens 2.jpg' => 'vergroot',
	
			'Trisetum flavescens 3.jpg' => 'foto, in bloei',
	
			'Trisetum flavescens 4.jpg' => 'foto, in bloei',
	
			'Trisetum flavescens a.jpg' => 'habitus met details'
	
		),
	
		'Koeleria pyramidata' => array(
	
			'Koeleria pyramidata.jpg' => 'overzicht foto',
	
			'Koeleria pyramidata 2.jpg' => 'tekening Heukels',
	
			'Koeleria pyramidata 3.jpg' => 'foto'
	
		),
	
		'Koeleria macrantha' => array(
	
			'Koeleria macrantha.jpg' => 'overzicht',
	
			'Koeleria macrantha 2.jpg' => 'vergroot',
	
			'Koeleria macrantha 3.jpg' => 'foto',
	
			'Koeleria macrantha 4.jpg' => 'foto',
	
			'Koeleria macrantha a.jpg' => 'habitus met details'
	
		),
	
		'Rostraria cristata' => array(
	
			'Rostraria cristata.jpg' => 'overzicht',
	
			'Rostraria cristata 2.jpg' => 'foto, habitus bloeiend',
	
			'Rostraria cristata 3.jpg' => 'tekening '
	
		),
	
		'Deschampsia cespitosa' => array(
	
			'Deschampsia cespitosa.jpg' => 'overzicht',
	
			'Deschampsia cespitosa 2.jpg' => 'vergroot',
	
			'Deschampsia cespitosa a.jpg' => 'habitus met details',
	
			'Deschampsia cespitosa 3.jpg' => 'foto'
	
		),
	
		'Deschampsia flexuosa' => array(
	
			'Deschampsia flexuosa.jpg' => 'overzicht',
	
			'Deschampsia flexuosa 2.jpg' => 'vergroot',
	
			'Deschampsia flexuosa 3.jpg' => 'foto',
	
			'Deschampsia flexuosa a.jpg' => 'habitus met details'
	
		),
	
		'Deschampsia setacea' => array(
	
			'Deschampsia setacea.jpg' => 'overzicht',
	
			'Deschampsia setacea 2.jpg' => 'vergroot',
	
			'Deschampsia setacea 3.jpg' => 'foto',
	
			'Deschampsia setacea 4.jpg' => 'foto',
	
			'Deschampsia setacea a.jpg' => 'habitus met details'
	
		),
	
		'Holcus lanatus' => array(
	
			'Holcus lanatus.jpg' => 'overzicht',
	
			'Holcus lanatus 2.jpg' => 'vergroot',
	
			'Holcus lanatus 3.jpg' => 'foto',
	
			'Holcus lanatus a.jpg' => 'habitus met details'
	
		),
	
		'Holcus mollis' => array(
	
			'Holcus mollis.jpg' => 'overzicht',
	
			'Holcus mollis 2.jpg' => 'vergroot',
	
			'Holcus mollis 3.jpg' => 'foto',
	
			'Holcus mollis a.jpg' => 'habitus met details'
	
		),
	
		'Corynephorus canescens' => array(
	
			'Corynephorus canescens.jpg' => 'overzicht',
	
			'Corynephorus canescens 2.jpg' => 'vergroot',
	
			'Corynephorus canescens 3.jpg' => 'foto',
	
			'Corynephorus canescens a.jpg' => 'habitus met details'
	
		),
	
		'Aira praecox' => array(
	
			'Aira praecox.jpg' => 'overzicht',
	
			'Aira praecox 2.jpg' => 'vergroot',
	
			'Aira praecox 3.jpg' => 'foto, habitus bloeiend',
	
			'Aira praecox a.jpg' => 'habitus met details'
	
		),
	
		'Aira caryophyllea' => array(
	
			'Aira caryophyllea.jpg' => 'overzicht',
	
			'Aira caryophyllea 2.jpg' => 'vergroot',
	
			'Aira caryophyllea 3.jpg' => 'foto, habitus bloeiend',
	
			'Aira caryophyllea a.jpg' => 'habitus met details'
	
		),
	
		'Hierochloe odorata' => array(
	
			'Hierochloe odorata.jpg' => 'overzicht',
	
			'Hierochloe odorata 2.jpg' => 'vergroot',
	
			'Hierochloe odorata 3.jpg' => 'foto',
	
			'Hierochloe odorata 4.jpg' => 'foto',
	
			'Hierochloe odorata a.jpg' => 'habitus met details'
	
		),
	
		'Anthoxanthum odoratum' => array(
	
			'Anthoxanthum odoratum.jpg' => 'overzicht',
	
			'Anthoxanthum odoratum 2.jpg' => 'vergroot',
	
			'Anthoxanthum odoratum 3.jpg' => 'foto, habitus bloeiend',
	
			'Anthoxanthum odoratum a.jpg' => 'habitus met details'
	
		),
	
		'Anthoxanthum aristatum' => array(
	
			'Anthoxanthum aristatum.jpg' => 'overzicht',
	
			'Anthoxanthum aristatum 3.jpg' => 'foto, habitus bloeiend',
	
			'Anthoxanthum aristatum pu a.jpg' => 'habitus met details'
	
		),
	
		'Phalaris arundinacea' => array(
	
			'Phalaris arundinacea.jpg' => 'overzicht',
	
			'Phalaris arundinacea 2.jpg' => 'vergroot',
	
			'Phalaris arundinacea 3.jpg' => 'foto',
	
			'Phalaris arundinacea a.jpg' => 'habitus met details'
	
		),
	
		'Phalaris canariensis' => array(
	
			'Phalaris canariensis.jpg' => 'overzicht',
	
			'Phalaris canariensis 3.jpg' => 'foto',
	
			'Phalaris canariensis a.jpg' => 'habitus met details'
	
		),
	
		'Agrostis stolonifera' => array(
	
			'Agrostis stolonifera.jpg' => 'overzicht',
	
			'Agrostis stolonifera 3.jpg' => 'foto, habitus bloeiend',
	
			'Agrostis stolonifera 4.jpg' => 'foto, bloeiwijzen',
	
			'Agrostis stolonifera a.jpg' => 'habitus met details'
	
		),
	
		'Agrostis canina' => array(
	
			'Agrostis canina.jpg' => 'overzicht',
	
			'Agrostis canina a.jpg' => 'habitus met details'
	
		),
	
		'Agrostis vinealis' => array(
	
			'Agrostis vinealis.jpg' => 'overzicht',
	
			'Agrostis vinealis 3.jpg' => 'foto, habitus bloeiend',
	
			'Agrostis vinealis a.jpg' => 'habitus met details'
	
		),
	
		'Agrostis gigantea' => array(
	
			'Agrostis gigantea.jpg' => 'overzicht',
	
			'Agrostis gigantea 3.jpg' => 'foto, bloeiwijze',
	
			'Agrostis gigantea a.jpg' => 'habitus met details'
	
		),
	
		'Agrostis capillaris' => array(
	
			'Agrostis capillaris.jpg' => 'overzicht',
	
			'Agrostis capillaris 3.jpg' => 'foto, bloeiwijze',
	
			'Agrostis capillaris a.jpg' => 'habitus met details'
	
		),
	
		'Agrostis fouilladeana(x)' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Calamagrostis stricta' => array(
	
			'Calamagrostis stricta.jpg' => 'overzicht',
	
			'Calamagrostis stricta 2.jpg' => 'vergroot;',
	
			'Calamagrostis stricta 3.jpg' => 'foto',
	
			'Calamagrostis stricta a.jpg' => 'habitus met details'
	
		),
	
		'Calamagrostis canescens' => array(
	
			'Calamagrostis canescens.jpg' => 'overzicht',
	
			'Calamagrostis canescens 2.jpg' => 'vergroot',
	
			'Calamagrostis canescens 3.jpg' => 'foto',
	
			'Calamagrostis canescens a.jpg' => 'habitus met details'
	
		),
	
		'Calamagrostis epigejos' => array(
	
			'Calamagrostis epigejos.jpg' => 'overzicht',
	
			'Calamagrostis epigejos 2.jpg' => 'vergroot',
	
			'Calamagrostis epigejos 3.jpg' => 'foto',
	
			'Calamagrostis epigejos 4.jpg' => 'foto',
	
			'Calamagrostis epigejos 5.jpg' => 'foto',
	
			'Calamagrostis epigejos a.jpg' => 'habitus met details'
	
		),
	
		'Calamagrostis pseudophragmites' => array(
	
			'Calamagrostis pseudophragm.jpg' => 'overzicht',
	
			'Calamagrostis pseudophragm 2.jpg' => 'vergroot; a = aartje, b = bloem'
	
		),
	
		'Calammophila(X) baltica' => array(
	
			'Calammophila(x) baltica.jpg' => 'overzicht',
	
			'Calammophila(x) baltica 2.jpg' => 'vergroot',
	
			'Calammophila(x) baltica 3.jpg' => 'foto',
	
			'Calammophila(X) baltica 4.jpg' => 'beharing op de ribben',
	
			'Calammophila(X) baltica a.jpg' => 'habitus met details'
	
		),
	
		'Ammophila arenaria' => array(
	
			'Ammophila arenaria.jpg' => 'overzicht',
	
			'Ammophila arenaria 2.jpg' => 'vergroot',
	
			'Ammophila arenaria 3.jpg' => 'foto, bloeiend',
	
			'Ammophila arenaria 4.jpg' => 'Detail beharing op ribben',
	
			'Ammophila arenaria a.jpg' => 'habitus met details'
	
		),
	
		'Lagurus ovatus' => array(
	
			'Lagurus ovatus.jpg' => 'overzicht',
	
			'Lagurus ovatus 2.jpg' => 'vergroot',
	
			'Lagurus ovatus 3.jpg' => 'foto',
	
			'Lagurus ovatus 4.jpg' => 'foto',
	
			'Lagurus ovatus a.jpg' => 'habitus met details'
	
		),
	
		'Apera interrupta' => array(
	
			'Apera interrupta.jpg' => 'overzicht',
	
			'Apera interrupta 2.jpg' => 'vergroot',
	
			'Apera interrupta 3.jpg' => 'foto, habitus bloeiend',
	
			'Apera interrupta 4.jpg' => 'foto, bloeiwijzen',
	
			'Apera interrupta a.jpg' => 'habitus met details'
	
		),
	
		'Apera spica-venti' => array(
	
			'Apera spica-venti.jpg' => 'overzicht',
	
			'Apera spica-venti 2.jpg' => 'vergroot',
	
			'Apera spica-venti 3.jpg' => 'foto, habitus bloeiend',
	
			'Apera spica-venti 4.jpg' => 'foto, bloeiwijze',
	
			'Apera spica-venti a.jpg' => 'habitus met details'
	
		),
	
		'Mibora minima' => array(
	
			'Mibora minima.jpg' => 'overzicht',
	
			'Mibora minima 2.jpg' => 'vergroot',
	
			'Mibora minima a.jpg' => 'habitus met details',
	
			'Mibora minima 3.jpg' => 'foto'
	
		),
	
		'Polypogon monspeliensis' => array(
	
			'Polypogon monspeliensis.jpg' => 'overzicht',
	
			'Polypogon monspeliensis 2.jpg' => 'vergroot',
	
			'Polypogon monspeliensis 3.jpg' => 'foto',
	
			'Polypogon monspeliensis 4.jpg' => 'foto',
	
			'Polypogon monspeliensis a.jpg' => 'habitus met details'
	
		),
	
		'Polypogon viridis' => array(
	
			'Polypogon viridis.jpg' => 'overzicht',
	
			'Polypogon viridis a.jpg' => 'habitus met details',
	
			'Polypogon viridis 2.jpg' => 'foto, bloeiend',
	
			'Polypogon viridis 3.jpg' => 'foto, bloeiend',
	
			'Polypogon viridis 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Alopecurus myosuroides' => array(
	
			'Alopecurus myosuroides.jpg' => 'overzicht',
	
			'Alopecurus myosuroides 3.jpg' => 'foto, habitus bloeiend',
	
			'Alopecurus myosuroides a.jpg' => 'habitus met details'
	
		),
	
		'Alopecurus bulbosus' => array(
	
			'Alopecurus bulbosus.jpg' => 'overzicht',
	
			'Alopecurus bulbosus 3.jpg' => 'foto, habitus bloeiend',
	
			'Alopecurus bulbosus 4.jpg' => 'foto, bloeiend',
	
			'Alopecurus bulbosus 5.jpg' => 'foto, detail wortelknolletjes',
	
			'Alopecurus bulbosus a.jpg' => 'habitus met details'
	
		),
	
		'Alopecurus aequalis' => array(
	
			'Alopecurus aequalis.jpg' => 'overzicht',
	
			'Alopecurus aequalis 2.jpg' => 'vergroot',
	
			'Alopecurus aequalis 3.jpg' => 'foto, bloeiwijzen',
	
			'Alopecurus aequalis 4.jpg' => 'foto, bloeiwijzen',
	
			'Alopecurus aequalis a.jpg' => 'habitus met details'
	
		),
	
		'Alopecurus geniculatus' => array(
	
			'Alopecurus geniculatus.jpg' => 'overzicht',
	
			'Alopecurus geniculatus 3.jpg' => 'foto, habitus bloeiend',
	
			'Alopecurus geniculatus a.jpg' => 'habitus met details'
	
		),
	
		'Alopecurus pratensis' => array(
	
			'Alopecurus pratensis.jpg' => 'overzicht',
	
			'Alopecurus pratensis 3.jpg' => 'foto, habitus bloeiend',
	
			'Alopecurus pratensis 4.jpg' => 'foto, bloeiwijze',
	
			'Alopecurus pratensis a.jpg' => 'habitus met details'
	
		),
	
		'Phleum arenarium' => array(
	
			'Phleum arenarium.jpg' => 'overzicht',
	
			'Phleum arenarium 3.jpg' => 'foto',
	
			'Phleum arenarium 4.jpg' => 'foto',
	
			'Phleum arenarium a.jpg' => 'habitus met details'
	
		),
	
		'Phleum pratense' => array(
	
			'Phleum pratense.jpg' => 'overzicht',
	
			'Phleum pratense a.jpg' => 'habitus met details'
	
		),
	
		'Phleum pratense subsp. pratense' => array(
	
			'Phleum pratense pratense.jpg' => 'overzicht',
	
			'Phleum pratense pratense 3.jpg' => 'foto',
	
			'Phleum pratense pratense 4.jpg' => 'foto'
	
		),
	
		'Phleum pratense subsp. serotinum' => array(
	
			'Phleum pratense serotinum.jpg' => 'overzicht',
	
			'Phleum pratense serotinum 3.jpg' => 'foto'
	
		),
	
		'Bromus secalinus' => array(
	
			'Bromus secalinus.jpg' => 'overzicht',
	
			'Bromus secalinus 2.jpg' => 'vergroot',
	
			'Bromus secalinus 3.jpg' => 'foto',
	
			'Bromus secalinus 4.jpg' => 'foto, bloeiwijzen',
	
			'Bromus secalinus a.jpg' => 'habitus met details'
	
		),
	
		'Bromus racemosus' => array(
	
			'Bromus racemosus.jpg' => 'overzicht',
	
			'Bromus racemosus 2.jpg' => 'vergroot',
	
			'Bromus racemosus 3.jpg' => 'foto',
	
			'Bromus racemosus 4.jpg' => 'beharing bladschede',
	
			'Bromus racemosus a.jpg' => 'habitus met details'
	
		),
	
		'Bromus racemosus subsp. commutatus' => array(
	
			'Bromus racemosus commutatus.jpg' => 'overzicht',
	
			'Bromus racemosus commutat a.jpg' => 'habitus met details'
	
		),
	
		'Bromus racemosus subsp. racemosus' => array(
	
			'Bromus racemosus racemosus.jpg' => 'overzicht'
	
		),
	
		'Bromus arvensis' => array(
	
			'Bromus arvensis.jpg' => 'overzicht',
	
			'Bromus arvensis 2.jpg' => 'vergroot',
	
			'Bromus arvensis 3.jpg' => 'foto',
	
			'Bromus arvensis a.jpg' => 'habitus met details'
	
		),
	
		'Bromus lepidus' => array(
	
			'Bromus lepidus.jpg' => 'overzicht',
	
			'Bromus lepidus a.jpg' => 'habitus met details'
	
		),
	
		'Bromus hordeaceus' => array(
	
			'Bromus hordeaceus.jpg' => 'overzicht',
	
			'Bromus hordeaceus 2.jpg' => 'vergroot',
	
			'Bromus hordeaceus 3.jpg' => 'foto',
	
			'Bromus hordeaceus 4.jpg' => 'foto',
	
			'Bromus hordeaceus 5.jpg' => 'beharing bladschede'
	
		),
	
		'Bromus hordeaceus subsp. thominei' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Bromus hordeaceus subsp. hordeaceus' => array(
	
			'Bromus hordeaceus hordeac.jpg' => 'overzicht',
	
			'Bromus hordeaceus hordeac a.jpg' => 'habitus met details'
	
		),
	
		'Bromopsis ramosa' => array(
	
			'Bromopsis ramosa.jpg' => 'overzicht',
	
			'Bromopsis ramosa a.jpg' => 'habitus met details'
	
		),
	
		'Bromopsis ramosa subsp. ramosa' => array(
	
			'Bromopsis ramosa ramosa.jpg' => 'overzicht',
	
			'Bromopsis ramosa ramosa 3.jpg' => 'foto'
	
		),
	
		'Bromopsis ramosa subsp. benekenii' => array(
	
			'Bromopsis ramosa benekenii.jpg' => 'overzicht',
	
			'Bromopsis ramosa benekenii 3.jpg' => 'foto',
	
			'Bromopsis ramosa benekenii a.jpg' => 'habitus met details'
	
		),
	
		'Bromopsis erecta' => array(
	
			'Bromopsis erecta.jpg' => 'overzicht',
	
			'Bromopsis erecta 2.jpg' => 'vergroot',
	
			'Bromopsis erecta 3.jpg' => 'foto',
	
			'Bromopsis erecta a.jpg' => 'habitus met details'
	
		),
	
		'Bromopsis inermis' => array(
	
			'Bromopsis inermis.jpg' => 'overzicht; a = aartje, b = lemma, c = bloem van binnen gezien',
	
			'Bromopsis inermis 2.jpg' => 'vergroot',
	
			'Bromopsis inermis 3.jpg' => 'foto'
	
		),
	
		'Bromopsis inermis subsp. inermis' => array(
	
			'Bromopsis inermis inermis.jpg' => 'overzicht',
	
			'Bromopsis inermis inermis 3.jpg' => 'foto'
	
		),
	
		'Bromopsis inermis subsp. pumpelliana' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Anisantha diandra' => array(
	
			'Anisantha diandra.jpg' => 'overzicht',
	
			'Anisantha diandra a.jpg' => 'habitus met details',
	
			'Anisantha diandra 2.jpg' => 'foto, bloeiwijze',
	
			'Anisantha diandra 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Anisantha madritensis' => array(
	
			'Anisantha madritensis.jpg' => 'overzicht',
	
			'Anisantha madritensis a.jpg' => 'habitus met details',
	
			'Anisantha madritensis 2.jpg' => 'foto, bloeiwijze',
	
			'Anisantha madritensis 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Anisantha sterilis' => array(
	
			'Anisantha sterilis.jpg' => 'overzicht',
	
			'Anisantha sterilis 3.jpg' => 'foto, bloeiwijze',
	
			'Anisantha sterilis 4.jpg' => 'foto, bloeiwijze',
	
			'Anisantha sterilis a.jpg' => 'habitus met details'
	
		),
	
		'Anisantha tectorum' => array(
	
			'Anisantha tectorum.jpg' => 'overzicht',
	
			'Anisantha tectorum 2.jpg' => 'vergroot',
	
			'Anisantha tectorum 3.jpg' => 'foto, bloeiwijze',
	
			'Anisantha tectorum 4.jpg' => 'foto, bloeiwijze',
	
			'Anisantha tectorum a.jpg' => 'habitus met details'
	
		),
	
		'Ceratochloa cathartica' => array(
	
			'Ceratochloa cathartica.jpg' => 'overzicht',
	
			'Ceratochloa cathartica 3.jpg' => 'foto',
	
			'Ceratochloa cathartica 4.jpg' => 'foto'
	
		),
	
		'Ceratochloa carinata' => array(
	
			'Ceratochloa carinata.jpg' => 'overzicht',
	
			'Ceratochloa carinata 3.jpg' => 'foto',
	
			'Ceratochloa carinata 4.jpg' => 'habitus met details',
	
			'Ceratochloa carinata a.jpg' => 'habitus met details'
	
		),
	
		'Brachypodium sylvaticum' => array(
	
			'Brachypodium sylvaticum.jpg' => 'overzicht',
	
			'Brachypodium sylvaticum 3.jpg' => 'foto',
	
			'Brachypodium sylvaticum 4.jpg' => 'foto',
	
			'Brachypodium sylvaticum a.jpg' => 'habitus met details'
	
		),
	
		'Brachypodium pinnatum' => array(
	
			'Brachypodium pinnatum.jpg' => 'overzicht',
	
			'Brachypodium pinnatum 3.jpg' => 'foto',
	
			'Brachypodium pinnatum 4.jpg' => 'foto',
	
			'Brachypodium pinnatum a.jpg' => 'habitus met details'
	
		),
	
		'Elymus caninus' => array(
	
			'Elymus caninus.jpg' => 'overzicht',
	
			'Elymus caninus 3.jpg' => 'foto',
	
			'Elymus caninus 4.jpg' => 'foto',
	
			'Elymus caninus 5.jpg' => 'foto',
	
			'Elymus caninus a.jpg' => 'habitus met details'
	
		),
	
		'Elytrigia juncea subsp. boreoatlantica' => array(
	
			'Elytrigia juncea.jpg' => 'overzicht',
	
			'Elytrigia juncea 2.jpg' => 'vergroot',
	
			'Elytrigia juncea 3.jpg' => 'foto',
	
			'Elytrigia juncea a.jpg' => 'habitus met details',
	
			'Elytrigia juncea x repens 3.jpg' => 'foto'
	
		),
	
		'Elytrigia repens' => array(
	
			'Elytrigia repens.jpg' => 'overzicht',
	
			'Elytrigia repens 2.jpg' => 'vergroot',
	
			'Elytrigia repens 3.jpg' => 'foto',
	
			'Elytrigia repens a.jpg' => 'habitus met details',
	
			'Elytrigia juncea x repens 3.jpg' => 'foto'
	
		),
	
		'Elytrigia atherica' => array(
	
			'Elytrigia atherica.jpg' => 'overzicht',
	
			'Elytrigia atherica 3.jpg' => 'foto',
	
			'Elytrigia atherica 4.jpg' => 'foto',
	
			'Elytrigia atherica a.jpg' => 'habitus met details'
	
		),
	
		'Elytrigia maritima' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Leymus arenarius' => array(
	
			'Leymus arenarius.jpg' => 'overzicht',
	
			'Leymus arenarius 2.jpg' => 'vergroot',
	
			'Leymus arenarius 3.jpg' => 'foto',
	
			'Leymus arenarius 4.jpg' => 'foto',
	
			'Leymus arenarius a.jpg' => 'habitus met details'
	
		),
	
		'Hordelymus europaeus' => array(
	
			'Hordelymus europaeus.jpg' => 'overzicht',
	
			'Hordelymus europaeus 2.jpg' => 'vergroot',
	
			'Hordelymus europaeus 3.jpg' => 'foto',
	
			'Hordelymus europaeus 4.jpg' => 'foto',
	
			'Hordelymus europaeus a.jpg' => 'habitus met details'
	
		),
	
		'Hordeum distichon' => array(
	
			'Hordeum distichon.jpg' => 'overzicht',
	
			'Hordeum distichon 2.jpg' => 'vergroot',
	
			'Hordeum distichon 3.jpg' => 'foto',
	
			'Hordeum distichon 4.jpg' => 'foto',
	
			'Hordeum distichon 5.jpg' => 'foto',
	
			'Hordeum distichon a.jpg' => 'habitus met details'
	
		),
	
		'Hordeum vulgare' => array(
	
			'Hordeum vulgare.jpg' => 'overzicht',
	
			'Hordeum vulgare 2.jpg' => 'vergroot',
	
			'Hordeum vulgare 3.jpg' => 'foto',
	
			'Hordeum vulgare a.jpg' => 'habitus met details'
	
		),
	
		'Hordeum murinum' => array(
	
			'Hordeum murinum.jpg' => 'overzicht',
	
			'Hordeum murinum 2.jpg' => 'vergroot',
	
			'Hordeum murinum 3.jpg' => 'foto',
	
			'Hordeum murinum 4.jpg' => 'foto',
	
			'Hordeum murinum a.jpg' => 'habitus met details'
	
		),
	
		'Hordeum marinum' => array(
	
			'Hordeum marinum.jpg' => 'overzicht',
	
			'Hordeum marinum 2.jpg' => 'vergroot',
	
			'Hordeum marinum 3.jpg' => 'foto',
	
			'Hordeum marinum a.jpg' => 'habitus met details'
	
		),
	
		'Hordeum secalinum' => array(
	
			'Hordeum secalinum.jpg' => 'overzicht',
	
			'Hordeum secalinum 2.jpg' => 'vergroot',
	
			'Hordeum secalinum 3.jpg' => 'foto',
	
			'Hordeum secalinum 4.jpg' => 'foto',
	
			'Hordeum secalinum a.jpg' => 'habitus met details'
	
		),
	
		'Hordeum jubatum' => array(
	
			'Hordeum jubatum.jpg' => 'overzicht foto',
	
			'Hordeum jubatum 2.jpg' => 'bloeiwijze, aartje en tongetje',
	
			'Hordeum jubatum 3.jpg' => 'foto'
	
		),
	
		'Secale cereale' => array(
	
			'Secale cereale.jpg' => 'overzicht',
	
			'Secale cereale 2.jpg' => 'vergroot',
	
			'Secale cereale 3.jpg' => 'foto, bloeiwijze',
	
			'Secale cereale 4.jpg' => 'foto, bloeiwijzen',
	
			'Secale cereale a.jpg' => 'habitus met details'
	
		),
	
		'Triticum aestivum' => array(
	
			'Triticum aestivum.jpg' => 'overzicht',
	
			'Triticum aestivum 2.jpg' => 'vergroot',
	
			'Triticum aestivum 3.jpg' => 'foto, bloeiwijzen in vrucht',
	
			'Triticum aestivum 4.jpg' => 'foto, bloeiwijzen in vrucht',
	
			'Triticum aestivum a.jpg' => 'habitus met details'
	
		),
	
		'Triticum spelta' => array(
	
			'Triticum spelta.jpg' => 'overzicht',
	
			'Triticum spelta 2.jpg' => 'vergroot',
	
			'Triticum spelta 3.jpg' => 'foto, bloeiwijzen in vrucht'
	
		),
	
		'Triticosecale(x) spec.' => array(
	
			'Triticosecale(X) rumpai.jpg' => 'overzicht',
	
			'Triticosecale(X) rumpai 2.jpg' => 'foto, bloeiwijzen in vrucht',
	
			'Triticosecale(X) rumpai 3.jpg' => 'foto, detail bloeiwijze met vruchten'
	
		),
	
		'Danthonia decumbens' => array(
	
			'Danthonia decumbens.jpg' => 'overzicht',
	
			'Danthonia decumbens 2.jpg' => 'vergroot',
	
			'Danthonia decumbens 3.jpg' => 'foto',
	
			'Danthonia decumbens 4.jpg' => 'foto',
	
			'Danthonia decumbens a.jpg' => 'habitus met details'
	
		),
	
		'Molinia caerulea' => array(
	
			'Molinia caerulea.jpg' => 'overzicht',
	
			'Molinia caerulea 2.jpg' => 'vergroot',
	
			'Molinia caerulea 3.jpg' => 'foto',
	
			'Molinia caerulea a.jpg' => 'habitus met details'
	
		),
	
		'Phragmites australis' => array(
	
			'Phragmites australis.jpg' => 'overzicht',
	
			'Phragmites australis 2.jpg' => 'vergroot',
	
			'Phragmites australis 3.jpg' => 'foto',
	
			'Phragmites australis 4.jpg' => 'foto',
	
			'Phragmites australis 5.jpg' => 'foto',
	
			'Phragmites australis a.jpg' => 'habitus met details',
	
			'Phragmites australis.mov' => 'zicht rondom bloeiwijze: klik op foto en beweeg naar links of rechts'
	
		),
	
		'Eragrostis minor' => array(
	
			'Eragrostis minor.jpg' => 'overzicht',
	
			'Eragrostis minor 3.jpg' => 'foto, bloeiwijzen',
	
			'Eragrostis minor a.jpg' => 'habitus met details'
	
		),
	
		'Eragrostis tef' => array(
	
			'Eragrostis tef.jpg' => 'overzicht',
	
			'Eragrostis tef 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Eragrostis pilosa' => array(
	
			'Eragrostis pilosa.jpg' => 'overzicht',
	
			'Eragrostis pilosa 2.jpg' => 'vergroot',
	
			'Eragrostis pilosa 3.jpg' => 'foto, habitus bloeiend',
	
			'Eragrostis pilosa 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Eleusine indica' => array(
	
			'Eleusine indica .jpg' => 'overzicht',
	
			'Eleusine indica 3.jpg' => 'foto',
	
			'Eleusine indica a.jpg' => 'habitus met details'
	
		),
	
		'Cynodon dactylon' => array(
	
			'Cynodon dactylon.jpg' => 'overzicht',
	
			'Cynodon dactylon 2.jpg' => 'vergroot',
	
			'Cynodon dactylon 3.jpg' => 'foto',
	
			'Cynodon dactylon 4.jpg' => 'foto',
	
			'Cynodon dactylon a.jpg' => 'habitus met details'
	
		),
	
		'Spartina anglica' => array(
	
			'Spartina anglica.jpg' => 'overzicht',
	
			'Spartina anglica 3.jpg' => 'foto, habitus bloeiend',
	
			'Spartina anglica 4.jpg' => 'foto, bloeiwijzen',
	
			'Spartina anglica a.jpg' => 'habitus met details',
	
			'Spartina alterniflora a.jpg' => 'Zie opmerking',
	
			'Spartina townsendii(x) a.jpg' => '(= Spartina anglica x Spartina maritima) habitus met details aartjes, kafjes en vrucht'
	
		),
	
		'Spartina maritima' => array(
	
			'Spartina maritima.jpg' => 'overzicht',
	
			'Spartina maritima a.jpg' => 'habitus met details',
	
			'Spartina townsendii(x) a.jpg' => '(= Spartina anglica x Spartina maritima) habitus met details aartjes, kafjes en vrucht'
	
		),
	
		'Panicum miliaceum' => array(
	
			'Panicum miliaceum.jpg' => 'overzicht',
	
			'Panicum miliaceum 2.jpg' => 'vergroot; a = tongetje, b = aartjes',
	
			'Panicum miliaceum 3.jpg' => 'foto',
	
			'Panicum miliaceum 4.jpg' => 'foto'
	
		),
	
		'Panicum capillare' => array(
	
			'Panicum capillare.jpg' => 'overzicht',
	
			'Panicum capillare 2.jpg' => 'vergroot; a = tongetje, b = aartjes',
	
			'Panicum capillare 3.jpg' => 'foto'
	
		),
	
		'Panicum schinzii' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Panicum dichotomiflorum' => array(
	
			'Panicum dichotomiflorum.jpg' => 'overzicht foto',
	
			'Panicum dichotomiflorum 2.jpg' => 'vergroot; a = tongetje, b = aartjes',
	
			'Panicum dichotomiflorum 3.jpg' => 'foto'
	
		),
	
		'Echinochloa crus-galli' => array(
	
			'Echinochloa crus-galli.jpg' => 'overzicht',
	
			'Echinochloa crus-galli 2.jpg' => 'vergroot',
	
			'Echinochloa crus-galli 3.jpg' => 'foto',
	
			'Echinochloa crus-galli 4.jpg' => 'foto',
	
			'Echinochloa crus-galli a.jpg' => 'habitus met details'
	
		),
	
		'Echinochloa muricata' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Setaria pumila' => array(
	
			'Setaria pumila.jpg' => 'overzicht',
	
			'Setaria pumila 2.jpg' => 'vergroot',
	
			'Setaria pumila 3.jpg' => 'foto, habitus bloeiend',
	
			'Setaria pumila 4.jpg' => 'foto, bloeiwijze',
	
			'Setaria pumila a.jpg' => 'habitus met details'
	
		),
	
		'Setaria verticillata' => array(
	
			'Setaria verticillata.jpg' => 'overzicht',
	
			'Setaria verticillata 2.jpg' => 'vergroot',
	
			'Setaria verticillata 3.jpg' => 'foto, habitus bloeiend',
	
			'Setaria verticillata a.jpg' => 'habitus met details'
	
		),
	
		'Setaria italica' => array(
	
			'Setaria italica.jpg' => 'overzicht foto',
	
			'Setaria italica 3.jpg' => 'foto, habitus bloeiend',
	
			'Setaria italica 4.jpg' => 'foto, detail bloeiwijze in vrucht'
	
		),
	
		'Setaria viridis' => array(
	
			'Setaria viridis.jpg' => 'overzicht',
	
			'Setaria viridis 2.jpg' => 'vergroot',
	
			'Setaria viridis 3.jpg' => 'foto, habitus bloeiend',
	
			'Setaria viridis a.jpg' => 'habitus met details'
	
		),
	
		'Setaria faberi' => array(
	
			'Setaria faberi.jpg' => 'overzicht',
	
			'Setaria faberi 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Digitaria ischaemum' => array(
	
			'Digitaria ischaemum.jpg' => 'overzicht',
	
			'Digitaria ischaemum 2.jpg' => 'vergroot',
	
			'Digitaria ischaemum 3.jpg' => 'foto',
	
			'Digitaria ischaemum a.jpg' => 'habitus met details'
	
		),
	
		'Digitaria sanguinalis' => array(
	
			'Digitaria sanguinalis.jpg' => 'overzicht',
	
			'Digitaria sanguinalis 2.jpg' => 'vergroot',
	
			'Digitaria sanguinalis 3.jpg' => 'foto',
	
			'Digitaria sanguinalis a.jpg' => 'habitus met details'
	
		),
	
		'Sorghum bicolor' => array(
	
			'Sorghum bicolor.jpg' => 'overzicht',
	
			'Sorghum bicolor 2.jpg' => 'vergroot; a = aartje',
	
			'Sorghum bicolor 3.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Sorghum halepense' => array(
	
			'Sorghum halepense.jpg' => 'overzicht',
	
			'Sorghum halepense 2.jpg' => 'vergroot; a = aartje',
	
			'Sorghum halepense 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Zea mays' => array(
	
			'Zea mays.jpg' => 'overzicht',
	
			'Zea mays 2.jpg' => 'vergroot',
	
			'Zea mays 3.jpg' => 'foto, habitus, in bloei',
	
			'Zea mays 4.jpg' => 'foto, vrouwelijke bloeiwijzen',
	
			'Zea mays 5.jpg' => 'foto, habitus met bovenin mannelijke bloeiwijzen en onderin vrouwelijke bloeiwijzen'
	
		),
	
		'Tradescantia virginiana' => array(
	
			'Tradescantia virginiana.jpg' => 'overzicht',
	
			'Tradescantia virginiana 2.jpg' => 'tekening',
	
			'Tradescantia virginiana 3.jpg' => 'foto, in bloei'
	
		),
	
		'Pontederia cordata' => array(
	
			'Pontederia cordata.jpg' => 'overzicht foto',
	
			'Pontederia cordata 2.jpg' => 'tekening',
	
			'Pontederia cordata 3.jpg' => 'foto',
	
			'Pontederia cordata 4.jpg' => 'foto',
	
			'Pontederia cordata 5.jpg' => 'foto'
	
		),
	
		'Ceratophyllum submersum' => array(
	
			'Ceratophyllum submersum.jpg' => 'overzicht; a = blad, b = vrucht',
	
			'Ceratophyllum submersum 2.jpg' => 'vergroot'
	
		),
	
		'Ceratophyllum demersum' => array(
	
			'Ceratophyllum demersum.jpg' => 'overzicht;  a = blad, b = vrucht',
	
			'Ceratophyllum demersum 2.jpg' => 'vergroot',
	
			'Ceratophyllum demersum 3.jpg' => 'foto'
	
		),
	
		'Aristolochia clematitis' => array(
	
			'Aristolochia clematitis.jpg' => 'overzicht; a = l.dsn. bloem, b = dws.dsn. vruchtbeginsel',
	
			'Aristolochia clematitis 2.jpg' => 'vergroot',
	
			'Aristolochia clematitis 3.jpg' => 'foto, bloeiwijze',
	
			'Aristolochia clematitis 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Epimedium alpinum' => array(
	
			'Epimedium alpinum.jpg' => 'overzicht foto',
	
			'Epimedium alpinum 3.jpg' => 'foto',
	
			'Epimedium alpinum 4.jpg' => 'foto'
	
		),
	
		'Berberis aquifolium' => array(
	
			'Berberis aquifolium.jpg' => 'overzicht foto',
	
			'Berberis aquifolium 3.jpg' => 'foto',
	
			'Berberis aquifolium 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Berberis thunbergii' => array(
	
			'Berberis thunbergii.jpg' => 'overzicht',
	
			'Berberis thunbergii 2.jpg' => 'foto, bloeiend'
	
		),
	
		'Berberis vulgaris' => array(
	
			'Berberis vulgaris.jpg' => 'overzicht',
	
			'Berberis vulgaris 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem',
	
			'Berberis vulgaris 3.jpg' => 'foto',
	
			'Berberis vulgaris 4.jpg' => 'foto'
	
		),
	
		'Berberis aggregata' => array(
	
			'Berberis aggregata.jpg' => 'overzicht',
	
			'Berberis aggregata 2.jpg' => 'foto, bloeiend',
	
			'Berberis aggregata 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Helleborus foetidus' => array(
	
			'Helleborus foetidus.jpg' => 'overzicht',
	
			'Helleborus foetidus 2.jpg' => 'vergroot',
	
			'Helleborus foetidus 3.jpg' => 'foto',
	
			'Helleborus foetidus 4.jpg' => 'foto',
	
			'Helleborus foetidus 5.jpg' => 'foto, habitus, in bloei'
	
		),
	
		'Helleborus viridis' => array(
	
			'Helleborus viridis.jpg' => 'overzicht',
	
			'Helleborus viridis 2.jpg' => 'vergroot',
	
			'Helleborus viridis 3.jpg' => 'foto',
	
			'Helleborus viridis 4.jpg' => 'foto',
	
			'Helleborus viridis 5.jpg' => 'foto, habitus, in bloei',
	
			'Helleborus viridis 6.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Eranthis hyemalis' => array(
	
			'Eranthis hyemalis.jpg' => 'overzicht',
	
			'Eranthis hyemalis 2.jpg' => 'vergroot',
	
			'Eranthis hyemalis 3.jpg' => 'foto, bloeiend',
	
			'Eranthis hyemalis 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Nigella damascena' => array(
	
			'Nigella damascena.jpg' => 'overzicht',
	
			'Nigella damascena 2.jpg' => 'vergroot',
	
			'Nigella damascena 3.jpg' => 'foto',
	
			'Nigella damascena 4.jpg' => 'foto',
	
			'Nigella damascena 5.jpg' => 'foto'
	
		),
	
		'Actaea spicata' => array(
	
			'Actaea spicata.jpg' => 'overzicht; a = bloemen; b = vruchten',
	
			'Actaea spicata 2.jpg' => 'vergroot',
	
			'Actaea spicata 3.jpg' => 'foto, bloeiend',
	
			'Actaea spicata 4.jpg' => 'foto, in vrucht',
	
			'Actaea spicata 5.jpg' => 'foto, bloeiend'
	
		),
	
		'Caltha palustris' => array(
	
			'Caltha palustris.jpg' => 'overzicht',
	
			'Caltha palustris palustris 2.jpg' => 'vergroot'
	
		),
	
		'Caltha palustris subsp. palustris' => array(
	
			'Caltha palustris palustris.jpg' => 'overzicht; a = vruchten',
	
			'Caltha palustris palustris 2.jpg' => 'vergroot',
	
			'Caltha palustris palustris 3.jpg' => 'foto',
	
			'Caltha palustris palustris 4.jpg' => 'foto'
	
		),
	
		'Caltha palustris subsp. araneosa' => array(
	
			'Caltha palustris araneosa.jpg' => 'overzicht',
	
			'Caltha palustris araneosa 2.jpg' => 'detail; bijwortels',
	
			'Caltha palustris araneosa 3.jpg' => 'foto'
	
		),
	
		'Aconitum vulparia' => array(
	
			'Aconitum vulparia.jpg' => 'overzicht',
	
			'Aconitum vulparia 2.jpg' => 'vergroot',
	
			'Aconitum vulparia 3.jpg' => 'foto, habitus bloeiend',
	
			'Aconitum vulparia 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Consolida regalis' => array(
	
			'Consolida regalis.jpg' => 'overzicht',
	
			'Consolida regalis 2.jpg' => 'vergroot; a = bloem, de voorste bloemdekbladen verwijderd, b = kokervruchten',
	
			'Consolida regalis 3.jpg' => 'foto'
	
		),
	
		'Consolida ajacis' => array(
	
			'Consolida ajacis.jpg' => 'overzicht foto',
	
			'Consolida ajacis 3.jpg' => 'foto'
	
		),
	
		'Consolida hispanica' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Anemone apennina' => array(
	
			'Anemone apennina.jpg' => 'overzicht foto',
	
			'Anemone apennina 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Anemone blanda' => array(
	
			'Anemone blanda.jpg' => 'overzicht',
	
			'Anemone blanda 2.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Anemone nemorosa' => array(
	
			'Anemone nemorosa.jpg' => 'overzicht',
	
			'Anemone nemorosa 2.jpg' => 'vergroot; a = bloem van achteren, b = dws.dsn. bloem, c = vruchthoofdje',
	
			'Anemone nemorosa 3.jpg' => 'foto, bloemen',
	
			'Anemone nemorosa 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Anemone ranunculoides' => array(
	
			'Anemone ranunculoides.jpg' => 'overzicht',
	
			'Anemone ranunculoides 2.jpg' => 'vergroot',
	
			'Anemone ranunculoides 3.jpg' => 'foto, bloeiend',
	
			'Anemone ranunculoides 4.jpg' => 'foto, bloeiend',
	
			'Anemone ranunculoides 5.jpg' => 'foto, bloeiend'
	
		),
	
		'Pulsatilla vulgaris' => array(
	
			'Pulsatilla vulgaris.jpg' => 'overzicht; a = dws.dsn. bloem, b = vruchthoofdje, c = vruchtje',
	
			'Pulsatilla vulgaris 2.jpg' => 'vergroot',
	
			'Pulsatilla vulgaris 3.jpg' => 'foto',
	
			'Pulsatilla vulgaris 4.jpg' => 'foto',
	
			'Pulsatilla vulgaris 5.jpg' => 'foto',
	
			'Pulsatilla vulgaris 6.jpg' => 'foto'
	
		),
	
		'Clematis vitalba' => array(
	
			'Clematis vitalba.jpg' => 'overzicht; a = vruchtje',
	
			'Clematis vitalba 2.jpg' => 'vergroot',
	
			'Clematis vitalba 3.jpg' => 'foto'
	
		),
	
		'Clematis viticella' => array(
	
			'Clematis viticella.jpg' => 'overzicht; a = vruchthoofdje',
	
			'Clematis viticella 2.jpg' => 'vergroot',
	
			'Clematis viticella 3.jpg' => 'foto',
	
			'Clematis viticella 4.jpg' => 'foto'
	
		),
	
		'Adonis flammea' => array(
	
			'Adonis flammea.jpg' => 'overzicht; a = vruchthoofdje, b = vruchtje',
	
			'Adonis flammea 2.jpg' => 'vergroot',
	
			'Adonis flammea 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Adonis annua' => array(
	
			'Adonis annua.jpg' => 'overzicht; a = vruchtje',
	
			'Adonis annua 2.jpg' => 'vergroot',
	
			'Adonis annua 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Adonis aestivalis' => array(
	
			'Adonis aestivalis.jpg' => 'overzicht',
	
			'Adonis aestivalis 2.jpg' => 'vergroot; a = bloemkleurvarianten, b = vruchthoofdje,  c = vruchtje',
	
			'Adonis aestivalis 3.jpg' => 'foto, bloem',
	
			'Adonis aestivalis 4.jpg' => 'foto, bloem'
	
		),
	
		'Ficaria verna' => array(
	
			'Ficaria verna.jpg' => 'overzicht',
	
			'Ficaria verna 3.jpg' => 'foto',
	
			'Ficaria verna 4.jpg' => 'foto'
	
		),
	
		'Ficaria verna subsp. verna' => array(
	
			'Ficaria verna verna.jpg' => 'overzicht; a = onderaanzicht bloem',
	
			'Ficaria verna verna 2.jpg' => 'vergroot; a = onderaanzicht bloem'
	
		),
	
		'Ficaria verna subsp. grandiflora' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Ranunculus lingua' => array(
	
			'Ranunculus lingua.jpg' => 'overzicht',
	
			'Ranunculus lingua 2.jpg' => 'vergroot; a = bloem, b = dsn. vruchthoofdje, c = vruchtje',
	
			'Ranunculus lingua 3.jpg' => 'foto, in bloei',
	
			'Ranunculus lingua 4.jpg' => 'foto, in bloei'
	
		),
	
		'Ranunculus flammula' => array(
	
			'Ranunculus flammula.jpg' => 'overzicht; a = vruchthoofdje, b = vruchtje',
	
			'Ranunculus flammula 2.jpg' => 'vergroot',
	
			'Ranunculus flammula 3.jpg' => 'foto, in bloei',
	
			'Ranunculus flammula 4.jpg' => 'foto, in bloei'
	
		),
	
		'Ranunculus arvensis' => array(
	
			'Ranunculus arvensis.jpg' => 'overzicht; a = vruchthoofdje, b = vruchtje',
	
			'Ranunculus arvensis 2.jpg' => 'vergroot',
	
			'Ranunculus arvensis 3.jpg' => 'foto, in bloei en vrucht',
	
			'Ranunculus arvensis 4.jpg' => 'foto, in bloei en vrucht'
	
		),
	
		'Ranunculus sceleratus' => array(
	
			'Ranunculus sceleratus.jpg' => 'overzicht',
	
			'Ranunculus sceleratus 2.jpg' => 'vergroot; a = vruchthoofdje, b = vruchtje',
	
			'Ranunculus sceleratus 3.jpg' => 'foto, bloeiend',
	
			'Ranunculus sceleratus 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Ranunculus bulbosus' => array(
	
			'Ranunculus bulbosus.jpg' => 'overzicht; a = vruchthoofdje, b = vruchtje',
	
			'Ranunculus bulbosus 2.jpg' => 'vergroot',
	
			'Ranunculus bulbosus 3.jpg' => 'foto, bloeiend',
	
			'Ranunculus bulbosus 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Ranunculus sardous' => array(
	
			'Ranunculus sardous.jpg' => 'overzicht',
	
			'Ranunculus sardous 2.jpg' => 'vergroot; a = bloem van achteren, b = vruchtje',
	
			'Ranunculus sardous 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Ranunculus repens' => array(
	
			'Ranunculus repens.jpg' => 'overzicht; a = vruchthoofdje',
	
			'Ranunculus repens 2.jpg' => 'vergroot',
	
			'Ranunculus repens 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Ranunculus polyanthemos' => array(
	
			'Ranunculus polyanthemos pol.jpg' => 'overzicht, foto',
	
			'Ranunculus polyanthemos pol3.jpg' => 'foto, subsp. polyanthemos',
	
			'Ranunculus polyanthemos nem2.jpg' => 'vergroot, subsp. nemorosus'
	
		),
	
		'Ranunculus polyanthemos subsp. nemorosus' => array(
	
			'Ranunculus polyanthemos nem.jpg' => 'overzicht; a = vruchthoofdje, b = vruchtje',
	
			'Ranunculus polyanthemos nem2.jpg' => 'vergroot'
	
		),
	
		'Ranunculus polyanthemos subsp. polyanthemoides' => array(
	
			'Ranunculus polyanthemos pol.jpg' => 'overzicht, foto',
	
			'Ranunculus polyanthemos pol3.jpg' => 'foto, bloeiend'
	
		),
	
		'Ranunculus auricomus' => array(
	
			'Ranunculus auricomus.jpg' => 'overzicht; a = vruchthoofdje',
	
			'Ranunculus auricomus 2.jpg' => 'vergroot',
	
			'Ranunculus auricomus 3.jpg' => 'foto, bloeiend',
	
			'Ranunculus auricomus 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Ranunculus acris' => array(
	
			'Ranunculus acris.jpg' => 'overzicht',
	
			'Ranunculus acris 2.jpg' => 'vergroot; a = dsn. bloem, b = vruchthoofdje, c = vruchtje',
	
			'Ranunculus acris 3.jpg' => 'foto, bloem en vruchthoofdje'
	
		),
	
		'Ranunculus hederaceus' => array(
	
			'Ranunculus hederaceus.jpg' => 'overzicht; a = bloem, b = vruchtje',
	
			'Ranunculus hederaceus 2.jpg' => 'vergroot',
	
			'Ranunculus hederaceus 3.jpg' => 'foto, in bloei',
	
			'Ranunculus hederaceus 4.jpg' => 'foto, in bloei'
	
		),
	
		'Ranunculus omiophyllus' => array(
	
			'Ranunculus omiophyllus.jpg' => 'overzicht foto',
	
			'Ranunculus omiophyllus 3.jpg' => 'foto, in bloei',
	
			'Ranunculus omiophyllus 4.jpg' => 'foto, in bloei'
	
		),
	
		'Ranunculus circinatus' => array(
	
			'Ranunculus circinatus.jpg' => 'overzicht; a = vruchtje',
	
			'Ranunculus circinatus 2.jpg' => 'vergroot',
	
			'Ranunculus circinatus 3.jpg' => 'foto, in bloei'
	
		),
	
		'Ranunculus fluitans' => array(
	
			'Ranunculus fluitans.jpg' => 'overzicht; a = bloem van onderen',
	
			'Ranunculus fluitans 2.jpg' => 'vergroot',
	
			'Ranunculus fluitans 3.jpg' => 'foto, in bloei'
	
		),
	
		'Ranunculus tripartitus' => array(
	
			'Ranunculus tripartitus.jpg' => 'overzicht foto',
	
			'Ranunculus tripartitus 3.jpg' => 'foto, bloeiend',
	
			'Ranunculus tripartitus 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Ranunculus ololeucos' => array(
	
			'Ranunculus ololeucos.jpg' => 'overzicht foto',
	
			'Ranunculus ololeucos 3.jpg' => 'foto, in bloei'
	
		),
	
		'Ranunculus baudotii' => array(
	
			'Ranunculus baudotii.jpg' => 'overzicht foto',
	
			'Ranunculus baudotii 3.jpg' => 'foto, bloeiend',
	
			'Ranunculus baudotii 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Ranunculus peltatus' => array(
	
			'Ranunculus peltatus.jpg' => 'overzicht foto',
	
			'Ranunculus peltatus 2.jpg' => 'vergroot; a = nootje',
	
			'Ranunculus peltatus 3.jpg' => 'foto, bloeiend',
	
			'Ranunculus peltatus 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Ranunculus peltatus var. peltatus' => array(
	
			'Ranunculus peltatus.jpg' => 'overzicht foto',
	
			'Ranunculus peltatus 2.jpg' => 'vergroot; a = nootje'
	
		),
	
		'Ranunculus peltatus var. heterophyllus' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Ranunculus aquatilis' => array(
	
			'Ranunculus aquatilis.jpg' => 'overzicht; a = uit het water gehaald takje, b = vrucht',
	
			'Ranunculus aquatilis 2.jpg' => 'vergroot',
	
			'Ranunculus aquatilis 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Ranunculus aquatilis var. aquatilis' => array(
	
			'Ranunculus aquatilis aquatil.jpg' => 'overzicht',
	
			'Ranunculus aquatilis aquat 2.jpg' => 'vergroot'
	
		),
	
		'Ranunculus aquatilis var. diffusus' => array(
	
			'Ranunculus aquatilis diffus.jpg' => 'overzicht',
	
			'Ranunculus aquatilis diffu 2.jpg' => 'vergroot'
	
		),
	
		'Myosurus minimus' => array(
	
			'Myosurus minimus.jpg' => 'overzicht; a = bloem, b = bloembodem met rijpe vruchtjes',
	
			'Myosurus minimus 2.jpg' => 'vergroot',
	
			'Myosurus minimus 3.jpg' => 'foto'
	
		),
	
		'Aquilegia vulgaris' => array(
	
			'Aquilegia vulgaris.jpg' => 'overzicht; a = bloemdekblad, b = nectarium, c = kokervruchten',
	
			'Aquilegia vulgaris 2.jpg' => 'vergroot',
	
			'Aquilegia vulgaris 3.jpg' => 'foto, habitus, in bloei',
	
			'Aquilegia vulgaris 4.jpg' => 'foto, in bloei',
	
			'Aquilegia vulgaris 5.jpg' => 'foto, in bloei'
	
		),
	
		'Thalictrum aquilegiifolium' => array(
	
			'Thalictrum aquilegifolium.jpg' => 'overzicht; a = bloem',
	
			'Thalictrum aquilegifolium 2.jpg' => 'vergroot',
	
			'Thalictrum aquilegifolium 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Thalictrum flavum' => array(
	
			'Thalictrum flavum.jpg' => 'overzicht; a = vruchthoofdje',
	
			'Thalictrum flavum 2.jpg' => 'vergroot',
	
			'Thalictrum flavum 3.jpg' => 'foto, habitus',
	
			'Thalictrum flavum 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Thalictrum minus' => array(
	
			'Thalictrum minus.jpg' => 'overzicht; a = bloem, b = vruchthoofdje',
	
			'Thalictrum minus 2.jpg' => 'vergroot'
	
		),
	
		'Papaver atlanticum' => array(
	
			'Papaver atlanticum.jpg' => 'overzicht',
	
			'Papaver atlanticum 2.jpg' => 'foto'
	
		),
	
		'Papaver orientale' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Papaver somniferum' => array(
	
			'Papaver somniferum.jpg' => 'overzicht',
	
			'Papaver somniferum 2.jpg' => 'vergroot',
	
			'Papaver somniferum 3.jpg' => 'foto',
	
			'Papaver somniferum 4.jpg' => 'foto',
	
			'Papaver somniferum 5.jpg' => 'foto'
	
		),
	
		'Papaver argemone' => array(
	
			'Papaver argemone.jpg' => 'overzicht; a = meeldraad, b = doosvrucht',
	
			'Papaver argemone 2.jpg' => 'vergroot',
	
			'Papaver argemone 3.jpg' => 'foto'
	
		),
	
		'Papaver rhoeas' => array(
	
			'Papaver rhoeas.jpg' => 'overzicht;  a = rijpende doosvrucht, b = rijpe doosvrucht',
	
			'Papaver rhoeas 2.jpg' => 'vergroot',
	
			'Papaver rhoeas 3.jpg' => 'foto',
	
			'Papaver rhoeas 4.jpg' => 'foto'
	
		),
	
		'Papaver dubium' => array(
	
			'Papaver dubium.jpg' => 'overzicht; a = doosvrucht',
	
			'Papaver dubium 2.jpg' => 'vergroot',
	
			'Papaver dubium 3.jpg' => 'foto',
	
			'Papaver dubium 4.jpg' => 'foto'
	
		),
	
		'Meconopsis cambrica' => array(
	
			'Meconopsis cambrica.jpg' => 'overzicht',
	
			'Meconopsis cambrica 2.jpg' => 'foto, habitus bloeiend',
	
			'Meconopsis cambrica 3.jpg' => 'foto, bloem',
	
			'Meconopsis cambrica 4.jpg' => 'foto, bloeiend',
	
			'Meconopsis cambrica 5.jpg' => 'foto, bloem'
	
		),
	
		'Glaucium flavum' => array(
	
			'Glaucium flavum.jpg' => 'overzicht',
	
			'Glaucium flavum 2.jpg' => 'vergroot',
	
			'Glaucium flavum 3.jpg' => 'foto',
	
			'Glaucium flavum 4.jpg' => 'foto',
	
			'Glaucium flavum 5.jpg' => 'foto'
	
		),
	
		'Chelidonium majus' => array(
	
			'Chelidonium majus.jpg' => 'overzicht',
	
			'Chelidonium majus 2.jpg' => 'vergroot; a = bloem zonder bloembekleedsels, b = meeldraad, c = doosvrucht, d = zaad',
	
			'Chelidonium majus 3.jpg' => 'foto'
	
		),
	
		'Eschscholzia californica' => array(
	
			'Eschscholzia californica.jpg' => 'overzicht foto',
	
			'Eschscholzia californica 3.jpg' => 'foto'
	
		),
	
		'Corydalis solida' => array(
	
			'Corydalis solida.jpg' => 'overzicht',
	
			'Corydalis solida 2.jpg' => 'vergroot; a = knol, b = dws.dsn. knol, c = l.dsn. bloem, d = doosvrucht, e = zaad',
	
			'Corydalis solida 3.jpg' => 'foto',
	
			'Corydalis solida 4.jpg' => 'foto'
	
		),
	
		'Corydalis cava' => array(
	
			'Corydalis cava.jpg' => 'overzicht;  a = knol, b = dws.dsn. knol',
	
			'Corydalis cava 2.jpg' => 'vergroot; a = knol, b = dws.dsn. knol, c = l.dsn. bloem, d = vergroeide meeldraden, e = doosvrucht, f = zaad',
	
			'Corydalis cava 3.jpg' => 'foto',
	
			'Corydalis cava 4.jpg' => 'foto'
	
		),
	
		'Pseudofumaria lutea' => array(
	
			'Pseudofumaria lutea.jpg' => 'overzicht',
	
			'Pseudofumaria lutea 2.jpg' => 'vergroot;  a = kelkblad, b = kroonbladen, c = doosvrucht, d = zaad',
	
			'Pseudofumaria lutea 3.jpg' => 'foto, in bloei',
	
			'Pseudofumaria lutea 4.jpg' => 'foto, in bloei',
	
			'Pseudofumaria lutea 5.jpg' => 'foto, in bloei'
	
		),
	
		'Pseudofumaria alba' => array(
	
			'Pseudofumaria alba.jpg' => 'overzicht',
	
			'Pseudofumaria alba 2.jpg' => 'vergroot; a = kelkblad, b = gespoord kroonblad, c = zaad',
	
			'Pseudofumaria alba 3.jpg' => 'foto',
	
			'Pseudofumaria alba 4.jpg' => 'foto'
	
		),
	
		'Ceratocapnos claviculata' => array(
	
			'Ceratocapnos claviculata.jpg' => 'overzicht; a = bloem',
	
			'Ceratocapnos claviculata 2.jpg' => 'vergroot',
	
			'Ceratocapnos claviculata 3.jpg' => 'foto',
	
			'Ceratocapnos claviculata 4.jpg' => 'foto'
	
		),
	
		'Fumaria officinalis' => array(
	
			'Fumaria officinalis.jpg' => 'overzicht',
	
			'Fumaria officinalis 2.jpg' => 'vergroot; a = bloem, b = l.dsn. bloem, 3 = vrucht',
	
			'Fumaria officinalis 3.jpg' => 'foto'
	
		),
	
		'Fumaria muralis' => array(
	
			'Fumaria muralis.jpg' => 'overzicht foto',
	
			'Fumaria muralis 3.jpg' => 'foto'
	
		),
	
		'Fumaria capreolata' => array(
	
			'Fumaria capreolata.jpg' => 'overzicht',
	
			'Fumaria capreolata 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Fumaria capreolata 3.jpg' => 'foto',
	
			'Fumaria capreolata 4.jpg' => 'foto'
	
		),
	
		'Platanus hispanica' => array(
	
			'Platanus hispanica.jpg' => 'overzicht',
	
			'Platanus hispanica 2.jpg' => 'vergroot',
	
			'Platanus hispanica 3.jpg' => 'foto',
	
			'Platanus hispanica 4.jpg' => 'foto, boom',
	
			'Platanus hispanica 5.jpg' => 'foto, bast'
	
		),
	
		'Platanus orientalis' => array(
	
			'Platanus orientalis.jpg' => 'overzicht',
	
			'Platanus orientalis 2.jpg' => 'vergroot',
	
			'Platanus orientalis 3.jpg' => 'foto, blad en vruchten',
	
			'Platanus orientalis 4.jpg' => 'foto, bast'
	
		),
	
		'Buxus sempervirens' => array(
	
			'Buxus sempervirens.jpg' => 'overzicht; a = bloem, b = vrucht',
	
			'Buxus sempervirens 2.jpg' => 'vergroot',
	
			'Buxus sempervirens 3.jpg' => 'foto',
	
			'Buxus sempervirens 4.jpg' => 'foto'
	
		),
	
		'Pachysandra terminalis' => array(
	
			'Pachysandra terminalis.jpg' => 'overzicht',
	
			'Pachysandra terminalis 2.jpg' => 'tekening ',
	
			'Pachysandra terminalis 3.jpg' => 'foto',
	
			'Pachysandra terminalis 4.jpg' => 'foto'
	
		),
	
		'Drosera intermedia' => array(
	
			'Drosera intermedia.jpg' => 'overzicht',
	
			'Drosera intermedia 2.jpg' => 'vergroot; a = stamper + meeldraad',
	
			'Drosera intermedia 3.jpg' => 'foto',
	
			'Drosera intermedia 4.jpg' => 'foto',
	
			'Drosera intermedia 4.jpg' => 'foto'
	
		),
	
		'Drosera rotundifolia' => array(
	
			'Drosera rotundifolia.jpg' => 'overzicht; a = dsn. bloem',
	
			'Drosera rotundifolia 2.jpg' => 'vergroot',
	
			'Drosera rotundifolia 3.jpg' => 'foto',
	
			'Drosera rotundifolia 4.jpg' => 'foto',
	
			'Drosera rotundifolia 5.jpg' => 'foto'
	
		),
	
		'Drosera anglica' => array(
	
			'Drosera anglica.jpg' => 'overzicht',
	
			'Drosera anglica 2.jpg' => 'vergroot',
	
			'Drosera anglica 3.jpg' => 'foto',
	
			'Drosera anglica 4.jpg' => 'foto'
	
		),
	
		'Tamarix gallica' => array(
	
			'Tamarix gallica.jpg' => 'overzicht foto',
	
			'Tamarix gallica 2.jpg' => 'tekening ',
	
			'Tamarix gallica 3.jpg' => 'foto, habitus',
	
			'Tamarix gallica 4.jpg' => 'foto, bloeiend',
	
			'Tamarix gallica 5.jpg' => 'foto, bloeiend'
	
		),
	
		'Armeria maritima' => array(
	
			'Armeria maritima.jpg' => 'overzicht',
	
			'Armeria maritima 2.jpg' => 'vergroot',
	
			'Armeria maritima 3.jpg' => 'foto, habitus bloeiend',
	
			'Armeria maritima 4.jpg' => 'foto, bloemhoofdjes'
	
		),
	
		'Limonium vulgare' => array(
	
			'Limonium vulgare.jpg' => 'overzicht',
	
			'Limonium vulgare 2.jpg' => 'vergroot',
	
			'Limonium vulgare 3.jpg' => 'foto',
	
			'Limonium vulgare 4.jpg' => 'foto'
	
		),
	
		'Limonium humile' => array(
	
			'Limonium humile.jpg' => 'overzicht',
	
			'Limonium humile 2.jpg' => 'foto'
	
		),
	
		'Persicaria bistorta' => array(
	
			'Persicaria bistorta.jpg' => 'overzicht; a = bloem',
	
			'Persicaria bistorta 2.jpg' => 'vergroot',
	
			'Persicaria bistorta 3.jpg' => 'foto',
	
			'Persicaria bistorta 4.jpg' => 'foto',
	
			'Persicaria bistorta a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Persicaria wallichii' => array(
	
			'Persicaria wallichii.jpg' => 'overzicht foto',
	
			'Persicaria wallichii 3.jpg' => 'foto',
	
			'Persicaria wallichii a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Persicaria capitata' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Persicaria amphibia' => array(
	
			'Persicaria amphibia.jpg' => 'overzicht; a = bloem',
	
			'Persicaria amphibia 2.jpg' => 'vergroot',
	
			'Persicaria amphibia 3.jpg' => 'foto',
	
			'Persicaria amphibia a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Persicaria pensylvanica' => array(
	
			'Persicaria pensylvanica.jpg' => 'overzicht',
	
			'Persicaria pensylvanica a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Persicaria lapathifolia' => array(
	
			'Persicaria lapathifolia.jpg' => 'overzicht',
	
			'Persicaria lapathifolia 2.jpg' => 'vergroot; a = detail bladonderkant, b = tuitje',
	
			'Persicaria lapathifolia 3.jpg' => 'foto',
	
			'Persicaria lapathifolia 4.jpg' => 'foto',
	
			'Persicaria lapathifolia 5.jpg' => 'foto',
	
			'Persicaria lapathifolia a.jpg' => 'tekening BSBI Handbook 3',
	
			'Persicaria lapathifolia b.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Persicaria maculosa' => array(
	
			'Persicaria maculosa.jpg' => 'overzicht; a = tuitje, b = bloem',
	
			'Persicaria maculosa 2.jpg' => 'vergroot',
	
			'Persicaria maculosa 3.jpg' => 'foto',
	
			'Persicaria maculosa 4.jpg' => 'foto',
	
			'Persicaria maculosa a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Persicaria hydropiper' => array(
	
			'Persicaria hydropiper.jpg' => 'overzicht; a = bloem, b = vruchtkelk',
	
			'Persicaria hydropiper 2.jpg' => 'vergroot',
	
			'Persicaria hydropiper 3.jpg' => 'foto',
	
			'Persicaria hydropiper a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Persicaria mitis' => array(
	
			'Persicaria mitis.jpg' => 'overzicht',
	
			'Persicaria mitis 2.jpg' => 'vergroot',
	
			'Persicaria mitis 3.jpg' => 'foto',
	
			'Persicaria mitis a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Persicaria minor' => array(
	
			'Persicaria minor.jpg' => 'overzicht; a = bloem',
	
			'Persicaria minor 2.jpg' => 'vergroot',
	
			'Persicaria minor 3.jpg' => 'foto',
	
			'Persicaria minor a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Fagopyrum esculentum' => array(
	
			'Fagopyrum esculentum.jpg' => 'overzicht; a = bloem, b = vrucht',
	
			'Fagopyrum esculentum 2.jpg' => 'vergroot',
	
			'Fagopyrum esculentum 3.jpg' => 'foto',
	
			'Fagopyrum esculentum a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Fagopyrum tataricum' => array(
	
			'Fagopyrum tataricum.jpg' => 'overzicht; a = vrucht',
	
			'Fagopyrum tataricum 2.jpg' => 'vergroot',
	
			'Fagopyrum tataricum a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Polygonum oxyspermum subsp. raii' => array(
	
			'Polygonum oxyspermum 2.jpg' => 'overzicht; vruchten',
	
			'Polygonum oxyspermum.jpg' => 'foto',
	
			'Polygonum oxyspermum 3.jpg' => 'foto',
	
			'Polygonum oxyspermum 4.jpg' => 'foto',
	
			'Polygonum oxyspermum 5.jpg' => 'foto',
	
			'Polygonum oxyspermum a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Polygonum aviculare' => array(
	
			'Polygonum aviculare.jpg' => 'overzicht; a = bloem, b = bloeiwijze',
	
			'Polygonum aviculare 2.jpg' => 'vergroot',
	
			'Polygonum aviculare 3.jpg' => 'foto',
	
			'Polygonum aviculare 4.jpg' => 'foto',
	
			'Polygonum aviculare a.jpg' => 'tekening BSBI Handbook 3',
	
			'Polygonum maritimum a.jpg' => 'tekening BSBI Handbook 3, zie opmerking',
	
			'Polygonum maritimum b.jpg' => 'tekening BSBI Handbook 3, zie opmerking'
	
		),
	
		'Fallopia japonica' => array(
	
			'Fallopia japonica.jpg' => 'overzicht',
	
			'Fallopia japonica 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Fallopia japonica 3.jpg' => 'foto',
	
			'Fallopia japonica a.jpg' => 'tekening BSBI Handbook 3',
	
			'Fallopia japonica 4.jpg' => 'foto'
	
		),
	
		'Fallopia bohemica(x)' => array(
	
			'Fallopia bohemica(x).jpg' => 'overzicht',
	
			'Fallopia bohemica(x) 2.jpg' => 'foto',
	
			'Fallopia bohemica(x) 3.jpg' => 'foto',
	
			'Fallopia bohemica(x) 4.jpg' => 'foto'
	
		),
	
		'Fallopia sachalinensis' => array(
	
			'Fallopia sachalinensis.jpg' => 'overzicht',
	
			'Fallopia sachalinensis 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Fallopia sachalinensis 3.jpg' => 'foto',
	
			'Fallopia sachalinensis a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Fallopia baldschuanica' => array(
	
			'Fallopia baldschuanica.jpg' => 'overzicht foto',
	
			'Fallopia baldschuanica 3.jpg' => 'foto',
	
			'Fallopia baldschuanica a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Fallopia convolvulus' => array(
	
			'Fallopia convolvulus.jpg' => 'overzicht; a = vrucht',
	
			'Fallopia convolvulus 2.jpg' => 'vergroot',
	
			'Fallopia convolvulus 3.jpg' => 'foto',
	
			'Fallopia convolvulus a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Fallopia dumetorum' => array(
	
			'Fallopia dumetorum.jpg' => 'overzicht; a = vrucht',
	
			'Fallopia dumetorum 2.jpg' => 'vergroot',
	
			'Fallopia dumetorum 3.jpg' => 'foto',
	
			'Fallopia dumetorum 4.jpg' => 'foto',
	
			'Fallopia dumetorum a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Rheum rhabarbarum(x)' => array(
	
			'Rheum rhabarbarum(x).jpg' => 'overzicht foto',
	
			'Rheum rhabarbarum(x) 3.jpg' => 'foto, habitus',
	
			'Rheum rhabarbarum(x) 4.jpg' => 'foto, vruchtjes',
	
			'Rheum rhabarbarum(x) 5.jpg' => 'foto, jonge bloeiwijzen'
	
		),
	
		'Rumex acetosella' => array(
	
			'Rumex acetosella.jpg' => 'overzicht; a = manlijke bloem van achteren, b = vrouwelijke bloem, c = vrucht',
	
			'Rumex acetosella 2.jpg' => 'vergroot',
	
			'Rumex acetosella 3.jpg' => 'foto, habitus in vrucht',
	
			'Rumex acetosella a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Rumex scutatus' => array(
	
			'Rumex scutatus.jpg' => 'overzicht; a = vrucht',
	
			'Rumex scutatus 2.jpg' => 'vergroot',
	
			'Rumex scutatus 3.jpg' => 'foto, habitus',
	
			'Rumex scutatus 4.jpg' => 'foto, in vrucht',
	
			'Rumex scutatus a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Rumex thyrsiflorus' => array(
	
			'Rumex thyrsiflorus.jpg' => 'overzicht foto',
	
			'Rumex thyrsiflorus 2.jpg' => 'vergroot; a = stengelblad, b = vrucht',
	
			'Rumex thyrsiflorus 3.jpg' => 'foto, bloeiwijzen',
	
			'Rumex thyrsiflorus 4.jpg' => 'foto, bloeiwijzen',
	
			'Rumex rugosus 3.jpg' => 'foto, bloeiwijze, zie opmerking',
	
			'Rumex rugosus 4.jpg' => 'foto, bloeiwijze, zie opmerking'
	
		),
	
		'Rumex acetosa' => array(
	
			'Rumex acetosa.jpg' => 'overzicht; a = vrouwelijke bloem, b = manlijke bloem, c = vrucht',
	
			'Rumex acetosa 2.jpg' => 'vergroot',
	
			'Rumex acetosa 3.jpg' => 'foto, in vrucht',
	
			'Rumex acetosa a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Rumex aquaticus' => array(
	
			'Rumex aquaticus.jpg' => 'overzicht; a = vrucht',
	
			'Rumex aquaticus 2.jpg' => 'vergroot',
	
			'Rumex aquaticus 3.jpg' => 'foto, habitus',
	
			'Rumex aquaticus a.jpg' => 'tekening BSBI Handbook 3',
	
			'Rumex aquaticus b.jpg' => 'tekening BSBI Handbook 3',
	
			'Rumex aquaticus c.jpg' => 'tekening BSBI Handbook 3',
	
			'Rumex longifolius a.jpg' => 'tekening BSBI Handbook 3',
	
			'Rumex longifolius b.jpg' => 'tekening BSBI Handbook 3',
	
			'Rumex longifolius c.jpg' => 'tekening BSBI Handbook 3',
	
			'Rumex longifolius.jpg' => 'tekening vruchtklep'
	
		),
	
		'Rumex salicifolius' => array(
	
			'Rumex salicifolius.jpg' => 'overzicht',
	
			'Rumex salicifolius a.jpg' => 'tekening BSBI Handbook 3',
	
			'Rumex salicifolius b.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Rumex sanguineus' => array(
	
			'Rumex sanguineus.jpg' => 'overzicht; a = vrucht',
	
			'Rumex sanguineus 2.jpg' => 'vergroot',
	
			'Rumex sanguineus 3.jpg' => 'foto, habitus',
	
			'Rumex sanguineus a.jpg' => 'tekening BSBI Handbook 3',
	
			'Rumex sanguineus b.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Rumex conglomeratus' => array(
	
			'Rumex conglomeratus.jpg' => 'overzicht; a = vrucht',
	
			'Rumex conglomeratus 2.jpg' => 'vergroot',
	
			'Rumex conglomeratus 3.jpg' => 'foto, habitus',
	
			'Rumex conglomeratus a.jpg' => 'tekening BSBI Handbook 3',
	
			'Rumex conglomeratus b.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Rumex crispus' => array(
	
			'Rumex crispus.jpg' => 'overzicht; a = vrucht',
	
			'Rumex crispus 2.jpg' => 'vergroot',
	
			'Rumex crispus 3.jpg' => 'foto, habitus',
	
			'Rumex crispus a.jpg' => 'tekening BSBI Handbook 3',
	
			'Rumex crispus b.jpg' => 'tekening BSBI Handbook 3',
	
			'Rumex crispus c.jpg' => 'tekening BSBI Handbook 3',
	
			'Rumex patientia 2.jpg' => 'Zie opmerking bij Rumex crispus, vergroot',
	
			'Rumex patientia a.jpg' => 'tekening BSBI Handbook 3, zie opmerking',
	
			'Rumex patientia b.jpg' => 'tekening BSBI Handbook 3, zie opmerking',
	
			'Rumex patientia c.jpg' => 'tekening BSBI Handbook 3, zie opmerking'
	
		),
	
		'Rumex hydrolapathum' => array(
	
			'Rumex hydrolapathum.jpg' => 'overzicht; a = vrucht',
	
			'Rumex hydrolapathum 2.jpg' => 'vergroot',
	
			'Rumex hydrolapathum 3.jpg' => 'foto, habitus',
	
			'Rumex hydrolapathum 4.jpg' => 'foto, bloeiwijze',
	
			'Rumex hydrolapathum a.jpg' => 'tekening BSBI Handbook 3',
	
			'Rumex hydrolapathum b.jpg' => 'tekening BSBI Handbook 3',
	
			'Rumex hydrolapathum c.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Rumex pratensis(x)' => array(
	
			'Rumex pratensis(x).jpg' => 'overzicht',
	
			'Rumex pratensis(x) 2.jpg' => 'vergroot',
	
			'Rumex pratensis(x) 3.jpg' => 'foto, habitus',
	
			'Rumex pratensis(x) a.jpg' => 'tekening BSBI Handbook 3',
	
			'Rumex pratensis(x) b.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Rumex obtusifolius' => array(
	
			'Rumex obtusifolius.jpg' => 'overzicht; a = vrucht',
	
			'Rumex obtusifolius 2.jpg' => 'vergroot',
	
			'Rumex obtusifolius 3.jpg' => 'foto, habitus',
	
			'Rumex obtusifolius a.jpg' => 'tekening BSBI Handbook 3',
	
			'Rumex obtusifolius b.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Rumex palustris' => array(
	
			'Rumex palustris.jpg' => 'overzicht; a = vrucht',
	
			'Rumex palustris 2.jpg' => 'vergroot',
	
			'Rumex palustris 3.jpg' => 'foto, habitus',
	
			'Rumex palustris 4.jpg' => 'foto, bloeiwijze',
	
			'Rumex palustris a.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Rumex maritimus' => array(
	
			'Rumex maritimus.jpg' => 'overzicht; a = vrucht',
	
			'Rumex maritimus 2.jpg' => 'vergroot',
	
			'Rumex maritimus 3.jpg' => 'foto, bloeiwijze',
	
			'Rumex maritimus a.jpg' => 'tekening BSBI Handbook 3',
	
			'Rumex maritimus b.jpg' => 'tekening BSBI Handbook 3'
	
		),
	
		'Corrigiola litoralis' => array(
	
			'Corrigiola litoralis.jpg' => 'overzicht; a = blad met steunblaadjes, b = bloeiwijze, c = bloem, d = vruchtkelk',
	
			'Corrigiola litoralis 2.jpg' => 'vergroot',
	
			'Corrigiola litoralis 3.jpg' => 'foto',
	
			'Corrigiola litoralis 4.jpg' => 'foto'
	
		),
	
		'Herniaria glabra' => array(
	
			'Herniaria glabra.jpg' => 'overzicht; a = bloem van opzij, b = dws.dsn. vruchtbeginsel, c = vruchtkelk',
	
			'Herniaria glabra 2.jpg' => 'vergroot',
	
			'Herniaria glabra 3.jpg' => 'foto',
	
			'Herniaria glabra 4.jpg' => 'foto'
	
		),
	
		'Herniaria hirsuta' => array(
	
			'Herniaria hirsuta.jpg' => 'overzicht; a = blad met steunblaadje, b = bloem',
	
			'Herniaria hirsuta 2.jpg' => 'vergroot',
	
			'Herniaria hirsuta 3.jpg' => 'foto'
	
		),
	
		'Illecebrum verticillatum' => array(
	
			'Illecebrum verticillatum.jpg' => 'overzicht; a = detail bloeistengel, b = bloem, c = kelkblad + meeldraad',
	
			'Illecebrum verticillatum 2.jpg' => 'vergroot',
	
			'Illecebrum verticillatum 3.jpg' => 'foto',
	
			'Illecebrum verticillatum 4.jpg' => 'foto'
	
		),
	
		'Polycarpon tetraphyllum' => array(
	
			'Polycarpon tetraphyllum.jpg' => 'overzicht; a = bloem',
	
			'Polycarpon tetraphyllum 2.jpg' => 'vergroot',
	
			'Polycarpon tetraphyllum 3.jpg' => 'foto',
	
			'Polycarpon tetraphyllum 4.jpg' => 'foto'
	
		),
	
		'Spergula arvensis' => array(
	
			'Spergula arvensis.jpg' => 'overzicht; a = stengelknoop, b = bloem, c = zaad',
	
			'Spergula arvensis 2.jpg' => 'vergroot',
	
			'Spergula arvensis 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Spergula morisonii' => array(
	
			'Spergula morisonii.jpg' => 'overzicht; a = zaad',
	
			'Spergula morisonii 2.jpg' => 'vergroot',
	
			'Spergula morisonii 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Spergularia segetalis' => array(
	
			'Spergularia segetalis.jpg' => 'overzicht; a = bloem, b = kelk',
	
			'Spergularia segetalis 2.jpg' => 'vergroot'
	
		),
	
		'Spergularia rubra' => array(
	
			'Spergularia rubra.jpg' => 'overzicht',
	
			'Spergularia rubra 2.jpg' => 'vergroot',
	
			'Spergularia rubra 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Spergularia media' => array(
	
			'Spergularia media.jpg' => 'overzicht; a = bloem, b =  zaad',
	
			'Spergularia media 2.jpg' => 'vergroot',
	
			'Spergularia media 3.jpg' => 'foto, bloeiend',
	
			'Spergularia media 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Spergularia salina' => array(
	
			'Spergularia salina.jpg' => 'overzicht; a = bloem, b = vruchtkelk, c = zaad',
	
			'Spergularia salina 2.jpg' => 'vergroot',
	
			'Spergularia salina 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Scleranthus perennis' => array(
	
			'Scleranthus perennis.jpg' => 'overzicht foto',
	
			'Scleranthus perennis 2.jpg' => 'vergroot; vruchtkelk',
	
			'Scleranthus perennis 3.jpg' => 'foto, bloeiend',
	
			'Scleranthus perennis 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Scleranthus annuus' => array(
	
			'Scleranthus annuus.jpg' => 'overzicht',
	
			'Scleranthus annuus 2.jpg' => 'vergroot; a = bloem, b = opengemaakte bloem, c = bloem van Scleranthus polycarpos',
	
			'Scleranthus annuus 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Scleranthus annuus subsp. annuus' => array(
	
			'Scleranthus annuus annuus.jpg' => 'overzicht',
	
			'Scleranthus annuus 2.jpg' => 'vergroot; a = bloem, b = opengemaakte bloem, c = bloem van Scleranthus polycarpos',
	
			'Scleranthus annuus annuus 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Scleranthus annuus subsp. polycarpos' => array(
	
			'Scleranthus annuus polycar.jpg' => 'overzicht',
	
			'Scleranthus annuus polycar 2.jpg' => 'foto, habitus bloeiend',
	
			'Scleranthus annuus polycar 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Arenaria serpyllifolia' => array(
	
			'Arenaria serpyllifolia.jpg' => 'overzicht; a = bloem, b = doosvrucht',
	
			'Arenaria serpyllifolia 2.jpg' => 'vergroot',
	
			'Arenaria serpyllifolia 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Arenaria leptoclados' => array(
	
			'Arenaria leptoclados.jpg' => 'overzicht foto',
	
			'Arenaria leptoclados 3.jpg' => 'foto, habitus bloeiend',
	
			'Arenaria leptoclados 4.jpg' => 'foto, habitus bloeiend en in vrucht'
	
		),
	
		'Moehringia trinervia' => array(
	
			'Moehringia trinervia.jpg' => 'overzicht; a = bloem, b = doosvrucht, c = zaad',
	
			'Moehringia trinervia 2.jpg' => 'vergroot',
	
			'Moehringia trinervia 3.jpg' => 'foto'
	
		),
	
		'Honckenya peploides' => array(
	
			'Honckenya peploides.jpg' => 'overzicht',
	
			'Honckenya peploides 2.jpg' => 'vergroot; a = bloem, b = kelk en doosvrucht',
	
			'Honckenya peploides 3.jpg' => 'foto',
	
			'Honckenya peploides 4.jpg' => 'foto'
	
		),
	
		'Minuartia hybrida' => array(
	
			'Minuartia hybrida.jpg' => 'overzicht; a = bloem, b = bloem met doosvrucht',
	
			'Minuartia hybrida 2.jpg' => 'vergroot',
	
			'Minuartia hybrida 3.jpg' => 'foto',
	
			'Minuartia hybrida 4.jpg' => 'foto'
	
		),
	
		'Stellaria nemorum' => array(
	
			'Stellaria nemorum.jpg' => 'overzicht; a = kroonblad, b = vruchtbeginsel',
	
			'Stellaria nemorum 2.jpg' => 'vergroot',
	
			'Stellaria nemorum 3.jpg' => 'foto, bloeiwijzen',
	
			'Stellaria nemorum 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Stellaria neglecta' => array(
	
			'Stellaria neglecta.jpg' => 'overzicht foto',
	
			'Stellaria neglecta 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Stellaria pallida' => array(
	
			'Stellaria pallida.jpg' => 'overzicht foto',
	
			'Stellaria pallida 3.jpg' => 'foto, habitus bloeiend',
	
			'Stellaria pallida 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Stellaria media' => array(
	
			'Stellaria media.jpg' => 'overzicht; a = bloem, b = bloem zonder kelk en kroon, c = doosvrucht',
	
			'Stellaria media 2.jpg' => 'vergroot',
	
			'Stellaria media 3.jpg' => 'foto, bloeiwijze',
	
			'Stellaria media 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Stellaria uliginosa' => array(
	
			'Stellaria uliginosa.jpg' => 'overzicht; a = bloem, b = vruchtkelk met jonge doosvrucht',
	
			'Stellaria uliginosa 2.jpg' => 'vergroot',
	
			'Stellaria uliginosa 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Stellaria holostea' => array(
	
			'Stellaria holostea.jpg' => 'overzicht; a = kroonblad, b = vruchtbeginsel, c = doosvrucht',
	
			'Stellaria holostea 2.jpg' => 'vergroot',
	
			'Stellaria holostea 3.jpg' => 'foto, habitus bloeiend',
	
			'Stellaria holostea 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Stellaria graminea' => array(
	
			'Stellaria graminea.jpg' => 'overzicht; a = kroonblad, b = kelk met jonge vrucht, c = doosvrucht',
	
			'Stellaria graminea 2.jpg' => 'vergroot',
	
			'Stellaria graminea 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Stellaria palustris' => array(
	
			'Stellaria palustris.jpg' => 'overzicht; a = doosvrucht',
	
			'Stellaria palustris 2.jpg' => 'vergroot',
	
			'Stellaria palustris 3.jpg' => 'foto, bloeiend',
	
			'Stellaria palustris 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Myosoton aquaticum' => array(
	
			'Myosoton aquatica.jpg' => 'overzicht;  a = bloem, b = doosvrucht',
	
			'Myosoton aquatica 2.jpg' => 'vergroot',
	
			'Myosoton aquatica 3.jpg' => 'foto',
	
			'Myosoton aquatica 4.jpg' => 'foto'
	
		),
	
		'Cerastium arvense' => array(
	
			'Cerastium arvense.jpg' => 'overzicht',
	
			'Cerastium arvense 2.jpg' => 'vergroot; a = bloem, b = bloem zonder kelk en kroon, c = lengte dsn. doosvrucht',
	
			'Cerastium arvense 3.jpg' => 'foto',
	
			'Cerastium arvense 4.jpg' => 'foto',
	
			'Cerastium arvense 5.jpg' => 'foto'
	
		),
	
		'Cerastium tomentosum' => array(
	
			'Cerastium tomentosum.jpg' => 'overzicht; a = kelkblad, b = doosvrucht',
	
			'Cerastium tomentosum 2.jpg' => 'vergroot',
	
			'Cerastium tomentosum 3.jpg' => 'foto',
	
			'Cerastium tomentosum 4.jpg' => 'foto'
	
		),
	
		'Cerastium fontanum' => array(
	
			'Cerastium fontanum vulgare.jpg' => 'overzicht; a = bloem, b = vruchtkelk',
	
			'Cerastium fontanum vulgare 2.jpg' => 'vergroot'
	
		),
	
		'Cerastium fontanum subsp. vulgare' => array(
	
			'Cerastium fontanum vulgare.jpg' => 'overzicht; a = bloem, b = vruchtkelk',
	
			'Cerastium fontanum vulgare 2.jpg' => 'vergroot',
	
			'Cerastium fontanum vulgare 3.jpg' => 'foto'
	
		),
	
		'Cerastium fontanum subsp. holosteoides' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Cerastium glomeratum' => array(
	
			'Cerastium glomeratum.jpg' => 'overzicht; a = bloem, b = kelk,  c = vruchtkelk',
	
			'Cerastium glomeratum 2.jpg' => 'vergroot',
	
			'Cerastium glomeratum 3.jpg' => 'foto',
	
			'Cerastium glomeratum 4.jpg' => 'foto',
	
			'Cerastium glomeratum 5.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Cerastium brachypetalum' => array(
	
			'Cerastium brachypetalum.jpg' => 'overzicht; a = bloem, b = vruchtkelk',
	
			'Cerastium brachypetalum 2.jpg' => 'vergroot',
	
			'Cerastium brachypetalum 3.jpg' => 'foto',
	
			'Cerastium brachypetalum 4.jpg' => 'foto',
	
			'Cerastium brachypetalum 5.jpg' => 'foto'
	
		),
	
		'Cerastium diffusum' => array(
	
			'Cerastium diffusum.jpg' => 'overzicht; a = bloem',
	
			'Cerastium diffusum 2.jpg' => 'vergroot',
	
			'Cerastium diffusum 3.jpg' => 'foto',
	
			'Cerastium diffusum 4.jpg' => 'foto'
	
		),
	
		'Cerastium semidecandrum' => array(
	
			'Cerastium semidecandrum.jpg' => 'overzicht; a = bloem, b = vruchtkelk',
	
			'Cerastium semidecandrum 2.jpg' => 'vergroot',
	
			'Cerastium semidecandrum 3.jpg' => 'foto',
	
			'Cerastium semidecandrum 4.jpg' => 'foto'
	
		),
	
		'Cerastium pumilum' => array(
	
			'Cerastium pumilum.jpg' => 'overzicht; a = bloem, b = vruchtkelk',
	
			'Cerastium pumilum 2.jpg' => 'vergroot',
	
			'Cerastium pumilum 3.jpg' => 'foto',
	
			'Cerastium pumilum 4.jpg' => 'foto',
	
			'Cerastium pumilum 5.jpg' => 'foto',
	
			'Cerastium pumilum 6.jpg' => 'foto',
	
			'Cerastium pumilum 7.jpg' => 'foto'
	
		),
	
		'Cerastium glutinosum' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Holosteum umbellatum' => array(
	
			'Holosteum umbellatum.jpg' => 'overzicht; a = bloem, b = doosvrucht',
	
			'Holosteum umbellatum 2.jpg' => 'vergroot',
	
			'Holosteum umbellatum 3.jpg' => 'foto',
	
			'Holosteum umbellatum 4.jpg' => 'foto',
	
			'Holosteum umbellatum 5.jpg' => 'foto',
	
			'Holosteum umbellatum 6.jpg' => 'foto',
	
			'Holosteum umbellatum 7.jpg' => 'foto'
	
		),
	
		'Moenchia erecta' => array(
	
			'Moenchia erecta.jpg' => 'overzicht; a = bloem, b = vruchtbeginsel, c = vruchtkelk',
	
			'Moenchia erecta 2.jpg' => 'vergroot'
	
		),
	
		'Sagina nodosa' => array(
	
			'Sagina nodosa.jpg' => 'overzicht; a = vruchtkelk',
	
			'Sagina nodosa 2.jpg' => 'vergroot',
	
			'Sagina nodosa 3.jpg' => 'foto, bloeiend',
	
			'Sagina nodosa 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Sagina subulata' => array(
	
			'Sagina subulata.jpg' => 'overzicht; a = bladpaar, b = bloem van boven, c = bloem van opzij',
	
			'Sagina subulata 2.jpg' => 'vergroot'
	
		),
	
		'Sagina procumbens' => array(
	
			'Sagina procumbens.jpg' => 'overzicht',
	
			'Sagina procumbens 2.jpg' => 'vergroot; a = bladpaar, b = bloem van boven, c = bloem van opzij',
	
			'Sagina procumbens 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Sagina maritima' => array(
	
			'Sagina maritima.jpg' => 'overzicht; a = bladpaar',
	
			'Sagina maritima 2.jpg' => 'vergroot',
	
			'Sagina maritima 3.jpg' => 'foto, habitus in vrucht'
	
		),
	
		'Sagina micropetala' => array(
	
			'Sagina micropetala.jpg' => 'overzicht',
	
			'Sagina micropetala 2.jpg' => 'vergroot'
	
		),
	
		'Sagina apetala' => array(
	
			'Sagina apetala.jpg' => 'overzicht; a = bloem, b = bladpaar',
	
			'Sagina apetala 2.jpg' => 'vergroot',
	
			'Sagina apetala 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Agrostemma githago' => array(
	
			'Agrostemma githago.jpg' => 'overzicht',
	
			'Agrostemma githago 2.jpg' => 'vergroot; a = vruchtbeginsel + meeldraden, b  = vruchtkelk',
	
			'Agrostemma githago 3.jpg' => 'foto, bloem',
	
			'Agrostemma githago 4.jpg' => 'foto, bloeiend',
	
			'Agrostemma githago 5.jpg' => 'foto, bloeiend',
	
			'Agrostemma githago 6.jpg' => 'foto, bloeiend'
	
		),
	
		'Silene otites' => array(
	
			'Silene otites.jpg' => 'overzicht; a = manlijke plant, b = manlijke bloem, c = vrouwelijke plant, d = doosvrucht',
	
			'Silene otites 2.jpg' => 'vergroot',
	
			'Silene otites 3.jpg' => 'foto, bloeiwijze',
	
			'Silene otites 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Silene dioica' => array(
	
			'Silene dioica.jpg' => 'overzicht',
	
			'Silene dioica 2.jpg' => 'vergroot; a = kroonblad + 2 meeldraden, b = stamper, c = dws.dsn. vruchtbeginsel, d = rijpe doosvruchten',
	
			'Silene dioica 3.jpg' => 'foto, habitus bloeiend',
	
			'Silene dioica 4.jpg' => 'foto, bloemen'
	
		),
	
		'Silene latifolia subsp. alba' => array(
	
			'Silene latifolia alba.jpg' => 'overzicht',
	
			'Silene latifolia alba 2.jpg' => 'vergroot; a = opengewerkte manlijke bloem, b = opengewerkte vrouwelijke bloem, c = rijpe doosvrucht, d = idem in dws.dsn.',
	
			'Silene latifolia 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Silene flos-cuculi' => array(
	
			'Silene flos-cuculi.jpg' => 'overzicht',
	
			'Silene flos-cuculi 2.jpg' => 'vergroot; a = vruchtbeginsel, 2 meeldraden + 1 kroonblad, b = dws.dsn. vruchtbeginsel',
	
			'Silene flos-cuculi 3.jpg' => 'foto, habitus bloeiend',
	
			'Silene flos-cuculi 5.jpg' => 'foto, bloem'
	
		),
	
		'Silene viscaria' => array(
	
			'Silene viscaria.jpg' => 'overzicht; a = kroonblad + meeldraad, b = vruchtbeginsel',
	
			'Silene viscaria 2.jpg' => 'vergroot',
	
			'Silene viscaria 3.jpg' => 'foto, bloeiwijze vanaf boven gezien'
	
		),
	
		'Silene coeli-rosa' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Silene coronaria' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Silene baccifera' => array(
	
			'Silene baccifera.jpg' => 'overzicht; a = onrijpe bes, b = dws.dsn. bes',
	
			'Silene baccifera 2.jpg' => 'vergroot',
	
			'Silene baccifera 3.jpg' => 'foto, bloemen en vruchten',
	
			'Silene baccifera 4.jpg' => 'foto, bloemen en vruchten'
	
		),
	
		'Silene vulgaris' => array(
	
			'Silene vulgaris.jpg' => 'overzicht; a = doosvrucht, de kelk verwijderd',
	
			'Silene vulgaris 2.jpg' => 'vergroot',
	
			'Silene vulgaris 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Silene conica' => array(
	
			'Silene conica.jpg' => 'overzicht',
	
			'Silene conica 2.jpg' => 'vergroot; a = opengewerkte bloem, b = vruchtkelk',
	
			'Silene conica 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Silene armeria' => array(
	
			'Silene armeria.jpg' => 'overzicht',
	
			'Silene armeria 2.jpg' => 'vergroot; a = bloem, b = meeldraden  + stamper, c = kroonblad',
	
			'Silene armeria 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Silene dichotoma' => array(
	
			'Silene dichotoma.jpg' => 'overzicht; a = stamper, 2 meeldraden + 1 kroonblad',
	
			'Silene dichotoma 2.jpg' => 'vergroot',
	
			'Silene dichotoma 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Silene nutans' => array(
	
			'Silene nutans.jpg' => 'overzicht',
	
			'Silene nutans 2.jpg' => 'vergroot; a = stamper + meeldraden, b = rijpe doosvruchten',
	
			'Silene nutans 3.jpg' => 'foto, bloeiend',
	
			'Silene nutans 4.jpg' => 'foto, bloeiwijzen',
	
			'Silene nutans 5.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Silene noctiflora' => array(
	
			'Silene noctiflora.jpg' => 'overzicht',
	
			'Silene noctiflora 2.jpg' => 'vergroot; a = kroonblad, b = vruchtkelk, c = rijpe doosvrucht',
	
			'Silene noctiflora 3.jpg' => 'foto, bloem',
	
			'Silene noctiflora 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Silene gallica' => array(
	
			'Silene gallica.jpg' => 'overzicht',
	
			'Silene gallica 2.jpg' => 'vergroot; a = vruchtkelk, b = idem, dws.dsn.',
	
			'Silene gallica 3.jpg' => 'foto,bloeiend',
	
			'Silene gallica 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Gypsophila muralis' => array(
	
			'Gypsophila muralis.jpg' => 'overzicht; a = vruchtbeginsel, b = vruchtkelk',
	
			'Gypsophila muralis 2.jpg' => 'vergroot',
	
			'Gypsophila muralis 3.jpg' => 'foto',
	
			'Gypsophila muralis 4.jpg' => 'foto, habitus, in bloei',
	
			'Gypsophila muralis 5.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Gypsophila paniculata' => array(
	
			'Gypsophila paniculata.jpg' => 'overzicht',
	
			'Gypsophila paniculata 2.jpg' => 'foto'
	
		),
	
		'Saponaria officinalis' => array(
	
			'Saponaria officinalis.jpg' => 'overzicht; a = vuchtbeginsel + rugzijde kroonblad, b = doosvrucht, de kelk verwijderd',
	
			'Saponaria officinalis 2.jpg' => 'vergroot',
	
			'Saponaria officinalis 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Saponaria ocymoides' => array(
	
			'Saponaria ocymoides.jpg' => 'overzicht',
	
			'Saponaria ocymoides 2.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Vaccaria hispanica' => array(
	
			'Vaccaria hispanica.jpg' => 'overzicht; a = doosvrucht, de kelk verwijderd',
	
			'Vaccaria hispanica 2.jpg' => 'vergroot',
	
			'Vaccaria hispanica 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Petrorhagia prolifera' => array(
	
			'Petrorhagia prolifera.jpg' => 'overzicht; a = doosvrucht',
	
			'Petrorhagia prolifera 2.jpg' => 'vergroot',
	
			'Petrorhagia prolifera 3.jpg' => 'foto'
	
		),
	
		'Petrorhagia saxifraga' => array(
	
			'Petrorhagia saxifraga.jpg' => 'overzicht',
	
			'Petrorhagia saxifraga 2.jpg' => 'foto'
	
		),
	
		'Dianthus deltoides' => array(
	
			'Dianthus deltoides.jpg' => 'overzicht',
	
			'Dianthus deltoides 2.jpg' => 'vergroot',
	
			'Dianthus deltoides 3.jpg' => 'foto',
	
			'Dianthus deltoides 4.jpg' => 'foto'
	
		),
	
		'Dianthus superbus' => array(
	
			'Dianthus superbus.jpg' => 'overzicht; a =  stamper, meeldraad + kroonblad, b = kelk',
	
			'Dianthus superbus 2.jpg' => 'vergroot',
	
			'Dianthus superbus 3.jpg' => 'foto'
	
		),
	
		'Dianthus carthusianorum' => array(
	
			'Dianthus carthusianorum.jpg' => 'overzicht; a = vruchtkelk',
	
			'Dianthus carthusianorum 2.jpg' => 'vergroot',
	
			'Dianthus carthusianorum 3.jpg' => 'foto',
	
			'Dianthus carthusianorum 4.jpg' => 'foto',
	
			'Dianthus carthusianorum 5.jpg' => 'foto'
	
		),
	
		'Dianthus armeria' => array(
	
			'Dianthus armeria.jpg' => 'overzicht; a = opengewerkte bloem, b = doosvrucht',
	
			'Dianthus armeria 2.jpg' => 'vergroot',
	
			'Dianthus armeria 3.jpg' => 'foto',
	
			'Dianthus armeria 4.jpg' => 'foto'
	
		),
	
		'Dianthus barbatus' => array(
	
			'Dianthus barbatus.jpg' => 'overzicht; a = bloemknop',
	
			'Dianthus barbatus 2.jpg' => 'vergroot',
	
			'Dianthus barbatus 3.jpg' => 'foto'
	
		),
	
		'Amaranthus albus' => array(
	
			'Amaranthus albus.jpg' => 'overzicht; a = vrucht',
	
			'Amaranthus albus 2.jpg' => 'vergroot',
	
			'Amaranthus albus 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Amaranthus graecizans' => array(
	
			'Amaranthus graecizans.jpg' => 'overzicht',
	
			'Amaranthus graecizans 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Amaranthus graecizans 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Amaranthus blitum' => array(
	
			'Amaranthus blitum.jpg' => 'overzicht',
	
			'Amaranthus blitum 2.jpg' => 'vergroot; a = vrucht',
	
			'Amaranthus blitum 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Amaranthus deflexus' => array(
	
			'Amaranthus deflexus.jpg' => 'overzicht; a = bloem',
	
			'Amaranthus deflexus 2.jpg' => 'vergroot'
	
		),
	
		'Amaranthus blitoides' => array(
	
			'Amaranthus blitoides.jpg' => 'overzicht',
	
			'Amaranthus blitoides 2.jpg' => 'vergroot; a = vrucht',
	
			'Amaranthus blitoides 3.jpg' => 'foto, habitus bloeiend',
	
			'Amaranthus blitoides 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Amaranthus standleyanus' => array(
	
			'Amaranthus standleyanus.jpg' => 'overzicht foto',
	
			'Amaranthus standleyanus 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Amaranthus rudis' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Amaranthus palmeri' => array(
	
			'Amaranthus palmeri.jpg' => 'overzicht',
	
			'Amaranthus palmeri 2.jpg' => 'foto, habitus',
	
			'Amaranthus palmeri 3.jpg' => 'foto, detail bloeiwijze'
	
		),
	
		'Amaranthus caudatus' => array(
	
			'Amaranthus caudatus.jpg' => 'overzicht foto',
	
			'Amaranthus caudatus 3.jpg' => 'foto, habitus bloeiend',
	
			'Amaranthus caudatus 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Amaranthus retroflexus' => array(
	
			'Amaranthus retroflexus.jpg' => 'overzicht',
	
			'Amaranthus retroflexus 2.jpg' => 'vergroot; a = vrucht',
	
			'Amaranthus retroflexus 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Amaranthus hybridus' => array(
	
			'Amaranthus hybridus.jpg' => 'overzicht',
	
			'Amaranthus hybridus 2.jpg' => 'vergroot; a = vrucht',
	
			'Amaranthus hybridus 3.jpg' => 'foto, habitus bloeiend',
	
			'Amaranthus hybridus 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Amaranthus hybridus subsp. hybridus' => array(
	
			'Amaranthus hybridus hybridus.jpg' => 'overzicht'
	
		),
	
		'Amaranthus hybridus subsp. bouchonii' => array(
	
			'Amaranthus hybridus bouch.jpg' => 'overzicht',
	
			'Amaranthus hybridus bouch 2.jpg' => 'foto, bloeiwijze',
	
			'Amaranthus hybridus bouch 3.jpg' => 'foto, bloeiwijze',
	
			'Amaranthus hybridus bouch 4.jpg' => 'foto, bloeiwijze',
	
			'Amaranthus hybridus bouch 5.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Beta vulgaris' => array(
	
			'Beta vulgaris vulgaris.jpg' => 'overzicht; a = l.dsn. bloem',
	
			'Beta vulgaris vulgaris 2.jpg' => 'vergroot',
	
			'Beta vulgaris maritima 2.jpg' => 'tekening habitus en bloem'
	
		),
	
		'Beta vulgaris subsp. maritima' => array(
	
			'Beta vulgaris maritima.jpg' => 'overzicht',
	
			'Beta vulgaris maritima 2.jpg' => 'tekening habitus en bloem',
	
			'Beta vulgaris maritima 3.jpg' => 'foto'
	
		),
	
		'Beta vulgaris subsp. vulgaris' => array(
	
			'Beta vulgaris vulgaris.jpg' => 'overzicht; a = l.dsn. bloem',
	
			'Beta vulgaris vulgaris 2.jpg' => 'vergroot',
	
			'Beta vulgaris vulgaris 3.jpg' => 'foto',
	
			'Beta vulgaris vulgaris 4.jpg' => 'foto'
	
		),
	
		'Chenopodium foliosum' => array(
	
			'Chenopodium foliosum.jpg' => 'overzicht',
	
			'Chenopodium foliosum 2.jpg' => 'vergroot',
	
			'Chenopodium foliosum 3.jpg' => 'foto, bloeiwijze',
	
			'Chenopodium foliosum 4.jpg' => 'foto, in vrucht',
	
			'Chenopodium foliosum 5.jpg' => 'foto, in vrucht'
	
		),
	
		'Chenopodium bonus-henricus' => array(
	
			'Chenopodium bonus-henricus.jpg' => 'overzicht; a = bloem, b = vrucht',
	
			'Chenopodium bonus-henricus 2.jpg' => 'vergroot',
	
			'Chenopodium bonus-henricus 3.jpg' => 'foto, bloeiwijze',
	
			'Chenopodium bonus-henricus a.jpg' => 'foto met scanning electronen microscoop, vruchtbeginsel met vrucht, gezien vanaf de zijkant'
	
		),
	
		'Chenopodium ambrosioides' => array(
	
			'Chenopodium ambrosioides.jpg' => 'overzicht',
	
			'Chenopodium ambrosioides 2.jpg' => 'vergroot',
	
			'Chenopodium ambrosioides 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Chenopodium botrys' => array(
	
			'Chenopodium botrys.jpg' => 'overzicht; a = bloem, b = zijaanzicht vrucht',
	
			'Chenopodium botrys 2.jpg' => 'vergroot',
	
			'Chenopodium botrys 3.jpg' => 'foto, habitus bloeiend',
	
			'Chenopodium botrys 4.jpg' => 'tekening, blad en vrucht'
	
		),
	
		'Chenopodium pumilio' => array(
	
			'Chenopodium pumilio.jpg' => 'overzicht foto',
	
			'Chenopodium pumilio 2.jpg' => 'tekening blad en vrucht',
	
			'Chenopodium pumilio 3.jpg' => 'foto, bloeiwijze',
	
			'Chenopodium pumilio a.jpg' => 'foto met scanning electronen microscoop, vruchtbeginsel met vrucht, gezien vanaf de zijkant'
	
		),
	
		'Chenopodium polyspermum' => array(
	
			'Chenopodium polyspermum.jpg' => 'overzicht',
	
			'Chenopodium polyspermum 2.jpg' => 'vergroot',
	
			'Chenopodium polyspermum 3.jpg' => 'foto, habitus bloeiend',
	
			'Chenopodium polyspermum 4.jpg' => 'tekening vrucht',
	
			'Chenopodium polyspermum c.jpg' => 'foto met scanning electronen microscoop, zaad'
	
		),
	
		'Chenopodium glaucum' => array(
	
			'Chenopodium glaucum.jpg' => 'overzicht',
	
			'Chenopodium glaucum 2.jpg' => 'vergroot',
	
			'Chenopodium glaucum 3.jpg' => 'foto, habitus bloeiend',
	
			'Chenopodium glaucum c.jpg' => 'foto met scanning electronen microscoop, zaad'
	
		),
	
		'Chenopodium rubrum' => array(
	
			'Chenopodium rubrum.jpg' => 'overzicht; a = bloemen',
	
			'Chenopodium rubrum 2.jpg' => 'vergroot',
	
			'Chenopodium rubrum 3.jpg' => 'foto, habitus bloeiend',
	
			'Chenopodium rubrum 4.jpg' => 'tekening vrucht',
	
			'Chenopodium rubrum a.jpg' => 'foto met scanning electronen microscoop, vruchtbeginsel met vrucht, gezien vanaf de zijkant',
	
			'Chenopodium rubrum c.jpg' => 'foto met scanning electronen microscoop, zaad'
	
		),
	
		'Chenopodium chenopodioides' => array(
	
			'Chenopodium chenopodioides.jpg' => 'overzicht',
	
			'Chenopodium chenopodioides 2.jpg' => 'tekening, 2 soorten bloemen',
	
			'Chenopodium chenopodioides a.jpg' => 'foto met scanning electronen microscoop, vruchtbeginsel gezien vanaf boven',
	
			'Chenopodium chenopodioides c.jpg' => 'foto met scanning electronen microscoop, zaad'
	
		),
	
		'Chenopodium vulvaria' => array(
	
			'Chenopodium vulvaria.jpg' => 'overzicht; a =  bloem',
	
			'Chenopodium vulvaria 2.jpg' => 'vergroot',
	
			'Chenopodium vulvaria 3.jpg' => 'foto, habitus bloeiend',
	
			'Chenopodium vulvaria 4.jpg' => 'foto, bloeiend',
	
			'Chenopodium vulvaria 5.jpg' => 'foto, bloeiwijzen',
	
			'Chenopodium vulvaria c.jpg' => 'foto met scanning electronen microscoop, zaad'
	
		),
	
		'Chenopodium hybridum' => array(
	
			'Chenopodium hybridum.jpg' => 'overzicht',
	
			'Chenopodium hybridum 2.jpg' => 'vergroot',
	
			'Chenopodium hybridum 3.jpg' => 'foto, habitus bloeiend',
	
			'Chenopodium hybridum 4.jpg' => 'foto, habitus bloeiend',
	
			'Chenopodium hybridum c.jpg' => 'foto met scanning electronen microscoop, zaad'
	
		),
	
		'Chenopodium murale' => array(
	
			'Chenopodium murale.jpg' => 'overzicht; a = zaad',
	
			'Chenopodium murale 2.jpg' => 'vergroot',
	
			'Chenopodium murale 3.jpg' => 'foto, habitus bloeiend',
	
			'Chenopodium murale c.jpg' => 'foto met scanning electronen microscoop, zaad'
	
		),
	
		'Chenopodium berlandieri' => array(
	
			'Chenopodium berlandieri.jpg' => 'foto, overzicht',
	
			'Chenopodium berlandieri a.jpg' => 'foto met scanning electronen microscoop, vruchtbeginsel met vrucht, gezien vanaf boven',
	
			'Chenopodium berlandieri b.jpg' => 'foto met scanning electronen microscoop, vruchtbeginsel gezien vanaf boven',
	
			'Chenopodium berlandieri c.jpg' => 'foto met scanning electronen microscoop, zaad',
	
			'Chenopodium quinoa 2.jpg' => 'foto, Quinoa, bloeiwijzen (zie opmerking)'
	
		),
	
		'Chenopodium ficifolium' => array(
	
			'Chenopodium ficifolium.jpg' => 'overzicht',
	
			'Chenopodium ficifolium 2.jpg' => 'vergroot',
	
			'Chenopodium ficifolium 3.jpg' => 'foto, bloeiwijze',
	
			'Chenopodium ficifolium c.jpg' => 'foto met scanning electronen microscoop, zaad'
	
		),
	
		'Chenopodium album' => array(
	
			'Chenopodium album.jpg' => 'overzicht; a = bloem (voorste bl.dekbl. verwijderd)',
	
			'Chenopodium album 2.jpg' => 'vergroot',
	
			'Chenopodium album 3.jpg' => 'foto, habitus bloeiend',
	
			'Chenopodium album a.jpg' => 'foto met scanning electronen microscoop, vruchtbeginsel met vrucht, gezien vanaf boven',
	
			'Chenopodium album b.jpg' => 'foto met scanning electronen microscoop, vruchtbeginsel gezien vanaf boven',
	
			'Chenopodium album c.jpg' => 'foto met scanning electronen microscoop, zaad'
	
		),
	
		'Spinacia oleracea' => array(
	
			'Spinacia oleracea.jpg' => 'overzicht; a = manlijke, b = vrouwelijke bloem',
	
			'Spinacia oleracea 2.jpg' => 'vergroot',
	
			'Spinacia oleracea 3.jpg' => 'foto, bloeiend',
	
			'Spinacia oleracea 4.jpg' => 'tekening habitus en vrucht',
	
			'Spinacia oleracea 5.jpg' => 'foto, habitus'
	
		),
	
		'Atriplex pedunculata' => array(
	
			'Atriplex pedunculata.jpg' => 'overzicht; a = plant in bloei, b = in vrucht, c = vrucht',
	
			'Atriplex pedunculata 2.jpg' => 'vergroot',
	
			'Atriplex pedunculata 3.jpg' => 'foto',
	
			'Atriplex pedunculata 4.jpg' => 'tekening habitus, mannelijke en vrouwelijke bloem',
	
			'Atriplex pedunculata a.jpg' => 'bloemdekbladen'
	
		),
	
		'Atriplex portulacoides' => array(
	
			'Atriplex portulacoides.jpg' => 'overzicht; a = vruchten',
	
			'Atriplex portulacoides 2.jpg' => 'vergroot',
	
			'Atriplex portulacoides 3.jpg' => 'foto',
	
			'Atriplex portulacoides a.jpg' => 'bloemdekbladen'
	
		),
	
		'Atriplex hortensis' => array(
	
			'Atriplex hortensis.jpg' => 'overzicht',
	
			'Atriplex hortensis 2.jpg' => 'vergroot; a = deelbloeiwijze met twee soorten bloemen, b =  vrucht, een steelblaadje neergeklapt, c = vruchtwijze',
	
			'Atriplex hortensis 3.jpg' => 'foto, habitus bloeiend',
	
			'Atriplex hortensis 4.jpg' => 'foto, habitus bloeiend',
	
			'Atriplex hortensis a.jpg' => 'bloemdekbladen'
	
		),
	
		'Atriplex laciniata' => array(
	
			'Atriplex laciniata.jpg' => 'overzicht; a = vruchten',
	
			'Atriplex laciniata 2.jpg' => 'vergroot',
	
			'Atriplex laciniata 3.jpg' => 'foto',
	
			'Atriplex laciniata a.jpg' => 'bloemdekbladen'
	
		),
	
		'Atriplex littoralis' => array(
	
			'Atriplex littoralis.jpg' => 'overzicht; a = vrucht',
	
			'Atriplex littoralis 2.jpg' => 'vergroot',
	
			'Atriplex littoralis 3.jpg' => 'foto',
	
			'Atriplex littoralis a.jpg' => 'bloemdekbladen'
	
		),
	
		'Atriplex prostrata' => array(
	
			'Atriplex prostrata.jpg' => 'overzicht; a = vrucht',
	
			'Atriplex prostrata 2.jpg' => 'vergroot',
	
			'Atriplex prostrata 3.jpg' => 'foto',
	
			'Atriplex gustafssoniana(x) a.jpg' => '(= Atriplex longipes x Atriplex prostrata), bloemdekbladen',
	
			'Atriplex prostrata 4.jpg' => 'tekening habitus',
	
			'Atriplex prostrata 5.jpg' => 'schematische tekening vrucht',
	
			'Atriplex prostrata a.jpg' => 'bloemdekbladen'
	
		),
	
		'Atriplex glabriuscula' => array(
	
			'Atriplex glabriuscula.jpg' => 'detail; vrucht',
	
			'Atriplex glabriuscula 3.jpg' => 'foto',
	
			'Atriplex glabriuscula 4.jpg' => 'foto',
	
			'Atriplex glabriuscula 5.jpg' => 'foto',
	
			'Atriplex glabriuscula 6.jpg' => 'foto',
	
			'Atriplex glabriuscula a.jpg' => 'bloemdekbladen'
	
		),
	
		'Atriplex patula' => array(
	
			'Atriplex patula.jpg' => 'overzicht; a = vrucht',
	
			'Atriplex patula 2.jpg' => 'vergroot',
	
			'Atriplex patula 3.jpg' => 'foto',
	
			'Atriplex patula 4.jpg' => 'tekening habitus',
	
			'Atriplex patula a.jpg' => 'bloemdekbladen'
	
		),
	
		'Atriplex longipes' => array(
	
			'Atriplex longipes.jpg' => 'overzicht',
	
			'Atriplex longipes a.jpg' => 'bloemdekbladen',
	
			'Atriplex gustafssoniana(x) a.jpg' => '(= Atriplex longipes x Atriplex prostrata), bloemdekbladen'
	
		),
	
		'Axyris amaranthoides' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Bassia hirsuta' => array(
	
			'Bassia hirsuta.jpg' => 'overzicht; a = vruchtkelk',
	
			'Bassia hirsuta 2.jpg' => 'habitus',
	
			'Bassia hirsuta 3.jpg' => 'habitus'
	
		),
	
		'Bassia scoparia' => array(
	
			'Bassia scoparia.jpg' => 'overzicht foto',
	
			'Bassia scoparia 2.jpg' => 'habitus',
	
			'Bassia scoparia 3.jpg' => 'foto',
	
			'Bassia scoparia 4.jpg' => 'foto'
	
		),
	
		'Corispermum intermedium' => array(
	
			'Corispermum intermedium.jpg' => 'overzicht; a = bloem, b = vruchten',
	
			'Corispermum intermedium 2.jpg' => 'vergroot',
	
			'Corispermum intermedium 3.jpg' => 'foto',
	
			'Corispermum intermedium 4.jpg' => 'foto'
	
		),
	
		'Corispermum marschallii' => array(
	
			'Corispermum marschallii.jpg' => 'overzicht; a = vruchten',
	
			'Corispermum marschallii 2.jpg' => 'vergroot'
	
		),
	
		'Salicornia pusilla' => array(
	
			'Salicornia pusilla.jpg' => 'detail; bloeiwijze',
	
			'Salicornia pusilla 2.jpg' => 'foto, habitus'
	
		),
	
		'Salicornia europaea' => array(
	
			'Salicornia europaea.jpg' => 'overzicht; a = detail bloeiwijze',
	
			'Salicornia europaea 2.jpg' => 'vergroot',
	
			'Salicornia europaea 3.jpg' => 'foto, habitus',
	
			'Salicornia europaea 4.jpg' => 'foto, detail'
	
		),
	
		'Salicornia procumbens' => array(
	
			'Salicornia procumbens.jpg' => 'overzicht foto',
	
			'Salicornia procumbens 2.jpg' => 'vergroot; stengelleden',
	
			'Salicornia procumbens 3.jpg' => 'foto, habitus',
	
			'Salicornia procumbens 4.jpg' => 'foto, habitus'
	
		),
	
		'Suaeda maritima' => array(
	
			'Suaeda maritima.jpg' => 'overzicht',
	
			'Suaeda maritima 2.jpg' => 'vergroot; a = bloem',
	
			'Suaeda maritima 3.jpg' => 'foto, habitus',
	
			'Suaeda maritima 4.jpg' => 'foto, habitus'
	
		),
	
		'Salsola kali' => array(
	
			'Salsola kali.jpg' => 'overzicht',
	
			'Salsola kali 2.jpg' => 'vergroot; a = bloem, b = vrucht, deels geopend',
	
			'Salsola kali 3.jpg' => 'foto, in bloei',
	
			'Salsola kali 4.jpg' => 'foto, habitus bloeiend',
	
			'Salsola kali 5.jpg' => 'foto, bloeiend'
	
		),
	
		'Salsola tragus' => array(
	
			'Salsola tragus.jpg' => 'overzicht',
	
			'Salsola tragus 3.jpg' => 'foto, habitus'
	
		),
	
		'Portulaca oleracea' => array(
	
			'Portulaca oleracea.jpg' => 'overzicht',
	
			'Portulaca oleracea 2.jpg' => 'vergroot; a = bloem, b = lengte dsn. knop, c = vrucht',
	
			'Portulaca oleracea 3.jpg' => 'foto',
	
			'Portulaca oleracea 4.jpg' => 'foto',
	
			'Portulaca oleracea 5.jpg' => 'foto'
	
		),
	
		'Montia minor' => array(
	
			'Montia minor.jpg' => 'overzicht',
	
			'Montia minor 2.jpg' => 'vergroot',
	
			'Montia minor 3.jpg' => 'foto',
	
			'Montia minor 4.jpg' => 'foto'
	
		),
	
		'Montia fontana' => array(
	
			'Montia fontana.jpg' => 'overzicht; a = bloem, b = vrucht',
	
			'Montia fontana 2.jpg' => 'vergroot'
	
		),
	
		'Claytonia perfoliata' => array(
	
			'Claytonia perfoliata.jpg' => 'overzicht',
	
			'Claytonia perfoliata 2.jpg' => 'vergroot',
	
			'Claytonia perfoliata 3.jpg' => 'foto',
	
			'Claytonia perfoliata 4.jpg' => 'foto'
	
		),
	
		'Claytonia sibirica' => array(
	
			'Claytonia sibirica.jpg' => 'overzicht',
	
			'Claytonia sibirica 2.jpg' => 'vergroot',
	
			'Claytonia sibirica 3.jpg' => 'foto'
	
		),
	
		'Phytolacca esculenta' => array(
	
			'Phytolacca esculenta.jpg' => 'overzicht foto',
	
			'Phytolacca esculenta 3.jpg' => 'foto',
	
			'Phytolacca esculenta 4.jpg' => 'foto'
	
		),
	
		'Phytolacca americana' => array(
	
			'Phytolacca americana.jpg' => 'overzicht; a = bloem',
	
			'Phytolacca americana 2.jpg' => 'vergroot',
	
			'Phytolacca americana 3.jpg' => 'foto',
	
			'Phytolacca americana 4.jpg' => 'foto'
	
		),
	
		'Thesium humifusum' => array(
	
			'Thesium humifusum.jpg' => 'overzicht; a = bloem, b = vrucht',
	
			'Thesium humifusum 2.jpg' => 'vergroot',
	
			'Thesium humifusum 3.jpg' => 'foto, bloeiend',
	
			'Thesium humifusum 4.jpg' => 'foto, bloeiend',
	
			'Thesium humifusum 5.jpg' => 'foto, in vrucht',
	
			'Thesium humifusum 6.jpg' => 'foto, in bloei en in vrucht'
	
		),
	
		'Thesium pyrenaicum' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Viscum album' => array(
	
			'Viscum album.jpg' => 'overzicht',
	
			'Viscum album 2.jpg' => 'vergroot; a = manlijke bloeiwijze, b = vrouwelijke bloeiwijze, c = dsn. vrouwelijke bloem',
	
			'Viscum album 3.jpg' => 'foto, habitus in boom',
	
			'Viscum album 4.jpg' => 'foto, habitus in vrucht',
	
			'Viscum album 5.jpg' => 'foto, in bloei',
	
			'Viscum album 6.jpg' => 'foto, habitus in bloei'
	
		),
	
		'Ribes uva-crispa' => array(
	
			'Ribes uva-crispa.jpg' => 'overzicht;  a = bloem, b = dsn. bloem, c = vrucht',
	
			'Ribes uva-crispa 2.jpg' => 'vergroot',
	
			'Ribes uva-crispa 3.jpg' => 'foto, in bloei',
	
			'Ribes uva-crispa 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Ribes sanguineum' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Ribes odoratum' => array(
	
			'Ribes odoratum.jpg' => 'overzicht',
	
			'Ribes odoratum 2.jpg' => 'foto, bloeiend',
	
			'Ribes odoratum 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Ribes alpinum' => array(
	
			'Ribes alpinum.jpg' => 'overzicht; a = manlijke bloem, b = vrouwelijke bloem',
	
			'Ribes alpinum 2.jpg' => 'vergroot',
	
			'Ribes alpinum 3.jpg' => 'foto, in bloei',
	
			'Ribes alpinum 4.jpg' => 'foto, in bloei'
	
		),
	
		'Ribes nigrum' => array(
	
			'Ribes nigrum.jpg' => 'overzicht',
	
			'Ribes nigrum 2.jpg' => 'vergroot',
	
			'Ribes nigrum 3.jpg' => 'foto, in bloei',
	
			'Ribes nigrum 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Ribes rubrum' => array(
	
			'Ribes rubrum.jpg' => 'overzicht; a = bloem, b = dsn. bloem',
	
			'Ribes rubrum 2.jpg' => 'vergroot',
	
			'Ribes rubrum 3.jpg' => 'foto, in vrucht',
	
			'Ribes rubrum 4.jpg' => 'foto, in bloei',
	
			'Ribes rubrum 5.jpg' => 'foto, in vrucht'
	
		),
	
		'Ribes spicatum' => array(
	
			'Ribes spicatum.jpg' => 'overzicht',
	
			'Ribes spicatum 2.jpg' => 'foto, bloeiend',
	
			'Ribes spicatum 3.jpg' => 'foto, in vrucht'
	
		),
	
		'Saxifraga tridactylites' => array(
	
			'Saxifraga tridactylites.jpg' => 'overzicht',
	
			'Saxifraga tridactylites 2.jpg' => 'vergroot',
	
			'Saxifraga tridactylites 3.jpg' => 'foto, habitus bloeiend',
	
			'Saxifraga tridactylites 4.jpg' => 'foto, habitus bloeiend',
	
			'Saxifraga tridactylites 5.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Saxifraga granulata' => array(
	
			'Saxifraga granulata.jpg' => 'overzicht; a = dsn. bloem, b = vruchtkelk',
	
			'Saxifraga granulata 2.jpg' => 'vergroot',
	
			'Saxifraga granulata 3.jpg' => 'foto, bloeiend',
	
			'Saxifraga granulata 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		"Saxifraga granulata-'Plena'" => array(
	
			'Saxifraga granulata Plena.jpg' => 'overzicht',
	
			'Saxifraga granulata Plena 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Chrysosplenium alternifolium' => array(
	
			'Chrysosplenium alternifolium.jpg' => 'overzicht; a = bloem, b = dsn. bloem',
	
			'Chrysosplenium alternifol 2.jpg' => 'vergroot',
	
			'Chrysosplenium alternifol 3.jpg' => 'foto'
	
		),
	
		'Chrysosplenium oppositifolium' => array(
	
			'Chrysosplenium oppositifol.jpg' => 'overzicht',
	
			'Chrysosplenium oppositifol 2.jpg' => 'vergroot',
	
			'Chrysosplenium oppositifol 3.jpg' => 'foto',
	
			'Chrysosplenium oppositifol 4.jpg' => 'foto'
	
		),
	
		'Tellima grandiflora' => array(
	
			'Tellima grandiflora.jpg' => 'overzicht',
	
			'Tellima grandiflora 2.jpg' => 'foto, bloeiend',
	
			'Tellima grandiflora 3.jpg' => 'foto, in vrucht'
	
		),
	
		'Crassula tillaea' => array(
	
			'Crassula tillaea.jpg' => 'overzicht; a = vergroot stengelstukje, b = bloem',
	
			'Crassula tillaea 2.jpg' => 'vergroot',
	
			'Crassula tillaea 3.jpg' => 'foto'
	
		),
	
		'Crassula helmsii' => array(
	
			'Crassula helmsii.jpg' => 'overzicht foto',
	
			'Crassula helmsii 3.jpg' => 'foto',
	
			'Crassula helmsii 4.jpg' => 'foto',
	
			'Crassula helmsii 5.jpg' => 'foto'
	
		),
	
		'Sempervivum tectorum' => array(
	
			'Sempervivum tectorum.jpg' => 'overzicht',
	
			'Sempervivum tectorum 2.jpg' => 'vergroot',
	
			'Sempervivum tectorum 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Sedum sarmentosum' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Sedum cepaea' => array(
	
			'Sedum cepaea.jpg' => 'overzicht',
	
			'Sedum cepaea 2.jpg' => 'vergroot'
	
		),
	
		'Sedum spurium' => array(
	
			'Sedum spurium.jpg' => 'overzicht foto',
	
			'Sedum spurium 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Sedum spectabile' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Sedum telephium' => array(
	
			'Sedum telephium.jpg' => 'overzicht',
	
			'Sedum telephium 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Sedum telephium 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Sedum album' => array(
	
			'Sedum album.jpg' => 'overzicht',
	
			'Sedum album 2.jpg' => 'vergroot; a = bloem, b = bloem van onderen',
	
			'Sedum album 3.jpg' => 'foto, habitus bloeiend',
	
			'Sedum album 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Sedum dasyphyllum' => array(
	
			'Sedum dasyphyllum.jpg' => 'overzicht',
	
			'Sedum dasyphyllum 2.jpg' => 'vergroot',
	
			'Sedum dasyphyllum 3.jpg' => 'foto, habitus bloeiend',
	
			'Sedum dasyphyllum 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Sedum rupestre' => array(
	
			'Sedum rupestre.jpg' => 'overzicht; a = bloem, b = vruchtkelk',
	
			'Sedum rupestre 2.jpg' => 'vergroot',
	
			'Sedum rupestre 3.jpg' => 'foto, habitus bloeiend',
	
			'Sedum rupestre 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Sedum sexangulare' => array(
	
			'Sedum sexangulare.jpg' => 'overzicht',
	
			'Sedum sexangulare 2.jpg' => 'vergroot; a = bloem',
	
			'Sedum sexangulare 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Sedum acre' => array(
	
			'Sedum acre.jpg' => 'overzicht',
	
			'Sedum acre 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem',
	
			'Sedum acre 3.jpg' => 'foto, habitus bloeiend',
	
			'Sedum acre 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Myriophyllum aquaticum' => array(
	
			'Myriophyllum aquaticum.jpg' => 'overzicht foto',
	
			'Myriophyllum aquaticum 3.jpg' => 'foto'
	
		),
	
		'Myriophyllum verticillatum' => array(
	
			'Myriophyllum verticillatum.jpg' => 'overzicht',
	
			'Myriophyllum verticillatum 2.jpg' => 'vergroot',
	
			'Myriophyllum verticillatum 3.jpg' => 'foto'
	
		),
	
		'Myriophyllum heterophyllum' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Myriophyllum alterniflorum' => array(
	
			'Myriophyllum alterniflorum.jpg' => 'overzicht',
	
			'Myriophyllum alterniflorum 2.jpg' => 'vergroot',
	
			'Myriophyllum alterniflorum 3.jpg' => 'foto',
	
			'Myriophyllum alterniflorum 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Myriophyllum spicatum' => array(
	
			'Myriophyllum spicatum.jpg' => 'overzicht',
	
			'Myriophyllum spicatum 2.jpg' => 'vergroot; a = bloem, b = dsn. vrucht',
	
			'Myriophyllum spicatum 3.jpg' => 'foto',
	
			'Myriophyllum spicatum 4.jpg' => 'foto'
	
		),
	
		'Parthenocissus tricuspidata' => array(
	
			'Parthenocissus tricuspidata.jpg' => 'overzicht',
	
			'Parthenocissus tricuspidata 3.jpg' => 'overzicht'
	
		),
	
		'Parthenocissus inserta' => array(
	
			'Parthenocissus inserta.jpg' => 'overzicht',
	
			'Parthenocissus inserta 2.jpg' => 'vergroot: a = bloem, b = dsn. bloem',
	
			'Parthenocissus inserta 3.jpg' => 'foto',
	
			'Parthenocissus inserta 4.jpg' => 'foto'
	
		),
	
		'Vitis vinifera' => array(
	
			'Vitis vinifera.jpg' => 'overzicht',
	
			'Vitis vinifera 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem',
	
			'Vitis vinifera 3.jpg' => 'foto, habitus',
	
			'Vitis vinifera 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Staphylea pinnata' => array(
	
			'Staphylea pinnata.jpg' => 'overzicht',
	
			'Staphylea pinnata 2.jpg' => 'vergroot; a = vrucht',
	
			'Staphylea pinnata 3.jpg' => 'foto, bloeiwijzen',
	
			'Staphylea pinnata 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Geranium robertianum' => array(
	
			'Geranium robertianum.jpg' => 'overzicht',
	
			'Geranium robertianum 2.jpg' => 'vergroot; a = bloem zonder kroonbladen',
	
			'Geranium robertianum 3.jpg' => 'foto'
	
		),
	
		'Geranium purpureum' => array(
	
			'Geranium purpureum.jpg' => 'overzicht foto',
	
			'Geranium purpureum 3.jpg' => 'foto',
	
			'Geranium purpureum 4.jpg' => 'foto'
	
		),
	
		'Geranium lucidum' => array(
	
			'Geranium lucidum.jpg' => 'overzicht',
	
			'Geranium lucidum 2.jpg' => 'vergroot',
	
			'Geranium lucidum 3.jpg' => 'foto',
	
			'Geranium lucidum 4.jpg' => 'blad, bloem, vruchtkelk',
	
			'Geranium lucidum 5.jpg' => 'foto'
	
		),
	
		'Geranium macrorrhizum' => array(
	
			'Geranium macrorrhizum.jpg' => 'overzicht',
	
			'Geranium macrorrhizum 2.jpg' => 'tekening habitus',
	
			'Geranium macrorrhizum 3.jpg' => 'foto'
	
		),
	
		'Geranium sanguineum' => array(
	
			'Geranium sanguineum.jpg' => 'overzicht',
	
			'Geranium sanguineum 2.jpg' => 'vergroot',
	
			'Geranium sanguineum 3.jpg' => 'foto',
	
			'Geranium sanguineum 4.jpg' => 'foto'
	
		),
	
		'Geranium phaeum' => array(
	
			'Geranium phaeum.jpg' => 'overzicht',
	
			'Geranium phaeum 2.jpg' => 'vergroot; a = bloem zonder kroonbladen',
	
			'Geranium phaeum 3.jpg' => 'foto, bloemen',
	
			'Geranium phaeum 4.jpg' => 'foto, habitus',
	
			'Geranium phaeum 5.jpg' => 'foto, bloemen',
	
			'Geranium phaeum 6.jpg' => 'foto, bloemen'
	
		),
	
		'Geranium endressii' => array(
	
			'Geranium endressii.jpg' => 'overzicht',
	
			'Geranium endressii 3.jpg' => 'foto'
	
		),
	
		'Geranium pratense' => array(
	
			'Geranium pratense.jpg' => 'overzicht',
	
			'Geranium pratense 2.jpg' => 'vergroot',
	
			'Geranium pratense 3.jpg' => 'foto',
	
			'Geranium pratense 4.jpg' => 'foto',
	
			'Geranium pratense 5.jpg' => 'foto, habitus, in bloei'
	
		),
	
		'Geranium sylvaticum' => array(
	
			'Geranium sylvaticum.jpg' => 'overzicht',
	
			'Geranium sylvaticum 2.jpg' => 'habitus',
	
			'Geranium sylvaticum 3.jpg' => 'foto'
	
		),
	
		'Geranium columbinum' => array(
	
			'Geranium columbinum.jpg' => 'overzicht',
	
			'Geranium columbinum 2.jpg' => 'vergroot',
	
			'Geranium columbinum 3.jpg' => 'foto'
	
		),
	
		'Geranium dissectum' => array(
	
			'Geranium dissectum.jpg' => 'overzicht',
	
			'Geranium dissectum 2.jpg' => 'vergroot',
	
			'Geranium dissectum 3.jpg' => 'foto'
	
		),
	
		'Geranium pusillum' => array(
	
			'Geranium pusillum.jpg' => 'overzicht',
	
			'Geranium pusillum 2.jpg' => 'vergroot',
	
			'Geranium pusillum 3.jpg' => 'foto'
	
		),
	
		'Geranium molle' => array(
	
			'Geranium molle.jpg' => 'overzicht',
	
			'Geranium molle 2.jpg' => 'vergroot',
	
			'Geranium molle 3.jpg' => 'foto',
	
			'Geranium molle 4.jpg' => 'foto'
	
		),
	
		'Geranium pyrenaicum' => array(
	
			'Geranium pyrenaicum.jpg' => 'overzicht',
	
			'Geranium pyrenaicum 2.jpg' => 'vergroot',
	
			'Geranium pyrenaicum 3.jpg' => 'foto'
	
		),
	
		'Geranium rotundifolium' => array(
	
			'Geranium rotundifolium.jpg' => 'overzicht',
	
			'Geranium rotundifolium 2.jpg' => 'vergroot; a = opengesprongen vrucht',
	
			'Geranium rotundifolium 3.jpg' => 'foto',
	
			'Geranium rotundifolium 4.jpg' => 'foto'
	
		),
	
		'Erodium lebelii' => array(
	
			'Erodium lebelii.jpg' => 'overzicht foto',
	
			'Erodium lebelii 3.jpg' => 'foto, habitus bloeiend',
	
			'Erodium lebelii 4.jpg' => 'foto, bloemen en vruchtjes'
	
		),
	
		'Erodium cicutarium' => array(
	
			'Erodium cicutarium.jpg' => 'overzicht',
	
			'Erodium cicutarium 2.jpg' => 'vergroot; a = bloem, 2 kroonbladen verwijderd, b = stamper, c = vruchtkelk',
	
			'Erodium cicutarium 3.jpg' => 'foto, habitus bloeiend',
	
			'Erodium cicutarium 4.jpg' => 'foto, bloemen',
	
			'Erodium cicutarium 5.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Lythrum salicaria' => array(
	
			'Lythrum salicaria.jpg' => 'overzicht',
	
			'Lythrum salicaria 2.jpg' => 'vergroot; a = bloem, b = geopende bloem',
	
			'Lythrum salicaria 3.jpg' => 'foto, habitus, in bloei',
	
			'Lythrum salicaria 4.jpg' => 'foto, bloeiwijze',
	
			'Lythrum salicaria 5.jpg' => 'foto, bloemen'
	
		),
	
		'Lythrum junceum' => array(
	
			'Lythrum junceum.jpg' => 'overzicht',
	
			'Lythrum junceum 2.jpg' => 'vergroot',
	
			'Lythrum junceum 3.jpg' => 'foto'
	
		),
	
		'Lythrum portula' => array(
	
			'Lythrum portula.jpg' => 'overzicht',
	
			'Lythrum portula 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Lythrum portula 3.jpg' => 'foto'
	
		),
	
		'Lythrum hyssopifolia' => array(
	
			'Lythrum hyssopifolia.jpg' => 'overzicht',
	
			'Lythrum hyssopifolia 2.jpg' => 'vergroot',
	
			'Lythrum hyssopifolia 3.jpg' => 'foto',
	
			'Lythrum hyssopifolia 4.jpg' => 'foto'
	
		),
	
		'Trapa natans' => array(
	
			'Trapa natans.jpg' => 'overzicht',
	
			'Trapa natans 2.jpg' => 'habitus en doorsnede vrucht',
	
			'Trapa natans 3.jpg' => 'foto, habitus'
	
		),
	
		'Circaea alpina' => array(
	
			'Circaea alpina.jpg' => 'overzicht',
	
			'Circaea alpina 2.jpg' => 'vergroot',
	
			'Circaea alpina 3.jpg' => 'foto'
	
		),
	
		'Circaea intermedia(x)' => array(
	
			'Circaea intermedia(x).jpg' => 'overzicht',
	
			'Circaea intermedia(x) 2.jpg' => 'vergroot',
	
			'Circaea intermedia(x) 3.jpg' => 'foto'
	
		),
	
		'Circaea lutetiana' => array(
	
			'Circaea lutetiana.jpg' => 'overzicht; a = bloem, b = dsn. bloem, c = dsn. vrucht',
	
			'Circaea lutetiana 2.jpg' => 'vergroot',
	
			'Circaea lutetiana 3.jpg' => 'foto',
	
			'Circaea lutetiana 4.jpg' => 'foto'
	
		),
	
		'Oenothera glazioviana' => array(
	
			'Oenothera glazioviana.jpg' => 'overzicht foto',
	
			'Oenothera glazioviana 3.jpg' => 'foto',
	
			'Oenothera glazioviana 4.jpg' => 'foto',
	
			'Oenothera glazioviana a.jpg' => 'bloem met detail vruchtbeginsel'
	
		),
	
		'Oenothera oakesiana' => array(
	
			'Oenothera oakesiana.jpg' => 'overzicht',
	
			'Oenothera oakesiana 2.jpg' => 'habitus',
	
			'Oenothera oakesiana 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Oenothera deflexa' => array(
	
			'Oenothera deflexa.jpg' => 'overzicht',
	
			'Oenothera deflexa 2.jpg' => 'vergroot',
	
			'Oenothera deflexa 3.jpg' => 'foto',
	
			'Oenothera deflexa 4.jpg' => 'foto'
	
		),
	
		'Oenothera biennis' => array(
	
			'Oenothera biennis.jpg' => 'overzicht; a = dsn. bloem',
	
			'Oenothera biennis 2.jpg' => 'vergroot',
	
			'Oenothera biennis 3.jpg' => 'foto',
	
			'Oenothera biennis 4.jpg' => 'foto',
	
			'Oenothera biennis a.jpg' => 'bloem met detail vruchtbeginsel'
	
		),
	
		'Oenothera fallax(x)' => array(
	
			'Oenothera fallax(x).jpg' => 'overzicht',
	
			'Oenothera fallax(x) 2.jpg' => 'foto',
	
			'Oenothera fallax(x) a.jpg' => 'bloem met detail vruchtbeginsel'
	
		),
	
		'Ludwigia palustris' => array(
	
			'Ludwigia palustris.jpg' => 'overzicht',
	
			'Ludwigia palustris 2.jpg' => 'vergroot',
	
			'Ludwigia palustris 3.jpg' => 'foto'
	
		),
	
		'Ludwigia grandiflora' => array(
	
			'Ludwigia grandiflora.jpg' => 'oveerzicht',
	
			'Ludwigia grandiflora 2.jpg' => 'bloemen en vrucht',
	
			'Ludwigia grandiflora 3.jpg' => 'foto, habitus, vegetatief',
	
			'Ludwigia grandiflora 4.jpg' => 'foto, habitus, in bloei',
	
			'Ludwigia grandiflora 5.jpg' => 'foto, bloem'
	
		),
	
		'Ludwigia peploides' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Chamerion angustifolium' => array(
	
			'Chamerion angustifolium.jpg' => 'overzicht',
	
			'Chamerion angustifolium 2.jpg' => 'vergroot; a = bloem, deels doorgesneden, b = vrucht, c = zaad',
	
			'Chamerion angustifolium 3.jpg' => 'foto',
	
			'Chamerion angustifolium 4.jpg' => 'foto'
	
		),
	
		'Epilobium komarovianum' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Epilobium hirsutum' => array(
	
			'Epilobium hirsutum.jpg' => 'overzicht',
	
			'Epilobium hirsutum 2.jpg' => 'vergroot',
	
			'Epilobium hirsutum 3.jpg' => 'foto',
	
			'Epilobium hirsutum 4.jpg' => 'foto'
	
		),
	
		'Epilobium parviflorum' => array(
	
			'Epilobium parviflorum.jpg' => 'overzicht',
	
			'Epilobium parviflorum 2.jpg' => 'vergroot',
	
			'Epilobium parviflorum 3.jpg' => 'foto',
	
			'Epilobium parviflorum 4.jpg' => 'foto'
	
		),
	
		'Epilobium palustre' => array(
	
			'Epilobium palustre.jpg' => 'overzicht',
	
			'Epilobium palustre 2.jpg' => 'vergroot',
	
			'Epilobium palustre 3.jpg' => 'foto'
	
		),
	
		'Epilobium ciliatum' => array(
	
			'Epilobium ciliatum.jpg' => 'overzicht foto',
	
			'Epilobium ciliatum 3.jpg' => 'foto'
	
		),
	
		'Epilobium roseum' => array(
	
			'Epilobium roseum.jpg' => 'overzicht; a = bloem + kroonblad',
	
			'Epilobium roseum 2.jpg' => 'vergroot',
	
			'Epilobium roseum 3.jpg' => 'foto'
	
		),
	
		'Epilobium lanceolatum' => array(
	
			'Epilobium lanceolatum.jpg' => 'overzicht',
	
			'Epilobium lanceolatum 2.jpg' => 'vergroot'
	
		),
	
		'Epilobium montanum' => array(
	
			'Epilobium montanum.jpg' => 'overzicht',
	
			'Epilobium montanum 2.jpg' => 'vergroot',
	
			'Epilobium montanum 3.jpg' => 'foto'
	
		),
	
		'Epilobium obscurum' => array(
	
			'Epilobium obscurum.jpg' => 'overzicht',
	
			'Epilobium obscurum 2.jpg' => 'vergroot',
	
			'Epilobium obscurum 3.jpg' => 'foto'
	
		),
	
		'Epilobium tetragonum' => array(
	
			'Epilobium tetragonum.jpg' => 'overzicht',
	
			'Epilobium tetragonum 2.jpg' => 'vergroot',
	
			'Epilobium tetragonum 3.jpg' => 'foto'
	
		),
	
		'Euonymus europaeus' => array(
	
			'Euonymus europaeus.jpg' => 'overzicht; a = bloem, b = dsn. bloem, c = openspringende vruchten',
	
			'Euonymus europaeus 2.jpg' => 'vergroot',
	
			'Euonymus europaeus 3.jpg' => 'foto',
	
			'Euonymus europaeus 4.jpg' => 'foto',
	
			'Euonymus europaeus 5.jpg' => 'foto, vruchten',
	
			'Euonymus europaeus 6.jpg' => 'foto, vruchten met zaden'
	
		),
	
		'Parnassia palustris' => array(
	
			'Parnassia palustris.jpg' => 'overzicht; a = onvruchtbare meeldraad, b = vrucht',
	
			'Parnassia palustris 2.jpg' => 'vergroot',
	
			'Parnassia palustris 3.jpg' => 'foto',
	
			'Parnassia palustris 4.jpg' => 'foto',
	
			'Parnassia palustris 5.jpg' => 'foto',
	
			'Parnassia palustris 6.jpg' => 'foto'
	
		),
	
		'Salix repens' => array(
	
			'Salix repens.jpg' => 'overzicht',
	
			'Salix repens a.jpg' => 'vergroot; bebladerde twijg met mannelijk katje, vrouwelijk katje, mannelijke bloem, vrouwelijke bloem',
	
			'Salix repens 3.jpg' => 'foto, mannelijke katjes',
	
			'Salix repens 4.jpg' => 'foto, vrouwelijke katjes'
	
		),
	
		'Salix purpurea' => array(
	
			'Salix purpurea.jpg' => 'overzicht',
	
			'Salix purpurea a.jpg' => 'vergroot; bebladerde twijg met mannelijk katje, vrouwelijk katje, mannelijke bloem, vrouwelijke bloem',
	
			'Salix purpurea 3.jpg' => 'foto, mannelijke katjes',
	
			'Salix purpurea 4.jpg' => 'foto, mannelijke katjes',
	
			'Salix rubra(x) a.jpg' => '(= Salix purpurea x viminalis) manlijk katje, vrouwelijk katje, manlijke bloem, vrouwelijke bloem,  steunblad'
	
		),
	
		"Salix 'Sekka'" => array(
	
			"Salix 'Sekka'.jpg" => 'overzicht',
	
			"Salix 'Sekka' 3.jpg" => 'foto, tak'
	
		),
	
		'Salix daphnoides' => array(
	
			'Salix daphnoides.jpg' => 'overzicht',
	
			'Salix daphnoides a.jpg' => 'vergroot; bebladerde twijg met manlijk katje, vrouwelijk katje, manlijke bloem, vrouwelijke bloem',
	
			'Salix daphnoides 3.jpg' => 'foto, vrouwelijke katjes'
	
		),
	
		'Salix triandra' => array(
	
			'Salix triandra.jpg' => 'overzicht',
	
			'Salix triandra a.jpg' => 'vergroot',
	
			'Salix triandra b.jpg' => 'vergroot; mannelijke en vrouwelijke bloem met schutblad',
	
			'Salix triandra 3.jpg' => 'foto, mannelijke katjes',
	
			'Salix triandra 4.jpg' => 'foto, vrouwelijke katjes',
	
			'Salix mollissima(x) a.jpg' => '(= Salix triandra x viminalis) vergroot; vrouwelijk katje, vrouwelijke bloem, steunblad, bladvoet'
	
		),
	
		'Salix pentandra' => array(
	
			'Salix pentandra.jpg' => 'overzicht',
	
			'Salix pentandra a.jpg' => 'vergroot; mannelijk katje, mannelijke bloem, bladvoet, steunblad',
	
			'Salix pentandra 3.jpg' => 'foto, vrouwelijke katjes',
	
			'Salix pentandra 4.jpg' => 'foto, vrouwelijke katjes'
	
		),
	
		'Salix fragilis' => array(
	
			'Salix fragilis.jpg' => 'overzicht',
	
			'Salix fragilis a.jpg' => 'vergroot; mannelijk katje, mannelijke bloem, bladvoet, steunblad',
	
			'Salix fragilis 3.jpg' => 'foto, mannelijke katjes',
	
			'Salix rubens(x) a.jpg' => '(= Salix alba x fragilis) vergroot; bebladerde twijg met mannelijk katje, vrouwelijk katje, mannelijke bloem, vrouwelijke bloem'
	
		),
	
		'Salix alba' => array(
	
			'Salix alba.jpg' => 'overzicht',
	
			'Salix alba a.jpg' => 'vergroot; bebladerde twijg met mannelijk katje, vrouwelijk katje, mannelijke bloem, vrouwelijke bloem',
	
			'Salix alba 3.jpg' => 'foto, habitus',
	
			'Salix alba 4.jpg' => 'foto, vrouwelijke katjes',
	
			'Salix alba 5.jpg' => 'foto, mannelijke katjes',
	
			'Salix rubens(x) a.jpg' => '(= Salix alba x fragilis) vergroot; bebladerde twijg met mannelijk katje, vrouwelijk katje, mannelijke bloem, vrouwelijke bloem'
	
		),
	
		'Salix dasyclados' => array(
	
			'Salix dasyclados.jpg' => 'overzicht',
	
			'Salix dasyclados 2.jpg' => 'vergroot; bebladerde twijg met vrouwelijk katje, vrouwelijke bloem',
	
			'Salix dasyclados 3.jpg' => 'foto, vrouwelijke katjes'
	
		),
	
		'Salix viminalis' => array(
	
			'Salix viminalis.jpg' => 'overzicht',
	
			'Salix viminalis a.jpg' => 'vergroot',
	
			'Salix viminalis 3.jpg' => 'foto, vrouwelijke katjes in vrucht',
	
			'Salix viminalis 4.jpg' => 'foto, vrouwelijke katjes',
	
			'Salix sericans(x) a.jpg' => '(= Salix caprea x viminalis) manlijk katje, vrouwelijk katje, manlijke bloem, vrouwelijke bloem, steunblad, hout zonder bast',
	
			'Salix smithiana(x) a.jpg' => '(= Salix cinerea x viminalis) manlijk katje, vrouwelijk katje, manlijke bloem, vrouwelijke bloem, steunblad, hout zonder bast',
	
			'Salix mollissima(x) a.jpg' => '(= Salix triandra x viminalis) vergroot; vrouwelijk katje, vrouwelijke bloem, steunblad, bladvoet',
	
			'Salix rubra(x) a.jpg' => '(= Salix purpurea x viminalis) manlijk katje, vrouwelijk katje, manlijke bloem, vrouwelijke bloem,  steunblad'
	
		),
	
		'Salix caprea' => array(
	
			'Salix caprea.jpg' => 'overzicht',
	
			'Salix caprea a.jpg' => 'vergroot; bebladerde twijg met manlijk katje, vrouwelijk katje, manlijke bloem, vrouwelijke bloem, steunblad, hout zonder bast',
	
			'Salix caprea 3.jpg' => 'foto, mannelijke katjes',
	
			'Salix caprea 4.jpg' => 'foto, vrouwelijke katjes',
	
			'Salix reichardtii(x) a.jpg' => '(= Salix caprea x cinerea) vergroot; manlijk katje, vrouwelijk katje, manlijke bloem, vrouwelijke bloem, steunblad',
	
			'Salix sericans(x) a.jpg' => '(= Salix caprea x viminalis) manlijk katje, vrouwelijk katje, manlijke bloem, vrouwelijke bloem, steunblad, hout zonder bast'
	
		),
	
		'Salix aurita' => array(
	
			'Salix aurita.jpg' => 'overzicht',
	
			'Salix aurita a.jpg' => 'vergroot; bebladerde twijg met manlijk katje, vrouwelijk katje, manlijke bloem, vrouwelijke bloem, steunblad, hout zonder bast',
	
			'Salix aurita 3.jpg' => 'foto, katjes in vrucht',
	
			'Salix multinervis(x) a.jpg' => '(= Salix aurita x cinerea) vergroot; manlijk katje,  vrouwelijk katje, manlijke bloem, vrouwelijke bloem, steunblad, hout zonder bast'
	
		),
	
		'Salix cinerea' => array(
	
			'Salix cinerea.jpg' => 'overzicht',
	
			'Salix cinerea 3.jpg' => 'foto, habitus in voorjaar met katjes',
	
			'Salix cinerea 4.jpg' => 'foto, vrouwelijke katjes',
	
			'Salix multinervis(x) a.jpg' => '(= Salix aurita x cinerea) vergroot; manlijk katje,  vrouwelijk katje, manlijke bloem, vrouwelijke bloem, steunblad, hout zonder bast',
	
			'Salix smithiana(x) a.jpg' => '(= Salix cinerea x viminalis) manlijk katje, vrouwelijk katje, manlijke bloem, vrouwelijke bloem, steunblad, hout zonder bast',
	
			'Salix reichardtii(x) a.jpg' => '(= Salix caprea x cinerea) vergroot; manlijk katje, vrouwelijk katje, manlijke bloem, vrouwelijke bloem, steunblad'
	
		),
	
		'Salix cinerea subsp. cinerea' => array(
	
			'Salix cinerea cinerea.jpg' => 'overzicht; a = manlijk katje, b = vrouwelijk katje',
	
			'Salix cinerea cinerea a.jpg' => 'vergroot; bebladerde twijg met manlijk katje, vrouwelijk katje, manlijke bloem, vrouwelijke bloem, steunblad,hout zonder bast'
	
		),
	
		'Salix cinerea subsp. oleifolia' => array(
	
			'Salix cinerea oleifolia.jpg' => 'overzicht',
	
			'Salix cinerea oleifolia a.jpg' => 'vergroot; bebladerde twijg met manlijk katje, vrouwelijk katje, manlijke bloem, vrouwelijke bloem, steunblad,hout zonder bast',
	
			'Salix cinerea oleifolia 2.jpg' => 'foto, vrouwelijke katjes'
	
		),
	
		'Populus alba' => array(
	
			'Populus alba.jpg' => 'overzicht',
	
			'Populus alba a.jpg' => 'vergroot',
	
			'Populus alba b.jpg' => 'manlijke en vrouwelijke bloem met katjesschubben',
	
			'Populus alba 3.jpg' => 'foto',
	
			'Populus alba 4.jpg' => 'foto'
	
		),
	
		'Populus canescens(x)' => array(
	
			'Populus canescens(x).jpg' => 'overzicht',
	
			'Populus canescens(x) a.jpg' => 'vergroot; manlijk katje, vrouwelijk katje, manlijke bloem, vrouwelijke bloem, katjesschubben',
	
			'Populus canescens(x) b.jpg' => 'vergroot; manlijke en vrouwelijke bloem met katjesschubben',
	
			'Populus canescens(x) 3.jpg' => 'foto, bladen',
	
			'Populus canescens(x) 4.jpg' => 'foto, bast'
	
		),
	
		'Populus trichocarpa' => array(
	
			'Populus trichocarpa.jpg' => 'overzicht',
	
			'Populus trichocarpa a.jpg' => 'vergroot; manlijk katje, vrouwelijk katje, manlijke bloem, vrouwelijke bloem, katjesschubben',
	
			'Populus trichocarpa 2.jpg' => 'foto'
	
		),
	
		'Populus balsamifera' => array(
	
			'Populus balsamifera.jpg' => 'overzicht; a = manlijk katje, b = vrouwelijk katje',
	
			'Populus balsamifera 2.jpg' => 'vergroot; a = manlijk katje, b = vrouwelijk katje, c = vrouwelijke bloem, d = katjesschub',
	
			'Populus balsamifera 3.jpg' => 'foto',
	
			'Populus balsamifera 4.jpg' => 'foto'
	
		),
	
		'Populus tremula' => array(
	
			'Populus tremula.jpg' => 'overzicht',
	
			'Populus tremula a.jpg' => 'vergroot',
	
			'Populus tremula b.jpg' => 'vergroot;  manlijke en vrouwelijke bloem met katjesschubben',
	
			'Populus tremula 3.jpg' => 'foto',
	
			'Populus tremula 4.jpg' => 'foto, vrouwelijke katjes',
	
			'Populus tremula 5.jpg' => 'foto, vrouwelijke katjes',
	
			'Populus tremula 6.jpg' => 'foto, vrouwelijke katjes',
	
			'Populus tremula 7.jpg' => 'foto, vrouwelijke katjes'
	
		),
	
		'Populus canadensis(x)' => array(
	
			'Populus canadensis(x).jpg' => 'overzicht',
	
			'Populus canadensis(x) a.jpg' => 'vergroot manlijke plant',
	
			'Populus canadensis(x) b.jpg' => 'vergroot vrouwelijke plant',
	
			'Populus canadensis(x) 3.jpg' => 'foto',
	
			'Populus canadensis(x) 4.jpg' => 'foto'
	
		),
	
		'Populus nigra' => array(
	
			'Populus nigra.jpg' => 'overzicht; a = manlijk katje, b = vrouwelijk katje',
	
			'Populus nigra a.jpg' => 'vergroot',
	
			'Populus nigra b.jpg' => 'vergroot;  manlijke en vrouwelijke bloem met katjesschubben',
	
			'Populus nigra 3.jpg' => 'foto',
	
			'Populus nigra 4.jpg' => 'foto',
	
			'Populus nigra 5.jpg' => 'foto, jonge mannelijke katjes',
	
			"Populus nigra 'Italica' 2.jpg" => 'foto, bomen'
	
		),
	
		'Viola arvensis' => array(
	
			'Viola arvensis.jpg' => 'overzicht',
	
			'Viola arvensis 2.jpg' => 'vergroot; a = meeldraden + stamper, b = gespoorde meeldraad met top-aangangsel, c = stamper, d = dws.dsn. vruchtbeginsel',
	
			'Viola arvensis 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Viola tricolor' => array(
	
			'Viola tricolor.jpg' => 'overzicht; a = dsn. bloem, b = meeldraden + stempel, c = stamper, d = dws.dsn. vruchtbeginsel',
	
			'Viola tricolor 2.jpg' => 'vergroot',
	
			'Viola tricolor 3.jpg' => 'foto, bloeiend',
	
			'Viola tricolor 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Viola lutea subsp. calaminaria' => array(
	
			'Viola lutea calaminaria.jpg' => 'overzicht',
	
			'Viola lutea calaminaria 2.jpg' => 'vergroot',
	
			'Viola lutea calaminaria 3.jpg' => 'foto, habitus bloeiend',
	
			'Viola lutea calaminaria 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Viola curtisii' => array(
	
			'Viola curtisii.jpg' => 'overzicht foto',
	
			'Viola curtisii 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Viola palustris' => array(
	
			'Viola palustris.jpg' => 'overzicht; a = vruchtkelk',
	
			'Viola palustris 2.jpg' => 'vergroot',
	
			'Viola palustris 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Viola odorata' => array(
	
			'Viola odorata.jpg' => 'overzicht; a = stamper, b = vrucht',
	
			'Viola odorata 2.jpg' => 'vergroot',
	
			'Viola odorata 3.jpg' => 'foto, habitus, blauw bloeiend',
	
			'Viola odorata 4.jpg' => 'foto, habitus, wilt bloeiend'
	
		),
	
		'Viola hirta' => array(
	
			'Viola hirta.jpg' => 'overzicht; a = stamper',
	
			'Viola hirta 2.jpg' => 'vergroot',
	
			'Viola hirta 3.jpg' => 'foto, habitus, bloeiend',
	
			'Viola hirta 4.jpg' => 'foto, habitus, bloeiend'
	
		),
	
		'Viola canina' => array(
	
			'Viola canina.jpg' => 'overzicht',
	
			'Viola canina 2.jpg' => 'vergroot',
	
			'Viola canina 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Viola persicifolia' => array(
	
			'Viola persicifolia.jpg' => 'overzicht',
	
			'Viola persicifolia 2.jpg' => 'vergroot',
	
			'Viola persicifolia 3.jpg' => 'foto, habitus bloeiend',
	
			'Viola persicifolia 4.jpg' => 'foto, bloeiend',
	
			'Viola persicifolia 5.jpg' => 'foto, in vrucht',
	
			'Viola persicifolia 6.jpg' => 'foto, habitus, bloeiend'
	
		),
	
		'Viola persicifolia var. persicifolia' => array(
	
			'Viola persicifolia.jpg' => 'overzicht',
	
			'Viola persicifolia 2.jpg' => 'vergroot'
	
		),
	
		'Viola persicifolia var. lacteaeoides' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Viola rupestris' => array(
	
			'Viola rupestris.jpg' => 'overzicht',
	
			'Viola rupestris 2.jpg' => 'vergroot',
	
			'Viola rupestris 3.jpg' => 'foto, bloeiend',
	
			'Viola rupestris 4.jpg' => 'foto, bloeiend',
	
			'Viola rupestris 5.jpg' => 'foto, in vrucht'
	
		),
	
		'Viola reichenbachiana' => array(
	
			'Viola reichenbachiana.jpg' => 'overzicht',
	
			'Viola reichenbachiana 2.jpg' => 'vergroot; a = bloem',
	
			'Viola reichenbachiana 3.jpg' => 'foto, habitus, bloeiend',
	
			'Viola reichenbachiana 4.jpg' => 'foto, habitus, bloeiend',
	
			'Viola reichenbachiana 5.jpg' => 'foto, habitus, bloeiend'
	
		),
	
		'Viola riviniana' => array(
	
			'Viola riviniana.jpg' => 'overzicht',
	
			'Viola riviniana 2.jpg' => 'vergroot; a = bloem',
	
			'Viola riviniana 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Mercurialis perennis' => array(
	
			'Mercurialis perennis.jpg' => 'overzicht; a = vrucht',
	
			'Mercurialis perennis 2.jpg' => 'vergroot',
	
			'Mercurialis perennis 3.jpg' => 'foto',
	
			'Mercurialis perennis 4.jpg' => 'foto'
	
		),
	
		'Mercurialis annua' => array(
	
			'Mercurialis annua.jpg' => 'overzicht; a = vrucht',
	
			'Mercurialis annua 2.jpg' => 'vergroot',
	
			'Mercurialis annua  3.jpg' => 'foto'
	
		),
	
		'Ricinus communis' => array(
	
			'Ricinus communis.jpg' => 'overzicht foto',
	
			'Ricinus communis 3.jpg' => 'foto, in vrucht',
	
			'Ricinus communis 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Euphorbia maculata' => array(
	
			'Euphorbia maculata.jpg' => 'overzicht',
	
			'Euphorbia maculata 2.jpg' => 'tekening, habitus en vrucht',
	
			'Euphorbia maculata 3.jpg' => 'foto'
	
		),
	
		'Euphorbia helioscopia' => array(
	
			'Euphorbia helioscopia.jpg' => 'overzicht; a = cyathium, b = dsn. cyathium, c = zaad',
	
			'Euphorbia helioscopia 2.jpg' => 'vergroot',
	
			'Euphorbia helioscopia 3.jpg' => 'foto',
	
			'Euphorbia helioscopia 4.jpg' => 'foto'
	
		),
	
		'Euphorbia epithymoides' => array(
	
			'Euphorbia epithymoides.jpg' => 'overzicht; a = cyathium',
	
			'Euphorbia epithymoides 2.jpg' => 'vergroot',
	
			'Euphorbia epithymoides 3.jpg' => 'foto',
	
			'Euphorbia epithymoides 4.jpg' => 'foto'
	
		),
	
		'Euphorbia stricta' => array(
	
			'Euphorbia stricta.jpg' => 'overzicht',
	
			'Euphorbia stricta 2.jpg' => 'vergroot',
	
			'Euphorbia stricta 3.jpg' => 'foto',
	
			'Euphorbia stricta 4.jpg' => 'foto, habitus, in bloei'
	
		),
	
		'Euphorbia platyphyllos' => array(
	
			'Euphorbia platyphyllos.jpg' => 'overzicht; a = cyathium',
	
			'Euphorbia platyphyllos 2.jpg' => 'vergroot',
	
			'Euphorbia platyphyllos 3.jpg' => 'foto',
	
			'Euphorbia platyphyllos 4.jpg' => 'foto'
	
		),
	
		'Euphorbia lathyrus' => array(
	
			'Euphorbia lathyrus.jpg' => 'overzicht',
	
			'Euphorbia lathyrus 2.jpg' => 'vergroot',
	
			'Euphorbia lathyrus 3.jpg' => 'foto',
	
			'Euphorbia lathyrus 4.jpg' => 'foto'
	
		),
	
		'Euphorbia paralias' => array(
	
			'Euphorbia paralias.jpg' => 'overzicht',
	
			'Euphorbia paralias 2.jpg' => 'vergroot'
	
		),
	
		'Euphorbia peplus' => array(
	
			'Euphorbia peplus.jpg' => 'overzicht; a = cyathium, b = zaad',
	
			'Euphorbia peplus 2.jpg' => 'vergroot',
	
			'Euphorbia peplus 3.jpg' => 'foto'
	
		),
	
		'Euphorbia exigua' => array(
	
			'Euphorbia exigua.jpg' => 'overzicht',
	
			'Euphorbia exigua 2.jpg' => 'vergroot',
	
			'Euphorbia exigua 3.jpg' => 'foto',
	
			'Euphorbia exigua 4.jpg' => 'foto'
	
		),
	
		'Euphorbia palustris' => array(
	
			'Euphorbia palustris.jpg' => 'overzicht',
	
			'Euphorbia palustris 2.jpg' => 'vergroot',
	
			'Euphorbia palustris 3.jpg' => 'foto'
	
		),
	
		'Euphorbia amygdaloides' => array(
	
			'Euphorbia amygdaloides.jpg' => 'overzicht',
	
			'Euphorbia amygdaloides 2.jpg' => 'vergroot',
	
			'Euphorbia amygdaloides 3.jpg' => 'foto',
	
			'Euphorbia amygdaloides 4.jpg' => 'foto'
	
		),
	
		'Euphorbia seguieriana' => array(
	
			'Euphorbia seguieriana.jpg' => 'overzicht; a = cyathium',
	
			'Euphorbia seguieriana 2.jpg' => 'vergroot',
	
			'Euphorbia seguieriana 3.jpg' => 'foto',
	
			'Euphorbia seguieriana 4.jpg' => 'foto'
	
		),
	
		'Euphorbia cyparissias' => array(
	
			'Euphorbia cyparissias.jpg' => 'overzicht',
	
			'Euphorbia cyparissias 2.jpg' => 'vergroot',
	
			'Euphorbia cyparissias 3.jpg' => 'foto',
	
			'Euphorbia cyparissias 4.jpg' => 'foto'
	
		),
	
		'Euphorbia esula' => array(
	
			'Euphorbia esula.jpg' => 'overzicht; a = cyathium',
	
			'Euphorbia esula 2.jpg' => 'vergroot',
	
			'Euphorbia esula 3.jpg' => 'foto'
	
		),
	
		'Hypericum elodes' => array(
	
			'Hypericum elodes.jpg' => 'overzicht; a = bloem',
	
			'Hypericum elodes 2.jpg' => 'vergroot',
	
			'Hypericum elodes 3.jpg' => 'foto',
	
			'Hypericum elodes 4.jpg' => 'foto'
	
		),
	
		'Hypericum hirsutum' => array(
	
			'Hypericum hirsutum.jpg' => 'overzicht',
	
			'Hypericum hirsutum 2.jpg' => 'vergroot',
	
			'Hypericum hirsutum 3.jpg' => 'foto',
	
			'Hypericum hirsutum 4.jpg' => 'foto',
	
			'Hypericum hirsutum 5.jpg' => 'foto',
	
			'Hypericum hirsutum 6.jpg' => 'foto'
	
		),
	
		'Hypericum canadense' => array(
	
			'Hypericum canadense.jpg' => 'overzicht foto',
	
			'Hypericum canadense 3.jpg' => 'foto'
	
		),
	
		'Hypericum androsaemum' => array(
	
			'Hypericum androsaemum.jpg' => 'overzicht',
	
			'Hypericum androsaemum 2.jpg' => 'vergroot',
	
			'Hypericum androsaemum 3.jpg' => 'foto'
	
		),
	
		'Hypericum humifusum' => array(
	
			'Hypericum humifusum.jpg' => 'overzicht',
	
			'Hypericum humifusum 2.jpg' => 'vergroot',
	
			'Hypericum humifusum 3.jpg' => 'foto'
	
		),
	
		'Hypericum pulchrum' => array(
	
			'Hypericum pulchrum.jpg' => 'overzicht; a = kelk',
	
			'Hypericum pulchrum 2.jpg' => 'vergroot',
	
			'Hypericum pulchrum 3.jpg' => 'foto'
	
		),
	
		'Hypericum montanum' => array(
	
			'Hypericum montanum.jpg' => 'overzicht; a = kelk',
	
			'Hypericum montanum 2.jpg' => 'vergroot',
	
			'Hypericum montanum 3.jpg' => 'foto'
	
		),
	
		'Hypericum tetrapterum' => array(
	
			'Hypericum tetrapterum.jpg' => 'overzicht',
	
			'Hypericum tetrapterum 2.jpg' => 'vergroot',
	
			'Hypericum tetrapterum 3.jpg' => 'foto'
	
		),
	
		'Hypericum perforatum' => array(
	
			'Hypericum perforatum.jpg' => 'overzicht; a = bloem, b = dws.dsn. vruchtbeginsel, c = doosvrucht',
	
			'Hypericum perforatum 2.jpg' => 'vergroot',
	
			'Hypericum perforatum 3.jpg' => 'foto',
	
			'Hypericum perforatum 4.jpg' => 'foto'
	
		),
	
		'Hypericum desetangsii(x)' => array(
	
			'Hypericum desetangsii(x).jpg' => 'overzicht',
	
			'Hypericum desetangsii(x) 2.jpg' => 'foto'
	
		),
	
		'Hypericum maculatum' => array(
	
			'Hypericum maculatum.jpg' => 'overzicht',
	
			'Hypericum maculatum 2.jpg' => 'vergroot',
	
			'Hypericum maculatum 3.jpg' => 'foto'
	
		),
	
		'Hypericum maculatum subsp. obtusiusculum' => array(
	
			'Hypericum maculatum obtus.jpg' => 'overzicht; a = dsn. stengel, b = doosvrucht',
	
			'Hypericum maculatum obtus 2.jpg' => 'vergroot',
	
			'Hypericum maculatum obtus 3.jpg' => 'foto'
	
		),
	
		'Hypericum maculatum subsp. maculatum' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Linum catharticum' => array(
	
			'Linum catharticum.jpg' => 'overzicht',
	
			'Linum catharticum 2.jpg' => 'vergroot',
	
			'Linum catharticum 3.jpg' => 'foto',
	
			'Linum catharticum 4.jpg' => 'foto'
	
		),
	
		'Linum usitatissimum' => array(
	
			'Linum usitatissimum.jpg' => 'overzicht',
	
			'Linum usitatissimum 2.jpg' => 'vergroot; a = bloem zonder kelk- en kroonbladen, b = vruchtkelk, c = dsn. vrucht',
	
			'Linum usitatissimum 3.jpg' => 'foto'
	
		),
	
		'Radiola linoides' => array(
	
			'Radiola linoides.jpg' => 'overzicht; a = bloem, b = dsn. vrucht',
	
			'Radiola linoides 2.jpg' => 'vergroot',
	
			'Radiola linoides 3.jpg' => 'foto, habitus bloeiend',
	
			'Radiola linoides 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Elatine hydropiper' => array(
	
			'Elatine hydropiper.jpg' => 'overzicht; a = bloem van boven, b = dsn. bloem',
	
			'Elatine hydropiper 2.jpg' => 'vergroot',
	
			'Elatine hydropiper 3.jpg' => 'foto'
	
		),
	
		'Elatine hexandra' => array(
	
			'Elatine hexandra.jpg' => 'overzicht; a = bloem, b = vrucht',
	
			'Elatine hexandra 2.jpg' => 'vergroot',
	
			'Elatine hexandra 3.jpg' => 'foto'
	
		),
	
		'Elatine triandra' => array(
	
			'Elatine triandra.jpg' => 'overzicht; a = bloem',
	
			'Elatine triandra 2.jpg' => 'vergroot'
	
		),
	
		'Oxalis acetosella' => array(
	
			'Oxalis acetosella.jpg' => 'overzicht',
	
			'Oxalis acetosella 2.jpg' => 'vergroot; a = dsn. bloem, b = vrucht',
	
			'Oxalis acetosella 3.jpg' => 'foto',
	
			'Oxalis acetosella 4.jpg' => 'foto'
	
		),
	
		'Oxalis stricta' => array(
	
			'Oxalis stricta.jpg' => 'overzicht',
	
			'Oxalis stricta 2.jpg' => 'vergroot; a = openspringende vrucht',
	
			'Oxalis stricta 3.jpg' => 'foto'
	
		),
	
		'Oxalis dillenii' => array(
	
			'Oxalis dillenii.jpg' => 'overzicht',
	
			'Oxalis dillenii 2.jpg' => 'tekening, steulblaadje en zaad'
	
		),
	
		'Oxalis corniculata' => array(
	
			'Oxalis corniculata.jpg' => 'overzicht',
	
			'Oxalis corniculata 2.jpg' => 'vergroot',
	
			'Oxalis corniculata 3.jpg' => 'foto',
	
			'Oxalis corniculata 4.jpg' => 'foto',
	
			'Oxalis corniculata 5.jpg' => 'tekening, steunblaadje en zaad'
	
		),
	
		'Laburnum anagyroides' => array(
	
			'Laburnum anagyroides.jpg' => 'overzicht',
	
			'Laburnum anagyroides 2.jpg' => 'vergroot',
	
			'Laburnum anagyroides 3.jpg' => 'foto'
	
		),
	
		'Cytisus scoparius' => array(
	
			'Cytisus scoparius.jpg' => 'overzicht',
	
			'Cytisus scoparius 2.jpg' => 'vergroot; a = dsn. bloem, b = bloem zonder de kroonbladen, c = geopende vrucht',
	
			'Cytisus scoparius 3.jpg' => 'foto'
	
		),
	
		'Genista pilosa' => array(
	
			'Genista pilosa.jpg' => 'overzicht; a = bloem en detail stijl',
	
			'Genista pilosa 2.jpg' => 'vergroot',
	
			'Genista pilosa 3.jpg' => 'foto'
	
		),
	
		'Genista tinctoria' => array(
	
			'Genista tinctoria.jpg' => 'overzicht',
	
			'Genista tinctoria 2.jpg' => 'vergroot',
	
			'Genista tinctoria 3.jpg' => 'foto'
	
		),
	
		'Genista anglica' => array(
	
			'Genista anglica.jpg' => 'overzicht',
	
			'Genista anglica 2.jpg' => 'vergroot',
	
			'Genista anglica 3.jpg' => 'foto'
	
		),
	
		'Genista germanica' => array(
	
			'Genista germanica.jpg' => 'overzicht',
	
			'Genista germanica 2.jpg' => 'vergroot',
	
			'Genista germanica 3.jpg' => 'foto',
	
			'Genista germanica 4.jpg' => 'foto'
	
		),
	
		'Ulex europaeus' => array(
	
			'Ulex europaeus.jpg' => 'overzicht; a = bloem',
	
			'Ulex europaeus 2.jpg' => 'vergroot',
	
			'Ulex europaeus 3.jpg' => 'foto, in bloei',
	
			'Ulex europaeus 4.jpg' => 'foto, in bloei'
	
		),
	
		'Lupinus angustifolius' => array(
	
			'Lupinus angustifolius.jpg' => 'overzicht',
	
			'Lupinus angustifolius 2.jpg' => 'vergroot',
	
			'Lupinus angustifolius 3.jpg' => 'foto',
	
			'Lupinus angustifolius 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Lupinus luteus' => array(
	
			'Lupinus luteus.jpg' => 'overzicht',
	
			'Lupinus luteus 2.jpg' => 'vergroot',
	
			'Lupinus luteus 3.jpg' => 'foto'
	
		),
	
		'Lupinus polyphyllus' => array(
	
			'Lupinus polyphyllus.jpg' => 'overzicht foto',
	
			'Lupinus polyphyllus 3.jpg' => 'foto',
	
			'Lupinus polyphyllus 4.jpg' => 'foto'
	
		),
	
		'Robinia pseudoacacia' => array(
	
			'Robinia pseudoacacia.jpg' => 'overzicht',
	
			'Robinia pseudoacacia 2.jpg' => 'vergroot',
	
			'Robinia pseudoacacia 3.jpg' => 'foto, bloeiwijzen',
	
			'Robinia pseudoacacia 4.jpg' => 'foto, jong boompje'
	
		),
	
		'Galega officinalis' => array(
	
			'Galega officinalis.jpg' => 'overzicht',
	
			'Galega officinalis 2.jpg' => 'vergroot',
	
			'Galega officinalis 4.jpg' => 'foto',
	
			'Galega officinalis 5.jpg' => 'foto',
	
			'Galega officinalis 6.jpg' => 'foto',
	
			'Galega officinalis 7.jpg' => 'foto'
	
		),
	
		'Colutea arborescens' => array(
	
			'Colutea arborescens.jpg' => 'overzicht',
	
			'Colutea arborescens 2.jpg' => 'vergroot; a = dsn. bloem, b = idem, zonder kroonbladen, c = stamper',
	
			'Colutea arborescens 3.jpg' => 'foto',
	
			'Colutea arborescens 4.jpg' => 'foto'
	
		),
	
		'Colutea media(x)' => array(
	
			'Geen illustratie.jpg' => '',
	
			'Colutea orientalis 2.jpg' => 'vergroot, een van de ouders'
	
		),
	
		'Astragalus glycyphyllos' => array(
	
			'Astragalus glycyphyllos.jpg' => 'overzicht; a = vruchtwijze',
	
			'Astragalus glycyphyllos 2.jpg' => 'vergroot',
	
			'Astragalus glycyphyllos 3.jpg' => 'foto',
	
			'Astragalus glycyphyllos 4.jpg' => 'foto'
	
		),
	
		'Phaseolus vulgaris' => array(
	
			'Phaseolus vulgaris.jpg' => 'overzicht',
	
			'Phaseolus vulgaris 2.jpg' => 'vergroot',
	
			'Phaseolus vulgaris 3.jpg' => 'foto'
	
		),
	
		'Phaseolus coccineus' => array(
	
			'Phaseolus coccineus.jpg' => 'overzicht',
	
			'Phaseolus coccineus 2.jpg' => 'vergroot',
	
			'Phaseolus coccineus 3.jpg' => 'foto'
	
		),
	
		'Glycine max' => array(
	
			'Glycine max.jpg' => 'overzicht',
	
			'Glycine max 2.jpg' => 'vergroot',
	
			'Glycine max 3.jpg' => 'foto',
	
			'Glycine max 4.jpg' => 'foto'
	
		),
	
		'Cicer arietinum' => array(
	
			'Cicer arietinum.jpg' => 'overzicht',
	
			'Cicer arietinum 2.jpg' => 'vergroot',
	
			'Cicer arietinum 3.jpg' => 'foto'
	
		),
	
		'Vicia hirsuta' => array(
	
			'Vicia hirsuta.jpg' => 'overzicht',
	
			'Vicia hirsuta 2.jpg' => 'vergroot',
	
			'Vicia hirsuta 3.jpg' => 'foto, habitus bloeiend',
	
			'Vicia hirsuta 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Vicia tetrasperma' => array(
	
			'Vicia tetrasperma.jpg' => 'overzicht',
	
			'Vicia tetrasperma 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Vicia tetrasperma subsp. tetrasperma' => array(
	
			'Vicia tetrasperma tetrasperm.jpg' => 'overzicht; a = bloem, b = vrucht',
	
			'Vicia tetrasperma tetrasp 2.jpg' => 'vergroot'
	
		),
	
		'Vicia tetrasperma subsp. gracilis' => array(
	
			'Vicia tetrasperma gracilis.jpg' => 'overzicht',
	
			'Vicia tetrasperma gracilis 2.jpg' => 'vergroot'
	
		),
	
		'Vicia villosa' => array(
	
			'Vicia villosa.jpg' => 'overzicht; a = kelk',
	
			'Vicia villosa 2.jpg' => 'vergroot',
	
			'Vicia villosa 3.jpg' => 'foto, habitus bloeiend',
	
			'Vicia villosa 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Vicia cracca' => array(
	
			'Vicia cracca.jpg' => 'overzicht',
	
			'Vicia cracca 2.jpg' => 'vergroot',
	
			'Vicia cracca 3.jpg' => 'foto, habitus bloeiend',
	
			'Vicia cracca 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Vicia tenuifolia' => array(
	
			'Vicia tenuifolia.jpg' => 'overzicht',
	
			'Vicia tenuifolia 2.jpg' => 'vergroot',
	
			'Vicia tenuifolia 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Vicia faba' => array(
	
			'Vicia faba.jpg' => 'overzicht',
	
			'Vicia faba 2.jpg' => 'vergroot',
	
			'Vicia faba 3.jpg' => 'foto, in bloei',
	
			'Vicia faba 4.jpg' => 'foto, in bloei',
	
			'Vicia faba 5.jpg' => 'foto, in vrucht'
	
		),
	
		'Vicia lathyroides' => array(
	
			'Vicia lathyroides.jpg' => 'overzicht',
	
			'Vicia lathyroides 2.jpg' => 'vergroot',
	
			'Vicia lathyroides 3.jpg' => 'foto, habitus bloeiend',
	
			'Vicia lathyroides 4.jpg' => 'foto, vrucht en bloemen',
	
			'Vicia lathyroides 5.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Vicia lutea' => array(
	
			'Vicia lutea.jpg' => 'overzicht',
	
			'Vicia lutea 2.jpg' => 'vergroot',
	
			'Vicia lutea 3.jpg' => 'foto, bloeiend',
	
			'Vicia lutea 4.jpg' => 'foto, bloemen en vruchten'
	
		),
	
		'Vicia sepium' => array(
	
			'Vicia sepium.jpg' => 'overzicht',
	
			'Vicia sepium 2.jpg' => 'vergroot; a = bloemknop, b = dsn. bloem, c = steunblaadjes',
	
			'Vicia sepium 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Vicia sativa' => array(
	
			'Vicia sativa sativa.jpg' => 'overzicht',
	
			'Vicia sativa sativa 2.jpg' => 'vergroot; a = bloem, b = vruchtwijze, subsp. sativa',
	
			'Vicia sativa nigra 2.jpg' => 'vergroot, subsp. nigra'
	
		),
	
		'Vicia sativa subsp. nigra' => array(
	
			'Vicia sativa nigra.jpg' => 'overzicht',
	
			'Vicia sativa nigra 2.jpg' => 'vergroot',
	
			'Vicia sativa nigra 3.jpg' => 'foto, bloeiend',
	
			'Vicia sativa nigra 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Vicia sativa subsp. segetalis' => array(
	
			'Vicia sativa segetalis.jpg' => 'overzicht',
	
			'Vicia sativa segetalis 2.jpg' => 'foto, bloeiend',
	
			'Vicia sativa segetalis 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Vicia sativa subsp. sativa' => array(
	
			'Vicia sativa sativa.jpg' => 'overzicht',
	
			'Vicia sativa sativa 2.jpg' => 'vergroot; a = bloem, b = vruchtwijze',
	
			'Vicia sativa sativa 3.jpg' => 'foto, bloemen',
	
			'Vicia sativa sativa 4.jpg' => 'foto, bloemen'
	
		),
	
		'Lens culinaris' => array(
	
			'Lens culinaris.jpg' => 'overzicht; a = bloem, b = dsn. bloem, c = vrucht',
	
			'Lens culinaris 2.jpg' => 'vergroot'
	
		),
	
		'Lathyrus nissolia' => array(
	
			'Lathyrus nissolia.jpg' => 'overzicht',
	
			'Lathyrus nissolia 2.jpg' => 'vergroot',
	
			'Lathyrus nissolia 3.jpg' => 'foto',
	
			'Lathyrus nissolia 4.jpg' => 'foto'
	
		),
	
		'Lathyrus linifolius' => array(
	
			'Lathyrus linifolius.jpg' => 'overzicht; a = opengesprongen vrucht',
	
			'Lathyrus linifolius 2.jpg' => 'vergroot',
	
			'Lathyrus linifolius 3.jpg' => 'foto',
	
			'Lathyrus linifolius 4.jpg' => 'foto',
	
			'Lathyrus linifolius 5.jpg' => 'foto'
	
		),
	
		'Lathyrus niger' => array(
	
			'Lathyrus niger.jpg' => 'overzicht',
	
			'Lathyrus niger 2.jpg' => 'vergroot',
	
			'Lathyrus niger 3.jpg' => 'foto',
	
			'Lathyrus niger 4.jpg' => 'foto'
	
		),
	
		'Lathyrus aphaca' => array(
	
			'Lathyrus aphaca.jpg' => 'overzicht',
	
			'Lathyrus aphaca 2.jpg' => 'vergroot',
	
			'Lathyrus aphaca 3.jpg' => 'foto, bloeiend',
	
			'Lathyrus aphaca 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Lathyrus palustris' => array(
	
			'Lathyrus palustris.jpg' => 'overzicht',
	
			'Lathyrus palustris 2.jpg' => 'vergroot',
	
			'Lathyrus palustris 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Lathyrus japonicus' => array(
	
			'Lathyrus japonicus.jpg' => 'overzicht; a = vrucht',
	
			'Lathyrus japonicus 2.jpg' => 'vergroot',
	
			'Lathyrus japonicus 3.jpg' => 'foto',
	
			'Lathyrus japonicus 4.jpg' => 'foto'
	
		),
	
		'Lathyrus pratensis' => array(
	
			'Lathyrus pratensis.jpg' => 'overzicht',
	
			'Lathyrus pratensis 2.jpg' => 'vergroot',
	
			'Lathyrus pratensis 3.jpg' => 'foto, bloeiend',
	
			'Lathyrus pratensis 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Lathyrus tuberosus' => array(
	
			'Lathyrus tuberosus.jpg' => 'overzicht',
	
			'Lathyrus tuberosus 2.jpg' => 'vergroot',
	
			'Lathyrus tuberosus 3.jpg' => 'foto',
	
			'Lathyrus tuberosus 4.jpg' => 'foto'
	
		),
	
		'Lathyrus hirsutus' => array(
	
			'Lathyrus hirsutus.jpg' => 'overzicht',
	
			'Lathyrus hirsutus 2.jpg' => 'vergroot',
	
			'Lathyrus hirsutus 3.jpg' => 'foto'
	
		),
	
		'Lathyrus sylvestris' => array(
	
			'Lathyrus sylvestris.jpg' => 'overzicht',
	
			'Lathyrus sylvestris 2.jpg' => 'vergroot',
	
			'Lathyrus sylvestris 3.jpg' => 'foto'
	
		),
	
		'Lathyrus latifolius' => array(
	
			'Lathyrus latifolius.jpg' => 'overzicht',
	
			'Lathyrus latifolius 2.jpg' => 'vergroot',
	
			'Lathyrus latifolius 3.jpg' => 'foto'
	
		),
	
		'Pisum sativum' => array(
	
			'Pisum sativum.jpg' => 'overzicht; a = dsn. bloem',
	
			'Pisum sativum 2.jpg' => 'vergroot',
	
			'Pisum sativum 3.jpg' => 'foto'
	
		),
	
		'Ononis repens' => array(
	
			'Ononis repens repens.jpg' => 'overzicht',
	
			'Ononis repens repens 2.jpg' => 'vergroot, subsp. repens',
	
			'Ononis repens spinosa 2.jpg' => 'vergroot; a  = dsn. bloem, b = vruchtkelk, subsp. spinosa'
	
		),
	
		'Ononis repens subsp. spinosa' => array(
	
			'Ononis repens spinosa.jpg' => 'overzicht',
	
			'Ononis repens spinosa 2.jpg' => 'vergroot; a  = dsn. bloem, b = vruchtkelk',
	
			'Ononis repens spinosa 3.jpg' => 'foto',
	
			'Ononis repens spinosa 4.jpg' => 'foto',
	
			'Ononis repens spinosa 5.jpg' => 'foto'
	
		),
	
		'Ononis repens subsp. repens' => array(
	
			'Ononis repens repens.jpg' => 'overzicht',
	
			'Ononis repens repens 2.jpg' => 'vergroot',
	
			'Ononis repens repens 3.jpg' => 'foto',
	
			'Ononis repens repens 4.jpg' => 'foto'
	
		),
	
		'Melilotus albus' => array(
	
			'Melilotus albus.jpg' => 'overzicht',
	
			'Melilotus albus 2.jpg' => 'vergroot',
	
			'Melilotus albus 3.jpg' => 'foto'
	
		),
	
		'Melilotus indicus' => array(
	
			'Melilotus indicus.jpg' => 'overzicht',
	
			'Melilotus indicus 2.jpg' => 'vergroot',
	
			'Melilotus indicus 3.jpg' => 'foto'
	
		),
	
		'Melilotus altissimus' => array(
	
			'Melilotus altissimus.jpg' => 'overzicht',
	
			'Melilotus altissimus 2.jpg' => 'vergroot',
	
			'Melilotus altissimus 3.jpg' => 'foto'
	
		),
	
		'Melilotus officinalis' => array(
	
			'Melilotus officinalis.jpg' => 'overzicht',
	
			'Melilotus officinalis 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem, c = vruchtkelk',
	
			'Melilotus officinalis 3.jpg' => 'foto'
	
		),
	
		'Trigonella foenum-graecum' => array(
	
			'Trigonella foenum-graecum.jpg' => 'overzicht',
	
			'Trigonella foenum-graecum 2.jpg' => 'vergroot; a = opengesneden vrucht',
	
			'Trigonella foenum-graecum 3.jpg' => 'foto, habitus in vrucht'
	
		),
	
		'Medicago sativa' => array(
	
			'Medicago sativa.jpg' => 'overzicht',
	
			'Medicago sativa 2.jpg' => 'vergroot; a = bloem, b = dsn, bloem, c = vruchttros',
	
			'Medicago sativa 3.jpg' => 'foto',
	
			'Medicago sativa 4.jpg' => 'foto; in vrucht'
	
		),
	
		'Medicago falcata' => array(
	
			'Medicago falcata.jpg' => 'overzicht; a = vrucht',
	
			'Medicago falcata 2.jpg' => 'vergroot',
	
			'Medicago falcata 3.jpg' => 'foto',
	
			'Medicago falcata 4.jpg' => 'bloeiwijze en vrucht'
	
		),
	
		'Medicago varia(x)' => array(
	
			'Medicago varia(x).jpg' => 'overzicht',
	
			'Medicago varia(x) 2.jpg' => 'vergroot',
	
			'Medicago varia(x) 3.jpg' => 'foto',
	
			'Medicago varia(x) 4.jpg' => 'foto',
	
			'Medicago varia(x) 5.jpg' => 'foto'
	
		),
	
		'Medicago lupulina' => array(
	
			'Medicago lupulina.jpg' => 'overzicht; a = vruchtkelk',
	
			'Medicago lupulina 2.jpg' => 'vergroot',
	
			'Medicago lupulina 3.jpg' => 'foto'
	
		),
	
		'Medicago arabica' => array(
	
			'Medicago arabica.jpg' => 'overzicht',
	
			'Medicago arabica 2.jpg' => 'vergroot',
	
			'Medicago arabica 3.jpg' => 'foto',
	
			'Medicago arabica 4.jpg' => 'vrucht details'
	
		),
	
		'Medicago minima' => array(
	
			'Medicago minima.jpg' => 'overzicht',
	
			'Medicago minima 2.jpg' => 'vergroot; a = vrucht van onderen, b = vrucht van boven, c = vruchtwinding van opzij',
	
			'Medicago minima 3.jpg' => 'foto',
	
			'Medicago minima 4.jpg' => 'vrucht details'
	
		),
	
		'Medicago polymorpha' => array(
	
			'Medicago polymorpha.jpg' => 'overzicht',
	
			'Medicago polymorpha 2.jpg' => 'vergroot',
	
			'Medicago polymorpha 3.jpg' => 'vrucht details'
	
		),
	
		'Trifolium campestre' => array(
	
			'Trifolium campestre.jpg' => 'overzicht; a = bloem',
	
			'Trifolium campestre 2.jpg' => 'vergroot',
	
			'Trifolium campestre 3.jpg' => 'foto, habitus bloeiend en in vrucht',
	
			'Trifolium campestre 4.jpg' => 'foto, habitus bloeiend en in vrucht',
	
			'Trifolium campestre 5.jpg' => 'tekening, habitus en bloem'
	
		),
	
		'Trifolium micranthum' => array(
	
			'Trifolium micranthum.jpg' => 'overzicht; a = bloemtros',
	
			'Trifolium micranthum 2.jpg' => 'vergroot',
	
			'Trifolium micranthum 3.jpg' => 'foto, habitus in bloei',
	
			'Trifolium micranthum 4.jpg' => 'foto, in vrucht',
	
			'Trifolium micranthum 5.jpg' => 'tekening, habitus en bloeiwijze'
	
		),
	
		'Trifolium dubium' => array(
	
			'Trifolium dubium.jpg' => 'overzicht; a = bloem',
	
			'Trifolium dubium 2.jpg' => 'vergroot',
	
			'Trifolium dubium 3.jpg' => 'foto, habitus bloeiend',
	
			'Trifolium dubium 4.jpg' => 'foto, habitus bloeiend',
	
			'Trifolium dubium 5.jpg' => 'tekening, habitus, bloeiwijze en bloem'
	
		),
	
		'Trifolium ornithopodioides' => array(
	
			'Trifolium ornithopodioides.jpg' => 'overzicht foto',
	
			'Trifolium ornithopodioides 3.jpg' => 'foto, habitus in bloei'
	
		),
	
		'Trifolium subterraneum' => array(
	
			'Trifolium subterraneum.jpg' => 'overzicht',
	
			'Trifolium subterraneum 2.jpg' => 'vergroot',
	
			'Trifolium subterraneum 3.jpg' => 'foto, habitus in bloei',
	
			'Trifolium subterraneum 4.jpg' => 'foto, habitus in bloei'
	
		),
	
		'Trifolium repens' => array(
	
			'Trifolium repens.jpg' => 'overzicht',
	
			'Trifolium repens 2.jpg' => 'vergroot; a = bloem van opzij, b = bloem van voren, c = vruchthoofdje',
	
			'Trifolium repens 3.jpg' => 'foto, habitus in bloei',
	
			'Trifolium repens 4.jpg' => 'foto, habitus in bloei'
	
		),
	
		'Trifolium fragiferum' => array(
	
			'Trifolium fragiferum.jpg' => 'overzicht; a = bloem, b  = vruchtkelk',
	
			'Trifolium fragiferum 2.jpg' => 'vergroot',
	
			'Trifolium fragiferum 3.jpg' => 'foto, habitus in bloei',
	
			'Trifolium fragiferum 4.jpg' => 'tekening, habitus en bloeiwijze, in bloei en in vrucht'
	
		),
	
		'Trifolium incarnatum' => array(
	
			'Trifolium incarnatum.jpg' => 'overzicht; a = bloem',
	
			'Trifolium incarnatum 2.jpg' => 'vergroot',
	
			'Trifolium incarnatum 3.jpg' => 'foto, habitus in bloei'
	
		),
	
		'Trifolium diffusum' => array(
	
			'Trifolium diffusum.jpg' => 'overzicht foto',
	
			'Trifolium diffusum 3.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Trifolium alexandrinum' => array(
	
			'Trifolium alexandrinum.jpg' => 'overzicht',
	
			'Trifolium alexandrinum 2.jpg' => 'vergroot'
	
		),
	
		'Trifolium pratense' => array(
	
			'Trifolium pratense.jpg' => 'overzicht',
	
			'Trifolium pratense 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem, c = kelk',
	
			'Trifolium pratense 3.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Trifolium medium' => array(
	
			'Trifolium medium.jpg' => 'overzicht; a = bloem',
	
			'Trifolium medium 2.jpg' => 'vergroot',
	
			'Trifolium medium 3.jpg' => 'foto, habitus in bloei',
	
			'Trifolium medium 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Trifolium striatum' => array(
	
			'Trifolium striatum.jpg' => 'overzicht; a = vruchtkelk',
	
			'Trifolium striatum 2.jpg' => 'vergroot',
	
			'Trifolium striatum 3.jpg' => 'foto, habitus in bloei'
	
		),
	
		'Trifolium scabrum' => array(
	
			'Trifolium scabrum.jpg' => 'overzicht; a = bloem',
	
			'Trifolium scabrum 2.jpg' => 'vergroot',
	
			'Trifolium scabrum 3.jpg' => 'foto, habitus in bloei en vrucht'
	
		),
	
		'Trifolium arvense' => array(
	
			'Trifolium arvense.jpg' => 'overzicht; a = bloem, b = vruchtkelk',
	
			'Trifolium arvense 2.jpg' => 'vergroot',
	
			'Trifolium arvense 3.jpg' => 'foto, habitus bloeiend',
	
			'Trifolium arvense 4.jpg' => 'foto, bloemhoofdjes'
	
		),
	
		'Trifolium hybridum' => array(
	
			'Trifolium hybridum.jpg' => 'overzicht',
	
			'Trifolium hybridum 2.jpg' => 'vergroot',
	
			'Trifolium hybridum 3.jpg' => 'foto, habitus in bloei',
	
			'Trifolium hybridum 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Trifolium resupinatum' => array(
	
			'Trifolium resupinatum.jpg' => 'overzicht',
	
			'Trifolium resupinatum 2.jpg' => 'vergroot',
	
			'Trifolium resupinatum 3.jpg' => 'foto, habitus in bloei',
	
			'Trifolium resupinatum 4.jpg' => 'foto, habitus in bloei'
	
		),
	
		'Lotus pedunculatus' => array(
	
			'Lotus pedunculatus.jpg' => 'overzicht',
	
			'Lotus pedunculatus 2.jpg' => 'vergroot',
	
			'Lotus pedunculatus 3.jpg' => 'foto'
	
		),
	
		'Lotus glaber' => array(
	
			'Lotus glaber.jpg' => 'overzicht',
	
			'Lotus glaber 2.jpg' => 'vergroot',
	
			'Lotus glaber 3.jpg' => 'foto'
	
		),
	
		'Lotus corniculatus' => array(
	
			'Lotus corniculatus.jpg' => 'overzicht',
	
			'Lotus corniculatus 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem, c = vrucht',
	
			'Lotus corniculatus 3.jpg' => 'foto'
	
		),
	
		"Lotus 'Sativus'" => array(
	
			"Lotus 'Sativus'.jpg" => 'overzicht',
	
			"Lotus 'Sativus' 2.jpg" => 'tekening, habitus',
	
			"Lotus 'Sativus' 3.jpg" => 'foto'
	
		),
	
		'Tetragonolobus maritimus' => array(
	
			'Tetragonolobus maritimus.jpg' => 'overzicht',
	
			'Tetragonolobus maritimus 2.jpg' => 'vergroot',
	
			'Tetragonolobus maritimus 3.jpg' => 'foto, bloeiend',
	
			'Tetragonolobus maritimus 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Anthyllis vulneraria' => array(
	
			'Anthyllis vulneraria.jpg' => 'overzicht; a = bloem, b = dsn. bloem zonder kelk',
	
			'Anthyllis vulneraria 2.jpg' => 'vergroot',
	
			'Anthyllis vulneraria 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Ornithopus compressus' => array(
	
			'Ornithopus compressus.jpg' => 'overzicht foto',
	
			'Ornithopus compressus 3.jpg' => 'foto'
	
		),
	
		'Ornithopus sativus' => array(
	
			'Ornithopus sativus.jpg' => 'overzicht',
	
			'Ornithopus sativus 2.jpg' => 'vergroot',
	
			'Ornithopus sativus 3.jpg' => 'foto',
	
			'Ornithopus sativus 4.jpg' => 'foto'
	
		),
	
		'Ornithopus perpusillus' => array(
	
			'Ornithopus perpusillus.jpg' => 'overzicht',
	
			'Ornithopus perpusillus 2.jpg' => 'vergroot',
	
			'Ornithopus perpusillus 3.jpg' => 'foto',
	
			'Ornithopus perpusillus 4.jpg' => 'foto'
	
		),
	
		'Securigera varia' => array(
	
			'Securigera varia.jpg' => 'overzicht',
	
			'Securigera varia 2.jpg' => 'vergroot; a = kroonbladen; b = vruchtwijze; c = dsn. vrucht',
	
			'Securigera varia 3.jpg' => 'foto, bloeiend',
	
			'Securigera varia 4.jpg' => 'foto, bloeihoofdje'
	
		),
	
		'Hippocrepis comosa' => array(
	
			'Hippocrepis comosa.jpg' => 'overzicht; a = vruchtdragende tak, b = bloem',
	
			'Hippocrepis comosa 2.jpg' => 'vergroot',
	
			'Hippocrepis comosa 3.jpg' => 'foto'
	
		),
	
		'Onobrychis viciifolia' => array(
	
			'Onobrychis viciifolia.jpg' => 'overzicht; a = vruchtkelk',
	
			'Onobrychis viciifolia 2.jpg' => 'vergroot',
	
			'Onobrychis viciifolia 3.jpg' => 'foto',
	
			'Onobrychis viciifolia 4.jpg' => 'foto'
	
		),
	
		'Polygala serpyllifolia' => array(
	
			'Polygala serpyllifolia.jpg' => 'overzicht',
	
			'Polygala serpyllifolia 2.jpg' => 'vergroot',
	
			'Polygala serpyllifolia 3.jpg' => 'foto',
	
			'Polygala serpyllifolia 4.jpg' => 'foto'
	
		),
	
		'Polygala comosa' => array(
	
			'Polygala comosa.jpg' => 'overzicht; a = bloem van onderen, b = bloem van boven, c = dsn. bloem',
	
			'Polygala comosa 2.jpg' => 'vergroot; a = bloem met los schutblad, b = bloem, 1 vleugel verwijderd, c = stamper, d = dsn. bloem zonder stamper',
	
			'Polygala comosa 3.jpg' => 'foto'
	
		),
	
		'Polygala vulgaris' => array(
	
			'Polygala vulgaris.jpg' => 'overzicht; a = bloem van opzij, b = bloem zonder de vleugels',
	
			'Polygala vulgaris 2.jpg' => 'vergroot',
	
			'Polygala vulgaris 3.jpg' => 'foto'
	
		),
	
		'Sorbaria sorbifolia' => array(
	
			'Sorbaria sorbifolia.jpg' => 'overzicht',
	
			'Sorbaria sorbifolia 2.jpg' => 'foto, bloeiend',
	
			'Sorbaria sorbifolia 3.jpg' => 'foto, bloeiwijze en bladen',
	
			'Sorbaria sorbifolia 4.jpg' => 'foto, detail bloeiwijze'
	
		),
	
		'Sorbaria tomentosa' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Spiraea salicifolia' => array(
	
			'Spiraea salicifolia.jpg' => 'overzicht; a = bloem, b = stampers',
	
			'Spiraea salicifolia 2.jpg' => 'vergroot',
	
			'Spiraea salicifolia 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Spiraea douglasii' => array(
	
			'Spiraea douglasii.jpg' => 'overzicht foto',
	
			'Spiraea douglasii 3.jpg' => 'foto, bloeiend',
	
			'Spiraea douglasii 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Aruncus dioicus' => array(
	
			'Aruncus dioicus.jpg' => 'overzicht',
	
			'Aruncus dioicus 2.jpg' => 'vergroot; a = dsn. manlijke bloem, b = vrucht',
	
			'Aruncus dioicus 3.jpg' => 'foto, in bloei',
	
			'Aruncus dioicus 4.jpg' => 'foto, in bloei'
	
		),
	
		'Filipendula ulmaria' => array(
	
			'Filipendula ulmaria.jpg' => 'overzicht; a = bloem, b = stamper',
	
			'Filipendula ulmaria 2.jpg' => 'vergroot',
	
			'Filipendula ulmaria 3.jpg' => 'foto',
	
			'Filipendula ulmaria 4.jpg' => 'foto'
	
		),
	
		'Filipendula vulgaris' => array(
	
			'Filipendula vulgaris.jpg' => 'overzicht',
	
			'Filipendula vulgaris 2.jpg' => 'vergroot',
	
			'Filipendula vulgaris 3.jpg' => 'foto',
	
			'Filipendula vulgaris 4.jpg' => 'foto',
	
			'Filipendula vulgaris 5.jpg' => 'foto'
	
		),
	
		'Rubus odoratus' => array(
	
			'Rubus odoratus.jpg' => 'overzicht foto',
	
			'Rubus odoratus 3.jpg' => 'foto, in bloei',
	
			'Rubus odoratus 4.jpg' => 'foto, in bloei en vrucht (jong)'
	
		),
	
		'Rubus saxatilis' => array(
	
			'Rubus saxatilis.jpg' => 'overzicht; a = vrucht',
	
			'Rubus saxatilis 2.jpg' => 'vergroot',
	
			'Rubus saxatilis 3.jpg' => 'foto, in vrucht',
	
			'Rubus saxatilis 4.jpg' => 'foto, in knop',
	
			'Rubus saxatilis 5.jpg' => 'foto, in bloei'
	
		),
	
		'Rubus spectabilis' => array(
	
			'Rubus spectabilis.jpg' => 'overzicht foto',
	
			'Rubus spectabilis 3.jpg' => 'foto, in bloei'
	
		),
	
		'Rubus phoenicolasius' => array(
	
			'Rubus phoenicolasius.jpg' => 'overzicht foto',
	
			'Rubus phoenicolasius 3.jpg' => 'foto, in bloei',
	
			'Rubus phoenicolasius 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Rubus idaeus' => array(
	
			'Rubus idaeus.jpg' => 'overzicht',
	
			'Rubus idaeus 2.jpg' => 'vergroot',
	
			'Rubus idaeus 3.jpg' => 'foto, in vrucht'
	
		),
	
		'Rubus idaeoides(x)' => array(
	
			'Rubus idaeoides(x).jpg' => 'overzicht',
	
			'Rubus idaeoides(x) 3.jpg' => 'foto, vegetatief',
	
			'Rubus idaeoides(x) 4.jpg' => 'foto, vruchten'
	
		),
	
		'Rubus laciniatus' => array(
	
			'Rubus laciniatus.jpg' => 'overzicht',
	
			'Rubus laciniatus 2.jpg' => 'vergroot',
	
			'Rubus laciniatus 3.jpg' => 'foto, in bloei'
	
		),
	
		'Rubus caesius' => array(
	
			'Rubus caesius.jpg' => 'overzicht',
	
			'Rubus caesius 2.jpg' => 'vergroot',
	
			'Rubus caesius 3.jpg' => 'foto, bloeiend',
	
			'Rubus caesius 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Rubus corylifolius' => array(
	
			'Rubus corylifolius.jpg' => 'overzicht',
	
			'Rubus corylifolius 2.jpg' => 'vergroot'
	
		),
	
		'Rubus fruticosus' => array(
	
			'Rubus fruticosus.jpg' => 'overzicht',
	
			'Rubus fruticosus 2.jpg' => 'vergroot'
	
		),
	
		'Rosa villosa' => array(
	
			'Rosa villosa.jpg' => 'overzicht',
	
			'Rosa villosa 2.jpg' => 'vergroot; a = tak met stekels, b = onderzijde blaadje, c = vruchten, d = dsn. vrucht',
	
			'Rosa villosa 3.jpg' => 'foto, vruchten',
	
			'Rosa villosa 4.jpg' => 'foto, vruchten',
	
			'Rosa villosa a.jpg' => 'tekening, BSBI Handbook no. 7'
	
		),
	
		'Rosa rubiginosa' => array(
	
			'Rosa rubiginosa.jpg' => 'overzicht',
	
			'Rosa rubiginosa 2.jpg' => 'vergroot; a = tak met stekels, b = onderzijde blaadje, c = vruchten, d = dsn. vrucht',
	
			'Rosa rubiginosa 3.jpg' => 'foto, in bloei',
	
			'Rosa rubiginosa 4.jpg' => 'foto, in vrucht',
	
			'Rosa rubiginosa a.jpg' => 'tekening, BSBI Handbook no. 7'
	
		),
	
		'Rosa majalis' => array(
	
			'Rosa majalis.jpg' => 'overzicht foto',
	
			'Rosa majalis 3.jpg' => 'foto, bloem'
	
		),
	
		'Rosa virginiana' => array(
	
			'Rosa virginiana.jpg' => 'overzicht',
	
			'Rosa virginiana 2.jpg' => 'vergroot',
	
			'Rosa virginiana 3.jpg' => 'foto, in bloei',
	
			'Rosa virginiana a.jpg' => 'tekening, BSBI Handbook no. 7'
	
		),
	
		'Rosa pimpinellifolia' => array(
	
			'Rosa pimpinellifolia.jpg' => 'overzicht',
	
			'Rosa pimpinellifolia 2.jpg' => 'vergroot; a = tak met stekels, b = onderzijde blaadje, c = vruchten, d = dsn. vrucht',
	
			'Rosa pimpinellifolia 3.jpg' => 'foto, wit bloeiend',
	
			'Rosa pimpinellifolia 4.jpg' => 'foto, roze bloeiend',
	
			'Rosa pimpinellifolia 5.jpg' => 'foto, wit bloeiend',
	
			'Rosa pimpinellifolia 6.jpg' => 'foto, vruchten',
	
			'Rosa pimpinellifolia a.jpg' => 'tekening, BSBI Handbook no. 7'
	
		),
	
		'Rosa gallica' => array(
	
			'Rosa gallica.jpg' => 'overzicht',
	
			'Rosa gallica 2.jpg' => 'vergroot; a = tak met stekels, b = onderzijde blaadje, c = vruchten, d = dsn. vrucht',
	
			'Rosa gallica 3.jpg' => 'foto, in bloei',
	
			'Rosa gallica a.jpg' => 'tekening, BSBI Handbook no. 7'
	
		),
	
		'Rosa rugosa' => array(
	
			'Rosa rugosa.jpg' => 'overzicht',
	
			'Rosa rugosa 2.jpg' => 'vergroot; a = tak met stekels, b = onderzijde blaadje, c = vruchten, d = dsn. vrucht',
	
			'Rosa rugosa 3.jpg' => 'foto, in bloei',
	
			'Rosa rugosa 4.jpg' => 'foto, vruchten',
	
			'Rosa rugosa 5.jpg' => 'foto, vruchten',
	
			'Rosa rugosa a.jpg' => 'tekening, BSBI Handbook no. 7'
	
		),
	
		"Rosa 'hollandica'" => array(
	
			'Rosa hollandica.jpg' => 'overzicht',
	
			"Rosa 'hollandica' 4.jpg" => 'foto, bloem',
	
			"Rosa 'hollandica' 5.jpg" => 'foto, bladen',
	
			'Rosa hollandica a.jpg' => 'tekening, BSBI Handbook no. 7'
	
		),
	
		'Rosa arvensis' => array(
	
			'Rosa arvensis.jpg' => 'overzicht',
	
			'Rosa arvensis 2.jpg' => 'vergroot; a = tak met stekels, b = onderzijde blaadje, c = vruchten, d = dsn. vrucht',
	
			'Rosa arvensis 3.jpg' => 'foto, in bloei',
	
			'Rosa arvensis 4.jpg' => 'foto, in bloei',
	
			'Rosa arvensis 5.jpg' => 'foto, in bloei',
	
			'Rosa arvensis 6.jpg' => 'foto, in vrucht',
	
			'Rosa arvensis a.jpg' => 'tekening, BSBI Handbook no. 7'
	
		),
	
		'Rosa multiflora' => array(
	
			'Rosa multiflora.jpg' => 'overzicht',
	
			'Rosa multiflora 2.jpg' => 'vergroot; a = tak met stekels, b = onderzijde blaadje, c = vruchten, d = dsn. vrucht',
	
			'Rosa multiflora 3.jpg' => 'foto, in vrucht',
	
			'Rosa multiflora 4.jpg' => 'foto, in bloei',
	
			'Rosa multiflora a.jpg' => 'tekening, BSBI Handbook no. 7'
	
		),
	
		'Rosa glauca' => array(
	
			'Rosa glauca.jpg' => 'overzicht',
	
			'Rosa glauca 2.jpg' => 'vergroot; a = tak met stekels, b = onderzijde blaadje, c = vruchten, d = dsn. vrucht',
	
			'Rosa glauca 3.jpg' => 'foto, in bloei',
	
			'Rosa glauca a.jpg' => 'tekening, BSBI Handbook no. 7'
	
		),
	
		'Rosa canina' => array(
	
			'Rosa canina.jpg' => 'overzicht',
	
			'Rosa canina 2.jpg' => 'vergroot; a = bloemknop, b = dsn. bloem zonder kroonbladen, c = vrucht, d = tak met stekels, e = onderzijde blaadje, f = vruchten, g = dsn. vrucht',
	
			'Rosa canina 3.jpg' => 'foto, in bloei',
	
			'Rosa canina 4.jpg' => 'foto, in vrucht',
	
			'Rosa canina a.jpg' => 'tekening, BSBI Handbook no. 7',
	
			'Rosa canina b.jpg' => 'tekening, BSBI Handbook no. 7',
	
			'Rosa canina c.jpg' => 'tekening,  bloem en vrucht'
	
		),
	
		'Agrimonia eupatoria' => array(
	
			'Agrimonia eupatoria.jpg' => 'overzicht',
	
			'Agrimonia eupatoria 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem, c = vrucht',
	
			'Agrimonia eupatoria 3.jpg' => 'foto, vruchtjes (links) en bloemen (rechts)',
	
			'Agrimonia eupatoria 4.jpg' => 'foto, in bloei',
	
			'Agrimonia eupatoria 5.jpg' => 'foto, in vrucht'
	
		),
	
		'Agrimonia procera' => array(
	
			'Agrimonia procera.jpg' => 'overzicht',
	
			'Agrimonia procera 2.jpg' => 'vergroot; a = vrucht',
	
			'Agrimonia procera 3.jpg' => 'foto, vruchtje (links) en bloemen (rechts)'
	
		),
	
		'Sanguisorba officinalis' => array(
	
			'Sanguisorba officinalis.jpg' => 'overzicht',
	
			'Sanguisorba officinalis 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem',
	
			'Sanguisorba officinalis 3.jpg' => 'foto, habitus bloeiend',
	
			'Sanguisorba officinalis 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Sanguisorba minor' => array(
	
			'Sanguisorba minor.jpg' => 'overzicht',
	
			'Sanguisorba minor 2.jpg' => 'vergroot; a = vrouwelijke bloem, b = manlijke bloem, c = vrucht',
	
			'Sanguisorba minor 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Sanguisorba minor subsp. minor' => array(
	
			'Sanguisorba minor minor.jpg' => 'overzicht',
	
			'Sanguisorba minor minor 2.jpg' => 'foto, in bloei en vrucht'
	
		),
	
		'Sanguisorba minor subsp. balcarica' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Geum rivale' => array(
	
			'Geum rivale.jpg' => 'overzicht',
	
			'Geum rivale 2.jpg' => 'vergroot; a = dsn. bloem, b = vruchtje',
	
			'Geum rivale 3.jpg' => 'foto',
	
			'Geum rivale 4.jpg' => 'foto',
	
			'Geum intermedium(x) 3.jpg' => '(= Geum rivale x Geum urbanum) foto'
	
		),
	
		'Geum urbanum' => array(
	
			'Geum urbanum.jpg' => 'overzicht',
	
			'Geum urbanum 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Geum urbanum 3.jpg' => 'foto',
	
			'Geum intermedium(x) 3.jpg' => '(= Geum rivale x Geum urbanum) foto'
	
		),
	
		'Geum macrophyllum' => array(
	
			'Geum macrophyllum.jpg' => 'foto',
	
			'Geum macrophyllum 2.jpg' => 'bladrozet en vruchtje, Heukels, 2005',
	
			'Geum macrophyllum 3.jpg' => 'foto'
	
		),
	
		'Potentilla supina' => array(
	
			'Potentilla supina.jpg' => 'overzicht',
	
			'Potentilla supina 2.jpg' => 'vergroot',
	
			'Potentilla supina 3.jpg' => 'foto',
	
			'Potentilla supina 4.jpg' => 'foto'
	
		),
	
		'Potentilla anserina' => array(
	
			'Potentilla anserina.jpg' => 'overzicht',
	
			'Potentilla anserina 2.jpg' => 'vergroot; a = bloem van onderen, b = dsn. bloem',
	
			'Potentilla anserina 3.jpg' => 'foto'
	
		),
	
		'Potentilla sterilis' => array(
	
			'Potentilla sterilis.jpg' => 'overzicht',
	
			'Potentilla sterilis 2.jpg' => 'vergroot',
	
			'Potentilla sterilis 3.jpg' => 'foto'
	
		),
	
		'Potentilla erecta' => array(
	
			'Potentilla erecta.jpg' => 'overzicht',
	
			'Potentilla erecta 2.jpg' => 'vergroot; a = bloemknop, b = bloem, c = stampers',
	
			'Potentilla erecta 3.jpg' => 'foto'
	
		),
	
		'Potentilla anglica' => array(
	
			'Potentilla anglica.jpg' => 'overzicht',
	
			'Potentilla anglica 2.jpg' => 'vergroot',
	
			'Potentilla anglica 3.jpg' => 'foto'
	
		),
	
		'Potentilla indica' => array(
	
			'Potentilla indica.jpg' => 'overzicht foto',
	
			'Potentilla indica 3.jpg' => 'foto',
	
			'Potentilla indica 4.jpg' => 'foto',
	
			'Potentilla indica 5.jpg' => 'foto, bloeiend en in vrucht'
	
		),
	
		'Potentilla reptans' => array(
	
			'Potentilla reptans.jpg' => 'overzicht',
	
			'Potentilla reptans 2.jpg' => 'vergroot',
	
			'Potentilla reptans 3.jpg' => 'foto'
	
		),
	
		'Potentilla tabernaemontani' => array(
	
			'Potentilla tabernaemontani.jpg' => 'overzicht',
	
			'Potentilla tabernaemontani 2.jpg' => 'vergroot',
	
			'Potentilla tabernaemontani 3.jpg' => 'foto, bloeiende habitus',
	
			'Potentilla tabernaemontani 4.jpg' => 'foto, bloeiende habitus',
	
			'Potentilla tabernaemontani 5.jpg' => 'foto, bloeiende habitus',
	
			'Potentilla tabernaemontani 6.jpg' => 'foto, bloeiende habitus'
	
		),
	
		'Potentilla argentea' => array(
	
			'Potentilla argentea.jpg' => 'overzicht',
	
			'Potentilla argentea 2.jpg' => 'vergroot',
	
			'Potentilla argentea 3.jpg' => 'foto'
	
		),
	
		'Potentilla norvegica' => array(
	
			'Potentilla norvegica.jpg' => 'overzicht',
	
			'Potentilla norvegica 2.jpg' => 'vergroot; a = bloem',
	
			'Potentilla norvegica 3.jpg' => 'foto'
	
		),
	
		'Potentilla recta' => array(
	
			'Potentilla recta.jpg' => 'overzicht',
	
			'Potentilla recta 2.jpg' => 'vergroot',
	
			'Potentilla recta 3.jpg' => 'foto'
	
		),
	
		'Potentilla intermedia' => array(
	
			'Potentilla intermedia.jpg' => 'overzicht',
	
			'Potentilla intermedia 2.jpg' => 'vergroot',
	
			'Potentilla intermedia 3.jpg' => 'foto'
	
		),
	
		'Fragaria ananassa(x)' => array(
	
			'Fragaria ananassa(x).jpg' => 'overzicht',
	
			'Fragaria ananassa(x) 2.jpg' => 'foto, bloeiend',
	
			'Fragaria ananassa(x) 3.jpg' => 'foto, bloem'
	
		),
	
		'Fragaria vesca' => array(
	
			'Fragaria vesca.jpg' => 'overzicht',
	
			'Fragaria vesca 2.jpg' => 'vergroot; a = bloemknop',
	
			'Fragaria vesca 3.jpg' => 'foto'
	
		),
	
		'Fragaria moschata' => array(
	
			'Fragaria moschata.jpg' => 'overzicht',
	
			'Fragaria moschata 2.jpg' => 'vergroot',
	
			'Fragaria moschata 3.jpg' => 'foto',
	
			'Fragaria moschata 4.jpg' => 'foto',
	
			'Fragaria moschata 5.jpg' => 'foto'
	
		),
	
		'Alchemilla mollis' => array(
	
			'Alchemilla mollis.jpg' => 'overzicht foto',
	
			'Alchemilla mollis 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Alchemilla glabra' => array(
	
			'Alchemilla glabra.jpg' => 'overzicht foto',
	
			'Alchemilla glabra 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Alchemilla xanthochlora' => array(
	
			'Alchemilla xanthochlora.jpg' => 'overzicht foto',
	
			'Alchemilla xanthochlora 3.jpg' => 'foto, habitus bloeiend',
	
			'Alchemilla xanthochlora 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Alchemilla acutiloba' => array(
	
			'Alchemilla acutiloba.jpg' => 'overzicht',
	
			'Alchemilla acutiloba 2.jpg' => 'vergroot;  a = bloem van opzij, b = bloem van boven'
	
		),
	
		'Alchemilla subcrenata' => array(
	
			'Alchemilla subcrenata.jpg' => 'overzicht',
	
			'Alchemilla subcrenata a.jpg' => 'vergroot'
	
		),
	
		'Alchemilla filicaulis' => array(
	
			'Alchemilla filicaulis.jpg' => 'overzicht; a = onderste blad',
	
			'Alchemilla filicaulis 2.jpg' => 'vergroot'
	
		),
	
		'Alchemilla monticola' => array(
	
			'Alchemilla monticola.jpg' => 'overzicht',
	
			'Alchemilla monticola a.jpg' => 'vergroot'
	
		),
	
		'Alchemilla micans' => array(
	
			'Alchemilla micans.jpg' => 'overzicht foto',
	
			'Alchemilla micans 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Aphanes arvensis' => array(
	
			'Aphanes arvensis.jpg' => 'overzicht',
	
			'Aphanes arvensis 2.jpg' => 'vergroot; a = blad en bloemkluwen, b = vrucht',
	
			'Aphanes arvensis 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Aphanes australis' => array(
	
			'Aphanes australis.jpg' => 'overzicht foto',
	
			'Aphanes australis 2.jpg' => 'detail; a = blad + steunblaadjes, b = vruchtkelk',
	
			'Aphanes australis 3.jpg' => 'foto, habitus'
	
		),
	
		'Comarum palustre' => array(
	
			'Comarum palustris.jpg' => 'overzicht',
	
			'Comarum palustris 2.jpg' => 'vergroot',
	
			'Comarum palustre 4.jpg' => 'foto, bloeiend',
	
			'Comarum palustre 5.jpg' => 'foto, bloeiend',
	
			'Comarum palustris 3.jpg' => 'foto'
	
		),
	
		'Pyrus communis' => array(
	
			'Pyrus communis.jpg' => 'overzicht',
	
			'Pyrus communis 2.jpg' => 'vergroot; a = dsn. bloem zonder kroonbladen, b = dsn. vruchtbeginsel, c = dsn. vrucht',
	
			'Pyrus communis 3.jpg' => 'foto',
	
			'Pyrus communis 4.jpg' => 'foto',
	
			'Pyrus communis 5.jpg' => 'foto, in vrucht'
	
		),
	
		'Malus sylvestris' => array(
	
			'Malus sylvestris.jpg' => 'overzicht',
	
			'Malus sylvestris 2.jpg' => 'vergroot; a = dsn. bloem, b = dwarsdsn. vrucht, c = lengtedsn. vrucht',
	
			'Malus sylvestris 3.jpg' => 'foto, in bloei',
	
			'Malus sylvestris 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Sorbus aucuparia' => array(
	
			'Sorbus aucuparia.jpg' => 'overzicht',
	
			'Sorbus aucuparia 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem, c = dsn. vrucht',
	
			'Sorbus aucuparia 3.jpg' => 'foto, habitus in vrucht',
	
			'Sorbus aucuparia 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Sorbus thuringiaca(x)' => array(
	
			'Sorbus thuringiaca(x).jpg' => 'overzicht',
	
			'Sorbus thuringiaca(x) 2.jpg' => 'vergroot; a = bloem'
	
		),
	
		'Sorbus aria' => array(
	
			'Sorbus aria.jpg' => 'overzicht',
	
			'Sorbus aria 2.jpg' => 'vergroot',
	
			'Sorbus aria 3.jpg' => 'foto, bloeiend',
	
			'Sorbus aria 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Sorbus intermedia' => array(
	
			'Sorbus intermedia.jpg' => 'overzicht foto',
	
			'Sorbus intermedia 3.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Amelanchier lamarckii' => array(
	
			'Amelanchier lamarckii.jpg' => 'overzicht foto',
	
			'Amelanchier lamarckii 3.jpg' => 'foto, bloeiend',
	
			'Amelanchier lamarckii 4.jpg' => 'foto, bloemen',
	
			'Amelanchier lamarckii 5.jpg' => 'foto, in vrucht (jong)'
	
		),
	
		'Aronia prunifolia(x)' => array(
	
			'Aronia prunifolia(x).jpg' => 'overzicht foto',
	
			'Aronia prunifolia(x) 2.jpg' => 'detail; bladhelft met klieren',
	
			'Aronia prunifolia(x) 3.jpg' => 'foto, in vrucht ',
	
			'Aronia prunifolia(x) 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Cotoneaster salicifolius' => array(
	
			'Cotoneaster salicifolius.jpg' => 'overzicht foto',
	
			'Cotoneaster salicifolius 3.jpg' => 'bloeiend',
	
			'Cotoneaster salicifolius 4.jpg' => 'vruchten'
	
		),
	
		'Cotoneaster integerrimus' => array(
	
			'Cotoneaster integerrimus.jpg' => 'overzicht',
	
			'Cotoneaster integerrimus 2.jpg' => 'vergroot',
	
			'Cotoneaster integerrimus 3.jpg' => 'foto'
	
		),
	
		'Cotoneaster horizontalis' => array(
	
			'Cotoneaster horizontalis.jpg' => 'overzicht foto',
	
			'Cotoneaster horizontalis 3.jpg' => 'foto',
	
			'Cotoneaster horizontalis 5.jpg' => 'foto'
	
		),
	
		'Cotoneaster sternianus' => array(
	
			'Cotoneaster sternianus.jpg' => 'overzicht foto',
	
			'Cotoneaster sternianus 2.jpg' => 'bloeiend',
	
			'Cotoneaster sternianus 3.jpg' => 'vruchten'
	
		),
	
		'Cotoneaster rehderi' => array(
	
			'Cotoneaster rehderi.jpg' => 'overzicht foto',
	
			'Cotoneaster rehderi 2.jpg' => 'bloeiend',
	
			'Cotoneaster rehderi 3.jpg' => 'vruchten'
	
		),
	
		'Mespilus germanica' => array(
	
			'Mespilis germanica.jpg' => 'overzicht; a = dsn. bloem zonder kroonbladen, b = vrucht',
	
			'Mespilis germanica 2.jpg' => 'vergroot',
	
			'Mespilus germanica 3.jpg' => 'foto',
	
			'Mespilus germanica 4.jpg' => 'foto'
	
		),
	
		'Crataegus crus-galli' => array(
	
			'Crataegus crus-galli.jpg' => 'overzicht, foto',
	
			'Crataegus crus-galli 3.jpg' => 'vruchten'
	
		),
	
		'Crataegus monogyna' => array(
	
			'Crataegus monogyna.jpg' => 'overzicht',
	
			'Crataegus monogyna 2.jpg' => 'vergroot; a = bloem',
	
			'Crataegus monogyna 3.jpg' => 'foto',
	
			'Crataegus monogyna 4.jpg' => 'foto'
	
		),
	
		'Crataegus laevigata' => array(
	
			'Crataegus laevigata.jpg' => 'overzicht',
	
			'Crataegus laevigata 2.jpg' => 'vergroot; a = dsn. bloem, b = dsn. vrucht',
	
			'Crataegus laevigata 3.jpg' => 'foto',
	
			'Crataegus laevigata 4.jpg' => 'foto'
	
		),
	
		'Prunus mahaleb' => array(
	
			'Prunus mahaleb.jpg' => 'overzicht',
	
			'Prunus mahaleb 2.jpg' => 'vergroot; a = bloemtros, b = dsn. vrucht',
	
			'Prunus mahaleb 3.jpg' => 'foto',
	
			'Prunus mahaleb 4.jpg' => 'foto'
	
		),
	
		'Prunus laurocerasus' => array(
	
			'Prunus laurocerasus.jpg' => 'overzicht',
	
			'Prunus laurocerasus 2.jpg' => 'foto',
	
			'Prunus laurocerasus 3.jpg' => 'foto'
	
		),
	
		'Prunus serotina' => array(
	
			'Prunus serotina.jpg' => 'overzicht foto',
	
			'Prunus serotina 3.jpg' => 'foto',
	
			'Prunus serotina 4.jpg' => 'foto'
	
		),
	
		'Prunus padus' => array(
	
			'Prunus padus.jpg' => 'overzicht',
	
			'Prunus padus 2.jpg' => 'vergroot; a = dsn. bloem',
	
			'Prunus padus 3.jpg' => 'foto',
	
			'Prunus padus 4.jpg' => 'foto'
	
		),
	
		'Prunus cerasus' => array(
	
			'Prunus cerasus.jpg' => 'overzicht',
	
			'Prunus cerasus 2.jpg' => 'vergroot; a = dsn. bloem'
	
		),
	
		'Prunus avium' => array(
	
			'Prunus avium.jpg' => 'overzicht',
	
			'Prunus avium 2.jpg' => 'vergroot',
	
			'Prunus avium 3.jpg' => 'foto, bloeiend',
	
			'Prunus avium 4.jpg' => 'foto, bloeiend',
	
			'Prunus avium 5.jpg' => 'foto, met vruchten'
	
		),
	
		'Prunus spinosa' => array(
	
			'Prunus spinosa.jpg' => 'overzicht',
	
			'Prunus spinosa 2.jpg' => 'vergroot; a = dsn. bloem',
	
			'Prunus spinosa 3.jpg' => 'foto',
	
			'Prunus spinosa 4.jpg' => 'foto',
	
			'Prunus spinosa 5.jpg' => 'foto, vruchten',
	
			'Prunus spinosa 6.jpg' => 'foto, bloeiend',
	
			'Prunus spinosa 7.jpg' => 'foto, vruchten'
	
		),
	
		'Prunus cerasifera' => array(
	
			'Prunus cerasifera.jpg' => 'overzicht foto',
	
			'Prunus cerasifera 3.jpg' => 'foto, bloeiend',
	
			'Prunus cerasifera 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Prunus domestica' => array(
	
			'Prunus domestica.jpg' => 'overzicht',
	
			'Prunus domestica 2.jpg' => 'vergroot; a = dsn. bloem',
	
			'Prunus domestica 3.jpg' => 'vergroot; a = dsn. bloem',
	
			'Prunus domestica 4.jpg' => 'foto, bloeiend',
	
			'Prunus domestica 5.jpg' => 'foto, in vrucht'
	
		),
	
		'Hippophae rhamnoides' => array(
	
			'Hippophae rhamnoides.jpg' => 'overzicht; a = dsn. vrouwelijke bloem, b = dsn. vrucht',
	
			'Hippophae rhamnoides 2.jpg' => 'vergroot',
	
			'Hippophae rhamnoides 3.jpg' => 'foto; in vrucht',
	
			'Hippophae rhamnoides 4.jpg' => 'vrouwelijke bloemen',
	
			'Hippophae rhamnoides 5.jpg' => 'manlijke bloemen',
	
			'Hippophae rhamnoides 6.jpg' => 'vruchten'
	
		),
	
		'Elaeagnus multiflora' => array(
	
			'Elaeagnus multiflora.jpg' => 'overzicht foto',
	
			'Elaeagnus multiflora 3.jpg' => 'foto',
	
			'Elaeagnus multiflora 4.jpg' => 'foto'
	
		),
	
		'Elaeagnus angustifolia' => array(
	
			'Elaeagnus angustifolia.jpg' => 'overzicht',
	
			'Elaeagnus angustifolia 2.jpg' => 'vergroot',
	
			'Elaeagnus angustifolia 3.jpg' => 'foto',
	
			'Elaeagnus angustifolia 4.jpg' => 'foto'
	
		),
	
		'Elaeagnus commutata' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Rhamnus cathartica' => array(
	
			'Rhamnus cathartica.jpg' => 'overzicht',
	
			'Rhamnus cathartica 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem, c = vrucht',
	
			'Rhamnus cathartica 3.jpg' => 'foto, in bloei',
	
			'Rhamnus cathartica 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Rhamnus frangula' => array(
	
			'Rhamnus frangula.jpg' => 'overzicht',
	
			'Rhamnus frangula 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem',
	
			'Rhamnus frangula 3.jpg' => 'foto, in bloei',
	
			'Rhamnus frangula 4.jpg' => 'foto, in bloei',
	
			'Rhamnus frangula 5.jpg' => 'foto, in bloei',
	
			'Rhamnus frangula 6.jpg' => 'foto, in vrucht'
	
		),
	
		'Ulmus laevis' => array(
	
			'Ulmus laevis.jpg' => 'overzicht',
	
			'Ulmus laevis 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Ulmus vruchtjes.jpg' => 'vruchtjes van U. laevis, U. glabra en U. minor',
	
			'Ulmus laevis 3.jpg' => 'foto, in vrucht',
	
			'Ulmus laevis 4.jpg' => 'foto, in vrucht',
	
			'Ulmus laevis 5.jpg' => 'foto, in bloei',
	
			'Ulmus laevis 6.jpg' => 'foto, in bloei'
	
		),
	
		'Ulmus glabra' => array(
	
			'Ulmus glabra.jpg' => 'overzicht',
	
			'Ulmus glabra 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Ulmus vruchtjes.jpg' => 'vruchtjes van U. laevis, U. glabra en U. minor',
	
			'Ulmus glabra 3.jpg' => 'foto, in vrucht',
	
			'Ulmus glabra 4.jpg' => 'foto, blad',
	
			'Ulmus glabra 5.jpg' => 'foto, bast',
	
			'Ulmus glabra 6.jpg' => 'foto, bloeiend'
	
		),
	
		'Ulmus minor' => array(
	
			'Ulmus minor.jpg' => 'overzicht',
	
			'Ulmus minor 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Ulmus vruchtjes.jpg' => 'vruchtjes van U. laevis, U. glabra en U. minor',
	
			'Ulmus minor 3.jpg' => 'foto, vruchtjes',
	
			'Ulmus minor 4.jpg' => 'foto, in bloei'
	
		),
	
		'Ulmus hollandica(x)' => array(
	
			'Ulmus hollandica(x).jpg' => 'overzicht',
	
			'Ulmus hollandica(x) 2.jpg' => 'foto, blad'
	
		),
	
		'Humulus lupulus' => array(
	
			'Humulus lupulus.jpg' => 'overzicht;  a = manlijke plant, b = vrouwelijke plant',
	
			'Humulus lupulus 2.jpg' => 'vergroot; a = manlijke plant, b = vrouwelijke plant, c = manlijke bloem, d = vrouwelijk hoofdje,  e = twee vrouwelijke bloemen',
	
			'Humulus lupulus 3.jpg' => 'foto',
	
			'Humulus lupulus 4.jpg' => 'foto'
	
		),
	
		'Cannabis sativa' => array(
	
			'Cannabis sativa.jpg' => 'overzicht; a = manlijke plant, b = vrouwelijke plant',
	
			'Cannabis sativa 2.jpg' => 'vergroot; a = manlijke plant, b = vrouwelijke plant, c = manlijke bloem, d = vrouwelijke bloem, e = vrucht'
	
		),
	
		'Ficus carica' => array(
	
			'Ficus carica.jpg' => 'overzicht',
	
			'Ficus carica 2.jpg' => 'vergroot; a = vrucht, b = l.dsn. vrucht',
	
			'Ficus carica 3.jpg' => 'foto',
	
			'Ficus carica 4.jpg' => 'foto, habitus',
	
			'Ficus carica 5.jpg' => 'foto, met vruchten',
	
			'Ficus carica 6.jpg' => 'foto, met vruchten'
	
		),
	
		'Morus nigra' => array(
	
			'Morus nigra.jpg' => 'overzicht',
	
			'Morus nigra 2.jpg' => 'foto',
	
			'Morus nigra 3.jpg' => 'foto',
	
			'Morus nigra 4.jpg' => 'foto'
	
		),
	
		'Urtica dioica' => array(
	
			'Urtica dioica.jpg' => 'overzicht',
	
			'Urtica dioica 2.jpg' => 'vergroot; a = manlijke bloemen, b = vrouwelijke bloemen, c = manlijke bloem, d = vrouwelijke bloem',
	
			'Urtica dioica 3.jpg' => 'foto, plant vanaf boven gezien, bloeiwijzen',
	
			'Urtica dioica 4.jpg' => 'foto, bloeiwijzen',
	
			'Urtica dioica 5.jpg' => 'foto, habitus, bloeiend'
	
		),
	
		'Urtica urens' => array(
	
			'Urtica urens.jpg' => 'overzicht',
	
			'Urtica urens 2.jpg' => 'vergroot; a = manlijke bloem, b = vrouwelijke bloem (geopend)',
	
			'Urtica urens 3.jpg' => 'foto, habitus bloeiend',
	
			'Urtica urens 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Parietaria officinalis' => array(
	
			'Parietaria officinalis.jpg' => 'overzicht; a = vruchtdragend bloemdek, b = schutblaadjes',
	
			'Parietaria officinalis 2.jpg' => 'vergroot',
	
			'Parietaria officinalis 3.jpg' => 'foto'
	
		),
	
		'Parietaria judaica' => array(
	
			'Parietaria judaica.jpg' => 'overzicht; a = vruchtdragend bloemdek, b = schutblaadjes',
	
			'Parietaria judaica 2.jpg' => 'vergroot',
	
			'Parietaria judaica 3.jpg' => 'foto'
	
		),
	
		'Soleirolia soleirolii' => array(
	
			'Soleirolia soleirolii.jpg' => 'overzicht',
	
			'Soleirolia soleirolii 2.jpg' => 'tekening',
	
			'Soleirolia soleirolii 3.jpg' => 'foto, habitus in bloei'
	
		),
	
		'Bryonia dioica' => array(
	
			'Bryonia dioica.jpg' => 'overzicht',
	
			'Bryonia dioica 2.jpg' => 'vergroot',
	
			'Bryonia dioica 3.jpg' => 'foto',
	
			'Bryonia dioica 4.jpg' => 'foto',
	
			'Bryonia dioica 5.jpg' => 'foto; half uitgegraven knol'
	
		),
	
		'Citrullus lanatus' => array(
	
			'Citrullus lanatus.jpg' => 'overzicht vrucht',
	
			'Citrullus lanatus 2.jpg' => 'foto, habitaus met vrucht',
	
			'Citrullus lanatus 3.jpg' => 'foto, vrucht'
	
		),
	
		'Cucumis sativus' => array(
	
			'Cucumis sativus.jpg' => 'overzicht; a = dsn. vrouwelijke bloem',
	
			'Cucumis sativus 2.jpg' => 'vergroot; a = dsn. vrouwelijke bloem, b = dsn. manlijke bloem, c = meeldraad van voor- en achterzijde',
	
			'Cucumis sativus 3.jpg' => 'foto, bloemen'
	
		),
	
		'Cucumis melo' => array(
	
			'Cucumis melo.jpg' => 'overzicht',
	
			'Cucumis melo 3.jpg' => 'foto, bloem en vrucht',
	
			'Cucumis melo 4.jpg' => 'foto, bloem en vrucht'
	
		),
	
		'Cucurbita pepo' => array(
	
			'Cucurbita pepo.jpg' => 'overzicht foto',
	
			'Cucurbita pepo 3.jpg' => 'foto, bloeiend',
	
			'Cucurbita pepo 4.jpg' => 'foto, bloeiend',
	
			'Cucurbita pepo 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Castanea sativa' => array(
	
			'Castanea sativa.jpg' => 'overzicht',
	
			'Castanea sativa 2.jpg' => 'vergroot; a = manlijke bloem, b = 3-bloemige vrouwelijke bloeiwijze, c+d = vrucht',
	
			'Castanea sativa 3.jpg' => 'foto, in bloei',
	
			'Castanea sativa 4.jpg' => 'foto, in vrucht',
	
			'Castanea sativa 5.jpg' => 'foto, in vrucht',
	
			'Castanea sativa 6.jpg' => 'foto, bast',
	
			'Castanea sativa 7.jpg' => 'foto, vruchten en zaden'
	
		),
	
		'Fagus sylvatica' => array(
	
			'Fagus sylvatica.jpg' => 'overzicht',
	
			'Fagus sylvatica 2.jpg' => 'vergroot',
	
			'Fagus sylvatica 3.jpg' => 'foto',
	
			'Fagus sylvatica 4.jpg' => 'foto',
	
			'Fagus sylvatica 5.jpg' => 'foto, in vrucht',
	
			'Fagus sylvatica 6.jpg' => "foto, vari‘teit met 'rode' bladen"
	
		),
	
		'Quercus cerris' => array(
	
			'Quercus cerris.jpg' => 'overzicht',
	
			'Quercus cerris 2.jpg' => 'vergroot',
	
			'Quercus cerris 3.jpg' => 'foto, bladen',
	
			'Quercus cerris 4.jpg' => 'foto, bladen en vruchten',
	
			'Quercus cerris 5.jpg' => 'foto, bast'
	
		),
	
		'Quercus rubra' => array(
	
			'Quercus rubra.jpg' => 'overzicht; a = eikel',
	
			'Quercus rubra 2.jpg' => 'vergroot',
	
			'Quercus rubra 4.jpg' => 'foto, bast'
	
		),
	
		'Quercus palustris' => array(
	
			'Quercus palustris.jpg' => 'overzicht foto',
	
			'Quercus palustris 3.jpg' => 'foto, bladen'
	
		),
	
		'Quercus pubescens' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Quercus robur' => array(
	
			'Quercus robur.jpg' => 'overzicht',
	
			'Quercus robur 2.jpg' => 'vergroot',
	
			'Quercus robur 3.jpg' => 'foto, bladen',
	
			'Quercus robur 4.jpg' => 'foto, in bloei',
	
			'Quercus robur 5.jpg' => 'foto, met vruchten',
	
			'Quercus robur 6.jpg' => 'foto, bast'
	
		),
	
		'Quercus petraea' => array(
	
			'Quercus petraea.jpg' => 'overzicht',
	
			'Quercus petraea 2.jpg' => 'vergroot; a = manlijke bloemen, b = vrouwelijke bloemen',
	
			'Quercus petraea 3.jpg' => 'foto, bladen'
	
		),
	
		'Myrica gale' => array(
	
			'Myrica gale.jpg' => 'overzicht; a = takje met manlijke en vrouwelijke katjes, b = manlijke, c = vrouwelijke bloem, d = vrouwelijk katje',
	
			'Myrica gale 2.jpg' => 'vergroot',
	
			'Myrica gale 3.jpg' => 'foto',
	
			'Myrica gale 4.jpg' => 'foto',
	
			'Myrica gale 5.jpg' => 'foto, mannelijke bloeiwijzen'
	
		),
	
		'Morella caroliniensis' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Betula pendula' => array(
	
			'Betula pendula.jpg' => 'overzicht',
	
			'Betula pendula 2.jpg' => 'vergroot; a = katjesschub  en gevleugelde vrucht, b = gevleugelde vrucht',
	
			'Betula pendula 3.jpg' => 'foto',
	
			'Betula pendula 4.jpg' => 'foto',
	
			'Betula pendula 5.jpg' => 'foto, boom',
	
			'Betula pendula 6.jpg' => 'foto, bast',
	
			'Betula pendula 7.jpg' => 'foto, bladen en jonge katjes'
	
		),
	
		'Betula pubescens' => array(
	
			'Betula pubescens.jpg' => 'overzicht',
	
			'Betula pubescens 2.jpg' => 'vergroot; a = katjesschub, b = gevleugelde vrucht',
	
			'Betula pubescens 3.jpg' => 'foto',
	
			'Betula pubescens 4.jpg' => 'foto, bast'
	
		),
	
		'Alnus cordata' => array(
	
			'Alnus cordata.jpg' => 'overzicht foto',
	
			'Alnus cordata 3.jpg' => 'foto, vrouwelijke katjes (jong)',
	
			'Alnus cordata 4.jpg' => 'foto, mannelijk katje'
	
		),
	
		'Alnus glutinosa' => array(
	
			'Alnus glutinosa.jpg' => 'overzicht',
	
			'Alnus glutinosa 2.jpg' => 'vergroot',
	
			'Alnus glutinosa 3.jpg' => 'foto, vrouwelijke en mannelijke katjes',
	
			'Alnus glutinosa 4.jpg' => 'foto, vrouwelijke katjes (jong)'
	
		),
	
		'Alnus incana' => array(
	
			'Alnus incana.jpg' => 'overzicht',
	
			'Alnus incana 2.jpg' => 'vergroot',
	
			'Alnus incana 3.jpg' => 'foto, vrouwelijke katjes (jong)',
	
			'Alnus incana 4.jpg' => 'foto, vrouwelijke katjes'
	
		),
	
		'Carpinus betulus' => array(
	
			'Carpinus betulus.jpg' => 'overzicht',
	
			'Carpinus betulus 2.jpg' => 'vergroot',
	
			'Carpinus betulus 3.jpg' => 'foto, katjes',
	
			'Carpinus betulus 4.jpg' => 'foto, katjes',
	
			'Carpinus betulus 5.jpg' => 'foto, vruchten',
	
			'Carpinus betulus 6.jpg' => 'foto, bast'
	
		),
	
		'Corylus avellana' => array(
	
			'Corylus avellana.jpg' => 'overzicht',
	
			'Corylus avellana 2.jpg' => 'vergroot',
	
			'Corylus avellana 3.jpg' => 'foto',
	
			'Corylus avellana 4.jpg' => 'foto'
	
		),
	
		'Juglans regia' => array(
	
			'Juglans regia.jpg' => 'overzicht; a = onrijpe vruchten, b = halfopen rijpe vrucht',
	
			'Juglans regia 2.jpg' => 'vergroot',
	
			'Juglans regia 3.jpg' => 'foto',
	
			'Juglans regia 4.jpg' => 'foto, in vrucht',
	
			'Juglans regia 5.jpg' => 'foto, in vrucht',
	
			'Juglans regia 6.jpg' => 'foto, bast',
	
			'Juglans regia 7.jpg' => 'foto, vruchten met zaden'
	
		),
	
		'Limnanthes douglasii' => array(
	
			'Limnanthes douglasii.jpg' => 'overzicht',
	
			'Limnanthes douglasii 2.jpg' => 'vergroot',
	
			'Limnanthes douglasii 3.jpg' => 'foto',
	
			'Limnanthes douglasii 4.jpg' => 'foto'
	
		),
	
		'Limnanthes alba' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Reseda lutea' => array(
	
			'Reseda lutea.jpg' => 'overzicht',
	
			'Reseda lutea 2.jpg' => "vergroot;  a = bloem, b = 'geplukte' bloem met o.a. het vruchtbeginsel",
	
			'Reseda lutea 3.jpg' => 'foto, habitus bloeiend',
	
			'Reseda lutea 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Reseda luteola' => array(
	
			'Reseda luteola.jpg' => 'overzicht',
	
			'Reseda luteola 2.jpg' => 'vergroot;  a = bloem van voren, b = bloem van opzij, c = kroonblad, d = doosvrucht',
	
			'Reseda luteola 3.jpg' => 'foto, bloeiwijze',
	
			'Reseda luteola 4.jpg' => 'foto, detail bloeiwijze',
	
			'Reseda luteola 5.jpg' => 'foto, habitus'
	
		),
	
		'Sisymbrium supinum' => array(
	
			'Sisymbrium supinum.jpg' => 'overzicht; a = top vrucht zonder vruchtklep, b = top vruchtklep',
	
			'Sisymbrium supinum 2.jpg' => 'vergroot',
	
			'Sisymbrium supinum 3.jpg' => 'foto, bloeiwijze met vruchten',
	
			'Sisymbrium supinum 4.jpg' => 'foto, bloeiwijze met vruchten'
	
		),
	
		'Sisymbrium officinale' => array(
	
			'Sisymbrium officinale.jpg' => 'overzicht; a = bloemtros, b = vrucht',
	
			'Sisymbrium officinale 2.jpg' => 'vergroot',
	
			'Sisymbrium officinale 3.jpg' => 'foto, habitus bloeiend',
	
			'Sisymbrium officinale 4.jpg' => 'foto, bloeiwijze met bloemen en vruchten'
	
		),
	
		'Sisymbrium altissimum' => array(
	
			'Sisymbrium altissimum.jpg' => 'overzicht',
	
			'Sisymbrium altissimum 2.jpg' => 'vergroot; a = dsn. top vrucht',
	
			'Sisymbrium altissimum 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Sisymbrium orientale' => array(
	
			'Sisymbrium orientale.jpg' => 'overzicht',
	
			'Sisymbrium orientale 2.jpg' => 'vergroot',
	
			'Sisymbrium orientale 3.jpg' => 'foto, habitus bloeiend en in vrucht',
	
			'Sisymbrium orientale 4.jpg' => 'foto, bloeiwijze met bloemen en vruchten'
	
		),
	
		'Sisymbrium irio' => array(
	
			'Sisymbrium irio.jpg' => 'overzicht',
	
			'Sisymbrium irio 2.jpg' => 'vergroot',
	
			'Sisymbrium irio 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Sisymbrium loeselii' => array(
	
			'Sisymbrium loeselii.jpg' => 'overzicht',
	
			'Sisymbrium loeselii 2.jpg' => 'vergroot'
	
		),
	
		'Sisymbrium austriacum subsp. chrysanthum' => array(
	
			'Sisymbrium austriacum chrys.jpg' => 'overzicht',
	
			'Sisymbrium austriacum chry 2.jpg' => 'vergroot',
	
			'Sisymbrium austriacum chry 3.jpg' => 'foto, habitus bloeiend',
	
			'Sisymbrium austriacum chry 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Descurainia sophia' => array(
	
			'Descurainia sophia.jpg' => 'overzicht; a = bloem',
	
			'Descurainia sophia 2.jpg' => 'vergroot',
	
			'Descurainia sophia 3.jpg' => 'foto'
	
		),
	
		'Alliaria petiolata' => array(
	
			'Alliaria petiolata.jpg' => 'overzicht; a = vrucht',
	
			'Alliaria petiolata 2.jpg' => 'vergroot',
	
			'Alliaria petiolata 3.jpg' => 'foto, habitus bloeiend',
	
			'Alliaria petiolata 4.jpg' => 'foto, bloeiwijze vanaf boven gezien'
	
		),
	
		'Arabidopsis arenosa' => array(
	
			'Arabidopsis arenosa.jpg' => 'overzicht',
	
			'Arabidopsis arenosa 2.jpg' => 'vergroot',
	
			'Arabidopsis arenosa 3.jpg' => 'foto, bloeiende habitus',
	
			'Arabidopsis arenosa 4.jpg' => 'foto, bloeiende habitus'
	
		),
	
		'Arabidopsis thaliana' => array(
	
			'Arabidopsis thaliana.jpg' => 'overzicht; a = vrucht',
	
			'Arabidopsis thaliana 2.jpg' => 'vergroot',
	
			'Arabidopsis thaliana 3.jpg' => 'foto, habitus bloeiend',
	
			'Arabidopsis thaliana 4.jpg' => 'foto, habitus bloeiend en in vrucht'
	
		),
	
		'Isatis tinctoria' => array(
	
			'Isatis tinctoria.jpg' => 'overzicht',
	
			'Isatis tinctoria 2.jpg' => 'vergroot; a = bloem, b = vrucht, c = dsn. vrucht',
	
			'Isatis tinctoria 3.jpg' => 'foto, habitus bloeiend',
	
			'Isatis tinctoria 4.jpg' => 'foto, in vrucht',
	
			'Isatis tinctoria 5.jpg' => 'foto, in vrucht'
	
		),
	
		'Bunias orientalis' => array(
	
			'Bunias orientalis.jpg' => 'overzicht; a = vrucht',
	
			'Bunias orientalis 2.jpg' => 'vergroot',
	
			'Bunias orientalis 3.jpg' => 'foto',
	
			'Bunias orientalis 4.jpg' => 'foto'
	
		),
	
		'Erysimum cheiri' => array(
	
			'Erysimum cheiri.jpg' => 'overzicht; a = vrucht',
	
			'Erysimum cheiri 2.jpg' => 'vergroot',
	
			'Erysimum cheiri 3.jpg' => 'foto',
	
			'Erysimum cheiri 4.jpg' => 'foto',
	
			'Erysimum cheiri 5.jpg' => 'foto'
	
		),
	
		'Erysimum cheiranthoides' => array(
	
			'Erysimum cheiranthoides.jpg' => 'overzicht',
	
			'Erysimum cheiranthoides 2.jpg' => 'vergroot',
	
			'Erysimum cheiranthoides 3.jpg' => 'foto',
	
			'Erysimum cheiranthoides 4.jpg' => 'foto'
	
		),
	
		'Erysimum virgatum' => array(
	
			'Erysimum virgatum.jpg' => 'overzicht',
	
			'Erysimum virgatum 2.jpg' => 'vergroot',
	
			'Erysimum virgatum 3.jpg' => 'foto'
	
		),
	
		'Erysimum repandum' => array(
	
			'Erysimum repandum.jpg' => 'overzicht; a = vrucht',
	
			'Erysimum repandum 2.jpg' => 'vergroot',
	
			'Erysimum repandum 3.jpg' => 'foto'
	
		),
	
		'Hesperis matronalis' => array(
	
			'Hesperis matronalis.jpg' => 'overzicht',
	
			'Hesperis matronalis 2.jpg' => 'vergroot; a = bloem, b = vrucht, 1 vruchtklep verwijderd',
	
			'Hesperis matronalis 3.jpg' => 'foto'
	
		),
	
		'Malcolmia maritima' => array(
	
			'Malcolmia maritima.jpg' => 'overzicht; a = vrucht',
	
			'Malcolmia maritima 2.jpg' => 'vergroot'
	
		),
	
		'Barbarea intermedia' => array(
	
			'Barbarea intermedia.jpg' => 'overzicht foto',
	
			'Barbarea intermedia 3.jpg' => 'foto',
	
			'Barbarea intermedia 4.jpg' => 'foto',
	
			'Barbarea intermedia 5.jpg' => 'foto'
	
		),
	
		'Barbarea stricta' => array(
	
			'Barbarea stricta.jpg' => 'overzicht; a = top vrucht',
	
			'Barbarea stricta 2.jpg' => 'vergroot',
	
			'Barbarea stricta 3.jpg' => 'foto'
	
		),
	
		'Barbarea vulgaris' => array(
	
			'Barbarea vulgaris.jpg' => 'overzicht; a = top vrucht',
	
			'Barbarea vulgaris 2.jpg' => 'vergroot',
	
			'Barbarea vulgaris 3.jpg' => 'foto'
	
		),
	
		'Nasturtium microphyllum' => array(
	
			'Nasturtium microphyllum.jpg' => 'overzicht foto',
	
			'Nasturtium microphyllum 2.jpg' => 'vergroot; a = vrucht, b = zaad',
	
			'Nasturtium microphyllum 3.jpg' => 'foto',
	
			'Nasturtium microphyllum 4.jpg' => 'foto'
	
		),
	
		'Nasturtium officinale' => array(
	
			'Nasturtium officinale.jpg' => 'overzicht; a = vrucht + zaad',
	
			'Nasturtium officinale 2.jpg' => 'vergroot',
	
			'Nasturtium officinale 3.jpg' => 'foto',
	
			'Nasturtium officinale 4.jpg' => 'foto',
	
			'Nasturtium officinale 5.jpg' => 'foto'
	
		),
	
		'Rorippa palustris' => array(
	
			'Rorippa palustris.jpg' => 'overzicht',
	
			'Rorippa palustris 2.jpg' => 'vergroot',
	
			'Rorippa palustris 3.jpg' => 'foto, habitus in bloei en vrucht',
	
			'Rorippa palustris 4.jpg' => 'foto, bloemen en vruchten'
	
		),
	
		'Rorippa austriaca' => array(
	
			'Rorippa austriaca.jpg' => 'overzicht; a = vrucht, b = dsn. vrucht',
	
			'Rorippa austriaca 2.jpg' => 'vergroot',
	
			'Rorippa austriaca 3.jpg' => 'foto, in bloei'
	
		),
	
		'Rorippa armoracioides(x)' => array(
	
			'Rorippa armoracioides(x).jpg' => 'overzicht',
	
			'Rorippa armoracioides(x) 2.jpg' => 'vergroot; a = vrucht'
	
		),
	
		'Rorippa amphibia' => array(
	
			'Rorippa amphibia.jpg' => 'overzicht',
	
			'Rorippa amphibia 2.jpg' => 'vergroot',
	
			'Rorippa amphibia 3.jpg' => 'foto, habitus in bloei',
	
			'Rorippa amphibia 4.jpg' => 'foto, habitus in bloei',
	
			'Rorippa anceps(x).jpg' => '(= Rorippa amphibia x sylvestris) vergroot'
	
		),
	
		'Rorippa sylvestris' => array(
	
			'Rorippa sylvestris.jpg' => 'overzicht; a = vrucht',
	
			'Rorippa sylvestris 2.jpg' => 'vergroot',
	
			'Rorippa sylvestris 3.jpg' => 'foto, habitus in bloei',
	
			'Rorippa anceps(x).jpg' => '(= Rorippa amphibia x sylvestris) vergroot'
	
		),
	
		'Armoracia rusticana' => array(
	
			'Armoracia rusticana.jpg' => 'overzicht; a = vrucht, b = dsn. vrucht',
	
			'Armoracia rusticana 2.jpg' => 'vergroot',
	
			'Armoracia rusticana 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Cardamine bulbifera' => array(
	
			'Cardamine bulbifera.jpg' => 'overzicht',
	
			'Cardamine bulbifera 2.jpg' => 'vergroot',
	
			'Cardamine bulbifera 3.jpg' => 'foto',
	
			'Cardamine bulbifera 4.jpg' => 'foto',
	
			'Cardamine bulbifera 5.jpg' => 'foto'
	
		),
	
		'Cardamine impatiens' => array(
	
			'Cardamine impatiens.jpg' => 'overzicht; a = vrucht, b = bladvoet',
	
			'Cardamine impatiens 2.jpg' => 'vergroot',
	
			'Cardamine impatiens 3.jpg' => 'foto',
	
			'Cardamine impatiens 4.jpg' => 'foto'
	
		),
	
		'Cardamine corymbosa' => array(
	
			'Cardamine corymbosa.jpg' => 'overzicht',
	
			'Cardamine corymbosa 3.jpg' => 'foto',
	
			'Cardamine corymbosa 4.jpg' => 'foto'
	
		),
	
		'Cardamine hirsuta' => array(
	
			'Cardamine hirsuta.jpg' => 'overzicht',
	
			'Cardamine hirsuta 2.jpg' => 'vergroot',
	
			'Cardamine hirsuta 3.jpg' => 'foto',
	
			'Cardamine hirsuta 4.jpg' => 'foto'
	
		),
	
		'Cardamine flexuosa' => array(
	
			'Cardamine flexuosa.jpg' => 'overzicht; a = vrucht',
	
			'Cardamine flexuosa 2.jpg' => 'vergroot',
	
			'Cardamine flexuosa 3.jpg' => 'foto',
	
			'Cardamine flexuosa 4.jpg' => 'foto'
	
		),
	
		'Cardamine amara' => array(
	
			'Cardamine amara.jpg' => 'overzicht; a = vrucht',
	
			'Cardamine amara 2.jpg' => 'vergroot',
	
			'Cardamine amara 3.jpg' => 'foto'
	
		),
	
		'Cardamine pratensis' => array(
	
			'Cardamine pratensis.jpg' => 'overzicht',
	
			'Cardamine pratensis 2.jpg' => 'vergroot',
	
			'Cardamine pratensis 3.jpg' => 'foto',
	
			'Cardamine pratensis 4.jpg' => 'foto'
	
		),
	
		'Arabis glabra' => array(
	
			'Arabis glabra.jpg' => 'overzicht; a = vrucht',
	
			'Arabis glabra 2.jpg' => 'vergroot',
	
			'Arabis glabra 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Arabis hirsuta' => array(
	
			'Arabis hirsuta hirsuta.jpg' => 'overzicht',
	
			'Arabis hirsuta hirsuta 2.jpg' => 'vergroot; a = top vrucht',
	
			'Arabis hirsuta sagittata 2.jpg' => 'vergroot'
	
		),
	
		'Arabis hirsuta subsp. hirsuta' => array(
	
			'Arabis hirsuta hirsuta.jpg' => 'overzicht',
	
			'Arabis hirsuta hirsuta 2.jpg' => 'vergroot; a = top vrucht',
	
			'Arabis hirsuta hirsuta 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Arabis hirsuta subsp. sagittata' => array(
	
			'Arabis hirsuta sagittata.jpg' => 'overzicht',
	
			'Arabis hirsuta sagittata 2.jpg' => 'vergroot',
	
			'Arabis hirsuta sagittata 3.jpg' => 'foto, bebladerde stengel',
	
			'Arabis hirsuta sagittata 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Aubrieta deltoidea' => array(
	
			'Aubrieta deltoidea.jpg' => 'overzicht foto',
	
			'Aubrieta deltoidea 3.jpg' => 'foto',
	
			'Aubrieta deltoidea 4.jpg' => 'foto'
	
		),
	
		'Lunaria annua' => array(
	
			'Lunaria annua.jpg' => 'overzicht; a = vrucht',
	
			'Lunaria annua 2.jpg' => 'vergroot',
	
			'Lunaria annua 3.jpg' => 'foto, in bloei',
	
			'Lunaria annua 4.jpg' => 'foto, in bloei',
	
			'Lunaria annua 5.jpg' => 'foto, in vrucht'
	
		),
	
		'Lunaria rediviva' => array(
	
			'Lunaria rediviva.jpg' => 'overzicht',
	
			'Lunaria rediviva 3.jpg' => 'foto, in bloei',
	
			'Lunaria rediviva 4.jpg' => 'foto, in bloei',
	
			'Lunaria rediviva 5.jpg' => 'foto, in bloei'
	
		),
	
		'Alyssum alyssoides' => array(
	
			'Alyssum alyssoides.jpg' => 'overzicht; a = dsn. vrucht',
	
			'Alyssum alyssoides 2.jpg' => 'vergroot',
	
			'Alyssum alyssoides 3.jpg' => 'foto, bloeiwijze met bloemen en vruchten',
	
			'Alyssum alyssoides 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Alyssum saxatile' => array(
	
			'Alyssum saxatile.jpg' => 'overzicht',
	
			'Alyssum saxatile 2.jpg' => 'foto, bloeiend'
	
		),
	
		'Berteroa incana' => array(
	
			'Berteroa incana.jpg' => 'overzicht;  a = deel bloem, b = halve vrucht',
	
			'Berteroa incana 2.jpg' => 'vergroot',
	
			'Berteroa incana 3.jpg' => 'foto',
	
			'Berteroa incana 4.jpg' => 'foto'
	
		),
	
		'Lobularia maritima' => array(
	
			'Lobularia maritima.jpg' => 'overzicht; a = halve vrucht',
	
			'Lobularia maritima 2.jpg' => 'vergroot',
	
			'Lobularia maritima 3.jpg' => 'foto'
	
		),
	
		'Draba muralis' => array(
	
			'Draba muralis.jpg' => 'overzicht; a = bloem, b = vrucht, c = halve vrucht',
	
			'Draba muralis 2.jpg' => 'vergroot',
	
			'Draba muralis 3.jpg' => 'foto, habitus bloeiend',
	
			'Draba muralis 4.jpg' => 'foto, habitus bloeiend en in vrucht'
	
		),
	
		'Erophila verna' => array(
	
			'Erophila verna.jpg' => 'overzicht; a = kroonblad, b = vrucht, c = blad',
	
			'Erophila verna 2.jpg' => 'vergroot',
	
			'Erophila verna 3.jpg' => 'foto, habitus in bloei en vrucht',
	
			'Erophila verna 4.jpg' => 'foto, habitus in bloei en vrucht',
	
			'Erophila verna 5.jpg' => 'foto, habitus in bloei en vrucht'
	
		),
	
		'Cochlearia danica' => array(
	
			'Cochlearia danica.jpg' => 'overzicht; a = vrucht',
	
			'Cochlearia danica 2.jpg' => 'vergroot',
	
			'Cochlearia danica 3.jpg' => 'foto',
	
			'Cochlearia danica 4.jpg' => 'foto'
	
		),
	
		'Cochlearia officinalis' => array(
	
			'Cochlearia officinalis off.jpg' => 'overzicht; a = bloem, b = dsn. vrucht',
	
			'Cochlearia officinalis off 2.jpg' => 'vergroot, subsp. officinalis',
	
			'Cochlearia officinalis ang 2.jpg' => 'vergroot, subsp. anglica'
	
		),
	
		'Cochlearia officinalis subsp. officinalis' => array(
	
			'Cochlearia officinalis off.jpg' => 'overzicht; a = bloem, b = dsn. vrucht',
	
			'Cochlearia officinalis off 2.jpg' => 'vergroot',
	
			'Cochlearia officinalis off 3.jpg' => 'foto'
	
		),
	
		'Cochlearia officinalis subsp. anglica' => array(
	
			'Cochlearia officinalis ang.jpg' => 'overzicht',
	
			'Cochlearia officinalis ang 2.jpg' => 'vergroot',
	
			'Cochlearia officinalis ang 3.jpg' => 'foto'
	
		),
	
		'Camelina sativa' => array(
	
			'Camelina sativa.jpg' => 'overzicht',
	
			'Camelina sativa 2.jpg' => 'vergroot; a = vrucht, b = detail top vrucht',
	
			'Camelina sativa 3.jpg' => 'foto'
	
		),
	
		'Camelina sativa subsp. alyssum' => array(
	
			'Camelina sativa alyssum.jpg' => 'overzicht',
	
			'Camelina sativa alyssum 2.jpg' => 'vergroot; a = detail top vrucht'
	
		),
	
		'Camelina sativa subsp. sativa' => array(
	
			'Camelina sativa.jpg' => 'overzicht'
	
		),
	
		'Neslia paniculata' => array(
	
			'Neslia paniculata.jpg' => 'overzicht',
	
			'Neslia paniculata 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Neslia paniculata 3.jpg' => 'foto'
	
		),
	
		'Capsella bursa-pastoris' => array(
	
			'Capsella bursa-pastoris.jpg' => 'overzicht',
	
			'Capsella bursa-pastoris 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Capsella bursa-pastoris 3.jpg' => 'foto',
	
			'Capsella bursa-pastoris 4.jpg' => 'foto'
	
		),
	
		'Teesdalia nudicaulis' => array(
	
			'Teesdalia nudicaulis.jpg' => 'overzicht; a = bloem, b = vrucht, c = dsn. vrucht',
	
			'Teesdalia nudicaulis 2.jpg' => 'vergroot',
	
			'Teesdalia nudicaulis 3.jpg' => 'foto, habitus'
	
		),
	
		'Thlaspi caerulescens' => array(
	
			'Thlaspi caerulescens.jpg' => 'overzicht',
	
			'Thlaspi caerulescens 2.jpg' => 'vergroot; a = bloem, b = vrucht, c = geopende vrucht',
	
			'Thlaspi caerulescens 3.jpg' => 'foto, bloeiend',
	
			'Thlaspi caerulescens 4.jpg' => 'foto, in vrucht',
	
			'Thlaspi caerulescens 5.jpg' => 'foto, habitus',
	
			'Thlaspi caerulescens 6.jpg' => 'foto, bloeiend en in vrucht'
	
		),
	
		'Thlaspi arvense' => array(
	
			'Thlaspi arvense.jpg' => 'overzicht',
	
			'Thlaspi arvense 2.jpg' => 'vergroot; a = vrucht; b = geopende vrucht',
	
			'Thlaspi arvense 3.jpg' => 'foto, habitus, bloeiend',
	
			'Thlaspi arvense 4.jpg' => 'foto, bloeiwijze met bloemen en vruchten',
	
			'Thlaspi arvense 5.jpg' => 'foto, bloeiwijze met bloemen en vruchten'
	
		),
	
		'Thlaspi perfoliatum' => array(
	
			'Thlaspi perfoliatum.jpg' => 'overzicht',
	
			'Thlaspi perfoliatum 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Thlaspi perfoliatum 3.jpg' => 'foto, bloeiwijze met bloemen en vruchten'
	
		),
	
		'Iberis umbellata' => array(
	
			'Iberis umbellata.jpg' => 'overzicht; a = vrucht',
	
			'Iberis umbellata 2.jpg' => 'vergroot',
	
			'Iberis umbellata 3.jpg' => 'foto',
	
			'Iberis amara 2.jpg' => 'Iberis amara (zie opmerking)',
	
			'Iberis amara 3.jpg' => 'Iberis amara (zie opmerking)'
	
		),
	
		'Lepidium perfoliatum' => array(
	
			'Lepidium perfoliatum.jpg' => 'overzicht; a = vrucht',
	
			'Lepidium perfoliatum 2.jpg' => 'vergroot',
	
			'Lepidium perfoliatum 3.jpg' => 'foto'
	
		),
	
		'Lepidium draba' => array(
	
			'Lepidium draba.jpg' => 'overzicht; a = bloem, b = vrucht',
	
			'Lepidium draba 2.jpg' => 'vergroot',
	
			'Lepidium draba 3.jpg' => 'foto',
	
			'Lepidium draba 4.jpg' => 'foto'
	
		),
	
		'Lepidium campestre' => array(
	
			'Lepidium campestre.jpg' => 'overzicht; a = vrucht',
	
			'Lepidium campestre 2.jpg' => 'vergroot',
	
			'Lepidium campestre 3.jpg' => 'foto',
	
			'Lepidium campestre 4.jpg' => 'foto',
	
			'Lepidium campestre 5.jpg' => 'foto'
	
		),
	
		'Lepidium heterophyllum' => array(
	
			'Lepidium heterophyllum.jpg' => 'overzicht foto',
	
			'Lepidium heterophyllum 3.jpg' => 'foto'
	
		),
	
		'Lepidium graminifolium' => array(
	
			'Lepidium graminifolium.jpg' => 'overzicht; a = bloem, b = vrucht',
	
			'Lepidium graminifolium 2.jpg' => 'vergroot'
	
		),
	
		'Lepidium latifolium' => array(
	
			'Lepidium latifolium.jpg' => 'overzicht; a = vrucht',
	
			'Lepidium latifolium 2.jpg' => 'vergroot',
	
			'Lepidium latifolium 3.jpg' => 'foto',
	
			'Lepidium latifolium 4.jpg' => 'foto'
	
		),
	
		'Lepidium sativum' => array(
	
			'Lepidium sativum.jpg' => 'overzicht; a = geopende vrucht',
	
			'Lepidium sativum 2.jpg' => 'vergroot',
	
			'Lepidium sativum 3.jpg' => 'foto'
	
		),
	
		'Lepidium ruderale' => array(
	
			'Lepidium ruderale.jpg' => 'overzicht; a = vrucht',
	
			'Lepidium ruderale 2.jpg' => 'vergroot',
	
			'Lepidium ruderale 3.jpg' => 'foto',
	
			'Lepidium ruderale 4.jpg' => 'foto'
	
		),
	
		'Lepidium virginicum' => array(
	
			'Lepidium virginicum.jpg' => 'overzicht',
	
			'Lepidium virginicum 2.jpg' => 'vergroot; a = vrucht, b = stengel oppervlak',
	
			'Lepidium virginicum 3.jpg' => 'tekening',
	
			'Lepidium neglectum 2.jpg' => 'Zie opmerking; vergroot; a = vrucht, b = stengel oppervlak'
	
		),
	
		'Lepidium densiflorum' => array(
	
			'Lepidium densiflorum.jpg' => 'overzicht',
	
			'Lepidium densiflorum 2.jpg' => 'vergroot; a = vrucht, b = stengel oppervlak',
	
			'Lepidium densiflorum 3.jpg' => 'foto'
	
		),
	
		'Coronopus squamatus' => array(
	
			'Coronopus squamatus.jpg' => 'overzicht; a = vrucht',
	
			'Coronopus squamatus 2.jpg' => 'vergroot',
	
			'Coronopus squamatus 3.jpg' => 'foto',
	
			'Coronopus squamatus 4.jpg' => 'foto'
	
		),
	
		'Coronopus didymus' => array(
	
			'Coronopus didymus.jpg' => 'overzicht; a = bloem, b = vrucht',
	
			'Coronopus didymus 2.jpg' => 'vergroot',
	
			'Coronopus didymus 3.jpg' => 'foto',
	
			'Coronopus didymus 4.jpg' => 'foto'
	
		),
	
		'Subularia aquatica' => array(
	
			'Subularia aquatica.jpg' => 'overzicht; a = vrucht, b = geopende vrucht',
	
			'Subularia aquatica 2.jpg' => 'vergroot'
	
		),
	
		'Conringia orientalis' => array(
	
			'Conringia orientalis.jpg' => 'overzicht',
	
			'Conringia orientalis 2.jpg' => 'vergroot',
	
			'Conringia orientalis 3.jpg' => 'foto'
	
		),
	
		'Diplotaxis tenuifolia' => array(
	
			'Diplotaxis tenuifolia.jpg' => 'overzicht',
	
			'Diplotaxis tenuifolia 2.jpg' => 'vergroot',
	
			'Diplotaxis tenuifolia 3.jpg' => 'foto',
	
			'Diplotaxis tenuifolia 4.jpg' => 'foto',
	
			'Diplotaxis tenuifolia 5.jpg' => 'foto'
	
		),
	
		'Diplotaxis muralis' => array(
	
			'Diplotaxis muralis.jpg' => 'overzicht',
	
			'Diplotaxis muralis 2.jpg' => 'vergroot',
	
			'Diplotaxis muralis 3.jpg' => 'foto'
	
		),
	
		'Brassica nigra' => array(
	
			'Brassica nigra.jpg' => 'overzicht; a = bloem, b = vrucht',
	
			'Brassica nigra 2.jpg' => 'vergroot',
	
			'Brassica nigra 3.jpg' => 'foto',
	
			'Brassica nigra 4.jpg' => 'foto'
	
		),
	
		'Brassica oleracea' => array(
	
			'Brassica oleracea.jpg' => 'overzicht',
	
			'Brassica oleracea 2.jpg' => 'vergroot',
	
			'Brassica oleracea 3.jpg' => 'foto, bloeiwijzen',
	
			'Brassica oleracea 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Brassica oleracea subsp. oleracea' => array(
	
			'Brassica oleracea oleracea.jpg' => 'overzicht; a = vrucht',
	
			'Brassica oleracea oleracea 2.jpg' => 'vergroot',
	
			'Brassica oleracea oleracea 3.jpg' => 'foto'
	
		),
	
		'Brassica oleracea var. cultivars' => array(
	
			'Brassica oleracea cult.jpg' => 'overzicht verschillende gecultiveerde kolen',
	
			'Brassica oleracea cult 2.jpg' => 'foto, Rode kool',
	
			'Brassica oleracea cult 3.jpg' => 'foto, Savooiekool',
	
			'Brassica oleracea cult 4.jpg' => 'foto, Spitskool',
	
			'Brassica oleracea cult 5.jpg' => 'foto, Wiite kool',
	
			'Brassica oleracea cult 6.jpg' => 'foto, Bloemkool',
	
			'Brassica oleracea cult 7.jpg' => 'foto, Boerenkool',
	
			'Brassica oleracea cult 8.jpg' => 'foto, Broccoli',
	
			'Brassica oleracea cult 9.jpg' => 'foto, Spruitjes'
	
		),
	
		'Brassica napus' => array(
	
			'Brassica napus.jpg' => 'overzicht; a = vrucht, b = geopende vrucht',
	
			'Brassica napus 2.jpg' => 'vergroot',
	
			'Brassica napus 3.jpg' => 'foto, bloeiend',
	
			'Brassica napus 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Brassica rapa' => array(
	
			'Brassica rapa.jpg' => 'overzicht',
	
			'Brassica rapa 2.jpg' => 'vergroot',
	
			'Brassica rapa 3.jpg' => 'foto'
	
		),
	
		'Sinapis arvensis' => array(
	
			'Sinapis arvensis.jpg' => 'overzicht; a = bloem, b = geopende vrucht',
	
			'Sinapis arvensis 2.jpg' => 'vergroot',
	
			'Sinapis arvensis 3.jpg' => 'foto, bloeiend',
	
			'Sinapis arvensis 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Sinapis alba' => array(
	
			'Sinapis alba.jpg' => 'overzicht; a = bloem, b = vrucht, c = geopende vrucht',
	
			'Sinapis alba 2.jpg' => 'vergroot',
	
			'Sinapis alba 3.jpg' => 'foto, bloeiwijze met bloemen en vruchten',
	
			'Sinapis alba 4.jpg' => 'foto, bloeiwijze met bloemen en vruchten'
	
		),
	
		'Eruca vesicaria' => array(
	
			'Eruca vesicaria.jpg' => 'overzicht',
	
			'Eruca vesicaria 2.jpg' => 'vergroot',
	
			'Eruca vesicaria 3.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Erucastrum gallicum' => array(
	
			'Erucastrum gallicum.jpg' => 'overzicht',
	
			'Erucastrum gallicum 2.jpg' => 'vergroot',
	
			'Erucastrum gallicum 3.jpg' => 'foto',
	
			'Erucastrum gallicum 4.jpg' => 'foto'
	
		),
	
		'Coincya monensis' => array(
	
			'Coincya monensis.jpg' => 'overzicht; a = vrucht',
	
			'Coincya monensis 2.jpg' => 'vergroot',
	
			'Coincya monensis 3.jpg' => 'foto'
	
		),
	
		'Hirschfeldia incana' => array(
	
			'Hirschfeldia incana.jpg' => 'overzicht ',
	
			'Hirschfeldia incana 2.jpg' => 'vergroot',
	
			'Hirschfeldia incana 3.jpg' => 'foto',
	
			'Hirschfeldia incana 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Cakile maritima' => array(
	
			'Cakile maritima.jpg' => 'overzicht;  a = vrucht, b = dsn. vrucht',
	
			'Cakile maritima 2.jpg' => 'vergroot',
	
			'Cakile maritima 3.jpg' => 'foto'
	
		),
	
		'Rapistrum rugosum' => array(
	
			'Rapistrum rugosum.jpg' => 'overzicht; a = vrucht',
	
			'Rapistrum rugosum 2.jpg' => 'vergroot',
	
			'Rapistrum rugosum 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Crambe maritima' => array(
	
			'Crambe maritima.jpg' => 'overzicht',
	
			'Crambe maritima 2.jpg' => 'vergroot; a = vrucht, b = dsn. vrucht, c = meeldraad',
	
			'Crambe maritima 3.jpg' => 'foto',
	
			'Crambe maritima 4.jpg' => 'foto',
	
			'Crambe maritima 5.jpg' => 'foto',
	
			'Crambe maritima 6.jpg' => 'foto'
	
		),
	
		'Crambe abyssinica' => array(
	
			'Crambe abyssinica.jpg' => 'overzicht; a = bloeiwijze, b = vruchten',
	
			'Crambe abyssinica 2.jpg' => 'vergroot'
	
		),
	
		'Calepina irregularis' => array(
	
			'Calepina irregularis.jpg' => 'overzicht; a = vrucht',
	
			'Calepina irregularis 2.jpg' => 'vergroot',
	
			'Calepina irregularis 3.jpg' => 'foto',
	
			'Calepina irregularis 4.jpg' => 'foto'
	
		),
	
		'Raphanus raphanistrum' => array(
	
			'Raphanus raphanistrum.jpg' => 'overzicht; a = vrucht',
	
			'Raphanus raphanistrum 2.jpg' => 'vergroot',
	
			'Raphanus raphanistrum 3.jpg' => 'foto, habitus in bloei en vrucht'
	
		),
	
		'Raphanus sativus' => array(
	
			'Raphanus sativus.jpg' => 'overzicht; a = vruchtwijze',
	
			'Raphanus sativus 2.jpg' => 'vergroot',
	
			'Raphanus sativus 3.jpg' => 'foto, bloemen en vruchten'
	
		),
	
		'Tilia platyphyllos' => array(
	
			'Tilia platyphyllos.jpg' => 'overzicht',
	
			'Tilia platyphyllos 2.jpg' => 'vergroot',
	
			'Tilia platyphyllos 3.jpg' => 'foto, in bloei',
	
			'Tilia platyphyllos 4.jpg' => 'foto, in bloei'
	
		),
	
		'Tilia cordata' => array(
	
			'Tilia cordata.jpg' => 'overzicht',
	
			'Tilia cordata 2.jpg' => 'vergroot; a = dsn. bloem, b = vruchtwijze',
	
			'Tilia cordata 3.jpg' => 'foto, in bloei'
	
		),
	
		'Tilia vulgaris(x)' => array(
	
			'Tilia vulgaris(x).jpg' => 'overzicht foto',
	
			'Tilia vulgaris(x) 3.jpg' => 'foto, in bloei',
	
			'Tilia vulgaris(x) 4.jpg' => 'foto, in bloei'
	
		),
	
		'Malva alcea' => array(
	
			'Malva alcea.jpg' => 'overzicht',
	
			'Malva alcea 2.jpg' => 'vergroot',
	
			'Malva alcea 3.jpg' => 'foto'
	
		),
	
		'Malva moschata' => array(
	
			'Malva moschata.jpg' => 'overzicht',
	
			'Malva moschata 2.jpg' => 'vergroot',
	
			'Malva moschata 3.jpg' => 'foto'
	
		),
	
		'Malva neglecta' => array(
	
			'Malva neglecta.jpg' => 'overzicht',
	
			'Malva neglecta 2.jpg' => 'vergroot; a = dsn. Bloem',
	
			'Malva neglecta 3.jpg' => 'vruchtkelk en deelvruchtje',
	
			'Malva neglecta 4.jpg' => 'foto'
	
		),
	
		'Malva sylvestris' => array(
	
			'Malva sylvestris.jpg' => 'overzicht',
	
			'Malva sylvestris 2.jpg' => 'vergroot',
	
			'Malva sylvestris 3.jpg' => 'foto'
	
		),
	
		'Malva verticillata' => array(
	
			'Malva verticillata.jpg' => 'overzicht',
	
			'Malva verticillata 2.jpg' => 'vruchtkelk en deelvruchtje'
	
		),
	
		'Malva pusilla' => array(
	
			'Malva pusilla.jpg' => 'overzicht',
	
			'Malva pusilla 2.jpg' => 'vergroot; a = stamper',
	
			'Malva pusilla 3.jpg' => 'vruchtkelk en deelvruchtje',
	
			'Malva pusilla 4.jpg' => 'foto'
	
		),
	
		'Malva parviflora' => array(
	
			'Malva parviflora.jpg' => 'detail; a = vruchtkelk, b = deelvruchtje',
	
			'Malva parviflora 2.jpg' => 'vruchtkelk en deelvruchtje'
	
		),
	
		'Anoda cristata' => array(
	
			'Anoda cristata.jpg' => 'overzicht foto',
	
			'Anoda cristata 3.jpg' => 'foto, habitus'
	
		),
	
		'Althaea hirsuta' => array(
	
			'Althaea hirsuta.jpg' => 'overzicht',
	
			'Althaea hirsuta 2.jpg' => 'vergroot; a = vruchtkelk, b = deelvruchtje',
	
			'Althaea hirsuta 3.jpg' => 'foto, habitus bloeiend',
	
			'Althaea hirsuta 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Althaea officinalis' => array(
	
			'Althaea officinalis.jpg' => 'overzicht',
	
			'Althaea officinalis 2.jpg' => 'vergroot; a = stamper, b = meeldraadkolom, c = vrucht',
	
			'Althaea officinalis 3.jpg' => 'foto, habitus bloeiend',
	
			'Althaea officinalis 4.jpg' => 'foto, bloemen'
	
		),
	
		'Alcea rosea' => array(
	
			'Alcea rosea.jpg' => 'overzicht',
	
			'Alcea rosea 2.jpg' => 'foto, bloeiend',
	
			'Alcea rosea 3.jpg' => 'foto, bloeiend',
	
			'Alcea rosea 4.jpg' => 'foto, bloeiend',
	
			'Alcea rosea 5.jpg' => 'foto, bloem '
	
		),
	
		'Abutilon theophrasti' => array(
	
			'Abutilon theophrasti.jpg' => 'overzicht',
	
			'Abutilon theophrasti 2.jpg' => 'vergroot; a = opengesprongen deelvrucht',
	
			'Abutilon theophrasti 3.jpg' => 'foto, habitus bloeiend en in vrucht',
	
			'Abutilon theophrasti 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Hibiscus trionum' => array(
	
			'Hibiscus trionum.jpg' => 'overzicht',
	
			'Hibiscus trionum 2.jpg' => 'vergroot; a = vruchtkelk',
	
			'Hibiscus trionum 3.jpg' => 'foto'
	
		),
	
		'Tuberaria guttata' => array(
	
			'Tuberaria guttata.jpg' => 'overzicht; a = vruchtkelk',
	
			'Tuberaria guttata 2.jpg' => 'vergroot',
	
			'Tuberaria guttata 3.jpg' => 'foto, habitus in bloei',
	
			'Tuberaria guttata 4.jpg' => 'foto, bloem',
	
			'Tuberaria guttata 5.jpg' => 'foto, bloem'
	
		),
	
		'Helianthemum nummularium' => array(
	
			'Helianthemum nummularium.jpg' => 'overzicht',
	
			'Helianthemum nummularium 2.jpg' => 'vergroot',
	
			'Helianthemum nummularium 3.jpg' => 'foto',
	
			'Helianthemum nummularium 4.jpg' => 'foto'
	
		),
	
		'Daphne mezereum' => array(
	
			'Daphne mezereum.jpg' => 'overzicht',
	
			'Daphne mezereum 2.jpg' => 'vergroot; a = geopende bloem',
	
			'Daphne mezereum 3.jpg' => 'foto, in bloei',
	
			'Daphne mezereum 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Daphne laureola' => array(
	
			'Daphne laureola.jpg' => 'overzicht',
	
			'Daphne laureola 2.jpg' => 'foto',
	
			'Daphne laureola 3.jpg' => 'foto',
	
			'Daphne laureola 4.jpg' => 'foto',
	
			'Daphne laureola 6.jpg' => 'foto',
	
			'Daphne laureola 5.jpg' => 'foto',
	
			'Daphne laureola 7.jpg' => 'foto'
	
		),
	
		'Koelreuteria paniculata' => array(
	
			'Koelreuteria paniculata.jpg' => 'overzicht foto',
	
			'Koelreuteria paniculata 2.jpg' => 'blad',
	
			'Koelreuteria paniculata 3.jpg' => 'foto',
	
			'Koelreuteria paniculata 4.jpg' => 'foto'
	
		),
	
		'Aesculus hippocastanum' => array(
	
			'Aesculus hippocastanum.jpg' => 'overzicht',
	
			'Aesculus hippocastanum 2.jpg' => 'vergroot; a = dsn. bloem, b = vrucht',
	
			'Aesculus hippocastanum 3.jpg' => 'foto, bloeiend',
	
			'Aesculus hippocastanum 4.jpg' => 'foto, bloeiend',
	
			'Aesculus hippocastanum 5.jpg' => 'foto, boom',
	
			'Aesculus hippocastanum 6.jpg' => 'foto, bast',
	
			'Aesculus hippocastanum 7.jpg' => 'foto, vruchten en zaden'
	
		),
	
		'Aesculus carnea' => array(
	
			'Aesculus carnea.jpg' => 'overzicht foto',
	
			'Aesculus carnea 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Acer negundo' => array(
	
			'Acer negundo.jpg' => 'overzicht',
	
			'Acer negundo 2.jpg' => 'vergroot',
	
			'Acer negundo 3.jpg' => 'foto, vruchtjes',
	
			'Acer negundo 4.jpg' => 'foto, in bloei'
	
		),
	
		'Acer saccharinum' => array(
	
			'Acer saccharinum.jpg' => 'overzicht, bladen',
	
			'Acer saccharinum 2.jpg' => 'foto, in vrucht',
	
			'Acer saccharinum 3.jpg' => 'foto, in bloei',
	
			'Acer saccharinum 4.jpg' => 'foto, bladen',
	
			'Acer saccharinum 5.jpg' => 'foto, bast'
	
		),
	
		'Acer pseudoplatanus' => array(
	
			'Acer pseudoplatanus.jpg' => 'overzicht; a = bloem',
	
			'Acer pseudoplatanus 2.jpg' => 'vergroot',
	
			'Acer pseudoplatanus 3.jpg' => 'foto, bloeiwijzen',
	
			'Acer pseudoplatanus 4.jpg' => 'foto, met vruchtjes'
	
		),
	
		'Acer platanoides' => array(
	
			'Acer platanoides.jpg' => 'overzicht',
	
			'Acer platanoides 2.jpg' => 'vergroot; a = manlijke bloem, b = tweeslachtige bloem',
	
			'Acer platanoides 3.jpg' => 'foto, in bloei',
	
			'Acer platanoides 4.jpg' => 'foto, in bloei',
	
			'Acer platanoides 5.jpg' => 'foto, in vrucht'
	
		),
	
		'Acer campestre' => array(
	
			'Acer campestre.jpg' => 'overzicht',
	
			'Acer campestre 2.jpg' => 'vergroot',
	
			'Acer campestre 3.jpg' => 'foto, bloeiwijze',
	
			'Acer campestre 4.jpg' => 'foto, jonge vruchtjes',
	
			'Acer campestre 5.jpg' => 'foto, blad',
	
			'Acer campestre 6.jpg' => 'foto, bast',
	
			'Acer campestre 7.jpg' => 'foto, vruchten'
	
		),
	
		'Rhus radicans' => array(
	
			'Rhus radicans.jpg' => 'overzicht',
	
			'Rhus radicans 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem, c = vruchtpluim, d = vrucht',
	
			'Rhus radicans 3.jpg' => 'foto, habitus'
	
		),
	
		'Rhus typhina' => array(
	
			'Rhus typhina.jpg' => 'overzicht foto',
	
			'Rhus typhina 3.jpg' => 'foto, habitus',
	
			'Rhus typhina 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Ailanthus altissima' => array(
	
			'Ailanthus altissima.jpg' => 'overzicht; a = bloemknop, b = manlijke bloem, c = vruchten',
	
			'Ailanthus altissima 2.jpg' => 'vergroot',
	
			'Ailanthus altissima 3.jpg' => 'foto, bloeiwijze',
	
			'Ailanthus altissima 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Ruta graveolens' => array(
	
			'Ruta graveolens.jpg' => 'overzicht'
	
		),
	
		'Cornus suecica' => array(
	
			'Cornus suecica.jpg' => 'overzicht',
	
			'Cornus suecica 2.jpg' => 'vergroot',
	
			'Cornus suecica 3.jpg' => 'foto',
	
			'Cornus suecica 4.jpg' => 'foto'
	
		),
	
		'Cornus mas' => array(
	
			'Cornus mas.jpg' => 'overzicht',
	
			'Cornus mas 2.jpg' => 'vergroot; a = bloem, b = dsn. vrucht',
	
			'Cornus mas 3.jpg' => 'foto, in bloei'
	
		),
	
		'Cornus sericea' => array(
	
			'Cornus sericea.jpg' => 'overzicht foto',
	
			'Cornus sericea 3.jpg' => 'foto, in bloei',
	
			'Cornus sericea 4.jpg' => 'foto, in vrucht',
	
			'Cornus sericea 5.jpg' => 'foto, bloeiwijze, jonge vruchten, onder- en bovenkant blad',
	
			'Cornus sericea 6.jpg' => 'foto, bloeiwijze, rijpe vruchten, onder- en bovenkant blad'
	
		),
	
		'Cornus sanguinea' => array(
	
			'Cornus sanguinea.jpg' => 'overzicht',
	
			'Cornus sanguinea 2.jpg' => 'vergroot',
	
			'Cornus sanguinea 3.jpg' => 'foto, in bloei',
	
			'Cornus sanguinea 4.jpg' => 'foto, in bloei',
	
			'Cornus sanguinea 5.jpg' => 'foto, jonge vruchten, onder- en bovenkant blad',
	
			'Cornus sanguinea 6.jpg' => 'foto, rijpe vruchten'
	
		),
	
		'Philadelphus coronarius' => array(
	
			'Philadelphus coronarius.jpg' => 'overzicht',
	
			'Philadelphus coronarius 2.jpg' => 'vergroot; a = dsn. bloemknop, b = stamper',
	
			'Philadelphus coronarius 3.jpg' => 'foto',
	
			'Philadelphus coronarius 4.jpg' => 'foto'
	
		),
	
		'Deutzia scabra' => array(
	
			'Deutzia scabra.jpg' => 'overzicht foto',
	
			'Deutzia scabra 3.jpg' => 'foto'
	
		),
	
		'Deutzia gracilis' => array(
	
			'Deutzia gracilis.jpg' => 'overzicht foto',
	
			'Deutzia gracilis 3.jpg' => 'foto'
	
		),
	
		'Impatiens glandulifera' => array(
	
			'Impatiens glandulifera.jpg' => 'overzicht foto',
	
			'Impatiens glandulifera 3.jpg' => 'foto',
	
			'Impatiens glandulifera 4.jpg' => 'foto'
	
		),
	
		'Impatiens balfourii' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Impatiens parviflora' => array(
	
			'Impatiens parviflora.jpg' => 'overzicht',
	
			'Impatiens parviflora 2.jpg' => 'vergroot',
	
			'Impatiens parviflora 3.jpg' => 'foto',
	
			'Impatiens parviflora 4.jpg' => 'foto'
	
		),
	
		'Impatiens noli-tangere' => array(
	
			'Impatiens noli-tangere.jpg' => 'overzicht',
	
			'Impatiens noli-tangere 2.jpg' => 'vergroot; a = bloem, deel opengelegd',
	
			'Impatiens noli-tangere 3.jpg' => 'foto',
	
			'Impatiens noli-tangere 4.jpg' => 'foto'
	
		),
	
		'Impatiens capensis' => array(
	
			'Impatiens capensis.jpg' => 'overzicht foto',
	
			'Impatiens capensis 3.jpg' => 'foto',
	
			'Impatiens capensis 4.jpg' => 'foto'
	
		),
	
		'Polemonium caeruleum' => array(
	
			'Polemonium caeruleum.jpg' => 'overzicht',
	
			'Polemonium caeruleum 2.jpg' => 'vergroot',
	
			'Polemonium caeruleum 3.jpg' => 'foto'
	
		),
	
		'Samolus valerandi' => array(
	
			'Samolus valerandi.jpg' => 'overzicht',
	
			'Samolus valerandi 2.jpg' => 'vergroot',
	
			'Samolus valerandi 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Centunculus minimus' => array(
	
			'Centunculus minima.jpg' => 'overzicht',
	
			'Centunculus minima 2.jpg' => 'vergroot; a = bloem, b = vrucht, c = geopende vrucht',
	
			'Centunculus minima 3.jpg' => 'foto',
	
			'Centunculus minima 4.jpg' => 'foto',
	
			'Centunculus minima 5.jpg' => 'foto'
	
		),
	
		'Trientalis europaea' => array(
	
			'Trientalis europaea.jpg' => 'overzicht',
	
			'Trientalis europaea 2.jpg' => 'vergroot',
	
			'Trientalis europaea 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Anagallis tenella' => array(
	
			'Anagallis tenella.jpg' => 'overzicht',
	
			'Anagallis tenella 2.jpg' => 'vergroot',
	
			'Anagallis tenella 3.jpg' => 'foto, habitus bloeiend',
	
			'Anagallis tenella 4.jpg' => 'foto, habitus bloeiend',
	
			'Anagallis tenella 5.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Anagallis arvensis' => array(
	
			'Anagallis arvensis arvensis.jpg' => 'overzicht; a = vrucht, b = geopende vrucht',
	
			'Anagallis arvensis arvens 2.jpg' => 'vergroot',
	
			'Anagallis arvensis krslippen.jpg' => 'beharing kroonslippen ondersoorten'
	
		),
	
		'Anagallis arvensis subsp. arvensis' => array(
	
			'Anagallis arvensis arvensis.jpg' => 'overzicht; a = vrucht, b = geopende vrucht',
	
			'Anagallis arvensis arvens 2.jpg' => 'vergroot',
	
			'Anagallis arvensis arvens 3.jpg' => 'foto, bloeiend, rood',
	
			'Anagallis arvensis krslippen.jpg' => 'klieren op kroonslippen'
	
		),
	
		'Anagallis arvensis subsp. foemina' => array(
	
			'Anagallis arvensis foemina.jpg' => 'overzicht',
	
			'Anagallis arvensis foemina 2.jpg' => 'vergroot',
	
			'Anagallis arvensis foemina 3.jpg' => 'foto, habitus bloeiend',
	
			'Anagallis arvensis krslippen.jpg' => 'klieren op kroonslippen'
	
		),
	
		'Lysimachia nummularia' => array(
	
			'Lysimachia nummularia.jpg' => 'overzicht',
	
			'Lysimachia nummularia 2.jpg' => 'vergroot',
	
			'Lysimachia nummularia 3.jpg' => 'foto',
	
			'Lysimachia nummularia 4.jpg' => 'foto'
	
		),
	
		'Lysimachia nemorum' => array(
	
			'Lysimachia nemorum.jpg' => 'overzicht',
	
			'Lysimachia nemorum 2.jpg' => 'vergroot',
	
			'Lysimachia nemorum 3.jpg' => 'foto'
	
		),
	
		'Lysimachia thyrsiflora' => array(
	
			'Lysimachia thyrsiflora.jpg' => 'overzicht',
	
			'Lysimachia thyrsiflora 2.jpg' => 'vergroot',
	
			'Lysimachia thyrsiflora 3.jpg' => 'foto'
	
		),
	
		'Lysimachia vulgaris' => array(
	
			'Lysimachia vulgaris.jpg' => 'overzicht',
	
			'Lysimachia vulgaris 2.jpg' => 'vergroot; a = dsn. bloem, b = dsn. vrucht',
	
			'Lysimachia vulgaris 3.jpg' => 'foto',
	
			'Lysimachia vulgaris 4.jpg' => 'foto'
	
		),
	
		'Lysimachia punctata' => array(
	
			'Lysimachia punctata.jpg' => 'overzicht',
	
			'Lysimachia punctata 2.jpg' => 'vergroot',
	
			'Lysimachia punctata 3.jpg' => 'foto',
	
			'Lysimachia punctata 4.jpg' => 'foto'
	
		),
	
		'Glaux maritima' => array(
	
			'Glaux maritima.jpg' => 'overzicht',
	
			'Glaux maritima 2.jpg' => 'vergroot',
	
			'Glaux maritima 3.jpg' => 'foto'
	
		),
	
		'Primula vulgaris' => array(
	
			'Primula vulgaris.jpg' => 'overzicht',
	
			'Primula vulgaris 2.jpg' => 'vergroot',
	
			'Primula vulgaris 3.jpg' => 'foto',
	
			'Primula vulgaris 4.jpg' => 'foto, habitus, bloeiend'
	
		),
	
		'Primula veris' => array(
	
			'Primula veris.jpg' => 'overzicht',
	
			'Primula veris 2.jpg' => 'vergroot; a = dsn. langstijlige bloem, b = dsn. kortstijlige bloem, c = dsn. vruchtkelk',
	
			'Primula veris 3.jpg' => 'foto',
	
			'Primula veris 4.jpg' => 'foto',
	
			'Primula veris 5.jpg' => 'foto'
	
		),
	
		'Primula elatior' => array(
	
			'Primula elatior.jpg' => 'overzicht',
	
			'Primula elatior 2.jpg' => 'vergroot',
	
			'Primula elatior 3.jpg' => 'foto; kortstijlige bloemen',
	
			'Primula elatior 4.jpg' => 'foto; langstijlige bloemen',
	
			'Primula elatior 5.jpg' => 'foto'
	
		),
	
		'Hottonia palustris' => array(
	
			'Hottonia palustris.jpg' => 'overzicht',
	
			'Hottonia palustris 2.jpg' => 'vergroot; a = dsn. bloem',
	
			'Hottonia palustris 3.jpg' => 'foto',
	
			'Hottonia palustris 4.jpg' => 'foto'
	
		),
	
		'Actinidia deliciosa' => array(
	
			'Actinidia deliciosa.jpg' => 'overzicht',
	
			'Actinidia deliciosa 3.jpg' => 'foto, habitus',
	
			'Actinidia deliciosa 4.jpg' => 'foto, bloem en vruchten'
	
		),
	
		'Clethra alnifolia' => array(
	
			'Clethra alnifolia.jpg' => 'overzicht',
	
			'Clethra alnifolia 2.jpg' => 'bloeiende takjes met details'
	
		),
	
		'Empetrum nigrum' => array(
	
			'Empetrum nigrum.jpg' => 'overzicht',
	
			'Empetrum nigrum 2.jpg' => 'vergroot;  a = manlijke bloem van opzij, b = idem, van boven',
	
			'Empetrum nigrum 3.jpg' => 'foto',
	
			'Empetrum nigrum 4.jpg' => 'foto'
	
		),
	
		'Erica tetralix' => array(
	
			'Erica tetralix.jpg' => 'overzicht',
	
			'Erica tetralix 2.jpg' => 'vergroot; a = bloem, b = blad van onderen',
	
			'Erica tetralix 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Erica cinerea' => array(
	
			'Erica cinerea.jpg' => 'overzicht',
	
			'Erica cinerea 2.jpg' => 'vergroot; a = meeldraad van voren, b = idem, van opzij',
	
			'Erica cinerea 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Erica scoparia' => array(
	
			'Erica scoparia.jpg' => 'overzicht foto',
	
			'Erica scoparia 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Calluna vulgaris' => array(
	
			'Calluna vulgaris.jpg' => 'overzicht',
	
			'Calluna vulgaris 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem, c = vrucht',
	
			'Calluna vulgaris 3.jpg' => 'foto'
	
		),
	
		'Rhododendron ponticum' => array(
	
			'Rhodondendron ponticum.jpg' => 'overzicht',
	
			'Rhodondendron ponticum 2.jpg' => 'vergroot',
	
			'Rhododendron ponticum 3.jpg' => 'foto, habitus in bloei'
	
		),
	
		'Arctostaphylos uva-ursi' => array(
	
			'Arctostaphylos uva-ursi.jpg' => 'overzicht',
	
			'Arctostaphylos uva-ursi 2.jpg' => 'vergroot'
	
		),
	
		'Andromeda polifolia' => array(
	
			'Andromeda polifolia.jpg' => 'overzicht',
	
			'Andromeda polifolia 2.jpg' => 'vergroot',
	
			'Andromeda polifolia 3.jpg' => 'foto, bloeiend',
	
			'Andromeda polifolia 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Vaccinium oxycoccos' => array(
	
			'Vaccinium oxycoccus.jpg' => 'overzicht; a = dsn. vrucht',
	
			'Vaccinium oxycoccus 2.jpg' => 'vergroot',
	
			'Vaccinium oxycoccus 3.jpg' => 'foto, in bloei',
	
			'Vaccinium oxycoccus 4.jpg' => 'foto, in bloei'
	
		),
	
		'Vaccinium macrocarpon' => array(
	
			'Vaccinium macrocarpon.jpg' => 'overzicht',
	
			'Vaccinium macrocarpon 2.jpg' => 'vergroot',
	
			'Vaccinium macrocarpon 3.jpg' => 'foto, in bloei',
	
			'Vaccinium macrocarpon 4.jpg' => 'foto, in bloei'
	
		),
	
		'Vaccinium vitis-idaea' => array(
	
			'Vaccinium vitis-idaea.jpg' => 'overzicht',
	
			'Vaccinium vitis-idaea 2.jpg' => 'vergroot; a = meeldraad',
	
			'Vaccinium vitis-idaea 3.jpg' => 'foto, in bloei en vrucht',
	
			'Vaccinium vitis-idaea 4.jpg' => 'foto, in bloei',
	
			'Vaccinium vitis-idaea 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Vaccinium corymbosum' => array(
	
			'Vaccinium corymbosum.jpg' => 'overzicht',
	
			'Vaccinium corymbosum 2.jpg' => 'vergroot',
	
			'Vaccinium corymbosum 3.jpg' => 'foto, in vrucht',
	
			'Vaccinium corymbosum 4.jpg' => 'foto, in bloei'
	
		),
	
		'Vaccinium myrtillus' => array(
	
			'Vaccinium myrtillus.jpg' => 'overzicht',
	
			'Vaccinium myrtillus 2.jpg' => 'vergroot; a = bloem, b = bloem zonder de bloemkroon, c = meeldraad, d = vruchten',
	
			'Vaccinium myrtillus 3.jpg' => 'foto, in bloei',
	
			'Vaccinium myrtillus 4.jpg' => 'foto, in bloei',
	
			'Vaccinium myrtillus 5.jpg' => 'foto, in vrucht'
	
		),
	
		'Vaccinium uliginosum' => array(
	
			'Vaccinium uliginosum.jpg' => 'overzicht',
	
			'Vaccinium uliginosum 2.jpg' => 'vergroot; a = vruchten',
	
			'Vaccinium uliginosum 3.jpg' => 'foto, in bloei',
	
			'Vaccinium uliginosum 4.jpg' => 'foto, in bloei'
	
		),
	
		'Pyrola minor' => array(
	
			'Pyrola minor.jpg' => 'overzicht',
	
			'Pyrola minor 2.jpg' => 'vergroot',
	
			'Pyrola minor 3.jpg' => 'foto'
	
		),
	
		'Pyrola rotundifolia' => array(
	
			'Pyrola rotundifolia.jpg' => 'overzicht',
	
			'Pyrola rotundifolia 2.jpg' => 'vergroot',
	
			'Pyrola rotundifolia 3.jpg' => 'foto',
	
			'Pyrola rotundifolia 4.jpg' => 'foto',
	
			'Pyrola rotundifolia 5.jpg' => 'foto'
	
		),
	
		'Orthilia secunda' => array(
	
			'Orthilia secunda.jpg' => 'overzicht',
	
			'Orthilia secunda 2.jpg' => 'vergroot',
	
			'Orthilia secunda 3.jpg' => 'foto'
	
		),
	
		'Moneses uniflora' => array(
	
			'Moneses uniflora.jpg' => 'overzicht',
	
			'Moneses uniflora 2.jpg' => 'vergroot',
	
			'Moneses uniflora 3.jpg' => 'foto',
	
			'Moneses uniflora 4.jpg' => 'foto'
	
		),
	
		'Monotropa hypopitys' => array(
	
			'Monotropa hypopitys.jpg' => 'overzicht',
	
			'Monotropa hypopitys 2.jpg' => 'vergroot; a = bloem, b = bloem , deels geopend, c = dsn. stamper, d = vrucht',
	
			'Monotropa hypopitys 3.jpg' => 'foto',
	
			'Monotropa hypopitys 4.jpg' => 'foto'
	
		),
	
		'Lithospermum officinale' => array(
	
			'Lithospermum officinale.jpg' => 'overzicht',
	
			'Lithospermum officinale 2.jpg' => 'vergroot',
	
			'Lithospermum officinale 3.jpg' => 'foto',
	
			'Lithospermum officinale 4.jpg' => 'foto'
	
		),
	
		'Lithospermum arvense' => array(
	
			'Lithospermum arvense.jpg' => 'overzicht',
	
			'Lithospermum arvense 2.jpg' => 'vergroot',
	
			'Lithospermum arvense 3.jpg' => 'foto'
	
		),
	
		'Echium vulgare' => array(
	
			'Echium vulgare.jpg' => 'overzicht',
	
			'Echium vulgare 2.jpg' => 'vergroot; a = bloem van boven, b = dsn. bloem, c = nootjes',
	
			'Echium vulgare 3.jpg' => 'foto',
	
			'Echium vulgare 4.jpg' => 'foto'
	
		),
	
		'Pulmonaria montana' => array(
	
			'Pulmonaria montana.jpg' => 'overzicht',
	
			'Pulmonaria montana 2.jpg' => 'vergroot',
	
			'Pulmonaria montana 3.jpg' => 'foto'
	
		),
	
		'Pulmonaria officinalis' => array(
	
			'Pulmonaria officinalis.jpg' => 'overzicht foto',
	
			'Pulmonaria officinalis 3.jpg' => 'foto, in bloei',
	
			'Pulmonaria officinalis 4.jpg' => 'foto, in bloei'
	
		),
	
		'Pulmonaria obscura' => array(
	
			'Pulmonaria obscura.jpg' => 'overzicht, habitus',
	
			'Pulmonaria obscura 2.jpg' => 'foto, habitus'
	
		),
	
		'Symphytum officinale' => array(
	
			'Symphytum officinale.jpg' => 'overzicht',
	
			'Symphytum officinale 2.jpg' => 'vergroot; a = dsn. bloem, b = vruchtkelk, c = dsn. zaad',
	
			'Symphytum officinale 3.jpg' => 'foto, habitus bloeiend',
	
			'Symphytum officinale 4.jpg' => 'foto, bloeiwijze',
	
			'Symphytum officinale 5.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Symphytum asperum' => array(
	
			'Symphytum asperum.jpg' => 'overzicht',
	
			'Symphytum asperum 2.jpg' => 'foto, habitus bloeiend',
	
			'Symphytum uplandicum(x).jpg' => 'overzicht'
	
		),
	
		'Symphytum uplandicum(x)' => array(
	
			'Symphytum uplandicum(x).jpg' => 'overzicht',
	
			'Symphytum uplandicum(x) 2.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Brunnera macrophylla' => array(
	
			'Brunnera macrophylla.jpg' => 'overzicht',
	
			'Brunnera macrophylla 2.jpg' => 'blad en bloeiwijze',
	
			'Brunnera macrophylla 3.jpg' => 'foto, habitus, bloeiend'
	
		),
	
		'Anchusa arvensis' => array(
	
			'Anchusa arvensis.jpg' => 'overzicht',
	
			'Anchusa arvensis 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem, c = vruchtkelk',
	
			'Anchusa arvensis 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Anchusa officinalis' => array(
	
			'Anchusa officinalis.jpg' => 'overzicht',
	
			'Anchusa officinalis 2.jpg' => 'vergroot',
	
			'Anchusa officinalis 3.jpg' => 'foto, bloeiend',
	
			'Anchusa officinalis 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Anchusa ochroleuca' => array(
	
			'Anchusa ochroleuca.jpg' => 'overzicht foto',
	
			'Anchusa ochroleuca 3.jpg' => 'foto, habitus bloeiend',
	
			'Anchusa ochroleuca 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Nonea lutea' => array(
	
			'Nonea lutea.jpg' => 'overzicht',
	
			'Nonea lutea 2.jpg' => 'vergroot'
	
		),
	
		'Pentaglottis sempervirens' => array(
	
			'Pentaglottis sempervirens.jpg' => 'overzicht foto',
	
			'Pentaglottis sempervirens 3.jpg' => 'foto'
	
		),
	
		'Borago officinalis' => array(
	
			'Borago officinalis.jpg' => 'overzicht',
	
			'Borago officinalis 2.jpg' => 'vergroot',
	
			'Borago officinalis 3.jpg' => 'foto, bloemen',
	
			'Borago officinalis 4.jpg' => 'foto, habitus',
	
			'Borago officinalis 5.jpg' => 'foto, bloeiwijze',
	
			'Borago officinalis 6.jpg' => 'foto, habitus'
	
		),
	
		'Amsinckia micrantha' => array(
	
			'Amsinckia micrantha.jpg' => 'overzicht foto',
	
			'Amsinckia micrantha 3.jpg' => 'foto, habitus bloeiend',
	
			'Amsinckia micrantha 4.jpg' => 'foto, bloeiwijze met bloemen en vruchten'
	
		),
	
		'Asperugo procumbens' => array(
	
			'Asperugo procumbens.jpg' => 'overzicht',
	
			'Asperugo procumbens 2.jpg' => 'vergroot',
	
			'Asperugo procumbens 3.jpg' => 'foto',
	
			'Asperugo procumbens 4.jpg' => 'foto',
	
			'Asperugo procumbens 5.jpg' => ' bloeiend, kelk na de bloei'
	
		),
	
		'Myosotis laxa subsp. cespitosa' => array(
	
			'Myosotis laxa cespitosa.jpg' => 'overzicht; a = vruchtkelk',
	
			'Myosotis laxa cespitosa 2.jpg' => 'overzicht',
	
			'Myosotis laxa cespitosa 3.jpg' => 'foto'
	
		),
	
		'Myosotis scorpioides' => array(
	
			'Myosotis scorpioides.jpg' => 'overzicht; a = vruchtkelk',
	
			'Myosotis scorpioides 2.jpg' => 'vergroot',
	
			'Myosotis scorpioides 3.jpg' => 'foto'
	
		),
	
		'Myosotis scorpioides subsp. nemorosa' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Myosotis scorpioides subsp. scorpioides' => array(
	
			'Myosotis scorpioides.jpg' => 'overzicht; a = vruchtkelk',
	
			'Myosotis scorpioides 3.jpg' => 'foto'
	
		),
	
		'Myosotis discolor' => array(
	
			'Myosotis discolor.jpg' => 'overzicht',
	
			'Myosotis discolor 2.jpg' => 'vergroot',
	
			'Myosotis discolor 3.jpg' => 'foto'
	
		),
	
		'Myosotis stricta' => array(
	
			'Myosotis stricta.jpg' => 'overzicht; a = vruchtkelk',
	
			'Myosotis stricta 2.jpg' => 'vergroot',
	
			'Myosotis stricta 3.jpg' => 'foto'
	
		),
	
		'Myosotis ramosissima' => array(
	
			'Myosotis ramosissima.jpg' => 'overzicht; a = vruchtkelk',
	
			'Myosotis ramosissima 2.jpg' => 'vergroot',
	
			'Myosotis ramosissima 3.jpg' => 'foto'
	
		),
	
		'Myosotis arvensis' => array(
	
			'Myosotis arvensis.jpg' => 'overzicht',
	
			'Myosotis arvensis 2.jpg' => 'vergroot',
	
			'Myosotis arvensis 3.jpg' => 'foto'
	
		),
	
		'Myosotis sylvatica' => array(
	
			'Myosotis sylvatica.jpg' => 'overzicht',
	
			'Myosotis sylvatica 2.jpg' => 'vergroot',
	
			'Myosotis sylvatica 3.jpg' => 'foto',
	
			'Myosotis sylvatica 4.jpg' => 'foto'
	
		),
	
		'Lappula squarrosa' => array(
	
			'Lappula squarrosa.jpg' => 'overzicht; a = stamper, b = vruchtkelk met 1 deelvruchtje',
	
			'Lappula squarrosa 2.jpg' => 'vergroot',
	
			'Lappula squarrosa 3.jpg' => 'foto'
	
		),
	
		'Omphalodes verna' => array(
	
			'Omphalodes verna.jpg' => 'overzicht',
	
			'Omphalodes verna 2.jpg' => 'vergroot'
	
		),
	
		'Cynoglossum officinale' => array(
	
			'Cynoglossum officinale.jpg' => 'overzicht',
	
			'Cynoglossum officinale 2.jpg' => 'vergroot; a = deel bloemkroon van binnen, b = deelvruchtje',
	
			'Cynoglossum officinale 3.jpg' => 'foto',
	
			'Cynoglossum officinale 4.jpg' => 'foto',
	
			'Cynoglossum officinale 5.jpg' => 'foto',
	
			'Cynoglossum officinale 6.jpg' => 'foto, vruchten'
	
		),
	
		'Phacelia tanacetifolia' => array(
	
			'Phacelia tanacetifolia.jpg' => 'overzicht',
	
			'Phacelia tanacetifolia 2.jpg' => 'vergroot',
	
			'Phacelia tanacetifolia 3.jpg' => 'foto',
	
			'Phacelia tanacetifolia 4.jpg' => 'foto'
	
		),
	
		'Sherardia arvensis' => array(
	
			'Sherardia arvensis.jpg' => 'overzicht; a = bloem, b = dsn. bloem',
	
			'Sherardia arvensis 2.jpg' => 'vergroot',
	
			'Sherardia arvensis 3.jpg' => 'foto, bloeiend',
	
			'Sherardia arvensis 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Asperula arvensis' => array(
	
			'Asperula arvensis.jpg' => 'overzicht',
	
			'Asperula arvensis 2.jpg' => 'vergroot',
	
			'Asperula arvensis 3.jpg' => 'foto',
	
			'Asperula arvensis 4.jpg' => 'foto'
	
		),
	
		'Asperula cynanchica' => array(
	
			'Asperula cynanchica.jpg' => 'overzicht',
	
			'Asperula cynanchica 2.jpg' => 'vergroot',
	
			'Asperula cynanchica 3.jpg' => 'foto',
	
			'Asperula cynanchica 4.jpg' => 'foto'
	
		),
	
		'Galium odoratum' => array(
	
			'Galium odoratum.jpg' => 'overzicht',
	
			'Galium odoratum 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem, c = dsn. vrucht',
	
			'Galium odoratum 3.jpg' => 'foto',
	
			'Galium odoratum 4.jpg' => 'foto'
	
		),
	
		'Galium glaucum' => array(
	
			'Galium glaucum.jpg' => 'overzicht',
	
			'Galium glaucum 2.jpg' => 'vergroot',
	
			'Galium glaucum 3.jpg' => 'foto',
	
			'Galium glaucum 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Galium palustre' => array(
	
			'Galium palustre.jpg' => 'overzicht',
	
			'Galium palustre 2.jpg' => 'vergroot',
	
			'Galium palustre 3.jpg' => 'foto',
	
			'Galium palustre 4.jpg' => 'foto'
	
		),
	
		'Galium boreale' => array(
	
			'Galium boreale.jpg' => 'overzicht',
	
			'Galium boreale 2.jpg' => 'vergroot',
	
			'Galium boreale 3.jpg' => 'foto'
	
		),
	
		'Galium uliginosum' => array(
	
			'Galium uliginosum.jpg' => 'overzicht',
	
			'Galium uliginosum 2.jpg' => 'vergroot',
	
			'Galium uliginosum 3.jpg' => 'foto'
	
		),
	
		'Galium parisiense' => array(
	
			'Galium parisiense.jpg' => 'overzicht',
	
			'Galium parisiense 2.jpg' => 'foto',
	
			'Galium parisiense 3.jpg' => 'foto'
	
		),
	
		'Galium tricornutum' => array(
	
			'Galium tricornutum.jpg' => 'overzicht; a = bloeiwijze, b = vrucht',
	
			'Galium tricornutum 2.jpg' => 'vergroot',
	
			'Galium tricornutum 3.jpg' => 'foto'
	
		),
	
		'Galium aparine' => array(
	
			'Galium aparine.jpg' => 'overzicht',
	
			'Galium aparine 2.jpg' => 'vergroot; a = bloeiwijze, b = dsn. bloem',
	
			'Galium aparine 3.jpg' => 'foto, habitus',
	
			'Galium aparine 4.jpg' => 'foto, in vrucht',
	
			'Galium aparine 5.jpg' => 'foto, detail blad'
	
		),
	
		'Galium spurium' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Galium verum' => array(
	
			'Galium verum.jpg' => 'overzicht',
	
			'Galium verum 2.jpg' => 'vergroot',
	
			'Galium verum 3.jpg' => 'foto',
	
			'Galium verum 4.jpg' => 'foto'
	
		),
	
		'Galium sylvaticum' => array(
	
			'Galium sylvaticum.jpg' => 'overzicht',
	
			'Galium sylvaticum 2.jpg' => 'vergroot',
	
			'Galium sylvaticum 3.jpg' => 'foto',
	
			'Galium sylvaticum 4.jpg' => 'foto'
	
		),
	
		'Galium mollugo' => array(
	
			'Galium mollugo.jpg' => 'overzicht',
	
			'Galium mollugo 2.jpg' => 'vergroot',
	
			'Galium mollugo 3.jpg' => 'foto',
	
			'Galium mollugo 4.jpg' => 'foto'
	
		),
	
		'Galium pumilum' => array(
	
			'Galium pumilum.jpg' => 'overzicht',
	
			'Galium pumilum 2.jpg' => 'vergroot',
	
			'Galium pumilum 3.jpg' => 'foto'
	
		),
	
		'Galium saxatile' => array(
	
			'Galium saxatile.jpg' => 'overzicht',
	
			'Galium saxatile 2.jpg' => 'vergroot'
	
		),
	
		'Cruciata laevipes' => array(
	
			'Cruciata laevipes.jpg' => 'overzicht',
	
			'Cruciata laevipes 2.jpg' => 'vergroot; a = bloeiwijze, b = tweeslachtige en manlijke bloem',
	
			'Cruciata laevipes 3.jpg' => 'foto',
	
			'Cruciata laevipes 4.jpg' => 'foto'
	
		),
	
		'Rubia tinctorum' => array(
	
			'Rubia tinctorum.jpg' => 'overzicht',
	
			'Rubia tinctorum 2.jpg' => 'vergroot',
	
			'Rubia tinctorum 3.jpg' => 'foto, habitus in bloei',
	
			'Rubia tinctorum 4.jpg' => 'foto, habitus in bloei'
	
		),
	
		'Cicendia filiformis' => array(
	
			'Cicendia filiformis.jpg' => 'overzicht',
	
			'Cicendia filiformis 2.jpg' => 'vergroot',
	
			'Cicendia filiformis 3.jpg' => 'foto',
	
			'Cicendia filiformis 4.jpg' => 'foto'
	
		),
	
		'Blackstonia perfoliata' => array(
	
			'Blackstonia perfoliata.jpg' => 'overzicht',
	
			'Blackstonia perfoliata 2.jpg' => 'foto, bloemen'
	
		),
	
		'Blackstonia perfoliata subsp. serotina' => array(
	
			'Blackstonia perfoliata ser.jpg' => 'overzicht',
	
			'Blackstonia perfoliata ser 2.jpg' => 'vergroot',
	
			'Blackstonia perfoliata ser 3.jpg' => 'foto',
	
			'Blackstonia perfoliata ser 4.jpg' => 'foto',
	
			'Blackstonia perfoliata ser 5.jpg' => 'foto'
	
		),
	
		'Blackstonia perfoliata subsp. perfoliata' => array(
	
			'Blackstonia perfoliata per.jpg' => 'overzicht',
	
			'Blackstonia perfoliata per 2.jpg' => 'vergroot',
	
			'Blackstonia perfoliata per 3.jpg' => 'foto'
	
		),
	
		'Centaurium pulchellum' => array(
	
			'Centaurium pulchellum.jpg' => 'overzicht',
	
			'Centaurium pulchellum 2.jpg' => 'vergroot; a = bloem, b = stamper',
	
			'Centaurium pulchellum 3.jpg' => 'bloem in bloei en in vrucht',
	
			'Centaurium pulchellum 4.jpg' => 'foto'
	
		),
	
		'Centaurium erythraea' => array(
	
			'Centaurium erythraea.jpg' => 'overzicht',
	
			'Centaurium erythraea 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem,  c = helmknop voor en na het stuiven',
	
			'Centaurium erythraea 3.jpg' => 'bloem in bloei en in vrucht',
	
			'Centaurium erythraea 4.jpg' => 'foto',
	
			'Centaurium intermedium(x) 2.jpg' => '(= C. erythraea x littorale) , bloeiend',
	
			'Centaurium intermedium(x) 3.jpg' => '(= C. erythraea x littorale) , bloeiend'
	
		),
	
		'Centaurium littorale' => array(
	
			'Centaurium littorale.jpg' => 'overzicht',
	
			'Centaurium littorale 2.jpg' => 'vergroot',
	
			'Centaurium littorale 3.jpg' => 'bloem in bloei en in vrucht',
	
			'Centaurium intermedium(x) 2.jpg' => '(= C. erythraea x littorale) , bloeiend',
	
			'Centaurium intermedium(x) 3.jpg' => '(= C. erythraea x littorale) , bloeiend',
	
			'Centaurium littorale 4.jpg' => 'foto'
	
		),
	
		'Gentiana pneumonanthe' => array(
	
			'Gentiana pneumonanthe.jpg' => 'overzicht',
	
			'Gentiana pneumonanthe 2.jpg' => 'vergroot',
	
			'Gentiana pneumonanthe 3.jpg' => 'foto',
	
			'Gentiana pneumonanthe 4.jpg' => 'foto'
	
		),
	
		'Gentiana cruciata' => array(
	
			'Gentiana cruciata.jpg' => 'overzicht',
	
			'Gentiana cruciata 2.jpg' => 'vergroot',
	
			'Gentiana cruciata 3.jpg' => 'foto',
	
			'Gentiana cruciata 4.jpg' => 'foto'
	
		),
	
		'Gentianella campestris' => array(
	
			'Gentianella campestris.jpg' => 'overzicht',
	
			'Gentianella campestris 2.jpg' => 'vergroot',
	
			'Gentianella campestris 3.jpg' => 'foto',
	
			'Gentianella campestris 4.jpg' => 'foto'
	
		),
	
		'Gentianella amarella' => array(
	
			'Gentianella amarella.jpg' => 'overzicht',
	
			'Gentianella amarella 2.jpg' => 'vergroot',
	
			'Gentianella amarella 3.jpg' => 'foto',
	
			'Gentianella amarella 4.jpg' => 'foto',
	
			'Gentianella amarella 5.jpg' => 'foto'
	
		),
	
		'Gentianella germanica' => array(
	
			'Gentianella germanica.jpg' => 'overzicht',
	
			'Gentianella germanica 2.jpg' => 'vergroot; a = opengesneden bloem',
	
			'Gentianella germanica 3.jpg' => 'foto'
	
		),
	
		'Gentianopsis ciliata' => array(
	
			'Gentianopsis ciliata.jpg' => 'overzicht',
	
			'Gentianopsis ciliata 2.jpg' => 'vergroot',
	
			'Gentianopsis ciliata 3.jpg' => 'foto',
	
			'Gentianopsis ciliata 4.jpg' => 'foto'
	
		),
	
		'Vinca minor' => array(
	
			'Vinca minor.jpg' => 'overzicht',
	
			'Vinca minor 2.jpg' => 'vergroot; a = dsn. bloem, b = meeldraad',
	
			'Vinca minor 3.jpg' => 'foto, bloeiend',
	
			'Vinca minor 4.jpg' => 'foto, bloeiend',
	
			'Vinca minor 5.jpg' => 'foto, bloeiend'
	
		),
	
		'Vincetoxicum hirundinaria' => array(
	
			'Vincetoxicum hirundinaria.jpg' => 'overzicht; a = dsn. bloem, b = vrucht',
	
			'Vincetoxicum hirundinaria 2.jpg' => 'vergroot',
	
			'Vincetoxicum hirundinaria 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Vincetoxicum nigrum' => array(
	
			'Vincetoxicum nigrum.jpg' => 'overzicht foto',
	
			'Vincetoxicum nigrum 3.jpg' => 'foto, bloeiend',
	
			'Vincetoxicum nigrum 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Asclepias syriaca' => array(
	
			'Asclepias syriaca.jpg' => 'overzicht',
	
			'Asclepias syriaca 2.jpg' => 'vergroot'
	
		),
	
		'Nicandra physalodes' => array(
	
			'Nicandra physalodes.jpg' => 'overzicht; a = dsn. bloem',
	
			'Nicandra physalodes 2.jpg' => 'vergroot',
	
			'Nicandra physalodes 3.jpg' => 'foto'
	
		),
	
		'Lycium barbarum' => array(
	
			'Lycium barbarum.jpg' => 'overzicht',
	
			'Lycium barbarum 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem, c = dsn. vrucht',
	
			'Lycium barbarum 3.jpg' => 'foto',
	
			'Lycium barbarum 4.jpg' => 'foto'
	
		),
	
		'Atropa bella-donna' => array(
	
			'Atropa bella-donna.jpg' => 'overzicht',
	
			'Atropa bella-donna 2.jpg' => 'vergroot; a = dsn. bloem, b = vruchtkelk, c = dsn. vrucht',
	
			'Atropa bella-donna 3.jpg' => 'foto',
	
			'Atropa bella-donna 4.jpg' => 'foto'
	
		),
	
		'Hyoscyamus niger' => array(
	
			'Hyoscyamus niger.jpg' => 'overzicht',
	
			'Hyoscyamus niger 2.jpg' => 'vergroot; a = opgesneden bloem, b = vruchtkelk, c = doosvrucht',
	
			'Hyoscyamus niger 3.jpg' => 'foto',
	
			'Hyoscyamus niger 4.jpg' => 'foto',
	
			'Hyoscyamus niger 5.jpg' => 'foto'
	
		),
	
		'Physalis alkekengi' => array(
	
			'Physalis alkekengi.jpg' => 'overzicht',
	
			'Physalis alkekengi 2.jpg' => 'vergroot',
	
			'Physalis alkekengi 3.jpg' => 'foto'
	
		),
	
		'Physalis peruviana' => array(
	
			'Physalis peruviana.jpg' => 'overzicht foto',
	
			'Physalis peruviana 3.jpg' => 'foto',
	
			'Physalis peruviana 4.jpg' => 'foto',
	
			'Physalis peruviana 5.jpg' => 'foto',
	
			'Physalis peruviana 6.jpg' => 'foto'
	
		),
	
		'Solanum dulcamara' => array(
	
			'Solanum dulcamara.jpg' => 'overzicht; a = bloem, b = dsn. deel bloem',
	
			'Solanum dulcamara 2.jpg' => 'vergroot',
	
			'Solanum dulcamara 3.jpg' => 'foto, bloeiend',
	
			'Solanum dulcamara 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Solanum tuberosum' => array(
	
			'Solanum tuberosum.jpg' => 'overzicht',
	
			'Solanum tuberosum 2.jpg' => 'vergroot; a = dsn. bloem, b = vrucht',
	
			'Solanum tuberosum 3.jpg' => 'foto, in bloei',
	
			'Solanum tuberosum 4.jpg' => 'foto, in bloei',
	
			'Solanum tuberosum 5.jpg' => 'foto, in bloei',
	
			'Solanum tuberosum 6.jpg' => 'foto, in bloei'
	
		),
	
		'Solanum lycopersicum' => array(
	
			'Solanum lycopersicum.jpg' => 'overzicht foto',
	
			'Solanum lycopersicum 3.jpg' => 'foto, bloeiend',
	
			'Solanum lycopersicum 4.jpg' => 'foto, bloeiend met jonge vrucht',
	
			'Solanum lycopersicum 5.jpg' => 'foto, in vrucht',
	
			'Solanum lycopersicum 6.jpg' => 'foto, in vrucht'
	
		),
	
		'Solanum triflorum' => array(
	
			'Solanum triflorum.jpg' => 'overzicht',
	
			'Solanum triflorum 2.jpg' => 'vergroot',
	
			'Solanum triflorum 3.jpg' => 'foto, habitus in bloei en vrucht (jong)'
	
		),
	
		'Solanum nigrum' => array(
	
			'Solanum nigrum.jpg' => 'overzicht stengelbeharing',
	
			'Solanum nigrum 2.jpg' => 'detail stengelbeharing; a = subsp. nigrum, b = subsp. schultesii',
	
			'Solanum nigrum 3.jpg' => 'foto, in bloei',
	
			'Solanum nigrum 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Solanum nigrum subsp. nigrum' => array(
	
			'Solanum nigrum nigrum.jpg' => 'overzicht',
	
			'Solanum nigrum nigrum 2.jpg' => 'vergroot; a = dsn. bloem, b = dsn. vrucht'
	
		),
	
		'Solanum nigrum subsp. schultesii' => array(
	
			'Solanum nigrum schultesii.jpg' => 'overzicht',
	
			'Solanum nigrum schultesii 3.jpg' => 'foto, habitus in bloei'
	
		),
	
		'Solanum physalifolium' => array(
	
			'Solanum physalifolium.jpg' => 'overzicht foto',
	
			'Solanum physalifolium 3.jpg' => 'foto, habitus in bloei'
	
		),
	
		'Solanum sarachoides' => array(
	
			'Solanum sarachoides.jpg' => 'overzicht',
	
			'Solanum sarachoides 2.jpg' => 'vergroot'
	
		),
	
		'Datura stramonium' => array(
	
			'Datura stramonium.jpg' => 'overzicht',
	
			'Datura stramonium 2.jpg' => 'vergroot; a = dsn. midden vruchtbeginsel',
	
			'Datura stramonium 3.jpg' => 'foto',
	
			'Datura stramonium 4.jpg' => 'foto',
	
			'Datura stramonium 5.jpg' => 'foto'
	
		),
	
		'Nicotiana tabacum' => array(
	
			'Nicotiana tabacum.jpg' => 'overzicht',
	
			'Nicotiana tabacum 2.jpg' => 'vergroot; a = dsn. bloem',
	
			'Nicotiana tabacum 3.jpg' => 'foto'
	
		),
	
		'Convolvulus arvensis' => array(
	
			'Convolvulus arvensis.jpg' => 'overzicht',
	
			'Convolvulus arvensis 2.jpg' => 'vergroot; a = dsn. bloem, b = vruchtkelk',
	
			'Convolvulus arvensis 3.jpg' => 'foto',
	
			'Convolvulus arvensis 4.jpg' => 'foto'
	
		),
	
		'Convolvulus soldanella' => array(
	
			'Convolvulus soldanella.jpg' => 'overzicht',
	
			'Convolvulus soldanella 2.jpg' => 'vergroot',
	
			'Convolvulus soldanella 3.jpg' => 'foto',
	
			'Convolvulus soldanella 4.jpg' => 'foto'
	
		),
	
		'Convolvulus sepium' => array(
	
			'Convolvulus sepium.jpg' => 'overzicht',
	
			'Convolvulus sepium 2.jpg' => 'vergroot; a = dsn. bloem, b = stamper',
	
			'Convolvulus sepium 3.jpg' => 'foto'
	
		),
	
		'Convolvulus silvatica' => array(
	
			'Convolvulus silvatica.jpg' => 'overzicht bloem',
	
			'Convolvulus silvatica 2.jpg' => 'vergroot',
	
			'Convolvulus silvatica 3.jpg' => 'foto, habitus',
	
			'Convolvulus silvatica 4.jpg' => 'foto, bloemknop',
	
			'Convolvulus silvatica 5.jpg' => 'foto, bloem'
	
		),
	
		'Cuscuta lupuliformis' => array(
	
			'Cuscuta lupuliformis.jpg' => 'overzicht',
	
			'Cuscuta lupuliformis 2.jpg' => 'vergroot; a = bloem, b = geopende bloem, c = geopende kelk',
	
			'Cuscuta lupuliformis 3.jpg' => 'foto'
	
		),
	
		'Cuscuta campestris' => array(
	
			'Cuscuta campestris.jpg' => 'overzicht; a = bloem, b = geopende bloem, c = geopende kelk',
	
			'Cuscuta campestris 2.jpg' => 'vergroot',
	
			'Cuscuta campestris 3.jpg' => 'foto'
	
		),
	
		'Cuscuta gronovii' => array(
	
			'Cuscuta gronovii.jpg' => 'overzicht; a = bloem, b = geopende bloem, c = geopende kelk',
	
			'Cuscuta gronovii 2.jpg' => 'vergroot'
	
		),
	
		'Cuscuta epithymum' => array(
	
			'Cuscuta epithymum.jpg' => 'overzicht',
	
			'Cuscuta epithymum 2.jpg' => 'vergroot; a = bloem, b = geopende bloem, c = geopende kelk',
	
			'Cuscuta epithymum 3.jpg' => 'foto'
	
		),
	
		'Cuscuta europaea' => array(
	
			'Cuscuta europaea.jpg' => 'overzicht',
	
			'Cuscuta europaea 2.jpg' => 'vergroot; a = bloem, b = geopende bloem, c = geopende kelk',
	
			'Cuscuta europaea 3.jpg' => 'foto',
	
			'Cuscuta europaea 4.jpg' => 'foto'
	
		),
	
		'Cuscuta epilinum' => array(
	
			'Cuscuta epilinum.jpg' => 'overzicht',
	
			'Cuscuta epilinum 2.jpg' => 'vergroot; a = bloem, b = geopende bloem, c = geopende kelk'
	
		),
	
		'Fraxinus excelsior' => array(
	
			'Fraxinus excelsior.jpg' => 'overzicht',
	
			'Fraxinus excelsior 2.jpg' => 'vergroot; a = tweeslachtige bloem, b = dsn. vrouwelijke bloem',
	
			'Fraxinus excelsior 3.jpg' => 'foto',
	
			'Fraxinus excelsior 4.jpg' => 'foto',
	
			'Fraxinus excelsior 5.jpg' => 'foto, boom',
	
			'Fraxinus excelsior 6.jpg' => 'foto, stam'
	
		),
	
		'Syringa vulgaris' => array(
	
			'Syringa vulgaris.jpg' => 'overzicht',
	
			'Syringa vulgaris 2.jpg' => 'vergroot; a = dsn. bloem, b = vruchtwijze',
	
			'Syringa vulgaris 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Forsythia viridissima' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Forsythia suspensa' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Forsythia intermedia(x)' => array(
	
			'Forsythia intermedia(x).jpg' => 'overzicht',
	
			'Forsythia intermedia(x) 2.jpg' => 'foto, bloeiend',
	
			'Forsythia intermedia(x) 3.jpg' => 'foto, bloeiend',
	
			'Forsythia intermedia(x) 4.jpg' => 'foto, bloemen'
	
		),
	
		'Ligustrum vulgare' => array(
	
			'Ligustrum vulgare.jpg' => 'overzicht',
	
			'Ligustrum vulgare 2.jpg' => 'vergroot; a = geopende bloem',
	
			'Ligustrum vulgare 3.jpg' => 'foto, in bloei',
	
			'Ligustrum vulgare 4.jpg' => 'foto, in vrucht',
	
			'Ligustrum vulgare 5.jpg' => 'foto, in bloei',
	
			'Ligustrum vulgare 6.jpg' => 'foto, in vrucht'
	
		),
	
		'Ligustrum ovalifolium' => array(
	
			'Ligustrum ovalifolium.jpg' => 'overzicht foto',
	
			'Ligustrum ovalifolium 3.jpg' => 'foto',
	
			'Ligustrum ovalifolium 4.jpg' => 'foto'
	
		),
	
		'Jasminum nudiflorum' => array(
	
			'Jasminum nudiflorum.jpg' => 'overzicht foto',
	
			'Jasminum nudiflorum 3.jpg' => 'foto',
	
			'Jasminum nudiflorum 4.jpg' => 'foto'
	
		),
	
		'Scrophularia vernalis' => array(
	
			'Scrophularia vernalis.jpg' => 'overzicht',
	
			'Scrophularia vernalis 2.jpg' => 'vergroot; a = bloem',
	
			'Scrophularia vernalis 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Scrophularia nodosa' => array(
	
			'Scrophularia nodosa.jpg' => 'overzicht',
	
			'Scrophularia nodosa 2.jpg' => 'vergroot; a = bloem op rugzijde geopend, b = staminodium, c = dsn. bloem',
	
			'Scrophularia nodosa 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Scrophularia auriculata' => array(
	
			'Scrophularia auriculata.jpg' => 'overzicht',
	
			'Scrophularia auriculata 2.jpg' => 'vergroot; a = staminodium',
	
			'Scrophularia auriculata 3.jpg' => 'foto, habitus bloeiend',
	
			'Scrophularia canina 3.jpg' => 'foto, bloeiwijzen',
	
			'Scrophularia canina 4.jpg' => 'foto, detail van bloeiwijze'
	
		),
	
		'Scrophularia umbrosa' => array(
	
			'Scrophularia umbrosa.jpg' => 'overzicht',
	
			'Scrophularia umbrosa 2.jpg' => 'vergroot',
	
			'Scrophularia umbrosa 2a.jpg' => 'detail staminodi‘n; a = subsp. umbrosa, b = subsp. neesii',
	
			'Scrophularia umbrosa 3.jpg' => 'foto, bloeiend',
	
			'Scrophularia umbrosa 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Verbascum nigrum' => array(
	
			'Verbascum nigrum.jpg' => 'overzicht',
	
			'Verbascum nigrum 2.jpg' => 'vergroot',
	
			'Verbascum nigrum 3.jpg' => 'foto, habtius bloeiend',
	
			'Verbascum nigrum 4.jpg' => 'foto, detail bloeiwijze'
	
		),
	
		'Verbascum lychnitis' => array(
	
			'Verbascum lychnitis.jpg' => 'overzicht',
	
			'Verbascum lychnitis 2.jpg' => 'vergroot',
	
			'Verbascum lychnitis 3.jpg' => 'foto, habitus bloeiend',
	
			'Verbascum lychnitis 4.jpg' => 'foto, bloeiwijze, wit bloeiend',
	
			'Verbascum lychnitis 5.jpg' => 'foto, habitus, geelbloeiend',
	
			'Verbascum lychnitis 6.jpg' => 'foto, bloeiwijze, geelbloeiend'
	
		),
	
		'Verbascum pulverulentum' => array(
	
			'Verbascum pulverulentum.jpg' => 'overzicht',
	
			'Verbascum pulverulentum 2.jpg' => 'foto, habtius bloeiend',
	
			'Verbascum pulverulentum 3.jpg' => 'foto, detail bloeiwijze',
	
			'Verbascum pulverulentum 4.jpg' => 'foto, habtius bloeiend',
	
			'Verbascum pulverulentum 5.jpg' => 'foto, habtius bloeiend'
	
		),
	
		'Verbascum blattaria' => array(
	
			'Verbascum blattaria.jpg' => 'overzicht',
	
			'Verbascum blattaria 2.jpg' => 'vergroot',
	
			'Verbascum blattaria 3.jpg' => 'foto, bloeiwijze, geelbloeiend',
	
			'Verbascum blattaria 4.jpg' => 'foto, bloeiwijze, geelbloeiend',
	
			'Verbascum blattaria 5.jpg' => 'foto, deel bloeiwijze, witbloeiend',
	
			'Verbascum blattaria 6.jpg' => 'foto, bloemen witbloeiend, let op vorm van meeldraden'
	
		),
	
		'Verbascum phlomoides' => array(
	
			'Verbascum phlomoides.jpg' => 'overzicht; a = meeldraden',
	
			'Verbascum phlomoides 2.jpg' => 'vergroot',
	
			'Verbascum phlomoides 3.jpg' => 'foto, habtius bloeiend',
	
			'Verbascum phlomoides 4.jpg' => 'foto, bebladerde stengel',
	
			'Verbascum phlomoides 5.jpg' => 'foto, bloeiwijze',
	
			'Verbascum phlomoides 6.jpg' => 'foto, detail bloeiwijze'
	
		),
	
		'Verbascum thapsus' => array(
	
			'Verbascum thapsus.jpg' => 'overzicht; a = meeldraden',
	
			'Verbascum thapsus 2.jpg' => 'vergroot',
	
			'Verbascum thapsus 3.jpg' => 'foto, bloeiwijze',
	
			'Verbascum thapsus 4.jpg' => 'foto, detail bloeiwijze',
	
			'Verbascum thapsus 5.jpg' => 'foto, bebladerde stengel',
	
			'Verbascum thapsus 6.jpg' => 'foto, top bloeiwijze'
	
		),
	
		'Verbascum densiflorum' => array(
	
			'Verbascum densiflorum.jpg' => 'overzicht; a = meeldraden, b = stijl',
	
			'Verbascum densiflorum 2.jpg' => 'vergroot',
	
			'Verbascum densiflorum 3.jpg' => 'foto, habitus bloeiend',
	
			'Verbascum densiflorum 4.jpg' => 'foto, bebladerde stengel',
	
			'Verbascum densiflorum 5.jpg' => 'foto, bloeiwijze',
	
			'Verbascum densiflorum 6.jpg' => 'foto, detail bloeiwijze'
	
		),
	
		'Nemesia melissaefolia' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Sutera cordata' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Buddleja davidii' => array(
	
			'Buddleja davidii.jpg' => 'overzicht',
	
			'Buddleja davidii 2.jpg' => 'vergroot',
	
			'Buddleja davidii 3.jpg' => 'foto'
	
		),
	
		'Verbena officinalis' => array(
	
			'Verbena officinalis.jpg' => 'overzicht',
	
			'Verbena officinalis 2.jpg' => 'vergroot; a = dsn. bloem, b = stamper',
	
			'Verbena officinalis 3.jpg' => 'foto, bloeiend',
	
			'Verbena officinalis 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Verbena bonariensis' => array(
	
			'Verbena bonariensis.jpg' => 'overzicht',
	
			'Verbena bonariensis 2.jpg' => 'foto, bloeiend'
	
		),
	
		'Catalpa bignonioides' => array(
	
			'Catalpa bignonioides.jpg' => 'overzicht',
	
			'Catalpa bignonioides 2.jpg' => 'vergroot, bloeiend',
	
			'Catalpa bignonioides 3.jpg' => 'foto, bladen en vruchten',
	
			'Catalpa bignonioides 4.jpg' => 'foto, bladen en vruchten'
	
		),
	
		'Ajuga chamaepitys' => array(
	
			'Ajuga chamaepitys.jpg' => 'overzicht',
	
			'Ajuga chamaepitys 2.jpg' => 'vergroot',
	
			'Ajuga chamaepitys 3.jpg' => 'foto, habitus bloeiend',
	
			'Ajuga chamaepitys 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Ajuga reptans' => array(
	
			'Ajuga reptans.jpg' => 'overzicht',
	
			'Ajuga reptans 2.jpg' => 'vergroot; a = bloem van boven',
	
			'Ajuga reptans 3.jpg' => 'foto, habitus bloeiend',
	
			'Ajuga genevensis 3.jpg' => 'Ajuga genevensis (zie opmerking), bloeiwijze',
	
			'Ajuga genevensis 4.jpg' => 'Ajuga genevensis (zie opmerking), bloeiwijze',
	
			'Ajuga reptans 4.jpg' => 'foto, bloewijzen'
	
		),
	
		'Ajuga pyramidalis' => array(
	
			'Ajuga pyramidalis.jpg' => 'overzicht',
	
			'Ajuga pyramidalis 2.jpg' => 'vergroot',
	
			'Ajuga pyramidalis 3.jpg' => 'foto, habitus bloeiend',
	
			'Ajuga pyramidalis 4.jpg' => 'foto, habitus bloeiend',
	
			'Ajuga pyramidalis 5.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Teucrium botrys' => array(
	
			'Teucrium botrys.jpg' => 'overzicht',
	
			'Teucrium botrys 2.jpg' => 'vergroot',
	
			'Teucrium botrys 3.jpg' => 'foto, habitus',
	
			'Teucrium botrys 4.jpg' => 'foto, habitus'
	
		),
	
		'Teucrium montanum' => array(
	
			'Teucrium montanum.jpg' => 'overzicht',
	
			'Teucrium montanum 2.jpg' => 'vergroot',
	
			'Teucrium montanum 3.jpg' => 'foto, bloeiwijze',
	
			'Teucrium montanum 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Teucrium scorodonia' => array(
	
			'Teucrium scorodonia.jpg' => 'overzicht',
	
			'Teucrium scorodonia 2.jpg' => 'vergroot',
	
			'Teucrium scorodonia 3.jpg' => 'foto, habitus',
	
			'Teucrium scorodonia 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Teucrium scordium' => array(
	
			'Teucrium scordium.jpg' => 'overzicht',
	
			'Teucrium scordium 2.jpg' => 'vergroot',
	
			'Teucrium scordium 3.jpg' => 'foto, bloeiwijze',
	
			'Teucrium scordium 4.jpg' => 'foto, bloeiwijze',
	
			'Teucrium scordium 5.jpg' => 'foto, habitus',
	
			'Teucrium scordium 6.jpg' => 'foto, habitus'
	
		),
	
		'Teucrium chamaedrys' => array(
	
			'Teucrium chamaedrys.jpg' => 'overzicht',
	
			'Teucrium chamaedrys 2.jpg' => 'vergroot',
	
			'Teucrium chamaedrys 3.jpg' => 'foto, bloeiwijze',
	
			'Teucrium chamaedrys 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Scutellaria columnae' => array(
	
			'Scutellaria columnae.jpg' => 'overzicht',
	
			'Scutellaria columnae 2.jpg' => 'vergroot'
	
		),
	
		'Scutellaria galericulata' => array(
	
			'Scutellaria galericulata.jpg' => 'overzicht',
	
			'Scutellaria galericulata 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem',
	
			'Scutellaria galericulata 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Scutellaria minor' => array(
	
			'Scutellaria minor.jpg' => 'overzicht',
	
			'Scutellaria minor 2.jpg' => 'vergroot',
	
			'Scutellaria minor 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Scutellaria hybrida(x)' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Marrubium vulgare' => array(
	
			'Marrubium vulgare.jpg' => 'overzicht; a = kelk',
	
			'Marrubium vulgare 2.jpg' => 'vergroot',
	
			'Marrubium vulgare 3.jpg' => 'foto'
	
		),
	
		'Galeopsis segetum' => array(
	
			'Galeopsis segetum.jpg' => 'overzicht',
	
			'Galeopsis segetum 2.jpg' => 'vergroot',
	
			'Galeopsis segetum 3.jpg' => 'foto',
	
			'Galeopsis segetum 4.jpg' => 'foto',
	
			'Galeopsis segetum 5.jpg' => 'foto'
	
		),
	
		'Galeopsis angustifolia' => array(
	
			'Galeopsis angustifolia.jpg' => 'overzicht',
	
			'Galeopsis angustifolia 2.jpg' => 'vergroot',
	
			'Galeopsis angustifolia 3.jpg' => 'foto',
	
			'Galeopsis angustifolia 4.jpg' => 'foto'
	
		),
	
		'Galeopsis ladanum' => array(
	
			'Galeopsis ladanum.jpg' => 'overzicht',
	
			'Galeopsis ladanum 2.jpg' => 'vergroot'
	
		),
	
		'Galeopsis pubescens' => array(
	
			'Galeopsis pubescens.jpg' => 'overzicht',
	
			'Galeopsis pubescens 2.jpg' => 'vergroot',
	
			'Galeopsis pubescens 3.jpg' => 'foto',
	
			'Galeopsis pubescens 4.jpg' => 'foto, bloem'
	
		),
	
		'Galeopsis speciosa' => array(
	
			'Galeopsis speciosa.jpg' => 'overzicht',
	
			'Galeopsis speciosa 2.jpg' => 'vergroot',
	
			'Galeopsis speciosa 3.jpg' => 'foto'
	
		),
	
		'Galeopsis tetrahit' => array(
	
			'Galeopsis tetrahit.jpg' => 'overzicht; a = bloem',
	
			'Galeopsis tetrahit 2.jpg' => 'vergroot',
	
			'Galeopsis tetrahit 3.jpg' => 'foto'
	
		),
	
		'Galeopsis bifida' => array(
	
			'Galeopsis bifida.jpg' => 'overzicht; a = bloem',
	
			'Galeopsis bifida 2.jpg' => 'vergroot',
	
			'Galeopsis bifida 3.jpg' => 'foto'
	
		),
	
		'Lamium maculatum' => array(
	
			'Lamium maculatum.jpg' => 'overzicht',
	
			'Lamium maculatum 2.jpg' => 'vergroot',
	
			'Lamium maculatum 3.jpg' => 'foto',
	
			'Lamium maculatum 4.jpg' => 'foto',
	
			'Lamium maculatum 5.jpg' => "foto 'Variegatum'"
	
		),
	
		'Lamium album' => array(
	
			'Lamium album.jpg' => 'overzicht; a = bloemkroon, b = dsn. bloem',
	
			'Lamium album 2.jpg' => 'vergroot',
	
			'Lamium album 3.jpg' => 'foto',
	
			'Lamium album 4.jpg' => 'foto'
	
		),
	
		'Lamium amplexicaule' => array(
	
			'Lamium amplexicaule.jpg' => 'overzicht',
	
			'Lamium amplexicaule 2.jpg' => 'vergroot',
	
			'Lamium amplexicaule 3.jpg' => 'foto',
	
			'Lamium amplexicaule 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Lamium confertum' => array(
	
			'Lamium confertum.jpg' => 'overzicht',
	
			'Lamium confertum 2.jpg' => 'vergroot',
	
			'Lamium confertum 3.jpg' => 'foto'
	
		),
	
		'Lamium purpureum' => array(
	
			'Lamium purpureum.jpg' => 'overzicht',
	
			'Lamium purpureum 2.jpg' => 'vergroot',
	
			'Lamium purpureum 3.jpg' => 'foto'
	
		),
	
		'Lamium hybridum' => array(
	
			'Lamium hybridum.jpg' => 'overzicht',
	
			'Lamium hybridum 2.jpg' => 'vergroot',
	
			'Lamium hybridum 3.jpg' => 'foto',
	
			'Lamium hybridum 4.jpg' => 'foto'
	
		),
	
		'Lamiastrum galeobdolon' => array(
	
			'Lamiastrum galeobdolon.jpg' => 'overzicht',
	
			'Lamiastrum galeobdolon 2.jpg' => 'foto, bloemen'
	
		),
	
		'Lamiastrum galeobdolon subsp. argentatum' => array(
	
			'Lamiastrum galeobdolon arg.jpg' => 'overzicht',
	
			'Lamiastrum galeobdolon arg 3.jpg' => 'foto',
	
			'Lamiastrum galeobdolon arg 4.jpg' => 'foto'
	
		),
	
		'Lamiastrum galeobdolon subsp. galeobdolon' => array(
	
			'Lamiastrum galeobdolon gal.jpg' => 'overzicht; a = dsn. bloem',
	
			'Lamiastrum galeobdolon gal 2.jpg' => 'vergroot',
	
			'Lamiastrum galeobdolon gal 3.jpg' => 'foto'
	
		),
	
		'Leonurus cardiaca' => array(
	
			'Leonurus cardiaca.jpg' => 'overzicht',
	
			'Leonurus cardiaca 2.jpg' => 'vergroot',
	
			'Leonurus cardiaca 3.jpg' => 'foto',
	
			'Leonurus cardiaca 4.jpg' => 'foto'
	
		),
	
		'Ballota nigra subsp. meridionalis' => array(
	
			'Ballote nigra meridionalis.jpg' => 'overzicht; a = bloem, b = dsn. bloem',
	
			'Ballote nigra meridionalis 2.jpg' => 'vergroot',
	
			'Ballota nigra nigra 3.jpg' => 'foto',
	
			'Ballota nigra nigra 4.jpg' => 'foto'
	
		),
	
		'Stachys recta' => array(
	
			'Stachys recta.jpg' => 'overzicht',
	
			'Stachys recta 2.jpg' => 'vergroot',
	
			'Stachys recta 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Stachys annua' => array(
	
			'Stachys annua.jpg' => 'overzicht',
	
			'Stachys annua 2.jpg' => 'vergroot',
	
			'Stachys annua 3.jpg' => 'foto, bloeiwijze',
	
			'Stachys annua 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Stachys officinalis' => array(
	
			'Stachys officinalis.jpg' => 'overzicht; a = bloem',
	
			'Stachys officinalis 2.jpg' => 'vergroot',
	
			'Stachys officinalis 3.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Stachys arvensis' => array(
	
			'Stachys arvensis.jpg' => 'overzicht',
	
			'Stachys arvensis 2.jpg' => 'vergroot',
	
			'Stachys arvensis 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Stachys palustris' => array(
	
			'Stachys palustris.jpg' => 'overzicht',
	
			'Stachys palustris 2.jpg' => 'vergroot',
	
			'Stachys palustris 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Stachys alpina' => array(
	
			'Stachys alpina.jpg' => 'overzicht',
	
			'Stachys alpina 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Stachys sylvatica' => array(
	
			'Stachys sylvatica.jpg' => 'overzicht; a = bloem, b = dsn. bloem',
	
			'Stachys sylvatica 2.jpg' => 'vergroot',
	
			'Stachys sylvatica 3.jpg' => 'foto, habitus bloeiend',
	
			'Stachys sylvatica 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Stachys ambigua(x)' => array(
	
			'Stachys ambigua(x).jpg' => 'overzicht',
	
			'Stachys ambigua(x) 2.jpg' => 'vergroot',
	
			'Stachys ambigua(x) 3.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Nepeta cataria' => array(
	
			'Nepeta cataria.jpg' => 'overzicht',
	
			'Nepeta cataria 2.jpg' => 'vergroot; a = kroon, b = kelk',
	
			'Nepeta cataria 3.jpg' => 'foto'
	
		),
	
		'Nepeta racemosa' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Nepeta faassenii(x)' => array(
	
			'Nepeta faassenii(x).jpg' => 'overzicht',
	
			'Nepeta faassenii(x) 2.jpg' => 'foto',
	
			'Nepeta faassenii(x) 3.jpg' => 'foto'
	
		),
	
		'Glechoma hederacea' => array(
	
			'Glechoma hederacea.jpg' => 'overzicht',
	
			'Glechoma hederacea 2.jpg' => 'vergroot',
	
			'Glechoma hederacea 3.jpg' => 'foto',
	
			'Glechoma hederacea 4.jpg' => 'foto'
	
		),
	
		'Prunella vulgaris' => array(
	
			'Prunella vulgaris.jpg' => 'overzicht',
	
			'Prunella vulgaris 2.jpg' => 'vergroot; a = kelk',
	
			'Prunella vulgaris 3.jpg' => 'foto',
	
			'Prunella vulgaris 4.jpg' => 'foto'
	
		),
	
		'Satureja hortensis' => array(
	
			'Satureja hortensis.jpg' => 'overzicht',
	
			'Satureja hortensis 2.jpg' => 'vergroot'
	
		),
	
		'Clinopodium acinos' => array(
	
			'Clinopodium acinos.jpg' => 'overzicht',
	
			'Clinopodium acinos 2.jpg' => 'vergroot;  a = dsn. bloem, b = kelk',
	
			'Clinopodium acinos 3.jpg' => 'foto'
	
		),
	
		'Clinopodium vulgare' => array(
	
			'Clinopodium vulgare.jpg' => 'overzicht',
	
			'Clinopodium vulgare 2.jpg' => 'vergroot; a = dsn. bloem, b = kelk',
	
			'Clinopodium vulgare 3.jpg' => 'foto',
	
			'Clinopodium vulgare 4.jpg' => 'foto'
	
		),
	
		'Clinopodium grandiflorum' => array(
	
			'Clinopodium grandiflorum.jpg' => 'overzicht',
	
			'Clinopodium grandiflorum 2.jpg' => 'vergroot',
	
			'Clinopodium grandiflorum 3.jpg' => 'foto',
	
			'Clinopodium grandiflorum 4.jpg' => 'foto'
	
		),
	
		'Clinopodium calamintha' => array(
	
			'Clinopodium calamintha.jpg' => 'overzicht',
	
			'Clinopodium calamintha 2.jpg' => 'vergroot',
	
			'Clinopodium calamintha 3.jpg' => 'foto',
	
			'Clinopodium calamintha 4.jpg' => 'foto',
	
			'Clinopodium calamintha 5.jpg' => 'foto, habitus bloeiend',
	
			'Clinopodium calamintha 6.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Clinopodium menthifolium' => array(
	
			'Clinopodium menthifolium.jpg' => 'overzicht',
	
			'Clinopodium menthifolium 2.jpg' => 'vergroot'
	
		),
	
		'Melissa officinalis' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Hyssopus officinalis' => array(
	
			'Hyssopus officinalis.jpg' => 'overzicht',
	
			'Hyssopus officinalis 2.jpg' => 'vergroot; a = bloem, b = kelk',
	
			'Hyssopus officinalis 3.jpg' => 'foto, bloeiwijzen',
	
			'Hyssopus officinalis 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Origanum vulgare' => array(
	
			'Origanum vulgare.jpg' => 'overzicht',
	
			'Origanum vulgare 2.jpg' => 'vergroot; a = bloem',
	
			'Origanum vulgare 3.jpg' => 'foto',
	
			'Origanum vulgare 4.jpg' => 'foto'
	
		),
	
		'Origanum majorana' => array(
	
			'Origanum majorana.jpg' => 'overzicht',
	
			'Origanum majorana 2.jpg' => 'vergroot; a = geopende bloem'
	
		),
	
		'Thymus vulgaris' => array(
	
			'Thymus vulgaris.jpg' => 'overzicht',
	
			'Thymus vulgaris 2.jpg' => 'vergroot',
	
			'Thymus vulgaris 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Thymus pulegioides' => array(
	
			'Thymus pulegioides.jpg' => 'overzicht',
	
			'Thymus pulegioides 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem',
	
			'Thymus pulegioides 3.jpg' => 'foto, habitus, bloeiend'
	
		),
	
		'Thymus serpyllum' => array(
	
			'Thymus serpyllum.jpg' => 'overzicht',
	
			'Thymus serpyllum 2.jpg' => 'vergroot',
	
			'Thymus serpyllum 3.jpg' => 'foto, habitus, bloeiend'
	
		),
	
		'Thymus praecox' => array(
	
			'Thymus praecox.jpg' => 'overzicht foto',
	
			'Thymus praecox 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Lycopus europaeus' => array(
	
			'Lycopus europaeus.jpg' => 'overzicht',
	
			'Lycopus europaeus 2.jpg' => 'vergroot',
	
			'Lycopus europaeus 3.jpg' => 'foto',
	
			'Lycopus europaeus 4.jpg' => 'foto'
	
		),
	
		'Mentha aquatica' => array(
	
			'Mentha aquatica.jpg' => 'overzicht',
	
			'Mentha aquatica 2.jpg' => 'vergroot; a = kelk',
	
			'Mentha aquatica 3.jpg' => 'foto',
	
			'Mentha aquatica 4.jpg' => 'foto'
	
		),
	
		'Mentha pulegium' => array(
	
			'Mentha pulegium.jpg' => 'overzicht; a = bloem, b = kelk',
	
			'Mentha pulegium 2.jpg' => 'vergroot',
	
			'Mentha pulegium 3.jpg' => 'foto'
	
		),
	
		'Mentha arvensis' => array(
	
			'Mentha arvensis.jpg' => 'overzicht',
	
			'Mentha arvensis 2.jpg' => 'vergroot; a = kelk',
	
			'Mentha arvensis 3.jpg' => 'foto',
	
			'Mentha gracilis(x).jpg' => '(= Mentaha arvensis x spicata) vergroot'
	
		),
	
		'Mentha verticillata(x)' => array(
	
			'Mentha verticillata(x).jpg' => 'overzicht',
	
			'Mentha verticillata(x) 2.jpg' => 'vergroot; a = kelk',
	
			'Mentha verticillata(x) 3.jpg' => 'foto'
	
		),
	
		'Mentha piperita(x)' => array(
	
			'Mentha piperita(x).jpg' => 'overzicht',
	
			'Mentha piperita(x) 2.jpg' => 'vergroot'
	
		),
	
		'Mentha spicata' => array(
	
			'Mentha spicata.jpg' => 'overzicht',
	
			'Mentha spicata 2.jpg' => 'vergroot',
	
			'Kruizemunt.jpg' => 'Kruizemunt',
	
			'Mentha gracilis(x).jpg' => '(= Mentaha arvensis x spicata) vergroot'
	
		),
	
		'Mentha longifolia' => array(
	
			'Mentha longifolia.jpg' => 'overzicht',
	
			'Mentha longifolia 2.jpg' => 'vergroot; a = kelk',
	
			'Mentha longifolia 3.jpg' => 'foto'
	
		),
	
		'Mentha suaveolens' => array(
	
			'Mentha suaveolens.jpg' => 'overzicht',
	
			'Mentha suaveolens 2.jpg' => 'vergroot; a = kelk',
	
			'Mentha suaveolens 3.jpg' => 'foto'
	
		),
	
		'Mentha rotundifolia(x)' => array(
	
			'Mentha rotundifolia(x).jpg' => 'overzicht; a = kelk',
	
			'Mentha rotundifolia(x) 2.jpg' => 'vergroot',
	
			'Mentha rotundifolia(x) 3.jpg' => 'foto'
	
		),
	
		'Rosmarinus officinalis' => array(
	
			'Rosmarinus officinalis.jpg' => 'overzicht; a = bloem',
	
			'Rosmarinus officinalis 2.jpg' => 'vergroot',
	
			'Rosmarinus officinalis 3.jpg' => 'foto, bloeiend',
	
			'Rosmarinus officinalis 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Salvia officinalis' => array(
	
			'Salvia officinalis.jpg' => 'overzicht',
	
			'Salvia officinalis 2.jpg' => 'vergroot; a = dsn. bloem, b = meeldraad, c = vruchtkelk',
	
			'Salvia officinalis 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Salvia pratensis' => array(
	
			'Salvia pratensis.jpg' => 'overzicht',
	
			'Salvia pratensis 2.jpg' => 'vergroot; a = dsn. bloem, b = meeldraad, c = kelk',
	
			'Salvia pratensis 3.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Salvia verbenaca' => array(
	
			'Salvia verbenaca.jpg' => 'overzicht',
	
			'Salvia verbenaca 2.jpg' => 'vergroot',
	
			'Salvia verbenaca 3.jpg' => 'foto, bloeiend',
	
			'Salvia verbenaca 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Salvia verticillata' => array(
	
			'Salvia verticillata.jpg' => 'overzicht',
	
			'Salvia verticillata 2.jpg' => 'vergroot',
	
			'Salvia verticillata 3.jpg' => 'foto, bloeiend',
	
			'Salvia verticillata 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Salvia nemorosa' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Ocimum basilicum' => array(
	
			'Ocimum basilicum.jpg' => 'overzicht; a = kelk van onderen',
	
			'Ocimum basilicum 2.jpg' => 'vergroot',
	
			'Ocimum basilicum 3.jpg' => 'foto',
	
			'Ocimum basilicum 4.jpg' => 'foto'
	
		),
	
		'Mimulus guttatus' => array(
	
			'Mimulus guttatus.jpg' => 'overzicht',
	
			'Mimulus guttatus 2.jpg' => 'vergroot; a = dsn. bloem'
	
		),
	
		'Mimulus moschatus' => array(
	
			'Mimulus moschatus.jpg' => 'overzicht foto',
	
			'Mimulus moschatus 3.jpg' => 'foto',
	
			'Mimulus moschatus 4.jpg' => 'foto'
	
		),
	
		'Paulownia tomentosa' => array(
	
			'Paulownia tomentosa.jpg' => 'overzicht',
	
			'Paulownia tomentosa 2.jpg' => 'vergroot',
	
			'Paulownia tomentosa 3.jpg' => 'foto, boom',
	
			'Paulownia tomentosa 4.jpg' => 'foto, vruchten in boom'
	
		),
	
		'Rhinanthus alectorolophus' => array(
	
			'Rhinanthus alectorolophus.jpg' => 'overzicht',
	
			'Rhinanthus alectorolophus 2.jpg' => 'vergroot; a = schutblad, b = bloem, c = bloemkroon',
	
			'Rhinanthus alectorolophus 3.jpg' => 'foto, habitus in bloei',
	
			'Rhinanthus alectorolophus 4.jpg' => 'foto, bloeiwijze',
	
			'Rhinanthus alectorolophus 5.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Rhinanthus angustifolius' => array(
	
			'Rhinanthus angustifolius.jpg' => 'overzicht',
	
			'Rhinanthus angustifolius 2.jpg' => 'vergroot; a = schutblad, b = bloem, c = bloemkroon',
	
			'Rhinanthus angustifolius 3.jpg' => 'foto, bloeiend',
	
			'Rhinanthus angustifolius 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Rhinanthus minor' => array(
	
			'Rhinanthus minor.jpg' => 'overzicht',
	
			'Rhinanthus minor 2.jpg' => 'vergroot; a = schutblad, b = bloem, c = bloemkroon',
	
			'Rhinanthus minor 3.jpg' => 'foto, habitus in bloei'
	
		),
	
		'Melampyrum pratense' => array(
	
			'Melampyrum pratense.jpg' => 'overzicht; a = bloem, b = dsn. bloem',
	
			'Melampyrum pratense 2.jpg' => 'vergroot',
	
			'Melampyrum pratense 3.jpg' => 'foto'
	
		),
	
		'Melampyrum arvense' => array(
	
			'Melampyrum arvense.jpg' => 'overzicht; a = bloem',
	
			'Melampyrum arvense 2.jpg' => 'vergroot',
	
			'Melampyrum arvense 3.jpg' => 'foto',
	
			'Melampyrum arvense 4.jpg' => 'foto'
	
		),
	
		'Lathraea clandestina' => array(
	
			'Lathraea clandestina.jpg' => 'overzicht foto',
	
			'Lathraea clandestina 2.jpg' => 'tekening bloemen',
	
			'Lathraea clandestina 3.jpg' => 'foto',
	
			'Lathraea clandestina 4.jpg' => 'foto',
	
			'Lathraea clandestina 5.jpg' => 'foto'
	
		),
	
		'Lathraea squamaria' => array(
	
			'Lathraea squamaria.jpg' => 'overzicht',
	
			'Lathraea squamaria 2.jpg' => 'vergroot',
	
			'Lathraea squamaria 3.jpg' => 'foto',
	
			'Lathraea squamaria 4.jpg' => 'foto',
	
			'Lathraea squamaria 5.jpg' => 'foto, bloeiwijze',
	
			'Lathraea squamaria 6.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Parentucellia viscosa' => array(
	
			'Parentucellia viscosa.jpg' => 'overzicht foto',
	
			'Parentucellia viscosa 3.jpg' => 'foto',
	
			'Parentucellia viscosa 4.jpg' => 'foto'
	
		),
	
		'Euphrasia officinalis' => array(
	
			'Euphrasia officinalis.jpg' => 'overzicht foto',
	
			'Euphrasia officinalis 3.jpg' => 'foto',
	
			'Euphrasia officinalis 4.jpg' => 'foto'
	
		),
	
		'Euphrasia stricta' => array(
	
			'Euphrasia stricta.jpg' => 'overzicht',
	
			'Euphrasia stricta 2.jpg' => 'vergroot',
	
			'Euphrasia stricta 3.jpg' => 'foto',
	
			'Euphrasia stricta 4.jpg' => 'foto'
	
		),
	
		'Euphrasia stricta-tetraquetra' => array(
	
			'Euphrasia tetraquetra.jpg' => 'overzicht',
	
			'Euphrasia tetraquetra 2.jpg' => 'foto'
	
		),
	
		'Euphrasia stricta-stricta' => array(
	
			'Euphrasia stricta.jpg' => 'overzicht',
	
			'Euphrasia stricta 2.jpg' => 'foto',
	
			'Euphrasia stricta 3.jpg' => 'foto',
	
			'Euphrasia stricta 4.jpg' => 'foto'
	
		),
	
		'Euphrasia stricta-micrantha' => array(
	
			'Euphrasia micrantha.jpg' => 'overzicht',
	
			'Euphrasia micrantha 2.jpg' => 'foto',
	
			'Euphrasia micrantha 3.jpg' => 'foto'
	
		),
	
		'Euphrasia stricta-nemorosa' => array(
	
			'Euphrasia nemorosa.jpg' => 'overzicht',
	
			'Euphrasia nemorosa 2.jpg' => 'foto',
	
			'Euphrasia nemorosa 3.jpg' => 'foto',
	
			'Euphrasia nemorosa 4.jpg' => 'foto'
	
		),
	
		'Odontites vernus' => array(
	
			'Odontites vernus vernus.jpg' => 'overzicht',
	
			'Odontites vernus vernus 2.jpg' => 'vergroot; a = bloem, subsp. vernus',
	
			'Odontites vernus serotinus 2.jpg' => 'vergroot, subsp. serotinus'
	
		),
	
		'Odontites vernus subsp. serotinus' => array(
	
			'Odontites vernus serotinus.jpg' => 'overzicht',
	
			'Odontites vernus serotinus 2.jpg' => 'vergroot',
	
			'Odontites vernus serotinus 3.jpg' => 'foto',
	
			'Odontites vernus litoralis 3.jpg' => 'foto subsp.litoralis (zie opmerking)',
	
			'Odontites vernus serotinus 4.jpg' => 'foto',
	
			'Odontites vernus serotinus 5.jpg' => 'foto'
	
		),
	
		'Odontites vernus subsp. vernus' => array(
	
			'Odontites vernus vernus.jpg' => 'overzicht',
	
			'Odontites vernus vernus 2.jpg' => 'vergroot; a = bloem'
	
		),
	
		'Orobanche purpurea' => array(
	
			'Orobanche purpurea.jpg' => 'overzicht',
	
			'Orobanche purpurea 2.jpg' => 'vergroot; a = bloem, b = stamper + meeldraden',
	
			'Orobanche purpurea 2a.jpg' => 'bloeiwijze en bloem',
	
			'Orobanche purpurea 3.jpg' => 'foto',
	
			'Orobanche purpurea 4.jpg' => 'foto',
	
			'Orobanche purpurea 5.jpg' => 'foto'
	
		),
	
		'Orobanche ramosa' => array(
	
			'Orobanche ramosa.jpg' => 'overzicht',
	
			'Orobanche ramosa 2.jpg' => 'vergroot; a = bloem, b = stamper + meeldraden',
	
			'Orobanche ramosa 3.jpg' => 'foto',
	
			'Orobanche ramosa 4.jpg' => 'foto',
	
			'Orobanche ramosa 5.jpg' => 'foto'
	
		),
	
		'Orobanche minor' => array(
	
			'Orobanche minor.jpg' => 'overzicht',
	
			'Orobanche minor 2.jpg' => 'vergroot; a = bloem, b = stamper + meeldraden',
	
			'Orobanche minor 2a.jpg' => 'bloeiwijze en bloem',
	
			'Orobanche minor 3.jpg' => 'foto',
	
			'Orobanche minor 4.jpg' => 'foto',
	
			'Orobanche minor 5.jpg' => 'foto'
	
		),
	
		'Orobanche rapum-genistae' => array(
	
			'Orobanche rapum-genistae.jpg' => 'overzicht',
	
			'Orobanche rapum-genistae 2.jpg' => 'vergroot; a = lds. bloem, b = kelkbladen',
	
			'Orobanche rapum-genistae 2a.jpg' => 'bloeiwijze en bloem',
	
			'Orobanche rapum-genistae 3.jpg' => 'foto',
	
			'Orobanche rapum-genistae 4.jpg' => 'foto',
	
			'Orobanche rapum-genistae 5.jpg' => 'foto'
	
		),
	
		'Orobanche elatior' => array(
	
			'Orobanche elatior.jpg' => 'overzicht',
	
			'Orobanche elatior 2.jpg' => 'vergroot; a = bloem, b = stamper + meeldraden',
	
			'Orobanche elatior 2a.jpg' => 'bloeiwijze en bloem',
	
			'Orobanche elatior 3.jpg' => 'foto',
	
			'Orobanche elatior 4.jpg' => 'foto'
	
		),
	
		'Orobanche hederae' => array(
	
			'Orobanche hederae.jpg' => 'overzicht',
	
			'Orobanche hederae 2.jpg' => 'vergroot; a = bloem, b = stamper + meeldraden',
	
			'Orobanche hederae 2a.jpg' => 'bloeiwijze en bloem',
	
			'Orobanche hederae 3.jpg' => 'foto',
	
			'Orobanche hederae 4.jpg' => 'foto',
	
			'Orobanche hederae 5.jpg' => 'foto'
	
		),
	
		'Orobanche reticulata' => array(
	
			'Orobanche reticulata.jpg' => 'overzicht',
	
			'Orobanche reticulata 2.jpg' => 'vergroot; a = bloem, b = stamper + meeldraden',
	
			'Orobanche reticulata 2a.jpg' => 'bloeiwijze en bloem',
	
			'Orobanche reticulata 3.jpg' => 'foto',
	
			'Orobanche reticulata 4.jpg' => 'foto',
	
			'Orobanche reticulata 5.jpg' => 'foto'
	
		),
	
		'Orobanche caryophyllacea' => array(
	
			'Orobanche caryophyllacea.jpg' => 'overzicht',
	
			'Orobanche caryophyllacea 2.jpg' => 'vergroot; a = bloem, b = stamper + meeldraden',
	
			'Orobanche caryophyllacea 2a.jpg' => 'bloeiwijze en bloem',
	
			'Orobanche caryophyllacea 3.jpg' => 'foto',
	
			'Orobanche caryophyllacea 4.jpg' => 'foto'
	
		),
	
		'Orobanche lutea' => array(
	
			'Orobanche lutea.jpg' => 'overzicht',
	
			'Orobanche lutea 2.jpg' => 'vergroot; a = bloem, b = stamper + meeldraden',
	
			'Orobanche lutea 2a.jpg' => 'bloeiwijze en bloem',
	
			'Orobanche lutea 3.jpg' => 'foto',
	
			'Orobanche lutea 4.jpg' => 'foto'
	
		),
	
		'Orobanche picridis' => array(
	
			'Orobanche picridis.jpg' => 'overzicht',
	
			'Orobanche picridis 2.jpg' => 'vergroot; a = bloem, b = stamper + meeldraden',
	
			'Orobanche picridis 2a.jpg' => 'bloeiwijze en bloem',
	
			'Orobanche picridis 3.jpg' => 'foto',
	
			'Orobanche picridis 4.jpg' => 'foto'
	
		),
	
		'Pedicularis sylvatica' => array(
	
			'Pedicularis sylvatica.jpg' => 'overzicht; a = bloem',
	
			'Pedicularis sylvatica 2.jpg' => 'vergroot',
	
			'Pedicularis sylvatica 3.jpg' => 'foto'
	
		),
	
		'Pedicularis palustris' => array(
	
			'Pedicularis palustris.jpg' => 'overzicht; a = bloem',
	
			'Pedicularis palustris 2.jpg' => 'vergroot',
	
			'Pedicularis palustris 3.jpg' => 'foto'
	
		),
	
		'Gratiola officinalis' => array(
	
			'Gratiola officinalis.jpg' => 'overzicht',
	
			'Gratiola officinalis 2.jpg' => 'vergroot; a = opengemaakte bloem',
	
			'Gratiola officinalis 3.jpg' => 'foto'
	
		),
	
		'Limosella aquatica' => array(
	
			'Limosella aquatica.jpg' => 'overzicht',
	
			'Limosella aquatica 2.jpg' => 'vergroot; a = bloem, b = opengemaakte bloem',
	
			'Limosella aquatica 3.jpg' => 'foto'
	
		),
	
		'Misopates orontium' => array(
	
			'Misopates orontium.jpg' => 'overzicht; a = doosvrucht',
	
			'Misopates orontium 2.jpg' => 'vergroot',
	
			'Misopates orontium 3.jpg' => 'foto'
	
		),
	
		'Linaria arvensis' => array(
	
			'Linaria arvensis.jpg' => 'overzicht; a = bloem, b = vruchtkelk',
	
			'Linaria arvensis 2.jpg' => 'vergroot'
	
		),
	
		'Linaria repens' => array(
	
			'Linaria repens.jpg' => 'overzicht; a = bloem',
	
			'Linaria repens 2.jpg' => 'vergroot',
	
			'Linaria repens 3.jpg' => 'foto',
	
			'Linaria repens 4.jpg' => 'foto'
	
		),
	
		'Linaria purpurea' => array(
	
			'Linaria purpurea.jpg' => 'overzicht',
	
			'Linaria purpurea 2.jpg' => 'foto'
	
		),
	
		'Linaria vulgaris' => array(
	
			'Linaria vulgaris.jpg' => 'overzicht',
	
			'Linaria vulgaris 2.jpg' => 'vergroot',
	
			'Linaria vulgaris 3.jpg' => 'foto',
	
			'Linaria vulgaris 4.jpg' => 'foto',
	
			'Linaria vulgaris.mov' => 'zicht rondom bloeiwijze: klik op foto en beweeg naar links of rechts'
	
		),
	
		'Linaria supina' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Chaenorhinum minus' => array(
	
			'Chaenorhinum minus.jpg' => 'overzicht; a = bloem van onderen, b = vruchtkelk',
	
			'Chaenorhinum minus 2.jpg' => 'vergroot',
	
			'Chaenorhinum minus 3.jpg' => 'foto'
	
		),
	
		'Chaenorhinum origanifolium' => array(
	
			'Chaenorhinum origanifolium.jpg' => 'overzicht',
	
			'Chaenorhinum origanifolium 2.jpg' => 'foto'
	
		),
	
		'Antirrhinum majus' => array(
	
			'Antirrhinum majus.jpg' => 'overzicht; a = doosvrucht',
	
			'Antirrhinum majus 2.jpg' => 'vergroot',
	
			'Antirrhinum majus 3.jpg' => 'foto, bloemen',
	
			'Antirrhinum majus 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Cymbalaria muralis' => array(
	
			'Cymbalaria muralis.jpg' => 'overzicht',
	
			'Cymbalaria muralis 2.jpg' => 'vergroot',
	
			'Cymbalaria muralis 3.jpg' => 'foto',
	
			'Cymbalaria muralis 4.jpg' => 'foto'
	
		),
	
		'Kickxia elatine' => array(
	
			'Kickxia elatine.jpg' => 'overzicht; a = vruchtkelk',
	
			'Kickxia elatine 2.jpg' => 'vergroot',
	
			'Kickxia elatine 3.jpg' => 'foto',
	
			'Kickxia elatine 4.jpg' => 'foto'
	
		),
	
		'Kickxia spuria' => array(
	
			'Kickxia spuria.jpg' => 'overzicht',
	
			'Kickxia spuria 2.jpg' => 'vergroot',
	
			'Kickxia spuria 3.jpg' => 'foto'
	
		),
	
		'Callitriche cophocarpa' => array(
	
			'Callitriche cophocarpa.jpg' => 'overzicht; vrucht',
			
			'Callitriche cophocarpa 2.jpg' => 'vergroot'
	
		),
	
		'Callitriche hermaphroditica' => array(
	
			'Callitriche hermaphroditica.jpg' => 'overzicht',
	
			'Callitriche hermaphroditica2.jpg' => 'vergroot'
	
		),
	
		'Callitriche truncata' => array(
	
			'Callitriche truncata.jpg' => 'overzicht',
	
			'Callitriche truncata 2.jpg' => 'vergroot; a = takje met 2 manlijke bloemen (onder) en 1 vrouwelijke bloem, b = vrucht',
	
			'Callitriche truncata 3.jpg' => 'foto',
	
			'Callitriche truncata 4.jpg' => 'foto'
	
		),
	
		'Callitriche brutia' => array(
	
			'Callitriche brutia.jpg' => 'overzicht; a = blad, b = vrucht',
	
			'Callitriche brutia 2.jpg' => 'vergroot; a = blad, b = vrucht',
	
			'Callitriche brutia 3.jpg' => 'vergroot; a = blad, b = vrucht'
	
		),
	
		'Callitriche obtusangula' => array(
	
			'Callitriche obtusangula.jpg' => 'overzicht; a = vrucht, b = bladen, c = stuifmeelkorrels',
	
			'Callitriche obtusangula 2.jpg' => 'vergroot'
	
		),
	
		'Callitriche palustris' => array(
	
			'Callitriche palustris.jpg' => 'overzicht; vrucht',
	
			'Callitriche palustris 2.jpg' => 'foto, habitus'
	
		),
	
		'Callitriche platycarpa' => array(
	
			'Callitriche platycarpa.jpg' => 'overzicht; a = vrucht, b = bladen, c = stuifmeelkorrels',
	
			'Callitriche platycarpa 2.jpg' => 'vergroot',
	
			'Callitriche platycarpa 3.jpg' => 'foto'
	
		),
	
		'Callitriche stagnalis' => array(
	
			'Callitriche stagnalis.jpg' => 'overzicht; a = vrucht, b = bladen, c = stuifmeelkorrels',
	
			'Callitriche stagnalis 2.jpg' => 'vergroot',
	
			'Callitriche stagnalis 3.jpg' => 'foto',
	
			'Callitriche stagnalis 4.jpg' => 'jonge plant'
	
		),
	
		'Hippuris vulgaris' => array(
	
			'Hippuris vulgaris.jpg' => 'overzicht',
	
			'Hippuris vulgaris 2.jpg' => 'vergroot; a = tweeslachtige bloem, b = vrouwelijke bloem',
	
			'Hippuris vulgaris 3.jpg' => 'foto',
	
			'Hippuris vulgaris 4.jpg' => 'foto',
	
			'Hippuris vulgaris 5.jpg' => 'foto; onderwatervorm'
	
		),
	
		'Digitalis purpurea' => array(
	
			'Digitalis purpurea.jpg' => 'overzicht',
	
			'Digitalis purpurea 2.jpg' => 'vergroot; a = dsn. bloem, b = dsn. vruchtbeginsel, c = vruchtkelk',
	
			'Digitalis purpurea 3.jpg' => 'foto, bloeiend habitus',
	
			'Digitalis purpurea 3.jpg' => 'foto, detail bloeiwijze'
	
		),
	
		'Digitalis lutea' => array(
	
			'Digitalis lutea.jpg' => 'overzicht',
	
			'Digitalis lutea 2.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Veronica hederifolia' => array(
	
			'Veronica hederifolia.jpg' => 'overzicht',
	
			'Veronica hederifolia 2.jpg' => 'vergroot; habitus, a = dsn. bloem',
	
			'Veronica hederifolia 3.jpg' => 'foto, habitus bloeiend',
	
			'Veronica hederifolia 4.jpg' => 'foto, habitus bloeiend',
	
			'Veronica hederifolia 5.jpg' => 'habitus met details kelk en vrucht'
	
		),
	
		'Veronica triphyllos' => array(
	
			'Veronica triphyllos.jpg' => 'overzicht',
	
			'Veronica triphyllos 2.jpg' => 'vergroot; a = vruchtkelk',
	
			'Veronica triphyllos 3.jpg' => 'foto, habitus bloeiend',
	
			'Veronica triphyllos 4.jpg' => 'foto, habitus bloeiend',
	
			'Veronica triphyllos 5.jpg' => 'foto, bloeiend'
	
		),
	
		'Veronica verna' => array(
	
			'Veronica verna.jpg' => 'overzicht',
	
			'Veronica verna 2.jpg' => 'vergroot; a = vruchtkelk',
	
			'Veronica verna 3.jpg' => 'foto, habitus bloeiend',
	
			'Veronica verna 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Veronica filiformis' => array(
	
			'Veronica filiformis.jpg' => 'overzicht foto',
	
			'Veronica filiformis 2.jpg' => 'vergroot; vruchtkelk',
	
			'Veronica filiformis 3.jpg' => 'foto, habitus bloeiend',
	
			'Veronica filiformis 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Veronica persica' => array(
	
			'Veronica persica.jpg' => 'overzicht',
	
			'Veronica persica 2.jpg' => 'vergroot; a = vruchtkelk',
	
			'Veronica persica 3.jpg' => 'foto, habitus bloeiend',
	
			'Veronica persica 4.jpg' => 'foto, habitus bloeiend',
	
			'Veronica persica 5.jpg' => 'foto, bloeiend'
	
		),
	
		'Veronica serpyllifolia' => array(
	
			'Veronica serpyllifolia.jpg' => 'overzicht',
	
			'Veronica serpyllifolia 2.jpg' => 'vergroot; a = vruchtkelk',
	
			'Veronica serpyllifolia 3.jpg' => 'foto, habitus bloeiend',
	
			'Veronica serpyllifolia 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Veronica peregrina' => array(
	
			'Veronica peregrina.jpg' => 'overzicht',
	
			'Veronica peregrina 2.jpg' => 'vergroot; a = vruchtkelk',
	
			'Veronica peregrina 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Veronica arvensis' => array(
	
			'Veronica arvensis.jpg' => 'overzicht',
	
			'Veronica arvensis 2.jpg' => 'vergroot; a = vruchtkelk',
	
			'Veronica arvensis 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Veronica praecox' => array(
	
			'Veronica praecox.jpg' => 'overzicht',
	
			'Veronica praecox 2.jpg' => 'vergroot; a = vruchtkelk',
	
			'Veronica praecox 3.jpg' => 'foto, habitus bloeiend',
	
			'Veronica praecox 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Veronica acinifolia' => array(
	
			'Veronica acinifolia.jpg' => 'overzicht',
	
			'Veronica acinifolia 2.jpg' => 'vergroot, habitus met vruchtkelk'
	
		),
	
		'Veronica agrestis' => array(
	
			'Veronica agrestis.jpg' => 'overzicht',
	
			'Veronica agrestis 2.jpg' => 'vergroot; a = vruchtkelk',
	
			'Veronica agrestis 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Veronica polita' => array(
	
			'Veronica polita.jpg' => 'overzicht',
	
			'Veronica polita 2.jpg' => 'vergroot; a = bloem, b = dsn. vrucht, c = vruchtkelk',
	
			'Veronica polita 3.jpg' => 'foto, habitus bloeiend',
	
			'Veronica polita 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Veronica opaca' => array(
	
			'Veronica opaca.jpg' => 'overzicht',
	
			'Veronica opaca 2.jpg' => 'vergroot; a = vruchtkelk'
	
		),
	
		'Veronica longifolia' => array(
	
			'Veronica longifolia.jpg' => 'overzicht',
	
			'Veronica longifolia 2.jpg' => 'vergroot; a = dsn. bloem, b = vruchtkelk',
	
			'Veronica longifolia 3.jpg' => 'foto, bloeiwijzen',
	
			'Veronica longifolia 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Veronica spicata' => array(
	
			'Veronica spicata.jpg' => 'overzicht',
	
			'Veronica spicata 2.jpg' => 'foto, habitus bloeiend',
	
			'Veronica spicata 3.jpg' => 'foto, bloeiwijze',
	
			'Veronica spicata 4.jpg' => 'foto, habitus bloeiend',
	
			'Veronica spicata 5.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Veronica scutellata' => array(
	
			'Veronica scutellata.jpg' => 'overzicht',
	
			'Veronica scutellata 2.jpg' => 'vergroot; a = vruchtkelk',
	
			'Veronica scutellata 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Veronica beccabunga' => array(
	
			'Veronica beccabunga.jpg' => 'overzicht',
	
			'Veronica beccabunga 2.jpg' => 'vergroot; a = vruchtkelk',
	
			'Veronica beccabunga 3.jpg' => 'foto, habitus bloeiend',
	
			'Veronica beccabunga 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Veronica anagallis-aquatica' => array(
	
			'Veronica anagallis-aquatica.jpg' => 'overzicht',
	
			'Veronica anagallis-aquatica2.jpg' => 'vergroot; a = vruchtkelk',
	
			'Veronica anagallis-aquatica3.jpg' => 'foto, habitus bloeiend',
	
			'Veronica anagallis-aquatica4.jpg' => 'foto, habitus'
	
		),
	
		'Veronica catenata' => array(
	
			'Veronica catenata.jpg' => 'overzicht foto',
	
			'Veronica catenata 2.jpg' => 'vergroot; vruchtkelk',
	
			'Veronica catenata 3.jpg' => 'foto, habitus bloeiend',
	
			'Veronica catenata 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Veronica austriaca subsp. teucrium' => array(
	
			'Veronica austriaca teucrium.jpg' => 'overzicht',
	
			'Veronica austriaca teucrium2.jpg' => 'vergroot; a = vruchtkelk',
	
			'Veronica austriaca teucrium3.jpg' => 'foto, bloeiwijzen',
	
			'Veronica austriaca teucrium4.jpg' => 'foto, bloeiwijzen',
	
			'Veronica austriaca teucrium5.jpg' => 'foto, bloeiwijzen',
	
			'Veronica austr x prostrata 3.jpg' => 'foto, tussenvorm, zie opmerking, bloeiwijzen',
	
			'Veronica austr x prostrata 4.jpg' => 'foto, tussenvorm, zie opmerking, bloeiwijzen'
	
		),
	
		'Veronica prostrata' => array(
	
			'Veronica prostrata.jpg' => 'overzicht',
	
			'Veronica prostrata 2.jpg' => 'vergroot; a = vruchtkelk',
	
			'Veronica prostrata 3.jpg' => 'foto, habitus bloeiend',
	
			'Veronica prostrata 4.jpg' => 'foto, bloeiwijze',
	
			'Veronica prostrata 5.jpg' => 'foto, bloeiwijzen',
	
			'Veronica austr x prostrata 3.jpg' => 'foto, tussenvorm, zie opmerking, bloeiwijzen',
	
			'Veronica austr x prostrata 4.jpg' => 'foto, tussenvorm, zie opmerking, bloeiwijzen'
	
		),
	
		'Veronica montana' => array(
	
			'Veronica montana.jpg' => 'overzicht',
	
			'Veronica montana 2.jpg' => 'vergroot; a = vruchtkelk',
	
			'Veronica montana 3.jpg' => 'foto, habitus bloeiend',
	
			'Veronica montana 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Veronica chamaedrys' => array(
	
			'Veronica chamaedrys.jpg' => 'overzicht',
	
			'Veronica chamaedrys 2.jpg' => 'vergroot; a = bloem, b = vruchtkelk',
	
			'Veronica chamaedrys 3.jpg' => 'foto, habitus bloeiend',
	
			'Veronica chamaedrys 4.jpg' => 'foto, habitus bloeiend',
	
			'Veronica chamaedrys 5.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Veronica officinalis' => array(
	
			'Veronica officinalis.jpg' => 'overzicht',
	
			'Veronica officinalis 2.jpg' => 'vergroot; a = vruchtkelk',
	
			'Veronica officinalis 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Plantago arenaria' => array(
	
			'Plantago arenaria.jpg' => 'overzicht',
	
			'Plantago arenaria 2.jpg' => 'vergroot',
	
			'Plantago arenaria 3.jpg' => 'foto',
	
			'Plantago arenaria 4.jpg' => 'foto'
	
		),
	
		'Plantago coronopus' => array(
	
			'Plantago coronopus.jpg' => 'overzicht',
	
			'Plantago coronopus 2.jpg' => 'vergroot; a = bloem',
	
			'Plantago coronopus 3.jpg' => 'foto'
	
		),
	
		'Plantago maritima' => array(
	
			'Plantago maritima.jpg' => 'overzicht',
	
			'Plantago maritima 2.jpg' => 'vergroot',
	
			'Plantago maritima 3.jpg' => 'foto'
	
		),
	
		'Plantago lanceolata' => array(
	
			'Plantago lanceolata.jpg' => 'overzicht',
	
			'Plantago lanceolata 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem',
	
			'Plantago lanceolata 3.jpg' => 'foto',
	
			'Plantago lanceolata 4.jpg' => 'foto',
	
			'Plantago lanceolata 5.jpg' => 'foto'
	
		),
	
		'Plantago media' => array(
	
			'Plantago media.jpg' => 'overzicht; a = bloem',
	
			'Plantago media 2.jpg' => 'vergroot',
	
			'Plantago media 3.jpg' => 'foto'
	
		),
	
		'Plantago major' => array(
	
			'Plantago major major.jpg' => 'overzicht; a = vrucht, b =  zaad, c = blad',
	
			'Plantago major major 2.jpg' => 'vergroot, subsp. major',
	
			'Plantago major intermedia 2.jpg' => 'vergroot; a = vrucht, b = zaad, c = blad, subsp. intermedia'
	
		),
	
		'Plantago major subsp. major' => array(
	
			'Plantago major major.jpg' => 'overzicht; a = vrucht, b = zaad, c = blad',
	
			'Plantago major major 2.jpg' => 'vergroot',
	
			'Plantago major major 3.jpg' => 'foto',
	
			'Plantago major major 4.jpg' => 'foto'
	
		),
	
		'Plantago major subsp. intermedia' => array(
	
			'Plantago major intermedia.jpg' => 'overzicht; a = vrucht, b = zaad, c = blad',
	
			'Plantago major intermedia 2.jpg' => 'vergroot; a = vrucht, b = zaad, c = blad',
	
			'Plantago major intermedia 3.jpg' => 'foto'
	
		),
	
		'Littorella uniflora' => array(
	
			'Littorella uniflora.jpg' => 'overzicht',
	
			'Littorella uniflora 2.jpg' => 'vergroot; a = manlijke  bloem, b = wortelstandige vrouwelijke bloemen',
	
			'Littorella uniflora 3.jpg' => 'foto'
	
		),
	
		'Pinguicula vulgaris' => array(
	
			'Pinguicula vulgaris.jpg' => 'overzicht',
	
			'Pinguicula vulgaris 2.jpg' => 'vergroot; a = dsn. bloem',
	
			'Pinguicula vulgaris 3.jpg' => 'foto'
	
		),
	
		'Utricularia vulgaris' => array(
	
			'Utricularia vulgaris.jpg' => 'overzicht',
	
			'Utricularia vulgaris 2.jpg' => 'vergroot',
	
			'Utricularia vulgaris 3.jpg' => 'foto, bloeiwijze',
	
			'Utricularia vulg & aust.jpg' => 'verschil bloemen U. vulgaris en U. australis'
	
		),
	
		'Utricularia australis' => array(
	
			'Utricularia australis.jpg' => 'overzicht',
	
			'Utricularia australis 2.jpg' => 'vergroot',
	
			'Utricularia australis 3.jpg' => 'foto, bloem',
	
			'Utricularia australis 4.jpg' => 'foto, habitus met winterknoppen',
	
			'Utricularia vulg & aust.jpg' => 'verschil bloemen U. vulgaris en U. australis'
	
		),
	
		'Utricularia minor' => array(
	
			'Utricularia minor.jpg' => 'overzicht',
	
			'Utricularia minor 2.jpg' => 'vergroot; a = blad',
	
			'Utricularia minor 3.jpg' => 'foto, in bloei'
	
		),
	
		'Utricularia intermedia' => array(
	
			'Utricularia intermedia.jpg' => 'overzicht',
	
			'Utricularia intermedia 2.jpg' => 'vergroot; a = blad, b = bloem van opzij, c = bloem van onderen'
	
		),
	
		'Utricularia ochroleuca' => array(
	
			'Utricularia ochroleuca.jpg' => 'overzicht; a = blad, b = bloem van opzij, c = bloem van onderen',
	
			'Utricularia ochroleuca 2.jpg' => 'vergroot'
	
		),
	
		'Ilex aquifolium' => array(
	
			'Ilex aquifolium.jpg' => 'overzicht',
	
			'Ilex aquifolium 2.jpg' => 'vergroot; a = bloem, b = dsn. stamper, c = vrucht',
	
			'Ilex aquifolium 3.jpg' => 'foto, in bloei',
	
			'Ilex aquifolium 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Hedera helix' => array(
	
			'Hedera helix.jpg' => 'overzicht',
	
			'Hedera helix 2.jpg' => 'vergroot; a = bloem, b = stamper, c = dsn. vruchtbeginsel',
	
			'Hedera helix 3.jpg' => 'foto',
	
			'Hedera helix 4.jpg' => 'foto',
	
			'Hedera helix 5.jpg' => 'foto, bloeiend',
	
			'Hedera helix 6.jpg' => 'foto, bloeiend'
	
		),
	
		'Hydrocotyle vulgaris' => array(
	
			'Hydrocotyle vulgaris.jpg' => 'overzicht',
	
			'Hydrocotyle vulgaris 2.jpg' => 'vergroot; a = bloem, b = bloeiwijze, c = vrucht v/d voorzijde, d = dsn. vrucht',
	
			'Hydrocotyle vulgaris 3.jpg' => 'foto',
	
			'Hydrocotyle vulgaris a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Hydrocotyle ranunculoides' => array(
	
			'Hydrocotyle ranunculoides.jpg' => 'overzicht foto',
	
			'Hydrocotyle ranunculoides 3.jpg' => 'foto',
	
			'Hydrocotyle ranunculoides 4.jpg' => 'foto'
	
		),
	
		'Sanicula europaea' => array(
	
			'Sanicula europaea.jpg' => 'overzicht',
	
			'Sanicula europaea 2.jpg' => 'vergroot; a = tweeslachtige bloem, b = manlijke bloem, c = deelvrucht v/d rugzijde, d = deelvrucht v/d buikzijde, e = dsn. deelvrucht',
	
			'Sanicula europaea 3.jpg' => 'foto, habitus bloeiend',
	
			'Sanicula europaea a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Eryngium campestre' => array(
	
			'Eryngium campestre.jpg' => 'overzicht',
	
			'Eryngium campestre 2.jpg' => 'vergroot; a = bloem, b = vrucht v/d rugzijde, c = deelvrucht v/d buikzijde, d = dsn. deelvrucht',
	
			'Eryngium campestre 3.jpg' => 'foto',
	
			'Eryngium campestre 4.jpg' => 'foto',
	
			'Eryngium campestre 5.jpg' => 'foto',
	
			'Eryngium campestre 6.jpg' => 'foto',
	
			'Eryngium campestre a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Eryngium maritimum' => array(
	
			'Eryngium maritimum.jpg' => 'overzicht',
	
			'Eryngium maritimum 2.jpg' => 'vergroot; a = bloem, b = vrucht v/d rugzijde, c = deelvrucht v/d buikzijde, d = dsn. deelvrucht',
	
			'Eryngium maritimum 3.jpg' => 'foto',
	
			'Eryngium maritimum 4.jpg' => 'foto',
	
			'Eryngium maritimum 5.jpg' => 'foto',
	
			'Eryngium maritimum a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Chaerophyllum aureum' => array(
	
			'Chaerophyllum aureum.jpg' => 'overzicht',
	
			'Chaerophyllum aureum a.jpg' => 'vergroot, habitus met vrucht',
	
			'Chaerophyllum aureum 2.jpg' => 'foto, bloeiwijze en vruchtjes'
	
		),
	
		'Chaerophyllum temulum' => array(
	
			'Chaerophyllum temulum.jpg' => 'overzicht',
	
			'Chaerophyllum temulum 2.jpg' => 'vergroot; a = bloem, b = deelvrucht v/d rugzijde, c = deelvrucht v/d buikzijde, d = dsn. deelvrucht',
	
			'Chaerophyllum temulum 3.jpg' => 'foto',
	
			'Chaerophyllum temulum 4.jpg' => 'foto',
	
			'Chaerophyllum temulum a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Chaerophyllum bulbosum' => array(
	
			'Chaerophyllum bulbosum.jpg' => 'overzicht',
	
			'Chaerophyllum bulbosum 2.jpg' => 'vergroot',
	
			'Chaerophyllum bulbosum 3.jpg' => 'foto',
	
			'Chaerophyllum bulbosum 4.jpg' => 'foto'
	
		),
	
		'Anthriscus sylvestris' => array(
	
			'Anthriscus sylvestris.jpg' => 'overzicht',
	
			'Anthriscus sylvestris 2.jpg' => 'vergroot; a = bloem, b = deelvrucht v/d rugzijde, c = deelvrucht v/d buikzijde, d = dsn. deelvrucht',
	
			'Anthriscus sylvestris 3.jpg' => 'foto, habitus bloeiend en in vrucht',
	
			'Anthriscus sylvestris 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Anthriscus caucalis' => array(
	
			'Anthriscus caucalis.jpg' => 'overzicht',
	
			'Anthriscus caucalis 2.jpg' => 'vergroot; a = bloem, b = vrucht v/d voorzijde, c = deelvrucht v/d rugzijde, d = deelvrucht v/d buikzijde, e = dsn. deelvrucht',
	
			'Anthriscus caucalis 3.jpg' => 'foto, habitus bloeiend en in vrucht',
	
			'Anthriscus caucalis 4.jpg' => 'foto, habitus bloeiend',
	
			'Anthriscus caucalis a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Anthriscus cerefolium' => array(
	
			'Anthriscus cerefolium.jpg' => 'overzicht',
	
			'Anthriscus cerefolium 2.jpg' => 'vergroot; a = bloem',
	
			'Anthriscus cerefolium 3.jpg' => 'foto, habitus bloeiend en in vrucht',
	
			'Anthriscus cerefolium 4.jpg' => 'foto, habitus bloeiend en in vrucht',
	
			'Anthriscus sylvestris a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Scandix pecten-veneris' => array(
	
			'Scandix pecten-veneris.jpg' => 'overzicht',
	
			'Scandix pecten-veneris 2.jpg' => 'vergroot; a = bloem, b = vrucht v/d voorzijde, c = dsn. deelvrucht',
	
			'Scandix pecten-veneris 3.jpg' => 'foto, habitus bloeiend',
	
			'Scandix pecten-veneris 4.jpg' => 'foto, habitus in vrucht',
	
			'Scandix pecten-veneris a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Myrrhis odorata' => array(
	
			'Myrrhis odorata.jpg' => 'overzicht',
	
			'Myrrhis odorata 2.jpg' => 'vergroot; a = vrucht v/d voorzijde, b = dsn. vrucht',
	
			'Myrrhis odorata 3.jpg' => 'foto',
	
			'Myrrhis odorata 4.jpg' => 'foto',
	
			'Myrrhis odorata a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Coriandrum sativum' => array(
	
			'Coriandrum sativum.jpg' => 'overzicht',
	
			'Coriandrum sativum 2.jpg' => 'vergroot; a = randbloem, b = bloem, c = deelvrucht v/d rugzijde, d = deelvrucht v/d buikzijde, e = deelvrucht v/d zijkant, f = dsn. deelvrucht',
	
			'Coriandrum sativum 3.jpg' => 'foto, bloeiend',
	
			'Coriandrum sativum 4.jpg' => 'foto, bloeiend en in vrucht',
	
			'Coriandrum sativum a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Bifora radians' => array(
	
			'Bifora radians.jpg' => 'overzicht',
	
			'Bifora radians 2.jpg' => 'vergroot; a = bloem, b = vrucht v/d voorzijde'
	
		),
	
		'Smyrnium olusatrum' => array(
	
			'Smyrnium olusatrum.jpg' => 'overzicht',
	
			'Smyrnium olusatrum 2.jpg' => 'vergroot; a = bloem, b = deelvrucht v/d rugzijde, c = deelvrucht v/d buikzijde, d = dsn. deelvrucht',
	
			'Smyrnium olusatrum 3.jpg' => 'foto, bloeiwijzen met bloemen en vruchten',
	
			'Smyrnium olusatrum 4.jpg' => 'foto, bloeiwijzen met bloemen en vruchten',
	
			'Smyrnium olusatrum 5.jpg' => 'foto, bloeiwijzen',
	
			'Smyrnium olusatrum 6.jpg' => 'foto, in vrucht',
	
			'Smyrnium olusatrum a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Smyrnium perfoliatum' => array(
	
			'Smyrnium perfoliatum.jpg' => 'overzicht',
	
			'Smyrnium perfoliatum a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Bunium bulbocastanum' => array(
	
			'Bunium bulbocastanum.jpg' => 'overzicht',
	
			'Bunium bulbocastanum 2.jpg' => 'vergroot; a = bloem, b = deelvrucht v/d rugzijde, c = deelvrucht v/d buikzijde, d = dsn. deelvrucht',
	
			'Bunium bulbocastanum a.jpg' => 'tekening uit BSBI Handbook',
	
			'Bunium bulbocastanum 3.jpg' => 'foto, habitus, bloeiend',
	
			'Bunium bulbocastanum 4.jpg' => 'foto, wortelknol'
	
		),
	
		'Conopodium majus' => array(
	
			'Conopodium majus.jpg' => 'overzicht; a = vrucht van voren, b = vrucht van opzij, c = dsn. vrucht',
	
			'Conopodium majus 2.jpg' => 'vergroot',
	
			'Conopodium majus 3.jpg' => 'foto',
	
			'Conopodium majus a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Pimpinella anisum' => array(
	
			'Pimpinella anisum.jpg' => 'overzicht',
	
			'Pimpinella anisum 2.jpg' => 'vergroot; a = bloem, b = vrucht'
	
		),
	
		'Pimpinella major' => array(
	
			'Pimpinella major.jpg' => 'overzicht',
	
			'Pimpinella major 2.jpg' => 'vergroot; a = bloem, b = deelvrucht v/d rugzijde, c = deelvrucht v/d buikzijde, d = dsn. deelvrucht',
	
			'Pimpinella major 3.jpg' => 'foto',
	
			'Pimpinella major a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Pimpinella saxifraga' => array(
	
			'Pimpinella saxifraga.jpg' => 'overzicht',
	
			'Pimpinella saxifraga 2.jpg' => 'vergroot; a = deelvrucht v/d rugzijde, b = deelvrucht v/d buikzijde, c = dsn. deelvrucht',
	
			'Pimpinella saxifraga 3.jpg' => 'foto',
	
			'Pimpinella saxifraga a.jpg' => 'tekening uit BSBI Handbook',
	
			'Pimpinella saxifraga b.jpg' => "tekening uit Heukels' Flora"
	
		),
	
		'Aegopodium podagraria' => array(
	
			'Aegopodium podagraria.jpg' => 'overzicht',
	
			'Aegopodium podagraria 2.jpg' => 'vergroot; a = deelvrucht v/d rugzijde, b = deelvrucht v/d buikzijde, c = dsn. deelvrucht',
	
			'Aegopodium podagraria 3.jpg' => 'foto, bloeiend',
	
			'Aegopodium podagraria a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Sium latifolium' => array(
	
			'Sium latifolium.jpg' => 'overzicht',
	
			'Sium latifolium 2.jpg' => 'vergroot; a = deelvrucht v/d rugzijde, b = deelvrucht v/d buikzijde, c = dsn. deelvrucht',
	
			'Sium latifolium 3.jpg' => 'foto, bloeiwijzen met bloemen en vruchten',
	
			'Sium latifolium a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Berula erecta' => array(
	
			'Berula erecta.jpg' => 'overzicht',
	
			'Berula erecta 2.jpg' => 'vergroot; a = deelvrucht v/d rugzijde, b = deelvrucht v/d buikzijde, c = deelvrucht v/d zijkant, d = dsn. deelvrucht',
	
			'Berula erecta 3.jpg' => 'foto',
	
			'Berula erecta a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Crithmum maritimum' => array(
	
			'Crithmum maritimum.jpg' => 'overzicht',
	
			'Crithmum maritimum 2.jpg' => 'vergroot; a = bloem, b = vrucht v/d rugzijde,  c = dsn. deelvrucht',
	
			'Crithmum maritimum 3.jpg' => 'foto',
	
			'Crithmum maritimum a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Seseli montanum' => array(
	
			'Seseli montanum.jpg' => 'overzicht',
	
			'Seseli montanum 2.jpg' => 'vergroot'
	
		),
	
		'Oenanthe aquatica' => array(
	
			'Oenanthe aquatica.jpg' => 'overzicht',
	
			'Oenanthe aquatica 2.jpg' => 'vergroot; a = bloem, b = deelvrucht v/d rugzijde, c = deelvrucht v/d buikzijde, d = dsn. deelvrucht',
	
			'Oenanthe aquatica 3.jpg' => 'foto',
	
			'Oenanthe aquatica 4.jpg' => 'foto',
	
			'Oenanthe aquatica a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Oenanthe crocata' => array(
	
			'Oenanthe crocata.jpg' => 'overzicht foto',
	
			'Oenanthe crocata 2.jpg' => 'vergroot; a = vrucht v/d voorzijde, b = dsn. vrucht',
	
			'Oenanthe crocata 3.jpg' => 'foto',
	
			'Oenanthe crocata 4.jpg' => 'foto',
	
			'Oenanthe crocata a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Oenanthe fistulosa' => array(
	
			'Oenanthe fistulosa.jpg' => 'overzicht',
	
			'Oenanthe fistulosa 2.jpg' => 'vergroot; a = bloem, b = deelvrucht v/d rugzijde, c = deelvrucht v/d buikzijde, d = dsn. deelvrucht',
	
			'Oenanthe fistulosa 3.jpg' => 'foto',
	
			'Oenanthe fistulosa a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Oenanthe pimpinelloides' => array(
	
			'Oenanthe pimpinelloides.jpg' => 'overzicht',
	
			'Oenanthe pimpinelloides 2.jpg' => 'foto',
	
			'Oenanthe pimpinelloides a.jpg' => 'vergroot'
	
		),
	
		'Oenanthe lachenalii' => array(
	
			'Oenanthe lachenalii.jpg' => 'overzicht',
	
			'Oenanthe lachenalii 2.jpg' => 'vergroot; a = bloem, b = deelvrucht v/d rugzijde, c = deelvrucht v/d buikzijde, d = dsn. deelvrucht',
	
			'Oenanthe lachenalii 3.jpg' => 'foto',
	
			'Oenanthe lachenalii 4.jpg' => 'foto',
	
			'Oenanthe lachenalii a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Oenanthe silaifolia' => array(
	
			'Oenanthe silaifolia.jpg' => 'overzicht',
	
			'Oenanthe silaifolia 2.jpg' => 'vergroot',
	
			'Oenanthe silaifolia 3.jpg' => 'foto',
	
			'Oenanthe silaifolia 4.jpg' => 'foto',
	
			'Oenanthe silaifolia a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Oenanthe peucedanifolia' => array(
	
			'Oenanthe peucedanifolia.jpg' => 'overzicht',
	
			'Oenanthe peucedanifolia 2.jpg' => 'vergroot'
	
		),
	
		'Aethusa cynapium' => array(
	
			'Aethusa cynapium.jpg' => 'overzicht',
	
			'Aethusa cynapium 2.jpg' => 'vergroot; a = bloem, b = deelvrucht v/d rugzijde, c = deelvrucht v/d buikzijde, d = dsn. deelvrucht',
	
			'Aethusa cynapium 3.jpg' => 'foto, habitus bloeiend',
	
			'Aethusa cynapium 4.jpg' => 'foto, in vrucht',
	
			'Aethusa cynapium a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Foeniculum vulgare' => array(
	
			'Foeniculum vulgare.jpg' => 'overzicht',
	
			'Foeniculum vulgare 2.jpg' => 'vergroot; a = bloem, b = vrucht v/d voorzijde, c = dsn. deelvrucht',
	
			'Foeniculum vulgare 3.jpg' => 'foto',
	
			'Foeniculum vulgare 4.jpg' => 'foto, habitus, in bloei',
	
			'Foeniculum vulgare 5.jpg' => 'foto, bloeiwijze',
	
			'Foeniculum vulgare a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Anethum graveolens' => array(
	
			'Anethum graveolens.jpg' => 'overzicht',
	
			'Anethum graveolens 2.jpg' => 'vergroot; a = bloem, b = deelvrucht v/d rugzijde, c = deelvrucht v/d buikzijde, d = dsn. deelvrucht',
	
			'Anethum graveolens a.jpg' => 'tekening uit BSBI Handbook',
	
			'Anethum graveolens 3.jpg' => 'foto, habitus, bloeiend'
	
		),
	
		'Silaum silaus' => array(
	
			'Silaum silaus.jpg' => 'overzicht',
	
			'Silaum silaus 2.jpg' => 'vergroot; a = vrucht v/d voorzijde, b = dsn. deelvrucht',
	
			'Silaum silaus 3.jpg' => 'foto, bloeiwijzen',
	
			'Silaum silaus a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Conium maculatum' => array(
	
			'Conium maculatum.jpg' => 'overzicht',
	
			'Conium maculatum 2.jpg' => 'vergroot; a = bloem, b = deelvrucht v/d rugzijde, c = deelvrucht v/d buikzijde, d = deelvrucht v/d zijkant, e = dsn. deelvrucht',
	
			'Conium maculatum 3.jpg' => 'foto',
	
			'Conium maculatum a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Bupleurum falcatum' => array(
	
			'Bupleurum falcatum.jpg' => 'overzicht',
	
			'Bupleurum falcatum 2.jpg' => 'vergroot; a = kroonblad',
	
			'Bupleurum falcatum 3.jpg' => 'foto',
	
			'Bupleurum falcatum 4.jpg' => 'foto',
	
			'Bupleurum falcatum a.jpg' => 'foto'
	
		),
	
		'Bupleurum tenuissimum' => array(
	
			'Bupleurum tenuissimum.jpg' => 'overzicht',
	
			'Bupleurum tenuissimum 2.jpg' => 'vergroot; a = deelvrucht v/d rugzijde, b = deelvrucht v/d buikzijde, c = dsn. deelvrucht',
	
			'Bupleurum tenuissimum 3.jpg' => 'foto',
	
			'Bupleurum tenuissimum a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Bupleurum rotundifolium' => array(
	
			'Bupleurum rotundifolium.jpg' => 'overzicht',
	
			'Bupleurum rotundifolium 2.jpg' => 'vergroot',
	
			'Bupleurum rotundifolium 3.jpg' => 'foto',
	
			'Bupleurum rotundifolium a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Bupleurum subovatum' => array(
	
			'Bupleurum subovatum.jpg' => 'overzicht',
	
			'Bupleurum subovatum 2.jpg' => 'vergroot',
	
			'Bupleurum subovatum 3.jpg' => 'foto',
	
			'Bupleurum subovatum a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Cuminum cyminum' => array(
	
			'Cuminum cyminum.jpg' => 'vrucht',
	
			'Cuminum cyminum 3.jpg' => 'zaden'
	
		),
	
		'Apium graveolens' => array(
	
			'Apium graveolens.jpg' => 'overzicht',
	
			'Apium graveolens 2.jpg' => 'vergroot; a = bloem, b = deelvrucht v/d rugzijde, c = deelvrucht v/d buikzijde, d = deelvrucht v/d zijkant, e = dsn. deelvrucht',
	
			'Apium graveolens 3.jpg' => 'foto, bloeiend',
	
			'Apium graveolens 4.jpg' => 'foto, bloeiend',
	
			'Apium graveolens 5.jpg' => 'foto, knolselderij',
	
			'Apium graveolens a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Apium inundatum' => array(
	
			'Apium inundatum.jpg' => 'overzicht',
	
			'Apium inundatum 2.jpg' => 'vergroot; a = schermpje, b = vrucht v/d voorzijde',
	
			'Apium inundatum 3.jpg' => 'foto, habitus bloeiend',
	
			'Apium inundatum a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Apium repens' => array(
	
			'Apium repens.jpg' => 'overzicht',
	
			'Apium repens 2.jpg' => 'vergroot',
	
			'Apium repens 3.jpg' => 'foto, habitus bloeiend',
	
			'Apium repens 4.jpg' => 'foto, habitus bloeiend',
	
			'Apium repens 5.jpg' => 'foto, habitus bloeiend',
	
			'Apium repens 6.jpg' => 'foto, habitus bloeiend',
	
			'Apium repens a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Apium nodiflorum' => array(
	
			'Apium nodiflorum.jpg' => 'overzicht',
	
			'Apium nodiflorum 2.jpg' => 'vergroot; a = bloem, b = vrucht v/d voorzijde',
	
			'Apium nodiflorum 3.jpg' => 'foto, habitus bloeiend',
	
			'Apium nodiflorum 4.jpg' => 'foto, in bloei en vrucht',
	
			'Apium nodiflorum a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Petroselinum segetum' => array(
	
			'Petroselinum segetum.jpg' => 'overzicht foto',
	
			'Petroselinum segetum 2.jpg' => 'detail; vrucht v/d voorzijde',
	
			'Petroselinum segetum 3.jpg' => 'foto',
	
			'Petroselinum segetum 4.jpg' => 'foto',
	
			'Petroselinum segetum 5.jpg' => 'foto',
	
			'Petroselinum segetum a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Petroselinum crispum' => array(
	
			'Petroselinum crispum.jpg' => 'overzicht',
	
			'Petroselinum crispum 2.jpg' => 'vergroot; a = bloem, b = deelvrucht v/d rugzijde, c = deelvrucht v/d buikzijde, d = dsn. deelvrucht',
	
			'Petroselinum crispum a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Cicuta virosa' => array(
	
			'Cicuta virosa.jpg' => 'overzicht',
	
			'Cicuta virosa 2.jpg' => 'vergroot; a = deelvrucht v/d rugzijde, b = deelvrucht v/d buikzijde, c = dsn. deelvrucht',
	
			'Cicuta virosa 3.jpg' => 'foto',
	
			'Cicuta virosa 4.jpg' => 'foto',
	
			'Cicuta virosa a.jpg' => 'foto'
	
		),
	
		'Ammi majus' => array(
	
			'Ammi majus.jpg' => 'overzicht',
	
			'Ammi majus 2.jpg' => 'vergroot; a = vrucht v/d voorzijde',
	
			'Ammi majus 3.jpg' => 'foto, bloeiend',
	
			'Ammi majus 4.jpg' => 'foto, bloeiend',
	
			'Ammi majus 5.jpg' => 'foto, bloeiwijze',
	
			'Ammi majus a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Ammi visnaga' => array(
	
			'Ammi visnaga.jpg' => 'overzicht',
	
			'Ammi visnaga.jpg' => 'vergroot',
	
			'Ammi visnaga 3.jpg' => 'foto, bloeiend',
	
			'Ammi visnaga 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Falcaria vulgaris' => array(
	
			'Falcaria vulgaris.jpg' => 'overzicht',
	
			'Falcaria vulgaris 2.jpg' => 'vergroot; a = bloem, b= vrucht v/d voorzijde',
	
			'Falcaria vulgaris 3.jpg' => 'foto',
	
			'Falcaria vulgaris a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Carum carvi' => array(
	
			'Carum carvi.jpg' => 'overzicht',
	
			'Carum carvi 2.jpg' => 'vergroot; a = bloem, b = deelvrucht v/d rugzijde, c = deelvrucht v/d buikzijde, d = deelvrucht v/d zijkant, e = dsn. deelvrucht',
	
			'Carum carvi 3.jpg' => 'foto, habitus in bloei',
	
			'Carum carvi 4.jpg' => 'foto, bloeiwijzen',
	
			'Carum carvi 5.jpg' => 'foto, bloeiwijzen',
	
			'Carum carvi 6.jpg' => 'foto, bloeiwijzen',
	
			'Carum carvi 7.jpg' => 'foto, habitus in fruit',
	
			'Carum carvi a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Carum verticillatum' => array(
	
			'Carum verticillatum.jpg' => 'overzicht',
	
			'Carum verticillatum 2.jpg' => 'vergroot; a = vrucht v/d voorzijde',
	
			'Carum verticillatum 3.jpg' => 'foto',
	
			'Carum verticillatum 4.jpg' => 'foto',
	
			'Carum verticillatum a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Selinum carvifolia' => array(
	
			'Selinum carvifolia.jpg' => 'overzicht',
	
			'Selinum carvifolia 2.jpg' => 'vergroot; a = deelvrucht v/d rugzijde, b = deelvrucht v/d buikzijde, c = dsn. deelvrucht',
	
			'Selinum carvifolia 3.jpg' => 'foto, habitus bloeiend',
	
			'Selinum carvifolia 4.jpg' => 'foto, bloeiwijze',
	
			'Selinum carvifolia a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Angelica sylvestris' => array(
	
			'Angelica sylvestris.jpg' => 'overzicht',
	
			'Angelica sylvestris 2.jpg' => 'vergroot; a = deelvrucht v/d rugzijde, b = deelvrucht v/d buikzijde, c = dsn. deelvrucht',
	
			'Angelica sylvestris 3.jpg' => 'foto, habitus, bloeiend',
	
			'Angelica sylvestris 4.jpg' => 'foto, habitus, bloeiend',
	
			'Angelica sylvestris a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Angelica archangelica' => array(
	
			'Angelica archangelica.jpg' => 'overzicht',
	
			'Angelica archangelica 2.jpg' => 'vergroot; a = deelvrucht v/d rugzijde, b = deelvrucht v/d buikzijde, c = dsn. deelvrucht',
	
			'Angelica archangelica 3.jpg' => 'foto, habitus, bloeiend',
	
			'Angelica archangelica 4.jpg' => 'foto, bloeiend',
	
			'Angelica archangelica a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Levisticum officinale' => array(
	
			'Levisticum officinale.jpg' => 'overzicht',
	
			'Levisticum officinale 2.jpg' => 'vergroot; a = deelvrucht v/d rugzijde, b = deelvrucht v/d buikzijde, c = dsn. deelvrucht',
	
			'Levisticum officinale 3.jpg' => 'foto',
	
			'Levisticum officinale 4.jpg' => 'foto',
	
			'Levisticum officinale a.jpg' => 'foto'
	
		),
	
		'Peucedanum palustre' => array(
	
			'Peucedanum palustre.jpg' => 'overzicht',
	
			'Peucedanum palustre 2.jpg' => 'vergroot; a deelvrucht v/d rugzijde, b = deelvrucht v/d buikzijde, c = dsn. deelvrucht',
	
			'Peucedanum palustre 3.jpg' => 'foto',
	
			'Peucedanum palustre a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Peucedanum carvifolia' => array(
	
			'Peucedanum carvifolia.jpg' => 'overzicht',
	
			'Peucedanum carvifolia 2.jpg' => 'vergroot; a = bloem, b = vrucht v/d rugzijde, c = dsn. vrucht',
	
			'Peucedanum carvifolia 3.jpg' => 'foto'
	
		),
	
		'Peucedanum officinale' => array(
	
			'Peucedanum officinale.jpg' => 'overzicht',
	
			'Peucedanum officinale a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Pastinaca sativa' => array(
	
			'Pastinaca sativa.jpg' => 'overzicht',
	
			'Pastinaca sativa 2.jpg' => 'vergroot; a deelvrucht v/d rugzijde, b = deelvrucht v/d buikzijde, c = dsn. deelvrucht',
	
			'Pastinaca sativa 3.jpg' => 'foto',
	
			'Pastinaca sativa 4.jpg' => 'foto',
	
			'Pastinaca sativa a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Pastinaca sativa subsp. urens' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Pastinaca sativa subsp. sativa' => array(
	
			'Pastinaca sativa.jpg' => 'overzicht',
	
			'Pastinaca sativa 2.jpg' => 'vergroot; a deelvrucht v/d rugzijde, b = deelvrucht v/d buikzijde, c = dsn. deelvrucht'
	
		),
	
		'Heracleum sphondylium' => array(
	
			'Heracleum sphondylium.jpg' => 'overzicht',
	
			'Heracleum sphondylium 2.jpg' => 'vergroot; a = deelvrucht v/d rugzijde, b = deelvrucht v/d buikzijde, c = dsn. deelvrucht',
	
			'Heracleum sphondylium 3.jpg' => 'foto',
	
			'Heracleum sphondylium 4.jpg' => 'foto',
	
			'Heracleum sphondylium a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Heracleum mantegazzianum' => array(
	
			'Heracleum mantegazzianum.jpg' => 'overzicht',
	
			'Heracleum mantegazzianum 2.jpg' => 'vergroot; a = deelvrucht v/d rugzijde, b = deelvrucht v/d buikzijde, c = dsn. deelvrucht',
	
			'Heracleum mantegazzianum 3.jpg' => 'foto',
	
			'Heracleum mantegazzianum 4.jpg' => 'foto',
	
			'Heracleum mantegazzianum 5.jpg' => 'foto',
	
			'Heracleum mantegazzianum a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Torilis nodosa' => array(
	
			'Torilis nodosa.jpg' => 'overzicht',
	
			'Torilis nodosa 2.jpg' => 'vergroot; a = bloem, b = vrucht met de buitenste stekelige deelvrucht en de binnenste wrattige deelvrucht, c = dsn. vrucht',
	
			'Torilis nodosa 3.jpg' => 'foto, in vrucht',
	
			'Torilis nodosa a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Torilis arvensis' => array(
	
			'Torilis arvensis.jpg' => 'overzicht',
	
			'Torilis arvensis 2.jpg' => 'vergroot; a = vrucht van voren, b = vrucht van opzij, c = dsn. vrucht',
	
			'Torilis arvensis 3.jpg' => 'foto, in bloei',
	
			'Torilis arvensis 4.jpg' => 'foto, in vrucht',
	
			'Torilis arvensis a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Torilis japonica' => array(
	
			'Torilis japonica.jpg' => 'overzicht',
	
			'Torilis japonica 2.jpg' => 'vergroot; a = deelvrucht v/d rugzijde, b = deelvrucht v/d buikzijde, c = dsn. deelvrucht',
	
			'Torilis japonica 3.jpg' => 'foto, habitus in bloei',
	
			'Torilis japonica a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Orlaya grandiflora' => array(
	
			'Orlaya grandiflora.jpg' => 'overzicht',
	
			'Orlaya grandiflora 2.jpg' => 'vergroot'
	
		),
	
		'Caucalis platycarpos' => array(
	
			'Caucalis platycarpos.jpg' => 'overzicht',
	
			'Caucalis platycarpos 2.jpg' => 'vergroot; a = deelvrucht v/d rugzijde, b = deelvrucht v/d buikzijde, c = dsn. deelvrucht',
	
			'Caucalis platycarpos 3.jpg' => 'foto'
	
		),
	
		'Daucus carota' => array(
	
			'Daucus carota.jpg' => 'overzicht',
	
			'Daucus carota 2.jpg' => 'vergroot; a = bloem, b = deelvrucht v/d rugzijde, c = deelvrucht v/d buikzijde, d = dsn. deelvrucht',
	
			'Daucus carota 3.jpg' => 'foto',
	
			'Daucus carota 4.jpg' => 'foto',
	
			'Daucus carota a.jpg' => 'tekening uit BSBI Handbook'
	
		),
	
		'Viburnum opulus' => array(
	
			'Viburnum opulus.jpg' => 'overzicht; a = tweeslachtige bloem',
	
			'Viburnum opulus 2.jpg' => 'vergroot',
	
			'Viburnum opulus 3.jpg' => 'foto, in bloei',
	
			'Viburnum opulus 4.jpg' => 'foto, met vruchten',
	
			'Viburnum opulus 5.jpg' => 'foto, met vruchten'
	
		),
	
		'Viburnum lantana' => array(
	
			'Viburnum lantana.jpg' => 'overzicht',
	
			'Viburnum lantana 2.jpg' => 'vergroot',
	
			'Viburnum lantana 3.jpg' => 'foto, bloeiwijze',
	
			'Viburnum lantana 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Adoxa moschatellina' => array(
	
			'Adoxa moschatellina.jpg' => 'overzicht; a = bloeiwijze, b = dsn. bloem',
	
			'Adoxa moschatellina 2.jpg' => 'vergroot',
	
			'Adoxa moschatellina 3.jpg' => 'foto, bloeiend',
	
			'Adoxa moschatellina 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Sambucus ebulus' => array(
	
			'Sambucus ebulus.jpg' => 'overzicht',
	
			'Sambucus ebulus 2.jpg' => 'vergroot',
	
			'Sambucus ebulus 3.jpg' => 'foto, bloeiend',
	
			'Sambucus ebulus 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Sambucus racemosa' => array(
	
			'Sambucus racemosa.jpg' => 'overzicht',
	
			'Sambucus racemosa 2.jpg' => 'vergroot',
	
			'Sambucus racemosa 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Sambucus nigra' => array(
	
			'Sambucus nigra.jpg' => 'overzicht',
	
			'Sambucus nigra 2.jpg' => 'vergroot; a = bloem van onderen en van boven, b = dsn. vruchtbeginsel',
	
			'Sambucus nigra 3.jpg' => 'foto, habitus, in bloei',
	
			'Sambucus nigra 4.jpg' => 'foto, in bloei',
	
			'Sambucus nigra 5.jpg' => 'foto, vruchten',
	
			'Sambucus nigra 6.jpg' => 'foto, in bloei',
	
			"Sambucus nigra 'Laciniata' 2.jpg" => 'vergroot',
	
			"Sambucus nigra 'Laciniata' 3.jpg" => 'foto, in bloei',
	
			"Sambucus nigra 'Laciniata' 4.jpg" => 'foto, in vrucht'
	
		),
	
		'Sambucus canadensis' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Symphoricarpos albus' => array(
	
			'Symphoricarpos albus.jpg' => 'overzicht',
	
			'Symphoricarpos albus 2.jpg' => 'vergroot',
	
			'Symphoricarpos albus 3.jpg' => 'foto, bloeiwijze',
	
			'Symphoricarpos albus 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Lonicera xylosteum' => array(
	
			'Lonicera xylosteum.jpg' => 'overzicht',
	
			'Lonicera xylosteum 2.jpg' => 'vergroot; a = dsn. bloem',
	
			'Lonicera xylosteum 3.jpg' => 'foto'
	
		),
	
		'Lonicera tatarica' => array(
	
			'Lonicera tatarica.jpg' => 'overzicht',
	
			'Lonicera tatarica 2.jpg' => 'foto, bloeiend',
	
			'Lonicera tatarica 3.jpg' => 'foto, vruchten'
	
		),
	
		'Lonicera periclymenum' => array(
	
			'Lonicera periclymenum.jpg' => 'overzicht',
	
			'Lonicera periclymenum 2.jpg' => 'vergroot',
	
			'Lonicera periclymenum 3.jpg' => 'foto',
	
			'Lonicera periclymenum 4.jpg' => 'foto'
	
		),
	
		'Lonicera caprifolium' => array(
	
			'Lonicera caprifolium.jpg' => 'overzicht',
	
			'Lonicera caprifolium 2.jpg' => 'vergroot; a = dsn. bloem',
	
			'Lonicera caprifolium 3.jpg' => 'foto'
	
		),
	
		'Leycesteria formosa' => array(
	
			'Leycesteria formosa.jpg' => 'overzicht',
	
			'Leycesteria formosa 2.jpg' => 'foto, bloeiend',
	
			'Leycesteria formosa 3.jpg' => 'foto, vruchten'
	
		),
	
		'Linnaea borealis' => array(
	
			'Linnaea borealis.jpg' => 'overzicht',
	
			'Linnaea borealis 2.jpg' => 'vergroot; a = bloem',
	
			'Linnaea borealis 3.jpg' => 'foto',
	
			'Linnaea borealis 4.jpg' => 'foto'
	
		),
	
		'Valerianella locusta' => array(
	
			'Valerianella locusta.jpg' => 'overzicht; a = bloem, b = vrucht + dsn. vrucht',
	
			'Valerianella locusta 2.jpg' => 'vergroot',
	
			'Valerianella locusta 3.jpg' => 'foto, in bloei'
	
		),
	
		'Valerianella carinata' => array(
	
			'Valerianella carinata.jpg' => 'overzicht; a = vrucht + dsn. vrucht',
	
			'Valerianella carinata 2.jpg' => 'vergroot',
	
			'Valerianella carinata 3.jpg' => 'foto, bloeiwijze',
	
			'Valerianella carinata 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Valerianella dentata' => array(
	
			'Valerianella dentata.jpg' => 'overzicht; a = vrucht + dsn. vrucht',
	
			'Valerianella dentata 2.jpg' => 'vergroot',
	
			'Valerianella dentata 3.jpg' => 'foto, in bloei'
	
		),
	
		'Valerianella rimosa' => array(
	
			'Valerianella rimosa.jpg' => 'overzicht; a = vrucht + dsn. vrucht',
	
			'Valerianella rimosa 2.jpg' => 'vergroot',
	
			'Valerianella rimosa 3.jpg' => 'foto, in vrucht',
	
			'Valerianella rimosa 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Valeriana dioica' => array(
	
			'Valeriana dioica.jpg' => 'overzicht',
	
			'Valeriana dioica 2.jpg' => 'vergroot; a = bloem, b = nootje',
	
			'Valeriana dioica 3.jpg' => 'foto, in bloei',
	
			'Valeriana dioica 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Valeriana officinalis' => array(
	
			'Valeriana officinalis.jpg' => 'overzicht',
	
			'Valeriana officinalis 2.jpg' => 'vergroot; a = deel bloeiwijze, b = dsn. bloem',
	
			'Valeriana officinalis 3.jpg' => 'foto, in bloei'
	
		),
	
		'Centranthus ruber' => array(
	
			'Centranthus ruber.jpg' => 'overzicht',
	
			'Centranthus ruber 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Centranthus ruber 3.jpg' => 'foto'
	
		),
	
		'Dipsacus pilosus' => array(
	
			'Dipsacus pilosus.jpg' => 'overzicht',
	
			'Dipsacus pilosus 2.jpg' => 'vergroot; a = vrucht',
	
			'Dipsacus pilosus 3.jpg' => 'foto',
	
			'Dipsacus pilosus 4.jpg' => 'foto'
	
		),
	
		'Dipsacus fullonum' => array(
	
			'Dipsacus fullonum.jpg' => 'overzicht',
	
			'Dipsacus fullonum 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem, c = vrucht',
	
			'Dipsacus fullonum 3.jpg' => 'foto, habitus, in bloei',
	
			'Dipsacus fullonum 4.jpg' => 'foto, bloeiwijze',
	
			'Dipsacus fullonum 5.jpg' => 'foto, bloeiwijze',
	
			'Dipsacus fullonum 6.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Dipsacus laciniatus' => array(
	
			'Dipsacus laciniatus.jpg' => 'overzicht',
	
			'Dipsacus laciniatus 2.jpg' => 'foto'
	
		),
	
		'Knautia arvensis' => array(
	
			'Knautia arvensis.jpg' => 'overzicht',
	
			'Knautia arvensis 2.jpg' => 'vergroot; a = dsn. bloem, b = vrucht',
	
			'Knautia arvensis 3.jpg' => 'foto'
	
		),
	
		'Succisa pratensis' => array(
	
			'Succisa pratensis.jpg' => 'overzicht',
	
			'Succisa pratensis 2.jpg' => 'vergroot; a = dsn. bloem, b = vrucht met bijzonder omwindsel, c = idem, zonder b.o.',
	
			'Succisa pratensis 3.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Scabiosa columbaria' => array(
	
			'Scabiosa columbaria.jpg' => 'overzicht',
	
			'Scabiosa columbaria 2.jpg' => 'vergroot; a = vrucht',
	
			'Scabiosa columbaria 3.jpg' => 'foto, bloemhoofdje',
	
			'Scabiosa columbaria 4.jpg' => 'foto, vruchthoofdje'
	
		),
	
		'Campanula medium' => array(
	
			'Campanula medium.jpg' => 'overzicht',
	
			'Campanula medium 2.jpg' => 'foto'
	
		),
	
		'Campanula glomerata' => array(
	
			'Campanula glomerata.jpg' => 'overzicht',
	
			'Campanula glomerata 2.jpg' => 'vergroot',
	
			'Campanula glomerata 3.jpg' => 'foto',
	
			'Campanula glomerata 4.jpg' => 'foto'
	
		),
	
		'Campanula rotundifolia' => array(
	
			'Campanula rotundifolia.jpg' => 'overzicht',
	
			'Campanula rotundifolia 2.jpg' => 'vergroot',
	
			'Campanula rotundifolia 3.jpg' => 'foto'
	
		),
	
		'Campanula rapunculus' => array(
	
			'Campanula rapunculus.jpg' => 'overzicht',
	
			'Campanula rapunculus 2.jpg' => 'vergroot',
	
			'Campanula rapunculus 3.jpg' => 'foto',
	
			'Campanula rapunculus 4.jpg' => 'foto'
	
		),
	
		'Campanula patula' => array(
	
			'Campanula patula.jpg' => 'overzicht',
	
			'Campanula patula 2.jpg' => 'vergroot',
	
			'Campanula patula 3.jpg' => 'foto',
	
			'Campanula patula 4.jpg' => 'foto'
	
		),
	
		'Campanula carpatica' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Campanula portenschlagiana' => array(
	
			'Campanula portenschlagiana.jpg' => 'overzicht',
	
			'Campanula portenschlagiana 3.jpg' => 'foto'
	
		),
	
		'Campanula poscharskyana' => array(
	
			'Campanula poscharskyana.jpg' => 'overzicht',
	
			'Campanula poscharskyana 3.jpg' => 'foto',
	
			'Campanula poscharskyana 4.jpg' => 'foto'
	
		),
	
		'Campanula rapunculoides' => array(
	
			'Campanula rapunculoides.jpg' => 'overzicht',
	
			'Campanula rapunculoides 2.jpg' => 'vergroot',
	
			'Campanula rapunculoides 3.jpg' => 'foto'
	
		),
	
		'Campanula persicifolia' => array(
	
			'Campanula persicifolia.jpg' => 'overzicht',
	
			'Campanula persicifolia 2.jpg' => 'vergroot; a = dsn. bloem, b = dsn. vruchtbeginsel',
	
			'Campanula persicifolia 3.jpg' => 'foto'
	
		),
	
		'Campanula trachelium' => array(
	
			'Campanula trachelium.jpg' => 'overzicht',
	
			'Campanula trachelium 2.jpg' => 'vergroot',
	
			'Campanula trachelium 3.jpg' => 'foto',
	
			'Campanula trachelium 4.jpg' => 'foto'
	
		),
	
		'Campanula latifolia' => array(
	
			'Campanula latifolia.jpg' => 'overzicht',
	
			'Campanula latifolia 2.jpg' => 'vergroot',
	
			'Campanula latifolia 3.jpg' => 'foto',
	
			'Campanula latifolia 4.jpg' => 'foto'
	
		),
	
		'Legousia speculum-veneris' => array(
	
			'Legousia speculum-veneris.jpg' => 'overzicht',
	
			'Legousia speculum-veneris 2.jpg' => 'vergroot',
	
			'Legousia speculum-veneris 3.jpg' => 'foto',
	
			'Legousia speculum-veneris 4.jpg' => 'foto',
	
			'Legousia speculum-veneris 5.jpg' => 'foto, bloeiend'
	
		),
	
		'Legousia hybrida' => array(
	
			'Legousia hybrida.jpg' => 'overzicht',
	
			'Legousia hybrida 2.jpg' => 'vergroot',
	
			'Legousia hybrida 3.jpg' => 'foto',
	
			'Legousia hybrida 4.jpg' => 'foto'
	
		),
	
		'Phyteuma spicatum' => array(
	
			'Phyteuma spicatum spicatum.jpg' => 'overzicht',
	
			'Phyteuma spicatum spicatum 2.jpg' => 'vergroot; a = bloem, subsp. spicatum',
	
			'Phyteuma spicatum nigrum 2.jpg' => 'vergroot, subsp. nigrum'
	
		),
	
		'Phyteuma spicatum subsp. nigrum' => array(
	
			'Phyteuma spicatum nigrum.jpg' => 'overzicht',
	
			'Phyteuma spicatum nigrum 2.jpg' => 'vergroot',
	
			'Phyteuma spicatum nigrum 3.jpg' => 'foto',
	
			'Phyteuma spicatum nigrum 4.jpg' => 'foto'
	
		),
	
		'Phyteuma spicatum subsp. spicatum' => array(
	
			'Phyteuma spicatum spicatum.jpg' => 'overzicht',
	
			'Phyteuma spicatum spicatum 2.jpg' => 'vergroot; a = bloem',
	
			'Phyteuma spicatum spicatum 3.jpg' => 'foto',
	
			'Phyteuma spicatum spicatum 4.jpg' => 'foto',
	
			'Phyteuma spicatum spicatum 5.jpg' => 'foto',
	
			'Phyteuma spicatum spicatum 6.jpg' => 'foto'
	
		),
	
		'Wahlenbergia hederacea' => array(
	
			'Wahlenbergia hederacea.jpg' => 'overzicht',
	
			'Wahlenbergia hederacea 2.jpg' => 'vergroot; a = vrucht',
	
			'Wahlenbergia hederacea 3.jpg' => 'foto, bloeiend',
	
			'Wahlenbergia hederacea 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Jasione montana' => array(
	
			'Jasione montana.jpg' => 'overzicht',
	
			'Jasione montana 2.jpg' => 'vergroot; a = bloem, b = vruchtkelk',
	
			'Jasione montana 3.jpg' => 'foto'
	
		),
	
		'Trachelium caeruleum' => array(
	
			'Trachelium caeruleum.jpg' => 'overzicht',
	
			'Trachelium caeruleum 2.jpg' => 'vergroot',
	
			'Trachelium caeruleum 3.jpg' => 'foto, habitus, bloeiend'
	
		),
	
		'Lobelia dortmanna' => array(
	
			'Lobelia dortmanna.jpg' => 'overzicht',
	
			'Lobelia dortmanna 2.jpg' => 'vergroot; a = bloem, b = vruchtkelk',
	
			'Lobelia dortmanna 3.jpg' => 'foto',
	
			'Lobelia dortmanna 4.jpg' => 'foto'
	
		),
	
		'Lobelia inflata' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Menyanthes trifoliata' => array(
	
			'Menyanthes trifoliata.jpg' => 'overzicht; a = dsn. bloem, b = vrucht',
	
			'Menyanthes trifoliata 2.jpg' => 'vergroot',
	
			'Menyanthes trifoliata 3.jpg' => 'foto',
	
			'Menyanthes trifoliata 4.jpg' => 'foto'
	
		),
	
		'Nymphoides peltata' => array(
	
			'Nymphoides peltata.jpg' => 'overzicht',
	
			'Nymphoides peltata 2.jpg' => 'vergroot',
	
			'Nymphoides peltata 3.jpg' => 'foto'
	
		),
	
		'Eupatorium cannabinum' => array(
	
			'Eupatorium cannabinum.jpg' => 'overzicht',
	
			'Eupatorium cannabinum 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Eupatorium cannabinum 3.jpg' => 'foto',
	
			'Eupatorium cannabinum 4.jpg' => 'foto',
	
			'Eupatorium cannabinum 5.jpg' => 'foto',
	
			'Eupatorium cannabinum 6.jpg' => 'foto',
	
			'Eupatorium cannabinum.mov' => 'zicht rondom bloeiwijze: klik op foto en beweeg naar links of rechts'
	
		),
	
		'Solidago virgaurea' => array(
	
			'Solidago virgaurea.jpg' => 'overzicht',
	
			'Solidago virgaurea 2.jpg' => 'vergroot; a = hoofdje, b = dsn. hoofdje',
	
			'Solidago virgaurea 3.jpg' => 'foto, habitus in bloei'
	
		),
	
		'Solidago gigantea' => array(
	
			'Solidago gigantea.jpg' => 'overzicht foto',
	
			'Solidago gigantea 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Solidago canadensis' => array(
	
			'Solidago canadensis.jpg' => 'overzicht; a = hoofdje',
	
			'Solidago canadensis 2.jpg' => 'vergroot',
	
			'Solidago canadensis 3.jpg' => 'foto, habitus in bloei',
	
			'Solidago canadensis 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Bellis perennis' => array(
	
			'Bellis perennis.jpg' => 'overzicht',
	
			'Bellis perennis 2.jpg' => 'vergroot; a = dsn. hoofdje, b = dsn. vrucht',
	
			'Bellis perennis 3.jpg' => 'foto, habitus, bloeiend',
	
			'Bellis perennis 4.jpg' => 'foto, habitus, bloeiend',
	
			'Bellis perennis 5.jpg' => 'foto, habitus, bloeiend'
	
		),
	
		'Aster tripolium' => array(
	
			'Aster tripolium.jpg' => 'overzicht',
	
			'Aster tripolium 2.jpg' => 'vergroot',
	
			'Aster tripolium 3.jpg' => 'foto',
	
			'Aster tripolium 4.jpg' => 'foto',
	
			'Aster tripolium discoidea 3.jpg' => 'foto'
	
		),
	
		'Aster lanceolatus' => array(
	
			'Aster lanceolatus.jpg' => 'overzicht',
	
			'Aster lanceolatus 2.jpg' => 'vergroot',
	
			'Aster lanceolatus 3.jpg' => 'foto',
	
			'Aster lanceolatus 4.jpg' => 'foto'
	
		),
	
		'Aster laevis' => array(
	
			'Aster laevis.jpg' => 'overzicht foto',
	
			'Aster laevis 3.jpg' => 'foto',
	
			'Aster laevis 4.jpg' => 'foto'
	
		),
	
		'Aster novi-belgii' => array(
	
			'Aster novi-belgii.jpg' => 'overzicht foto',
	
			'Aster novi-belgii 3.jpg' => 'foto'
	
		),
	
		'Conyza canadensis' => array(
	
			'Conyza canadensis.jpg' => 'overzicht',
	
			'Conyza canadensis 2.jpg' => 'vergroot',
	
			'Conyza canadensis 3.jpg' => 'foto'
	
		),
	
		'Conyza sumatrensis' => array(
	
			'Conyza sumatrensis.jpg' => 'overzicht',
	
			'Conyza sumatrensis 3.jpg' => 'foto',
	
			'Conyza sumatrensis 4.jpg' => 'foto',
	
			'Conyza sumatrensis 5.jpg' => 'foto',
	
			'Conyza sumatrensis 6.jpg' => 'foto'
	
		),
	
		'Conyza bonariensis' => array(
	
			'Conyza bonariensis.jpg' => 'overzicht',
	
			'Conyza bonariensis 3.jpg' => 'foto'
	
		),
	
		'Erigeron acer' => array(
	
			'Erigeron acer.jpg' => 'overzicht',
	
			'Erigeron acer 2.jpg' => 'vergroot; a = dsn. hoofdje, b = lintbloem, c = buisbloem, d = vrucht',
	
			'Erigeron acer 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Erigeron karvinskianus' => array(
	
			'Erigeron karvinskianus.jpg' => 'overzicht foto',
	
			'Erigeron karvinskianus 3.jpg' => 'foto, habitus bloeiend',
	
			'Erigeron karvinskianus 4.jpg' => 'foto, bloemhoofjdes'
	
		),
	
		'Erigeron annuus' => array(
	
			'Erigeron annuus.jpg' => 'overzicht',
	
			'Erigeron annuus 2.jpg' => 'vergroot',
	
			'Erigeron annuus 3.jpg' => 'foto, bloeiwijze',
	
			'Erigeron annuus 4.jpg' => 'foto, bloeiwijzen',
	
			'Erigeron annuus 5.jpg' => 'foto, bloeiwijzen',
	
			'Erigeron annuus 6.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Baccharis halimifolia' => array(
	
			'Baccharis halimifolia.jpg' => 'blad'
	
		),
	
		'Filago minima' => array(
	
			'Filago minima.jpg' => 'overzicht',
	
			'Filago minima 2.jpg' => 'vergroot',
	
			'Filago minima 3.jpg' => 'foto'
	
		),
	
		'Filago arvensis' => array(
	
			'Filago arvensis.jpg' => 'overzicht',
	
			'Filago arvensis 2.jpg' => 'vergroot',
	
			'Filago arvensis 3.jpg' => 'foto'
	
		),
	
		'Filago vulgaris' => array(
	
			'Filago vulgaris.jpg' => 'overzicht; a = hoofdje, b = vrouwelijke bloem',
	
			'Filago vulgaris 2.jpg' => 'vergroot',
	
			'Filago vulgaris 3.jpg' => 'foto',
	
			'Filago vulgaris 4.jpg' => 'foto',
	
			'Filago vulgaris 5.jpg' => 'foto'
	
		),
	
		'Filago lutescens' => array(
	
			'Filago lutescens.jpg' => 'overzicht; a = omwindselblad',
	
			'Filago lutescens 2.jpg' => 'vergroot'
	
		),
	
		'Filago pyramidata' => array(
	
			'Filago pyramidata.jpg' => 'overzicht',
	
			'Filago pyramidata 2.jpg' => 'vergroot'
	
		),
	
		'Gnaphalium sylvaticum' => array(
	
			'Gnaphalium sylvaticum.jpg' => 'overzicht',
	
			'Gnaphalium sylvaticum 2.jpg' => 'vergroot; a = hoofdje, b = dsn. hoofdje, c = vrouwelijke bloem, d = buisbloem',
	
			'Gnaphalium sylvaticum 3.jpg' => 'foto'
	
		),
	
		'Gnaphalium uliginosum' => array(
	
			'Gnaphalium uliginosum.jpg' => 'overzicht',
	
			'Gnaphalium uliginosum 2.jpg' => 'vergroot',
	
			'Gnaphalium uliginosum 3.jpg' => 'foto'
	
		),
	
		'Gnaphalium luteo-album' => array(
	
			'Gnaphalium luteo-album.jpg' => 'overzicht',
	
			'Gnaphalium luteo-album 2.jpg' => 'vergroot',
	
			'Gnaphalium luteo-album 3.jpg' => 'foto',
	
			'Gnaphalium luteo-album 4.jpg' => 'foto'
	
		),
	
		'Helichrysum arenarium' => array(
	
			'Helichrysum arenarium.jpg' => 'overzicht',
	
			'Helichrysum arenarium 2.jpg' => 'vergroot',
	
			'Helichrysum arenarium 3.jpg' => 'foto'
	
		),
	
		'Antennaria dioica' => array(
	
			'Antennaria dioica.jpg' => 'overzicht',
	
			'Antennaria dioica 2.jpg' => 'vergroot',
	
			'Antennaria dioica 3.jpg' => 'foto, bloeiend',
	
			'Antennaria dioica 4.jpg' => 'foto, vrouwelijke bloemen',
	
			'Antennaria dioica 5.jpg' => 'foto,mannelijke bloemen',
	
			'Antennaria dioica 6.jpg' => 'foto,mannelijke bloemen'
	
		),
	
		'Anaphalis margaritacea' => array(
	
			'Anaphalis margaritacea.jpg' => 'overzicht',
	
			'Anaphalis margaritacea 2.jpg' => 'vergroot',
	
			'Anaphalis margaritacea 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Inula conyzae' => array(
	
			'Inula conyzae.jpg' => 'overzicht',
	
			'Inula conyzae 2.jpg' => 'vergroot',
	
			'Inula conyzae 3.jpg' => 'foto',
	
			'Inula conyzae 4.jpg' => 'foto'
	
		),
	
		'Inula helenium' => array(
	
			'Inula helenium.jpg' => 'overzicht',
	
			'Inula helenium 2.jpg' => 'vergroot',
	
			'Inula helenium 3.jpg' => 'foto',
	
			'Inula helenium 4.jpg' => 'foto'
	
		),
	
		'Inula britannica' => array(
	
			'Inula britannica.jpg' => 'overzicht',
	
			'Inula britannica 2.jpg' => 'vergroot',
	
			'Inula britannica 3.jpg' => 'foto',
	
			'Inula britannica 4.jpg' => 'foto'
	
		),
	
		'Inula salicina' => array(
	
			'Inula salicina.jpg' => 'overzicht',
	
			'Inula salicina 2.jpg' => 'vergroot',
	
			'Inula salicina 3.jpg' => 'foto'
	
		),
	
		'Dittrichia graveolens' => array(
	
			'Dittrichia graveolens.jpg' => 'overzicht',
	
			'Dittrichia graveolens 2.jpg' => 'vergroot'
	
		),
	
		'Dittrichia viscosa' => array(
	
			'Dittrichia viscosa.jpg' => 'overzicht',
	
			'Dittrichia viscosa 2.jpg' => 'foto, bloeiend'
	
		),
	
		'Pulicaria vulgaris' => array(
	
			'Pulicaria vulgaris.jpg' => 'overzicht',
	
			'Pulicaria vulgaris 2.jpg' => 'vergroot',
	
			'Pulicaria vulgaris 3.jpg' => 'foto'
	
		),
	
		'Pulicaria dysenterica' => array(
	
			'Pulicaria dysenterica.jpg' => 'overzicht',
	
			'Pulicaria dysenterica 2.jpg' => 'vergroot;  a = lintbloem, b = buisbloem, c = vrucht',
	
			'Pulicaria dysenterica 3.jpg' => 'foto',
	
			'Pulicaria dysenterica 4.jpg' => 'foto'
	
		),
	
		'Guizotia abyssinica' => array(
	
			'Guizotia abyssinica.jpg' => 'overzicht foto',
	
			'Guizotia abyssinica 3.jpg' => 'foto',
	
			'Guizotia abyssinica 4.jpg' => 'foto'
	
		),
	
		'Bidens frondosa' => array(
	
			'Bidens frondosa.jpg' => 'overzicht foto',
	
			'Bidens frondosa 2.jpg' => 'detail; vrucht',
	
			'Bidens frondosa 3.jpg' => 'foto'
	
		),
	
		'Bidens radiata' => array(
	
			'Bidens radiata.jpg' => 'overzicht foto',
	
			'Bidens radiata 3.jpg' => 'foto'
	
		),
	
		'Bidens tripartita' => array(
	
			'Bidens tripartita.jpg' => 'overzicht; a = vrucht',
	
			'Bidens tripartita 2.jpg' => 'vergroot',
	
			'Bidens tripartita 3.jpg' => 'foto'
	
		),
	
		'Bidens cernua' => array(
	
			'Bidens cernua.jpg' => 'overzicht; a = vrucht',
	
			'Bidens cernua 2.jpg' => 'vergroot',
	
			'Bidens cernua 3.jpg' => 'foto',
	
			'Bidens cernua 4.jpg' => 'foto'
	
		),
	
		'Bidens connata' => array(
	
			'Bidens connata.jpg' => 'overzicht foto',
	
			'Bidens connata 2.jpg' => 'detail; vrucht',
	
			'Bidens connata 3.jpg' => 'foto'
	
		),
	
		'Cosmos bipinnatus' => array(
	
			'Cosmos bipinnatus.jpg' => 'overzicht foto',
	
			'Cosmos bipinnatus 3.jpg' => 'foto'
	
		),
	
		'Rudbeckia laciniata' => array(
	
			'Rudbeckia laciniata.jpg' => 'overzicht',
	
			'Rudbeckia laciniata 2.jpg' => 'vergroot'
	
		),
	
		'Rudbeckia hirta' => array(
	
			'Rudbeckia hirta.jpg' => 'overzicht foto',
	
			'Rudbeckia hirta 3.jpg' => 'foto, in bloei'
	
		),
	
		'Helianthus annuus' => array(
	
			'Helianthus annuus.jpg' => 'overzicht',
	
			'Helianthus annuus 2.jpg' => 'vergroot; a = buisbloem + stroschub, b = vrucht',
	
			'Helianthus annuus 3.jpg' => 'foto, bloeiend',
	
			'Helianthus annuus 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Helianthus tuberosus' => array(
	
			'Helianthus tuberosus.jpg' => 'overzicht',
	
			'Helianthus tuberosus 2.jpg' => 'vergroot',
	
			'Helianthus tuberosus 3.jpg' => 'foto'
	
		),
	
		'Helianthus laetiflorus(x)' => array(
	
			'Helianthus laetiflorus(x).jpg' => 'overzicht',
	
			'Helianthus laetiflorus(x) 2.jpg' => 'vergroot',
	
			'Helianthus laetiflorus(x) 3.jpg' => 'foto'
	
		),
	
		'Iva xanthifolia' => array(
	
			'Iva xanthifolia.jpg' => 'overzicht',
	
			'Iva xanthifolia 2.jpg' => 'vergroot',
	
			'Iva xanthifolia 3.jpg' => 'foto',
	
			'Iva xanthifolia 4.jpg' => 'foto',
	
			'Iva xanthifolia 5.jpg' => 'foto'
	
		),
	
		'Ambrosia trifida' => array(
	
			'Ambrosia trifida.jpg' => 'overzicht foto',
	
			'Ambrosia trifida 3.jpg' => 'foto, habitus'
	
		),
	
		'Ambrosia artemisiifolia' => array(
	
			'Ambrosia artemisiifolia.jpg' => 'overzicht; a = bloeiwijze, b = bloem',
	
			'Ambrosia artemisiifolia 2.jpg' => 'vergroot',
	
			'Ambrosia artemisiifolia 3.jpg' => 'foto, habitus bloeiend',
	
			'Ambrosia artemisiifolia 4.jpg' => 'foto, bloeiend',
	
			'Ambrosia artemisiifolia 5.jpg' => 'foto, bloeiend'
	
		),
	
		'Ambrosia psilostachya' => array(
	
			'Ambrosia psilostachya.jpg' => 'overzicht',
	
			'Ambrosia psilostachya 2.jpg' => 'vergroot',
	
			'Ambrosia psilostachya 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Xanthium spinosum' => array(
	
			'Xanthium spinosum.jpg' => 'overzicht',
	
			'Xanthium spinosum 2.jpg' => 'vergroot',
	
			'Xanthium spinosum 3.jpg' => 'foto, in vrucht',
	
			'Xanthium spinosum 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Xanthium strumarium' => array(
	
			'Xanthium strumarium.jpg' => 'overzicht',
	
			'Xanthium strumarium 2.jpg' => 'vergroot; a = manlijke  bloem, b = dsn. vruchthoofdje',
	
			'Xanthium strumarium 3.jpg' => 'foto, in bloei',
	
			'Xanthium strumarium 4.jpg' => 'foto, in bloei'
	
		),
	
		'Galinsoga parviflora' => array(
	
			'Galinsoga parviflora.jpg' => 'overzicht',
	
			'Galinsoga parviflora 2.jpg' => 'vergroot',
	
			'Galinsoga parviflora 3.jpg' => 'foto',
	
			'Galinsoga parviflora 4.jpg' => 'foto'
	
		),
	
		'Galinsoga quadriradiata' => array(
	
			'Galinsoga quadriradiata.jpg' => 'overzicht foto',
	
			'Galinsoga quadriradiata 3.jpg' => 'foto',
	
			'Galinsoga quadriradiata 4.jpg' => 'foto'
	
		),
	
		'Tagetes minuta' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Anthemis tinctoria' => array(
	
			'Anthemis tinctoria.jpg' => 'overzicht',
	
			'Anthemis tinctoria 2.jpg' => 'vergroot',
	
			'Anthemis tinctoria 3.jpg' => 'foto, bloemhoofdje',
	
			'Anthemis tinctoria 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Anthemis cotula' => array(
	
			'Anthemis cotula.jpg' => 'overzicht',
	
			'Anthemis cotula 2.jpg' => 'vergroot; a = vrucht + stroschub',
	
			'Anthemis cotula 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Anthemis arvensis' => array(
	
			'Anthemis arvensis.jpg' => 'overzicht',
	
			'Anthemis arvensis 2.jpg' => 'vergroot; a = vrucht + stroschub',
	
			'Anthemis arvensis 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Achillea ptarmica' => array(
	
			'Achillea ptarmica.jpg' => 'overzicht',
	
			'Achillea ptarmica 2.jpg' => 'vergroot; a = dsn. hoofdje',
	
			'Achillea ptarmica 3.jpg' => 'foto, bloeiwijze',
	
			'Achillea ptarmica 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Achillea millefolium' => array(
	
			'Achillea millefolium.jpg' => 'overzicht',
	
			'Achillea millefolium 2.jpg' => 'vergroot; a = hoofdje, b = lintbloem, c = buisbloem, d = vrucht',
	
			'Achillea millefolium 3.jpg' => 'foto, habitus bloeiend',
	
			'Achillea millefolium 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Matricaria chamomilla' => array(
	
			'Matricaria chamomilla.jpg' => 'overzicht',
	
			'Matricaria chamomilla 2.jpg' => 'vergroot; a = dsn. hoofdje, b = dsn. buisbloem, c = vrucht',
	
			'Matricaria chamomilla 3.jpg' => 'foto'
	
		),
	
		'Matricaria discoidea' => array(
	
			'Matricaria discoidea.jpg' => 'overzicht',
	
			'Matricaria discoidea 2.jpg' => 'vergroot',
	
			'Matricaria discoidea 3.jpg' => 'foto'
	
		),
	
		'Tripleurospermum maritimum' => array(
	
			'Tripleurospermum maritimum.jpg' => 'overzicht',
	
			'Tripleurospermum maritimum 2.jpg' => 'vergroot',
	
			'Tripleurospermum maritimum 3.jpg' => 'foto, habitus in bloei'
	
		),
	
		'Glebionis segetum' => array(
	
			'Glebionis segetum.jpg' => 'overzicht',
	
			'Glebionis segetum 2.jpg' => 'vergroot',
	
			'Glebionis segetum 3.jpg' => 'foto, bloeiend',
	
			'Glebionis segetum 4.jpg' => 'foto, hoofdje',
	
			'Glebionis segetum a.jpg' => 'foto, nootjes van lint- en buisbloem'
	
		),
	
		'Glebionis coronaria' => array(
	
			'Glebionis coronaria.jpg' => 'overzicht',
	
			'Glebionis coronaria 3.jpg' => 'foto, bloeiend',
	
			'Glebionis coronaria a.jpg' => 'foto, nootjes van lint- en buisbloem'
	
		),
	
		'Tanacetum vulgare' => array(
	
			'Tanacetum vulgare.jpg' => 'overzicht',
	
			'Tanacetum vulgare 2.jpg' => 'vergroot; a = dsn. hoofdje, b = vrucht',
	
			'Tanacetum vulgare 3.jpg' => 'foto, bloeiwijze',
	
			'Tanacetum vulgare.mov' => 'zicht rondom bloeiwijze: klik op foto en beweeg naar links of rechts'
	
		),
	
		'Tanacetum parthenium' => array(
	
			'Tanacetum parthenium.jpg' => 'overzicht',
	
			'Tanacetum parthenium 2.jpg' => 'vergroot',
	
			'Tanacetum parthenium 3.jpg' => 'foto, habitus, bloeiend'
	
		),
	
		'Leucanthemum vulgare' => array(
	
			'Leucanthemum vulgare.jpg' => 'overzicht',
	
			'Leucanthemum vulgare 2.jpg' => 'vergroot; a = jong hoofdje, b = dsn. buisbloem, c = vrucht',
	
			'Leucanthemum vulgare 3.jpg' => 'foto'
	
		),
	
		'Leucanthemum paludosum' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Cotula coronopifolia' => array(
	
			'Cotula coronopifolia.jpg' => 'overzicht',
	
			'Cotula coronopifolia 2.jpg' => 'vergroot; a = randbloem, b = buisbloem, c = vrucht',
	
			'Cotula coronopifolia 3.jpg' => 'foto'
	
		),
	
		'Artemisia maritima' => array(
	
			'Artemisia maritima.jpg' => 'overzicht',
	
			'Artemisia maritima 2.jpg' => 'vergroot',
	
			'Artemisia maritima 3.jpg' => 'foto, habitus'
	
		),
	
		'Artemisia dracunculus' => array(
	
			'Artemisia dracunculus.jpg' => 'overzicht',
	
			'Artemisia dracunculus 2.jpg' => 'vergroot'
	
		),
	
		'Artemisia biennis' => array(
	
			'Artemisia biennis.jpg' => 'overzicht foto',
	
			'Artemisia biennis 3.jpg' => 'foto, bloeiend',
	
			'Artemisia biennis 4.jpg' => 'foto, detail bloeiwijze'
	
		),
	
		'Artemisia campestris' => array(
	
			'Artemisia campestris camp.jpg' => 'overzicht',
	
			'Artemisia campestris camp 2.jpg' => 'vergroot'
	
		),
	
		'Artemisia campestris subsp. maritima' => array(
	
			'Artemisia campestris mar.jpg' => 'overzicht',
	
			'Artemisia campestris mar 3.jpg' => 'foto, bloeiwijze',
	
			'Artemisia campestris mar 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Artemisia campestris subsp. campestris' => array(
	
			'Artemisia campestris camp.jpg' => 'overzicht',
	
			'Artemisia campestris camp 2.jpg' => 'vergroot'
	
		),
	
		'Artemisia absinthium' => array(
	
			'Artemisia absinthium.jpg' => 'overzicht',
	
			'Artemisia absinthium 2.jpg' => 'vergroot; a = hoofdje, b = randbloem, c = geopende buisbloem',
	
			'Artemisia absinthium 3.jpg' => 'foto, bloeiend',
	
			'Artemisia absinthium 4.jpg' => 'foto, bloeiend',
	
			'Artemisia absinthium 5.jpg' => 'foto, bloeiend'
	
		),
	
		'Artemisia verlotiorum' => array(
	
			'Artemisia verlotiorum.jpg' => 'overzicht',
	
			'Artemisia verlotiorum 2.jpg' => 'foto, habitus',
	
			'Artemisia verlotiorum 3.jpg' => 'foto, habitus, bloeiend',
	
			'Artemisia verlotiorum 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Artemisia vulgaris' => array(
	
			'Artemisia vulgaris.jpg' => 'overzicht',
	
			'Artemisia vulgaris 2.jpg' => 'vergroot; a = hoofdje, b = dsn. hoofdje',
	
			'Artemisia vulgaris 3.jpg' => 'foto, habitus, bloeiend',
	
			'Artemisia vulgaris 4.jpg' => 'foto, bloeiwijze',
	
			'Artemisia vulgaris 5.jpg' => 'foto, detail bloeiwijze'
	
		),
	
		'Tussilago farfara' => array(
	
			'Tussilago farfara.jpg' => 'overzicht',
	
			'Tussilago farfara 2.jpg' => 'vergroot; a = lintbloem, b = dsn. buisbloem, c = vrucht',
	
			'Tussilago farfara 3.jpg' => 'foto, habitus bloeiend',
	
			'Tussilago farfara 4.jpg' => 'foto, bloeiwijzen',
	
			'Tussilago farfara 5.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Petasites japonicus' => array(
	
			'Petasites japonicus.jpg' => 'overzicht foto',
	
			'Petasites japonicus 3.jpg' => 'foto'
	
		),
	
		'Petasites hybridus' => array(
	
			'Petasites hybridus.jpg' => 'overzicht',
	
			'Petasites hybridus 2.jpg' => 'vergroot',
	
			'Petasites hybridus 3.jpg' => 'foto',
	
			'Petasites hybridus 4.jpg' => 'foto manlijke plant',
	
			'Petasites hybridus 10.jpg' => 'foto, vrouwelijke bloeiwijze',
	
			'Petasites hybridus 5.jpg' => 'foto vrouwelijke plant',
	
			'Petasites hybridus 6.jpg' => 'foto ',
	
			'Petasites hybridus 7.jpg' => 'foto, mannelijke bloemen',
	
			'Petasites hybridus 8.jpg' => 'foto, mannelijke bloemen',
	
			'Petasites hybridus 9.jpg' => 'foto, habitus vegetatief'
	
		),
	
		'Petasites albus' => array(
	
			'Petasites albus.jpg' => 'overzicht',
	
			'Petasites albus 2.jpg' => 'vergroot',
	
			'Petasites albus 3.jpg' => 'foto',
	
			'Petasites albus 4.jpg' => 'foto'
	
		),
	
		'Arnica montana' => array(
	
			'Arnica montana.jpg' => 'overzicht',
	
			'Arnica montana 2.jpg' => 'vergroot; a = dsn. hoofdje',
	
			'Arnica montana 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Doronicum pardalianches' => array(
	
			'Doronicum pardalianches.jpg' => 'overzicht',
	
			'Doronicum pardalianches 2.jpg' => 'vergroot',
	
			'Doronicum pardalianches 3.jpg' => 'foto',
	
			'Doronicum pardalianches 4.jpg' => 'foto'
	
		),
	
		'Doronicum plantagineum' => array(
	
			'Doronicum plantagineum.jpg' => 'overzicht',
	
			'Doronicum plantagineum 2.jpg' => 'vergroot'
	
		),
	
		'Tephroseris palustris' => array(
	
			'Tephroseris palustris.jpg' => 'overzicht',
	
			'Tephroseris palustris 2.jpg' => 'vergroot',
	
			'Tephroseris palustris 3.jpg' => 'foto, habitus'
	
		),
	
		'Senecio inaequidens' => array(
	
			'Senecio inaequidens.jpg' => 'overzicht foto',
	
			'Senecio inaequidens 3.jpg' => 'foto, bloeiend',
	
			'Senecio inaequidens 4.jpg' => 'foto, bloeiend',
	
			'Senecio inaequidens 5.jpg' => 'foto, bloeiend',
	
			'Senecio inaequidens 6.jpg' => 'foto, bloeiend'
	
		),
	
		'Senecio sarracenicus' => array(
	
			'Senecio sarracenicus.jpg' => 'overzicht',
	
			'Senecio sarracenicus 2.jpg' => 'vergroot',
	
			'Senecio sarracenicus 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Senecio nemorensis' => array(
	
			'Senecio nemorensis.jpg' => 'overzicht',
	
			'Senecio nemorensis 2.jpg' => 'vergroot',
	
			'Senecio nemorensis 3.jpg' => 'foto, bloeiend',
	
			'Senecio nemorensis 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Senecio squalidus' => array(
	
			'Senecio squalidus.jpg' => 'overzicht foto',
	
			'Senecio squalidus 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Senecio vernalis' => array(
	
			'Senecio vernalis.jpg' => 'overzicht',
	
			'Senecio vernalis 2.jpg' => 'vergroot',
	
			'Senecio vernalis 3.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Senecio vulgaris' => array(
	
			'Senecio vulgaris.jpg' => 'overzicht',
	
			'Senecio vulgaris 2.jpg' => 'vergroot; a = hoofdje, b = dsn. hoofdje, c = vrucht',
	
			'Senecio vulgaris 3.jpg' => 'foto, habitus bloeiend',
	
			'Senecio vulgaris 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Senecio viscosus' => array(
	
			'Senecio viscosus.jpg' => 'overzicht',
	
			'Senecio viscosus 2.jpg' => 'vergroot',
	
			'Senecio viscosus 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Senecio sylvaticus' => array(
	
			'Senecio sylvaticus.jpg' => 'overzicht',
	
			'Senecio sylvaticus 2.jpg' => 'vergroot',
	
			'Senecio sylvaticus 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Jacobaea paludosa' => array(
	
			'Jacobaea paludosa.jpg' => 'overzicht',
	
			'Jacobaea paludosa 2.jpg' => 'vergroot',
	
			'Jacobaea paludosa 3.jpg' => 'foto'
	
		),
	
		'Jacobaea maritima' => array(
	
			'Jacobaea maritima.jpg' => 'overzicht',
	
			'Jacobaea maritima 2.jpg' => 'foto, bloeiend'
	
		),
	
		'Jacobaea erucifolia' => array(
	
			'Jacobaea erucifolia.jpg' => 'overzicht',
	
			'Jacobaea erucifolia 2.jpg' => 'vergroot',
	
			'Jacobaea erucifolia 3.jpg' => 'foto'
	
		),
	
		'Jacobaea aquatica' => array(
	
			'Jacobaea aquatica.jpg' => 'overzicht',
	
			'Jacobaea aquatica 2.jpg' => 'vergroot',
	
			'Jacobaea aquatica 3.jpg' => 'foto',
	
			'Jacobaea aquatica 4.jpg' => 'foto',
	
			'Jacobaea aquatica x vulgaris.jpg' => '(= Jacobaea aquatica x J. vulgaris subsp. dunensis) foto, bloeiwijzen'
	
		),
	
		'Jacobaea aquatica var. aquatica' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Jacobaea aquatica var. erratica' => array(
	
			'Geen illustratie.jpg' => ''
			
		),
			
		'Jacobaea vulgaris' => array(
	
			'Jacobaea vulgaris.jpg' => 'overzicht',
	
			'Jacobaea vulgaris 2.jpg' => 'vergroot; a = dsn. hoofdje, b = vrucht',
	
			'Jacobaea vulgaris 3.jpg' => 'foto'
	
		),
	
		'Jacobaea vulgaris subsp. vulgaris' => array(
	
			'Jacobaea vulgaris.jpg' => 'overzicht',
	
			'Jacobaea vulgaris 2.jpg' => 'vergroot; a = dsn. hoofdje, b = vrucht'
	
		),
	
		'Jacobaea vulgaris subsp. dunensis' => array(
	
			'Jacobaea vulgaris dunensis.jpg' => 'overzicht',
	
			'Jacobaea vulgaris dunensis 3.jpg' => 'foto',
	
			'Jacobaea vulgaris dunensis 4.jpg' => 'foto',
	
			'Jacobaea vulgaris dunensis 5.jpg' => 'foto',
	
			'Jacobaea aquatica x vulgaris.jpg' => '(= Jacobaea aquatica x J. vulgaris subsp. dunensis) foto, bloeiwijzen'
	
		),
	
		'Calendula officinalis' => array(
	
			'Calendula officinalis.jpg' => 'overzicht',
	
			'Calendula officinalis 2.jpg' => 'vergroot;  a = hoofdje in vrucht, b = vrucht, c = dsn. vrucht',
	
			'Calendula officinalis 3.jpg' => 'foto',
	
			'Calendula officinalis 4.jpg' => 'foto'
	
		),
	
		'Calendula arvensis' => array(
	
			'Calendula arvensis.jpg' => 'overzicht',
	
			'Calendula arvensis 2.jpg' => 'vergroot',
	
			'Calendula arvensis 3.jpg' => 'foto'
	
		),
	
		'Carlina vulgaris' => array(
	
			'Carlina vulgaris.jpg' => 'overzicht',
	
			'Carlina vulgaris 2.jpg' => 'vergroot; a = buisbloem, b = vrucht',
	
			'Carlina vulgaris 3.jpg' => 'foto',
	
			'Carlina vulgaris 4.jpg' => 'foto',
	
			'Carlina vulgaris 5.jpg' => 'foto'
	
		),
	
		'Echinops sphaerocephalus' => array(
	
			'Echinops sphaerocephalus.jpg' => 'overzicht',
	
			'Echinops sphaerocephalus 2.jpg' => 'vergroot; a = hoofdje, b = bloem',
	
			'Echinops sphaerocephalus 3.jpg' => 'foto',
	
			'Echinops sphaerocephalus 4.jpg' => 'foto'
	
		),
	
		'Echinops exaltatus' => array(
	
			'Echinops exaltatus.jpg' => 'overzicht foto',
	
			'Echinops exaltatus 2.jpg' => 'foto, habitus bloeiend',
	
			'Echinops exaltatus 3.jpg' => 'foto, bloeihoofdje'
	
		),
	
		'Arctium tomentosum' => array(
	
			'Arctium tomentosum.jpg' => 'overzicht',
	
			'Arctium tomentosum 2.jpg' => 'vergroot; a = bloem, b = dsn. bloem, c-e = buitenste, middelste, binnenste omwindselblad, f = buisbloem, g = meeldraad, h = stijl + stempel',
	
			'Arctium tomentosum 3.jpg' => 'foto, bloemhoofdjes',
	
			'Arctium tomentosum 4.jpg' => 'foto, habitus'
	
		),
	
		'Arctium lappa' => array(
	
			'Arctium lappa.jpg' => 'overzicht',
	
			'Arctium lappa 2.jpg' => 'vergroot; a-c = buitenste, middelste, binnenste omwindselblad, d = buisbloem, e = meeldraad, f = stijl + stempel, g = nootje',
	
			'Arctium lappa 3.jpg' => 'foto, habitus'
	
		),
	
		'Arctium nemorosum' => array(
	
			'Arctium nemorosum.jpg' => 'overzicht',
	
			'Arctium nemorosum 2.jpg' => 'vergroot; a-c = buitenste, middelste, binnenste omwindselblad, d = buisbloem, e = meeldraad, f = stijl + stempel, g = nootje'
	
		),
	
		'Arctium minus' => array(
	
			'Arctium minus.jpg' => 'overzicht',
	
			'Arctium minus 2.jpg' => 'vergroot; a = bloem, b-d = buitenste, middelste, binnenste omwindselblad, e = buisbloem, f = meeldraad, g = stijl + stempel, h = nootje',
	
			'Arctium minus 3.jpg' => 'foto, habitus'
	
		),
	
		'Carduus tenuiflorus' => array(
	
			'Carduus tenuiflorus.jpg' => 'overzicht',
	
			'Carduus tenuiflorus 2.jpg' => 'vergroot',
	
			'Carduus tenuiflorus 3.jpg' => 'foto'
	
		),
	
		'Carduus nutans' => array(
	
			'Carduus nutans.jpg' => 'overzicht',
	
			'Carduus nutans 2.jpg' => 'vergroot; a = omwindselbladen, b = bloem, c = vrucht',
	
			'Carduus nutans 3.jpg' => 'foto'
	
		),
	
		'Carduus crispus' => array(
	
			'Carduus crispus.jpg' => 'overzicht',
	
			'Carduus crispus 2.jpg' => 'vergroot',
	
			'Carduus crispus 3.jpg' => 'foto',
	
			'Carduus crispus 4.jpg' => 'foto'
	
		),
	
		'Carduus acanthoides' => array(
	
			'Carduus acanthoides.jpg' => 'overzicht',
	
			'Carduus acanthoides 2.jpg' => 'vergroot',
	
			'Carduus acanthoides 3.jpg' => 'foto',
	
			'Carduus acanthoides 4.jpg' => 'foto'
	
		),
	
		'Cirsium oleraceum' => array(
	
			'Cirsium oleraceum.jpg' => 'overzicht',
	
			'Cirsium oleraceum 2.jpg' => 'vergroot',
	
			'Cirsium oleraceum 3.jpg' => 'foto',
	
			'Cirsium oleraceum 4.jpg' => 'foto'
	
		),
	
		'Cirsium vulgare' => array(
	
			'Cirsium vulgare.jpg' => 'overzicht',
	
			'Cirsium vulgare 2.jpg' => 'vergroot',
	
			'Cirsium vulgare 3.jpg' => 'foto'
	
		),
	
		'Cirsium eriophorum' => array(
	
			'Cirsium eriophorum.jpg' => 'overzicht',
	
			'Cirsium eriophorum 2.jpg' => 'vergroot; a = omwindselblad',
	
			'Cirsium eriophorum 3.jpg' => 'foto',
	
			'Cirsium eriophorum 4.jpg' => 'foto'
	
		),
	
		'Cirsium acaule' => array(
	
			'Cirsium acaule.jpg' => 'overzicht',
	
			'Cirsium acaule 2.jpg' => 'vergroot',
	
			'Cirsium acaule 3.jpg' => 'foto',
	
			'Cirsium acaule 4.jpg' => 'foto'
	
		),
	
		'Cirsium dissectum' => array(
	
			'Cirsium dissectum.jpg' => 'overzicht',
	
			'Cirsium dissectum 2.jpg' => 'vergroot',
	
			'Cirsium dissectum 3.jpg' => 'foto',
	
			'Cirsium dissectum 4.jpg' => 'foto',
	
			'Cirsium dissectum 5.jpg' => 'foto'
	
		),
	
		'Cirsium palustre' => array(
	
			'Cirsium palustre.jpg' => 'overzicht',
	
			'Cirsium palustre 2.jpg' => 'vergroot',
	
			'Cirsium palustre 3.jpg' => 'foto',
	
			'Cirsium palustre 4.jpg' => 'foto'
	
		),
	
		'Cirsium arvense' => array(
	
			'Cirsium arvense.jpg' => 'overzicht',
	
			'Cirsium arvense 2.jpg' => 'vergroot; a = bloem',
	
			'Cirsium arvense 3.jpg' => 'foto',
	
			'Cirsium arvense 4.jpg' => 'foto'
	
		),
	
		'Onopordum acanthium' => array(
	
			'Onopordum acanthium.jpg' => 'overzicht',
	
			'Onopordum acanthium 2.jpg' => 'vergroot;  a = bloem, b = deel bloemhoofdjesbodem met rijpe vrucht',
	
			'Onopordum acanthium 3.jpg' => 'foto'
	
		),
	
		'Silybum marianum' => array(
	
			'Silybum marianum.jpg' => 'overzicht',
	
			'Silybum marianum 2.jpg' => 'vergroot; a = vrucht',
	
			'Silybum marianum 3.jpg' => 'foto, bloemhoofdje'
	
		),
	
		'Serratula tinctoria' => array(
	
			'Serratula tinctoria.jpg' => 'overzicht',
	
			'Serratula tinctoria 2.jpg' => 'vergroot; a = omwindselbladen, b = dsn. hoofdje, c = bloem',
	
			'Serratula tinctoria 3.jpg' => 'foto, bloeiwijzen',
	
			'Serratula tinctoria 4.jpg' => 'foto, bloeiwijzen'
	
		),
	
		'Centaurea calcitrapa' => array(
	
			'Centaurea calcitrapa.jpg' => 'overzicht',
	
			'Centaurea calcitrapa 2.jpg' => 'vergroot',
	
			'Centaurea calcitrapa 3.jpg' => 'foto',
	
			'Centaurea calcitrapa 4.jpg' => 'foto'
	
		),
	
		'Centaurea solstitialis' => array(
	
			'Centaurea solstitialis.jpg' => 'overzicht',
	
			'Centaurea solstitialis 2.jpg' => 'vergroot; a = omwindselblad',
	
			'Centaurea solstitialis 3.jpg' => 'foto',
	
			'Centaurea solstitialis 4.jpg' => 'foto'
	
		),
	
		'Centaurea jacea' => array(
	
			'Centaurea jacea.jpg' => 'overzicht',
	
			'Centaurea jacea 2.jpg' => 'vergroot; a = dsn. hoofdje, b = omwindselbladen, c = bloemen, d = vrucht',
	
			'Centaurea jacea 3.jpg' => 'foto',
	
			'Centaurea jacea 4.jpg' => 'foto'
	
		),
	
		'Centaurea scabiosa' => array(
	
			'Centaurea scabiosa.jpg' => 'overzicht',
	
			'Centaurea scabiosa 2.jpg' => 'vergroot',
	
			'Centaurea scabiosa 3.jpg' => 'foto',
	
			'Centaurea scabiosa 4.jpg' => 'foto'
	
		),
	
		'Centaurea stoebe' => array(
	
			'Centaurea stoebe.jpg' => 'overzicht',
	
			'Centaurea stoebe 3.jpg' => 'foto',
	
			'Centaurea stoebe 4.jpg' => 'foto'
	
		),
	
		'Centaurea cyanus' => array(
	
			'Centaurea cyanus.jpg' => 'overzicht',
	
			'Centaurea cyanus 2.jpg' => 'vergroot; a = dsn. hoofdje, b = omwindselbladen, c = randbloem, d = buisbloem, e = vrucht',
	
			'Centaurea cyanus 3.jpg' => 'foto'
	
		),
	
		'Centaurea montana' => array(
	
			'Centaurea montana.jpg' => 'overzicht',
	
			'Centaurea montana 2.jpg' => 'vergroot; a = omwindselblad',
	
			'Centaurea montana 3.jpg' => 'foto'
	
		),
	
		'Carthamus tinctorius' => array(
	
			'Carthamus tinctorius.jpg' => 'overzicht',
	
			'Carthamus tinctorius 2.jpg' => 'vergroot; a = omwindselbladen, b = bloem, c = vrucht',
	
			'Carthamus tinctorius 3.jpg' => 'foto'
	
		),
	
		'Cichorium intybus' => array(
	
			'Cichorium intybus.jpg' => 'overzicht',
	
			'Cichorium intybus 2.jpg' => 'vergroot; a = bloem, b = vrucht',
	
			'Cichorium intybus 3.jpg' => 'foto',
	
			'Cichorium intybus 4.jpg' => 'foto'
	
		),
	
		'Cichorium endivia' => array(
	
			'Cichorium endivia.jpg' => 'overzicht',
	
			'Cichorium endivia 2.jpg' => 'foto, andijvie in cultuur'
	
		),
	
		'Arnoseris minima' => array(
	
			'Arnoseris minima.jpg' => 'overzicht',
	
			'Arnoseris minima 2.jpg' => 'vergroot',
	
			'Arnoseris minima 3.jpg' => 'foto, habitus bloeiend',
	
			'Arnoseris minima 4.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Hypochaeris maculata' => array(
	
			'Hypochaeris maculata.jpg' => 'overzicht',
	
			'Hypochaeris maculata 2.jpg' => 'vergroot',
	
			'Hypochaeris maculata 3.jpg' => 'foto'
	
		),
	
		'Hypochaeris glabra' => array(
	
			'Hypochaeris glabra.jpg' => 'overzicht',
	
			'Hypochaeris glabra 2.jpg' => 'vergroot',
	
			'Hypochaeris glabra 3.jpg' => 'foto',
	
			'Hypochaeris glabra 4.jpg' => 'foto'
	
		),
	
		'Hypochaeris radicata' => array(
	
			'Hypochaeris radicata.jpg' => 'overzicht',
	
			'Hypochaeris radicata 2.jpg' => 'vergroot',
	
			'Hypochaeris radicata 3.jpg' => 'foto',
	
			'Hypochaeris radicata 4.jpg' => 'foto'
	
		),
	
		'Leontodon autumnalis' => array(
	
			'Leontodon autumnalis.jpg' => 'overzicht',
	
			'Leontodon autumnalis 2.jpg' => 'vergroot; a = dsn. hoofdje',
	
			'Leontodon autumnalis 3.jpg' => 'foto'
	
		),
	
		'Leontodon saxatilis' => array(
	
			'Leontodon saxatilis.jpg' => 'overzicht',
	
			'Leontodon saxatilis 2.jpg' => 'vergroot; a = randstandige vrucht, b = vrucht overige bloemen',
	
			'Leontodon saxatilis 3.jpg' => 'foto'
	
		),
	
		'Leontodon hispidus' => array(
	
			'Leontodon hispidus.jpg' => 'overzicht',
	
			'Leontodon hispidus 2.jpg' => 'vergroot; a = vrucht',
	
			'Leontodon hispidus 3.jpg' => 'foto',
	
			'Leontodon hispidus 4.jpg' => 'foto'
	
		),
	
		'Picris echioides' => array(
	
			'Picris echioides.jpg' => 'overzicht',
	
			'Picris echioides 2.jpg' => 'vergroot',
	
			'Picris echioides 3.jpg' => 'foto'
	
		),
	
		'Picris hieracioides' => array(
	
			'Picris hieracioides.jpg' => 'overzicht',
	
			'Picris hieracioides 2.jpg' => 'vergroot; a = vrucht',
	
			'Picris hieracioides 3.jpg' => 'foto'
	
		),
	
		'Scorzonera humilis' => array(
	
			'Scorzonera humilis.jpg' => 'overzicht',
	
			'Scorzonera humilis 2.jpg' => 'vergroot',
	
			'Scorzonera humilis 3.jpg' => 'foto, habitus bloeiend',
	
			'Scorzonera humilis 4.jpg' => 'foto, bloemhoofdjes'
	
		),
	
		'Scorzonera hispanica' => array(
	
			'Scorzonera hispanica.jpg' => 'overzicht',
	
			'Scorzonera hispanica 2.jpg' => 'vergroot',
	
			'Scorzonera hispanica 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Tragopogon pratensis' => array(
	
			'Tragopogon pratensis pratens.jpg' => 'overzicht',
	
			'Tragopogon pratensis prat 2.jpg' => 'vergroot; a = bloem, subsp. pratensis',
	
			'Tragopogon pratensis orien 2.jpg' => 'vergroot, subsp. orientalis'
	
		),
	
		'Tragopogon pratensis subsp. orientalis' => array(
	
			'Tragopogon pratensis orient.jpg' => 'overzicht',
	
			'Tragopogon pratensis orien 2.jpg' => 'vergroot',
	
			'Tragopogon pratensis orien 3.jpg' => 'foto, habitus bloeiend'
	
		),
	
		'Tragopogon pratensis subsp. pratensis' => array(
	
			'Tragopogon pratensis pratens.jpg' => 'overzicht',
	
			'Tragopogon pratensis prat 2.jpg' => 'vergroot; a = bloem'
	
		),
	
		'Tragopogon porrifolius' => array(
	
			'Tragopogon porrifolius.jpg' => 'overzicht',
	
			'Tragopogon porrifolius 2.jpg' => 'vergroot, in bloei',
	
			'Tragopogon porrifolius 3.jpg' => 'foto, habitus in bloei',
	
			'Tragopogon porrifolius 4.jpg' => 'foto, bloemhoofdje'
	
		),
	
		'Tragopogon dubius' => array(
	
			'Tragopogon dubius.jpg' => 'overzicht',
	
			'Tragopogon dubius 2.jpg' => 'vergroot, in bloei',
	
			'Tragopogon dubius 3.jpg' => 'foto, habitus in bloei en in vrucht',
	
			'Tragopogon dubius 4.jpg' => 'foto, bloemhoofdje'
	
		),
	
		'Sonchus arvensis' => array(
	
			'Sonchus arvensis.jpg' => 'overzicht',
	
			'Sonchus arvensis 2.jpg' => 'vergroot; a = dsn. hoofdje, b = bloem, c = vrucht',
	
			'Sonchus arvensis 3.jpg' => 'foto, bloemhoofdjes',
	
			'Sonchus arvensis 4.jpg' => 'foto, in bloei'
	
		),
	
		'Sonchus palustris' => array(
	
			'Sonchus palustris.jpg' => 'overzicht',
	
			'Sonchus palustris 2.jpg' => 'vergroot',
	
			'Sonchus palustris 3.jpg' => 'foto, bloeiend',
	
			'Sonchus palustris 4.jpg' => 'foto, bloemhoofdjes'
	
		),
	
		'Sonchus oleraceus' => array(
	
			'Sonchus oleraceus.jpg' => 'overzicht',
	
			'Sonchus oleraceus 2.jpg' => 'vergroot',
	
			'Sonchus oleraceus 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Sonchus asper' => array(
	
			'Sonchus asper.jpg' => 'overzicht',
	
			'Sonchus asper 2.jpg' => 'vergroot',
	
			'Sonchus asper 3.jpg' => 'foto, bloeiwijze',
	
			'Sonchus asper 4.jpg' => 'foto, bloeiwijze'
	
		),
	
		'Lactuca tatarica' => array(
	
			'Lactuca tatarica.jpg' => 'overzicht foto',
	
			'Lactuca tatarica 3.jpg' => 'foto, in bloei'
	
		),
	
		'Lactuca saligna' => array(
	
			'Lactuca saligna.jpg' => 'overzicht',
	
			'Lactuca saligna 2.jpg' => 'vergroot'
	
		),
	
		'Lactuca sativa' => array(
	
			'Lactuca sativa.jpg' => 'overzicht',
	
			'Lactuca sativa 2.jpg' => 'vergroot',
	
			'Lactuca sativa 3.jpg' => 'foto, bloeiend',
	
			'Lactuca sativa 4.jpg' => 'foto, bloeiend',
	
			'Lactuca sativa 5.jpg' => 'foto, kropsla'
	
		),
	
		'Lactuca serriola' => array(
	
			'Lactuca serriola.jpg' => 'overzicht',
	
			'Lactuca serriola 2.jpg' => 'vergroot',
	
			'Lactuca serriola 3.jpg' => 'foto, in bloei',
	
			'Lactuca serriola 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Lactuca virosa' => array(
	
			'Lactuca virosa.jpg' => 'overzicht',
	
			'Lactuca virosa 2.jpg' => 'vergroot; a = dsn. hoofdje, b = vruchten',
	
			'Lactuca virosa 3.jpg' => 'foto, in bloei en vrucht',
	
			'Lactuca virosa 4.jpg' => 'foto, in bloei en vrucht'
	
		),
	
		'Mycelis muralis' => array(
	
			'Mycelis muralis.jpg' => 'overzicht',
	
			'Mycelis muralis 2.jpg' => 'vergroot; a = vrucht',
	
			'Mycelis muralis 3.jpg' => 'foto'
	
		),
	
		'Taraxacum officinale' => array(
	
			'Taraxacum officinale.jpg' => 'overzicht',
	
			'Taraxacum officinale 2.jpg' => 'vergroot; a = basisdeel vrucht',
	
			'Taraxacum officinale 3.jpg' => 'foto, bloeiend',
	
			'Taraxacum officinale 4.jpg' => 'foto, in vrucht'
	
		),
	
		'Chondrilla juncea' => array(
	
			'Chondrilla juncea.jpg' => 'overzicht',
	
			'Chondrilla juncea 2.jpg' => 'vergroot; a = pappus',
	
			'Chondrilla juncea 3.jpg' => 'foto, bloeiend',
	
			'Chondrilla juncea 4.jpg' => 'foto, bloeiend'
	
		),
	
		'Lapsana communis' => array(
	
			'Lapsana communis.jpg' => 'overzicht',
	
			'Lapsana communis 2.jpg' => 'vergroot; a = hoofdje, b = vruchthoofdje',
	
			'Lapsana communis 3.jpg' => 'foto'
	
		),
	
		'Crepis paludosa' => array(
	
			'Crepis paludosa.jpg' => 'overzicht',
	
			'Crepis paludosa 2.jpg' => 'vergroot'
	
		),
	
		'Crepis foetida' => array(
	
			'Crepis foetida.jpg' => 'overzicht',
	
			'Crepis foetida 2.jpg' => 'vergroot',
	
			'Crepis foetida 3.jpg' => 'foto'
	
		),
	
		'Crepis setosa' => array(
	
			'Crepis setosa.jpg' => 'overzicht',
	
			'Crepis setosa 2.jpg' => 'vergroot',
	
			'Crepis setosa 3.jpg' => 'foto'
	
		),
	
		'Crepis vesicaria subsp. taraxacifolia' => array(
	
			'Crepis vesicaria taraxacif.jpg' => 'overzicht',
	
			'Crepis vesicaria taraxacif 2.jpg' => 'vergroot; a = pappus',
	
			'Crepis vesicaria taraxacif 3.jpg' => 'foto',
	
			'Crepis vesicaria taraxacif 4.jpg' => 'foto'
	
		),
	
		'Crepis tectorum' => array(
	
			'Crepis tectorum.jpg' => 'overzicht',
	
			'Crepis tectorum 2.jpg' => 'vergroot; a = vrucht',
	
			'Crepis tectorum 3.jpg' => 'foto'
	
		),
	
		'Crepis capillaris' => array(
	
			'Crepis capillaris.jpg' => 'overzicht',
	
			'Crepis capillaris 2.jpg' => 'vergroot',
	
			'Crepis capillaris 3.jpg' => 'foto'
	
		),
	
		'Crepis biennis' => array(
	
			'Crepis biennis.jpg' => 'overzicht',
	
			'Crepis biennis 2.jpg' => 'vergroot; a = pappus',
	
			'Crepis biennis 3.jpg' => 'foto'
	
		),
	
		'Hieracium peleterianum' => array(
	
			'Hieracium peleterianum.jpg' => 'overzicht',
	
			'Hieracium peleterianum 3.jpg' => 'foto, bloeiend'
	
		),
	
		'Hieracium pilosella' => array(
	
			'Hieracium pilosella.jpg' => 'overzicht',
	
			'Hieracium pilosella 2.jpg' => 'vergroot; a = dsn. hoofdje, b = vrucht',
	
			'Hieracium pilosella 3.jpg' => 'foto',
	
			'Hieracium pilosella 4.jpg' => 'foto',
	
			'Hieracium pilosella 5.jpg' => 'foto',
	
			'Hieracium stoloniflorum(x) 3.jpg' => '(= Hieracium aurantiacum x Hierancium pilosella) foto',
	
			'Hieracium stoloniflorum(x) 4.jpg' => '(= Hieracium aurantiacum x Hierancium pilosella) foto'
	
		),
	
		'Hieracium aurantiacum' => array(
	
			'Hieracium aurantiacum.jpg' => 'overzicht',
	
			'Hieracium aurantiacum 2.jpg' => 'vergroot',
	
			'Hieracium aurantiacum 3.jpg' => 'foto',
	
			'Hieracium aurantiacum 4.jpg' => 'foto',
	
			'Hieracium stoloniflorum(x) 3.jpg' => '(= Hieracium aurantiacum x Hierancium pilosella) foto',
	
			'Hieracium stoloniflorum(x) 4.jpg' => '(= Hieracium aurantiacum x Hierancium pilosella) foto'
	
		),
	
		'Hieracium caespitosum' => array(
	
			'Hieracium caespitosum.jpg' => 'overzicht',
	
			'Hieracium caespitosum 2.jpg' => 'vergroot'
	
		),
	
		'Hieracium praealtum' => array(
	
			'Hieracium praealtum.jpg' => 'overzicht',
	
			'Hieracium praealtum 2.jpg' => 'vergroot'
	
		),
	
		'Hieracium lactucella' => array(
	
			'Hieracium lactucella.jpg' => 'overzicht',
	
			'Hieracium lactucella 2.jpg' => 'vergroot',
	
			'Hieracium lactucella 3.jpg' => 'foto'
	
		),
	
		'Hieracium flagellare(x)' => array(
	
			'Hieracium flagellare(x).jpg' => 'overzicht',
	
			'Hieracium flagellare(x) 2.jpg' => '(= Hieracium caespitosum x Hieracium pilosella) foto'
	
		),
	
		'Hieracium brachiatum(x)' => array(
	
			'Hieracium brachiatum(x).jpg' => 'overzicht',
	
			'Hieracium brachiatum(x) 2.jpg' => '(= Hieracium pilosella x Hiercium praealtum) vergroot'
	
		),
	
		'Hieracium schultesii(x)' => array(
	
			'Hieracium schultesii(x).jpg' => 'overzicht',
	
			'Hieracium schultesii(x) 2.jpg' => 'foto, bloeiend'
	
		),
	
		'Hieracium amplexicaule' => array(
	
			'Hieracium amplexicaule.jpg' => 'overzicht',
	
			'Hieracium amplexicaule 2.jpg' => 'vergroot',
	
			'Hieracium amplexicaule 3.jpg' => 'foto'
	
		),
	
		'Hieracium vulgatum' => array(
	
			'Hieracium vulgatum.jpg' => 'overzicht',
	
			'Hieracium vulgatum 2.jpg' => 'vergroot',
	
			'Hieracium vulgatum 3.jpg' => 'foto',
	
			'Hieracium vulgatum 4.jpg' => 'foto'
	
		),
	
		'Hieracium murorum' => array(
	
			'Hieracium murorum.jpg' => 'overzicht',
	
			'Hieracium murorum 2.jpg' => 'vergroot',
	
			'Hieracium murorum 3.jpg' => 'foto'
	
		),
	
		'Hieracium umbellatum' => array(
	
			'Hieracium umbellatum.jpg' => 'overzicht',
	
			'Hieracium umbellatum 2.jpg' => 'vergroot',
	
			'Hieracium umbellatum 3.jpg' => 'foto',
	
			'Hieracium umbellatum 4.jpg' => 'foto'
	
		),
	
		'Hieracium sabaudum' => array(
	
			'Hieracium sabaudum.jpg' => 'overzicht',
	
			'Hieracium sabaudum 2.jpg' => 'vergroot',
	
			'Hieracium sabaudum 3.jpg' => 'foto',
	
			'Hieracium sabaudum 4.jpg' => 'foto',
	
			'Hieracium sabaudum 5.jpg' => 'foto'
	
		),
	
		'Hieracium laevigatum' => array(
	
			'Hieracium laevigatum.jpg' => 'overzicht',
	
			'Hieracium laevigatum 2.jpg' => 'vergroot'
		)
	);
?>