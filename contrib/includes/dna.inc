<?php
function Tm($primer) {
  return round((Tm_cal($primer, true) + Tm_cal($primer, false)) / 2, 2);
}

function Tm_cal($primer, $min = false) {
  $primer_len = strlen($primer);
  list($n_CG, $n_AT) = CG_AT($primer, $min);
  if ($primer_len < 14) {
    return round(2 * $n_AT + 4 * $n_CG, 3);
  }else {
    return round(64.9 + 41 * (($n_CG - 16.4) / $primer_len), 3);
  }
}

function Mw($primer) {
  return round(((Mw_cal($primer, true) + Mw_cal($primer, false)) / 2) / 1000, 2);
}

function Mw_cal($primer, $min = false) {
  $dna_A_wt = 313.245;
  $dna_C_wt = 289.215;
  $dna_G_wt = 329.245;
  $dna_T_wt = 304.225;
  $water = 18.015;
  $wts = array('A' => array($dna_A_wt, $dna_A_wt), // Adenine
    'C' => array($dna_C_wt, $dna_C_wt), // Cytosine
    'G' => array($dna_G_wt, $dna_G_wt), // Guanine
    'T' => array($dna_T_wt, $dna_T_wt), // Thymine
    'M' => array($dna_C_wt, $dna_A_wt), // A or C
    'R' => array($dna_A_wt, $dna_G_wt), // A or G
    'W' => array($dna_T_wt, $dna_A_wt), // A or T
    'S' => array($dna_C_wt, $dna_G_wt), // C or G
    'Y' => array($dna_C_wt, $dna_T_wt), // C or T
    'K' => array($dna_T_wt, $dna_G_wt), // G or T
    'V' => array($dna_C_wt, $dna_G_wt), // A or C or G
    'H' => array($dna_C_wt, $dna_A_wt), // A or C or T
    'D' => array($dna_T_wt, $dna_G_wt), // A or G or T
    'B' => array($dna_C_wt, $dna_G_wt), // C or G or T
    'X' => array($dna_C_wt, $dna_G_wt), // G, A, T or C
    'N' => array($dna_C_wt, $dna_G_wt) // G, A, T or C
    );
  $mwt = 0;
  $wlimit = 0;
  $NA_len = strlen($primer);
  if ($min) $wlimit = 1;
  for ($i = 0; $i < $NA_len; $i++) {
    $NA_base = substr($primer, $i, 1);
    $mwt += $wts[$NA_base][$wlimit];
  }
  $mwt += $water;
  return $mwt;
}

function CG($primer) {
  $primer_len = strlen($primer);
  list($CG_min) = CG_AT($primer, true);
  list($CG_max) = CG_AT($primer, false);
  return (round((($CG_min + $CG_max) / 2) / $primer_len, 2) * 100);
}

function CG_AT($primer, $min = false) {
  if ($min) {
    $primer = preg_replace('/A|T|Y|R|W|K|M|D|V|H|B|N|X/', 'A', $primer);
    $primer = preg_replace('/C|G|S/', 'G', $primer);
  }else {
    $primer = preg_replace('/A|T|W/', 'A', $primer);
    $primer = preg_replace('/C|G|Y|R|S|K|M|D|V|H|B|N|X/', 'G', $primer);
  }
  return array(substr_count($primer, 'G'), substr_count($primer, 'A'));
}
?>