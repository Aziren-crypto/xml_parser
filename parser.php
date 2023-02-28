<canvas id="canvas" height="400" width="1000" style="top:0; left:0; position: absolute;">
</canvas>
<?
class xmlToArrayParser {

  /** The array created by the parser can be assigned to any variable: $anyVarArr = $domObj->array.*/

  public  $array = array();

  public  $parse_error = false;

  private $parser;

  private $pointer;

  

  /** Constructor: $domObj = new xmlToArrayParser($xml); */

  public function __construct($xml) {

    $this->pointer =& $this->array;

    $this->parser = xml_parser_create("UTF-8");

    xml_set_object($this->parser, $this);

    xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);

    xml_set_element_handler($this->parser, "tag_open", "tag_close");

    xml_set_character_data_handler($this->parser, "cdata"); 

    $this->parse_error = xml_parse($this->parser, ltrim($xml))? false : true;

  }

  

  /** Free the parser. */

  public function __destruct() { xml_parser_free($this->parser);}



  /** Get the xml error if an an error in the xml file occured during parsing. */

  public function get_xml_error() {

    if($this->parse_error) { 

      $errCode = xml_get_error_code ($this->parser);

      $thisError =  "Error Code [". $errCode ."] \"<strong style='color:red;'>" . xml_error_string($errCode)."</strong>\", 

                            at char ".xml_get_current_column_number($this->parser) . " 

                            on line ".xml_get_current_line_number($this->parser)."";

    }else $thisError = $this->parse_error;

    return $thisError;

  }

  

  private function tag_open($parser, $tag, $attributes) {

    $this->convert_to_array($tag, 'attrib');

    $idx=$this->convert_to_array($tag, 'cdata'); 

    if(isset($idx)) { 

      $this->pointer[$tag][$idx] = Array('@idx' => $idx,'@parent' => &$this->pointer);

      $this->pointer =& $this->pointer[$tag][$idx];

    }else {

      $this->pointer[$tag] = Array('@parent' => &$this->pointer);

      $this->pointer =& $this->pointer[$tag];

    }

    if (!empty($attributes)) { $this->pointer['attrib'] = $attributes; }

  }



  /** Adds the current elements content to the current pointer[cdata] array. */

  private function cdata($parser, $cdata) { $this->pointer['cdata'] = trim($cdata); }



  private function tag_close($parser, $tag) {

    $current = & $this->pointer;

    if(isset($this->pointer['@idx'])) {unset($current['@idx']);}

    

    $this->pointer = & $this->pointer['@parent'];

    unset($current['@parent']);

    

    if(isset($current['cdata']) && count($current) == 1) { $current = $current['cdata'];}

    else if(empty($current['cdata'])) {unset($current['cdata']);}

  }

  

  /** Converts a single element item into array(element[0]) if a second element of the same name is encountered. */

  private function convert_to_array($tag, $item) { 

    if(isset($this->pointer[$tag][$item])) { 

      $content = $this->pointer[$tag];

      $this->pointer[$tag] = array((0) => $content);

      $idx = 1;

    }else if (isset($this->pointer[$tag])) { 

      $idx = count($this->pointer[$tag]); 

      if(!isset($this->pointer[$tag][0])) { 

        foreach ($this->pointer[$tag] as $key => $value) {

            unset($this->pointer[$tag][$key]);

            $this->pointer[$tag][0][$key] = $value;

    }}}else $idx = null;

    return $idx;

  }

}


$xml = file_get_contents('diagramm_test.dia');
//$xml = file_get_contents('test_new.dia');
$domObj = new xmlToArrayParser($xml);

$domArr = $domObj->array;



if($domObj->parse_error) echo $domObj->get_xml_error();

else 
{
	$arElems = [];
	$arLine = [];
	foreach($domArr['dia:diagram']['dia:layer']['dia:object'] as $k => $v)
	{
		$arEl = [];
		switch ($v['attrib']['type']) {
		  case 'Flowchart - Ellipse':
			$arEl['type'] = 'circle';
			break;
		  case 'Flowchart - Box':
			$arEl['type'] = 'square';
			break;
		  case 'Standard - Line':
			$arEl['type'] = 'line';
			break;
		  case 'Circuit - Vertical Fuse (European)':
			$arEl['type'] = 'cond';
			break;
		  default:
			$arEl['type'] = $v['attrib']['type'];
		}
		foreach($v['dia:attribute'] as $k2 => $v2)
		{
			if($v2['attrib']['name'] == 'elem_corner')
			{
				$arrCoord = explode(',', $v2['dia:point']['attrib']['val']);
				$arEl['x'] = $arrCoord[0];
				$arEl['y'] = $arrCoord[1];
			}
			elseif($v2['attrib']['name'] == 'elem_width')
			{
				$arEl['width'] = $v2['dia:real']['attrib']['val'];
				//$v2['dia:point']
				/*echo '<pre>';
				print_r($v2);
				echo '</pre>';*/
			}
			elseif($v2['attrib']['name'] == 'elem_height')
			{
				$arEl['height'] = $v2['dia:real']['attrib']['val'];
			}
			elseif($v2['attrib']['name'] == 'conn_endpoints')
			{
				$arrCoord = explode(',', $v2['dia:point'][0]['attrib']['val']);
				$arEl['begin'] = ['x' => $arrCoord[0], 'y' => $arrCoord[1]];
				
				$arrCoord = explode(',', $v2['dia:point'][1]['attrib']['val']);
				$arEl['end'] = ['x' => $arrCoord[0], 'y' => $arrCoord[1]];
			}	
		}
		if($arEl['type'] == 'line')
		{
			$arLine[] = $arEl;
		}
		else
		{
			$arElems[] = $arEl;
		}
	}
	/*echo '<pre>';
	print_r($arElems);
	echo '</pre>';
	
	echo '<pre>';
	print_r($arLine);
	echo '</pre>';*/
	
	$i = 0;
	foreach($arElems as $k => $v)
	{
		echo '<img src="img/'.$v['type'].'.png" style="position:absolute; width:'.($v['width']*20).'px; height:'.($v['height']*20).'px; top:'.($v['y']*20).'px; left:'.($v['x']*20).'px;">';
	}
			echo '<script>
				function draw() {
					const canvas = document.querySelector(\'#canvas\');

					if (!canvas.getContext) {
						return;
					}
					const ctx = canvas.getContext(\'2d\');

					ctx.strokeStyle = \'black\';
					ctx.lineWidth = 1;

					';
					foreach($arLine as $k => $v){
						echo 'ctx.moveTo('.($v['begin']['x']*20).', '.($v['begin']['y']*20).');
						ctx.lineTo('.($v['end']['x']*20).', '.($v['end']['y']*20).');
						ctx.stroke();';
					}
				echo '}
				draw();
			</script>';
}
?>

