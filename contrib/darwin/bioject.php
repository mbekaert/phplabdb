<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  if (($_SESSION['login']['right'] >= 3) && !empty($_GET['edit']) && preg_match('/B(\d+)\.(\d+)/', rawurldecode($_GET['edit']), $matches)) {
    $sql = sql_connect($config['db']);
    if (isset($_POST['remove']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(trim(rawurldecode($_GET['edit']))) . floor(intval(date('b')))))) {
      $result = sql_query('DELETE FROM darwin_bioject WHERE prefix=' . octdec(intval($matches[1])) . ' AND id=' . octdec(intval($matches[2])) . ';', $sql);
      header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/bioject');
      exit;
    }elseif (isset($_POST['edit']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(trim(rawurldecode($_GET['edit']))) . floor(intval(date('b'))))) && !empty($_POST['catalognumber']) && !empty($_POST['institutioncode']) && !empty($_POST['collectioncode'])  && !empty($_POST['taxon']) && ((($_POST['taxon'] == 'newtaxon') && !empty($_POST['newspecies']) && (strlen($_POST['newspecies']) > 10) && (isset($_POST['newtaxon']) && !fullyempty($_POST['newtaxon'])) && (strlen($_POST['newtaxon']) > 10)) || ($_POST['taxon'] != 'newtaxon'))) {
      if (!empty($_POST['event'])) {
        $ret=explode('|',$_POST['event'],2);
        $result = sql_query('SELECT geolocation, datecollected FROM darwin_environment WHERE geolocation=\'' . addslashes(stripslashes(strip_tags($ret[0]))) . '\' AND datecollected=\'' . addslashes(stripslashes(strip_tags($ret[1]))) . '\';', $sql);
        if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
          $row = sql_fetch_row($result);
          $geolocation = $row[0];
          $_POST['event'] = $row[1];
        }else {
          unset($_POST['event']);
        }
      }
      $result = sql_query('UPDATE darwin_bioject SET institutioncode=\'' . addslashes(stripslashes(strip_tags(trim($_POST['institutioncode'])))) . '\', collectioncode=\'' . addslashes(stripslashes(strip_tags(trim($_POST['collectioncode'])))) . '\', catalognumber=\'' . addslashes(stripslashes(strip_tags(trim($_POST['catalognumber'])))) . '\', observer=' . (!empty($_POST['observer'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['observer'])))) . '\'':'NULL') . ', validdistributionflag=' . (!empty($_POST['validdistributionflag'])?'\'f\'':'\'t\'') . ', informationwithheld=' . ((isset($_POST['informationwithheld']) && !fullyempty($_POST['informationwithheld']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['informationwithheld'])))) . '\'':'NULL') . ', geolocation=' . (isset($geolocation)?'\'' . addslashes($geolocation) . '\'':'NULL') . ', event=' . ((isset($_POST['event']) && !fullyempty($_POST['event']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['event'])))) . '\'':'NULL') . ', ' . (($_POST['taxon'] == 'newtaxon')?'scientificname=\'' . addslashes(stripslashes(strip_tags(trim($_POST['newspecies'])))) . '\', taxon=\'' . addslashes(stripslashes(strip_tags(trim($_POST['newtaxon'])))) . '\'':'scientificname=\'' . addslashes(stripslashes(strip_tags(trim($_POST['taxon'])))) . '\', taxon=NULL') . ', identificationqualifier=' . (!empty($_POST['identificationqualifier'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['identificationqualifier'])))) . '\'':'NULL') . ', sex=' . (!empty($_POST['sex'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['sex'])))) . '\'':'NULL') . ', lifestage=' . (!empty($_POST['lifestage'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['lifestage'])))) . '\'':'NULL') . ', reproductiveevidence=' . ((isset($_POST['reproductiveevidence']) && !fullyempty($_POST['reproductiveevidence']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['reproductiveevidence'])))) . '\'':'NULL') . ', density=' . (!empty($_POST['density'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['density'])))) . '\'':'NULL') . ', conditionelement=' . (!empty($_POST['conditionelement'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['conditionelement'])))) . '\'':'NULL') . ', observedsize=' . ((isset($_POST['observedsize']) && !fullyempty($_POST['observedsize']))?'\'' . floatval($_POST['observedsize']) . '\'':'NULL') . ', observedweight=' . ((isset($_POST['observedweight']) && !fullyempty($_POST['observedweight']))?'\'' . floatval($_POST['observedweight']) . '\'':'NULL') . ', comments=' . ((isset($_POST['remark']) && !fullyempty($_POST['remark']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['remark'])))) . '\'':'NULL') . ', updated=NOW(), author=\'' . addslashes($_SESSION['login']['username']) . '\' WHERE prefix=' . octdec(intval($matches[1])) . ' AND id=' . octdec(intval($matches[2])) . ';', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/bioject/' . rawurlencode($matches[0]));
        exit;
      }
    }
    head('darwin', true);
?>
        <div class="items">
          <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php
    $result = sql_query('SELECT institutioncode, collectioncode, catalognumber, validdistributionflag, informationwithheld, event, observer, scientificname, taxon, identificationqualifier, sex, lifestage, reproductiveevidence, density, conditionelement, observedsize, observedweight, comments, geolocation FROM darwin_bioject WHERE prefix=' . octdec(intval($matches[1])) . ' AND id=' . octdec(intval($matches[2])) . ';', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
?>
          <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url'] . '/bioject/edit/' . rawurlencode($matches[0]); ?>">
          <div>
            <h2><?php print $row[0] . '-' . $row[1] . '-' . $row[2]; ?></h2><br />
            <div>
              <label for="event"><?php print _("Collecting event"); ?></label>
              <select name="event" id="event" title="<?php print _("The full name of a collecting event"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT geolocation, datecollected FROM darwin_environment ORDER BY geolocation, datecollected;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]|$row2[1]\"" . ((isset($_POST['event']) && (($row2[0].'|'.$row2[1]) == $_POST['event']))?' selected="selected"':((!isset($_POST['event']) && isset($row[18])  && isset($row[5]) && (($row2[0].'|'.$row2[1]) == ($row[18].'|'.$row[5])))?' selected="selected"':'')) . ">$row2[0] / " . ((substr($row2[1],-8)!='00:00:00')?date(_("d-m-Y H:i:s"), strtotime($row2[1])):((substr($row2[1], -15) == '-01-01 00:00:00')?date(_("Y"), strtotime($row2[1])):date(_("d-m-Y"), strtotime($row2[1])))) . "</option>";
        }
      }
?></select>
              <br />
            </div>
            <div>
              <label for="institutioncode"><strong><?php print _("Institution"); ?></strong></label>
              <select name="institutioncode" id="institutioncode" title="<?php print _("The full name of the Institution involved"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT institutioncode, name FROM darwin_institution ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($_POST['institutioncode']) && ($row2[0] == $_POST['institutioncode']))?' selected="selected"':((!isset($_POST['institutioncode']) && isset($row[0]) && ($row2[0] == $row[0]))?' selected="selected"':'')) . ">$row2[1]</option>";
        }
      }
?></select>
              <br />
            </div>
            <div>
              <label for="collectioncode"><strong><?php print _("Collection"); ?></strong></label>
              <select name="collectioncode" id="collectioncode" title="<?php print _("The full name of the Collection / Project"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT collectioncode, name FROM darwin_collection ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($_POST['collectioncode']) && ($row2[0] == $_POST['collectioncode']))?' selected="selected"':((!isset($_POST['collectioncode']) && isset($row[1]) && ($row2[0] == $row[1]))?' selected="selected"':'')) . ">$row2[1]</option>";
        }
      }
?></select>
              <br />
            </div>
            <div>
              <label for="catalognumber"><strong><?php print _("Catalog Number"); ?></strong></label>
              <input name="catalognumber" id="catalognumber" type="text" maxlength="128" title="<?php print _("The alphanumeric value identifying an individual organism record within the collection"); ?>"<?php print (!empty($_POST['catalognumber'])?' value="' . stripslashes(strip_tags(trim($_POST['catalognumber']))) . '"':(!isset($_POST['catalognumber']) && isset($row[2])?' value="' . $row[2] . '"':'')); ?> />
              <br />
            </div>
            <div>
              <label for="observer"><?php print _("Author"); ?></label>
              <select name="observer" id="observer" title="<?php print _("The name of the collector of the original data for the specimen or observation"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT username, real_name FROM darwin_users ORDER BY real_name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($_POST['observer']) && ($row2[0] == $_POST['observer']))?' selected="selected"':((!isset($_POST['observer']) && isset($row[6]) && ($row2[0] == $row[6]))?' selected="selected"':'')) . ">$row2[1]</option>";
        }
      }
?></select>
                <br />
              </div>
              <div>
                <label for="validdistributionflag"><?php print _("Restricted distribution?"); ?></label>
                <input type="checkbox" name="validdistributionflag" id="validdistributionflag" rel="distribution"<?php print (!empty($_POST['validdistributionflag'])?' checked="checked"':((!isset($_POST['observer']) && isset($row[3]) && ($row[3] == 'f'))?' checked="checked"':'')); ?> />
                <br />
              </div>
              <div rel="distribution">
                <label for="informationwithheld"><?php print _("Information with held"); ?></label>
                <textarea name="informationwithheld" id="informationwithheld" rows="4" cols="30" title="<?php print _("Brief descriptions of additional information that may exist, but that has not been made public"); ?>"><?php print ((isset($_POST['informationwithheld']) && !fullyempty($_POST['informationwithheld']))?stripslashes(strip_tags(trim($_POST['informationwithheld']))):(!isset($_POST['informationwithheld']) && isset($row[4])?$row[4]:'')); ?></textarea>
                <br />
              </div>
              <div>
                <label for="taxon"><strong><?php print _("Species"); ?></strong></label>
                <select name="taxon" id="taxon" title="<?php print _("The full latin name of the species (if known)"); ?>"><option rel="none" value=""></option><option rel="newtaxon" value="newtaxon"<?php print ((isset($_POST['taxon']) && ($_POST['taxon'] == 'newtaxon'))?' selected="selected"':((!isset($_POST['taxon']) && isset($row[8]))?' selected="selected"':'')); ?>>[unknown]</option><?php
      $result = sql_query('SELECT scientificname, commonname FROM tree_taxonomy ORDER BY scientificname;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option rel=\"none\" value=\"$row2[0]\"" . ((isset($_POST['taxon']) && ($row2[0] == $_POST['taxon']))?' selected="selected"':((!isset($_POST['taxon']) && isset($row[7]) && ($row2[0] == $row[7]))?' selected="selected"':'')) . ">$row2[0]" . (isset($row2[1])?' - ' . $row2[1]:'') . '</option>';
        }
      }
?></select>
                <br />
              </div>
              <div rel="newtaxon">
                <label for="newspecies"><strong><?php print _("Proposed Species"); ?></strong></label>
                <input name="newspecies" id="newspecies" type="text" maxlength="128" title="<?php print _("The full latin name of the species / Genus"); ?>"<?php print (!empty($_POST['newspecies'])?' value="' . stripslashes(strip_tags(trim($_POST['newspecies']))) . '"':(!isset($_POST['newspecies']) && isset($row[8])?' value="' . $row[7] . '"':'')); ?> />
                <br />
              </div>
              <div rel="newtaxon">
                <label for="newtaxon"><strong><?php print _("Proposed Taxonomy"); ?></strong></label>
                <textarea name="newtaxon" id="newtaxon" rows="4" cols="30" title="<?php print _("The combination of names of taxonomic ranks [kingdom ; phylum ; subphylum ; class ; subclass ; infraclass ; order ; suborder ; family ; subfamily ; genus ; species ; subspecies]"); ?>"><?php print ((isset($_POST['newtaxon']) && !fullyempty($_POST['newtaxon']))?stripslashes(strip_tags(trim($_POST['newtaxon']))):(!isset($_POST['newtaxon']) && isset($row[8])?$row[8]:'')); ?></textarea>
                <br />
              </div>
              <div>
                <label for="identificationqualifier"><?php print _("Identification"); ?></label>
                <select name="identificationqualifier" id="identificationqualifier" title="<?php print _("A standard term to qualify the identification of the specimen when doubts have arisen as to its identity"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT name FROM darwin_identificationqualifier ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($_POST['identificationqualifier']) && ($row2[0] == $_POST['identificationqualifier']))?' selected="selected"':((!isset($_POST['identificationqualifier']) && isset($row[9]) && ($row2[0] == $row[9]))?' selected="selected"':'')) . ">$row2[0]</option>";
        }
      }
?></select>
                <br />
              </div>
              <div>
                <label for="sex"><?php print _("Sex"); ?></label>
                <select name="sex" id="sex" title="<?php print _("The sex of a biological individual represented by the cataloged specimen or observation"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT name FROM darwin_sex ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($_POST['sex']) && ($row2[0] == $_POST['sex']))?' selected="selected"':((!isset($_POST['sex']) && isset($row[10]) && ($row2[0] == $row[10]))?' selected="selected"':'')) . ">$row2[0]</option>";
        }
      }
?></select>
                <br />
              </div>
              <div>
                <label for="lifestage"><?php print _("Life Stage"); ?></label>
                <select name="lifestage" id="lifestage" title="<?php print _("The age class, reproductive stage, or life stage of the biological individual referred to by the record"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT name FROM darwin_lifestage ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($_POST['lifestage']) && ($row2[0] == $_POST['lifestage']))?' selected="selected"':((!isset($_POST['lifestage']) && isset($row[11]) && ($row2[0] == $row[11]))?' selected="selected"':'')) . ">$row2[0]</option>";
        }
      }
?></select>
                <br />
              </div>
              <div>
                <label for="reproductiveevidence"><?php print _("Reproductive Evidences"); ?></label>
                <textarea name="reproductiveevidence" id="reproductiveevidence" rows="4" cols="30" title="<?php print _("Reproductive Evidences"); ?>"><?php print ((isset($_POST['reproductiveevidence']) && !fullyempty($_POST['reproductiveevidence']))?stripslashes(strip_tags(trim($_POST['reproductiveevidence']))):(!isset($_POST['reproductiveevidence']) && isset($row[12])?$row[12]:'')); ?></textarea>
                <br />
              </div>
              <div>
                <label for="density"><?php print _("Density"); ?></label>
                <select name="density" id="density" title="<?php print _("The distribution of the specimen on the landscape"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT name FROM darwin_density ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($_POST['density']) && ($row2[0] == $_POST['density']))?' selected="selected"':((!isset($_POST['density']) && isset($row[13]) && ($row2[0] == $row[13]))?' selected="selected"':'')) . ">$row2[0]</option>";
        }
      }
?></select>
                <br />
              </div>
              <div>
                <label for="conditionelement"><?php print _("Condition"); ?></label>
                <select name="conditionelement" id="conditionelement" title="<?php print _("Description of the quality of specimen"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT name FROM darwin_conditionelement ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($_POST['conditionelement']) && ($row2[0] == $_POST['conditionelement']))?' selected="selected"':((!isset($_POST['conditionelement']) && isset($row[14]) && ($row2[0] == $row[14]))?' selected="selected"':'')) . ">$row2[0]</option>";
        }
      }
?></select>
                <br />
              </div>
              <div>
                <label for="observedsize"><?php print _("Observed Size"); ?></label>
                <input name="observedsize" id="observedsize" type="text" maxlength="32" title="<?php print _("The size of the sample from which the collection/observation was drawn in centimeter"); ?>"<?php print ((isset($_POST['observedsize']) && !fullyempty($_POST['observedsize']))?' value="' . floatval($_POST['observedsize']) . '"':(!isset($_POST['observedsize']) && isset($row[15])?' value="' . $row[15] . '"':'')); ?> class="half" /> cm
                <br />
              </div>
              <div>
                <label for="observedweight"><?php print _("Observed Weight"); ?></label>
                <input name="observedweight" id="observedweight" type="text" maxlength="32" title="<?php print _("Observed weight in kg"); ?>"<?php print ((isset($_POST['observedweight']) && !fullyempty($_POST['observedweight']))?' value="' . floatval($_POST['observedweight']) . '"':(!isset($_POST['observedweight']) && isset($row[16])?' value="' . $row[16] . '"':'')); ?> class="half" /> kg
                <br />
              </div>
              <div>
                <label for="remark"><?php print _("Remark"); ?></label>
                <textarea name="remark" id="remark" rows="4" cols="30" title="<?php print _("General remark"); ?>"><?php print ((isset($_POST['remark']) && !fullyempty($_POST['remark']))?stripslashes(strip_tags(trim($_POST['remark']))):(!isset($_POST['remark']) && isset($row[17])?$row[17]:'')); ?></textarea>
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
    if (!empty($_POST['darwin']) && ($_POST['darwin'] == md5('add' . floor(intval(date('b'))))) && !empty($_POST['institutioncode']) && !empty($_POST['collectioncode']) && !empty($_POST['taxon']) && ((($_POST['taxon'] == 'newtaxon') && !empty($_POST['newspecies']) && (strlen($_POST['newspecies']) > 10) && (isset($_POST['newtaxon']) && !fullyempty($_POST['newtaxon'])) && (strlen($_POST['newtaxon']) > 10)) || ($_POST['taxon'] != 'newtaxon'))) {
      if (!empty($_POST['event'])) {
        $ret=explode('|',$_POST['event'],2);
        $result = sql_query('SELECT geolocation, datecollected FROM darwin_environment WHERE geolocation=\'' . addslashes(stripslashes(strip_tags($ret[0]))) . '\' AND datecollected=\'' . addslashes(stripslashes(strip_tags($ret[1]))) . '\';', $sql);
        if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
          $row = sql_fetch_row($result);
          $geolocation = $row[0];
          $_POST['event'] = $row[1];
        }else {
          unset($_POST['event']);
        }
      }
      $prefix = floor(((intval(date('Y', time())) - 2001) * 12 + intval(date('m', time())) - 1) / 1.5);
      $catalognumber = md5(uniqid(rand(), true));
      $result = sql_query('INSERT INTO darwin_bioject (prefix, id, institutioncode, collectioncode, catalognumber, observer, validdistributionflag, informationwithheld, geolocation, event, scientificname, taxon, identificationqualifier, sex, lifestage, reproductiveevidence, density, conditionelement, observedsize, observedweight, comments, author) SELECT ' . $prefix . ', CASE WHEN max(id)>=1 THEN max(id)+1 ELSE 1 END, \'' . addslashes(stripslashes(strip_tags(trim($_POST['institutioncode'])))) . '\',\'' . addslashes(stripslashes(strip_tags(trim($_POST['collectioncode'])))) . '\',\'' . $catalognumber . '\',' . (!empty($_POST['observer'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['observer'])))) . '\'':'NULL') . ',' . (!empty($_POST['validdistributionflag'])?'\'f\'':'\'t\'') . ',' . ((isset($_POST['informationwithheld']) && !fullyempty($_POST['informationwithheld']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['informationwithheld'])))) . '\'':'NULL') . ',' . (isset($geolocation)?'\'' . addslashes($geolocation) . '\'':'NULL') . ',' . ((isset($_POST['event']) && !fullyempty($_POST['event']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['event'])))) . '\'':'NULL') . ',' . (($_POST['taxon'] == 'newtaxon')?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['newspecies'])))) . '\',\'' . addslashes(stripslashes(strip_tags(trim($_POST['newtaxon'])))) . '\'':'\'' . addslashes(stripslashes(strip_tags(trim($_POST['taxon'])))) . '\',NULL') . ',' . (!empty($_POST['identificationqualifier'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['identificationqualifier'])))) . '\'':'NULL') . ',' . (!empty($_POST['sex'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['sex'])))) . '\'':'NULL') . ',' . (!empty($_POST['lifestage'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['lifestage'])))) . '\'':'NULL') . ',' . ((isset($_POST['reproductiveevidence']) && !fullyempty($_POST['reproductiveevidence']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['reproductiveevidence'])))) . '\'':'NULL') . ',' . (!empty($_POST['density'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['density'])))) . '\'':'NULL') . ',' . (!empty($_POST['conditionelement'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['conditionelement'])))) . '\'':'NULL') . ',' . ((isset($_POST['observedsize']) && !fullyempty($_POST['observedsize']))?'\'' . floatval($_POST['observedsize']) . '\'':'NULL') . ',' . ((isset($_POST['observedweight']) && !fullyempty($_POST['observedweight']))?'\'' . floatval($_POST['observedweight']) . '\'':'NULL') . ',' . ((isset($_POST['remark']) && !fullyempty($_POST['remark']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['remark'])))) . '\'':'NULL') . ',\'' . addslashes($_SESSION['login']['username']) . '\' FROM darwin_bioject WHERE prefix=' . $prefix . ';', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        $result = sql_query('SELECT prefix, id FROM darwin_bioject WHERE (prefix=' . $prefix . ' AND catalognumber=\'' . $catalognumber . '\');', $sql);
        if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
          $row = sql_fetch_row($result);
          $result = sql_query('UPDATE darwin_bioject SET catalognumber=\'' . (!empty($_POST['catalognumber'])?addslashes(stripslashes(strip_tags(trim($_POST['catalognumber'])))):'B' . decoct($row[0]) . '.' . decoct($row[1])) . '\' WHERE (prefix=' . $prefix . ' AND catalognumber=\'' . $catalognumber . '\');', $sql);
          header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/bioject/B' . decoct($row[0]) . '.' . decoct($row[1]));
          exit;
        }else {
          $error = _("Database entry error:") . ' ' . $r;
        }
      }
    }
    head('darwin', true);
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url']; ?>/bioject/add">
        <div>
          <h2><?php print _("New specimen"); ?><small><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/help/specimen" title="<?php print _("Help"); ?>"><?php print _("Help"); ?></a></small></h2><br /><?php print _("You can specify the specie, condition and reference of the entry."); ?><br /><br />
          <div>
            <label for="advanced"><?php print _("Advanced"); ?></label>
            <input type="checkbox" name="advanced" id="advanced" rel="advanced"<?php print (!empty($_POST['advanced'])?' checked="checked"':''); ?> />
            <br />
          </div>
          <div>
            <label for="event"><?php print _("Collecting event"); ?></label>
            <select name="event" id="event" title="<?php print _("The full name of a collecting event"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT geolocation, datecollected FROM darwin_environment ORDER BY geolocation, datecollected;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]|$row[1]\"" . ((!empty($_POST['event']) && (($row[0].'|'.$row[1]) == $_POST['event']))?' selected="selected"':'') . ">$row[0] / " . ((substr($row[1],-8)!='00:00:00')?date(_("d-m-Y H:i:s"), strtotime($row[1])):((substr($row[1], -15) == '-01-01 00:00:00')?date(_("Y"), strtotime($row[1])):date(_("d-m-Y"), strtotime($row[1])))) . "</option>";
      }
    }
?></select> [<a href="<?php print $config['server'] . $plugin['darwin']['url'] ?>/event/add">add</a>]
            <br />
          </div>
           <div>
            <label for="institutioncode"><strong><?php print _("Institution"); ?></strong></label>
            <select name="institutioncode" id="institutioncode" title="<?php print _("The full name of the Institution involved"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT institutioncode, name FROM darwin_institution ORDER BY name;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((!empty($_POST['institutioncode']) && ($row[0] == $_POST['institutioncode']))?' selected="selected"':'') . ">$row[1]</option>";
      }
    }
?></select> [<a href="<?php print $config['server'] . $plugin['darwin']['url'] ?>/institution/add">add</a>]
            <br />
          </div>
          <div>
            <label for="collectioncode"><strong><?php print _("Collection"); ?></strong></label>
            <select name="collectioncode" id="collectioncode" title="<?php print _("The full name of the Institution involved"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT collectioncode, name FROM darwin_collection ORDER BY name;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((!empty($_POST['collectioncode']) && ($row[0] == $_POST['collectioncode']))?' selected="selected"':'') . ">$row[1]</option>";
      }
    }
?></select> [<a href="<?php print $config['server'] . $plugin['darwin']['url'] ?>/collection/add">add</a>]
            <br />
          </div>
          <div rel="advanced">
            <label for="catalognumber"><?php print _("Catalog Number"); ?></label>
            <input name="catalognumber" id="catalognumber" type="text" maxlength="128" title="<?php print _("The alphanumeric value identifying an individual specimen record within the collection"); ?>"<?php print (!empty($_POST['catalognumber'])?' value="' . stripslashes(strip_tags(trim($_POST['catalognumber']))) . '"':''); ?> />
            <br />
          </div>
          <div>
            <label for="observer"><?php print _("Author"); ?></label>
            <select name="observer" id="observer" title="<?php print _("The name of the collector of the original data for the specimen or observation."); ?>"><option value=""></option><?php
    $result = sql_query('SELECT username, real_name FROM darwin_users ORDER BY real_name;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((!empty($_POST['observer']) && ($row[0] == $_POST['observer']))?' selected="selected"':'') . ">$row[1]</option>";
      }
    }
?></select> [<a href="<?php print $config['server'] . $plugin['darwin']['url'] ?>/author/add">add</a>]
           <br />
          </div>
          <div rel="advanced">
            <label for="validdistributionflag"><?php print _("Restricted distribution?"); ?></label>
            <input type="checkbox" name="validdistributionflag" id="validdistributionflag" rel="distribution"<?php print (!empty($_POST['validdistributionflag'])?' checked="checked"':''); ?> />
            <br />
          </div>
          <div rel="distribution">
            <label for="informationwithheld"><?php print _("Information with held"); ?></label>
            <textarea name="informationwithheld" id="informationwithheld" rows="4" cols="30" title="<?php print _("Brief descriptions of additional information that may exist, but that has not been made public"); ?>"><?php print ((isset($_POST['informationwithheld']) && !fullyempty($_POST['informationwithheld']))?stripslashes(strip_tags(trim($_POST['informationwithheld']))):''); ?></textarea>
            <br />
          </div>
          <div>
            <label for="taxon"><strong><?php print _("Species"); ?></strong></label>
            <select name="taxon" id="taxon" title="<?php print _("The full latin name of the species (if known)"); ?>"><option rel="none" value=""></option><option rel="newtaxon" value="newtaxon"<?php print ((!empty($_POST['taxon']) && ($_POST['taxon'] == 'newtaxon'))?' selected="selected"':''); ?>>[unknown]</option><?php
    $result = sql_query('SELECT scientificname, commonname FROM tree_taxonomy ORDER BY scientificname;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option rel=\"none\" value=\"$row[0]\"" . ((!empty($_POST['taxon']) && ($row[0] == $_POST['taxon']))?' selected="selected"':'') . ">$row[0]" . (isset($row[1])?' - ' . $row[1]:'') . '</option>';
      }
    }
?></select> [<a href="<?php print $config['server'] . $plugin['tree']['url'] ?>/species/add">add</a>]
            <br />
          </div>
          <div rel="newtaxon">
            <label for="newspecies"><strong><?php print _("Proposed Species"); ?></strong></label>
            <input name="newspecies" id="newspecies" type="text" maxlength="128" title="<?php print _("The full latin name of the species / Genus"); ?>"<?php print (!empty($_POST['newspecise'])?' value="' . stripslashes(strip_tags(trim($_POST['newspecie']))) . '"':''); ?> />
            <br />
          </div>
          <div rel="newtaxon">
            <label for="newtaxon"><strong><?php print _("Proposed taxonomy"); ?></strong></label>
            <textarea name="newtaxon" id="newtaxon" rows="4" cols="30" title="<?php print _("The combination of names of taxonomic ranks [kingdom ; phylum ; subphylum ; class ; subclass ; infraclass ; order ; suborder ; family ; subfamily ; genus ; species ; subspecies]"); ?>"><?php print ((isset($_POST['newtaxon']) && !fullyempty($_POST['newtaxon']))?stripslashes(strip_tags(trim($_POST['newtaxon']))):''); ?></textarea>
            <br />
          </div>
          <div>
            <label for="identificationqualifier"><?php print _("Identification"); ?></label>
            <select name="identificationqualifier" id="identificationqualifier" title="<?php print _("A standard term to qualify the identification of the specimen when doubts have arisen as to its identity"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT name FROM darwin_identificationqualifier ORDER BY name;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((!empty($_POST['identificationqualifier']) && ($row[0] == $_POST['identificationqualifier']))?' selected="selected"':'') . ">$row[0]</option>";
      }
    }
?></select>
            <br />
          </div>
          <div rel="advanced">
            <label for="sex"><?php print _("Sex"); ?></label>
            <select name="sex" id="sex" title="<?php print _("The sex of a biological individual represented by the catalogedspecimen or observation"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT name FROM darwin_sex ORDER BY name;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((!empty($_POST['sex']) && ($row[0] == $_POST['sex']))?' selected="selected"':'') . ">$row[0]</option>";
      }
    }
?></select>
            <br />
          </div>
          <div rel="advanced">
            <label for="lifestage"><?php print _("Life Stage"); ?></label>
            <select name="lifestage" id="lifestage" title="<?php print _("The age class, reproductive stage, or life stage of the biological individual referred to by the record"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT name FROM darwin_lifestage ORDER BY name;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((!empty($_POST['lifestage']) && ($row[0] == $_POST['lifestage']))?' selected="selected"':'') . ">$row[0]</option>";
      }
    }
?></select>
            <br />
          </div>
          <div rel="advanced">
            <label for="reproductiveevidence"><?php print _("Reproductive Evidences"); ?></label>
            <textarea name="reproductiveevidence" id="reproductiveevidence" rows="4" cols="30" title="<?php print _("Reproductive Evidences"); ?>"><?php print ((isset($_POST['reproductiveevidence']) && !fullyempty($_POST['reproductiveevidence']))?stripslashes(strip_tags(trim($_POST['reproductiveevidence']))):''); ?></textarea>
            <br />
          </div>
          <div rel="advanced">
            <label for="density"><?php print _("Density"); ?></label>
            <select name="density" id="density" title="<?php print _("The distribution of the specimen on the landscape"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT name FROM darwin_density ORDER BY name;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((!empty($_POST['density']) && ($row[0] == $_POST['density']))?' selected="selected"':'') . ">$row[0]</option>";
      }
    }
?></select>
            <br />
          </div>
          <div rel="advanced">
            <label for="conditionelement"><?php print _("Condition"); ?></label>
            <select name="conditionelement" id="conditionelement" title="<?php print _("Description of the quality of specimen"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT name FROM darwin_conditionelement ORDER BY name;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((!empty($_POST['conditionelement']) && ($row[0] == $_POST['conditionelement']))?' selected="selected"':'') . ">$row[0]</option>";
      }
    }
?></select>
            <br />
          </div>
          <div rel="advanced">
            <label for="observedsize"><?php print _("Observed Size"); ?></label>
            <input name="observedsize" id="observedsize" type="text" maxlength="32" title="<?php print _("The size of the sample from which the collection/observation was drawn in centimeter"); ?>"<?php print ((isset($_POST['observedsize']) && !fullyempty($_POST['observedsize']))?' value="' . floatval($_POST['observedsize']) . '"':''); ?> class="half" /> cm
            <br />
          </div>
          <div rel="advanced">
            <label for="observedweight"><?php print _("Observed Weight"); ?></label>
            <input name="observedweight" id="observedweight" type="text" maxlength="32" title="<?php print _("Observed weight in kg"); ?>"<?php print ((isset($_POST['observedweight']) && !fullyempty($_POST['observedweight']))?' value="' . floatval($_POST['observedweight']) . '"':''); ?> class="half" /> kg
            <br />
          </div>
          <div rel="advanced">
            <label for="remark"><?php print _("Remark"); ?></label>
            <textarea name="remark" id="remark" rows="4" cols="30" title="<?php print _("General remark"); ?>"><?php print ((isset($_POST['remark']) && !fullyempty($_POST['remark']))?stripslashes(strip_tags(trim($_POST['remark']))):''); ?></textarea>
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
  }elseif (!empty($_GET['bioject']) && preg_match('/B(\d+)\.(\d+)/', rawurldecode($_GET['bioject']), $matches)) {
    head('darwin');
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php
    $sql = sql_connect($config['db']);
    $result = sql_query('SELECT a.institutioncode, c.name, a.collectioncode, b.name , a.catalognumber, a.updated, a.author, d.code, a.validdistributionflag, a.informationwithheld, a.geolocation, a.event, a.observer, a.attributes, a.scientificname, a.taxon, a.identificationqualifier, a.sex, a.lifestage, a.reproductiveevidence, a.density, a.conditionelement, a.observedsize, a.observedweight, a.comments FROM darwin_bioject AS a, darwin_collection AS b, darwin_institution AS c, users AS d WHERE a.prefix=' . octdec(intval($matches[1])) . ' AND a.id=' . octdec(intval($matches[2])) . ' AND b.collectioncode=a.collectioncode AND c.institutioncode=a.institutioncode AND d.username=a.author;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
?>
        <div>
          <h2><?php print $row[0] . '-' . $row[2] . '-' . $row[4] . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['darwin']['url'] . '/bioject/edit/' . rawurlencode($matches[0]) . '" title="' . _("Edit") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
      print '            <div class="semocode"><img src="' . $config['server'] . $plugin['darwin']['url'] . '/semacode/' . rawurlencode(str_rot13($row[0] . '-' . $row[2] . '-' . $row[4])) . "\" width=\"100\" height=\"100\" alt=\"\" /></div>\n";
      print '            <h3>' . ("Details") . "</h3>\n";
      print '            <div class="details"><div class="title">' . _("Reference") . '</div><div class="label">' . $row[0] . '-' . $row[2] . '-' . $row[4] . "</div></div>\n";
      print '            <div class="details"><div class="title">' . _("Short ID") . '</div><div class="label">' . $matches[0] . "</div></div><br />\n";
      print '            <div class="details"><div class="title">' . _("Institution") . '</div><div class="label">' . $row[1] . ' [' . $row[0] . "]</div></div>\n";
      print '            <div class="details"><div class="title">' . _("Collection") . '</div><div class="label">' . $row[3] . ' [' . $row[2] . "]</div></div>\n";
      print '            <div class="details"><div class="title">' . _("Catalog Number") . '</div><div class="label">' . $row[4] . "</div></div>\n";
      print '            <div class="details"><div class="title">' . _("Release") . '</div><div class="label">' . gmdate(_("d-m-Y"), strtotime($row[5])) . ' <span class="grey">(' . $row[7] . $row[6] . ")</span></div></div>\n";
      if (!empty($row[8]) && ($row[8] == 'f')) {
        print '          <h3>' . ("Restriction") . "</h3>\n";
        print '          <div class="details"><div class="title">' . _("Distribution") . "</div><div class=\"label\">Restricted</div></div>\n";
        if (!empty($row[9])) print '          <div class="details"><div class="label">' . htmlentities($row[9], ENT_COMPAT, 'ISO-8859-1') . "</div></div>\n";
      }
      if (!empty($row[10]) || !empty($row[11]) || !empty($row[12])) {
        print '          <h3>' . ("Origin") . "</h3>\n";
        if (!empty($row[10]) && !empty($row[11])) print '          <div class="details"><div class="title">' . _("Location name") . '</div><div class="label"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/geolocation/' . rawurlencode($row[10]) . '">' . $row[10] . "</a></div></div>\n";
        if (!empty($row[11])) print '          <div class="details"><div class="title">' . _("Collecting date") . '</div><div class="label"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/event/' . rawurlencode($row[10]) . '/' . rawurlencode($row[11]) . '">' . ((substr($row[11],-8)!='00:00:00')?date(_("d-m-Y H:i:s"), strtotime($row[11])):((substr($row[11], -15) == '-01-01 00:00:00')?date(_("Y"), strtotime($row[11])):date(_("d-m-Y"), strtotime($row[11])))) . "</a></div></div>\n";
        if (!empty($row[12])) {
          $result = sql_query('SELECT real_name FROM darwin_users WHERE username=\'' . $row[12] . '\';', $sql);
          if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
            $row2 = sql_fetch_row($result);
            print '          <div class="details"><div class="title">' . _("Author") . '</div><div class="label"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/author/' . rawurlencode($row[12]) . '">' . $row2[0] . "</a></div></div>\n";
          }
        }
      }
      print '          <h3>' . ("Organism") . "</h3>\n";
      if (!empty($row[15])) {
        print '          <div class="details"><div class="title">' . _("Proposed Name") . '</div><div class="label"><em>' . $row[14] . "</em></div></div>\n";
        print '          <div class="details"><div class="title">' . _("Taxon") . '</div><div class="label">' . $row[15] . "</div></div>\n";
      }else {
        print '          <div class="details"><div class="title">' . _("Latin Name") . '</div><div class="label"><em><a href="' . $config['server'] . $plugin['tree']['url'] . '/species/' . rawurlencode(str_replace(' ', '_', $row[14])) . '">' . $row[14] . "</a></em></div></div>\n";
      }
      if (!empty($row[16])) print '          <div class="details"><div class="title">' . _("Identification") . '</div><div class="label">' . $row[16] . "</div></div>\n";
      if (!empty($row[17])) print '          <div class="details"><div class="title">' . _("Sex") . '</div><div class="label">' . $row[17] . "</div></div>\n";
      if (!empty($row[18])) print '          <div class="details"><div class="title">' . _("Life Stage") . '</div><div class="label">' . $row[18] . "</div></div>\n";
      if (!empty($row[19])) print '          <div class="details"><div class="title">' . _("Evidences") . '</div><div class="label">' . htmlentities($row[19], ENT_COMPAT, 'ISO-8859-1') . "</div></div>\n";
      if (!empty($row[20])) print '          <div class="details"><div class="title">' . _("Density") . '</div><div class="label">' . $row[20] . "</div></div>\n";
      if (!empty($row[21])) print '          <div class="details"><div class="title">' . _("Condition") . '</div><div class="label">' . $row[21] . "</div></div>\n";
      if (!empty($row[22])) print '          <div class="details"><div class="title">' . _("Size") . '</div><div class="label">' . $row[22] . " cm</div></div>\n";
      if (!empty($row[23])) print '          <div class="details"><div class="title">' . _("Weight") . '</div><div class="label">' . $row[23] . " kg</div></div>\n";
      if (!empty($row[13])) {
        print '          <h3>' . ("Attributs") . "</h3>\n";
        foreach(explode('|', $row[13]) as $subattr) {
          $attr = explode('=', $subattr, 2);
          print '          <div class="details"><div class="title">' . $attr[0] . '</div><div class="label">' . $attr[1] . "</div></div>\n";
        }
      }
      if (!empty($row[24])) print '          <h3>' . ("Comments") . "</h3>\n" . '            <div class="details">' . preg_replace('/\[([^\|\]]*)\|([^\|\]]*)\]/', '<a href="\1">\2</a><br />', htmlentities($row[24], ENT_COMPAT, 'ISO-8859-1')) . "</div>\n";
      print '        </div>';
      print "        <div>\n          <h2>" . _("Samples") . (($_SESSION['login']['right'] >= 2)?'<small><a href="' . $config['server'] . $plugin['darwin']['url'] . '/sample/add/' . $matches[0] . '" title="' . _("Add a new sample") . '">' . _("add") . '</a></small>':'') . "</h2>\n";
      $result = sql_query('SELECT prefix, id, subcatalognumber, basisofrecord, partname FROM darwin_sample WHERE (bioject_prefix=' . octdec(intval($matches[1])) . ' AND bioject_id=' . octdec(intval($matches[2])) . ');', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print '          <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/sample/D' . decoct($row2[0]) . '.' . decoct($row2[1]) . '">' . ucfirst($row2[3]) . '</a></span><span class="desc">' . $row[0] . '-' . $row[2] . '-' . $row[4] . '-' . $row2[2] . '</span><span class="detail">' . ucfirst($row2[4]) . "</span></div>\n";
        }
      }else {
        print '          <div><em>' . _("none") . "</em></div>\n";
      }
      print "        </div>\n";
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
        <div><h2><?php print _("Add a specimen in the collection"); ?><small><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/bioject/add" title="<?php print _("Add a new specimen"); ?>"><?php print _("Add a specimen..."); ?></a></small></h2><br /><?php print _("You may add a new entry (specimen) in the collection."); ?><br /></div>
<?php }
?>
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url']; ?>/bioject/search">
        <div>
          <h2><?php print _("Search"); ?></h2><br /><?php print _("Retrive a specimen. You may provide a reference numbre, or a date."); ?><br /><br />
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
        $result = sql_query('SELECT prefix, id, institutioncode, collectioncode, catalognumber, scientificname FROM darwin_bioject WHERE (institutioncode=\'' . addslashes($matches[1]) . '\' AND collectioncode=\'' . addslashes($matches[2]) . '\' AND catalognumber=\'' . addslashes($matches[3]) . '\');', $sql);
      }else {
        $result = sql_query('SELECT prefix, id, institutioncode, collectioncode, catalognumber, scientificname FROM darwin_bioject WHERE (geolocation' . sql_reg(addslashes($search)) . ' OR catalognumber' . sql_reg(addslashes($search)) . ' OR comments' . sql_reg(addslashes($search)) . ');', $sql);
      }
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        print "        <div>\n          <h2>" . _("Results") . "</h2>\n";
        while ($row = sql_fetch_row($result)) {
          print '          <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/bioject/B' . decoct($row[0]) . '.' . decoct($row[1]) . '">' . $row[2] . '-' . $row[3] . '-' . $row[4] . '</a></span><span class="desc"><em>' . $row[5] . "</em></span></div>\n";
        }
        print "        </div>\n";
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