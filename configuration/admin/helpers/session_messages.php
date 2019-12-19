<?php

class SessionMessages
{
	public function setMessage($m=null)
	{
		if (empty($m))
		{
			unset($_SESSION['messagehelper']);
		}
		else
		{
			if (is_array($m))
			{
				array_merge((array)$_SESSION['messagehelper'],$m);
			}
			else
			{
				$_SESSION['messagehelper'][]=$m;
			}
		}
	}

	public function getMessages($keep=false)
	{
		$d=@$_SESSION['messagehelper'];
		if (!$keep) $this->setMessage(null);
		return $d;
	}
}


