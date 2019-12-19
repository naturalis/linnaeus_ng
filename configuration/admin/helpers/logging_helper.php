<?php
/*

	take note: logger PREPENDS, does not APPEND!

*/
class LoggingHelper
{

	private $_levels =	array(
		0 => 'MESSAGE',	// not an error
		1 => 'WARNING',	// non-fatal error
		2 => 'ERROR'	// fatal error
	);
	private $_fileName = false;
	private $_level = 2;
	private $_lineTerminator = "\n";
	private $_source = false;
	private $_maxfilesize = 10000000;
	private $_caveat = '

welcome to the end!
this logger prepends, rather than appends, so the latest log lines are at the top of the file.
on linux, use the \'less\' or \'head\' commands to see the beginning of the file.

';

	public function __destruct()
	{

	}

	public function setLogFile($file)
	{

		if (!file_exists($file) && !touch($file)) {

			trigger_error(sprintf(_('Cannot create log file %s'),$file), E_USER_ERROR);

		} else {
		
			$this->_fileName = $file;

			// check file accessibility
			$fp = fopen($this->_fileName,'r');
			
			if (!$fp)
				trigger_error(sprintf(_('Cannot open log file %s'),$this->_fileName), E_USER_ERROR);
			else
				fclose($fp);

		}

	}
	

	public function setTruncateLength($length) // in bytes
	{

		if ($length===false)
			$this->_maxfilesize = $length; // switch off truncating
		
		if (!is_numeric($length) || $length < 999999) // minimum 1mb
			return;
		
		$this->_maxfilesize = $length; // set trunc length
		
	}


	public function setLevel($level)
	{

		if (array_key_exists($level,$this->_levels)) {

			$this->_level = $level;

		} else {

			trigger_error(sprintf(_('Illegal log level: %s'),$level), E_USER_ERROR);

		}

	}

	public function log($msg,$severity=0,$source=false)
	{
	
		if ($source) $this->_source = $source;
	
		$this->write($msg,$severity);
	
	}

	public function write($msg,$severity=0,$source=false)
	{

		if ($source) $this->_source = $source;

		if ($severity < $this->_level) return;

		if (is_array($msg)) {

			foreach((array)$msg as $key => $val)
				$this->writeLine($val,$severity);

		} else {

			$this->writeLine($msg,$severity);

		}

		return true;

	}

	private function writeLine($msg,$severity)
	{

		if (!$this->_source) {

			$d = debug_backtrace();
			
			$t = ($d[2]['function']=='log' ? $d[3] : $d[2]);
			
			$t = ($t['function']=='addError' ? $d[4] : $t);
			
			$t = $t['class'].$t['type'].$t['function'];

		} else {
		
			$t = $this->_source;
		
		}

		$line = 
			date('r').
			' - '.
			$this->_levels[$severity].
			': '.
			$msg.
			' ['.
			$t.
			//' ('.$t['file'].', l'.$t['line'].')'.
			']'.
			$this->_lineTerminator;

			
			if ($this->_maxfilesize)
				$line .= file_get_contents($this->_fileName, NULL, NULL, 0, $this->_maxfilesize);
			else
				$line .= file_get_contents($this->_fileName);

			file_put_contents($this->_fileName, $line.(substr($line,-50)==substr($this->_caveat,-50)?'':$this->_caveat));

	}
}