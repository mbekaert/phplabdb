<?php
// All code is based on the (US) Federal Meteorological Handbook
// http://www.ofcm.gov/fmh-1/fmh1.htm
//
// Adapted from WeatherIcon
//
class WeatherIconMetar {
  var $decoded_metar;
  var $units;

  function WeatherIconMetar($metar, $unit = array('temp' => 'C', 'visibility' => 'm', 'wind' => 'km/h', 'clouds' => 'm', 'pressure' => 'hPa')) {
    // clear the decoded metar
    $this->decoded_metar = array();
    $this->units = $unit;
    $this->decoded_metar['metar'] = trim($metar);
    // Decode our metar
    $this->decoded_metar();
  }

  function store_speed($value, $windunit, &$knots, &$meterspersec, &$kilometersperhour, &$milesperhour) {
    if ($value == 0) {
      $knots = 0;
      $meterspersec = 0;
      $kilometersperhour = 0;
      $milesperhour = 0;
      return;
    }

    if ($windunit == 'KT') {
      // The windspeed measured in knots:
      $knots = number_format($value);
      // The windspeed measured in meters per second, rounded to one decimal place
      $meterspersec = number_format($value * 0.5144, 1);
      // The windspeed measured in kilometers per hour, rounded to one decimal place
      $kilometersperhour = number_format($value * 1.85200, 1);
      // The windspeed measured in miles per hour, rounded to one decimal place
      $milesperhour = number_format($value * 1.1508, 1);
    }elseif ($windunit == 'MPS') {
      // The windspeed measured in meters per second
      $meterspersec = number_format($value);
      // The windspeed measured in knots, rounded to one decimal place
      $knots = number_format($value / 0.5144, 1);
      // The windspeed measured in kilometers per hour, rounded to one decimal place
      $kilometersperhour = number_format($value * 3.6, 1);
      // The windspeed measured in miles per hour, rounded to one decimal place
      $milesperhour = number_format($value / 0.5144 * 1.1508, 1);
    }elseif ($windunit == 'KMH') {
      // The windspeed measured in kilometers per hour
      $kilometersperhour = number_format($value);
      // The windspeed measured in kilometers per hour
      $meterspersec = number_format($value * 1000 / 3600, 1);
      $knots = number_format($value * 1000 / 3600 / 0.5144, 1);
      // The windspeed measured in miles per hour, rounded to one decimal place
      $milesperhour = number_format($knots * 1.1508, 1);
    }
  }

  function store_temp($temp, &$temp_c, &$temp_f) {
    // Note: $temp is converted to negative if $temp > 100.0 (See
    // Federal Meteorological Handbook for groups T, 1, 2 and 4).
    // For example, a temperature of 2.6°C and dew point of -1.5°C
    // would be reported in the body of the report as "03/M01" and the
    // TsnT'T'T'snT'dT'dT'd group as "T00261015").
    if ($temp[0] == 1) {
      $temp[0] = '-';
    }
    // The temperature in Celsius.
    $temp_c = number_format($temp, 1);
    // The temperature in Fahrenheit.
    $temp_f = number_format($temp * (9 / 5) + 32, 1);
  }

  function decode_direction($deg) {
    $dir = '';

    switch (round($deg / 22.5) % 16) {
      case 0: $dir = _('N');
        break;
      case 1: $dir = _('NNE');
        break;
      case 2: $dir = _('NE');
        break;
      case 3: $dir = _('ENE');
        break;
      case 4: $dir = _('E');
        break;
      case 5: $dir = _('ESE');
        break;
      case 6: $dir = _('SE');
        break;
      case 7: $dir = _('SSE');
        break;
      case 8: $dir = _('S');
        break;
      case 9: $dir = _('SSW');
        break;
      case 10: $dir = _('SW');
        break;
      case 11: $dir = _('WSW');
        break;
      case 12: $dir = _('W');
        break;
      case 13: $dir = _('WNW');
        break;
      case 14: $dir = _('NW');
        break;
      case 15: $dir = _('NNW');
        break;
    }

    return $dir;
  }

  function decoded_metar() {
    // initialization
    $temp_visibility_miles = '';
    $this->decoded_metar['remarks'] = '';
    // Parse the metar
    $parts = explode(' ', $this->decoded_metar['metar']);
    $num_parts = count($parts);

    for ($i = 0; $i < $num_parts; $i++) {
      $part = $parts[$i];

      if (ereg('RMK|TEMPO|BECMG|INTER', $part)) {
        // The rest of the METAR is either a remark or temporary
        // information. We keep the remark.
        for($j = $i;$j < $num_parts; $j++) {
          $this->decoded_metar['remarks'] .= ' ' . $parts[$j];
        }

        $this->decoded_metar['remarks'] = trim($this->decoded_metar['remarks']);
        break;
      }elseif ($part == 'METAR') {
        // Type of Report: METAR
        $this->decoded_metar['type'] = 'METAR';
      }elseif ($part == 'SPECI') {
        // Type of Report: SPECI
        $this->decoded_metar['type'] = 'SPECI';
      }elseif (ereg('^[A-Z]{4}$', $part) && empty($this->decoded_metar['icao'])) {
        // Station Identifier
        $this->decoded_metar['icao'] = $part;
      }elseif (ereg('([0-9]{2})([0-9]{2})([0-9]{2})Z', $part, $regs)) {
        // Date and Time of Report.

        // We return a standard Unix UTC/GMT timestamp suitable for
        // gmdate().

        // If you experience incorrect timestamps but cannot set the
        // clock, then you can set $this->properties['offset'] to be
        // the offset in hours to add. For example, if your times
        // generated are 1 hour too early (so METARs appear an hour
        // older than they are), set $this->properties['offset'] to be
        // +1 in your defaults.php file.
        if ($regs[1] > gmdate('j')) {
          // The day is greather that the current day of month => the
          // report is from last month.
          $month = gmdate('n') - 1;
        }else {
          $month = gmdate('n');
        }

        $this->decoded_metar['time']['update'] =
        gmmktime($regs[2], $regs[3], 0, $month, $regs[1], gmdate('Y'));
      }elseif (ereg('(AUTO|COR|RTD|CC[A-Z]|RR[A-Z])', $part, $regs)) {
        // Report Modifier: AUTO, COR, CCx or RRx
        $this->decoded_metar['report_mod'] = $regs[1];
      }elseif (ereg('([0-9]{3}|VRB)([0-9]{2,3})G?([0-9]{2,3})?(KT|MPS|KMH)', $part, $regs)) {
        // Wind Group
        $this->decoded_metar['wind']['raw'] = $regs[0];
        $this->decoded_metar['wind']['deg'] = $regs[1];
        $this->decoded_metar['wind']['dir'] = $this->decode_direction($regs[1]);

        $this->store_speed($regs[2],
          $regs[4],
          $this->decoded_metar['wind']['knots'],
          $this->decoded_metar['wind']['m/s'],
          $this->decoded_metar['wind']['km/h'],
          $this->decoded_metar['wind']['mph']);

        if (!empty($regs[3])) {
          // We have a report with information about the gust.
          // First we have the gust measured in knots.
          $this->store_speed($regs[3],
            $regs[4],
            $this->decoded_metar['wind']['gust']['knots'],
            $this->decoded_metar['wind']['gust']['m/s'],
            $this->decoded_metar['wind']['gust']['km/h'],
            $this->decoded_metar['wind']['gust']['mph']);
        }
      }elseif (ereg('^([0-9]{3})V([0-9]{3})$', $part, $regs) && !empty($this->decoded_metar['wind'])) {
        // Variable wind-direction
        $this->decoded_metar['wind']['varraw'] = $regs[0];
        $this->decoded_metar['wind']['var_beg'] = $regs[1];
        $this->decoded_metar['wind']['var_end'] = $regs[2];
      }elseif (ereg('^([0-9]{4})([NESW]?[NESWD]?[NESWV]?)$', $part, $regs)) {
        // Visibility in meters (4 digits only)
        unset($group);
        $group['raw'] = $regs[0];

        if ($regs[1] == '0000') {
          // Special low value
          $group['prefix'] = -1; //
          $group['m'] = 50;
          $group['km'] = 0.05;
          $group['ft'] = 164;
          $group['miles'] = 0.031;
        }elseif ($regs[1] == '9999') {
          // Special high value
          $group['prefix'] = 1;
          $group['m'] = 10000;
          $group['km'] = 10;
          $group['ft'] = 32800;
          $group['miles'] = 6.2;
        }else {
          // Normal visibility, returned in both small and large units.
          $group['prefix'] = 0;
          $group['km'] = number_format($regs[1] / 1000, 1);
          $group['miles'] = number_format($regs[1] / 1609.344, 1);
          $group['m'] = $regs[1] * 1;
          $group['ft'] = round($regs[1] * 3.28084);
        }

        if (!empty($regs[2])) {
          $group['deg'] = $regs[2];
        }

        $this->decoded_metar['visibility'][] = $group;
      }elseif (ereg('^[0-9]$', $part)) {
        // Temp Visibility Group, single digit followed by space.
        $temp_visibility_miles = $part;
      }elseif (ereg('^M?(([0-9]?)[ ]?([0-9])(/?)([0-9]*))SM$',
          $temp_visibility_miles . ' ' . $part, $regs)) {
        // Visibility Group
        unset($group);
        $group['raw'] = $regs[0];
        if ($regs[4] == '/') {
          $vis_miles = $regs[2] + $regs[3] / $regs[5];
        }else {
          $vis_miles = $regs[1];
        }

        if ($regs[0][0] == 'M') {
          // Prefix - less than
          $group['prefix'] = -1;
        }else {
          $group['prefix'] = 0;
        }
        // The visibility measured in miles
        $group['miles'] = number_format($vis_miles, 1);
        // The visibility measured in feet
        $group['ft'] = round($vis_miles * 5280, 1);
        // The visibility measured in kilometers
        $group['km'] = number_format($vis_miles * 1.6093, 1);
        // The visibility measured in meters
        $group['m'] = round($vis_miles * 1609.3);

        $this->decoded_metar['visibility'][] = $group;
      }elseif ($part == 'CAVOK') {
        // CAVOK is used when the visibility is greater than 10
        // kilometers, the lowest cloud-base is at 5000 feet or more
        // and there is no significant weather.
        unset($group);
        $group['raw'] = $part;
        $group['prefix'] = 1;
        $group['km'] = 10;
        $group['m'] = 10000;
        $group['miles'] = 6.2;
        $group['ft'] = 32800;
        $this->decoded_metar['visibility'][] = $group;
        $this->decoded_metar['clouds'][]['condition'] = 'CAVOK';
      }elseif (ereg('^R([0-9]{2})([RLC]?)/([MP]?)([0-9]{4})' . '([DNU]?)(FT?)?V?(P?)([0-9]{4})?([DNU]?)?(FT?)?/?([NWSE]?)?$', $part, $regs)) {
        // Runway-group
        unset($group);
        $group['raw'] = $regs[0];
        $group['nr'] = $regs[1];

        if (!empty($regs[2])) {
          $group['approach'] = $regs[2];
        }

        if (!empty($regs[8])) {
          // We have both min and max visibility since $regs[7] holds
          // the max visibility.
          if (!empty($regs[5])) {
            // $regs[5] is tendency for min visibility.
            $group['min_tendency'] = $regs[5];
          }

          if (!empty($regs[9])) {
            // $regs[9] is tendency for max visibility.
            $group['max_tendency'] = $regs[9];
          }

          if ($regs[3] == 'M') {
            // Less than.
            $group['min_prefix'] = -1;
          }

          if ($regs[6] == 'FT') {
            $group['min_meter'] = round($regs[4] / 3.2808);
            $group['min_ft'] = $regs[4] * 1;
          }else {
            $group['min_meter'] = $regs[4] * 1;
            $group['min_ft'] = round($regs[4] * 3.2808);
          }

          if ($regs[7] == 'P') {
            // Greater than.
            $group['max_prefix'] = 1;
          }

          if ($regs[10] == 'FT') {
            $group['max_meter'] = $regs[8] * 1;
            $group['max_ft'] = round($regs[8] * 3.2808);
          }else {
            $group['max_ft'] = $regs[8] * 1;
            $group['max_meter'] = round($regs[8] / 3.2808);
          }
        }else {
          // We only have a single visibility.
          if (!empty($regs[5])) {
            // $regs[5] holds the tendency for visibility.
            $group['tendency'] = $regs[5];
          }

          if ($regs[3] == 'M') {
            // Less than.
            $group['prefix'] = -1;
          }elseif ($regs[3] == 'P') {
            // Greater than.
            $group['prefix'] = 1;
          }

          if ($regs[6] == 'FT') {
            $group['m'] = round($regs[4] / 3.2808);
            $group['ft'] = $regs[4] * 1;
          }else {
            $group['m'] = $regs[4] * 1;
            $group['ft'] = round($regs[4] * 3.2808);
          }

          if (!empty($regs[11])) {
            $group['deg'] = $regs[11];
          }
        }

        $this->decoded_metar['runway'][] = $group;
      }elseif (ereg('^(VC)?' . // Proximity
          '(-|\+)?' . // Intensity
          '(MI|PR|BC|DR|BL|SH|TS|FZ)?' . // Descriptor
          '((DZ|RA|SN|SG|IC|PL|GR|GS|UP)+)?' . // Precipitation
          '(BR|FG|FU|VA|DU|SA|HZ|PY)?' . // Obscuration
          '(PO|SQ|FC|SS)?$', // Other
          $part, $regs)) {
        // Current weather-group.
        $this->decoded_metar['weather'][] = array('raw' => $regs[0], 'proximity' => $regs[1],
          'intensity' => $regs[2],
          'descriptor' => $regs[3],
          'precipitation' => $regs[4],
          'obscuration' => $regs[6],
          'other' => $regs[7]);
      }elseif ($part == 'SKC' || $part == 'CLR' || $part == 'NSC') {
        // Cloud-group
        $this->decoded_metar['clouds'][]['condition'] = $part;
      }elseif (ereg('^(VV|FEW|SCT|BKN|OVC)([0-9]{3}|///)' . '(CB|TCU)?$', $part, $regs)) {
        // We have found (another) a cloud-layer-group.
        unset($group);
        $group['raw'] = $regs[0];
        $group['condition'] = $regs[1];
        if (!empty($regs[3])) {
          $group['cumulus'] = $regs[3];
        }

        if ($regs[2] == '000') {
          // '000' is a special height.
          $group['km'] = 0.03;
          $group['miles'] = 0.02;
          $group['m'] = 30;
          $group['ft'] = 100;
          $group['prefix'] = -1; // Less than
        }elseif ($regs[2] == '///') {
          // '///' means height nil
          $group['km'] = 'nil';
          $group['miles'] = 'nil';
          $group['m'] = 'nil';
          $group['ft'] = 'nil';
        }else {
          $group['km'] = number_format($regs[2] * .03048, 2);
          $group['miles'] = number_format($regs[2] * .0189, 2);
          $group['m'] = round($regs[2] * 30.48);
          $group['ft'] = $regs[2] * 100;
        }
        $this->decoded_metar['clouds'][] = $group;
      }elseif (ereg('^(M?[0-9]{2})/(M?[0-9]{2}|//)?$', $part, $regs)) {
        // Temperature/Dew Point Group.
        $this->decoded_metar['temperature']['raw'] = $regs[0];
        $this->decoded_metar['temperature']['temp_c'] =
        round(strtr($regs[1], 'M', '-'));

        $this->decoded_metar['temperature']['temp_f'] =
        round(strtr($regs[1], 'M', '-') * (9 / 5) + 32);
        // The dewpoint could be missing, this is indicated by the
        // second group being empty at most places, but in the UK they
        // use '//' instead of the missing temperature...
        if (!empty($regs[2]) && $regs[2] != '//') {
          $this->decoded_metar['temperature']['dew_c'] =
          round(strtr($regs[2], 'M', '-'));
          $this->decoded_metar['temperature']['dew_f'] =
          round(strtr($regs[2], 'M', '-') * (9 / 5) + 32);
        }
      }elseif (ereg('A([0-9]{4})', $part, $regs)) {
        // Altimeter.
        $this->decoded_metar['altimeter']['raw'] = $regs[0];
        // The pressure measured in inHg.
        $this->decoded_metar['altimeter']['inhg'] =
        number_format($regs[1] / 100, 2);
        // The pressure measured in mmHg, hPa and atm
        $this->decoded_metar['altimeter']['mmhg'] =
        number_format($regs[1] * 0.254, 1, '.', '');

        $this->decoded_metar['altimeter']['hpa'] =
        round($regs[1] * 0.33864);

        $this->decoded_metar['altimeter']['atm'] =
        number_format($regs[1] * 3.3421e-4, 3, '.', '');
      }elseif (ereg('Q([0-9]{4})', $part, $regs)) {
        // Altimeter.
        $this->decoded_metar['altimeter']['raw'] = $regs[0];
        // The specification doesn't say anything about
        // the Qxxxx-form, but it's in the METARs.
        // The pressure measured in hPa
        $this->decoded_metar['altimeter']['hpa'] = round($regs[1]);
        // The pressure measured in mmHg, inHg and atm
        $this->decoded_metar['altimeter']['mmhg'] =
        number_format($regs[1] * 0.75006, 1, '.', '');

        $this->decoded_metar['altimeter']['inhg'] =
        number_format($regs[1] * 0.02953, 2);

        $this->decoded_metar['altimeter']['atm'] =
        number_format($regs[1] * 9.8692e-4, 3, '.', '');
      }elseif (ereg('^T([0-9]{4})([0-9]{4})', $part, $regs)) {
        $this->decoded_metar['temperature']['raw'] = $regs[0];
        // Temperature/Dew Point Group, coded to tenth of degree Celsius.
        $this->store_temp($regs[1] / 10,
          $this->decoded_metar['temperature']['temp_c'],
          $this->decoded_metar['temperature']['temp_f']);

        $this->store_temp($regs[2] / 10,
          $this->decoded_metar['temperature']['dew_c'],
          $this->decoded_metar['temperature']['dew_f']);
      }elseif (ereg('^T([0-9]{4}$)', $part, $regs)) {
        $this->decoded_metar['temperature']['raw'] = $regs[0];
        $this->store_temp($regs[1],
          $this->decoded_metar['temperature']['temp_c'],
          $this->decoded_metar['temperature']['temp_f']);
      }elseif (ereg('^1([0-9]{4}$)', $part, $regs)) {
        // 6 hour maximum temperature Celsius, coded to tenth of degree
        $this->store_temp($regs[1] / 10,
          $this->decoded_metar['temp_min_max']['max6h_c'],
          $this->decoded_metar['temp_min_max']['max6h_f']);
      }elseif (ereg('^2([0-9]{4}$)', $part, $regs)) {
        // 6 hour minimum temperature Celsius, coded to tenth of degree
        $this->store_temp($regs[1] / 10,
          $this->decoded_metar['temp_min_max']['min6h_c'],
          $this->decoded_metar['temp_min_max']['min6h_f']);
      }elseif (ereg('^4([0-9]{4})([0-9]{4})$', $part, $regs)) {
        // 24 hour maximum and minimum temperature Celsius, coded to
        // tenth of degree
        $this->store_temp($regs[1] / 10,
          $this->decoded_metar['temp_min_max']['max24h_c'],
          $this->decoded_metar['temp_min_max']['max24h_f']);

        $this->store_temp($regs[2] / 10,
          $this->decoded_metar['temp_min_max']['min24h_c'],
          $this->decoded_metar['temp_min_max']['min24h_f']);
      }elseif (ereg('^P([0-9]{4})', $part, $regs)) {
        // Precipitation during last hour in hundredths of an inch
        if ($regs[1] == '0000') {
          $this->decoded_metar['precipitation']['in'] = -1;
          $this->decoded_metar['precipitation']['mm'] = -1;
        }else {
          $this->decoded_metar['precipitation']['in'] =
          number_format($regs[1] / 100, 2);
          $this->decoded_metar['precipitation']['mm'] =
          number_format($regs[1] * 0.254, 2);
        }
      }elseif (ereg('^6([0-9]{4})', $part, $regs)) {
        // Precipitation during last 3 or 6 hours in hundredths of an
        // inch.
        if ($regs[1] == '0000') {
          $this->decoded_metar['precipitation']['in_6h'] = -1;
          $this->decoded_metar['precipitation']['mm_6h'] = -1;
        }else {
          $this->decoded_metar['precipitation']['in_6h'] =
          number_format($regs[1] / 100, 2);

          $this->decoded_metar['precipitation']['mm_6h'] =
          number_format($regs[1] * 0.254, 2);
        }
      }elseif (ereg('^7([0-9]{4})', $part, $regs)) {
        // Precipitation during last 24 hours in hundredths of an inch.
        if ($regs[1] == '0000') {
          $this->decoded_metar['precipitation']['in_24h'] = -1;
          $this->decoded_metar['precipitation']['mm_24h'] = -1;
        }else {
          $this->decoded_metar['precipitation']['in_24h'] =
          number_format($regs[1] / 100, 2, '.', '');
          $this->decoded_metar['precipitation']['mm_24h'] =
          number_format($regs[1] * 0.254, 2, '.', '');
        }
      }elseif (ereg('^4/([0-9]{3})', $part, $regs)) {
        // Snow depth in inches
        if ($regs[1] == '0000') {
          $this->decoded_metar['precipitation']['snow_in'] = -1;
          $this->decoded_metar['precipitation']['snow_mm'] = -1;
        }else {
          $this->decoded_metar['precipitation']['snow_in'] = $regs[1] * 1;
          $this->decoded_metar['precipitation']['snow_mm'] = round($regs[1] * 25.4);
        }
      }else {
        // If we couldn't match the group, we assume that it was a
        // remark.
        $this->decoded_metar['remarks'] .= ' ' . $part;
      }
    }
    // ---------------------
    // POST METAR PROCESSING
    // ---------------------
    // Relative humidity
    if (!empty($this->decoded_metar['temperature']['temp_c']) && !empty($this->decoded_metar['temperature']['dew_c'])) {
      $this->decoded_metar['rel_humidity'] =
      number_format(pow(10, (1779.75 * ($this->decoded_metar['temperature']['dew_c'] - $this->decoded_metar['temperature']['temp_c']) / ((237.3 + $this->decoded_metar['temperature']['dew_c']) * (237.3 + $this->decoded_metar['temperature']['temp_c'])) + 2)), 1);
    }
    // Compute windchill if temp < 51f and windspeed > 3 mph
    if (!empty($this->decoded_metar['temperature']['temp_f']) && $this->decoded_metar['temperature']['temp_f'] < 51 && !empty($this->decoded_metar['wind']['mph']) && $this->decoded_metar['wind']['mph'] > 3) {
      $this->decoded_metar['windchill']['windchill_f'] =
      number_format(35.74 + 0.6215 * $this->decoded_metar['temperature']['temp_f'] - 35.75 * pow((float)$this->decoded_metar['wind']['mph'], 0.16) + 0.4275 * $this->decoded_metar['temperature']['temp_f'] * pow((float)$this->decoded_metar['wind']['mph'], 0.16));
      $this->decoded_metar['windchill']['windchill_c'] =
      number_format(13.112 + 0.6215 * $this->decoded_metar['temperature']['temp_c'] - 13.37 * pow(($this->decoded_metar['wind']['mph'] / 1.609), 0.16) + 0.3965 * $this->decoded_metar['temperature']['temp_c'] * pow(($this->decoded_metar['wind']['mph'] / 1.609), 0.16));
    }
    // Compute heat index if temp > 70F
    if (!empty($this->decoded_metar['temperature']['temp_f']) && $this->decoded_metar['temperature']['temp_f'] > 70 && !empty($this->decoded_metar['rel_humidity'])) {
      $this->decoded_metar['heatindex']['heatindex_f'] =
      number_format(-42.379 + 2.04901523 * $this->decoded_metar['temperature']['temp_f'] + 10.1433312 * $this->decoded_metar['rel_humidity'] - 0.22475541 * $this->decoded_metar['temperature']['temp_f'] * $this->decoded_metar['rel_humidity'] - 0.00683783 * $this->decoded_metar['temperature']['temp_f'] * $this->decoded_metar['temperature']['temp_f'] - 0.05481717 * $this->decoded_metar['rel_humidity'] * $this->decoded_metar['rel_humidity'] + 0.00122874 * $this->decoded_metar['temperature']['temp_f'] * $this->decoded_metar['temperature']['temp_f'] * $this->decoded_metar['rel_humidity'] + 0.00085282 * $this->decoded_metar['temperature']['temp_f'] * $this->decoded_metar['rel_humidity'] * $this->decoded_metar['rel_humidity'] - 0.00000199 * $this->decoded_metar['temperature']['temp_f'] * $this->decoded_metar['temperature']['temp_f'] * $this->decoded_metar['rel_humidity'] * $this->decoded_metar['rel_humidity']);

      $this->decoded_metar['heatindex']['heatindex_c'] =
      number_format(($this->decoded_metar['heatindex']['heatindex_f'] - 32) / 1.8);
    }
    // Compute the humidity index
    if (!empty($this->decoded_metar['rel_humidity'])) {
      $e = (6.112 * pow(10, 7.5 * $this->decoded_metar['temperature']['temp_c'] / (237.7 + $this->decoded_metar['temperature']['temp_c'])) * $this->decoded_metar['rel_humidity'] / 100) - 10;
      $this->decoded_metar['humidex']['humidex_c'] =
      number_format($this->decoded_metar['temperature']['temp_c'] + 5 / 9 * $e, 1);
      $this->decoded_metar['humidex']['humidex_f'] =
      number_format($this->decoded_metar['humidex']['humidex_c'] * 9 / 5 + 32, 1);
    }
    // Humanized the weather conditions
    if (isset($this->decoded_metar['weather']) && (count($this->decoded_metar['weather']) > 0)) {
      foreach ($this->decoded_metar['weather'] as $count => $weather) {
        $conditions = "";
        // 12.6.8.e.(2):
        // Tornadoes and waterspouts shall be coded as +FC.
        if ($weather['other'] <> 'FC') {
          // Intensity
          switch ($weather['intensity']) {
            case '+': $conditions .= _('Heavy') . ' ';
              break;
            case '-': $conditions .= _('Light') . ' ';
              break;
          }
        }
        // Check for Thunderstorm
        switch ($weather['descriptor']) {
          // 12.8.6.b.(4):
          // TS can be designated alone or with single or multiple
          // precipitation codes;
          case 'TS':
            $conditions .= _('Thunderstorm') . ' ';

            if (strlen(trim($weather['precipitation'] . $weather['obscuration'] . $weather['other'])) > 0) {
              $conditions .= '(';
              $conditions .= _('with') . ' ';
            }

            break;
        }
        // There can be multiple precipitatation codes for one weather
        // event.

        // IE: Thunderstorm with Snow and Small Hail would be
        // TSSNGS with $weather['precipitation'] = 'SNGS'

        // As a result, we have to explode the string into two character array
        foreach (explode("\r\n", chunk_split($weather['precipitation'], 2)) as $pcnt => $precipitation) {
          if (strlen(trim($precipitation)) > 0 and $pcnt <> 0) {
            $conditions .= _('and') . ' ';
          }

          switch (trim($precipitation)) {
            case 'DZ':
              // Check for Valid Weather Phenomenoa Qualifier for Drizzle
              switch ($weather['descriptor']) {
                // 12.8.6.b.(5):
                // FZ can only be designated with FG, DZ, RA
                case 'FZ': $conditions .= _('Freezing') . ' ';
                  break;
              }

              $conditions .= _('Drizzle') . ' ';

              break;

            case 'RA':
              // Check for Valid Weather Phenomenoa Qualifier for Rain
              switch ($weather['descriptor']) {
                // 12.8.6.b.(5):
                // FZ can only be designated with FG, DZ, RA
                case 'FZ': $conditions .= _('Freezing') . ' ';
                  break;
              }

              $conditions .= _('Rain') . ' ';
              // Check for Valid Weather Phenomenoa Qualifier for Snow
              switch ($weather['descriptor']) {
                // 12.8.6.b.(3): SH only with RA, SN, PL, GS, and GR
                // Changed to Falling since Snow Showers is stupid
                case 'SH': $conditions .= _('Showers') . ' ';
              }

              break;

            case 'SN':
              // Check for Valid Weather Phenomenoa Qualifier for Snow
              switch ($weather['descriptor']) {
                // 12.8.6.b.(2):
                // DR & BL only with Dust (DU), Sand (SA), or Snow (SN)
                case 'DR': $conditions .= _('Low Drifting') . ' ';
                  break;
                case 'BL': $conditions .= _('Blowing') . ' ';
                  break;
                // 12.8.6.b.(3): SH only with RA, SN, PL, GS, and GR
                // Changed to Falling since Snow Showers is stupid
                case 'SH': $conditions .= _('Falling') . ' ';
              }

              $conditions .= _('Snow') . ' ';
              break;

            case 'SG': $conditions .= _('Snow Grains') . ' ';
              break;
            case 'IC': $conditions .= _('Ice Crystals') . ' ';
              break;
            case 'PL':
              $conditions .= _('Ice Pellets') . ' ';
              // Check for Valid Weather Phenomenoa Qualifier for Snow
              switch ($weather['descriptor']) {
                // 12.8.6.b.(3): SH only with RA, SN, PL, GS, and GR
                // Changed to Falling since Snow Showers is stupid
                case 'SH': $conditions .= _('Showers') . ' ';
              }

              break;

            case 'GR':
              $conditions .= _('Hail') . ' ';
              // Check for Valid Weather Phenomenoa Qualifier for Snow
              switch ($weather['descriptor']) {
                // 12.8.6.b.(3): SH only with RA, SN, PL, GS, and GR
                // Changed to Falling since Snow Showers is stupid
                case 'SH': $conditions .= _('Showers') . ' ';
              }

              break;

            case 'GS':
              $conditions .= _('Small Hail') . ' ';
              // Check for Valid Weather Phenomenoa Qualifier for Snow
              switch ($weather['descriptor']) {
                // 12.8.6.b.(3): SH only with RA, SN, PL, GS, and GR
                // Changed to Falling since Snow Showers is stupid
                case 'SH': $conditions .= _('Showers') . ' ';
              }
              break;

            case 'UP': $conditions .= _('Unknown Precipitation') . ' ';
              break;
          }
        }

        switch ($weather['obscuration']) {
          case 'BR': $conditions .= _('Mist') . ' ';
            break;
          case 'FG':
            // Check for Valid Weather Phenomenoa Qualifier for Fog
            switch ($weather['descriptor']) {
              // 12.8.6.b.(1): MI/PR/BC only with Fog (FG)
              case 'MI': $conditions .= _('Shallow (Ground)') . ' ';
                break;
              case 'PR': $conditions .= _('Partial') . ' ';
                break;
              case 'BC': $conditions .= _('Patches') . ' ';
                break;
              // 12.8.6.b.(5): FZ can only be designated with FG, DZ, RA
              case 'FZ': $conditions .= _('Freezing') . ' ';
                break;
            }
            $conditions .= _('Fog') . ' ';
            break;

          case 'FU': $conditions .= _('Smoke') . ' ';
            break;
          case 'VA': $conditions .= _('Volcanic Ash') . ' ';
            break;
          case 'DU':
            // Check for Valid Weather Phenomenoa Qualifier for Snow
            switch ($weather['descriptor']) {
              // 12.8.6.b.(2):
              // DR & BL only with Dust (DU), Sand (SA), or Snow (SN)
              case 'DR': $conditions .= _('Low Drifting') . ' ';
                break;
              case 'BL': $conditions .= _('Blowing') . ' ';
                break;
            }

            $conditions .= _('Dust') . ' ';
            break;

          case 'SA':
            // Check for Valid Weather Phenomenoa Qualifier for Snow
            switch ($weather['descriptor']) {
              // 12.8.6.b.(2):
              // DR & BL only with Dust (DU), Sand (SA), or Snow (SN)
              case 'DR': $conditions .= _('Low Drifting') . ' ';
                break;
              case 'BL': $conditions .= _('Blowing') . ' ';
                break;
            }

            $conditions .= _('Sand') . ' ';
            break;

          case 'HZ': $conditions .= _('Haze') . ' ';
            break;
          // Must have a descriptor of BL as per 12.8.6.d.(3)
          case 'PY':
            // 12.8.6.d.(3):
            // Spray (PY) must use the descriptor of BL
            if ($weather['descriptor'] = 'BL') {
              $conditions .= _('Blowing') . ' ';
              $conditions .= _('Spray') . ' ';
            }
            break;
        }

        switch ($weather['other']) {
          case 'PO': $conditions .= _('Well Developed Dust/Sand Whirls') . ' ';
            break;
          case 'SQ': $conditions .= _('Squalls') . ' ';
            break;
          // 12.6.8.e.(2):
          // Tornados are coded as +FC, and Funnel Clouds are only FC
          case 'FC':
            if ($weather['intensity'] = '+') {
              $conditions .= _('Tornado') . ' ';
            }else {
              $conditions .= _('Funnel Cloud') . ' ';
            }
            break;

          case 'SS': $conditions .= _('Sandstorm') . ' ';
            break;
          case 'DS': $conditions .= _('Duststorm') . ' ';
            break;
        }
        // Check for Thunderstorm
        switch ($weather['descriptor']) {
          // 12.8.6.b.(4):
          // TS can be designated alone or with single or multiple
          // precipitation codes;
          case 'TS':
            if (strlen(trim($weather['precipitation'] . $weather['obscuration'] . $weather['other'])) > 0) {
              $conditions = substr($conditions, 0, strlen(trim($conditions))-1);
              $conditions .= ') ';
            }

            break;
        }
        // Proximity of Weather
        if ($weather['proximity'] <> '') $conditions .= _('in the vicinity') . ' ';
        // Save translated text into tag "humanized"
        $this->decoded_metar['weather'][$count]['humanized'] = $conditions;
      }
    }
    // Humanize the cloud conditions
    if (count($this->decoded_metar['clouds']) > 0) {
      foreach ($this->decoded_metar['clouds'] as $count => $cloud) {
        switch ($cloud['condition']) {
          case 'CAVOK':
            $this->decoded_metar['clouds'][$count]['humanized'] = _('Cloud and Visibility OK');
            break;
          case 'FEW':
            $this->decoded_metar['clouds'][$count]['humanized'] = _('Few Clouds');
            break;
          case 'SCT':
            $this->decoded_metar['clouds'][$count]['humanized'] = _('Scattered Clouds');
            break;
          case 'BKN':
            $this->decoded_metar['clouds'][$count]['humanized'] = _('Broken Clouds');
            break;
          case 'OVC':
            $this->decoded_metar['clouds'][$count]['humanized'] = _('Overcast');
            break;
          case 'VV':
            $this->decoded_metar['clouds'][$count]['humanized'] = _('Indefinite Ceiling');
            break;
          // SKC, CLR, NSC
          default:
            $this->decoded_metar['clouds'][$count]['humanized'] = _('Clear Skies');
            break;
        }
      }
    }
    // Determine the Icon Name to display
    $cloud = $this->decoded_metar['clouds'][count($this->decoded_metar['clouds']) - 1]['condition'];

    $condition = '';
    if (isset($this->decoded_metar['weather'])) {
      // Check for Thunderstorm Activity (overrides all other conditions)
      if ($this->decoded_metar['weather'][0]['descriptor'] == 'TS') {
        $condition = $this->decoded_metar['weather'][0]['descriptor'];
      }else {
        $condition = $this->decoded_metar['weather'][0]['intensity'] . $this->decoded_metar['weather'][0]['obscuration'] . substr($this->decoded_metar['weather'][0]['precipitation'], 0, 2);
      }
    }

    $this->decoded_metar['icon_name'] = $cloud . $condition;
    // Ensure we have and icon selected.  If not, default to CLR
    if (strlen(trim($this->decoded_metar['icon_name'])) == 0) {
      $this->decoded_metar['icon_name'] = 'CLR';
    }
  }

  function show_plus ($value) {
    if (intval($value) > 0) {
      return "+";
    }else {
      return "";
    }
  }

  function show_gmt ($value) {
    $gmt = $this->show_plus ($value);
    if (intval($value) != 0) {
      $gmt .= intval($value);
    }
    return $gmt;
  }

  function get_temp () {
    return number_format($this->decoded_metar['temperature']['temp_' . strtolower($this->units['temp'])], 0) .
    _('&deg;' . $this->units['temp']);
  }

  function get_windchill () {
    $values = number_format($this->decoded_metar['windchill']['windchill_' . strtolower($this->units['temp'])], 0) .
    _('&deg;' . $this->units['temp']);

    if (($this->decoded_metar['temperature']['temp_f'] < 51 && $this->decoded_metar['wind']['mph'] > 3)) {
      return $values;
    }
  }

  function get_heatindex () {
    global $WI;

    $values = number_format($this->decoded_metar['heatindex']['heatindex_' . strtolower($this->units['temp'])], 0) .
    _('&deg;' . $this->units['temp']);

    if (($this->decoded_metar['temperature']['temp_f'] >= 80 && !empty($this->decoded_metar['rel_humidity']))) {
      return $values;
    }
  }

  function get_humidex () {
    $values = number_format($this->decoded_metar['humidex']['humidex_' . strtolower($this->units['temp'])], 0) .
    _('&deg;' . $this->units['temp']);

    if (($this->decoded_metar['temperature']['temp_f'] > 79 && $this->decoded_metar['rel_humidity'] > 39)) {
      return $values;
    }
  }

  function get_humidity() {
    if ((!empty($this->decoded_metar['rel_humidity']))) {
      return $this->decoded_metar['rel_humidity'] . _('%');
    }
  }

  function get_wind () {
    $gustingto = '';
    $gust_value = '';
    $gust_units = '';
    if (isset($this->decoded_metar['wind']['gust'])) {
      $gustingto = _('gusting to');
      $gust_value = number_format($this->decoded_metar['wind']['gust'][strtolower($this->units['wind'])], 0);
      $gust_units = _($this->units['wind']);
    }

    if (number_format($this->decoded_metar['wind'][strtolower($this->units['wind'])], 0) > 0) {
      $values = $this->decoded_metar['wind']['dir'] . ' ' .
      _('at') . ' ' .
      number_format($this->decoded_metar['wind'][strtolower($this->units['wind'])], 0) . ' ' .
      _($this->units['wind']) . ' ' . $gustingto . ' ' . $gust_value . ' ' . $gust_units;
    }else {
      $values = _('Calm');
    }

    return $values;
  }

  function get_dewpoint () {
    if ((!empty($this->decoded_metar['temperature']['dew_' . strtolower($this->units['temp'])]))) {
      return number_format($this->decoded_metar['temperature']['dew_' . strtolower($this->units['temp'])], 0) .
      _('&deg;' . $this->units['temp']) ;
    }
  }

  function get_altimeter () {
    if ((!empty($this->decoded_metar['altimeter'][strtolower($this->units['pressure'])]))) {
      return $this->decoded_metar['altimeter'][strtolower($this->units['pressure'])] . ' ' .
      _($this->units['pressure']);
    }
  }

  function get_conditions_loop() {
    $conditions = '';
    if (isset($this->decoded_metar['weather']) && (count($this->decoded_metar['weather']) > 0)) {
      foreach ($this->decoded_metar['weather'] as $count => $weather) {
        if ($count <> 0) $conditions .= _('and') . ' ';
        $conditions .= $weather['humanized'];
      }
    }

    return $conditions;
  }

  function get_conditions () {
    $conditions = $this->get_conditions_loop();

    if (strlen(trim($conditions)) > 0) {
      return $conditions;
    }
  }

  function get_visibility () {
    if ((!empty($this->decoded_metar['visibility'][0][strtolower($this->units['visibility'])]))) {
      return number_format($this->decoded_metar['visibility'][0][strtolower($this->units['visibility'])], 0) . ' ' . _($this->units['visibility']);
    }
  }

  function get_simple_clouds() {
    $clouds = $this->decoded_metar['clouds'][count($this->decoded_metar['clouds'])-1]['humanized'];

    if (strlen(trim($clouds)) > 0)
      return $clouds;
  }

  function get_detailed_clouds () {
    $clouds = "";
    if (count($this->decoded_metar['clouds']) > 0) {
      foreach ($this->decoded_metar['clouds'] as $count => $cloud) {
        if ($count <> 0) $clouds .= _('and') . ' ';

        $clouds .= $cloud['humanized'] . ' ';

        if (!empty($cloud['cumulus'])) {
          switch ($cloud['cumulus']) {
            case 'CB': $clouds .= '(' . _('cumulonimbus') . ') ';
              break;
            case 'TCU': $clouds .= '(' . _('towering cumulus') . ') ';
              break;
          }
        }

        if ($cloud['condition'] != 'CAVOK' && $cloud['condition'] != 'SKC' && $cloud['condition'] != 'CLR' && $cloud['condition'] != 'NSC' && $cloud['condition'] != 'VV') {
          if ($cloud[strtolower($this->units['clouds'])] != 'nil') {
            $clouds .= _('at') . ' ' . $cloud[strtolower($this->units['clouds'])] . $this->units['clouds'] . ' ';
          }else {
            $clouds .= _('with unknown cloud base') . ' ';
          }
        }else if ($cloud['condition'] == 'VV') {
          $clouds .= '(' . _('vertical visibility') . ': ' . $cloud[strtolower($this->units['clouds'])] . $this->units['clouds'] . ') ';
        }
      }
    }

    if (strlen(trim($clouds)) > 0)
      return $clouds;
  }

  function get_icon() {
    include('icons/icon.inc');
    return $icon_map[ $this->decoded_metar['icon_name'] ]['icon'] . '.png';
  }

  function get_status() {
    $conditions = trim($this->get_conditions_loop());

    if (strlen($conditions) > 0) {
      return $conditions;
    }else {
      if (count($this->decoded_metar['clouds']) > 0) {
        return $this->decoded_metar['clouds'][count($this->decoded_metar['clouds'])-1]['humanized'];
      }

      return '';
    }
  }
}
?>
