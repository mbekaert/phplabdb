<?php
ob_start("ob_gzhandler");
session_start();
require_once('../includes/main.inc');

if ($config['login'] && ($_SESSION['login']['right'] >= 3)) {
    $sql = sql_connect($config['db']);
    if (!empty($_POST['darwin']) && ($_POST['darwin'] == md5('import' . floor(intval(date('b'))))) && !empty($_POST['database']) && isset($_FILES['import']) && ($_FILES['import']['error'] == UPLOAD_ERR_OK) && ($_FILES['import']['size'] > 0) && is_uploaded_file($_FILES['import']['tmp_name']) && ($import = file_get_contents($_FILES['import']['tmp_name']))) {
        $prefix = floor(((intval(date('Y', time())) - 2001) * 12 + intval(date('m', time())) - 1) / 1.5);
        foreach(explode ("\n", $import) as $line) {
            $error = '';
            switch ($_POST['database']) {
                case 'bioject':
                    $row = explode("\t", trim($line), 13);
                    if ((count($row) > 5) && !empty($row[0]) && !empty($row[1]) && !empty($row[2]) && !empty($row[3]) && !empty($row[4]) && !empty($row[5]) && (($timestamp = strtotime(preg_replace('/(\d{1,2})\/(\d{1,2})\/(19|20)(\d{2})/', '\2/\1/\3\4', $row[3]))) !== false)) {
                        $result = sql_query('SELECT institutioncode FROM darwin_institution WHERE name =\'' . addslashes(stripslashes(strip_tags(trim($row[0])))) . '\';', $sql);
                        if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
                            list($row[0]) = sql_fetch_row($result);
                            $result = sql_query('SELECT collectioncode FROM darwin_collection WHERE name =\'' . addslashes(stripslashes(strip_tags(trim($row[1])))) . '\';', $sql);
                            if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
                                list($row[1]) = sql_fetch_row($result);
                                $result = sql_query('SELECT geolocation, datecollected FROM darwin_environment WHERE geolocation =\'' . addslashes(stripslashes(strip_tags(trim($row[2])))) . '\' AND datecollected =\'' . date('Y-m-d H:i:s', $timestamp) . '\';', $sql);
                                if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
                                    list($row[2], $row[3]) = sql_fetch_row($result);
                                    $result = sql_query('SELECT username FROM darwin_users WHERE real_name =\'' . addslashes(stripslashes(strip_tags(trim($row[4])))) . '\';', $sql);
                                    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
                                        list($row[4]) = sql_fetch_row($result);
                                        $result = sql_query('SELECT scientificname FROM tree_taxonomy WHERE scientificname =\'' . addslashes(stripslashes(strip_tags(trim($row[5])))) . '\';', $sql);
                                        if (((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) || (!empty($row[7]) && (((strlen($r = sql_last_error($sql))) || (sql_num_rows($result) != 1))))) {
                                            if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
                                                list($row[5]) = sql_fetch_row($result);
                                                unset($row[7]);
                                            } else {
                                                $row[5] = addslashes(stripslashes(strip_tags(trim($row[5]))));
                                                $row[7] = addslashes(stripslashes(strip_tags(trim($row[7]))));
                                            }
                                            if (!empty($row[6])) {
                                                $result = sql_query('SELECT name FROM darwin_identificationqualifier WHERE name =\'' . addslashes(stripslashes(strip_tags(trim($row[6])))) . '\';', $sql);
                                                if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
                                                    list($row[6]) = sql_fetch_row($result);
                                                } else {
                                                    unset($row[6]);
                                                }
                                            }
                                            if (!empty($row[8])) {
                                                $result = sql_query('SELECT institutioncode, collectioncode, catalognumber FROM darwin_bioject WHERE institutioncode =\'' . addslashes(stripslashes($row[0])) . '\' AND collectioncode =\'' . addslashes(stripslashes($row[1])) . '\' AND catalognumber =\'' . addslashes(stripslashes(strip_tags(trim($row[8])))) . '\';', $sql);
                                                if ((strlen($r = sql_last_error($sql))) || (sql_num_rows($result) != 0)) {
                                                    $error = _("Catalog number is already used");
                                                }
                                            }
                                            if (!empty($row[9])) {
                                                $result = sql_query('SELECT name FROM darwin_sex WHERE name =\'' . addslashes(stripslashes(strip_tags(trim($row[9])))) . '\';', $sql);
                                                if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
                                                    list($row[9]) = sql_fetch_row($result);
                                                } else {
                                                    unset($row[9]);
                                                }
                                            }
                                            if (!empty($row[10])) {
                                                $result = sql_query('SELECT name FROM darwin_conditionelement WHERE name =\'' . addslashes(stripslashes(strip_tags(trim($row[10])))) . '\';', $sql);
                                                if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
                                                    list($row[10]) = sql_fetch_row($result);
                                                } else {
                                                    unset($row[10]);
                                                }
                                            }
                                            if (empty($error)) {
                                                $catalognumber = md5(uniqid(rand(), true));
                                                $result = sql_query('INSERT INTO darwin_bioject (prefix, id, institutioncode, collectioncode, geolocation, event, observer, scientificname, identificationqualifier, taxon, catalognumber, sex, conditionelement, observedweight, observedsize, author) SELECT ' . $prefix . ', CASE WHEN max(id)>=1 THEN max(id)+1 ELSE 1 END, \'' . addslashes(stripslashes($row[0])) . '\', \'' . addslashes(stripslashes($row[1])) . '\', \'' . addslashes(stripslashes($row[2])) . '\', \'' . addslashes(stripslashes($row[3])) . '\', \'' . addslashes(stripslashes($row[4])) . '\', \'' . addslashes(stripslashes($row[5])) . '\', ' . (!empty($row[6])?'\'' . addslashes(stripslashes(strip_tags(trim($row[6])))) . '\'':'NULL') . ',' . (!empty($row[7])?'\'' . addslashes(stripslashes(strip_tags(trim($row[7])))) . '\'':'NULL') . ', \'' . $catalognumber . '\',' . (!empty($row[9])?'\'' . addslashes(stripslashes(strip_tags(trim($row[9])))) . '\'':'NULL') . ',' . (!empty($row[10])?'\'' . addslashes(stripslashes(strip_tags(trim($row[10])))) . '\'':'NULL') . ',' . (!empty($row[11])?floatval($row[11]):'NULL') . ',' . (!empty($row[12])?floatval($row[12]):'NULL') . ', \'' . addslashes($_SESSION['login']['username']) . '\' FROM darwin_bioject WHERE prefix=' . $prefix . ';', $sql);
                                                if (!strlen($r = sql_last_error($sql))) {
                                                    $result = sql_query('SELECT prefix, id FROM darwin_bioject WHERE (prefix=' . $prefix . ' AND catalognumber=\'' . $catalognumber . '\');', $sql);
                                                    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
                                                        $row2 = sql_fetch_row($result);
                                                        $result = sql_query('UPDATE darwin_bioject SET catalognumber=\'' . (!empty($row[8])?addslashes(stripslashes(strip_tags(trim($row[8])))):'B' . decoct($row2[0]) . '.' . decoct($row2[1])) . '\' WHERE (prefix=' . $prefix . ' AND catalognumber=\'' . $catalognumber . '\');', $sql);
                                                        print _("New entry") . ' ' . addslashes(stripslashes($row[0])) . '-' . addslashes(stripslashes($row[1])) . '-' . (!empty($row[8])?addslashes(stripslashes(strip_tags(trim($row[8])))):'B' . decoct($row2[0]) . '.' . decoct($row2[1])) . "\t(ref: B" . decoct($row2[0]) . '.' . decoct($row2[1]) . ")<br />\n";
                                                    } else {
                                                        $error = _("Database entry error:") . ' ' . $r;
                                                    }
                                                }
                                            }
                                        } else {
                                            $error = _("Species unkown (add a taxonomy entry)");
                                        }
                                    } else {
                                        $error = _("Observer unkown");
                                    }
                                } else {
                                    $error = _("Collection event unkown");
                                }
                            } else {
                                $error = _("Collection unkown");
                            }
                        } else {
                            $error = _("Institution unkown");
                        }
                    }
                    break;
                case 'samples':
                    $row = explode("\t", trim($line), 10);
                    if ((count($row) > 6) && !empty($row[0]) && !empty($row[1]) && !empty($row[2]) && !empty($row[3]) && !empty($row[4]) && !empty($row[5]) && !empty($row[6])) {
//                        $result = sql_query('SELECT institutioncode FROM darwin_institution WHERE name =\'' . addslashes(stripslashes(strip_tags(trim($row[0])))) . '\';', $sql);
//                        if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
//                            list($row[0]) = sql_fetch_row($result);
//                           $result = sql_query('SELECT collectioncode FROM darwin_collection WHERE name =\'' . addslashes(stripslashes(strip_tags(trim($row[1])))) . '\';', $sql);
//                            if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
//                                list($row[1]) = sql_fetch_row($result);
//                                $result = sql_query('SELECT prefix, id, catalognumber FROM darwin_bioject WHERE institutioncode =\'' . addslashes(stripslashes($row[0])) . '\' AND collectioncode =\'' . addslashes(stripslashes($row[1])) . '\' AND catalognumber =\'' . addslashes(stripslashes(strip_tags(trim($row[2])))) . '\';', $sql);
$result = sql_query('SELECT prefix, id, catalognumber, institutioncode, collectioncode FROM darwin_bioject WHERE catalognumber =\'' . addslashes(stripslashes(strip_tags(trim($row[2])))) . '\';', $sql);
                                if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
                                    $row[2] = sql_fetch_row($result);
$row[0] = $row[2][3];
$row[1] = $row[2][4];
                                    $result = sql_query('SELECT username FROM darwin_users WHERE real_name =\'' . addslashes(stripslashes(strip_tags(trim($row[3])))) . '\';', $sql);
                                    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
                                        list($row[3]) = sql_fetch_row($result);
                                        $result = sql_query('SELECT name FROM darwin_disposition WHERE name =\'' . addslashes(stripslashes(strip_tags(trim($row[4])))) . '\';', $sql);
                                        if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
                                            list($row[4]) = sql_fetch_row($result);
                                            $result = sql_query('SELECT name FROM darwin_basisofrecord WHERE name =\'' . addslashes(stripslashes(strip_tags(trim($row[6])))) . '\';', $sql);
                                            if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
                                                list($row[6]) = sql_fetch_row($result);
                                                if (!empty($row[8])) {
                                                    $result = sql_query('SELECT name FROM darwin_protocol WHERE name =\'' . addslashes(stripslashes(strip_tags(trim($row[8])))) . '\';', $sql);
                                                    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
                                                        list($row[8]) = sql_fetch_row($result);
                                                    } else {
                                                        unset($row[8]);
                                                    }
                                                }
                                                if (!empty($row[9])) {
                                                    $result = sql_query('SELECT name FROM darwin_preservationmethod WHERE name =\'' . addslashes(stripslashes(strip_tags(trim($row[9])))) . '\';', $sql);
                                                    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
                                                        list($row[9]) = sql_fetch_row($result);
                                                    } else {
                                                        unset($row[9]);
                                                    }
                                                }
                                                $result = sql_query('INSERT INTO darwin_sample (prefix, id, subcatalognumber, bioject_prefix, bioject_id, basisofrecord, observer, protocolname, preservationmethod, disposition, partname, stockbox, author) SELECT ' . $prefix . ', CASE WHEN max(id)>=1 THEN max(id)+1 ELSE 1 END, 0,' . $row[2][0] . ',' . $row[2][1] . ',\'' . addslashes(stripslashes($row[6])) . '\',\'' . addslashes(stripslashes($row[3])) . '\',' . (!empty($row[8])?'\'' . addslashes(stripslashes($row[8])) . '\'':'NULL') . ',' . (!empty($row[9])?'\'' . addslashes(stripslashes($row[9])) . '\'':'NULL') . ',' . (!empty($row[4])?'\'' . addslashes(stripslashes($row[4])) . '\'':'NULL') . ',' . (!empty($row[7])?'\'' . addslashes(stripslashes(strip_tags(trim($row[7])))) . '\'':'NULL') . ',' . (!empty($row[5])?'\'' . addslashes(stripslashes(strip_tags(trim($row[5])))) . '\'':'NULL') . ',\'' . addslashes($_SESSION['login']['username']) . '\' FROM darwin_sample WHERE prefix=' . $prefix . ';', $sql);
                                                if (!strlen($r = sql_last_error($sql))) {
                                                    $result = sql_query('SELECT (max(subcatalognumber)+1) FROM darwin_sample WHERE (bioject_prefix=' . $row[2][0] . ' AND bioject_id=' . $row[2][1] . ');', $sql);
                                                    if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) == 1)) {
                                                        list($subcatalognumber) = sql_fetch_row($result);;
                                                        $result = sql_query('SELECT prefix, id FROM darwin_sample WHERE (prefix=' . $prefix . ' AND bioject_prefix=' . $row[2][0] . ' AND bioject_id=' . $row[2][1] . ' AND subcatalognumber=0) ORDER BY updated DESC;', $sql);
                                                        if ((!strlen($r = sql_last_error($sql))) && (sql_num_rows($result) >= 1)) {
                                                            $row2 = sql_fetch_row($result);
                                                            $result = sql_query('UPDATE darwin_sample SET subcatalognumber=' . $subcatalognumber . ' WHERE (prefix=' . $row2[0] . ' AND id=' . $row2[1] . ' AND subcatalognumber=0) ;', $sql);
                                                            print _("New sample") . ' ' . addslashes(stripslashes($row[0])) . '-' . addslashes(stripslashes($row[1])) . '-' . addslashes(stripslashes($row[2][2])) . $subcatalognumber . "\t(ref: S" . decoct($row2[0]) . '.' . decoct($row2[1]) . ")<br />\n";
                                                        } else {
                                                            $error = _("Database entry error:") . ' ' . $r;
                                                        }
                                                    } else {
                                                        $error = _("Database entry error:") . ' ' . $r;
                                                    }
                                                }
                                            } else {
                                                $error = _("Base of Record unknown ");
                                            }
                                        } else {
                                            $error = _("Disposition unknown ");
                                        }
                                    } else {
                                        $error = _("Observer unkown");
                                    }
                                } else {
                                    $error = _("Catalog number unknown");
                                }
//                            } else {
//                                $error = _("Collection unkown");
//                            }
//                        } else {
//                            $error = _("Institution unkown");
//                        }
                    }
                    break;
            }
            if (!empty($error)) {
                print "\n<strong>$error!</strong>\n in $line<br />\n";
            }
        }
    }
    head('darwin');

    ?>
      <div class="items">
        <h1><?php print $plugin['darwin']['name'];
    ?><small><?php print $plugin['darwin']['description'];
    ?></small></h1><br />
        <form method="post" action="<?php print $config['server'] . $plugin['darwin']['url'];
    ?>/import.php" enctype="multipart/form-data">
        <div>
			<h2><?php print _("Import");
    ?></h2><br /><?php print _("You may import large amount of Sample (and the correspondig Specimens). The file must be a text file containing the following information, separated by tabs. The program will extract the primer information from the file. (From Excel, choose 'Save AS', then 'Text (Tab delimited)' as format)");
    ?>
<br />
<strong>Institution</strong> [tab] <strong>Collection</strong> [tab] <strong>Location</strong> [tab] <strong>Collecting date</strong> [tab] <strong>Observer</strong> [tab] <strong>Species</strong> [tab] Identification [tab] Taxonimy [tab] Catalog number [tab] Sex [tab] Specimen Condition [tab] Observed weight [tab] Observed size

<br />
<strong>Institution</strong> [tab] <strong>Collection</strong> [tab] <strong>Catalog number</strong> [tab] <strong>Observer</strong> [tab] <strong>Disposition</strong> [tab] <strong>Box</strong> [tab] <strong>Basis of Record</strong> [tab] Part Name [tab] Protocol used [tab] Preservation method

<br /><br /><?php print (isset($error)?'            <strong>' . $error . "</strong><br /><br />\n":'');
    ?>
			  <div>
              <label for="import"><?php print _("File name");
    ?></label>
				  <input name="import" id="import" type="file" title="<?php print _("File containing one or more samples");
    ?>" />
				  <br />
				  </div>
			  <div>
				<label for="database"><?php print _("Database");
    ?></label>
					<select name="database" id="database" title="<?php print _("The full name of the database");
    ?>"><option value=""></option><option value="<?php print _("geolocation");
    ?>"><?php print _("Locations");
    ?></option><option value="<?php print _("environment");
    ?>"><?php print _("Collecting events");
    ?></option><option value="<?php print _("bioject");
    ?>"><?php print _("Specimens");
    ?></option><option value="<?php print _("samples");
    ?>"><?php print _("Samples");
    ?></option></select>
					<br />
					</div>
           <br />
          <input type="hidden" name="darwin" value="<?php print md5('import' . floor(intval(date('b'))));
    ?>" />
          <input type="reset" value="<?php print _("Clear");
    ?>" />&nbsp;<input type="submit" value="<?php print _("Import");
    ?>" />
        </div>
        </form>
        <br />
      </div>
<?php
    foot();
} else {
    header('Location: ' . $config['server']);
}

?>