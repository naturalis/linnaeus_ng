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
        $search = isset($p['search']) && !empty($p['search']) ?
            $p['search'] : false;
        $projectId = isset($p['project_id']) && !empty($p['project_id']) ?
            $p['project_id'] : false;
        $sort = isset($p['sort']) && !empty($p['sort']) ?
            $p['sort'] : false;

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
                    $a[] = "t2.`sys_label` = '" . $this->escapeString($k) .
                        "' and t2.`metadata` like '" . $this->escapeString($v) . "%'";
                }
            }
        }
        if ($this->arrayHasData($search['tags'])) {
            foreach ($search['tags'] as $tag) {
                $a[] = "t3.`tag` like '" . $this->escapeString($tag) . "%'";
            }
        }
        if ($search['file_name'] != '') {
            $a[] = "t1.`name` like '" . $this->escapeString($search['file_name']) . "%'";
        }
        return isset($a) ? ' and ' . implode(' and ', $a) : null;
    }

}

?>