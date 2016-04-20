<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

final class MediaModel extends AbstractModel
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

    public function __destruct ()
    {
        if ($this->databaseConnection) {
            $this->disconnectFromDatabase();
        }
        parent::__destruct();
    }


    public function search ($p)
    {
        $search = isset($p['search']) && $p['search'] != '' ?
            $p['search'] : false;
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;
        $sort = isset($p['sort']) && !empty($p['sort']) ?
            $p['sort'] : 'name';

        if (!isset($search['metadata']) && !isset($search['tags']) &&
            !isset($search['file_name']) || !$projectId) {
            return false;
        }

        $query = '
            select
                t1.*
            from
                %PRE%media as t1 ' .
        ($this->arrayHasData($search['metadata']) ?
            'left join
                %PRE%media_metadata as t2
                on t1.`id` = t2.`media_id` ' : ''
        ) .
        ($this->arrayHasData($search['tags']) ?
            'left join
                %PRE%media_tags as t3
                on t1.`id` = t3.`media_id` ' : ''
        ) . '
            where
                t1.`deleted` = 0 and
                t1.`project_id` = ' . $this->escapeString($projectId) .
                $this->appendSearchWhere($search);

        return $this->freeQuery($query);
    }

    private function appendSearchWhere ($search) {
        if ($this->arrayHasData($search['metadata'])) {
            foreach ($search['metadata'] as $k => $v) {
                if ($v != '') {
                    $a[] = "t2.`sys_label` = '" . $this->escapeString(trim($k)) .
                        "' and t2.`metadata` like '" . $this->escapeString(trim($v)) . "%'";
                }
            }
        }
        if ($this->arrayHasData($search['tags'])) {
            foreach ($search['tags'] as $tag) {
                $a[] = "t3.`tag` like '" . $this->escapeString(trim($tag)) . "%'";
            }
        }
        if ($search['file_name'] != '') {
            $a[] = "t1.`name` like '" . $this->escapeString(trim($search['file_name'])) . "%'";
        }
        return isset($a) ? ' and ' . implode(' and ', $a) : null;
    }


    public function getItemMediaFiles ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;
        $moduleId = isset($p['module_id']) && !empty($p['module_id']) ?
            $p['module_id'] : false;
        $itemId = isset($p['item_id']) && !empty($p['item_id']) ?
            $p['item_id'] : false;

        $query = "
            select
                t1.id,
                t2.sort_order,
                t2.overview_image
            from %PRE%media as t1
            left join
                %PRE%media_modules as t2 on t1.id = t2.media_id
            where
                t2.module_id = " . $this->escapeString($moduleId) . " and
                t2.item_id = " . $this->escapeString($itemId) . " and
                t2.project_id = " . $this->escapeString($projectId) . " and
                t1.deleted = 0
            order by
                t2.sort_order,
                t1.name";

        $d = $this->freeQuery($query);

        return isset($d) ? $d : array();

    }

    public function getConverterMatrixMedia ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;

        if (!$projectId) return false;

        $query = "
            select
                id as item_id,
                file_name,
                '' as original_name
            from
                %PRE%characteristics_states
            where
                file_name != '' and file_name is not null and
                project_id = " . $this->escapeString($projectId);

        $d = $this->freeQuery($query);

        return isset($d) ? $d : array();
    }

    public function getConverterKeystepsMedia ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;

        if (!$projectId) return false;

        $query = "
            select
                id as item_id,
                image as file_name,
                '' as original_name
            from
                %PRE%keysteps
            where
                image != '' and image is not null and
                project_id = " . $this->escapeString($projectId);

        $d = $this->freeQuery($query);

        return isset($d) ? $d : array();
    }

    public function getConverterKeychoicesMedia ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;

        if (!$projectId) return false;

        $query = "
            select
                keystep_id as item_id,
                choice_img as file_name,
                '' as original_name
            from
                %PRE%choices_keysteps
            where
                choice_img != '' and choice_img is not null and
                project_id = " . $this->escapeString($projectId);

        $d = $this->freeQuery($query);

        return isset($d) ? $d : array();
    }

    public function getConverterFreeModuleMedia ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;
        $moduleId = isset($p['module_id']) && !empty($p['module_id']) ?
            $p['module_id'] : false;

        if (!$projectId || $moduleId) return false;

        $query = "
            select
                id as item_id,
                module_id,
                image as file_name,
                '' as original_name
            from
                %PRE%free_module_pages
            where
                image != '' and image is not null and
                project_id = " . $this->escapeString($projectId) . " and
                module_id = " . $this->escapeString($moduleId);

        $d = $this->freeQuery($query);

        return isset($d) ? $d : array();
    }

    public function getConverterGlossaryMedia ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;

        if (!$projectId) return false;

        $query = "
            select
                glossary_id as item_id,
                id as media_id,
                original_name,
                file_name
            from
                %PRE%glossary_media
            where
                file_name != '' and
                file_name is not null and
                project_id = " . $this->escapeString($projectId);

        $d = $this->freeQuery($query);

        return isset($d) ? $d : array();
    }

    public function getConverterIntroductionMedia ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;

        if (!$projectId) return false;

        $query = "
            select
                page_id as item_id,
                original_name,
                file_name
            from
                %PRE%introduction_media
            where
                file_name != '' and
                file_name is not null and
                project_id = " . $this->escapeString($projectId);

        $d = $this->freeQuery($query);

        return isset($d) ? $d : array();
    }

    public function getConverterTaxonMedia ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;

        if (!$projectId) return false;

        $query = "
            select
                id as media_id,
                taxon_id as item_id,
                original_name,
                file_name
            from
                %PRE%media_taxon
            where
                file_name != '' and
                file_name is not null and
                project_id = " . $this->escapeString($projectId);

        $d = $this->freeQuery($query);

        return isset($d) ? $d : null;
    }


    public function getConvertedMediaCount ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;

        if (!$projectId) return false;

        $query = "
            select
                count(*) as total
            from
                %PRE%media_conversion_log
            where
                `media_id` > -1 and
                `new_file` != 'failed' and
                `project_id` = " . $this->escapeString($projectId);

        $d = $this->freeQuery($query);

        return isset($d) ? $d[0]['total'] : 0;
    }


    public function getConvertedFileName ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;
        $oldFile = isset($p['old_file']) && !empty($p['old_file']) ?
            $p['old_file'] : false;

        if (!$projectId || !$oldFile) return false;

        $query = "
            select
                `media_id`, `new_file`
            from
                %PRE%media_conversion_log
            where
                `media_id` > -1 and
                `new_file` != 'failed' and
                `old_file` = '" . $this->escapeString($oldFile) . "' and
                `project_id` = " . $this->escapeString($projectId);

        return $this->freeQuery($query);
    }

    public function getMediaItem ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;
        $module = isset($p['module']) && !empty($p['module']) ?
            $p['module'] : false;
        $itemId = isset($p['item_id']) && !empty($p['item_id']) ?
            $p['item_id'] : false;

        if (!$projectId || !$module || !$itemId) return false;

        $query = "
            select
                `media_id`
            from
                %PRE%media_conversion_log
            where
                `media_id` > -1 and
                `new_file` != 'failed' and
                `module` = '" . $this->escapeString($module) . "' and
                `item_id` = " . $this->escapeString($itemId) . " and
                `project_id` = " . $this->escapeString($projectId);

        $d = $this->freeQuery($query);

        return isset($d) ? $d[0]['media_id'] : false;

    }

}

?>