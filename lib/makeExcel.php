<?php
session_start();
include_once("../vendor/xlsxwriter.class.php");

$filename = "robots_file_check.xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$sheet = new XLSXWriter();
$table = $_SESSION['table'];
foreach($table as $key => $data) {
    if($key === 'location') {
        $sheet->writeSheetRow('Sheet1', array("Проверяемый файл:",$data,"", ""), array('border'=>'left,right,top,bottom'));
        $sheet->writeSheetRow('Sheet1', array("Название проверки", "Статус", "Состояние", "Рекомендации"), array('border'=>'left,right,top,bottom'));
        $sheet->markMergedCell('Sheet1', $start_row=0, $start_col=1, $end_row=0, $end_col=3);        
    } else {
        $cellColor;
        if($data['status'] === 'Ок') {
            $cellColor = "#008000";
        } else {
            $cellColor = "#FF0000";
        }
        $sheet->writeSheetRow('Sheet1', array($data['name'], $data['status'], $data['state'], $data['advice']), array(array('border'=>'left,right,top,bottom'),array('border'=>'left,right,top,bottom', 'fill' => $cellColor),array('border'=>'left,right,top,bottom'),array('border'=>'left,right,top,bottom')));
    }
}
$sheet->writeToStdOut();