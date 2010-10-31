<?php

/*

	add rollover?

*/


class LoggingHelper
{

	private $_levels =	array(
		0 => 'MESSAGE',	// not an error
		1 => 'WARNING',	// non-fatal error
		2 => 'ERROR'	// fatal error
	);
	private $_fileName = false;
	private $_file = false;
	private $_level = 2;
	private $_lineTerminator = "\n";

	public function __destruct()
	{

		if ($this->_file) fclose($this->_file);

	}

	public function setLogFile($file)
	{

		if (!file_exists($file) && !touch($file)) {

			trigger_error(sprintf(_('Cannot create log file %s'),$file), E_USER_ERROR);

		} else {
		
			$this->_fileName = $file;

			$this->_file = fopen($this->_fileName,'a');

			if (!$this->_file) trigger_error(sprintf(_('Cannot open log file %s'),$this->_fileName), E_USER_ERROR);

		}

	}
	
	public function setLevel($level)
	{

		if (array_key_exists($level,$this->_levels)) {

			$this->_level = $level;

		} else {

			trigger_error(sprintf(_('Illegal log level: %s'),$level), E_USER_ERROR);

		}

	}

	public function write($msg,$severity=0)
	{

		if ($severity < $this->_level) return;

		if (!$this->_file) {
		
			trigger_error(sprintf(_('Log file not available for writing (%s)'),$this->_fileName), E_USER_ERROR);

			return false;
	
		} else {

			if (is_array($msg)) {

				foreach((array)$msg as $key => $val) {

					$this->writeLine($val,$severity);

				}

			} else {

				$this->writeLine($msg,$severity);

			}

			return true;

		}

	}

	private function writeLine($msg,$severity)
	{

		$d = debug_backtrace();
		
		$t = ($d[2]['function']=='log' ? $d[3] : $d[2]);
		
		$t = ($t['function']=='addError' ? $d[4] : $t);

		fwrite(
			$this->_file,
			date('r').
			' - '.
			$this->_levels[$severity].
			': '.
			$msg.
			' ['.
			$t['class'].$t['type'].$t['function'].
			//' ('.$t['file'].', l'.$t['line'].')'.
			']'.
			$this->_lineTerminator	
		);
	

	}


}