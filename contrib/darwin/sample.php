<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  if (($_SESSION['login']['right'] >= 3) && !empty($_GET['edit']) && preg_match('/D(\d+)\.(\d+)/', rawurldecode($_GET['edit']), $matches)) {
    $sql = sql_connect($config['db']);
    if (isset($_POST['remove']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(trim(rawurldecode($_GET['edit']))) . floor(intval(date('b')))))) {
      $result = sql_query('DELETE FROM darwin_sample WHERE prefix=' . octdec(intval($matches[1])) . ' AND id=' . octdec(intval($matches[2])) . ';', $sql);
      header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/sample');
      exit;
    }elseif (isset($_POST['edit']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(trim(rawurldecode($_GET['edit']))) . floor(intval(date('b'))))) && !empty($_POST['basisofrecord']) && !empty($_POST['disposition']) && !empty($_POST['box']) && (empty($_POST['extra']) || (!empty($_POST['extra']) && !empty($_POST['url']))) && (empty($_POST['biblio']) || (!empty($_POST['biblio']) && !empty($_POST['pmid'])))) {
      if (!empty($_POST['biblio']) && !empty($_POST['pmid'])) {
        $ref = getref(intval($_POST['pmid']), $sql);
      }
      $result = sql_query('UPDATE darwin_sample SET basisofrecord=\'' . addslashes(stripslashes(strip_tags(trim($_POST['basisofrecord'])))) . '\', observer=' . (!empty($_POST['observer'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['observer'])))) . '\'':'NULL') . ', protocolname=' . (!empty($_POST['protocolname'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['protocolname'])))) . '\'':'NULL') . ', preservationmethod=' . (!empty($_POST['preservationmethod'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['preservationmethod'])))) . '\'':'NULL') . ', disposition=' . (!empty($_POST['disposition'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['disposition'])))) . '\'':'NULL') . ', partname=' . (!empty($_POST['partname'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['partname'])))) . '\'':'NULL') . ', condition=' . (!empty($_POST['condition'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['condition'])))) . '\'':'NULL') . ', othercatalognumbers=' . (!empty($_POST['othercatalognumbers'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['othercatalognumbers'])))) . '\'':'NULL') . ', url=' . (!empty($_POST['url'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['url'])))) . '\'':'NULL') . ', relatedinformation=' . ((isset($_POST['relatedinformation']) && !fullyempty($_POST['relatedinformation']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['relatedinformation'])))) . '\'':'NULL') . ', citations=' . (!empty($ref)?'\'' . $ref . '\'':'NULL') . ', stockroom=' . (!empty($_POST['freezer'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['freezer'])))) . '\'':'NULL') . ', stockbox=' . (!empty($_POST['box'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['box'])))) . '\'':'NULL') . ', stockrank=' . (!empty($_POST['rank'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['rank'])))) . '\'':'NULL') . ', comments=' . ((isset($_POST['comments']) && !fullyempty($_POST['comments']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['comments'])))) . '\'':'NULL') . ', updated=NOW(), author=\'' . addslashes($_SESSION['login']['username']) . '\' WHERE prefix=' . octdec(intval($matches[1])) . ' AND id=' . octdec(intval($matches[2])) . ';', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/sample/' . rawurlencode($matches[0]));
        exit;
      }
    }
    head('darwin', true);
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php
    $result = sql_query('SELECT b.institutioncode, b.collectioncode, b.catalognumber, a.subcatalognumber, a.basisofrecord, a.observer, a.protocolname, a.preservationmethod, a.disposition, a.partname, a.condition, a.othercatalognumbers, a.url, a.relatedinformation, a.citations, a.stockroom, a.stockbox, a.stockrank, a.comments FROM darwin_sample AS a, darwin_bioject AS b WHERE a.prefix=' . octdec(intval($matches[1])) . ' AND a.id=' . octdec(intval($matches[2])) . ' AND b.prefix=a.bioject_prefix AND b.id=a.bioject_id;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result); ?>
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url'] . '/sample/edit/' . rawurlencode($matches[0]); ?>">
        <div>
          <h2><?php print $row[0] . '-' . $row[1] . '-' . $row[2] . '-' . $row[3]; ?></h2><br />
          <div>
            <label for="basisofrecord"><strong><?php print _("Basis Of Record"); ?></strong></label>
            <select name="basisofrecord" id="basisofrecord" title="<?php print _("A descriptive term indicating whether the record represents an specimen or observation"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT name FROM darwin_basisofrecord ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($_POST['basisofrecord']) && ($row2[0] == $_POST['basisofrecord']))?' selected="selected"':((!isset($_POST['basisofrecord']) && isset($row[4]) && ($row2[0] == $row[4]))?' selected="selected"':'')) . ">$row2[0]</option>";
        }
      }
?></select>
            <br />
          </div>
          <div>
            <label for="partname"><?php print _("Part Name"); ?></label>
            <input name="partname" id="partname" type="text" maxlength="128" title="<?php print _("Part names should refer to specific anatomical parts or recognized groups of parts (e.g., 'post-cranial skeleton'). With rare exception, parts are the singular form of a noun"); ?>"<?php print (!empty($_POST['partname'])?' value="' . stripslashes(strip_tags(trim($_POST['partname']))) . '"':(!isset($_POST['partname']) && isset($row[9])?' value="' . $row[9] . '"':'')); ?> />
            <br />
          </div>
          <div>
            <label for="disposition"><strong><?php print _("Disposition"); ?></strong></label>
            <select name="disposition" id="disposition" title="<?php print _("Disposition describes the status of parts and, as an abstract generality, the status of catalogued items."); ?>"><option value=""></option><?php
      $result = sql_query('SELECT name FROM darwin_disposition ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($_POST['disposition']) && ($row2[0] == $_POST['disposition']))?' selected="selected"':((!isset($_POST['disposition']) && isset($row[8]) && ($row2[0] == $row[8]))?' selected="selected"':'')) . ">$row2[0]</option>";
        }
      }
?></select>
            <br />
          </div>
          <div>
            <label for="observer"><?php print _("Author"); ?></label>
            <select name="observer" id="observer" title="<?php print _("The name of the collector of the original data for the object or observation."); ?>"><option value=""></option><?php
      $result = sql_query('SELECT username, real_name FROM darwin_users ORDER BY real_name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($_POST['observer']) && ($row2[0] == $_POST['observer']))?' selected="selected"':((!isset($_POST['observer']) && isset($row[5]) && ($row2[0] == $row[5]))?' selected="selected"':'')) . ">$row2[1]</option>";
        }
      }
?></select>  [<a href="<?php print $config['server'] . $plugin['darwin']['url'] ?>/author/add">add</a>]
            <br />
          </div>
          <div>
            <label for="protocolname"><?php print _("Protocol used"); ?></label>
            <select name="protocolname" id="protocolname" title="<?php print _("A formal or informal name for the protocol"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT name FROM darwin_protocol ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($_POST['protocolname']) && ($row2[0] == $_POST['protocolname']))?' selected="selected"':((!isset($_POST['protocolname']) && isset($row[6]) && ($row2[0] == $row[6]))?' selected="selected"':'')) . ">$row2[0]</option>";
        }
      }
?></select>
            <br />
          </div>
          <div>
            <label for="preservationmethod"><?php print _("Preservation Method"); ?></label>
            <select name="preservationmethod" id="preservationmethod" title="<?php print _("Preservation Method may refer to a preservation process (e.g., 'tanned') or a storage media (e.g., 'ethanol')"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT name FROM darwin_preservationmethod ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($_POST['preservationmethod']) && ($row2[0] == $_POST['preservationmethod']))?' selected="selected"':((!isset($_POST['preservationmethod']) && isset($row[7]) && ($row2[0] == $row[7]))?' selected="selected"':'')) . ">$row2[0]</option>";
        }
      }
?></select>
            <br />
          </div>
          <div>
            <label for="condition"><?php print _("Sample Condition"); ?></label>
            <input name="condition" id="condition" type="text" maxlength="128" title="<?php print _("Condition is used for entries such as 'broken','dissected' or the DNA concentration"); ?>"<?php print (!empty($_POST['condition'])?' value="' . stripslashes(strip_tags(trim($_POST['condition']))) . '"':(!isset($_POST['condition']) && isset($row[10])?' value="' . $row[10] . '"':'')); ?> />
            <br />
          </div>
          <div>
            <label for="othercatalognumbers"><?php print _("Other Catalog Numbers"); ?></label>
            <input name="othercatalognumbers" id="othercatalognumbers" type="text" title="<?php print _("External / Imported Catalog Numbers"); ?>"<?php print (!empty($_POST['othercatalognumbers'])?' value="' . stripslashes(strip_tags(trim($_POST['othercatalognumbers']))) . '"':(!isset($_POST['othercatalognumbers']) && isset($row[11])?' value="' . $row[11] . '"':'')); ?> />
            <br />
          </div>
          <div>
            <label for="freezer_l"><?php print _("Freezer"); ?></label>
            <input name="freezer" id="freezer_l" type="text" maxlength="64" title="<?php print _("Freezer or room of storage"); ?>"<?php print (!empty($_POST['freezer'])?' value="' . stripslashes(strip_tags(trim($_POST['freezer']))) . '"':(!isset($_POST['freezer']) && isset($row[15])?' value="' . $row[15] . '"':'')); ?> />
            <br />
          </div>
          <div>
            <label for="box_l"><strong><?php print _("Box"); ?></strong></label>
            <input name="box" id="box_l" type="text" maxlength="64" title="<?php print _("Box reference of storage"); ?>"<?php print (!empty($_POST['box'])?' value="' . stripslashes(strip_tags(trim($_POST['box']))) . '"':(!isset($_POST['box']) && isset($row[16])?' value="' . $row[16] . '"':'')); ?> />
            <br />
          </div>
          <div>
            <label for="rank_l"><?php print _("Rank"); ?></label>
            <input name="rank" id="rank_l" type="text" maxlength="64" title="<?php print _("Rank into the box"); ?>"<?php print (!empty($_POST['rank'])?' value="' . stripslashes(strip_tags(trim($_POST['rank']))) . '"':(!isset($_POST['rank']) && isset($row[17])?' value="' . $row[17] . '"':'')); ?> />
            <br />
          </div>
          <div>
            <label for="extra"><?php print _("URL/External file"); ?></label>
            <input type="checkbox" name="extra" id="extra" rel="extra"<?php print ((!empty($_POST['extra']) || (!isset($_POST['observer']) && isset($row[12])))?' checked="checked"':''); ?> />
            <br />
          </div>
          <div rel="extra">
            <label for="url"><?php print _("URL/File Name"); ?></label>
            <input name="url" id="url" type="text" title="<?php print _("A reference to digital images associated with the specimen or observation"); ?>"<?php print (!empty($_POST['url'])?' value="' . stripslashes(strip_tags(trim($_POST['url']))) . '"':(!isset($_POST['url']) && isset($row[12])?' value="' . $row[12] . '"':'')); ?> />
            <br />
          </div>
          <div rel="extra">
            <label for="relatedinformation"><?php print _("Related Informations"); ?></label>
            <textarea name="relatedinformation" id="relatedinformationd" rows="4" cols="30" title="<?php print _("Free text references to information not delivered via the conceptual schema, including URLs to specimen details, publications, etc"); ?>"><?php print ((isset($_POST['relatedinformation']) && !fullyempty($_POST['relatedinformation']))?stripslashes(strip_tags(trim($_POST['relatedinformation']))):(!isset($_POST['relatedinformation']) && isset($row[13])?$row[13]:'')); ?></textarea>
            <br />
          </div>
          <div>
            <label for="biblio"><?php print _("Bibliography"); ?></label>
            <input type="checkbox" name="biblio" id="biblio" rel="biblio"<?php print ((!empty($_POST['biblio']) || (!isset($_POST['pmid']) && !empty($row[14])))?' checked="checked"':''); ?> />
            <br />
          </div>
<?php
      if (!isset($_POST['pmid']) && !empty($row[14])) {
        $result_ref = sql_query('SELECT url, comments FROM reference WHERE id=' . intval($row[14]) . ';', $sql);
        if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result_ref) == 1)) {
          $row2 = sql_fetch_row($result_ref);
        }
      }
?>
          <div rel="biblio">
            <label for="pmid"><?php print _("PMID"); ?></label>
            <input name="pmid" id="pmid" type="text" maxlength="16" title="<?php print _("PubMed Identifier of the reference"); ?>"<?php print (isset($_POST['pmid'])?' value="' . stripslashes(strip_tags(trim($_POST['pmid']))) . '"':(!empty($row2[0])?' value="' . substr($row2[0], - (strlen($row2[0]) - strpos($row2[0], '=')-1)) . '"':'')); ?> />
            <br />
          </div>
          <div rel="biblio">
            <label for="comments_b"><?php print _("Comments"); ?></label>
            <textarea name="comments_b" id="comments_b" rows="4" cols="30" title="<?php print _("Citation comments"); ?>"><?php print (isset($_POST['comments_b'])?stripslashes(strip_tags(trim($_POST['comments_b']))):(!empty($row2[1])?$row2[1]:'')); ?></textarea>
            <br />
          </div>
          <div>
            <label for="comments"><?php print _("Remarks"); ?></label>
            <textarea name="comments" id="comments" rows="4" cols="30" title="<?php print _("General comments"); ?>"><?php print ((isset($_POST['comments']) && !fullyempty($_POST['comments']))?stripslashes(strip_tags(trim($_POST['comments']))):(!isset($_POST['comments']) && isset($row[18])?$row[18]:'')); ?></textarea>
            <br />
          </div>
          <br />
          <input type="hidden" name="key" value="<?php print md5(strip_tags(trim(rawurldecode($_GET['edit']))) . floor(intval(date('b')))); ?>" />
          <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit"  name="edit" value="<?php print _("Edit"); ?>" />&nbsp;<input type="submit" name="remove" value="<?php print _("Remove"); ?>" onclick="return confirm('<?php print _("Are you sure you want to delete?"); ?>')"/>
        </div>
        </form>
        <br />
      </div>
<?php
    }
  }elseif (($_SESSION['login']['right'] >= 2) && !empty($_GET['add'])) {
    $sql = sql_connect($config['db']);
    if (!empty($_POST['darwin']) && ($_POST['darwin'] == md5('add' . floor(intval(date('b'))))) && !empty($_POST['bioject']) && !empty($_POST['basisofrecord']) && !empty($_POST['disposition']) && !empty($_POST['box']) && (empty($_POST['extra']) || (!empty($_POST['extra']) && !empty($_POST['url']))) && (empty($_POST['biblio']) || (!empty($_POST['biblio']) && !empty($_POST['pmid'])))) {
      $prefix = floor(((intval(date('Y', time())) - 2001) * 12 + intval(date('m', time())) - 1) / 1.5);
      if (preg_match('/B(\d+)\.(\d+)/', $_POST['bioject'], $matches)) {
        if (!empty($_POST['biblio']) && !empty($_POST['pmid'])) {
          $ref = getref(intval($_POST['pmid']), $sql);
        }
        $result = sql_query('INSERT INTO darwin_sample (prefix, id, subcatalognumber, bioject_prefix, bioject_id, basisofrecord, observer, protocolname, preservationmethod, disposition, partname, condition, othercatalognumbers, url, relatedinformation, citations, stockroom, stockbox, stockrank, comments, author) SELECT ' . $prefix . ', CASE WHEN max(id)>=1 THEN max(id)+1 ELSE 1 END, 0,' . octdec($matches[1]) . ',' . octdec($matches[2]) . ',\'' . addslashes(stripslashes(strip_tags(trim($_POST['basisofrecord'])))) . '\',' . (!empty($_POST['observer'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['observer'])))) . '\'':'NULL') . ',' . (!empty($_POST['protocolname'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['protocolname'])))) . '\'':'NULL') . ',' . (!empty($_POST['preservationmethod'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['preservationmethod'])))) . '\'':'NULL') . ',' . (!empty($_POST['disposition'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['disposition'])))) . '\'':'NULL') . ',' . (!empty($_POST['partname'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['partname'])))) . '\'':'NULL') . ',' . (!empty($_POST['condition'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['condition'])))) . '\'':'NULL') . ',' . (!empty($_POST['othercatalognumbers'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['othercatalognumbers'])))) . '\'':'NULL') . ',' . (!empty($_POST['url'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['url'])))) . '\'':'NULL') . ',' . ((isset($_POST['relatedinformation']) && !fullyempty($_POST['relatedinformation']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['relatedinformation'])))) . '\'':'NULL') . ',' . (!empty($ref)?'\'' . $ref . '\'':'NULL') . ',' . (!empty($_POST['freezer'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['freezer'])))) . '\'':'NULL') . ',' . (!empty($_POST['box'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['box'])))) . '\'':'NULL') . ',' . (!empty($_POST['rank'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['rank'])))) . '\'':'NULL') . ',' . ((isset($_POST['comments']) && !fullyempty($_POST['comments']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['comments'])))) . '\'':'NULL') . ',\'' . addslashes($_SESSION['login']['username']) . '\' FROM darwin_sample WHERE prefix=' . $prefix . ';', $sql);
        if (!strlen($r = sql_last_error($sql))) {
          $result = sql_query('SELECT (max(subcatalognumber)+1) FROM darwin_sample WHERE (bioject_prefix=' . octdec($matches[1]) . ' AND bioject_id=' . octdec($matches[2]) . ');', $sql);
          if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
            $row = sql_fetch_row($result);
            $subcatalognumber = $row[0];
            $result = sql_query('SELECT prefix, id FROM darwin_sample WHERE (prefix=' . $prefix . ' AND bioject_prefix=' . octdec($matches[1]) . ' AND bioject_id=' . octdec($matches[2]) . ' AND subcatalognumber=0) ORDER BY updated DESC;', $sql);
            if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) >= 1)) {
              $row = sql_fetch_row($result);
              $result = sql_query('UPDATE darwin_sample SET subcatalognumber=' . $subcatalognumber . ' WHERE (prefix=' . $prefix . ' AND bioject_prefix=' . octdec($matches[1]) . ' AND bioject_id=' . octdec($matches[2]) . ' AND subcatalognumber=0) ;', $sql);
              header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/sample/D' . decoct($row[0]) . '.' . decoct($row[1]));
              exit;
            }else {
              $error = _("Database entry error:") . ' ' . $r;
            }
          }else {
            $error = _("Database entry error:") . ' ' . $r;
          }
        }
      }
    }
    head('darwin', true);
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url']; ?>/sample/add">
        <div>
          <h2><?php print _("New sample"); ?><small><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/help/sample" title="<?php print _("Help"); ?>"><?php print _("Help"); ?></a></small></h2><br /><?php print _("You can specify the type, the conditions and the references of the sample."); ?><br /><br /><?php print (isset($error)?'            <strong>' . $error . "</strong><br /><br />\n":''); ?>
          <div>
            <label for="advanced"><?php print _("Advanced"); ?></label>
            <input type="checkbox" name="advanced" id="advanced" rel="advanced"<?php print (!empty($_POST['advanced'])?' checked="checked"':''); ?> />
            <br />
          </div>
          <div>
            <label for="bioject"><strong><?php print _("Related specimen entry"); ?></strong></label>
            <select name="bioject" id="bioject" title="<?php print _("Reference of the related specimen entry"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT prefix, id, institutioncode, collectioncode, catalognumber FROM darwin_bioject ORDER BY institutioncode, collectioncode, catalognumber;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print '<option value="B' . decoct($row[0]) . '.' . decoct($row[1]) . '"' . (((!empty($_POST['bioject']) && ($_POST['bioject'] == 'B' . decoct($row[0]) . '.' . decoct($row[1]))) || (!empty($_GET['bioject']) && ($_GET['bioject'] == 'B' . decoct($row[0]) . '.' . decoct($row[1]))))?' selected="selected"':'') . ">$row[2]-$row[3]-$row[4]</option>";
      }
    }
?></select> [<a href="<?php print $config['server'] . $plugin['darwin']['url'] ?>/bioject/add">add</a>]
            <br />
          </div>
          <div>
           <label for="basisofrecord"><strong><?php print _("Basis Of Record"); ?></strong></label>
           <select name="basisofrecord" id="basisofrecord" title="<?php print _("A descriptive term indicating whether the record represents an specimen or observation"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT name FROM darwin_basisofrecord ORDER BY name;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((!empty($_POST['basisofrecord']) && ($row[0] == $_POST['basisofrecord']))?' selected="selected"':'') . ">$row[0]</option>";
      }
    }
?></select>
            <br />
          </div>
          <div rel="advanced">
            <label for="partname"><?php print _("Part Name"); ?></label>
            <input name="partname" id="partname" type="text" maxlength="128" title="<?php print _("Part names should refer to specific anatomical parts or recognized groups of parts (e.g., 'post-cranial skeleton'). With rare exception, parts are the singular form of a noun"); ?>"<?php print (!empty($_POST['partname'])?' value="' . stripslashes(strip_tags(trim($_POST['partname']))) . '"':''); ?> />
            <br />
          </div>
          <div>
            <label for="disposition"><strong><?php print _("Disposition"); ?></strong></label>
            <select name="disposition" id="disposition" title="<?php print _("Disposition describes the status of parts and, as an abstract generality, the status of catalogued items"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT name FROM darwin_disposition ORDER BY name;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((!empty($_POST['disposition']) && ($row[0] == $_POST['disposition']))?' selected="selected"':'') . ">$row[0]</option>";
      }
    }
?></select>
            <br />
          </div>
          <div>
            <label for="observer"><?php print _("Author"); ?></label>
            <select name="observer" id="observer" title="<?php print _("The name of the collector of the original data for the object or observation."); ?>"><option value=""></option><?php
    $result = sql_query('SELECT username, real_name FROM darwin_users ORDER BY real_name;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((!empty($_POST['observer']) && ($row[0] == $_POST['observer']))?' selected="selected"':'') . ">$row[1]</option>";
      }
    }
?></select>  [<a href="<?php print $config['server'] . $plugin['darwin']['url'] ?>/author/add">add</a>]
            <br />
          </div>
          <div>
            <label for="protocolname"><?php print _("Protocol used"); ?></label>
            <select name="protocolname" id="protocolname" title="<?php print _("A formal or informal name for the protocol"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT name FROM darwin_protocol ORDER BY name;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((!empty($_POST['protocolname']) && ($row[0] == $_POST['protocolname']))?' selected="selected"':'') . ">$row[0]</option>";
      }
    }
?></select>
            <br />
          </div>
          <div>
            <label for="preservationmethod"><?php print _("Preservation Method"); ?></label>
            <select name="preservationmethod" id="preservationmethod" title="<?php print _("Preservation Method may refer to a preservation process (e.g., 'tanned') or a storage media (e.g., 'ethanol')"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT name FROM darwin_preservationmethod ORDER BY name;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((!empty($_POST['preservationmethod']) && ($row[0] == $_POST['preservationmethod']))?' selected="selected"':'') . ">$row[0]</option>";
      }
    }
?></select>
            <br />
          </div>
          <div rel="advanced">
            <label for="condition"><?php print _("Sample Condition"); ?></label>
            <input name="condition" id="condition" type="text" maxlength="128" title="<?php print _("Condition is used for entries such as 'broken','dissected' or the DNA concentration"); ?>"<?php print (!empty($_POST['condition'])?' value="' . stripslashes(strip_tags(trim($_POST['condition']))) . '"':''); ?> />
            <br />
          </div>
          <div rel="advanced">
            <label for="othercatalognumbers"><?php print _("Other Catalog Numbers"); ?></label>
            <input name="othercatalognumbers" id="othercatalognumbers" type="text" title="<?php print _("External / Imported Catalog Numbers"); ?>"<?php print (!empty($_POST['othercatalognumbers'])?' value="' . stripslashes(strip_tags(trim($_POST['othercatalognumbers']))) . '"':''); ?> />
            <br />
          </div>
          <div rel="advanced">
            <label for="freezer_l"><?php print _("Freezer"); ?></label>
            <input name="freezer" id="freezer_l" type="text" maxlength="64" title="<?php print _("Freezer or room of storage"); ?>"<?php print (!empty($_POST['freezer'])?' value="' . stripslashes(strip_tags(trim($_POST['freezer']))) . '"':''); ?> />
            <br />
          </div>
          <div>
            <label for="box_l"><strong><?php print _("Box"); ?></strong></label>
            <input name="box" id="box_l" type="text" maxlength="64" title="<?php print _("Box reference of storage"); ?>"<?php print (!empty($_POST['box'])?' value="' . stripslashes(strip_tags(trim($_POST['box']))) . '"':''); ?> />
            <br />
          </div>
          <div rel="advanced">
            <label for="rank_l"><?php print _("Rank"); ?></label>
            <input name="rank" id="rank_l" type="text" maxlength="64" title="<?php print _("Rank into the box"); ?>"<?php print (!empty($_POST['rank'])?' value="' . stripslashes(strip_tags(trim($_POST['rank']))) . '"':''); ?> />
            <br />
          </div>
          <div rel="advanced">
            <label for="extra"><?php print _("URL/External file"); ?></label>
            <input type="checkbox" name="extra" id="extra" rel="extra"<?php print (!empty($_POST['extra'])?' checked="checked"':''); ?> />
            <br />
          </div>
          <div rel="extra">
            <label for="url"><?php print _("URL/File Name"); ?></label>
            <input name="url" id="url" type="text" title="<?php print _("A reference to digital images associated with the specimen or observation"); ?>"<?php print (!empty($_POST['url'])?' value="' . stripslashes(strip_tags(trim($_POST['url']))) . '"':''); ?> />
            <br />
          </div>
          <div rel="extra">
            <label for="relatedinformation"><?php print _("Related Informations"); ?></label>
            <textarea name="relatedinformation" id="relatedinformationd" rows="4" cols="30" title="<?php print _("Free text references to information not delivered via the conceptual schema, including URLs to specimen details, publications, etc"); ?>"><?php print ((isset($_POST['relatedinformation']) && !fullyempty($_POST['relatedinformation']))?stripslashes(strip_tags(trim($_POST['relatedinformation']))):''); ?></textarea>
            <br />
          </div>
          <div rel="advanced">
            <label for="biblio"><?php print _("Bibliography"); ?></label>
            <input type="checkbox" name="biblio" id="biblio" rel="biblio"<?php print (!empty($_POST['biblio'])?' checked="checked"':''); ?> />
            <br />
          </div>
          <div rel="biblio">
            <label for="pmid"><?php print _("PMID"); ?></label>
            <input name="pmid" id="pmid" type="text" maxlength="16" title="<?php print _("PubMed Identifier of the reference"); ?>"<?php print (!empty($_POST['pmid'])?' value="' . stripslashes(strip_tags(trim($_POST['pmid']))) . '"':''); ?> />
            <br />
          </div>
          <div rel="biblio">
            <label for="comments_b"><?php print _("Comments"); ?></label>
            <textarea name="comments_b" id="comments_b" rows="4" cols="30" title="<?php print _("Citation comments"); ?>"><?php print ((isset($_POST['comments_b']) && !fullyempty($_POST['comments_b']))?stripslashes(strip_tags(trim($_POST['comments_b']))):''); ?></textarea>
            <br />
          </div>
          <div rel="advanced">
            <label for="comments"><?php print _("Remarks"); ?></label>
            <textarea name="comments" id="comments" rows="4" cols="30" title="<?php print _("General comments"); ?>"><?php print ((isset($_POST['comments']) && !fullyempty($_POST['comments']))?stripslashes(strip_tags(trim($_POST['comments']))):''); ?></textarea>
            <br />
          </div>
          <br />
          <input type="hidden" name="darwin" value="<?php print md5('add' . floor(intval(date('b')))); ?>" />
          <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit" value="<?php print _("Add"); ?>" />
        </div>
        </form>
        <br />
      </div>
<?php
  }elseif (!empty($_GET['sample']) && preg_match('/D(\d+)\.(\d+)/', rawurldecode($_GET['sample']), $matches)) {
    head('darwin');
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php
    $sql = sql_connect($config['db']);
    $result = sql_query('SELECT a.bioject_prefix, a.bioject_id, b.institutioncode, b.collectioncode, b.catalognumber, a.subcatalognumber, b.event, b.geolocation, a.observer, a.protocolname, a.preservationmethod, a.disposition, a.partname, a.condition, a.othercatalognumbers, a.url, a.relatedinformation, a.citations, a.stockroom, a.stockbox, a.stockrank, a.attributes, a.comments, a.updated, a.author, c.code, a.basisofrecord FROM darwin_sample AS a, darwin_bioject AS b, users AS c WHERE a.prefix=' . octdec(intval($matches[1])) . ' AND a.id=' . octdec(intval($matches[2])) . ' AND b.prefix=a.bioject_prefix AND b.id=a.bioject_id AND c.username=a.author;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
?>
        <div>
          <h2><?php print $row[2] . '-' . $row[3] . '-' . $row[4] . '-' . $row[5] . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['darwin']['url'] . '/sample/edit/' . rawurlencode($matches[0]) . '" title="' . _("Edit") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
      print '            <div class="semocode"><img src="' . $config['server'] . $plugin['darwin']['url'] . '/semacode/' . rawurlencode(str_rot13($row[0] . '-' . $row[2] . '-' . $row[4] . '-' . $row[5])) . '" width="100" height="100" alt="'.EAN8('D',octdec(intval($matches[1])),octdec(intval($matches[2])))."\" /></div>\n";
      print '            <h3>' . ("Details") . "</h3>\n";
      print '            <div class="details"><div class="title">' . _("Reference") . '</div><div class="label">' . $row[2] . '-' . $row[3] . '-' . $row[4] . '-' . $row[5] . "</div></div>\n";
      print '            <div class="details"><div class="title">' . _("Short ID") . '</div><div class="label">' . $matches[0] . "</div></div>\n";
      print '            <div class="details"><div class="title">' . _("Release") . '</div><div class="label">' . gmdate(_("d-m-Y"), strtotime($row[23])) . ' <span class="grey">(' . $row[25] . $row[24] . ")</span></div></div><br />\n";
      print '            <div class="details"><div class="title">' . _("Related entry") . '</div><div class="label"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/bioject/B' . decoct($row[0]) . '.' . decoct($row[1]) . '">' . $row[2] . '-' . $row[3] . '-' . $row[4] . "</a></div></div>\n";
      print '            <div class="details"><div class="title">' . _("Base of Record") . '</div><div class="label">' . $row[26] . "</div></div>\n";
      if (!empty($row[6]) && !empty($row[7])) print '          <div class="details"><div class="title">' . _("Collecting date") . '</div><div class="label"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/event/' . rawurlencode($row[7]) . '/'. rawurlencode($row[6]) . '">' . ((substr($row[6],-8)!='00:00:00')?date(_("d-m-Y H:i:s"), strtotime($row[6])):((substr($row[1], -15) == '-01-01 00:00:00')?date(_("Y"), strtotime($row[6])):date(_("d-m-Y"), strtotime($row[6])))) . "</a></div></div>\n";
      if (!empty($row[7])) print '          <div class="details"><div class="title">' . _("Location name") . '</div><div class="label"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/geolocation/' . rawurlencode($row[7]) . '">' . $row[7] . "</a></div></div>\n";
      if (!empty($row[8])) {
        $result = sql_query('SELECT real_name FROM darwin_users WHERE username=\'' . $row[8] . '\';', $sql);
        if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
          $row2 = sql_fetch_row($result);
          print '          <div class="details"><div class="title">' . _("Author") . '</div><div class="label"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/author/' . rawurlencode($row[8]) . '">' . $row2[0] . "</a></div></div>\n";
        }
      }
      print '          <h3>' . ("Sample") . "</h3>\n";
      print '          <div class="details"><div class="title">' . _("Disposition") . '</div><div class="label">' . $row[11] . "</div></div>\n";
      if (!empty($row[12])) print '          <div class="details"><div class="title">' . _("Part Name") . '</div><div class="label">' . $row[12] . "</div></div>\n";
      if (!empty($row[13])) print '          <div class="details"><div class="title">' . _("Condition") . '</div><div class="label">' . $row[13] . "</div></div>\n";
      if (!empty($row[9])) print '          <div class="details"><div class="title">' . _("Protocol") . '</div><div class="label">' . $row[9] . "</div></div>\n";
      if (!empty($row[10])) print '          <div class="details"><div class="title">' . _("Storage") . '</div><div class="label">' . $row[10] . "</div></div>\n";
      if (!empty($row[14]) || !empty($row[15]) || !empty($row[16])) print '          <h3>' . ("External links") . "</h3>\n";
      if (!empty($row[14])) print '          <div class="details"><div class="title">' . _("Other Catalog") . '</div><div class="label">' . $row[14] . "</div></div>\n";
      if (!empty($row[15])) print '          <div class="details"><div class="title">' . _("URL/File") . '</div><div class="label">' . $row[15] . "</div></div>\n";
      if (!empty($row[16])) print '          <div class="details">' . htmlentities($row[16], ENT_COMPAT, 'ISO-8859-1') . "</div>\n";
      print '          <h3>' . ("Storage") . "</h3>\n";
      if (!empty($row[18])) print '          <div class="details"><div class="title">' . _("Freeze/Room") . '</div><div class="label">' . $row[18] . "</div></div>\n";
      if (!empty($row[19])) print '          <div class="details"><div class="title">' . _("Box/Rack") . '</div><div class="label">' . $row[19] . "</div></div>\n";
      if (!empty($row[20])) print '          <div class="details"><div class="title">' . _("Rank") . '</div><div class="label">' . $row[20] . "</div></div>\n";
      if (!empty($row[17])) put_ref($row[17], $sql);
      if (!empty($row[21])) {
        print '          <h3>' . ("Attributs") . "</h3>\n";
        foreach(explode('|', $row[21]) as $subattr) {
          $attr = explode('=', $subattr, 2);
          print '          <div class="details"><div class="title">' . $attr[0] . '</div><div class="label">' . $attr[1] . "</div></div>\n";
        }
      }
      if (!empty($row[22])) print '          <h3>' . ("Comments") . "</h3>\n" . '            <div class="details">' . preg_replace('/\[([^\|\]]*)\|([^\|\]]*)\]/', '<a href="\1">\2</a><br />', htmlentities($row[22], ENT_COMPAT, 'ISO-8859-1')) . "</div>\n";
      print '        </div>';
    }
?>
        <br />
      </div>
<?php
  }else {
    head('darwin');
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php if (($_SESSION['login']['right'] >= 2) && empty($_POST['search'])) {
?>
        <div><h2><?php print _("Add a sample"); ?><small><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/sample/add" title="<?php print _("Add a new sample"); ?>"><?php print _("Add an sample..."); ?></a></small></h2><br /><?php print _("You may add a new sample to the collection."); ?><br /></div>
<?php }
?>
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url']; ?>/sample/search">
        <div>
          <h2><?php print _("Search"); ?></h2><br /><?php print _("Retrive a sample. You may provide a reference numbre, or a date."); ?><br /><br />
          <div>
            <label for="search"><?php print _("search"); ?></label>
            <input name="search" id="search" type="text" maxlength="32"<?php print (!empty($_POST['search'])?' value="' . stripslashes(strip_tags(trim($_POST['search']))) . '"':''); ?> />
            <br />
          </div>
          <br />
          <input type="hidden" name="darwin" value="<?php print md5('search' . floor(intval(date('b')))); ?>" />
          <input type="submit" value="<?php print _("Search"); ?>" />
        </div>
        </form>
<?php
    if (!empty($_POST['darwin']) && ($_POST['darwin'] == md5('search' . floor(intval(date('b'))))) && !empty($_POST['search'])) {
      $sql = sql_connect($config['db']);
      $search = preg_replace('/[^\w\d\.\-\_\/ \(\)]/', '', stripslashes(strip_tags(trim($_POST['search']))));
      if (preg_match('/(\w+)-(\w+)-([\w\d\.]+)/', $search, $matches)) {
        $result = sql_query('SELECT a.prefix, a.id, b.institutioncode, b.collectioncode, b.catalognumber, a.subcatalognumber, a.basisofrecord, a.partname FROM darwin_sample AS a, darwin_bioject AS b WHERE (b.institutioncode=\'' . addslashes($matches[1]) . '\' AND b.collectioncode=\'' . addslashes($matches[2]) . '\' AND b.catalognumber=\'' . addslashes($matches[3]) . '\') AND b.prefix=a.bioject_prefix AND b.id=a.bioject_id;', $sql);
      }else {
        $result = sql_query('SELECT a.prefix, a.id, b.institutioncode, b.collectioncode, b.catalognumber, a.subcatalognumber, a.basisofrecord, a.partname FROM darwin_sample AS a, darwin_bioject AS b WHERE (a.comments' . sql_reg(addslashes($search)) . ' OR a.partname' . sql_reg(addslashes($search)) . ' OR disposition' . sql_reg(addslashes($search)) . 'OR b.geolocation' . sql_reg(addslashes($search)) . ' OR b.event' . sql_reg(addslashes($search)) . ' OR b.catalognumber' . sql_reg(addslashes($search)) . ') AND b.prefix=a.bioject_prefix AND b.id=a.bioject_id;', $sql);
      }
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        print "            <div>\n              <h2>" . _("Results") . "</h2>\n";
        while ($row = sql_fetch_row($result)) {
          print '          <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/sample/D' . decoct($row[0]) . '.' . decoct($row[1]) . '">' . ucfirst($row[6]) . '</a></span><span class="desc">' . $row[2] . '-' . $row[3] . '-' . $row[4] . '-' . $row[5] . '</span><span class="detail">' . ucfirst($row[7]) . "</span></div>\n";
        }
        print "            </div>\n";
      }
    }
?>
        <br />
      </div>
<?php
  }
  foot();
}else {
  header('Location: ' . $config['server']);
}
?>