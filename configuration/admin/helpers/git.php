<?php

class Git {

	private $git_command="git";
	private $exec_path;
	private $repo_path;
	private $git_branch;
	private $git_commit;

    public function __construct ()
    {
		$this->git_commit = new stdClass;
	}

    public function setRepoPath( $path )
    {
		$this->repo_path=str_replace('\\','/',realpath($path)) . "/";
    }

    public function setGitExe( $path )
    {
		$this->git_command=$path;
    }

    public function setData()
	{
		$this->setExecPath();
		$this->setBranch();
		$this->setCommit();
	}

    public function getBranch()
    {
		return $this->git_branch;
    }

    public function getCommit()
    {
		return $this->git_commit;
    }
	
	private function setExecPath()
	{
		$this->exec_path=
			$this->git_command . " " .
			"--git-dir=" . $this->repo_path . ".git " .
			"--work-tree=" . $this->repo_path . " "
			;
	}

	private function setBranch()
	{
		$p=$this->exec_path . " branch";
		$f=explode("\n",@shell_exec( $p ));
		
		foreach((array)$f as $val)
		{
			if (strpos(trim($val),'*')===0)
			{
				$this->git_branch=ltrim($val,'* ');
				break;
			}
		}
	}

	private function setCommit()
	{
		$p=$this->exec_path . " log -1";
		$f=explode("\n",@shell_exec( $p ));

		foreach((array)$f as $val)
		{
			if (stripos(trim($val),'commit')===0)
			{
				$this->git_commit->hash=trim(substr($val,strlen('commit')));
				$this->git_commit->hash_short=substr($this->git_commit->hash,-7);
			}
			if (stripos(trim($val),'Date:')===0)
			{
				$this->git_commit->date=trim(substr($val,strlen('Date:')));
			}
		}
	}
}

