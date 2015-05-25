<?php
$buffer[] = "\$plugin['darwin'] = array(
  'url' => '/darwin',
  'name' => 'darwin',
  'description' => 'All your samples in a box',
  'search' => 'b.scientificname, a.basisofrecord FROM darwin_sample AS a, darwin_bioject AS b WHERE b.prefix=a.bioject_prefix AND  b.id=a.bioject_id AND',
  'version' => '1.0.4',
  'code' => 'D|B'
  );\n";
?>