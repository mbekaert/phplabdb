<?php
require_once('config.inc');
@include_once('website.inc');
if ($config['sqlserver'] == 'postgresql') {
  require_once('postgresql.inc');
}elseif ($config['sqlserver'] == 'mysql') {
  require_once('mysql.inc');
}

/* To use file_put_contents prior to PHP5 */
if (!function_exists('file_put_contents')) {
  function file_put_contents($filename, $data, $file_append = false) {
    $fp = fopen($filename, (!$file_append ? 'w+' : 'a+'));
    if (!$fp) {
      return false;
    }
    fputs($fp, $data);
    fclose($fp);
    return true;
  }
}

function head ($current = 'home', $form = false, $gmap = false) {
  global $config, $plugin;
  header('Powered by: ' . $config['powered']);
  header('Node: Irish node');
  print '<' . '?xml version="1.0" encoding="ISO-8859-1"?' . ">\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
  <head>
    <title>
      phpLabDB | <?php print $current; ?>
    </title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta name="robots" content="noindex,nofollow" />
    <link rel="shortcut icon" type="image/x-icon" href="<?php print $config['server']; ?>/favicon.ico" />
    <link rel="search" type="application/opensearchdescription+xml" title="<?php print _("phpLabDB"); ?>" href="<?php print $config['server']; ?>/search.xml">
    <style type="text/css" media="all">
/*<![CDATA[*/
   @import url('<?php print $config['server']; ?>/styles/main.css');
/*]]>*/
    </style>
<?php if ($gmap) { ?>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php print $config['gmap']; ?>" type="text/javascript"></script>
    <script src="<?php print $config['server']; ?>/scripts/track.js" type="text/javascript"></script>
<?php
  }
  if ($current == 'about') {
    print '    <script type="text/javascript" src="' . $config['server'] . "/scripts/zulu.js\"></script>\n";
  }
  if ($form) {
    print '    <script type="text/javascript" src="' . $config['server'] . "/scripts/usableforms.js\"></script>\n";
  }
?>
  </head>
  <body<?php if ($gmap) print ' onunload="GUnload()"'; ?>>
    <div id="wrapper">
      <div id="header"></div>
      <div id="menu">
<?php
  if ($config['login']) {
    print '        <div id="secure">' . $_SESSION['login']['code'] . '<em>' . $_SESSION['login']['username'] . '</em>' . (($config['secure'])?' [' . $_SERVER['SSL_SERVER_S_DN_CN'] . ']':'') . ' (<a href="' . $config['server'] . '/logout" title="' . _("logout") . '">' . _("logout") . "</a>) </div>\n";
  }else {
    print '        <div id="secure">' . _("Please") . ' <a href="' . $config['server'] . '/login" title="' . _("login") . '">' . _("login") . '</a> ' . ((empty($config['organisation']['voucher']))?_("or") . ' <a href="' . $config['server'] . '/register" title="' . _("register") . '">' . _("register") . '</a> ':'') . _("to access") . ".</div>\n";
  }
  if (isset($config['organisation']['logo'])) {
    print '        <a href="' . (isset($config['organisation']['url'])?$config['organisation']['url']:$config['server']) . '" title="' . (isset($config['organisation']['name'])?$config['organisation']['name']:'') . '"><img id="logo" src="' . $config['organisation']['logo'] . '" alt="' . (isset($config['organisation']['name'])?$config['organisation']['name']:'') . '" width="236" height="138" /></a>' . "\n";
  }else {
    print '        <img id="logo" src="' . $config['server'] . '/images/logo.png" alt="phpLabDB: fresh. clean. databases." width="236" height="138" />' . "\n";
  }
?>
        <ul class="menulist">
<?php
  if ($config['login']) {
?>
          <li>
            &raquo; <a href="<?php print $config['server']; ?>/my" title="<?php print _("home"); ?>"><span><?php print _("home"); ?></span></a>
          </li>
<?php
    if (isset($plugin)) {
      foreach($plugin as $value) {
        print "          <li>\n            &raquo; <a href=\"" . $config['server'] . $value['url'] . '/" title="' . $value['description'] . '"><span>' . $value['name'] . "</span></a>\n          </li>\n";
      }
    }
?>
<!--          <li>
            &raquo; <a href="<?php print $config['server']; ?>/search" title="<?php print _("Direct search"); ?>"><span><?php print _("search"); ?></span></a>
          </li> -->
<?php }else { ?>
          <li>
            &raquo; <a href="<?php print $config['server']; ?>/" title="<?php print _("home"); ?>"><span><?php print _("home"); ?></span></a>
          </li>
<?php } ?>
          <li>
            &raquo; <a href="<?php print $config['server']; ?>/about" title="<?php print _("About"); ?>"><span><?php print _("about"); ?></span></a>
          </li>
        </ul>
      </div>
      <div id="content">
<?php
}

function foot() {
  global $config;
?>
      </div>
      <div class="clear"></div>
    </div>
    <div id="bottom">
      <div id="footer">
        <div id="foot1">
          <p class="light"><a href="http://phplabdb.sourceforge.net"><img src="<?php print $config['server']; ?>/images/logo-footer.png" alt="phpLabDB" width="55" height="16" /></a><br /><?php print _("This work is licensed under a"); ?><br /><a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/2.5/"><?php print _("Creative Commons License"); ?></a>.</p>
        </div>
        <div id="foot2"></div>
        <div id="foot3">
          <p id="totop"><a href="#header"><?php print _("Top"); ?></a> <img src="<?php print $config['server']; ?>/images/toparrow.png" alt="<?php print _("top"); ?>" /></p>
        </div>
        <div class="clear"></div>
      </div>
    </div>
  </body>
<!-- <rdf:RDF xmlns="http://web.resource.org/cc/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"><Work rdf:about=""><license rdf:resource="http://creativecommons.org/licenses/by-nc-sa/2.5/" /></Work><License rdf:about="http://creativecommons.org/licenses/by-nc-sa/2.5/"><permits rdf:resource="http://web.resource.org/cc/Reproduction"/><permits rdf:resource="http://web.resource.org/cc/Distribution"/><requires rdf:resource="http://web.resource.org/cc/Notice"/><requires rdf:resource="http://web.resource.org/cc/Attribution"/><prohibits rdf:resource="http://web.resource.org/cc/CommercialUse"/><permits rdf:resource="http://web.resource.org/cc/DerivativeWorks"/><requires rdf:resource="http://web.resource.org/cc/ShareAlike"/></License></rdf:RDF> -->
</html>
<?php
}

function getref($ref, $sql) {
  $ret = 'NULL';
  if (!empty($ref)) {
    $result = sql_query('SELECT id FROM reference WHERE url=\'' . 'http://www.hubmed.org/display.cgi?uids=' . $ref . '\';', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
      list($ret) = sql_fetch_row($result);
    }else {
      $url = 'http://www.hubmed.org/export/mods.cgi?uids=' . $ref;
      if ($hubmed = fopen($url, 'r')) {
        while (!feof($hubmed)) {
          $buffer = fgets($hubmed, 4096);
          if (preg_match('/\<title\>(.*)\<\/title\>/', $buffer, $matches) && empty($refx['title'])) $refx['title'] = $matches[1];
          if (preg_match('/\<identifier type=\"doi\"\>(.*)\<\/identifier\>/', $buffer, $matches)) $refx['doi'] = $matches[1];
          if (preg_match('/\<identifier type=\"pmid\"\>(.*)\<\/identifier\>/', $buffer, $matches)) $refx['pmid'] = $matches[1];
        }
        fclose($hubmed);
      }
      if (isset($refx['pmid'])) {
        $result = sql_query('INSERT INTO reference (id, url, doi, title, comments) SELECT CASE WHEN max(id)>=1 THEN max(id)+1 ELSE 1 END, \'' . 'http://www.hubmed.org/display.cgi?uids=' . $refx['pmid'] . '\', ' . (!empty($refx['doi']) ? ('\'' . addslashes(stripslashes(strip_tags(trim($refx['doi'])))) . '\'') : 'NULL') . ', ' . (!empty($refx['title']) ? ('\'' . addslashes(stripslashes(strip_tags(trim($refx['title'])))) . '\'') : 'NULL') . ', ' . (!empty($_POST['comments_b']) ? ('\'' . addslashes(stripslashes(strip_tags(trim($_POST['comments_b'])))) . '\'') : 'NULL') . ' FROM reference;', $sql);
        if (!strlen($r = sql_last_error($sql))) {
          $result = sql_query('SELECT id FROM reference WHERE url=\'' . 'http://www.hubmed.org/display.cgi?uids=' . $refx['pmid'] . '\';', $sql);
          if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
            list($ret) = sql_fetch_row($result);
          }
        }
      }
    }
  }
  return $ret;
}

function put_ref($reference, $sql) {
  print '            <h3>' . ("References") . "</h3>\n";
  foreach(explode(' ', $reference) as $id) {
    $result_ref = sql_query('SELECT url, doi, title FROM reference WHERE id=' . $id . ';', $sql);
    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result_ref) > 0)) {
      while ($row_ref = sql_fetch_row($result_ref)) {
        print '            <div class="details"><a href="' . $row_ref[0] . '">' . htmlentities($row_ref[2], ENT_COMPAT, 'ISO-8859-15') . '</a>' . (!empty($row_ref[1])?'  [<a href="http://dx.doi.org/' . rawurlencode($row_ref[1]) . '" >doi</a>]':'') . "</div>\n";
      }
    }
  }
}

function fullyempty($val) {
  $result = false;
  if (empty($val)) $result = true;
  if (!$result && (trim($val) == '')) $result = true;
  if ($result && is_numeric($val) && $val == 0) $result = false;
  return $result;
}

function set_pref($pref, $status = false) {
  $_SESSION[$pref] = $status;
  return $_SESSION[$pref];
}

function get_pref($pref) {
  if (isset($_SESSION[$pref])) {
    return $_SESSION[$pref];
  }
  return false;
}

function EAN8( $type, $prefix, $id ) {
    $types=array( 'O' => 1, 'D' => 2 );
    if (!empty($type) && !empty($prefix) && !empty($id) && isset($types[$type])) {
        return sprintf("%d%03d%04d", $types[$type], $prefix, $id);
    }
}

function isdawn($timestamp,$latitude,$longitude) {  if (function_exists('date_sunrise')) {    $dawn = array('midnight', 'night', 'dawn', 'sunrise', 'morning', 'noon', 'afternoon', 'sunset', 'twilight', 'evening', 'midnight');    $timearray = getdate($timestamp);    $i = 0;    if ($timestamp >= mktime(0, 30, 0, $timearray['mon'] , $timearray['mday'], $timearray['year'])) {      $i++;      if ($timestamp >= date_sunrise($timestamp, SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, 108)) {        $i++; ;        if ($timestamp >= date_sunrise($timestamp, SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, 96)) {          $i++;          if ($timestamp >= date_sunrise($timestamp, SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, 90 + 50 / 60)) {            $i++;            if ($timestamp >= mktime(11, 30, 0, $timearray['mon'] , $timearray['mday'], $timearray['year'])) {              $i++;              if ($timestamp >= mktime(12, 30, 0, $timearray['mon'] , $timearray['mday'], $timearray['year'])) {                $i++;                if ($timestamp >= date_sunset($timestamp, SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, 90 + 50 / 60) && (date_sunset($timestamp, SUNFUNCS_RET_TIMESTAMP, $latidude, $longitude, 90 + 50 / 60) > 0)) {                  $i++;                  if ($timestamp >= date_sunset($timestamp, SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, 96) && (date_sunset($timestamp, SUNFUNCS_RET_TIMESTAMP, $latidude, $longitude, 96) > 0)) {                    $i++;                    if ($timestamp >= date_sunset($timestamp, SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, 108) && (date_sunset($timestamp, SUNFUNCS_RET_TIMESTAMP, $latidude, $longitude, 108) > 0)) {                      $i++;                      if ($timestamp >= mktime(23, 30, 0, $timearray['mon'] , $timearray['mday'], $timearray['year'])) {                        $i++;                      }                    }                  }                }              }            }          }        }      }    }    return $dawn[$i];  }  return '';}
?>