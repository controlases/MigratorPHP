<?php
include("libs/config.php");
include("libs/Serialize.php");

if(!empty($_GET)){
	$view = str_replace("-", "", ucfirst(PROJECT_NAME));
	$data= [];
	$serialize = new Serialize($_GET["folder"]);
	$serialize->each();
	$data["sql"] = $serialize->printSql();
	$serialize->writeFiles();

	$data["output_directory"] = SAVE_IN;
	$data["status"] = "ok";
	$data["proccesed"] = count($serialize->files);

	$data["routeboot"] = '$this->map'.$view.'Routes();';
	$data["routeservice"] = 'protected function map'.$view.'Routes()
    {
        Route::middleware("web")
             ->namespace($this->namespace)
             ->group(base_path("routes/'.PROJECT_NAME.'.php"));
    }';

	echo json_encode($data);
}