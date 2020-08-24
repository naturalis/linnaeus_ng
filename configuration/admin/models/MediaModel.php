<?php /** @noinspection PhpMissingParentCallMagicInspection */
include_once (__DIR__ . "/AbstractModel.php");

final class MediaModel extends AbstractModel
{

    private $metadataFields = array();

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

    public function setMetadataFields ($f) {
        $this->metadataFields = $f;
    }

    // Alternative for Table->_get as this crashed on large result sets
    public function getAllMediaFiles ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;
        $sort = isset($p['sort']) && !empty($p['sort']) ?
            $p['sort'] : 'name';

        if (!$projectId) return false;

        $query = '
            select
                `rs_id`,
                `id`,
                `title`,
                `name`,
                `mime_type`,
                `height`,
                `width`,
                `rs_original`,
                `last_change`,
                `rs_thumb_small`,
                `rs_thumb_medium`,
                `rs_thumb_large`
            from
                %PRE%media
            where
                `deleted` = 0 and
                `project_id` = ' . $this->escapeString($projectId) . '
            order by `' . $this->escapeString($sort) . '`';

        return $this->freeQuery($query);
    }

    public function search ($p)
    {
        $search = isset($p['search']) && $p['search'] != '' ?
            $p['search'] : false;
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;
        $sort = isset($p['sort']) && !empty($p['sort']) ?
            $p['sort'] : 'name';
        $searchType = isset($p['search_type']) && !empty($p['search_type']) &&
            in_array($p['search_type'], array('and', 'or')) ? $p['search_type'] : 'and';
        $limit = isset($p['limit']) && !empty($p['limit']) ? $p['limit'] : false;

        // Cast search to appropriate array if it is a string
        if ($search && !is_array($search)) {
            $this->castSearchStringToArray($search);
            // Bit of a hack but whatever
            $groupBy = 't1.`id`';
        }

        if (!isset($search['metadata']) && !isset($search['tags']) && !isset($search['file_name']) || !$projectId) {
            return false;
        }

        $query = '
            select
                t1.`rs_id`,
                t1.`id`,
                t1.`title`,
                t1.`name`,
                t1.`mime_type`,
                t1.`height`,
                t1.`width`,
                t1.`rs_original`,
                t1.`last_change`,
                t1.`rs_thumb_small`,
                t1.`rs_thumb_medium`,
                t1.`rs_thumb_large`
            from
                %PRE%media as t1 ' .
        ($this->arrayHasData($search['metadata']) ? '
            left join
                %PRE%media_metadata as t2
                on t1.`id` = t2.`media_id` ' : ''
        ) .
        ($this->arrayHasData($search['tags']) ? '
            left join
                %PRE%media_tags as t3
                on t1.`id` = t3.`media_id` ' : '') . '
            where
                t1.`deleted` = 0 and
                t1.`project_id` = ' . $this->escapeString($projectId) .
                $this->appendSearchWhere($search, $searchType) .
        (isset($groupBy) ? '
            group by ' . $groupBy : '') . '
            order by t1.`' .  $sort  . '`' .
        ($limit ? '
            limit ' . $limit : ''
        );

        return $this->freeQuery($query);
    }

    private function castSearchStringToArray (&$search) {
        $v = $search;
        foreach ($this->metadataFields as $f) {
            $meta[$f] = $v;
        }
        $search = array(
            'metadata' => $meta,
            'tags' => array($v),
            'file_name' => $v
        );
    }

    private function appendSearchWhere ($search, $searchType = 'and') {
        if ($this->arrayHasData($search['metadata'])) {
            foreach ($search['metadata'] as $k => $v) {
                if ($v != '') {
                    $a[] = "t2.`sys_label` = '" . $this->escapeString(trim($k)) .
                        "' and t2.`metadata` like '%" . $this->escapeString(trim($v)) . "%'";
                }
            }
        }
        if ($this->arrayHasData($search['tags'])) {
            foreach ($search['tags'] as $tag) {
                $a[] = "t3.`tag` like '" . $this->escapeString(trim($tag)) . "%'";
            }
        }
        if ($search['file_name'] != '') {
            $a[] = "t1.`name` like '%" . $this->escapeString(trim($search['file_name'])) . "%'";
        }
        return isset($a) ? ' and (' . implode(' ' . $searchType . ' ', $a) . ')' : null;
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
                t1.name as file_name,
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
        /* Exclude files containing / in file name; these have already been converted */

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
                image != ''
                and image is not null
                and image not like '%/%'
                and project_id = " . $this->escapeString($projectId);

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
                id as item_id,
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

        if (!$projectId || !$moduleId) return false;

        $query = "
            select
                id as item_id,
                module_id,
                image as file_name,
                '' as original_name
            from
                %PRE%free_modules_pages
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
                file_name,
                overview_image,
                sort_order
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
                `error` is null and
                `project_id` = " . $this->escapeString($projectId);

        $d = $this->freeQuery($query);

        return isset($d) ? $d[0]['total'] : 0;
    }


    public function getMediaId ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;
        $oldFile = isset($p['old_file']) && !empty($p['old_file']) ?
            $p['old_file'] : false;

        if (!$projectId || !$oldFile) return false;

        $query = "
            select
                `media_id`,
                `new_file`
            from
                %PRE%media_conversion_log
            where
                `media_id` > -1 and
                `new_file` != 'failed' and
                `old_file` = '" . $this->escapeString($oldFile) . "' and
                `project_id` = " . $this->escapeString($projectId);

        $d = $this->freeQuery($query);

        return isset($d) ? $d[0] : false;
    }

    public function getMediaConversionId ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;
        $module = isset($p['module']) && !empty($p['module']) ?
            $p['module'] : false;
        $itemId = isset($p['item_id']) && !empty($p['item_id']) ?
            $p['item_id'] : false;
        $mediaId = isset($p['media_id']) && !empty($p['media_id']) ?
            $p['media_id'] : false;

        if (!$projectId || !$module || !$itemId || !$mediaId) return false;

        $query = "
            select
                `id`
            from
                %PRE%media_conversion_log
            where
                `media_id` = " . $this->escapeString($mediaId) . " and
                `module` = '" . $this->escapeString($module) . "' and
                `item_id` = " . $this->escapeString($itemId) . " and
                `project_id` = " . $this->escapeString($projectId);

        $d = $this->freeQuery($query);

        return isset($d) ? $d[0]['id'] : false;
    }

    public function getInternalMediaLinks ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $this->escapeString($p['project_id']) : false;
        $table = isset($p['table']) && !empty($p['table']) ?
            $this->escapeString($p['table']) : false;
        $column = isset($p['column']) && !empty($p['column']) ?
            $this->escapeString($p['column']) : false;

        if (!$projectId || !$table || !$column) return false;

        $query = "
            select
            	`id`, `" . $column . "` as content
            from
            	%PRE%" . $table . "
            where
            	$column REGEXP '../../../shared/media/project/" .
            	   str_pad($projectId, 4, "0", STR_PAD_LEFT) . "/(.*).(jpg|mp3|mp4)'";

        $d = $this->freeQuery($query);

        return isset($d) ? $d : false;
    }

    public function updateInternalMediaLinks ($p)
    {
        $id = isset($p['id']) && !empty($p['id']) ?
            $this->escapeString($p['id']) : false;
        $content = isset($p['content']) && !empty($p['content']) ?
            $this->escapeString($p['content']) : false;
        $table = isset($p['table']) && !empty($p['table']) ?
            $this->escapeString($p['table']) : false;
        $column = isset($p['column']) && !empty($p['column']) ?
            $this->escapeString($p['column']) : false;

        $query = "update $table set $column = '$content' where id = $id";

        return $this->freeQuery($query);
    }

    public function replaceInternalMediaLinks ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $this->escapeString($p['project_id']) : false;
        $table = isset($p['table']) && !empty($p['table']) ?
            $this->escapeString($p['table']) : false;
        $column = isset($p['column']) && !empty($p['column']) ?
            $this->escapeString($p['column']) : false;
        $oldMedia = isset($p['old_media']) && !empty($p['old_media']) ?
            $this->escapeString($p['old_media']) : false;
        $newMedia = isset($p['new_media']) && !empty($p['new_media']) ?
            $this->escapeString($p['new_media']) : false;

        $path = 'onclick="showMedia(\'%s\',\'%s\');';
        $oldUrl = sprintf($path, $oldMedia['rs_original'], $oldMedia['name']);
        $newUrl = sprintf($path, $newMedia['rs_original'], $newMedia['name']);

        $query = "
            update
                %PRE%" . $table . "
            replace(`$column`, '$oldUrl', '$newUrl')
            where
                `$column` like '%$oldUrl%' and
                `project_id` = $projectId";

        $this->freeQuery($query);
    }

    public function getOldStyleTaxonMedia ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $this->escapeString($p['project_id']) : false;
        $taxonId = isset($p['taxon_id']) && !empty($p['taxon_id']) ?
            $this->escapeString($p['taxon_id']) : false;

        $q = "
            select
                file_name
            from
                %PRE%media_taxon
            where
                project_id = $projectId and
                taxon_id = $taxonId";

        return $this->freeQuery($q);
    }

    public function fixKeystepMedia ($projectId)
    {
        // First test if we should proceed (in case script is repeated)
        // If the test returns > 0 keysteps should be fixed
        $q = '
            select
                count(1) as test
            from
                %PRE%keysteps
            where
                `image` not like "http%"
                and project_id = ' . $projectId;
        $d = $this->freeQuery($q);

        if (isset($d) && $d[0]['test'] === 0) {
            return false;
        }

        $q = '
            update
                %PRE%keysteps as t1
            left join
                %PRE%media_modules as t2 on t1.id = t2.item_id
            left join
                %PRE%media as t3 on t2.media_id = t3.id
            left join
                %PRE%modules as t4 on t2.module_id = t4.id
            set t1.image = t3.rs_original
            where
                t4.controller = "key"
                and t1.id = t2.item_id
                and t1.project_id = ' . $projectId;

        $this->freeQuery($q);

        $q = '
            delete
                t1
            from
                %PRE%media_modules as t1
            left join
                %PRE%modules as t2 on t1.module_id = t2.id
            where
                t2.controller = "key"
                and t1.project_id = ' . $projectId;

        $this->freeQuery($q);

        return true;
    }

}

?>
