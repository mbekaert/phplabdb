<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  if (($_SESSION['login']['right'] >= 3) && !empty($_GET['edit'])) {
    $sql = sql_connect($config['db']);
    if (isset($_POST['remove']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(trim(rawurldecode($_GET['edit']))) . floor(intval(date('b')))))) {
      $result = sql_query('DELETE FROM darwin_geolocation WHERE geolocation=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['edit'])))) . '\';', $sql);
      header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/geolocation');
      exit;
    }elseif (isset($_POST['edit']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(trim(rawurldecode($_GET['edit']))) . floor(intval(date('b'))))) && !empty($_POST['habitatcategory']) && !empty($_POST['continentocean']) && isset($_POST['minimumlatitude']) && ((floatval($_POST['minimumlatitude']) >= -90) || (floatval($_POST['minimumlatitude']) <= 90)) && isset($_POST['minimumlongitude']) && ((floatval($_POST['minimumlongitude']) >= -180) || (floatval($_POST['minimumlongitude']) <= 180))) {
      $result = sql_query('UPDATE darwin_geolocation SET continentocean=\'' . addslashes(stripslashes(strip_tags(trim($_POST['continentocean'])))) . '\', islandgroup=' . (!empty($_POST['islandgroup'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['islandgroup'])))) . '\'':'NULL') . ', island=' . (!empty($_POST['island'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['island'])))) . '\'':'NULL') . ', country=' . (!empty($_POST['country'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['country'])))) . '\'':'NULL') . ', stateprovince=' . (!empty($_POST['stateprovince'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['stateprovince'])))) . '\'':'NULL') . ', county=' . (!empty($_POST['county'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['county'])))) . '\'':'NULL') . ', locality=' . (!empty($_POST['locality'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['locality'])))) . '\'':'NULL') . ', minimumelevation=' . ((isset($_POST['minimumelevation']) && !fullyempty($_POST['minimumelevation']))?intval($_POST['minimumelevation']):'NULL') . ', maximumelevation=' . ((isset($_POST['maximumelevation']) && !fullyempty($_POST['maximumelevation']))?intval($_POST['maximumelevation']):'NULL') . ', minimumdepth=' . ((isset($_POST['minimumdepth']) && !fullyempty($_POST['minimumdepth']))?intval($_POST['minimumdepth']):'NULL') . ', maximumdepth=' . ((isset($_POST['maximumdepth']) && !fullyempty($_POST['maximumdepth']))?intval($_POST['maximumdepth']):'NULL') . ', minimumlatitude=' . floatval($_POST['minimumlatitude']) . ', maximumlatitude=' . ((isset($_POST['maximumlatitude']) && !fullyempty($_POST['maximumlatitude']))?floatval($_POST['maximumlatitude']):'NULL') . ', minimumlongitude=' . floatval($_POST['minimumlongitude']) . ', maximumlongitude=' . ((isset($_POST['maximumlongitude']) && !fullyempty($_POST['maximumlongitude']))?floatval($_POST['maximumlongitude']):'NULL') . ', geodeticdatum=' . (!empty($_POST['geodeticdatum'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['geodeticdatum'])))) . '\'':'NULL') . ', coordinateuncertainty=' . ((isset($_POST['coordinateuncertainty']) && !fullyempty($_POST['coordinateuncertainty']))?intval($_POST['coordinateuncertainty']):'NULL') . ', feature=' . (!empty($_POST['feature'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['feature'])))) . '\'':'NULL') . ', comments=' . ((isset($_POST['description']) && !fullyempty($_POST['description']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['description'])))) . '\'':'NULL') . ', gis=' . ((isset($_POST['gis']) && !fullyempty($_POST['gis']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['gis'])))) . '\'':'NULL') . ', directions=' . (!empty($_POST['directions'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['directions'])))) . '\'':'NULL') . ', mapping=' . ((isset($_POST['mapping']) && !fullyempty($_POST['mapping']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['mapping'])))) . '\'':'NULL') . ', habitat=' . ((isset($_POST['habitat']) && !fullyempty($_POST['habitat']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['habitat'])))) . '\'':'NULL') . ', habitatcategory=\'' . addslashes(stripslashes(strip_tags(trim($_POST['habitatcategory'])))) . '\', updated=NOW(), author=\'' . addslashes($_SESSION['login']['username']) . '\' WHERE geolocation=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['edit'])))) . '\';', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/geolocation/' . rawurlencode(strip_tags(trim(rawurldecode($_GET['edit'])))));
        exit;
      }
    }
    head('darwin');
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php
    $result = sql_query('SELECT geolocation, continentocean, islandgroup, island, country, stateprovince, county, locality, minimumelevation, maximumelevation, minimumdepth, maximumdepth, minimumlatitude, maximumlatitude, minimumlongitude, maximumlongitude, geodeticdatum, coordinateuncertainty, feature, comments, gis, directions, mapping, habitat, habitatcategory FROM darwin_geolocation WHERE geolocation=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['edit'])))) . '\' ;', $sql);
    if (!strlen($r = sql_last_error($sql)) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result); ?>
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url'] . '/geolocation/edit/' . rawurlencode(stripslashes(strip_tags($row[0]))); ?>">
        <div>
          <h2><?php print $row[0]; ?></h2><br />
          <div>
            <label for="continentocean"><strong><?php print _("Continent/Ocean"); ?></strong></label>
            <select name="continentocean" id="continentocean" title="<?php print _("The full, unabbreviated name of the continent/water body of a collecting event"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT reference, name FROM darwin_continentocean ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . (!empty($_POST['continentocean'])?(($row2[0] == $_POST['continentocean'])?' selected="selected"':''):((isset($row[1]) && ($row2[0] == $row[1]))?' selected="selected"':'')) . ">$row2[1]</option>";
        }
      }
?></select>
            <br />
          </div>
          <div>
            <label for="country"><?php print _("Country"); ?></label>
            <select name="country" id="country" title="<?php print _("The full, unabbreviated name of the country or major political unit of a collecting event"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT iana, name FROM darwin_country ORDER BY name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . (!empty($_POST['country'])?(($row2[0] == $_POST['country'])?' selected="selected"':''):((isset($row[4]) && ($row2[0] == $row[4]))?' selected="selected"':'')) . ">$row2[1]</option>";
        }
      }
?></select>
            <br />
          </div>
          <div>
            <label for="islandgroup"><?php print _("Island Group"); ?></label>
            <input name="islandgroup" id="islandgroup" type="text" maxlength="128" title="<?php print _("The full, unabbreviated name of the island group of a collecting event"); ?>"<?php print (!empty($_POST['islandgroup'])?' value="' . stripslashes(strip_tags(trim($_POST['islandgroup']))) . '"':(isset($row[2])?' value="' . $row[2] . '"':'')); ?> />
            <br />
          </div>
          <div>
            <label for="island"><?php print _("Island"); ?></label>
            <input name="island" id="island" type="text" maxlength="128" title="<?php print _("The full, unabbreviated name of the island of a collecting event"); ?>"<?php print (!empty($_POST['island'])?' value="' . stripslashes(strip_tags(trim($_POST['island']))) . '"':(isset($row[3])?' value="' . $row[3] . '"':'')); ?> />
            <br />
          </div>
          <div>
            <label for="stateprovince"><?php print _("State/Province"); ?></label>
            <input name="stateprovince" id="stateprovince" type="text" maxlength="128" title="<?php print _("The full, unabbreviated name of the state, province, or region of a collecting event"); ?>"<?php print (!empty($_POST['stateprovince'])?' value="' . stripslashes(strip_tags(trim($_POST['stateprovince']))) . '"':(isset($row[5])?' value="' . $row[5] . '"':'')); ?> />
            <br />
          </div>
          <div>
            <label for="county"><?php print _("County"); ?></label>
            <input name="county" id="county" type="text" maxlength="128" title="<?php print _("The full, unabbreviated name of the county, shire, or municipality of a collecting event. The next smaller political region than State/Province"); ?>"<?php print (!empty($_POST['county'])?' value="' . stripslashes(strip_tags(trim($_POST['county']))) . '"':(isset($row[6])?' value="' . $row[6] . '"':'')); ?> />
            <br />
          </div>
          <div>
            <label for="locality"><?php print _("Locality"); ?></label>
            <input name="locality" id="locality" type="text" maxlength="128" title="<?php print _("The description of the locality of a collecting event. Need not contain geographic information provided in other geographic fields"); ?>"<?php print (!empty($_POST['locality'])?' value="' . stripslashes(strip_tags(trim($_POST['locality']))) . '"':(isset($row[7])?' value="' . $row[7] . '"':'')); ?> />
            <br />
          </div>
          <div>
            <label for="latitude"><strong><?php print _("Latitude"); ?></strong></label>
            <input name="minimumlatitude" id="minimumlatitude" type="text" maxlength="16" title="<?php print _("The (minimum) latitude of a collecting event, expressed in fractional degrees (e.g. 53.308231)"); ?>"<?php print ((isset($_POST['minimumlatitude']) && !fullyempty($_POST['minimumlatitude']))?' value="' . floatval($_POST['minimumlatitude']) . '"':(isset($row[12])?' value="' . $row[12] . '"':'')); ?> class="half" /> -
            <input name="maximumlatitude" id="maximumlatitude" type="text" maxlength="16" title="<?php print _("The maximum latitude of a collecting event, expressed in fractional degrees"); ?>"<?php print ((isset($_POST['maximumlatitude']) && !fullyempty($_POST['maximumlatitude']))?' value="' . floatval($_POST['maximumlatitude']) . '"':(isset($row[13])?' value="' . $row[13] . '"':'')); ?> class="half" />
            <br />
          </div>
          <div>
            <label for="longitude"><strong><?php print _("Longitude"); ?></strong></label>
            <input name="minimumlongitude" id="minimumlongitude" type="text" maxlength="16" title="<?php print _("The (minimum) longitude of a collecting event, expressed in fractional degrees (e.g. -6.225488)"); ?>"<?php print ((isset($_POST['minimumlongitude']) && !fullyempty($_POST['minimumlongitude']))?' value="' . floatval($_POST['minimumlongitude']) . '"':(isset($row[14])?' value="' . $row[14] . '"':'')); ?> class="half" /> -
            <input name="maximumlongitude" id="maximumlongitude" type="text" maxlength="16" title="<?php print _("The maximum longitude of a collecting event, expressed in fractional degrees"); ?>"<?php print ((isset($_POST['maximumlongitude']) && !fullyempty($_POST['maximumlongitude']))?' value="' . floatval($_POST['maximumlongitude']) . '"':(isset($row[15])?' value="' . $row[15] . '"':'')); ?> class="half" />
            <br />
          </div>
          <div>
            <label for="elevation"><?php print _("Elevation"); ?></label>
            <input name="minimumelevation" id="minimumelevation" type="text" maxlength="16" title="<?php print _("The (minimum) altitude in meters above (positive) or below (negative) sea level"); ?>"<?php print ((isset($_POST['minimumelevation']) && !fullyempty($_POST['minimumelevation']))?' value="' . intval($_POST['minimumelevation']) . '"':(isset($row[8])?' value="' . $row[8] . '"':'')); ?> class="half" /> -
            <input name="maximumelevation" id="maximumelevation" type="text" maxlength="16" title="<?php print _("The maximum altitude in meters above (positive) or below (negative) sea level"); ?>"<?php print ((isset($_POST['maximumelevatio']) && !fullyempty($_POST['maximumelevation']))?' value="' . intval($_POST['maximumelevation']) . '"':(isset($row[9])?' value="' . $row[9] . '"':'')); ?> class="half" />
            <br />
          </div>
          <div>
            <label for="depth"><?php print _("Depth"); ?></label>
            <input name="minimumdepth" id="minimumdepth" type="text" maxlength="16" title="<?php print _("The (minimum) depth in meters below the surface of the water at which the specimen was made; Use positive values for locations below the surface"); ?>"<?php print ((isset($_POST['minimumdepth']) && !fullyempty($_POST['minimumdepth']))?' value="' . intval($_POST['minimumdepth']) . '"':(isset($row[10])?' value="' . $row[10] . '"':'')); ?> class="half" /> -
            <input name="maximumdepth" id="maximumdepth" type="text" maxlength="16" title="<?php print _("The maximum depth in meters below the surface of the water at which the specimen was made; Use positive values for locations below the surface"); ?>"<?php print ((isset($_POST['maximumdepth']) && !fullyempty($_POST['maximumdepth']))?' value="' . intval($_POST['maximumdepth']) . '"':(isset($row[11])?' value="' . $row[11] . '"':'')); ?> class="half" />
            <br />
          </div>
          <div>
            <label for="geodeticdatum"><?php print _("Geodetic Datum"); ?></label>
            <select name="geodeticdatum" id="geodeticdatum" title="<?php print _("The geodetic datum to which the latitude and longitude refer, or the method by which the location was determined"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT name, description FROM darwin_datum ORDER BY description;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . (!empty($_POST['geodeticdatum'])?(($row2[0] == $_POST['geodeticdatum'])?' selected="selected"':''):((isset($row[16]) && ($row2[0] == $row[16]))?' selected="selected"':'')) . ">$row2[1]</option>";
        }
      }
?></select>
            <br />
          </div>
          <div>
            <label for="coordinateuncertainty"><?php print _("Coordinate Uncertainty"); ?></label>
            <input name="coordinateuncertainty" id="coordinateuncertainty" type="text" maxlength="16" title="<?php print _("The upper limit of the distance (in meters) from the given latitude and longitude describing a circle within which the whole of the described locality must lie"); ?>"<?php print ((isset($_POST['coordinateuncertainty']) && !fullyempty($_POST['coordinateuncertainty']))?' value="' . intval($_POST['coordinateuncertainty']) . '"':(isset($row[17])?' value="' . $row[17] . '"':'')); ?> class="half" /> m
            <br />
          </div>
          <div>
            <label for="feature"><?php print _("Feature"); ?></label>
            <input name="feature" id="feature" type="text" maxlength="128" title="<?php print _("Features include entities such as parks, preserves, refuges, and other delineated geo-political features. Feature may also be used to describe recognized sub-groups of islands. Many administrative units included in Feature (e.g., Alaska Game Management Units) have ephemeral boundaries, if not an ephemeral existance. Their past and future use may be inconsistent. Therefore, avoid using Feature if the locality is well georeferenced and/or unequivocal in the absence of Feature"); ?>"<?php print (!empty($_POST['feature'])?' value="' . stripslashes(strip_tags(trim($_POST['feature']))) . '"':(isset($row[18])?' value="' . $row[18] . '"':'')); ?> />
            <br />
          </div>
          <div>
            <label for="habitatcategory"><strong><?php print _("Habitat Category"); ?></strong></label>
            <select name="habitatcategory" id="habitatcategory" title="<?php print _("Habitat type"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT reference, name, description FROM darwin_habitatcategory ORDER BY description, name;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          if (empty($last) || ($last != $row2[2])) print (!empty($last)?'</optgroup>':'') . "<optgroup label=\"$row2[2]\">";
          print "<option value=\"$row2[0]\"" . (!empty($_POST['habitatcategory'])?(($row2[0] == $_POST['habitatcategory'])?' selected="selected"':''):((isset($row[24]) && ($row2[0] == $row[24]))?' selected="selected"':'')) . ">$row2[1]</option>";
          $last = $row2[2];
        }
      }
?></select>
            <br />
          </div>
          <div>
            <label for="habitat"><?php print _("Habitat"); ?></label>
            <textarea name="habitat" id="habitat" rows="4" cols="30" title="<?php print _("Description of the local or surrounding habitat"); ?>"><?php print ((isset($_POST['habitat']) && !fullyempty($_POST['habitat']))?stripslashes(strip_tags(trim($_POST['habitat']))):(isset($row[23])?$row[23]:'')); ?></textarea>
            <br />
          </div>
          <div>
            <label for="gis"><?php print _("GIS"); ?></label>
            <textarea name="gis" id="gis" rows="4" cols="30" title="<?php print _("Link to GIS features (point, line or polygon)"); ?>"><?php print ((isset($_POST['gis']) && !fullyempty($_POST['gis']))?stripslashes(strip_tags(trim($_POST['gis']))):(isset($row[20])?$row[20]:'')); ?></textarea>
            <br />
          </div>
          <div>
            <label for="directions"><?php print _("Directions"); ?></label>
            <textarea name="directions" id="directions" rows="4" cols="30" title="<?php print _("Precise directions to the site of the observation"); ?>"><?php print ((isset($_POST['directions']) && !fullyempty($_POST['directions']))?stripslashes(strip_tags(trim($_POST['directions']))):(isset($row[21])?$row[21]:'')); ?></textarea>
            <br />
          </div>
          <div>
            <label for="mapping"><?php print _("Mapping"); ?></label>
            <textarea name="mapping" id="mapping" rows="4" cols="30" title="<?php print _("Ability to map the Observation precisely"); ?>"><?php print ((isset($_POST['mapping']) && !fullyempty($_POST['mapping']))?stripslashes(strip_tags(trim($_POST['mapping']))):(isset($row[22])?$row[22]:'')); ?></textarea>
            <br />
          </div>
          <div>
            <label for="description"><?php print _("Description"); ?></label>
            <textarea name="description" id="description" rows="4" cols="30" title="<?php print _("Description of the location where the observation was made or the area that was searched"); ?>"><?php print ((isset($_POST['description']) && !fullyempty($_POST['description']))?stripslashes(strip_tags(trim($_POST['description']))):(isset($row[19])?$row[19]:'')); ?></textarea>
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
    if (!empty($_POST['darwin']) && ($_POST['darwin'] == md5('add' . floor(intval(date('b'))))) && !empty($_POST['geolocation']) && (strlen(stripslashes(strip_tags(trim($_POST['geolocation'])))) > 2) && !empty($_POST['habitatcategory']) && !empty($_POST['continentocean']) && (isset($_POST['minimumlatitude']) && !fullyempty($_POST['minimumlatitude'])) && ((floatval($_POST['minimumlatitude']) >= -90) || (floatval($_POST['minimumlatitude']) <= 90)) && (isset($_POST['minimumlongitude']) && !fullyempty($_POST['minimumlongitude'])) && ((floatval($_POST['minimumlongitude']) >= -180) || (floatval($_POST['minimumlongitude']) <= 180))) {
      $result = sql_query('INSERT INTO darwin_geolocation (geolocation, continentocean, islandgroup, island, country, stateprovince, county, locality, minimumelevation, maximumelevation, minimumdepth, maximumdepth, minimumlatitude, maximumlatitude, minimumlongitude, maximumlongitude, geodeticdatum, coordinateuncertainty, feature, comments, gis, directions, mapping, habitat, habitatcategory, author) VALUES (\'' . addslashes(ucfirst(stripslashes(strip_tags(trim($_POST['geolocation']))))) . '\',\'' . addslashes(stripslashes(strip_tags(trim($_POST['continentocean'])))) . '\',' . (!empty($_POST['islandgroup'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['islandgroup'])))) . '\'':'NULL') . ',' . (!empty($_POST['island'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['island'])))) . '\'':'NULL') . ',' . (!empty($_POST['country'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['country'])))) . '\'':'NULL') . ',' . (!empty($_POST['stateprovince'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['stateprovince'])))) . '\'':'NULL') . ',' . (!empty($_POST['county'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['county'])))) . '\'':'NULL') . ',' . (!empty($_POST['locality'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['locality'])))) . '\'':'NULL') . ',' . ((isset($_POST['minimumelevation']) && !fullyempty($_POST['minimumelevation']))?intval($_POST['minimumelevation']):'NULL') . ',' . ((isset($_POST['maximumelevation']) && !fullyempty($_POST['maximumelevation']))?intval($_POST['maximumelevation']):'NULL') . ',' . ((isset($_POST['minimumdepth']) && !fullyempty($_POST['minimumdepth']))?intval($_POST['minimumdepth']):'NULL') . ',' . ((isset($_POST['maximumdepth']) && !fullyempty($_POST['maximumdepth']))?intval($_POST['maximumdepth']):'NULL') . ',' . floatval($_POST['minimumlatitude']) . ',' . ((isset($_POST['maximumlatitude']) && !fullyempty($_POST['maximumlatitude']))?floatval($_POST['maximumlatitude']):'NULL') . ',' . floatval($_POST['minimumlongitude']) . ',' . ((isset($_POST['maximumlongitude']) && !fullyempty($_POST['maximumlongitude']))?floatval($_POST['maximumlongitude']):'NULL') . ',' . (!empty($_POST['geodeticdatum'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['geodeticdatum'])))) . '\'':'NULL') . ',' . ((isset($_POST['coordinateuncertainty']) && !fullyempty($_POST['coordinateuncertainty']))?intval($_POST['coordinateuncertainty']):'NULL') . ',' . (!empty($_POST['feature'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['feature'])))) . '\'':'NULL') . ',' . ((isset($_POST['description']) && !fullyempty($_POST['description']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['description'])))) . '\'':'NULL') . ',' . ((isset($_POST['gis']) && !fullyempty($_POST['gis']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['gis'])))) . '\'':'NULL') . ',' . ((isset($_POST['directions']) && !fullyempty($_POST['directions']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['directions'])))) . '\'':'NULL') . ',' . ((isset($_POST['mapping']) && !fullyempty($_POST['mapping']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['mapping'])))) . '\'':'NULL') . ',' . ((isset($_POST['habitat']) && !fullyempty($_POST['habitat']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['habitat'])))) . '\'':'NULL') . ',\'' . addslashes(stripslashes(strip_tags(trim($_POST['habitatcategory'])))) . '\',\'' . addslashes($_SESSION['login']['username']) . '\');', $sql);
      if (!strlen($r = sql_last_error($sql))) {
        header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/geolocation/' . rawurlencode(ucfirst(stripslashes(strip_tags(trim($_POST['geolocation']))))));
        exit;
      }
    }
    head('darwin', true);
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url']; ?>/geolocation/add">
        <div>
          <h2><?php print _("New location"); ?><small><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/help/geolocation" title="<?php print _("Help"); ?>"><?php print _("Help"); ?></a></small></h2><br /><?php print _("You can specify the place where a collecting event was conducted."); ?><br /><br />
          <div>
            <label for="advanced"><?php print _("Advanced"); ?></label>
            <input type="checkbox" name="advanced" id="advanced" rel="advanced"<?php print (!empty($_POST['advanced'])?' checked="checked"':''); ?> />
            <br />
          </div>
          <div>
            <label for="geolocation"><strong><?php print _("Location name"); ?></strong></label>
            <input name="geolocation" id="geolocation" type="text" maxlength="128" title="<?php print _("The full, unabbreviated name of site"); ?>"<?php print (!empty($_POST['geolocation'])?' value="' . stripslashes(strip_tags(trim($_POST['geolocation']))) . '"':''); ?> />
            <br />
          </div>
          <div>
            <label for="continentocean"><strong><?php print _("Continent/Ocean"); ?></strong></label><select name="continentocean" id="continentocean" title="<?php print _("The full, unabbreviated name of the continent/water body  of a collecting event"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT reference, name FROM darwin_continentocean ORDER BY name;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((!empty($_POST['continentocean']) && ($row[0] == $_POST['continentocean']))?' selected="selected"':'') . ">$row[1]</option>";
      }
    }
?></select>
            <br />
          </div>
          <div>
            <label for="country"><?php print _("Country"); ?></label><select name="country" id="country" title="<?php print _("The full, unabbreviated name of the country or major political unit from of a collecting event"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT iana, name FROM darwin_country ORDER BY name;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((!empty($_POST['country']) && ($row[0] == $_POST['country']))?' selected="selected"':'') . ">$row[1]</option>";
      }
    }
?></select>
            <br />
          </div>
          <div rel="advanced">
            <label for="islandgroup"><?php print _("Island Group"); ?></label>
            <input name="islandgroup" id="islandgroup" type="text" maxlength="128" title="<?php print _("The full, unabbreviated name of the island group of a collecting event"); ?>"<?php print (!empty($_POST['islandgroup'])?' value="' . stripslashes(strip_tags(trim($_POST['islandgroup']))) . '"':''); ?> />
            <br />
          </div>
          <div rel="advanced">
            <label for="island"><?php print _("Island"); ?></label>
            <input name="island" id="island" type="text" maxlength="128" title="<?php print _("The full, unabbreviated name of the island of a collecting event"); ?>"<?php print (!empty($_POST['island'])?' value="' . stripslashes(strip_tags(trim($_POST['island']))) . '"':''); ?> />
            <br />
          </div>
          <div rel="advanced">
            <label for="stateprovince"><?php print _("State/Province"); ?></label>
            <input name="stateprovince" id="stateprovince" type="text" maxlength="128" title="<?php print _("The full, unabbreviated name of the state, province, or region of a collecting event"); ?>"<?php print (!empty($_POST['stateprovince'])?' value="' . stripslashes(strip_tags(trim($_POST['stateprovince']))) . '"':''); ?> />
            <br />
          </div>
          <div rel="advanced">
            <label for="county"><?php print _("County"); ?></label>
            <input name="county" id="county" type="text" maxlength="128" title="<?php print _("The full, unabbreviated name of the county, shire, or municipality of a collecting event. The next smaller political region than State/Province"); ?>"<?php print (!empty($_POST['county'])?' value="' . stripslashes(strip_tags(trim($_POST['county']))) . '"':''); ?> />
            <br />
          </div>
          <div rel="advanced">
            <label for="locality"><?php print _("Locality"); ?></label>
            <input name="locality" id="locality" type="text" maxlength="128" title="<?php print _("The description of the locality of a collecting event. Need not contain geographic information provided in other geographic fields"); ?>"<?php print (!empty($_POST['locality'])?' value="' . stripslashes(strip_tags(trim($_POST['locality']))) . '"':''); ?> />
            <br />
          </div>
          <div>
            <label for="latitude"><strong><?php print _("Latitude"); ?></strong></label>
            <input name="minimumlatitude" id="minimumlatitude" type="text" maxlength="16" title="<?php print _("The (minimum) latitude of a collecting event, expressed in fractional degrees (e.g. 53.308231)"); ?>"<?php print ((isset($_POST['minimumlatitude']) && !fullyempty($_POST['minimumlatitude']))?' value="' . floatval(trim($_POST['minimumlatitude'])) . '"':''); ?> class="half" /> -
            <input name="maximumlatitude" id="maximumlatitude" type="text" maxlength="16" title="<?php print _("The maximum latitude of a collecting event, expressed in fractional degrees"); ?>"<?php print ((isset($_POST['maximumlatitude']) && !fullyempty($_POST['maximumlatitude']))?' value="' . floatval(trim($_POST['maximumlatitude'])) . '"':''); ?> class="half" />
            <br />
          </div>
          <div>
            <label for="longitude"><strong><?php print _("Longitude"); ?></strong></label>
            <input name="minimumlongitude" id="minimumlongitude" type="text" maxlength="16" title="<?php print _("The (minimum) longitude of a collecting event, expressed in fractional degrees (e.g. -6.225488)"); ?>"<?php print ((isset($_POST['minimumlongitude']) && !fullyempty($_POST['minimumlongitude']))?' value="' . floatval(trim($_POST['minimumlongitude'])) . '"':''); ?> class="half" /> -
            <input name="maximumlongitude" id="maximumlongitude" type="text" maxlength="16" title="<?php print _("The maximum longitude of a collecting event, expressed in fractional degrees"); ?>"<?php print ((isset($_POST['maximumlongitude']) && !fullyempty($_POST['maximumlongitude']))?' value="' . floatval(trim($_POST['maximumlongitude'])) . '"':''); ?> class="half" />
            <br />
          </div>
          <div rel="advanced">
            <label for="elevation"><?php print _("Elevation"); ?></label>
            <input name="minimumelevation" id="minimumelevation" type="text" maxlength="16" title="<?php print _("The (minimum) altitude in meters above (positive) or below (negative) sea level"); ?>"<?php print ((isset($_POST['minimumelevation']) && !fullyempty($_POST['minimumelevation']))?' value="' . floatval(trim($_POST['minimumelevation'])) . '"':''); ?> class="half" /> -
            <input name="maximumelevation" id="maximumelevation" type="text" maxlength="16" title="<?php print _("The maximum altitude in meters above (positive) or below (negative) sea level"); ?>"<?php print ((isset($_POST['maximumelevation']) && !fullyempty($_POST['maximumelevation']))?' value="' . floatval(trim($_POST['maximumelevation'])) . '"':''); ?> class="half" />
            <br />
          </div>
          <div rel="advanced">
            <label for="depth"><?php print _("Depth"); ?></label>
            <input name="minimumdepth" id="minimumdepth" type="text" maxlength="16" title="<?php print _("The (minimum) depth in meters below the surface of the water at which the specimen was made. Use positive values for locations below the surface"); ?>"<?php print ((isset($_POST['minimumdepth']) && !fullyempty($_POST['minimumdepth']))?' value="' . floatval(trim($_POST['minimumdepth'])) . '"':''); ?> class="half" /> -
            <input name="maximumdepth" id="maximumdepth" type="text" maxlength="16" title="<?php print _("The maximum depth in meters below the surface of the water at which the specimen was made. Use positive values for locations below the surface"); ?>"<?php print ((isset($_POST['maximumdepth']) && !fullyempty($_POST['maximumdepth']))?' value="' . floatval(trim($_POST['maximumdepth'])) . '"':''); ?> class="half" />
            <br />
          </div>
          <div>
            <label for="geodeticdatum"><?php print _("Geodetic Datum"); ?></label><select name="geodeticdatum" id="geodeticdatum" title="<?php print _("The geodetic datum to which the latitude and longitude refer, or the method by which the location was determined"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT name, description FROM darwin_datum ORDER BY description;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((!empty($_POST['geodeticdatum']) && ($row[0] == $_POST['geodeticdatum']))?' selected="selected"':'') . ">$row[1]</option>";
      }
    }
?></select>
            <br />
          </div>
          <div rel="advanced">
            <label for="coordinateuncertainty"><?php print _("Coordinate Uncertainty"); ?></label>
            <input name="coordinateuncertainty" id="coordinateuncertainty" type="text" maxlength="16" title="<?php print _("The upper limit of the distance (in meters) from the given latitude and longitude describing a circle within which the whole of the described locality must lie"); ?>"<?php print ((isset($_POST['coordinateuncertainty']) && !fullyempty($_POST['coordinateuncertainty']))?' value="' . intval(trim($_POST['coordinateuncertainty'])) . '"':''); ?> class="half" /> m
            <br />
          </div>
          <div rel="advanced">
            <label for="feature"><?php print _("Feature"); ?></label>
            <input name="feature" id="feature" type="text" maxlength="128" title="<?php print _("Features include entities such as parks, preserves, refuges, and other delineated geo-political features. Feature may also be used to describe recognized sub-groups of islands. Many administrative units included in Feature (e.g., Alaska Game Management Units) have ephemeral boundaries, if not an ephemeral existance. Their past and future use may be inconsistent. Therefore, avoid using Feature if the locality is well georeferenced and/or unequivocal in the absence of Feature"); ?>"<?php print (!empty($_POST['feature'])?' value="' . stripslashes(strip_tags(trim($_POST['feature']))) . '"':''); ?> />
            <br />
          </div>
          <div>
            <label for="habitatcategory"><strong><?php print _("Habitat Category"); ?></strong></label><select name="habitatcategory" id="habitatcategory" title="<?php print _("Habitat type"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT reference, name, description FROM darwin_habitatcategory ORDER BY description, name;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        if (empty($last) || ($last != $row[2])) print (!empty($last)?'</optgroup>':'') . "<optgroup label=\"$row[2]\">";
        print "<option value=\"$row[0]\"" . ((!empty($_POST['habitatcategory']) && ($row[0] == $_POST['habitatcategory']))?' selected="selected"':'') . ">$row[1]</option>";
        $last = $row[2];
      }
    }
?></select>
            <br />
          </div>
          <div rel="advanced">
            <label for="habitat"><?php print _("Habitat"); ?></label>
            <textarea name="habitat" id="habitat" rows="4" cols="30" title="<?php print _("Description of the local or surrounding habitat"); ?>"><?php print ((isset($_POST['habitat']) && !fullyempty($_POST['habitat']))?stripslashes(strip_tags(trim($_POST['habitat']))):''); ?></textarea>
            <br />
          </div>
          <div rel="advanced">
            <label for="gis"><?php print _("GIS"); ?></label>
            <textarea name="gis" id="gis" rows="4" cols="30" title="<?php print _("Link to GIS features (point, line or polygon)"); ?>"><?php print ((isset($_POST['gis']) && !fullyempty($_POST['gis']))?stripslashes(strip_tags(trim($_POST['gis']))):''); ?></textarea>
            <br />
          </div>
          <div rel="advanced">
            <label for="directions"><?php print _("Directions"); ?></label>
            <textarea name="directions" id="directions" rows="4" cols="30" title="<?php print _("Precise directions to the site of the observation"); ?>"><?php print ((isset($_POST['directions']) && !fullyempty($_POST['directions']))?stripslashes(strip_tags(trim($_POST['directions']))):''); ?></textarea>
            <br />
          </div>
          <div rel="advanced">
            <label for="mapping"><?php print _("Mapping"); ?></label>
            <textarea name="mapping" id="mapping" rows="4" cols="30" title="<?php print _("Ability to map the Observation precisely"); ?>"><?php print ((isset($_POST['mapping']) && !fullyempty($_POST['mapping']))?stripslashes(strip_tags(trim($_POST['mapping']))):''); ?></textarea>
            <br />
          </div>
          <div rel="advanced">
            <label for="description"><?php print _("Description"); ?></label>
            <textarea name="description" id="description" rows="4" cols="30" title="<?php print _("Description of the location where the observation was made or the area that was searched"); ?>"><?php print ((isset($_POST['description']) && !fullyempty($_POST['description']))?stripslashes(strip_tags(trim($_POST['description']))):''); ?></textarea>
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
  }elseif (!empty($_GET['geolocation'])) {
    head('darwin', false, true);
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php
    $sql = sql_connect($config['db']);
    $result = sql_query('SELECT a.geolocation, b.name, a.islandgroup, a.island, a.country, a.stateprovince, a.county, a.locality, a.minimumelevation, a.maximumelevation, a.minimumdepth, a.maximumdepth, a.minimumlatitude, a.maximumlatitude, a.minimumlongitude, a.maximumlongitude, a.geodeticdatum, a.coordinateuncertainty, a.feature, a.comments, a.gis, a.directions, a.mapping, a.habitat, c.name, c.description FROM darwin_geolocation AS a, darwin_continentocean AS b, darwin_habitatcategory AS c WHERE a.geolocation=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['geolocation'])))) . '\' AND b.reference=a.continentocean AND c.reference=a.habitatcategory;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
?>
        <div>
          <h2><?php print $row[0] . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['darwin']['url'] . '/geolocation/edit/' . rawurlencode(stripslashes(strip_tags($row[0]))) . '" title="' . _("Edit") . '">' . _("edit") . '</a></small>':''); ?></h2>
          <div id="map" style="width: 300px; height: 300px"></div><script type="text/javascript">
//<![CDATA[
 load(<?php print $row[12] . ',' . $row[14] . ((isset($row[13]) && isset($row[15]))?',' . $row[13] . ',' . $row[15]:''); ?>);
//]]>
</script>
<?php
      print '          <h3>' . ("Details") . "</h3>\n";
      print '          <div class="details"><div class="title">' . _("Location name") . '</div><div class="label">' . $row[0] . "</div></div>\n";
      print '          <div class="details"><div class="title">' . _("Continent/Ocean") . '</div><div class="label">' . $row[1] . "</div></div>\n";
      if (!empty($row[4])) {
        $result = sql_query('SELECT name FROM darwin_country WHERE iana=\'' . $row[4] . '\';', $sql);
        if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
          $row_country = sql_fetch_row($result);
          print '            <div class="details"><div class="title">' . _("Country") . '</div><div class="label">' . $row_country[0] . "</div></div>\n";
        }
      }
      if (!empty($row[2]) || !empty($row[3]) || !empty($row[5]) || !empty($row[6]) || !empty($row[7])) print '            <h3>' . ("Locality") . "</h3>\n";
      if (!empty($row[2])) print '          <div class="details"><div class="title">' . _("Island Group") . '</div><div class="label">' . $row[2] . "</div></div>\n";
      if (!empty($row[3])) print '          <div class="details"><div class="title">' . _("Island") . '</div><div class="label">' . $row[3] . "</div></div>\n";
      if (!empty($row[5])) print '          <div class="details"><div class="title">' . _("State/Province") . '</div><div class="label">' . $row[5] . "</div></div>\n";
      if (!empty($row[6])) print '          <div class="details"><div class="title">' . _("County") . '</div><div class="label">' . $row[6] . "</div></div>\n";
      if (!empty($row[7])) print '          <div class="details"><div class="title">' . _("Locality") . '</div><div class="label">' . $row[7] . "</div></div>\n";
      print '          <h3>' . ("Positioning") . "</h3>\n";
      print '          <div class="details"><div class="title">' . _("Latitude") . '</div><div class="label">' . (($row[12] < 0)?'S':'N') . abs($row[12]) . ((isset($row[13]))? ' / ' . (($row[13] < 0)?'S':'N') . abs($row[13]):'') . "</div></div>\n";
      print '          <div class="details"><div class="title">' . _("Longitude") . '</div><div class="label">' . (($row[14] < 0)?'W':'E') . abs($row[14]) . ((isset($row[15]))? ' / ' . (($row[15] < 0)?'W':'E') . abs($row[15]):'') . "</div></div>\n";
      if (isset($row[8])) print '          <div class="details"><div class="title">' . _("Elevation") . '</div><div class="label">' . $row[8] . ((isset($row[9]))? ' m / ' . $row[9]:'') . " m</div></div>\n";
      if (isset($row[10])) print '          <div class="details"><div class="title">' . _("Depth") . '</div><div class="label">' . $row[10] . ((isset($row[11]))? ' m / ' . $row[11]:'') . " m</div></div>\n";
      if (!empty($row[16])) {
        $result = sql_query('SELECT description FROM darwin_datum WHERE name=\'' . $row[16] . '\';', $sql);
        if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
          $row_datum = sql_fetch_row($result);
          print '          <div class="details"><div class="title">' . _("Geodetic Datum") . '</div><div class="label">' . $row_datum[0] . "</div></div>\n";
        }
      }
      if (isset($row[17])) print '          <div class="details"><div class="title">' . _("Uncertainty") . '</div><div class="label">' . $row[17] . " m</div></div>\n";
      print '          <h3>' . ("Habitat") . "</h3>\n";
      print '          <div class="details"><div class="title">' . _("Category") . '</div><div class="label">' . $row[24] . ' (' . $row[25] . ")</div></div>\n";
      if (!empty($row[18])) print '          <div class="details"><div class="title">' . _("Feature") . '</div><div class="label">' . $row[18] . "</div></div>\n";
      if (!empty($row[23])) print '          <div class="details">' . htmlentities($row[23], ENT_COMPAT, 'ISO-8859-1') . "</div>\n";
      if (!empty($row[20])) print '          <h3>' . _("GIS") . "</h3>\n" . '          <div class="details">' . preg_replace('/\[([^\|\]]*)\|([^\|\]]*)\]/', '<a href="\1">\2</a><br />', htmlentities($row[20], ENT_COMPAT, 'ISO-8859-1')) . "</div>\n";
      if (!empty($row[21])) print '          <h3>' . _("Directions") . "</h3>\n" . '          <div class="details">' . preg_replace('/\[([^\|\]]*)\|([^\|\]]*)\]/', '<a href="\1">\2</a><br />', htmlentities($row[21], ENT_COMPAT, 'ISO-8859-1')) . "</div>\n";
      if (!empty($row[22])) print '          <h3>' . _("Mapping") . "</h3>\n" . '          <div class="details">' . preg_replace('/\[([^\|\]]*)\|([^\|\]]*)\]/', '<a href="\1">\2</a><br />', htmlentities($row[22], ENT_COMPAT, 'ISO-8859-1')) . "</div>\n";
      if (!empty($row[19])) print '          <h3>' . _("Description") . "</h3>\n" . '          <div class="details">' . preg_replace('/\[([^\|\]]*)\|([^\|\]]*)\]/', '<a href="\1">\2</a><br />', htmlentities($row[19], ENT_COMPAT, 'ISO-8859-1')) . "</div>\n";
      print '        </div>';
      $result = sql_query('SELECT geolocation, datecollected, conditions FROM darwin_environment WHERE geolocation=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['geolocation'])))) . '\';', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        include('icons/icon.inc');
        print "        <div>\n          <h2>" . _("Related collecting events") . "</h2>\n";
        while ($row2 = sql_fetch_row($result)) {
          print '          <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/event/' . rawurlencode($row2[0]) . '/' . rawurlencode($row2[1]) . '">' . $row2[0] . '</a></span><span class="detail">' . ((substr($row2[1], -8) != '00:00:00')?date(_("d-m-Y H:i:s"), strtotime($row2[1])) . ' - ' . isdawn(strtotime($row2[1]),$row[12],$row[14]) :((substr($row2[1], -15) == '-01-01 00:00:00')?date(_("Y"), strtotime($row2[1])):date(_("d-m-Y"), strtotime($row2[1])))).'</span><span class="updated"><img src="' . $config['server'] . $plugin['darwin']['url'] . '/icons/' . $icon_map[$row2[2]]['icon'] . '.png' . "\" width=\"16\" height=\"16\" /></span></div>\n";
        }
        print "        </div>\n";
      }
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
        <div><h2><?php print _("Add Geo-Location"); ?><small><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/geolocation/add" title="<?php print _("Add a new location"); ?>"><?php print _("Add a location..."); ?></a></small></h2><br /><?php print _("You may add a new location before add a new collection entry."); ?><br /></div>
<?php }
?>
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url']; ?>/geolocation/search">
        <div>
          <h2><?php print _("Search"); ?></h2><br /><?php print _("Retrive a location. You may provide a reference numbre, or a name."); ?><br /><br />
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
      $result = sql_query('SELECT a.geolocation, b.name, a.locality, c.name FROM darwin_geolocation AS a, darwin_continentocean AS b, darwin_habitatcategory AS c WHERE (a.geolocation' . sql_reg(addslashes($search)) . ' OR b.name' . sql_reg(addslashes($search)) . ' OR a.locality' . sql_reg(addslashes($search)) . ') AND b.reference=a.continentocean AND c.reference=a.habitatcategory;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        print "        <div>\n          <h2>" . _("Results (current locations)") . "</h2>\n";
        while ($row = sql_fetch_row($result)) {
          print '          <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/geolocation/' . rawurlencode($row[0]) . '">' . $row[0] . '</a></span><span class="desc">' . $row[1] . '</span><span class="detail">' . $row[2] . '</span><span class="updated">' . $row[3] . "</span></div>\n";
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