<?PHP

	/**
	* This file generates some stats of the current game
	*
	*/

	// Gamepfad feststellen
	if ($_SERVER['argv'][1]!="")
	{
		$grd = $_SERVER['argv'][1];
	}
	else
	{
		$c=strrpos($_SERVER["SCRIPT_FILENAME"],"scripts/");
		if (stristr($_SERVER["SCRIPT_FILENAME"],"./")&&$c==0)
			$grd = "../";
		elseif ($c==0)
			$grd = ".";
		else
			$grd = substr($_SERVER["SCRIPT_FILENAME"],0,$c-1);
	}

	define("GAME_ROOT_DIR",$grd);

	/*******
	* Main *
	*******/

	// Initialisieren
	if (include(GAME_ROOT_DIR."/functions.php"))
	{
		include(GAME_ROOT_DIR."/conf.inc.php");
		dbconnect();
		if (!defined('CLASS_ROOT'))	
			define('CLASS_ROOT',GAME_ROOT_DIR.'/classes');
		
		$conf = get_all_config();
		include(GAME_ROOT_DIR."/def.inc.php");
		$nohtml=true;

		// Statistiken generieren und speichern
		Gamestats::generateAndSave(GAME_ROOT_DIR."/".GAMESTATS_FILE);
		
		// DB schliessen
		dbclose();
	}
	else
	{
		echo "Error: Could not include function file ".GAME_ROOT_DIR."/functions.php\n";
	}
?>