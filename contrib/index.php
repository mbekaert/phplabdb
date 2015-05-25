<?php
/** index.php // mod:PhD comics (www.phdcomics.com) **/
ob_start("ob_gzhandler");
session_start();
require_once('includes/main.inc');

class XMLParser {
  var $stack = array();
  function startTag($parser, $name, $attrs) {
    $tag = array('name'=>$name,'attrs'=>$attrs);
    array_push($this->stack,$tag);
  }
  function cdata($parser, $cdata) {
    if(trim($cdata)) {
      if(isset($this->stack[count($this->stack)-1]['cdata'])) {
        $this->stack[count($this->stack)-1]['cdata'].=$cdata;
      } else {
       $this->stack[count($this->stack)-1]['cdata']=$cdata;
	  }
    }
  }
  function endTag($parser, $name) {
   $this->stack[count($this->stack)-2]['children'][] = $this->stack[count($this->stack)-1];
   array_pop($this->stack);
  }
}

/** To read XML file and parse it to an array **/
function readXML($file) {
  if (($file=file_get_contents($file))!==false) {
    $xml_parser = xml_parser_create();
    $my_parser = new XMLParser();
    xml_set_object($xml_parser,$my_parser);
    xml_set_element_handler($xml_parser, "startTag", "endTag");
    xml_set_character_data_handler($xml_parser, "cdata");
    $data = xml_parse($xml_parser,$file);
    if(!$data) return false;
    xml_parser_free($xml_parser);
    return $my_parser->stack[0];
  } else {
    return false;
  }
} 

head('home');
?>
         <div class="items">
<?php print (isset($config['organisation']['welcome'])?$config['organisation']['welcome']:'');
      if(($rss=readXML('http://www.phdcomics.com/gradfeed.php')) && ($rss['name']=='RSS') && isset($rss['children'])) {
         foreach($rss['children'] as $channel) {
           if(($channel['name']=='CHANNEL') && isset($channel['children'])) {
             foreach($channel['children'] as $item) {
               if(($item['name']=='ITEM') && isset($item['children'])) {
                 foreach($item['children'] as $key) {
                   switch ($key['name']) {
                     case 'TITLE':
                       if(strpos($key['cdata'],'PHD comic:')===false) break 2;
                       $new['title']=$key['cdata'];
                       break;
                     case 'LINK':
                       $new['url']=$key['cdata'];
                       break;
                     case 'DESCRIPTION':
                       preg_match('/(http:\/\/www\.phdcomics\.com\/comics\/archive\/phd\d+s\.gif)/',$key['cdata'],$matches);
                       $new['img']=$matches[1];
                       break;
                   }
                 }
                 if ( isset( $new['img'] ) ) break 2;
               }
             }
           }
         }
if ( isset( $new['img'] ) ) print '<div class="news"><a title="'.$new['title'].'" href="'.$new['url'].'"><img src="'.$new['img'].'" alt="'.$new['title'].'" /></a><br />'.$new['title'].'</div>';
      }
?>
         </div>
<?php
foot();
?>