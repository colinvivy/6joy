<?php
function cfg2ini($cfg) {
    $lines = array();
    foreach ($cfg as $k=>$v) {
        if (!is_array($v)) {
            $lines[] = str_pad($k, 10)."= ".str_replace("\n", '\n', $v);
        } else {
            $lines[] = "\r\n[".$k."]";
            foreach ($v as $kk=>$vv) {
                foreach ($vv as $kkk=>$vvv) {
                    $lines[] = str_pad($kkk."[]", 10)."= ".str_replace("\n", '\n', $vvv);
                }
                $lines[] = "";
            }
        }
    }

    return join("\r\n", $lines);
}

function ini2cfg($ini) {
    $tmpname = tempnam(PATH_DATA."/tmp", "ini_");
    file_put_contents($tmpname, $ini);
    $struct = parse_ini_file($tmpname, true);
    unlink($tmpname);

    if (!isset($struct['plot']['pos'])) {
        return false;
    }

    $new_plot = array();
    foreach ($struct['plot']['pos'] as $k=>$v) {
        $new_plot[$k] = array();
        foreach ($struct['plot'] as $kk=>$vv) {
            $new_plot[$k][$kk] = $vv[$k];
        }
    }
    $struct['plot'] = $new_plot;

    return $struct;
}
