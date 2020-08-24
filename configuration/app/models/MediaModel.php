<?php
include_once (__DIR__ . "/AbstractModel.php");

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

        // @TODO: verify the expression! Hard to understand, may contain bug, rewrite or fix.
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
                t1.`project_id` = ' . $projectId .
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
            from media as t1
            left join
                media_modules as t2 on t1.id = t2.media_id
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

    public function getOverview ($p)
    {
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;
        $moduleId = isset($p['module_id']) && !empty($p['module_id']) ?
            $p['module_id'] : false;
        $itemId = isset($p['item_id']) && !empty($p['item_id']) ?
            $p['item_id'] : false;

        $query = "
            select
                t2.rs_original
            from
                media_modules as t1
            left join
                media as t2 on t1.media_id = t2.id
            where
                t1.overview_image = 1 and
                t1.module_id = " . $this->escapeString($moduleId) . " and
                t1.project_id = " . $this->escapeString($projectId) . " and
                t1.item_id = " . $this->escapeString($itemId);

        $d = $this->freeQuery($query);

        return isset($d) ? $d[0]['rs_original'] : false;

    }
}

?>