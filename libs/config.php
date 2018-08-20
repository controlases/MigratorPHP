<?php
/**
*@author Juan David Restrepo
*@version 1.0.0 <2018-08-15>
*/

DEFINE("PROJECT_NAME", $_GET["name"]);
DEFINE("SAVE_IN", "/home/jochoar2/public_html/tmp/");
DEFINE("CONTROLLER", ucfirst(PROJECT_NAME)."Controller");
DEFINE("DOMAIN", PROJECT_NAME.".com");
DEFINE("QUERY_FILES", $_GET["el"]);
DEFINE("FOLDER", $_GET["folder"]);

function setInnerHTML($element, $html)
{
    $fragment = $element->ownerDocument->createDocumentFragment();
    $fragment->appendXML($html);
    while ($element->hasChildNodes())
        $element->removeChild($element->firstChild);
    $element->appendChild($fragment);
}