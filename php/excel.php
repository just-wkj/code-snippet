<?php
/**
 * excel 导出处理
 * @author     :wkj
 * @createTime :2018/7/31 13:55
 * @description: 需要引用phpspreadsheet类库 `composer require phpoffice/phpspreadsheet`
 */

namespace app\util;


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Excel{

    /**
     *  数组下标转转化成EXCEL的列表
     * @author:wkj
     * @date 2018/7/31 15:06
     * @param int $num
     * @return string
     */
    private static function translateNum2Letters($num = 0){
        $letters = '';
        if (floor($num / 26)) {
            $letters = chr(floor($num / 26) - 1 + 65);
        }
        $letters = $letters . chr($num % 26 + 65);

        return $letters;
    }

    /**
     *  导出excel
     * @author:wkj
     * @date  2018/7/31 15:23
     * @param string $fileName
     * @param array  $data 二维数组
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function  export($fileName='导出', array $data = []){
        $spreadsheet = new Spreadsheet();
        foreach ($data as $row => $item) {
            $item = array_values($item);//变成数字连续索引数组
            foreach ($item as $key => $vo) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue(self::translateNum2Letters($key) . ($row + 1), $vo);
            }
        }

        $spreadsheet->getActiveSheet()->setTitle('sheet');
        $spreadsheet->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileName.'.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
