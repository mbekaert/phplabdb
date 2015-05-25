<?php
$config = array(
  'server' => 'http://192.168.1.2/phplabdb',
  'powered' => 'phpLabDB v1.211 (2007-03-01)',
  'db' => 'phplabdb',
  'gmap' => 'ABQIAAAAbsERbX0JYQ2efimaL55KMhRi_j0U6kJrkFvY4-OX2XYmEAa76BTboUFYRBtW6-qlxRn3DjcY73iPNA',
  'tmp' => ((ini_get('upload_tmp_dir') != '')?ini_get('upload_tmp_dir'):'/tmp/'),
  'secure' => false,
  'unsafe' => true,
  'login' => ((isset($_SESSION['login']['user_id']) && isset($_SESSION['login']['ip']) && isset($_COOKIE['user_id']) && ($_SESSION['login']['user_id'] == strip_tags(trim($_COOKIE['user_id']))) && ($_SESSION['login']['ip'] == $_SERVER['REMOTE_ADDR']))?true:false),
  'sqlserver' => 'postgresql',
  'sqlport' => '',
  'sqlhost' => 'localhost',
  'sqlpassword' => '',
  'sqllogin' => 'www'
  );

$plugin['oligoml'] = array(
  'url' => '/oligoml',
  'name' => 'oligo',
  'description' => 'All your oligonucleotides and primers set in a box',
  'search' => 'a.name, a.sequence FROM oligoml_oligo AS a WHERE',
  'version' => '1.2.5',
  'code' => 'O|P'
  );

$plugin['tree'] = array(
  'url' => '/tree',
  'name' => 'tree',
  'description' => 'All your species',
  'version' => '1.0.2'
  );

$plugin['darwin'] = array(
  'url' => '/darwin',
  'name' => 'darwin',
  'description' => 'All your samples in a box',
  'search' => 'b.scientificname, a.basisofrecord FROM darwin_sample AS a, darwin_bioject AS b WHERE b.prefix=a.bioject_prefix AND  b.id=a.bioject_id AND',
  'version' => '1.0.4',
  'code' => 'D|B'
  );

$plugin['uniprime'] = array(
  'url' => '/uniprime',
  'name' => 'uniprime',
  'description' => 'Designing universal primers',
  'version' => '1.1.2',
  'code' => 'S|L|A|R|X'
  );
?>