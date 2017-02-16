<?php

class Git {

	private $git_command="git";
	private $exec_path;
	private $repo_path;
	private $git_branch;
	private $git_commit;
	private $git_origin_commit_hash;
	private $git_tags;
	private $git_describe;

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
		$this->setOriginCommitHash();
		$this->setTags();
		$this->setDescribe();
	}

    public function getBranch()
    {
		return $this->git_branch;
    }

    public function getCommit()
    {
		return $this->git_commit;
    }
	
    public function getOriginCommitHash()
    {
		return $this->git_origin_commit_hash;
    }
	
    public function getTags()
    {
		return $this->git_tags;
    }
	
    public function getDescribe()
    {
		return $this->git_describe;
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
		$p=$this->exec_path . " rev-parse --abbrev-ref HEAD";
		$this->git_branch=trim(@shell_exec( $p ));
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
				$this->git_commit->hash_short=substr($this->git_commit->hash,0,7);
			}

			if (stripos(trim($val),'Date:')===0)
			{
				$this->git_commit->date=trim(substr($val,strlen('Date:')));
			}

			if (stripos(trim($val),'Author:')===0)
			{
				$this->git_commit->auhor=trim(substr($val,strlen('Author:')));
			}
		}
	}

	private function setOriginCommitHash()
	{
		if ( empty($this->git_branch) ) return;
		$p=$this->exec_path . " rev-parse origin/" . $this->git_branch;
		$this->git_origin_commit_hash=trim(@shell_exec( $p ));
	}

	private function setTags()
	{
		if ( empty($this->git_branch) ) return;
		$p=$this->exec_path . " show-ref --tags --dereference";
		foreach(explode("\n",trim(@shell_exec( $p ))) as $val) 
		{
			$d=explode(" ",$val);
			if ($d[0]==$this->git_commit->hash)
			{
				$this->git_tags[]=preg_replace(["/^refs\/tags\//","/\^\{\}(\s*)$/"],"",$d[1]);
			}
		}
	}

	private function setDescribe()
	{
		if ( empty($this->git_branch) ) return;
		$p=$this->exec_path . " describe";
		$this->git_describe=trim(@shell_exec( $p ));
	}
}

