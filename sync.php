<?php
// Configure error reporting to maximum for CLI output.
error_reporting(E_ALL);
ini_set('display_errors', 1);
new Sync($argv);

/**
 * Class Sync
 */
class Sync {
	/** @var bool */
	private $debug = true;
	/** @var string  */
	private $RSYNCBIN = "/usr/bin/rsync";
	/** @var string  */
	private $SYNCDIRECTION = "down";
	/** @var string  */
	private $SYNCFOLDER = "";
	/** @var string  */
	private $CONFIGFOLDERPATH = "";
	/** @var string  */
	private $CONFIGFOLDERNAME = ".SyncConfig";
	/** @var \stdClass */
	private $CONFIG;

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
		if( !($this->SYNCDIRECTION = isset($arguments[1]) && in_array($arguments[1], array("up", "down")) ? $arguments[1] : false) ) {
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
		$this->log("Sync was set up with arguments: DIRECTION($this->SYNCDIRECTION) FOLDER($this->SYNCFOLDER)");

		//check config folder path
		$this->CONFIGFOLDERPATH = $this->getSyncConfigFolder();
		if(!$this->CONFIGFOLDERPATH) {
			$this->log("The Sync configuration folder($this->CONFIGFOLDERNAME) was not found!", true);
			die();
		}

		//check sync configuration
		$this->CONFIG = $this->getSyncConfiguration();
		if(!$this->CONFIG) {
			$this->log("No valid Sync configuration!", true);
			die();
		}
		$this->log("Sync config: " . json_encode($this->CONFIG));

		//add excludes file to config if any
	}



	/**
	 * Find and read the configuration file
	 * @return \stdClass|boolean
	 */
	private function getSyncConfiguration() {
		$answer = false;
		$configFile = $this->CONFIGFOLDERPATH."/config.json";
		if(file_exists($configFile)) {
			$config = json_decode(file_get_contents($configFile));
			if(json_last_error() == JSON_ERROR_NONE) {
				$answer = $config;
			} else {
				$this->log("Reading config.json failed: " . json_last_error_msg());
			}
		} else {
			$this->log("No config.json file found in configuration folder($this->CONFIGFOLDERPATH)!");
		}
		return($answer);
	}

	/**
	 * This method will start looking in the current $SYNCFOLDER and will climb up until it finds ".SyncConfig" folder
	 * @return string|boolean
	 */
	private function getSyncConfigFolder() {
		$answer = false;
		$currentFolder = $this->SYNCFOLDER;
		while (realpath($currentFolder."/..") != $currentFolder) {
			if(in_array($this->CONFIGFOLDERNAME, scandir($currentFolder))) {
				$answer = $currentFolder."/".$this->CONFIGFOLDERNAME;
				break;
			}
			$currentFolder = realpath($currentFolder."/..");
		}
		return($answer);
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