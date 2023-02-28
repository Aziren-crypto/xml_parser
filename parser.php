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



$xml = '<?xml version="1.0" encoding="UTF-8"?>
<dia:diagram xmlns:dia="http://www.lysator.liu.se/~alla/dia/">
  <dia:diagramdata>
    <dia:attribute name="background">
      <dia:color val="#ffffff"/>
    </dia:attribute>
    <dia:attribute name="pagebreak">
      <dia:color val="#000099"/>
    </dia:attribute>
    <dia:attribute name="paper">
      <dia:composite type="paper">
        <dia:attribute name="name">
          <dia:string>#A4#</dia:string>
        </dia:attribute>
        <dia:attribute name="tmargin">
          <dia:real val="2.8222000598907471"/>
        </dia:attribute>
        <dia:attribute name="bmargin">
          <dia:real val="2.8222000598907471"/>
        </dia:attribute>
        <dia:attribute name="lmargin">
          <dia:real val="2.8222000598907471"/>
        </dia:attribute>
        <dia:attribute name="rmargin">
          <dia:real val="2.8222000598907471"/>
        </dia:attribute>
        <dia:attribute name="is_portrait">
          <dia:boolean val="true"/>
        </dia:attribute>
        <dia:attribute name="scaling">
          <dia:real val="1"/>
        </dia:attribute>
        <dia:attribute name="fitto">
          <dia:boolean val="false"/>
        </dia:attribute>
      </dia:composite>
    </dia:attribute>
    <dia:attribute name="grid">
      <dia:composite type="grid">
        <dia:attribute name="width_x">
          <dia:real val="1"/>
        </dia:attribute>
        <dia:attribute name="width_y">
          <dia:real val="1"/>
        </dia:attribute>
        <dia:attribute name="visible_x">
          <dia:int val="1"/>
        </dia:attribute>
        <dia:attribute name="visible_y">
          <dia:int val="1"/>
        </dia:attribute>
        <dia:composite type="color"/>
      </dia:composite>
    </dia:attribute>
    <dia:attribute name="color">
      <dia:color val="#d8e5e5"/>
    </dia:attribute>
    <dia:attribute name="guides">
      <dia:composite type="guides">
        <dia:attribute name="hguides"/>
        <dia:attribute name="vguides"/>
      </dia:composite>
    </dia:attribute>
  </dia:diagramdata>
  <dia:layer name="Фон" visible="true" active="true">
    <dia:object type="Flowchart - Ellipse" version="0" id="O0">
      <dia:attribute name="obj_pos">
        <dia:point val="8.74327,8.09664"/>
      </dia:attribute>
      <dia:attribute name="obj_bb">
        <dia:rectangle val="8.69327,8.04664;12.2067,9.85337"/>
      </dia:attribute>
      <dia:attribute name="elem_corner">
        <dia:point val="8.74327,8.09664"/>
      </dia:attribute>
      <dia:attribute name="elem_width">
        <dia:real val="3.4134556320067424"/>
      </dia:attribute>
      <dia:attribute name="elem_height">
        <dia:real val="1.7067278160033705"/>
      </dia:attribute>
      <dia:attribute name="show_background">
        <dia:boolean val="true"/>
      </dia:attribute>
      <dia:attribute name="padding">
        <dia:real val="0.35355339059327379"/>
      </dia:attribute>
      <dia:attribute name="text">
        <dia:composite type="text">
          <dia:attribute name="string">
            <dia:string>##</dia:string>
          </dia:attribute>
          <dia:attribute name="font">
            <dia:font family="sans" style="0" name="Helvetica"/>
          </dia:attribute>
          <dia:attribute name="height">
            <dia:real val="0.80000000000000004"/>
          </dia:attribute>
          <dia:attribute name="pos">
            <dia:point val="10.45,9.145"/>
          </dia:attribute>
          <dia:attribute name="color">
            <dia:color val="#000000"/>
          </dia:attribute>
          <dia:attribute name="alignment">
            <dia:enum val="1"/>
          </dia:attribute>
        </dia:composite>
      </dia:attribute>
    </dia:object>
    <dia:object type="Flowchart - Box" version="0" id="O1">
      <dia:attribute name="obj_pos">
        <dia:point val="1,1"/>
      </dia:attribute>
      <dia:attribute name="obj_bb">
        <dia:rectangle val="0.95,0.95;3.05,6.05"/>
      </dia:attribute>
      <dia:attribute name="elem_corner">
        <dia:point val="1,1"/>
      </dia:attribute>
      <dia:attribute name="elem_width">
        <dia:real val="2"/>
      </dia:attribute>
      <dia:attribute name="elem_height">
        <dia:real val="5"/>
      </dia:attribute>
      <dia:attribute name="show_background">
        <dia:boolean val="true"/>
      </dia:attribute>
      <dia:attribute name="padding">
        <dia:real val="0.5"/>
      </dia:attribute>
      <dia:attribute name="text">
        <dia:composite type="text">
          <dia:attribute name="string">
            <dia:string>##</dia:string>
          </dia:attribute>
          <dia:attribute name="font">
            <dia:font family="sans" style="0" name="Helvetica"/>
          </dia:attribute>
          <dia:attribute name="height">
            <dia:real val="0.80000000000000004"/>
          </dia:attribute>
          <dia:attribute name="pos">
            <dia:point val="2,3.695"/>
          </dia:attribute>
          <dia:attribute name="color">
            <dia:color val="#000000"/>
          </dia:attribute>
          <dia:attribute name="alignment">
            <dia:enum val="1"/>
          </dia:attribute>
        </dia:composite>
      </dia:attribute>
    </dia:object>
    <dia:object type="Standard - Line" version="0" id="O2">
      <dia:attribute name="obj_pos">
        <dia:point val="3,2"/>
      </dia:attribute>
      <dia:attribute name="obj_bb">
        <dia:rectangle val="2.93131,1.93131;20.0689,30.0956"/>
      </dia:attribute>
      <dia:attribute name="conn_endpoints">
        <dia:point val="3,2"/>
        <dia:point val="20,30"/>
      </dia:attribute>
      <dia:attribute name="numcp">
        <dia:int val="1"/>
      </dia:attribute>
      <dia:attribute name="end_arrow">
        <dia:enum val="22"/>
      </dia:attribute>
      <dia:attribute name="end_arrow_length">
        <dia:real val="0.5"/>
      </dia:attribute>
      <dia:attribute name="end_arrow_width">
        <dia:real val="0.5"/>
      </dia:attribute>
    </dia:object>
    <dia:object type="Circuit - Vertical Fuse (European)" version="1" id="O3">
      <dia:attribute name="obj_pos">
        <dia:point val="3.45,14.3"/>
      </dia:attribute>
      <dia:attribute name="obj_bb">
        <dia:rectangle val="3.4,14.3;4.7,17.5"/>
      </dia:attribute>
      <dia:attribute name="meta">
        <dia:composite type="dict"/>
      </dia:attribute>
      <dia:attribute name="elem_corner">
        <dia:point val="3.45,14.3"/>
      </dia:attribute>
      <dia:attribute name="elem_width">
        <dia:real val="1.1999999999999993"/>
      </dia:attribute>
      <dia:attribute name="elem_height">
        <dia:real val="3.1999999500159744"/>
      </dia:attribute>
      <dia:attribute name="line_width">
        <dia:real val="0.10000000000000001"/>
      </dia:attribute>
      <dia:attribute name="line_colour">
        <dia:color val="#000000"/>
      </dia:attribute>
      <dia:attribute name="fill_colour">
        <dia:color val="#ffffff"/>
      </dia:attribute>
      <dia:attribute name="show_background">
        <dia:boolean val="true"/>
      </dia:attribute>
      <dia:attribute name="line_style">
        <dia:enum val="0"/>
        <dia:real val="1"/>
      </dia:attribute>
      <dia:attribute name="flip_horizontal">
        <dia:boolean val="false"/>
      </dia:attribute>
      <dia:attribute name="flip_vertical">
        <dia:boolean val="false"/>
      </dia:attribute>
      <dia:attribute name="subscale">
        <dia:real val="1"/>
      </dia:attribute>
    </dia:object>
  </dia:layer>
</dia:diagram>';

    

    $domObj = new xmlToArrayParser($xml);

    $domArr = $domObj->array;

    

    if($domObj->parse_error) echo $domObj->get_xml_error();

    else 
	{
		
		//print_r($domArr);
		/*echo '<pre>';
		print_r($domArr['dia:diagram']['dia:layer']['dia:object']);
		echo '</pre>';*/
		$arElems = [];
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
				$arEl['type'] = 'не определено';
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
					//echo '<div>elem_width '.$v2['attrib']['val'].'</div>';
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
			$arElems[] = $arEl;
		}
		/*echo '<pre>';
		print_r($arElems);
		echo '</pre>';*/
		foreach($arElems as $k => $v)
		{
			if($v['type'] == 'line')
			{

				echo '<script>
					function draw() {
						const canvas = document.querySelector(\'#canvas\');

						if (!canvas.getContext) {
							return;
						}
						const ctx = canvas.getContext(\'2d\');

						// set line stroke and line width
						ctx.strokeStyle = \'black\';
						ctx.lineWidth = 1;

						// draw a red line
						ctx.beginPath();
						ctx.moveTo('.($v['begin']['x']*20).', '.($v['begin']['x']*20).');
						ctx.lineTo('.($v['end']['x']*20).', '.($v['end']['x']*20).');
						ctx.stroke();

					}
					draw();
				</script>';
			}
			else
			{
				echo '<img src="img/'.$v['type'].'.png" style="position:absolute; width:'.($v['width']*20).'px; height:'.($v['height']*20).'px; top:'.($v['y']*20).'px; left:'.($v['x']*20).'px;">';
			}
		}
	}

//$domArr['dia:diagram']['dia:layer']
?>

