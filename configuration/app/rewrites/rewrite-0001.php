<?php

        include_once('../configuration/app/controllers/SpeciesController.php');
        $c=new SpeciesController;

        $baseUrlLng='/linnaeus_ng/app/views/';
        //$redirectUrl='/';
        $redirectUrl=$url.(count((array)$parameters)>0 ? '?'. http_build_query($parameters) : '');

        function resolveNsrId($nsrid,$type)
        {
                global $c;

                $nsrid=str_pad($nsrid,12,'0',STR_PAD_LEFT);

                $data=$c->models->Taxon->freeQuery("
                        select
                                *
                        from
                                %PRE%nsr_ids
                        where
                                nsr_id like '%".
                                        mysql_real_escape_string($type).
                                        "/".
                                        mysql_real_escape_string($nsrid)."'
                ");
                return $data[0];
        }

        function resolveTab($name)
        {
                switch($name)
                {
                        case "introduction" :
                                return TAB_ALGEMEEN;
                                break;
                        case "recognition" :
                                return TAB_HERKENNING;
                                break;
                        case "similarSpecies" :
                                return TAB_GELIJKENDE_SOORTEN;
                                break;
                        case "summary" :
                                return TAB_SUMMARY;
                                break;
                        case "research" :
                                return TAB_ONDERZOEK;
                                break;
                        case "conservation" :
                                return TAB_BESCHERMING;
                                break;
                        case "biotopes" :
                                return TAB_BIOTOPEN;
                                break;
                        case "relatedSpecies" :
                                return TAB_RELATIES;
                                break;
                        case "biology" :
                                return TAB_LEVENSWIJZE;
                                break;
                        case "taxonomy" :
                                return CTAB_NAMES;
                                break;
                        case "images" :
                        case "imagesAndSounds" :
                                return CTAB_MEDIA;
                                break;
                        /*
                        TAB_BEDREIGING_EN_BESCHERMING
                        TAB_BRONNEN
                        TAB_HABITAT
                        TAB_NAAMGEVING
                        TAB_SAMENVATTING
                        TAB_SCHADE_EN_NUT
                        TAB_VERPLAATSING
                        TAB_VERSPREIDING
                        CTAB_CLASSIFICATION
                        CTAB_TAXON_LIST
                        CTAB_LITERATURE
                        CTAB_DNA_BARCODES
                        CTAB_NOMENCLATURE
                        */
                }
        }

        $fixedUrls=array(
                'nsr/nsr/i000399.html'=>$baseUrlLng.'search/nsr_search_extended.php',
                'nsr/nsr/i000398.html'=>$baseUrlLng.'search/nsr_search_pictures.php',
                'nsr/nsr/recentImages.html'=>$baseUrlLng.'search/nsr_recent_pictures.php',
                'natuurwidget'=>'/node/48',
                'nsr/nsr/home.html'=>'/',
                'nsr/nsr/i000000.html'=>'/',
                'nsr/nsr/i000222.html'=>'/node/12',
                'nsr/nsr/i000224.html'=>'/node/13',
                'nsr/nsr/i000234.html'=>'/node/14',
                'nsr/nsr/i000335.html'=>'/node/15',
                'nsr/nsr/i000404.html'=>'/node/16',
                'nsr/nsr/i000242.html'=>'/node/17',
                'nsr/nsr/i000330.html'=>'/node/49',
                'nsr/nsr/i000334.html'=>'/node/50',
                'nsr/nsr/i000388.html'=>'/node/18',
                'nsr/nsr/i000396.html'=>'/node/48',
                'nlsr/nlsr/i000396.html'=>'/node/48',
                'nsr/nsr/i000403.html'=>'/content/colofon-natuurwidget',
                'nsr/nsr/i000397.html'=>'/content/installatie-instructie',
                'nsr/nsr/i000363.html'=>'/node/19',
                'nsr/nsr/i000366.html'=>'/node/21',
                'nsr/nsr/i000369.html'=>'/node/22',
                'nsr/nsr/i000374.html'=>'/node/23',
                'nsr/nsr/i000372.html'=>'/node/24',
                'nsr/nsr/i000385.html'=>'/node/25',
                'nsr/nsr/i000386.html'=>'/node/26',
                'nsr/nsr/i000406.html'=>'/node/27',
                'nsr/nsr/i000323.html'=>'/node/28',
                'nsr/nsr/i000324.html'=>'/node/29',
                'nsr/nsr/i000326.html'=>'/node/30',
                'nsr/nsr/i000325.html'=>'/node/31',
                'nsr/nsr/i000327.html'=>'/node/32',
                'nsr/nsr/english.html'=>'/node/374',
                'nsr/nsr/english.html'=>'/node/375',
                'nsr/nsr/links.html'=>'/node/373',
                'nsr/nsr/contact.html'=>'/node/535',
                'nsr/nsr/colofon.html'=>'/content/colofon'
        );

        preg_match('/^(nsr|nlsr|zoek|natuurwidget|get)(\/)?(.*)$/',$url,$matches);

        if ($matches[1]=='get')
        {
                if (isset($parameters['page_alias']))
                {
                        if ($parameters['page_alias']=='topList' && isset($parameters['show']) && $parameters['show']=='photographers')
                        {
                                $redirectUrl=$baseUrlLng.'search/nsr_photographers.php';
                        }
                        else
                        if ($parameters['page_alias']=='topList' && isset($parameters['show']) && $parameters['show']=='validators')
                        {
                                $redirectUrl=$baseUrlLng.'search/nsr_validators.php';
                        }
                        else
                        if ($parameters['page_alias']=='searchImages' && isset($parameters['photographer']))
                        {
                                $redirectUrl=$baseUrlLng.'search/nsr_search_pictures.php?photographer='.$parameters['photographer'];
                        }
                        else
                        if ($parameters['page_alias']=='searchImages' && isset($parameters['validator']))
                        {
                                $redirectUrl=$baseUrlLng.'search/nsr_search_pictures.php?validator='.$parameters['validator'];
                        }
                        else
                        if ($parameters['page_alias']=='imageview' && isset($parameters['cid']))
                        {
                                $data=resolveNsrId($parameters['cid'],'concept');
                                $redirectUrl=
                                        $baseUrlLng.
                                        "species/nsr_taxon.php?id=".$data['lng_id'].
                                        "&cat=".CTAB_MEDIA.
                                        (isset($parameters['image']) ? "&img=".$parameters['image'] : null);
                        }
                        else
                        if ($parameters['page_alias']=='conceptcard' && isset($parameters['cid']) && isset($parameters['detail']))
                        {
                                $data=resolveNsrId($parameters['cid'],'concept');
                                $redirectUrl=$baseUrlLng."species/nsr_taxon.php?id=".$data['lng_id']."&cat=".resolveTab($parameters['detail']);
                        }
                        else
                        if ($parameters['page_alias']=='conceptcard' && isset($parameters['cid']))
                        {
                                $data=resolveNsrId($parameters['cid'],'concept');
                                $redirectUrl=$baseUrlLng."species/nsr_taxon.php?id=".$data['lng_id'];
                        }
                }
                else
                if (isset($parameters['id']) && $parameters['id']=='i000359')
                {
                        $redirectUrl='nieuws'.(isset($parameters['date']) ? '/'.$parameters['date'] : '');
                }
                else
                if ($parameters['site']=='nsr')
                {
                        $redirectUrl='/';
                }
        }
        else
        if ($matches[1]=='zoek')
        {
                $search=@$matches[3];
                if (isset($search))
                {
                        $redirectUrl=$baseUrlLng.'search/nsr_search.php?search='.$search;
                }
        }
        else
        if ($matches[1]=='natuurwidget')
        {
                $redirectUrl=$fixedUrls['natuurwidget'];
        }
        else
        if (isset($fixedUrls[$matches[0]]))
        {
                $redirectUrl=$fixedUrls[$matches[0]];
        }
        else
        {
                //preg_match('/(nsr|nlsr|zoek)\/([a-zA-Z]*)\/([^\/]*)(\/)(.*)$/',$url,$matches);
                preg_match('/(nsr|nlsr|zoek)\/([a-zA-Z]*)((\/)([^\/]*))(\/(.*))?/',$url,$matches);

                $type=@$matches[2];
                $nsrid=@$matches[5];
                $tab=@$matches[7];

                if (isset($nsrid))
                {
                        $data=resolveNsrId($nsrid,$type);
//print_r($data);die();
                        switch($data['item_type'])
                        {
                                case "taxon":
                                        $redirectUrl=$baseUrlLng."species/nsr_taxon.php?id=".$data['lng_id']."&cat=".resolveTab($tab);
                                        break;
                                case "name":
                                        $redirectUrl=$baseUrlLng."species/name.php?id=".$data['lng_id'];
                                        break;
                        }

                }

        }

        //die($redirectUrl);
        $c->redirect($redirectUrl);

