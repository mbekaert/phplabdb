<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login']) {
  if (($_SESSION['login']['right'] >= 3) && !empty($_GET['status']) && ( $_GET['status']=='edit' ) && !empty($_GET['event']) && !empty($_GET['geolocation']) && (($timestamp = strtotime($_GET['event'])) !== false)) {
    $sql = sql_connect($config['db']);
    if (isset($_POST['remove']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(trim(rawurldecode($_GET['geolocation']))).strip_tags(trim(rawurldecode($_GET['event']))). floor(intval(date('b')))))) {
      $result = sql_query('DELETE FROM darwin_environment WHERE geolocation=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['geolocation'])))) . '\' AND datecollected=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['event'])))) . '\';', $sql);
      header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/event');
      exit;
    }elseif (isset($_POST['edit']) && !empty($_POST['key']) && ($_POST['key'] == md5(strip_tags(trim(rawurldecode($_GET['geolocation']))).strip_tags(trim(rawurldecode($_GET['event']))). floor(intval(date('b'))))) && !empty($_POST['geolocation']) && !empty($_POST['datecollected']) && (($timestamp = strtotime($_POST['datecollected'])) !== false)) {
      require_once('metar.php');
      if (!empty($_POST['entry']) && ($_POST['entry'] == 'metar') && !empty($_POST['metar'])) {
        $metar = new WeatherIconMetar(stripslashes(strip_tags(trim($_POST['metar']))));
        if (!empty($metar->decoded_metar['altimeter']['raw']) && !empty($metar->decoded_metar['temperature']['raw']) && !empty($metar->decoded_metar['visibility'][0]['raw'])) {
          $weather = '';
          if (count($metar->decoded_metar['weather']) > 0) {
            foreach ($metar->decoded_metar['weather'] as $count => $w) {
              if ($count <> 0) $weather .= ' ';
              $weather .= $w['raw'];
            }
          }
          $clouds = '';
          if (count($metar->decoded_metar['clouds']) > 0) {
            foreach ($metar->decoded_metar['clouds'] as $count => $w) {
              if ($count <> 0) $clouds .= ' ';
              $clouds .= $w['raw'];
            }
          }
          $visibility = '';
          if (count($metar->decoded_metar['visibility']) > 0) {
            foreach ($metar->decoded_metar['visibility'] as $count => $w) {
              if ($count <> 0) $visibility .= ' ';
              $visibility .= $w['raw'];
            }
          }
          $result = sql_query('UPDATE darwin_environment SET geolocation=\'' . addslashes(stripslashes(strip_tags(trim($_POST['geolocation'])))) . '\', datecollected=\'' . date('Y-m-d H:i:s', $timestamp) . '\', wind=' . (!empty($metar->decoded_metar['wind']['raw'])?'\'' . $metar->decoded_metar['wind']['raw'] . (!empty($metar->decoded_metar['wind']['varraw'])?' ' . $metar->decoded_metar['wind']['varraw']:'') . '\'':'NULL') . ', visibility=\'' . $visibility . '\', weather=' . (!empty($weather)?'\'' . addslashes(trim($weather)) . '\'':'NULL') . ', conditions=\'' . $metar->decoded_metar['icon_name'] . '\', skycondition=' . (!empty($clouds)?'\'' . addslashes(trim($clouds)) . '\'':'NULL') . ', temperature=\'' . $metar->decoded_metar['temperature']['raw'] . '\', pressure=\'' . $metar->decoded_metar['altimeter']['raw'] . '\', comments=' . ((isset($_POST['conditionsite']) && !fullyempty($_POST['conditionsite']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['conditionsite'])))) . '\'':'NULL') . ', managementactivities=' . (!empty($_POST['managementactivities'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['managementactivities'])))) . '\'':'NULL') . ', metar=\'' . addslashes(stripslashes(strip_tags(trim($_POST['metar'])))) . '\', updated=NOW(), author=\'' . addslashes($_SESSION['login']['username']) . '\' WHERE geolocation=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['geolocation'])))) . '\' AND datecollected=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['event'])))) . '\';', $sql);
          if (!strlen($r = sql_last_error($sql))) {
            header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/event/' . rawurlencode(ucfirst(stripslashes(strip_tags(trim($_POST['geolocation']))))) . '/' . date('Y-m-d H:i:s', $timestamp));
            exit;
          }
        }
      }elseif (!empty($_POST['entry']) && ($_POST['entry'] == 'manual')) {
        $metar_string = '';
        if ((isset($_POST['winddir']) && !fullyempty($_POST['winddir'])) && (isset($_POST['windspeed']) && !fullyempty($_POST['windspeed']))) {
          if (strpos($_POST['winddir'], '-') !== false) {
            $wind = explode('-', $_POST['winddir'], 2);
            $metar_string .= sprintf("%03d%02dKT %03dV%03d", round((intval($wind[0]) + intval($wind[1])) / 2, 0), intval($_POST['windspeed']), intval($wind[0]), intval($wind[1]));
          }else {
            $metar_string .= sprintf("%03d%02dKT", intval($_POST['winddir']), intval($_POST['windspeed']));
          }
        }
        if (!empty($_POST['weatherp'])) {
          $metar_string .= ' ' . (!empty($_POST['weatherq'])?stripslashes(strip_tags(trim($_POST['weatherq']))):'') . stripslashes(strip_tags(trim($_POST['weatherp'])));
        }
        if (!empty($_POST['visibility'])) {
          if (intval($_POST['visibility']) >= 10000) $_POST['visibility'] = '9999';
          $metar_string .= sprintf(" %04d", abs(intval($_POST['visibility'])));
        }
        if (!empty($_POST['temperature'])) {
          if (!empty($_POST['humidity'])) $humidity = intval($_POST['temperature']) - ((100 - intval($_POST['humidity'])) / 5);
          $metar_string .= ' ' . ((intval($_POST['temperature']) < 0)?'M':'') . sprintf("%02d", abs(intval($_POST['temperature']))) . (!empty($_POST['humidity'])?(($humidity < 0)?'/M':'/') . sprintf("%02d", abs($humidity)):'');
        }
        if (!empty($_POST['pressure'])) {
          $metar_string .= sprintf(" Q%04d", intval($_POST['pressure']));
        }
        $metar = new WeatherIconMetar($metar_string);
        $weather = '';
        if (count($metar->decoded_metar['weather']) > 0) {
          foreach ($metar->decoded_metar['weather'] as $count => $w) {
            if ($count <> 0) $weather .= ' ';
            $weather .= $w['raw'];
          }
        }
        $visibility = '';
        if (count($metar->decoded_metar['visibility']) > 0) {
          foreach ($metar->decoded_metar['visibility'] as $count => $w) {
            if ($count <> 0) $visibility .= ' ';
            $visibility .= $w['raw'];
          }
        }
        $result = sql_query('UPDATE darwin_environment SET geolocation=\'' . addslashes(stripslashes(strip_tags(trim($_POST['geolocation'])))) . '\', datecollected=\'' . date('Y-m-d H:i:s', $timestamp) . '\', wind=\'' . $metar->decoded_metar['wind']['raw'] . (!empty($metar->decoded_metar['wind']['varraw'])?' ' . $metar->decoded_metar['wind']['varraw']:'') . '\', visibility=' . (!empty($visibility)?'\'' . addslashes(trim($visibility)) . '\'':'NULL') . ', weather=' . (!empty($weather)?'\'' . addslashes(trim($weather)) . '\'':'NULL') . ', conditions=' . ((!empty($visibility) && !empty($weather))?'\'' . $metar->decoded_metar['icon_name'] . '\'':'NULL') . ', temperature=' . (!empty($metar->decoded_metar['temperature']['raw'])?'\'' . $metar->decoded_metar['temperature']['raw'] . '\'':'NULL') . ', pressure=' . (!empty($metar->decoded_metar['altimeter']['raw'])?'\'' . $metar->decoded_metar['altimeter']['raw'] . '\'':'NULL') . ', comments=' . ((isset($_POST['conditionsite']) && !fullyempty($_POST['conditionsite']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['conditionsite'])))) . '\'':'NULL') . ', managementactivities=' . (!empty($_POST['managementactivities'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['managementactivities'])))) . '\'':'NULL') . ', skycondition=NULL, metar=NULL, updated=NOW(), author=\'' . addslashes($_SESSION['login']['username']) . '\' WHERE geolocation=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['geolocation'])))) . '\' AND datecollected=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['event'])))) . '\';', $sql);
        if (!strlen($r = sql_last_error($sql))) {
          header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/event/' . rawurlencode(ucfirst(stripslashes(strip_tags(trim($_POST['geolocation']))))) . '/' . date('Y-m-d H:i:s', $timestamp));
          exit;
        }
      }
    }
    head('darwin', true);
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php
    $result = sql_query('SELECT updated, geolocation, datecollected, wind, visibility, weather, temperature, pressure, metar, managementactivities, comments FROM darwin_environment WHERE geolocation=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['geolocation'])))) . '\' AND datecollected=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['event'])))) . '\';', $sql);
    if (!strlen($r = sql_last_error($sql)) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
      if (empty($_POST)) {
        require_once('metar.php');
        if (!empty($row[8])) {
          $metar = new WeatherIconMetar($row[8]);
        }else {
          $metar = new WeatherIconMetar(preg_replace('/ +/', ' ', trim(implode(' ', array($row[3], $row[4], $row[5], $row[6], $row[7])))));
        }
      }
?>
          <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url'] . '/event/edit/' . rawurlencode(stripslashes(strip_tags($row[1]))) .'/' . rawurlencode(stripslashes(strip_tags($row[2]))); ?>">
          <div>
            <h2><?php print $row[1] . ' (' . ((substr($row[2],-8)!='00:00:00')?date(_("d-m-Y H:i:s"), strtotime($row[2])):date(_("d-m-Y"), strtotime($row[2]))); ?>)</h2><br />
            <div>
              <label for="geolocation"><strong><?php print _("Location name"); ?></strong></label>
              <select name="geolocation" id="geolocation" title="<?php print _("The full name of the location"); ?>"><option value=""></option><?php
      $result = sql_query('SELECT geolocation FROM darwin_geolocation ORDER BY geolocation;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print "<option value=\"$row2[0]\"" . ((isset($_POST['geolocation']) && ($row2[0] == $_POST['geolocation']))?' selected="selected"':((!isset($_POST['geolocation']) && isset($row[1]) && ($row2[0] == $row[1]))?' selected="selected"':'')) . ">$row2[0]</option>";
        }
      }
?></select>
              <br />
            </div>
            <div>
              <label for="datecollected"><strong><?php print _("Date"); ?></strong></label>
              <input name="datecollected" id="datecollected" type="text" maxlength="128" title="<?php print _("Date / time of the start of the observation Ð includes either a full date, or partial date; time optional; follow ISO date / time standard (YYYY/MM/DD HH:MM:SS)"); ?>"<?php print (!empty($_POST['datecollected'])?' value="' . stripslashes(strip_tags(trim($_POST['datecollected']))) . '"':(isset($row[2])?' value="' . ((substr($row[2],-8)!='00:00:00')?date(_("d-m-Y H:i:s"), strtotime($row[2])):date(_("d-m-Y"), strtotime($row[2]))) . '"':'')); ?> />
              <br />
            </div>
            <div>
              <label for="entry"><?php print _("Weather entry"); ?></label>
              <select name="entry" id="entry" title="<?php print _("Select your type of weather submission"); ?>"><option value="metar" rel="metar"<?php print (((isset($_POST['entry']) && ($_POST['entry'] == 'metar')) || (!isset($_POST['entry']) && !empty($row[8])))?' selected="selected"':''); ?>><?php print _("METAR (recommended)"); ?></option><option value="manual" rel="manual"<?php print (((!empty($_POST['entry']) && ($_POST['entry'] == 'manual')) || (!isset($_POST['entry']) && empty($row[8])))?' selected="selected"':''); ?>><?php print _("manual"); ?></option></select>
              <br />
            </div>
            <div rel="metar">
              <label for="metar"><?php print _("METAR string"); ?></label>
              <input name="metar" id="metar" type="text" maxlength="128" title="<?php print _("METAR entry for WMO Code Forms METAR FM 15-IX Ext"); ?>"<?php print (!empty($_POST['metar'])?' value="' . stripslashes(strip_tags(trim($_POST['metar']))) . '"':(isset($row[8])?' value="' . $row[8] . '"':'')); ?> />
              <br />
            </div>
            <div rel="manual">
              <label for="winddir"><?php print _("Wind direction"); ?></label>
              <input name="winddir" id="winddir" type="text" maxlength="32" title="<?php print _("Wind direction coded in decimal degrees or a rang if variable (180-240)"); ?>"<?php print ((isset($_POST['winddir']) && !fullyempty($_POST['winddir']))?' value="' . intval($_POST['winddir']) . '"':(!empty($metar->decoded_metar['wind'])?(!empty($metar->decoded_metar['wind']['var_beg'])?' value="' . $metar->decoded_metar['wind']['var_beg'] . '-' . $metar->decoded_metar['wind']['var_end'] . '"':' value="' . $metar->decoded_metar['wind']['deg'] . '"'):'')); ?> class="half" /> &deg;
              <br />
            </div>
            <div rel="manual">
              <label for="windspeed"><?php print _("Wind speed"); ?></label>
              <input name="windspeed" id="windspeed" type="text" maxlength="16" title="<?php print _("Wind speed in knot (if calm use 0)"); ?>"<?php print ((isset($_POST['windspeed']) && !fullyempty($_POST['windspeed']))?' value="' . intval($_POST['windspeed']) . '"':(!empty($metar->decoded_metar['wind']['km/h'])?' value="' . $metar->decoded_metar['wind']['km/h'] . '"':'')); ?> class="half" /> km/h
              <br />
            </div>
            <div rel="manual">
              <label for="visibility"><?php print _("Visibility"); ?></label>
              <input name="visibility" id="visibility" type="text" maxlength="16" title="<?php print _("Visibility in meters (between 50 and 10000)"); ?>"<?php print ((isset($_POST['visibility']) && !fullyempty($_POST['visibility']))?' value="' . intval($_POST['visibility']) . '"':(!empty($metar->decoded_metar['visibility'])?' value="' . $metar->decoded_metar['visibility'][0]['m'] . '"':'')); ?> class="half" /> m
              <br />
            </div>
            <div rel="manual">
              <label for="weather"><?php print _("Weather"); ?></label>
              <select name="weatherq" id="weatherq" title="<?php print _("Weather Qualifier"); ?>" class="half"><option value=""></option><?php
      $result = sql_query('SELECT reference, name, groups FROM darwin_weather WHERE description=\'Qualifier\' ORDER BY groups DESC;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          if (empty($last) || ($last != $row2[2])) print (!empty($last)?'</optgroup>':'') . "<optgroup label=\"$row2[2]\">";
          print "<option value=\"$row2[0]\"" . (((isset($_POST['weatherq']) && ($row2[0] == $_POST['weatherq'])) || (!isset($_POST['weatherq']) && !empty($metar->decoded_metar['weather']) && (($metar->decoded_metar['weather'][0]['intensity'] == $row2[0]) || ($metar->decoded_metar['weather'][0]['descriptor'] == $row2[0]))))?' selected="selected"':'') . ">$row2[1]</option>";
          $last = $row2[2];
        }
      }
?></select>
              <select name="weatherp" id="weatherp" title="<?php print _("Weather Phenomena"); ?>" class="half"><option value=""></option><?php
      $result = sql_query('SELECT reference, name, groups FROM darwin_weather WHERE description=\'Phenomena\' ORDER BY groups DESC;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          if (empty($last) || ($last != $row2[2])) print (!empty($last)?'</optgroup>':'') . "<optgroup label=\"$row2[2]\">";
          print "<option value=\"$row2[0]\"" . (((isset($_POST['weatherp']) && ($row2[0] == $_POST['weatherp'])) || (!isset($_POST['weatherp']) && !empty($metar->decoded_metar['weather']) && (($metar->decoded_metar['weather'][0]['precipitation'] == $row2[0]) || ($metar->decoded_metar['weather'][0]['obscuration'] == $row2[0]) || ($metar->decoded_metar['weather'][0]['other'] == $row2[0]))))?' selected="selected"':'') . ">$row2[1]</option>";
          $last = $row2[2];
        }
      }
?></select>
              <br />
            </div>
            <div rel="manual">
              <label for="temperature"><?php print _("Temperature"); ?></label>
              <input name="temperature" id="temperature" type="text" maxlength="8" title="<?php print _("Temperature in Degree <strong>Celcius</strong>"); ?>"<?php print ((isset($_POST['temperature']) && !fullyempty($_POST['temperature']))?' value="' . intval($_POST['temperature']) . '"':(!empty($metar->decoded_metar['temperature'])?' value="' . $metar->decoded_metar['temperature']['temp_c'] . '"':'')); ?> class="half" />&deg;C
              <br />
            </div>
            <div rel="manual">
              <label for="humidity"><?php print _("Relative humidity"); ?></label>
              <input name="humidity" id="humidity" type="text" maxlength="8" title="<?php print _("Humidity (between 0 and 100); Required if the Temperature is specified"); ?>"<?php print ((isset($_POST['humidity']) && !fullyempty($_POST['humidity']))?' value="' . intval($_POST['humidity']) . '"':(!empty($metar->decoded_metar['temperature'])?' value="' . $metar->decoded_metar['rel_humidity'] . '"':'')); ?> class="half" /> %
              <br />
            </div>
            <div rel="manual">
              <label for="pressure"><?php print _("Pressure"); ?></label>
              <input name="pressure" id="pressure" type="text" maxlength="8" title="<?php print _("Pressure in Hectopascals (hPa)"); ?>"<?php print ((isset($_POST['pressure']) && !fullyempty($_POST['pressure']))?' value="' . intval($_POST['pressure']) . '"':(!empty($metar->decoded_metar['altimeter'])?' value="' . $metar->decoded_metar['altimeter']['hpa'] . '"':'')); ?> class="half" /> hPa
              <br />
            </div>
            <div>
              <label for="managementactivities"><?php print _("Management Activities"); ?></label>
              <input name="managementactivities" id="managementactivities" type="text" maxlength="128" title="<?php print _("Recent human activities (e.g. pulling or pesticides applied to invasives)"); ?>"<?php print (!empty($_POST['managementactivities'])?' value="' . stripslashes(strip_tags(trim($_POST['managementactivities']))) . '"':(isset($row[9])?' value="' . $row[9] . '"':'')); ?> />
              <br />
            </div>
            <div>
              <label for="conditionsite"><?php print _("Site conditions"); ?></label>
              <textarea name="conditionsite" id="conditionsite" rows="4" cols="30" title="<?php print _("Description of the site at this particular day"); ?>"><?php print ((isset($_POST['conditionsite']) && !fullyempty($_POST['conditionsite']))?stripslashes(strip_tags(trim($_POST['conditionsite']))):(isset($row[10])?$row[10]:'')); ?></textarea>
              <br />
            </div>
            <br />
            <input type="hidden" name="key" value="<?php print md5(strip_tags(trim(rawurldecode($_GET['geolocation']))).strip_tags(trim(rawurldecode($_GET['event']))) . floor(intval(date('b')))); ?>" />
            <input type="reset" value="<?php print _("Clear"); ?>" />&nbsp;<input type="submit"  name="edit" value="<?php print _("Edit"); ?>" />&nbsp;<input type="submit" name="remove" value="<?php print _("Remove"); ?>" onclick="return confirm('<?php print _("Are you sure you want to delete?"); ?>')"/>
          </div>
          </form>
          <br />
        </div>
<?php
    }
  }elseif (($_SESSION['login']['right'] >= 2) && !empty($_GET['add'])) {
    $sql = sql_connect($config['db']);
    if (!empty($_POST['darwin']) && ($_POST['darwin'] == md5('add' . floor(intval(date('b'))))) && !empty($_POST['geolocation']) && !empty($_POST['datecollected']) && (($timestamp = strtotime($_POST['datecollected'])) !== false)) {
      require_once('metar.php');
      if (!empty($_POST['entry']) && ($_POST['entry'] == 'metar') && !empty($_POST['metar'])) {
        $metar = new WeatherIconMetar(stripslashes(strip_tags(trim($_POST['metar']))));
        if (!empty($metar->decoded_metar['altimeter']['raw']) && !empty($metar->decoded_metar['temperature']['raw']) && !empty($metar->decoded_metar['visibility'][0]['raw'])) {
          $weather = '';
          if (count($metar->decoded_metar['weather']) > 0) {
            foreach ($metar->decoded_metar['weather'] as $count => $w) {
              if ($count <> 0) $weather .= ' ';
              $weather .= $w['raw'];
            }
          }
          $clouds = '';
          if (count($metar->decoded_metar['clouds']) > 0) {
            foreach ($metar->decoded_metar['clouds'] as $count => $w) {
              if ($count <> 0) $clouds .= ' ';
              $clouds .= $w['raw'];
            }
          }
          $visibility = '';
          if (count($metar->decoded_metar['visibility']) > 0) {
            foreach ($metar->decoded_metar['visibility'] as $count => $w) {
              if ($count <> 0) $visibility .= ' ';
              $visibility .= $w['raw'];
            }
          }
          $result = sql_query('INSERT INTO darwin_environment (geolocation, datecollected, wind, visibility, weather, conditions, skycondition, temperature, pressure, managementactivities, comments, metar, author) VALUES (\'' . addslashes(stripslashes(strip_tags(trim($_POST['geolocation'])))) . '\',\'' . date('Y-m-d H:i:s', $timestamp) . '\',' . (!empty($metar->decoded_metar['wind']['raw'])?'\'' . $metar->decoded_metar['wind']['raw'] . (!empty($metar->decoded_metar['wind']['varraw'])?' ' . $metar->decoded_metar['wind']['varraw']:'') . '\'':'NULL') . ',\'' . $visibility . '\',' . (!empty($weather)?'\'' . addslashes(trim($weather)) . '\'':'NULL') . ',\'' . $metar->decoded_metar['icon_name'] . '\',' . (!empty($clouds)?'\'' . addslashes(trim($clouds)) . '\'':'NULL') . ',\'' . $metar->decoded_metar['temperature']['raw'] . '\',\'' . $metar->decoded_metar['altimeter']['raw'] . '\',' . (!empty($_POST['managementactivities'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['managementactivities'])))) . '\'':'NULL') . ',' . ((isset($_POST['conditionsite']) && !fullyempty($_POST['conditionsite']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['conditionsite'])))) . '\'':'NULL') . ',\'' . addslashes(stripslashes(strip_tags(trim($_POST['metar'])))) . '\',\'' . addslashes($_SESSION['login']['username']) . '\');', $sql);
          if (!strlen($r = sql_last_error($sql))) {
            header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/event/' . rawurlencode(ucfirst(stripslashes(strip_tags(trim($_POST['geolocation']))))) . '/' . date('Y-m-d H:i:s', $timestamp));
            exit;
          }
        }
      }else {
        $metar_string = '';
        if ((isset($_POST['winddir']) && !fullyempty($_POST['winddir'])) && (isset($_POST['windspeed']) && !fullyempty($_POST['windspeed']))) {
          if (strpos($_POST['winddir'], '-') !== false) {
            $wind = explode('-', $_POST['winddir'], 2);
            $metar_string .= sprintf("%03d%02dKT %03dV%03d", round((intval($wind[0]) + intval($wind[1])) / 2, 0), intval($_POST['windspeed']), intval($wind[0]), intval($wind[1]));
          }else {
            $metar_string .= sprintf("%03d%02dKT", intval($_POST['winddir']), intval($_POST['windspeed']));
          }
        }
        if (!empty($_POST['weatherp'])) {
          $metar_string .= ' ' . (!empty($_POST['weatherq'])?stripslashes(strip_tags(trim($_POST['weatherq']))):'') . stripslashes(strip_tags(trim($_POST['weatherp'])));
        }
        if (!empty($_POST['visibility'])) {
          if (intval($_POST['visibility']) >= 10000) $_POST['visibility'] = '9999';
          $metar_string .= sprintf(" %04d", abs(intval($_POST['visibility'])));
        }
        if (!empty($_POST['temperature'])) {
          if (!empty($_POST['humidity'])) $humidity = intval($_POST['temperature']) - ((100 - intval($_POST['humidity'])) / 5);
          $metar_string .= ' ' . ((intval($_POST['temperature']) < 0)?'M':'') . sprintf("%02d", abs(intval($_POST['temperature']))) . (!empty($_POST['humidity'])?(($humidity < 0)?'/M':'/') . sprintf("%02d", abs($humidity)):'');
        }
        if (!empty($_POST['pressure'])) {
          $metar_string .= sprintf(" Q%04d", intval($_POST['pressure']));
        }
        $metar = new WeatherIconMetar($metar_string);
        $weather = '';
        if (count($metar->decoded_metar['weather']) > 0) {
          foreach ($metar->decoded_metar['weather'] as $count => $w) {
            if ($count <> 0) $weather .= ' ';
            $weather .= $w['raw'];
          }
        }
        $visibility = '';
        if (count($metar->decoded_metar['visibility']) > 0) {
          foreach ($metar->decoded_metar['visibility'] as $count => $w) {
            if ($count <> 0) $visibility .= ' ';
            $visibility .= $w['raw'];
          }
        }
        $result = sql_query('INSERT INTO darwin_environment (geolocation, datecollected, wind, visibility, weather, conditions, temperature, pressure, managementactivities, comments, author) VALUES (\'' . addslashes(stripslashes(strip_tags(trim($_POST['geolocation'])))) . '\',\'' . date('Y-m-d H:i:s', $timestamp) . '\',\'' . $metar->decoded_metar['wind']['raw'] . (!empty($metar->decoded_metar['wind']['varraw'])?' ' . $metar->decoded_metar['wind']['varraw']:'') . '\',' . (!empty($visibility)?'\'' . addslashes(trim($visibility)) . '\'':'NULL') . ',' . (!empty($weather)?'\'' . addslashes(trim($weather)) . '\'':'NULL') . ',' . ((!empty($visibility) && !empty($weather))?'\'' . $metar->decoded_metar['icon_name'] . '\'':'NULL') . ',' . (!empty($metar->decoded_metar['temperature']['raw'])?'\'' . $metar->decoded_metar['altimeter']['raw'] . '\'':'NULL') . ',' . (!empty($metar->decoded_metar['altimeter']['raw'])?'\'' . $metar->decoded_metar['altimeter']['raw'] . '\'':'NULL') . ',' . (!empty($_POST['managementactivities'])?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['managementactivities'])))) . '\'':'NULL') . ',' . ((isset($_POST['conditionsite']) && !fullyempty($_POST['conditionsite']))?'\'' . addslashes(stripslashes(strip_tags(trim($_POST['conditionsite'])))) . '\'':'NULL') . ',\'' . addslashes($_SESSION['login']['username']) . '\');', $sql);
        if (!strlen($r = sql_last_error($sql))) {
          header('Location: ' . $config['server'] . $plugin['darwin']['url'] . '/event/' . rawurlencode(ucfirst(stripslashes(strip_tags(trim($_POST['geolocation']))))) . '/' . date('Y-m-d H:i:s', $timestamp));
          exit;
        }
      }
    }
    head('darwin', true);
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url']; ?>/event/add">
        <div>
          <h2><?php print _("New Collecting Event"); ?><small><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/help/event" title="<?php print _("Help"); ?>"><?php print _("Help"); ?></a></small></h2><br /><?php print _("You can specify the date and the weather condition of a collecting event."); ?><br /><br />
          <div>
            <label for="advanced"><?php print _("Advanced"); ?></label>
            <input type="checkbox" name="advanced" id="advanced" rel="advanced"<?php print (!empty($_POST['advanced'])?' checked="checked"':''); ?> />
            <br />
          </div>
          <div>
            <label for="geolocation"><strong><?php print _("Location name"); ?></strong></label>
            <select name="geolocation" id="geolocation" title="<?php print _("The full name of the location"); ?>"><option value=""></option><?php
    $result = sql_query('SELECT geolocation FROM darwin_geolocation ORDER BY geolocation;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row = sql_fetch_row($result)) {
        print "<option value=\"$row[0]\"" . ((!empty($_POST['geolocation']) && ($row[0] == $_POST['geolocation']))?' selected="selected"':'') . ">$row[0]</option>";
      }
    }
?></select>
            <br />
          </div>
          <div>
            <label for="datecollected"><strong><?php print _("Date"); ?></strong></label>
            <input name="datecollected" id="datecollected" type="text" maxlength="128" title="<?php print _("Date / time of the start of the observation Ð includes either a full date, or partial date; time optional; follow ISO date / time standard (YYYY/MM/DD HH:MM:SS)"); ?>"<?php print (!empty($_POST['datecollected'])?' value="' . stripslashes(strip_tags(trim($_POST['datecollected']))) . '"':''); ?> />
            <br />
          </div>
          <div rel="advanced">
            <label for="entry"><?php print _("Weather entry"); ?></label>
            <select name="entry" id="entry" title="<?php print _("Select your type of weather submission"); ?>"><option value="metar" rel="metar"<?php print ((empty($_POST['entry']) || ($_POST['entry'] == 'metar'))?' selected="selected"':''); ?>><?php print _("METAR (recommended)"); ?></option><option value="manual" rel="manual"<?php print ((!empty($_POST['entry']) && ($_POST['entry'] == 'manual'))?' selected="selected"':''); ?>><?php print _("manual"); ?></option></select>
            <br />
          </div>
          <div rel="metar">
            <label for="metar"><?php print _("METAR string"); ?></label>
            <input name="metar" id="metar" type="text" maxlength="128" title="<?php print _("METAR entry for WMO Code Forms METAR FM 15-IX Ext"); ?>"<?php print (!empty($_POST['metar'])?' value="' . stripslashes(strip_tags(trim($_POST['metar']))) . '"':''); ?> />
            <br />
          </div>
          <div rel="manual">
            <label for="winddir"><?php print _("Wind direction"); ?></label>
            <input name="winddir" id="winddir" type="text" maxlength="32" title="<?php print _("Wind direction coded in decimal degrees or a rang if variable (180-240)"); ?>"<?php print ((isset($_POST['winddir']) && !fullyempty($_POST['winddir']))?' value="' . intval($_POST['winddir']) . '"':''); ?> class="half" /> &deg;
            <br />
          </div>
          <div rel="manual">
            <label for="windspeed"><?php print _("Wind speed"); ?></label>
            <input name="windspeed" id="windspeed" type="text" maxlength="16" title="<?php print _("Wind speed in knot (if calm use 0)"); ?>"<?php print ((isset($_POST['windspeed']) && !fullyempty($_POST['windspeed']))?' value="' . intval($_POST['windspeed']) . '"':''); ?> class="half" /> km/h
            <br />
          </div>
          <div rel="manual">
            <label for="visibility"><?php print _("Visibility"); ?></label>
            <input name="visibility" id="visibility" type="text" maxlength="16" title="<?php print _("Visibility in meters (between 50 and 10000)"); ?>"<?php print ((isset($_POST['visibility']) && !fullyempty($_POST['visibility']))?' value="' . intval($_POST['visibility']) . '"':''); ?> class="half" /> m
            <br />
          </div>
          <div rel="manual">
            <label for="weather"><?php print _("Weather"); ?></label>
      <select name="weatherq" id="weatherq" title="<?php print _("Weather Qualifier"); ?>" class="half"><option value=""></option><?php
    $result = sql_query('SELECT reference, name, groups FROM darwin_weather WHERE description=\'Qualifier\' ORDER BY groups DESC;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row2 = sql_fetch_row($result)) {
        if (empty($last) || ($last != $row2[2])) print (!empty($last)?'</optgroup>':'') . "<optgroup label=\"$row2[2]\">";
        print "<option value=\"$row2[0]\"" . ((!empty($_POST['weatherq']) && ($row2[0] == $_POST['weatherq']))?' selected="selected"':'') . ">$row2[1]</option>";
        $last = $row2[2];
      }
    }
?></select>&nbsp;
      <select name="weatherp" id="weatherp" title="<?php print _("Weather Phenomena"); ?>" class="half"><option value=""></option><?php
    $result = sql_query('SELECT reference, name, groups FROM darwin_weather WHERE description=\'Phenomena\' ORDER BY groups DESC;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
      while ($row2 = sql_fetch_row($result)) {
        if (empty($last) || ($last != $row2[2])) print (!empty($last)?'</optgroup>':'') . "<optgroup label=\"$row2[2]\">";
        print "<option value=\"$row2[0]\"" . ((!empty($_POST['weatherp']) && ($row2[0] == $_POST['weatherp']))?' selected="selected"':'') . ">$row2[1]</option>";
        $last = $row2[2];
      }
    }
?></select>
            <br />
          </div>
          <div rel="manual">
            <label for="temperature"><?php print _("Temperature"); ?></label>
            <input name="temperature" id="temperature" type="text" maxlength="8" title="<?php print _("Temperature in Degree <strong>Celcius</strong>"); ?>"<?php print ((isset($_POST['temperature']) && !fullyempty($_POST['temperature']))?' value="' . intval($_POST['temperature']) . '"':''); ?> class="half" /> &deg;C
            <br />
          </div>
          <div rel="manual">
            <label for="humidity"><?php print _("Relative humidity"); ?></label>
            <input name="humidity" id="humidity" type="text" maxlength="8" title="<?php print _("Humidity (between 0 and 100); Required if the Temperature is specified"); ?>"<?php print ((isset($_POST['humidity']) && !fullyempty($_POST['humidity']))?' value="' . intval($_POST['humidity']) . '"':''); ?> class="half" /> %
            <br />
          </div>
          <div rel="manual">
            <label for="pressure"><?php print _("Pressure"); ?></label>
            <input name="pressure" id="pressure" type="text" maxlength="8" title="<?php print _("Pressure in Hectopascals (hPa)"); ?>"<?php print ((isset($_POST['pressure']) && !fullyempty($_POST['pressure']))?' value="' . intval($_POST['pressure']) . '"':''); ?> class="half" /> hPa
            <br />
          </div>
          <div rel="advanced">
            <label for="managementactivities"><?php print _("Management Activities"); ?></label>
            <input name="managementactivities" id="managementactivities" type="text" maxlength="128" title="<?php print _("Recent human activities (e.g. pulling or pesticides applied to invasives)"); ?>"<?php print (!empty($_POST['managementactivities'])?' value="' . stripslashes(strip_tags(trim($_POST['managementactivities']))) . '"':''); ?> />
            <br />
          </div>
          <div rel="advanced">
            <label for="conditionsite"><?php print _("Site conditions"); ?></label>
            <textarea name="conditionsite" id="conditionsite" rows="4" cols="30" title="<?php print _("Description of the site at this particular day"); ?>"><?php print ((isset($_POST['conditionsite']) && !fullyempty($_POST['conditionsite']))?stripslashes(strip_tags(trim($_POST['conditionsite']))):''); ?></textarea>
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
  }elseif (!empty($_GET['event']) && !empty($_GET['geolocation']) && (($timestamp = strtotime($_GET['event'])) !== false)) {
    head('darwin');
?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name']; ?><small><?php print $plugin['darwin']['description']; ?></small></h1><br />
<?php
    $sql = sql_connect($config['db']);
    $result = sql_query('SELECT a.geolocation, a.datecollected, a.conditions, a.wind, a.visibility, a.weather, a.temperature, a.pressure, a.metar, a.managementactivities, a.comments, b.minimumlatitude ,b.minimumlongitude FROM darwin_environment AS a, darwin_geolocation AS b WHERE a.geolocation=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['geolocation'])))) . '\' AND a.datecollected=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['event'])))) . '\' AND b.geolocation=a.geolocation;', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      $row = sql_fetch_row($result);
?>
        <div>
          <h2><?php print $row[0] . (($_SESSION['login']['right'] >= 3)?'<small><a href="' . $config['server'] . $plugin['darwin']['url'] . '/event/edit/' . rawurlencode(stripslashes($row[0])) . '/' . rawurlencode(stripslashes($row[1])) . '" title="' . _("Edit") . '">' . _("edit") . '</a></small>':''); ?></h2>
<?php
      if (!empty($row[8]) || !empty($row[3]) || !empty($row[4]) || !empty($row[5]) || !empty($row[6]) || !empty($row[7])) {
        require_once('metar.php');
        if (!empty($row[8])) {
          $metar = new WeatherIconMetar($row[8]);
        }else {
          $metar = new WeatherIconMetar(preg_replace('/ +/', ' ', trim(implode(' ', array($row[3], $row[4], $row[5], $row[6], $row[7])))));
        }
        print '            <div class="semocode"><img src="' . $config['server'] . $plugin['darwin']['url'] . '/icons/' . $metar->get_icon() . '" width="64" height="64" alt="' . $metar->get_status() . "\" /></div>\n";
      }
      print '          <h3>' . ("Details") . "</h3>\n";
      print '          <div class="details"><div class="title">' . _("Location name") . '</div><div class="label"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/geolocation/' . rawurlencode($row[0]) . '">' . $row[0] . "</a></div></div>\n";
      print '          <div class="details"><div class="title">' . _("Date") . '</div><div class="label">' . ((substr($row[1], -8) != '00:00:00')?date(_("d-m-Y H:i:s"), strtotime($row[1])) . ' - ' . isdawn(strtotime($row[1]),$row[11],$row[12]) :((substr($row[1], -15) == '-01-01 00:00:00')?date(_("Y"), strtotime($row[1])):date(_("d-m-Y"), strtotime($row[1])))) . "</div></div>\n";
      if (!empty($metar)) {
        print '            <h3>' . ("Weather") . "</h3>\n";
        if (!empty($metar->decoded_metar['visibility'])) print '          <div class="details"><div class="title">' . _("Visibility") . '</div><div class="label">' . $metar->get_visibility() . "</div></div>\n";
        if (!empty($metar->decoded_metar['clouds'])) print '          <div class="details"><div class="title">' . _("Clouds") . '</div><div class="label">' . $metar->get_simple_clouds() . "</div></div>\n";
        if (!empty($metar->decoded_metar['weather'])) print '          <div class="details"><div class="title">' . _("Weather") . '</div><div class="label">' . $metar->get_conditions() . "</div></div>\n";
        if (!empty($metar->decoded_metar['temperature'])) {
          print '          <div class="details"><div class="title">' . _("Temperature") . '</div><div class="label">' . $metar->get_temp() . "</div></div>\n";
          if ($metar->get_dewpoint()) print '          <div class="details"><div class="title">' . _("Dew point") . '</div><div class="label">' . $metar->get_dewpoint() . "</div></div>\n";
        }
        if (!empty($metar->decoded_metar['wind'])) print '          <div class="details"><div class="title">' . _("Wind") . '</div><div class="label">' . $metar->get_wind() . "</div></div>\n";
        if ($metar->get_windchill()) print '          <div class="details"><div class="title">' . _("Wind Chill") . '</div><div class="label">' . $metar->get_windchill() . "</div></div>\n";
        if (!empty($metar->decoded_metar['altimeter'])) print '          <div class="details"><div class="title">' . _("Pressure") . '</div><div class="label">' . $metar->get_altimeter() . "</div></div>\n";
        if (!empty($row[8])) print '          <div class="details"><div class="title">' . _("METAR") . '</div><div><small>' . $row[8] . "</small></div></div>\n";
      }
      if (!empty($row[9]) || !empty($row[10])) {
        print '          <h3>' . ("Site") . "</h3>\n";
        if (!empty($row[9])) print '          <div class="details"><div class="title">' . _("Activities") . '</div><div class="label">' . $row[9] . "</div></div>\n";
        if (!empty($row[10])) print '          <div class="details"><div class="label">' . preg_replace('/\[([^\|\]]*)\|([^\|\]]*)\]/', '<a href="\1">\2</a><br />', htmlentities($row[10], ENT_COMPAT, 'ISO-8859-1')) . "</div></div>\n";
      }
      print '        </div>';
      print "        <div>\n          <h2>" . _("Specimens") . (($_SESSION['login']['right'] >= 2)?'<small><a href="' . $config['server'] . $plugin['darwin']['url'] . '/bioject/add" title="' . _("Add a new specimen") . '">' . _("add") . '</a></small>':'') . "</h2>\n";
      $result = sql_query('SELECT prefix, id, institutioncode, collectioncode, catalognumber, scientificname FROM darwin_bioject WHERE (geolocation=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['geolocation'])))) . '\' AND event=\'' . addslashes(stripslashes(strip_tags(rawurldecode($_GET['event'])))) . '\');', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        while ($row2 = sql_fetch_row($result)) {
          print '          <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/bioject/B' . decoct($row2[0]) . '.' . decoct($row2[1]) . '">' . $row2[2] . '-' . $row2[3] . '-' . $row2[4] . '</a></span><span class="desc"><em>' . $row2[5] . "</em></span></div>\n";
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
        <div><h2><?php print _("Add a Collection Event"); ?><small><a href="<?php print $config['server'] . $plugin['darwin']['url']; ?>/event/add" title="<?php print _("Add a new collection"); ?>"><?php print _("Add a collection event..."); ?></a></small></h2><br /><?php print _("You may add a new collection event add a new collection entry."); ?><br /></div>
<?php }
    ?>
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url']; ?>/event/search">
        <div>
          <h2><?php print _("Search"); ?></h2><br /><?php print _("Retrive a collection event. You may provide a reference numbre, or a date."); ?><br /><br />
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
      $result = sql_query('SELECT a.geolocation, a.datecollected, a.conditions, b.minimumlatitude, b.minimumlongitude FROM darwin_environment AS a, darwin_geolocation AS b WHERE (a.geolocation' . sql_reg(addslashes($search)) . ' OR a.datecollected' . sql_reg(addslashes($search)) . ') AND b.geolocation=a.geolocation;', $sql);
      if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) > 0)) {
        include('icons/icon.inc');
        print "        <div>\n          <h2>" . _("Results") . "</h2>\n";
        while ($row = sql_fetch_row($result)) {
          print '          <div class="result"><span class="ref"><a href="' . $config['server'] . $plugin['darwin']['url'] . '/event/' . rawurlencode($row[0]) . '/' . rawurlencode($row[1]) . '">' . $row[0] . '</a></span><span class="detail">' . ((substr($row[1], -8) != '00:00:00')?date(_("d-m-Y H:i:s"), strtotime($row[1])) . ' - ' . isdawn(strtotime($row[1]),$row[3],$row[4]):((substr($row[1], -15) == '-01-01 00:00:00')?date(_("Y"), strtotime($row[1])):date(_("d-m-Y"), strtotime($row[1])))).'</span><span class="updated"><img src="' . $config['server'] . $plugin['darwin']['url'] . '/icons/' . $icon_map[$row[2]]['icon'] . '.png' . "\" width=\"16\" height=\"16\" /></span></div>\n";
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