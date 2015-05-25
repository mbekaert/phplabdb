<?php
class XMLParser {
  var $stack = array();
  function startTag($parser, $name, $attrs) {
    $tag = array('name' => $name, 'attrs' => $attrs);
    array_push($this->stack, $tag);
  }
  function cdata($parser, $cdata) {
    if (trim($cdata))
      if (isset($this->stack[count($this->stack)-1]['cdata'])) {
        $this->stack[count($this->stack)-1]['cdata'] .= $cdata;
      }else {
        $this->stack[count($this->stack)-1]['cdata'] = $cdata;
      }
    }
    function endTag($parser, $name) {
      $this->stack[count($this->stack)-2]['children'][] = $this->stack[count($this->stack)-1];
      array_pop($this->stack);
    }
  }

  function readXML($buffer) {
    $xml_parser = xml_parser_create();
    $my_parser = new XMLParser();
    xml_set_object($xml_parser, $my_parser);
    xml_set_element_handler($xml_parser, "startTag", "endTag");
    xml_set_character_data_handler($xml_parser, "cdata");
    $data = xml_parse($xml_parser, $buffer);
    if (!$data)
      return false;
    xml_parser_free($xml_parser);
    return $my_parser->stack[0];
  }

  function deeptree($subtree, $deeper, $i = 0) {
    $node = trim($deeper[$i]);
    if (!isset($subtree[$node])) {
      $subtree[$node] = array();
    }
    if (isset($deeper[$i + 1])) {
      $subtree[$node] = deeptree($subtree[$node], $deeper, $i + 1);
    }
    return $subtree;
  }

  function compacttree ($subtree) {
    if (count($subtree) == 0) {
      return false;
    }elseif (count($subtree) == 1) {
      if (count($subtree[key($subtree)]) > 1) {
        $subtree[key($subtree)] = compacttree($subtree[key($subtree)]);
        return $subtree;
      }else {
        return ((($tmptree = compacttree($subtree[key($subtree)])) !== false)?$tmptree:key($subtree));
      }
    }else {
      foreach($subtree as $key => $value) {
        if (count($value) > 1) {
          $subtree[$key] = compacttree($value);
        }else {
          if (($tmptree = compacttree($value)) !== false) {
            $subtree[$key] = $tmptree;
          }else {
            $subtree[$key] = $key;
          }
        }
      }
      return $subtree;
    }
  }

  function printtree ($subtree) {
    if (!is_array($subtree)) {
      $buffer = explode('|', $subtree);
      return "<specie name=\"$buffer[0]\" common=\"" . str_replace('+', ';', $buffer[1]) . "\" />\n";
    }else {
      $buffer = '';
      foreach($subtree as $key => $value) {
        if (is_array($value)) {
          $buffer .= "<node name=\"$key\">\n" . printtree($value) . "</node>\n";
        }else {
          $buffer .= printtree($value);
        }
      }
      return $buffer;
    }
  }

  function newick ($subtree) {
    if (!is_array($subtree)) {
      $buffer = explode('|', $subtree);
      return str_replace(' ', '_', $buffer[0]);
    }else {
      foreach($subtree as $key => $value) {
        if (is_array($value)) {
          $buffer[] = "(" . newick($value) . ")";
        }else {
          $buffer[] = newick($value);
        }
      }
      return implode(',', $buffer);
    }
  }

  function nbleafs($tree, $row = 10) {
    global $line, $image, $white, $grey, $black;
    $step = 15;
    $min = 0;
    $i = array('leaf' => 0, 'node' => 0, 'line' => 0);
    if (isset($tree['attrs']['NAME'])) {
      $textbox = imagettfbbox(8, 0, 'Vera.ttf', $tree['attrs']['NAME']);
      $inc = abs($textbox[2] + $textbox[0]) + 30;
    }else {
      $inc = 30;
    }
    if (isset($tree['children'])) {
      foreach($tree['children'] as $subtree) {
        $j = nbleafs($subtree, $row + $inc);
        $i['line'] += $j['line'];
        $i['leaf'] += $j['leaf'];
        $i['node'] += $j['node'];
        if ($min == 0) {
          $min = $j['line'];
        }
        $max = $j['line'];
      }
      $i['node']++;
      $i['line'] = $i['line'] / count($tree['children']);
      imageline($image, $row-10, $i['line'] * $step, $row + $inc-10, $i['line'] * $step, $grey);
      imageline($image, $row-10, $i['line'] * $step-1, $row + $inc-10, $i['line'] * $step-1, $grey);
      imageline($image, $row-10, $i['line'] * $step + 1, $row + $inc-10, $i['line'] * $step + 1, $grey);
      imageline($image, $row + $inc-10, $min * $step, $row + $inc-10, $max * $step, $grey);
      imageline($image, $row + $inc-9, $min * $step, $row + $inc-9, $max * $step, $grey);
      imageline($image, $row + $inc-11, $min * $step-1, $row + $inc-11, $max * $step + 1, $grey);
      if (isset($tree['attrs']['NAME'])) {
        $textbox = imagettfbbox(8, 0, 'Vera.ttf', $tree['attrs']['NAME']);
        ImageFilledRectangle($image, $row, $i['line'] * $step-4, $row + abs($textbox[2] + $textbox[0]) + 6, $i['line'] * $step + 4, $white);
        imagettftext($image, 8, 0, $row + 2, $i['line'] * $step + 4, $black, 'Vera.ttf', $tree['attrs']['NAME']);
      }
    }else {
      $i['leaf']++;
      $i['line'] = ++$line;
      if (!empty($tree['attrs']['NAME'])) {
        imagettftext($image, 8, 0, $row + 2, $line * $step + 4, $black, 'Vera.ttf', $tree['attrs']['NAME']);
      }
      if (!empty($tree['attrs']['TYPE']) && isset($_GET['type'])) {
        imagettftext($image, 7, 0, $row + $inc-15, $line * $step + 4, $grey, 'Vera.ttf', '[' . $tree['attrs']['TYPE'] . ']');
      }
      if (!empty($tree['attrs']['COMMON']) && !isset($_GET['type'])) {
        imagettftext($image, 8, 0, $row + $inc-15, $line * $step + 4, $grey, 'Vera.ttf', '(' . $tree['attrs']['COMMON'] . ')');
      }
      imageline($image, $row-10, $line * $step, $row, $line * $step, $grey);
      imageline($image, $row-10, $line * $step-1, $row, $line * $step-1, $grey);
      imageline($image, $row-10, $line * $step + 1, $row, $line * $step + 1, $grey);
    }
    return $i;
  }
?>