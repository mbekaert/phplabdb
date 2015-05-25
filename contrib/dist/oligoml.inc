<?php
$buffer[] = "\$plugin['oligoml'] = array(
  'url' => '/oligoml',
  'name' => 'oligo',
  'description' => 'All your oligonucleotides and primers set in a box',
  'search' => 'a.name, a.sequence FROM oligoml_oligo AS a WHERE',
  'version' => '1.2.5',
  'code' => 'O|P'
  );\n";
?>