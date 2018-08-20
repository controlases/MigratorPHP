<?php


require_once("config.php");

class SerializeFile{

	public $doc = null;
	public $file = "";
	public $path = "";
	private $ext = "";
	private $content = "";
	private $view = "";
	public $finalContent = null;
	public $sql = [];

	const VALID_EXT = ["php", "html"];

	function __construct($dir = null){
		if(!$dir)	return;
		$tmp = explode(".", $dir);


		$this->path = $dir;


		$this->file = $tmp[0];
		$this->ext = $tmp[1];
		$this->view = $this->file;


		$this->content = file_get_contents(FOLDER."/".$this->path);
		$this->finalContent = $this->content;
	}


	function __init(){
		if(!$this->isValidFile())	return;
		$this->removeTags();
		$this->createDocument($this->content);
		$this->metas();
		
		
		$this->replaceImages();
		$this->replaceText();
		

		$this->getZoneFile($this->doc, QUERY_FILES);

		
		$this->serializeToBlade();

	}


	function createDocument($html){
		$this->doc = new DomDocument();
		$this->doc->loadHTML($html);
	}


	function getZoneFile($doc, $sel){

		if(empty($doc))	return false;	
		if(!empty($doc->documentElement))	$doc = $doc->documentElement;

		if(get_class($doc) != "DOMElement")	return;

		$arr = explode(" ", $sel);
		$aux =  $arr;
		$me = $aux[0];
		$attr = null;

		if(strpos($me, "#") !== false){
			$attr = "id";
		}elseif(strpos($me,".") !== false){
			$attr = "class";
		}
				
		if($attr)	$me = substr($me, 1);
		
		if(($attr &&  strrpos($doc->getAttribute($attr), $me) !== false ) || (!$attr && $doc->nodeType == $me)){
			$this->finalContent = $doc->C14N();
			
			unset($arr[0]);
			if(count($arr) == 0){
				return true;
			}
		}
		
		if($doc->hasChildNodes()){
			foreach ($doc->childNodes as  $node) {
				$status = $this->getZoneFile($node, implode(" ", $arr));
				if($status){
					return true;
				}
			}
		}
		
		
		return false;
	}


	function putInFile(){
		$this->fix_content();
		\file_put_contents(SAVE_IN."resources/views/".PROJECT_NAME.'/'.$this->file.".blade.php", $this->finalContent);
	}



	function serializeToBlade(){
	ob_start();?>
@extends('<?php echo PROJECT_NAME;?>.layout')
@section('content')
	<?php echo $this->finalContent;?>

@endsection

	<?php $this->finalContent = ob_get_clean();
	}





	function removeTags(){
		$content = $this->content;
		$pos = strpos($this->content, "<?php");
		

		if($pos !== false){
			$endPos = 0;
			
			for ($i=$pos; $i < strlen($content); $i++) { 
				$char = $content{$i};
				if($char == '?' && $content{$i+1} == ">"  && $content{$i-1} != "'"	){
					$endPos = $i;
					break;
				}	
			}	
			$this->content = substr($content, 0, $pos).substr($content, $endPos+2, strlen($content));
			$this->removeTags();
		}else{
			
			$this->content = str_replace(['&gt;', '&lt;', '.php', '??></textarea>'], ['>', '<', '', '?></textarea>'], $this->content);
		}
	}




	function replaceImages(){
		$imgs = $this->doc->getElementsByTagName("img");

		foreach ($imgs as $imgNode) {
			$src = $imgNode->getAttribute("src");
			$alt = $imgNode->getAttribute("alt");
			$class = $imgNode->getAttribute("class");
			$id = $imgNode->getAttribute("id");

			$key = $this->file.".".($id ? $id : uniqid());

			$imgx = "{!!\App\Config::key('".$key."', [
                            'type' => 'image',
                            'default' => asset('images/".PROJECT_NAME."/".$src."'), 
                            'class' => '".$class."',
                            'alt' => '".$alt."',
                        ])!!}";
			$aux = $this->doc->createElement("span");
			setInnerHTML($aux, $imgx);

			$imgNode->parentNode->replaceChild($aux, $imgNode);
		}
	}


	function replaceText(){
		

		$this->domTextReplace($this->doc);
	}



	function domTextReplace(DOMNode &$domNode ) {
	  if ( $domNode->hasChildNodes() ) {
	    $children = array();
	    foreach ( $domNode->childNodes as $child ) {
	      $children[] = $child;
	    }
	    foreach ( $children as $child ) {
	      if ( $child->nodeType === XML_TEXT_NODE ) {
	        $oldText = $child->wholeText;
	        $oldText = str_replace([PHP_EOL, "  "], ["", ""], $oldText);
	        $is_double = strpos($oldText, "'") !== false;


	        if(!empty($oldText)){
	        	$oldText = $is_double ? '"'.$oldText.'"' : "'".$oldText."'";
	        	$newText = PHP_EOL."{!!\App\Config::key('".$this->view.".".uniqid()."', ['default' => ".$oldText."])!!}".PHP_EOL;
            }else{
            	$newText = $oldText;
            }

	        
	        $newTextNode = $domNode->ownerDocument->createTextNode( $newText );
	        $domNode->replaceChild( $newTextNode, $child );
	      } else {
	        $this->domTextReplace( $child );
	      }
	    }
	  }
	}



	function declareRoute(){
		$fn = str_replace(["-", "."], ["_", "_"], $this->file);
		

		return  ["	Route::get('".$this->file."', '".CONTROLLER."@".$fn."')->name('".$fn."');".PHP_EOL, 
		"
		public function ".$fn."(){
			return view('".PROJECT_NAME.".".$this->view."');
		}
		"];
	}




	public function fix_content(){
		$this->finalContent = str_replace(["&gt;"], ">", $this->finalContent);
	}



	function isValidFile(){
		return (!empty($this->file) && !empty($this->content) && !empty($this->ext) && in_array($this->ext, self::VALID_EXT) && $this->file != "serialize");
	}


	function dd(){
		if(!$this->doc)	return;
		echo htmlentities($this->finalContent);
		die;
	}



	function metas(){
		foreach ($this->doc->getElementsByTagName("meta") as $metaNode) {
			$key = $metaNode->getAttribute("name");
			$content = $metaNode->getAttribute("content");
			$now = date("Y-m-d H:i:s");
			$this->sql[] = "<span style='color:#ed2133;'>INSERT INTO</span> tags (<span style='color:#e6dc74;'>key, section, value, created_at, updated_at</span>) <span style='color:#ed2133;'>VALUES</span> (<span style='color:#e6dc74;'>'$key', '{$this->view}' , '$content', '$now', '$now'</span>)";
		}
	}



}