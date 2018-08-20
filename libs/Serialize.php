<?php
require_once("config.php");
require_once("SerializeFile.php");

Class Serialize{

	public $fileroutes = "";
	public $fileController = "";
	public $dir = "";
	public $files = [];
	public $sql = [];

	function __construct($dir){
		$this->dir = $dir;
	}


	function getFiles(){
		$gestor_dir = opendir($this->dir);
		$ficheros = [];
		while (false !== ($nombre_fichero = readdir($gestor_dir))) {
		    $ficheros[] = $nombre_fichero;
		}
		
		return $ficheros;
	}

	

	function each(){
		$files = $this->getFiles();
		$z = 0;
		$limit = 300;
		foreach ($files as $file) {
			$file = new SerializeFile($file);
			if(!$file->isValidFile()) continue;

			$file->__init();
			$this->sql = array_merge($this->sql, $file->sql);

			$fileDeclarations = $file->declareRoute();

			$this->fileroutes .= $fileDeclarations[0];
			$this->fileController .= $fileDeclarations[1];

			array_push($this->files, $file);
			if($z > $limit)	break;
			$z++;
		}
	}



	function writeFiles(){
		$this->createFolders();
		$this->fileroutes = "<?php

Route::group(['domain' => '".DOMAIN."', 'middleware' => 'project:".PROJECT_NAME."'], function(){
		".$this->fileroutes."
		});";

		$this->fileController = "<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;


class ".CONTROLLER."{
	".$this->fileController."
}
		";



		file_put_contents(SAVE_IN."/app/Http/Controllers/".CONTROLLER.".php", $this->fileController);
		file_put_contents(SAVE_IN.'/routes/'.PROJECT_NAME.'.php', $this->fileroutes);
		foreach ($this->files as $file) {
			$file->putInFile();
		}
	}


	function createFolders(){	
		//mkdir(SAVE_IN."routes");
		//mkdir(SAVE_IN."app/Http/Controllers");
		//mkdir(SAVE_IN."resources/views/".PROJECT_NAME);
		//chmod(SAVE_IN."resources/views/".PROJECT_NAME, 777);

	}

	function printSql(){
		return implode(';<br /><br />', $this->sql);
	}

}