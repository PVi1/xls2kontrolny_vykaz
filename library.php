<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Cell;

class MyReadFilter implements IReadFilter
{
    public $columns = ['B','C','N','S','T'];
    public $columns_computed = ['S','T'];
    public function readCell($column, $row, $worksheetName = '')
    {
        if ($row == 8 and $column == 'D') {
            return true;
        } elseif ($row >= 19 && $row <= 46) {
            if (in_array($column, $this->columns)) {
                return true;
            }
        } else {
            return false;
        }
    }
}

function openfile($path)
{
    $inputFileName = $path;
    echo('Spracuvam subor: ' . pathinfo($inputFileName, PATHINFO_BASENAME) . '<br />');

    try {
        $sheetname = 'List_zákazníka';
        $inputFileType = IOFactory::identify($inputFileName);
        $reader = IOFactory::createReader($inputFileType);
        $reader->setLoadSheetsOnly($sheetname);
        $filter = new MyReadFilter();
        $reader->setReadFilter($filter);
        $spreadsheet = $reader->load($inputFileName);
        $worksheet = $spreadsheet->getActiveSheet();

        $faktura = $spreadsheet->getActiveSheet()->getCell('D8')->getValue();


        //1. suma Delenie mat.  celej faktury
        $delenie = 0;
        $totals = array();
        $last_pcn = 0;
        for ($i=18;$i<=46;$i++) {
            //spocitam sumu za delenie
            if (stripos(trim($worksheet->getCell('B'.$i)->getValue()), "delenie") !== false) {
                $delenie += round($worksheet->getCell('T'.$i)->getOldCalculatedValue(), 2);
            }
            $new_pcn = trim($worksheet->getCell('C'.$i)->getValue());
            if ($new_pcn == "" && stripos(trim($worksheet->getCell('B'.$i)->getValue()), "delenie") !== false && $last_pcn != 0) {
                $totals[$last_pcn]['sum'] += round($worksheet->getCell('T'.$i)->getOldCalculatedValue(), 2);
            } else if($new_pcn>0){
                $totals[$new_pcn]['sum'] += round($worksheet->getCell('T'.$i)->getOldCalculatedValue(), 2);
                $totals[$new_pcn]['mnozstvo'] += round($worksheet->getCell('S'.$i)->getOldCalculatedValue(), 2);
            }
            $last_pcn = trim($worksheet->getCell('C'.$i)->getValue());
        }
        echo "<br />Faktura: ".$faktura;
        echo "<br />Suma za delenie: ".$delenie;
        echo "<br /><br />Statistiky:<br />";        
        if (count($totals)>0) {
            foreach ($totals as $kat=>$val) {
                if ($kat>0) {
                    echo 'Kategoria:'.$kat.'<br />';
                    echo 'Sum: '.$val['sum'].'<br />';
                    echo 'Mnozstvo: '.$val['mnozstvo'].'<br /><br />';
                }
            }
        } else {
            echo "V tejto fakture sa nenachadzaju ziadne polozky s PCN.";
        }

        echo '<hr><br />';
        return true;
    } catch (InvalidArgumentException $e) {
        echo('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        return false;
    }
}

function extract_zip($fullpath)
{
    $zip = new ZipArchive;
    if ($zip->open($fullpath) === true) {
        $zip->extractTo('input/');
        $zip->close();
        echo 'Subory boli uspesne extrahovane<br />';
        return true;
    } else {
        die('Cannot open file'.$fullpath.' Zip extract failed');
    }
}

function process_fa($dir)
{
    if ($files = scandir($dir)) {
        if (sort($files)) {
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    $filetype = strtolower(pathinfo($dir.$file, PATHINFO_EXTENSION));
                    if ($filetype == "xls") {
                        if (openfile($dir . $file)) {
                            if (is_file($dir . $file)) {
                                unlink($dir . $file);
                            }
                        }
                    }
                }
            }
            return true;
        }
    }
}
