<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

class MatrixKeyExtModel extends AbstractModel
{

    public function __construct ()
    {
        parent::__construct();

        $this->connectToDatabase() or die(_('Failed to connect to database '.
            $this->databaseSettings['database'].
        	' with user ' . $this->databaseSettings['user'] . '. ' .
            mysqli_connect_error() . '. Correct the getDatabaseSettings() settings
        	in configuration/admin/config.php.'));
    }

    public function getMatrices( $p )
    {
        $project_id = isset($p['project_id']) ? $p['project_id'] : null;
        $default_project_language = isset($p['default_project_language']) ? $p['default_project_language'] : null;

        if ( is_null($project_id) ) return;

        $query="
            select
                * 
            from
                %PRE%matrices
            where
                project_id = ". $project_id . "
        ";

        $d=$this->freeQuery( $query );

        $m=[];

        foreach( $d as $key=>$val )
        {
            $l=$this->getMatrixLabels( [ "matrix_id" => $val["id"], "project_id" => $project_id ]);

            $defLabel=null;

            if ( !is_null($default_project_language) )
            {
                foreach($l as $lKey=>$lVal)
                {
                    if ($lVal["language_id"]==$default_project_language)
                    {
                        $l[$lKey]["default"]=1;
                        $defLabel=$lVal["name"];
                    }
                }
            }
            
            $m[]=[
                "id"=>$val["id"],
                "default"=>$val["default"],
                "sys_name"=>$val["sys_name"],
                "labels"=>$l,
                "default_label"=>$defLabel
            ];
        }

        if ( !is_null($default_project_language) )
        {
            usort($m, function($a,$b)
            {
                if ($a["default_label"]==$b["default_label"])
                    return 0;
                return $a["default_label"] > $b["default_label"];
            });
        }

        return $m;

     }

    public function getMatrix( $p )
    {
        $project_id = isset($p['project_id']) ? $p['project_id'] : null;
        $matrix_id = isset($p['matrix_id']) ? $p['matrix_id'] : null;
        $default_project_language = isset($p['default_project_language']) ? $p['default_project_language'] : null;

        if ( is_null($project_id) ) return;
        if ( is_null($matrix_id) ) return;

        $query="
            select
                * 
            from
                %PRE%matrices
            where
                project_id = ". $project_id . "
                and id = " . $matrix_id . "
        ";

        $d=$this->freeQuery( $query );

        if (!$d) return;

        $m=[
            "id"=>$d[0]["id"],
            "default"=>$d[0]["default"],
            "sys_name"=>$d[0]["sys_name"]
        ];

        $l=$this->getMatrixLabels( [ "matrix_id" => $m["id"], "project_id" => $project_id ]);

        $defLabel=null;

        if ( !is_null($default_project_language) )
        {
            foreach($l as $lKey=>$lVal)
            {
                if ($lVal["language_id"]==$default_project_language)
                {
                    $l[$lKey]["default"]=1;
                    $defLabel=$lVal["name"];
                }
            }
        }
            
        $m["labels"]=$l;
        $m["default_label"]=$defLabel;

        return $m;
     }

    public function getMatrixLabels( $p )
    {
        $project_id = isset($p['project_id']) ? $p['project_id'] : null;
        $matrix_id = isset($p['matrix_id']) ? $p['matrix_id'] : null;
        $language_id = isset($p['language_id']) ? $p['language_id'] : null;

        if ( is_null($project_id) ) return;
        if ( is_null($matrix_id) ) return;

        $query="
            select
                id,language_id,name
            from
                %PRE%matrices_names
            where
                project_id = ". $project_id . "
                and matrix_id = ". $matrix_id . "
                " . ( !is_null($language_id) ? "and language_id = ". $language_id : "" ) ."
            ";

        return $this->freeQuery( $query );
    }

    public function getCharacters( $p )
    {
        $project_id = isset($p['project_id']) ? $p['project_id'] : null;
        $matrix_id = isset($p['matrix_id']) ? $p['matrix_id'] : null;
        $default_project_language = isset($p['default_project_language']) ? $p['default_project_language'] : null;

        if ( is_null($project_id) ) return;
        if ( is_null($matrix_id) ) return;
        if ( is_null($default_project_language) ) return;

        $query ="
            select
                _b.id,
                _b.type,
                _b.sys_name,
                ifnull(_c.label,_b.sys_name) as default_label,
                _a.show_order

            from
                %PRE%characteristics_matrices _a
            
            right join
                %PRE%characteristics _b
                    on _a.project_id=_b.project_id
                    and _a.characteristic_id=_b.id

            left join
                %PRE%characteristics_labels _c
                    on _b.project_id=_c.project_id
                    and _b.id=_c.characteristic_id
                    and _c.language_id=" . $default_project_language . "

            where 
                _a.project_id = ". $project_id ."
                and _a.matrix_id = ". $matrix_id."

            order by
                _a.show_order
        ";


        $d=$this->freeQuery( $query );

        if ( $d )
        {
            foreach($d as $key=>$val)
            {
                if (strpos($val['default_label'],'|')!==false)
                {
                    $boom=explode('|',$val['default_label']);
                    $d[$key]['short_label']=$boom[0];
                    $d[$key]['default_label']=$boom[1];
                }

                $query ="
                    select
                        *
                    from
                        %PRE%characteristics_labels
                    where
                        project_id = ". $project_id ."
                        and characteristic_id = " . $val['id'] . "
                    ";

                $l=$this->freeQuery( ["query"=>$query ] );

                foreach($l as $lKey=>$lVal)
                {
                    if (strpos($lVal['label'],'|')!==false)
                    {
                        $boom=explode('|',$lVal['label']);
                        $l[$lKey]['short_label']=$boom[0];
                        $l[$lKey]['label']=$boom[1];
                    }
                }

                $d[$key]['labels']=$l;
            }

            return $d;
        }




        return
            $this->models->MatrixkeyModel->getCharacters( [
                'project_id' => $this->getCurrentProjectId(),
                'matrix_id' => $id,
                'language_id' => $this->getDefaultProjectLanguage()
        ] );
    }   
    
    public function getStates( $p )
    {

        $project_id = isset($p['project_id']) ? $p['project_id'] : null;
        $default_project_language = isset($p['default_project_language']) ? $p['default_project_language'] : null;
        $character_id = isset($params['character_id']) ? $params['character_id'] : null;

        if ( is_null($project_id) ) return;
        if ( is_null($default_project_language) ) return;

        $query ="
            select
                _a.id,
                _a.sys_name,
                _a.characteristic_id,
                _a.file_name,
                _a.file_dimensions,
                _a.lower,
                _a.upper,
                _a.mean,
                _a.sd,
                ifnull(_b.label,_a.sys_name) as default_label,
                _b.text as default_text

            from
                %PRE%characteristics_states _a

            left join
                %PRE%characteristics_labels_states _b
                    on _a.project_id=_b.project_id
                    and _a.id=_b.state_id
                    and _b.language_id=" . $default_project_language . "

            where 
                _a.project_id = ". $project_id ."
                " . ( !is_null($character_id)  ? "and _a.character_id = " . $character_id : "" ) ."

            order by
                _a.show_order
            ";

        $d=$this->freeQuery( $query );

        if ($d)
        {
            foreach((array)$d as $key=>$val)
            {
                $query ="
                    select
                        text,
                        label,
                        language_id

                    from
                        %PRE%characteristics_labels_states

                    where
                        project_id = ". $project_id ."
                        and state_id = " . $val['id'] . "
                    ";

                $t=$this->freeQuery( [ "query"=>$query ] );
                $d[$key]['labels']=array_map(function($val){ unset($val['text']); return $val;},(array)$t);
                $d[$key]['texts']=array_map(function($val){ unset($val['label']); return $val;},(array)$t);


            }
            
            return  $d;
        }
    }   

    public function getGroups( $p )
    {
        $project_id=isset($p['project_id']) ? $p['project_id'] : null;
        $matrix_id=isset($p['matrix_id']) ? $p['matrix_id'] : null;
        $default_project_language=isset($p['default_project_language']) ? $p['default_project_language'] : null;

        if ( is_null($project_id) ) return;
        if ( is_null($matrix_id) ) return;
        if ( is_null($default_project_language) ) return;

        $query="
            select
                id,
                label as sys_name
            from
                %PRE%chargroups
            where
                project_id = " .$project_id. "
                and matrix_id = " .$matrix_id. "
            order by
                show_order
            ";
        
        $d=$this->freeQuery([ 'query'=>$query ] );

        if ($d)
        {
            foreach((array)$d as $key=>$val)
            {
                $query ="
                    select
                        label,
                        language_id

                    from
                        %PRE%chargroups_labels

                    where
                        project_id = ". $project_id ."
                        and chargroup_id = ". $val['id'] ."
                    ";

                $d[$key]['labels']=$this->freeQuery( [ "query"=>$query ] );

                $dKey=array_search($default_project_language,array_column($d[$key]['labels'],'language_id'));

                if ($dKey!==false)
                {
                     $d[$key]['default_label']=$d[$key]['labels'][$dKey]['label'];
                }

                $query ="
                    select
                        characteristic_id

                    from
                        %PRE%characteristics_chargroups

                    where
                        project_id = ". $project_id ."
                        and chargroup_id = ". $val['id'] ."

                    order by
                        show_order
                    ";

                $d[$key]['characters']=$this->freeQuery( [ "query"=>$query ] );
            }
        }

        return $d;
    }

    public function getGuiOrder( $p )
    {
        $project_id=isset($p['project_id']) ? $p['project_id'] : null;
        $matrix_id=isset($p['matrix_id']) ? $p['matrix_id'] : null;

        if ( is_null($project_id) ) return;
        if ( is_null($matrix_id) ) return;

        $query="
            select
                ref_id,
                ref_type

            from
                %PRE%gui_menu_order

            where
                project_id = " .$project_id. "
                and matrix_id = " .$matrix_id. "

            order by
                show_order
            ";
        
        $d=$this->freeQuery( [ 'query'=>$query ] );

        return $d;
    }

    public function getTaxaForState( $p )
    {

        $project_id = isset($p['project_id']) ? $p['project_id'] : null;
        $matrix_id = isset($p['matrix_id']) ? $p['matrix_id'] : null;
        $state_id = isset($p['state_id']) ? $p['state_id'] : null;
        $language_id = isset($p['language_id']) ? $p['language_id'] : null;
        $name_type_id = isset($p['name_type_id']) ? $p['name_type_id'] : null;

        if ( is_null($project_id) ) return;
        if ( is_null($matrix_id) ) return;
        if ( is_null($state_id) ) return;
        if ( is_null($language_id) ) return;
        if ( is_null($name_type_id) ) return;

        $query ="
            select
                _a.id,
                _a.taxon,
                _c.name
            from
                %PRE%taxa _a

            right join
                %PRE%matrices_taxa_states _b
                    on _a.project_id = _b.project_id
                    and _a.id = _b.taxon_id
                    and _b.matrix_id = ". $matrix_id ."
                    and _b.state_id = ". $state_id ."

            left join
                %PRE%names _c
                    on _a.project_id = _c.project_id
                    and _a.id = _c.taxon_id
                    and _c.language_id= ". $language_id ."
                    and _c.type_id = ". $name_type_id ."

            where 
                _a.project_id = ". $project_id ."

            ";

        $d=$this->freeQuery( $query );

        // taking out possible double dutch names
        $result=[];
        foreach($d as $value) {
            $result[$value['taxon']] = $value;
        }

        return array_values($result);
    }

}
