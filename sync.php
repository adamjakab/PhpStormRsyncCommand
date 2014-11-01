<?php
// Configure error reporting to maximum for CLI output.
error_reporting(E_ALL);
ini_set('display_errors', 1);
new Sync($argv);

/**
 * Class Sync
 */
class Sync {
	private $debug = true;
	private $RSYNCBIN = "/usr/bin/rsync";
	private $SYNCDIR = "down";
	private $SYNCFOLDER = "";

	/**
	 * @param array $arguments - the command line arguments
	 */
	public function __construct($arguments) {
		if(php_sapi_name() !== 'cli') {
			$this->log("Sync must be called from command line!");
			die();
		}

		if(!isset($arguments)) {
			$this->log("Parameters are missing!", true);
			$this->showHelp();
			die();
		}

		//check direction
		if( !($this->SYNCDIR = isset($arguments[1]) && in_array($arguments[1], array("up", "down")) ? $arguments[1] : false) ) {
			$this->log("Parameter DIRECTION must be one of (up|down)!", true);
			$this->showHelp();
			die();
		}

		//check folder
		if( !($this->SYNCFOLDER = isset($arguments[2]) && file_exists(realpath($arguments[2])) ? realpath($arguments[2]) : false) ) {
			$this->log("Parameter FOLDER is not set or it does not exist!", true);
			$this->showHelp();
			die();
		}
		$this->log("Sync was set up with arguments: DIRECTION($this->SYNCDIR) FOLDER($this->SYNCFOLDER)");


	}

	/**
	 * Show usage help - we need to show this independently from the $debug setting
	 */
	private function showHelp() {
		$debug = $this->debug;
		$this->debug = true;
		$this->log("The sync.php file needs to be called as: php sync.php DIRECTION FOLDER");
		$this->log("  DIRECTION - one of (up|down)");
		$this->log("  FOLDER - absolute path of the folder to syncronize");
		$this->debug = $debug;
	}

	/**
	 * @param string $msg
	 * @param boolean $important
	 */
	private function log($msg, $important=false) {
		if($this->debug || $important) {
			if($important) {
				$msg = "\033[01;31m$msg\033[0m";
			}
			echo $msg . "\n";
		}
	}
}