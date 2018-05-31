<?php
/**
 * @author     :wkj
 * @createTime :2017/12/1 14:12
 * @description:
 */

namespace App\Services;


use App\Helpers\Variables;
use App\Objects\UserSession;
use Illuminate\Http\Request;

class ReportService{
    private $userSession;
    private $request;
    static $userSessionStatic;

    //region 时间类型定义
    const DATE_DAY_TYPE  = 1;
    const DATE_WEEK_TYPE  = 2;
    const DATE_MONTH_TYPE  = 3;
    const DATE_USER_DEFINED_TYPE = 4;
    //endregion

    //region 排名标志
    const RANKING_MARK_UP = '+';
    const RANKING_MARK_DOWN = '-';
    const RANKING_MARK_EQUAL = '→';
    const RANKING_MARK_NEW = '/';
    //endregion

    private static $formatDate = 'Y-m-d';
    private static $isCurrentMonth; //是否是当前月份,用于计算某日期所在月份

    public function __construct(UserSession $userSession, Request $request) {
        $this->userSession = $userSession;
        $this->request = $request;
        self::$userSessionStatic = $this->userSession;
    }

    //region 获取所有的来源

    /**
     *  获取所有来源
     * @author:wkj
     * @date  2017/12/1 14:18
     * @param int $portal_id
     * @return array
     */
    public static function allFromMap($portal_id = 0){
        if ($portal_id) {
            $alFrom = \City::getLocal()->all_from->filterByPortal($portal_id)->getIdNameMapping();
        } else {
            $alFrom = \City::getLocal()->all_from->getIdNameMapping();
        }

        return $alFrom->toArray();
    }
    //endregion

    //region 指定日期获取对应的时间范围
    /**
     *  获取获取指定是日期的时间范围
     * @author:wkj
     * @date  2017/12/1 15:32
     * @param      $startDate
     * @param      $dateType 1日 2周 3月 4自定义
     * @param null $endDate
     * @param bool $isTimestamp
     * @return array
     */
    public static function getStartAndEndTime($startDate, $dateType, $endDate = null, $isTimestamp=false){
        if(!$startDate){
            return [];
        }
        switch ($dateType) {
            case self::DATE_DAY_TYPE:
                $dateArr = self::getRelativeCurrentDay($startDate);
                break;
            case self::DATE_WEEK_TYPE:
                $dateArr = self::getRelativeCurrentWeek($startDate);
                break;
            case self::DATE_MONTH_TYPE:
                $dateArr = self::getRelativeCurrentMonth($startDate);
                break;
            case self::DATE_USER_DEFINED_TYPE:
                $dateArr = self::getUserDate($startDate, $endDate);
                break;
            default:
                $dateArr = self::getRelativeCurrentDay($startDate);
                break;
        }

        return $isTimestamp ? $dateArr : array_map(function ($date){
            return date(self::$formatDate, $date);
        }, $dateArr);
    }

    /**
     *  获取获取指定是日期的时间范围的上一个日期
     * @author:wkj
     * @date 2017/12/4 16:16
     * @param      $startDate
     * @param      $dateType
     * @param null $endDate
     * @param bool $isTimestamp
     * @return array
     */
    public static function getLastStartAndEndTime($startDate, $dateType, $endDate = null, $isTimestamp = false){
        self::$isCurrentMonth = date('Y-m', strtotime($startDate)) == date('Y-m');
        if (!$startDate) {
            return [];
        }
        $lastStartDate = $lastEndDate = '';
        switch ($dateType) {
            case self::DATE_DAY_TYPE:
                $lastStartDate = date(self::$formatDate, strtotime('-1 days', strtotime($startDate)));
                break;
            case self::DATE_WEEK_TYPE:
                $lastStartDate = date(self::$formatDate, strtotime('-1 weeks', strtotime($startDate)));
                break;
            case self::DATE_MONTH_TYPE:
                $lastStartDate = date(self::$formatDate, strtotime('-1 months', strtotime($startDate)));
                break;
            case self::DATE_USER_DEFINED_TYPE:
                $lastEndDate = date(self::$formatDate, strtotime('-1 days', strtotime($startDate)));
                $days = (strtotime($endDate) - strtotime($startDate)) / 86400;
                $lastStartDate = date(self::$formatDate, strtotime('-' . $days . ' days', strtotime($lastEndDate)));
                break;
            default:
                $lastStartDate = date(self::$formatDate, strtotime('-1 days', strtotime($startDate)));
                break;
        }

        return self::getStartAndEndTime($lastStartDate, $dateType, $lastEndDate, $isTimestamp);
    }

    /**
     *  获取给定日期的当天开始和结束时间
     * @author:wkj
     * @date  2017/12/1 15:33
     * @param $date
     * @return array
     */
    private static function getRelativeCurrentDay($date){
        $startTimestamp = strtotime(date(self::$formatDate, strtotime($date)));
        $endTimestamp = $startTimestamp + 86399;

        return [$startTimestamp, $endTimestamp];
    }

    /**
     *获取给定日期的当周开始和结束时间
     * @author:wkj
     * @date  2017/12/1 15:36
     * @param $date
     * @return array
     */
    private static function getRelativeCurrentWeek($date){
        $timestamp = strtotime($date);
        $startTimestamp = strtotime('-6 days',strtotime($date));
        $endTimestamp = $timestamp + 86400 - 1;

        return [$startTimestamp, $endTimestamp];
    }

    /**
     *  获取给定日期的当月开始和结束时间
     * @author:wkj
     * @date  2017/12/1 15:36
     * @param      $date
     * @return array
     */
    private static function getRelativeCurrentMonth($date){
        $timestamp = strtotime($date);
        $startTimestamp = strtotime(date('Y-m-01', $timestamp));
        if (self::$isCurrentMonth || date('Y-m', $timestamp) == date('Y-m')) {//当前月截止到当天
            $endTimestamp = strtotime(date(self::$formatDate, $timestamp)) + 86399;
        } else {//过去月,整月
            $endTimestamp = strtotime(date('Y-m-t', $timestamp)) + 86399;
        }

        return [$startTimestamp, $endTimestamp];
    }

    /**
     *  获取给定日期的当天开始和结束时间
     * @author:wkj
     * @date  2017/12/1 15:36
     * @param $startDate
     * @param $endDate
     * @return array
     */
    private static function getUserDate($startDate, $endDate){
        //日期不存在设置默认日期当天
        $defaultDate = date('Y-m-d');
        if(!$startDate){
            $startDate = $defaultDate;
        }
        $startTimestamp = strtotime($startDate);
        if (!$endDate) {
            $endDate = $defaultDate;
        }
        $endTimestamp = strtotime($endDate) + 86399;

        return [$startTimestamp, $endTimestamp];
    }
    //endregion

    public static function sortSerialize($serialize){
        return self::sortDimension(self::arrayToDimension(self::unserialize($serialize)));
    }

    public static function unserialize($serialize){
        if (!$serialize) {
            return [];
        }

        return Variables::unserialize($serialize);
    }

    public static function arrayToDimension(array $array){
        $return = [];
        foreach ($array as $key => $value) {
            $return[] = [
                'key'   => $key,
                'value' => $value
            ];
        }

        return $return;
    }

    public static function sortDimension($array, $sortField = 'value', $desc = 1){
        $sort = $desc ? SORT_DESC : SORT_ASC;
        array_multisort(array_column($array, $sortField), $sort, $array);

        return $array;
    }

    /**
     *  一维数组排序 返回二位数组  传入:[$key => $value,]
     * @author:wkj
     * @date 2017/12/5 18:23
     * @param $array
     * @return array 输出:[
     *                  $key => [
     *                      'sortId' => $sortId,
     *                      'key' => $key,
     *                      'value' => $value
     *                  ],
     *              ]
     */
    public static function sortNumAndAddSortId($array){
        $arrayNew = $array;
        rsort($arrayNew);
        $arrayNew = array_flip(array_values(array_unique($arrayNew)));
        $paiming = [];
        foreach ($arrayNew as $num => $sortId){
            $sortId++;
            foreach ($array as $from => $fromNum){
                if($num == $fromNum){
                    $paiming[$from] = [
                        'sortId' => $sortId,
                        'key' => $from,
                        'value' => $fromNum
                    ];
                }
            }
        }
        return $paiming;
    }


    /**
     *  数据排名变化
     * @author:wkj
     * @date  2017/12/5 19:42
     * @param        $array 新数据
     * @param        $arrayOld  旧数据
     * @param string $index  排名索引默认sortId
     * @return mixed
     *
     * 入参格式: [
     *           $key => [
     *                      'sortId' => $sortId,
     *                      'key' => $key,
     *                      'value' => $value
     *                  ],
     *              ]
     * 返回格式: [
     *                  $key => [
     *                      'sortId' => $sortId,
     *                      'key' => $key,
     *                      'value' => $value,
     *                      'mark' => +-/→,
     *                      'diff' => 排名变化数值,
     *                  ],
     *              ]
     */
    public static function paiMingRatio($array, $arrayOld, $index='sortId'){
        foreach ($array as $key => $vo) {
            if (isset($arrayOld[$key])) {//新旧数据都存在
                $diff = $arrayOld[$key][$index] - $vo[$index] ;
                if ($diff == 0) {
                    $mark = self::RANKING_MARK_EQUAL;
                } else if ($diff < 0) {
                    $mark = self::RANKING_MARK_DOWN;
                } else {
                    $mark = self::RANKING_MARK_UP;
                }
                $diff = abs($diff);
            } else {
                $diff = 0;
                $mark = self::RANKING_MARK_NEW;
            }
            $array[$key]['mark'] = $mark;
            $array[$key]['diff'] = $diff;
        }
        return $array;
    }

    /**
     *  环比计算
     * @author:wkj
     * @date  2017/12/7 17:14
     * @param        $array    新的数据
     * @param        $arrayOld 旧的数据
     * @param string $index    环比计算的索引 默认count
     * @param bool   $dimension 环比是否返回二维数组(默认否) 否:[a=>1,b=>22] =====> [a=>1,b=>22,mark=>+,ratio=>2]; 是: [a=>1,b=>22] ===>[a=>1,b=>22,dimensionIndex=>[mark=>+,ratio=>2]]
     * @param string $dimensionIndex 生成二维数组时候设置的键值
     * @return mixed
     *                         入参格式: [
     *                         $key => [
     *                         'count' => $count,
     *                         'key' => $key,
     *                         'value' => $value
     *                         ],
     *                         ]
     *                         返回格式1: [
     *                         $key => [
     *                             'count' => $count,
     *                             'key' => $key,
     *                             'value' => $value,
     *                             'mark' => +-/→,
     *                             'ratio' => 环比变化数值,
     *                            ],
     *                         ]
     *                        返回格式2: [
     *                            $key => [
     *                              'count' => $count,
     *                              'key' => $key,
     *                                 'value' => $value,
     *                                  $dimensionIndex =>[
     *                                      'mark' => +-/→,
     *                                      'ratio' => 环比变化数值,
     *                                  ]
     *                              ],
     *                          ]
     */
    public static function ratio($array, $arrayOld, $index = 'count', $dimension = false, $dimensionIndex = 'ratio'){
        foreach ($array as $key => $vo) {
            $count = $vo[$index] > 0 ? (int)$vo[$index] : 0;
            if (isset($arrayOld[$key])) {
                $countOld = $arrayOld[$key][$index] > 0 ? (int)$arrayOld[$key][$index] : 0;
                $ratio = $countOld > 0 ? round(($count - $countOld) / $countOld * 100, 2) : 0;
                if ($ratio == 0) {
                    $mark = self::RANKING_MARK_EQUAL;
                } else if ($ratio < 0) {
                    $mark = self::RANKING_MARK_DOWN;
                } else {
                    $mark = self::RANKING_MARK_UP;
                }
                $ratio = abs($ratio);
            } else {
                $mark = self::RANKING_MARK_NEW;
                $ratio = '';
            }
            $ratio = $ratio ? : '';
            if ($dimension && $dimensionIndex) {
                $array[$key][$dimensionIndex] = [
                    'mark'  => $mark,
                    'ratio' => $ratio
                ];
            } else {
                $array[$key]['mark'] = $mark;
                $array[$key]['ratio'] = $ratio;
            }

        }
        return $array;
    }

    /**
     *  一维数组环比计算
     * @author:wkj
     * @date  2017/12/8 16:56
     * @param        $array          新的数据
     * @param        $arrayOld       旧的数据
     * @param string $index          环比计算的键值
     * @param bool   $dimension      环比是否返回二维数组(默认否)
     * @param string $dimensionIndex 环比返回二维数组键值
     * @return mixed
     */
    public static function ratioOneDimension($array, $arrayOld, $index = 'count', $dimension = false, $dimensionIndex = 'ratio'){
        $count = $array[$index] > 0 ? (int)$array[$index] : 0;
        if (isset($arrayOld[$index])) {
            $countOld = $arrayOld[$index] > 0 ? (int)$arrayOld[$index] : 0;
            $ratio = $countOld > 0 ? round(($count - $countOld) / $countOld * 100, 2) : 0;
            if ($ratio == 0) {
                $mark = self::RANKING_MARK_EQUAL;
            } else if ($ratio < 0) {
                $mark = self::RANKING_MARK_DOWN;
            } else {
                $mark = self::RANKING_MARK_UP;
            }
            $ratio = abs($ratio);
        } else {
            $mark = self::RANKING_MARK_NEW;
            $ratio = '';
        }
        $ratio = $ratio ? : '';
        if ($dimension && $dimensionIndex) {
            $array[$dimensionIndex] = [
                'mark'  => $mark,
                'ratio' => $ratio
            ];
        } else {
            $array['mark'] = $mark;
            $array['ratio'] = $ratio;
        }

        return $array;
    }

    /**
     *  二维数组环比计算 ratio别名
     * @author:wkj
     * @date 2017/12/8 16:52
     * @param        $array
     * @param        $arrayOld
     * @param string $index
     * @param bool   $dimension
     * @param string $dimensionIndex
     * @return mixed
     */
    public static function ratioDimension($array, $arrayOld, $index = 'count', $dimension = false, $dimensionIndex = 'ratio'){
        return self::ratio($array, $arrayOld, $index, $dimension, $dimensionIndex);
    }


    /**
     *  二位数组根据某个字段$sortField 排序并且新增排序索引$newIndex
     * @author:wkj
     * @date 2017/12/12 14:33
     * @param        $array
     * @param string $sortField
     * @param int    $desc
     * @param string $newIndex
     * @return mixed
     */
    public static function sortDimensionAddId($array, $sortField = 'value', $desc = 1, $newIndex = 'id'){
        $array = self::sortDimension($array, $sortField, $desc);
        $dataId = array_flip(array_map(function($item){
            return strval($item);
        },array_unique(array_column($array, $sortField))));
        foreach ($array as $key => $value) {
            $array[$key][$newIndex] = $dataId[strval($value[$sortField])] + 1;
        }

        return $array;
    }

    /**
     *  排名百分比
     * @author:wkj
     * @date 2017/12/11 15:20
     * @param $newData1
     * @param $newData2
     * @return array
     */
    public static function getPercentRange($newData1, $newData2){
        $return = [];
        foreach ($newData1 as $key => $vo) {
            $mark = $per = '';
            if (isset($newData2[$key])) {
                $oldValue = intval($newData2[$key]);
                $per = ($vo - $oldValue) / $oldValue * 100;
                if ($per > 0) {
                    $mark = '+';
                } else if ($per < 0) {
                    $mark = '-';
                } else {
                    $mark = '';
                }

                $per = round(abs($per), 2);
                $return[] = [
                    'key'      => $key,
                    'value'    => $vo,
                    'oldValue' => $oldValue,
                    'mark'     => $mark,
                    'percent'  => $per
                ];
            } else {

                $return[] = [
                    'key'      => $key,
                    'value'    => $vo,
                    'oldValue' => 0,
                    'mark'     => $mark,
                    'percent'  => $per
                ];
            }
        }

        return $return;
    }

    /**
     *  二维数组某一列累加
     * @author:wkj
     * @date  2017/12/11 14:29
     * @param array $array
     * @param       $index
     * @return float|int
     */
    public static function arraySum(array $array, $index){
        return array_sum(array_column($array, $index));
    }

    /**
     *  二维数组某列最大值
     * @author:wkj
     * @date  2017/12/11 14:47
     * @param array $array
     * @param       $index
     * @return mixed
     */
    public static function arrayMax(array $array, $index){
        return max(array_column($array, $index));
    }

    /**
     *  获取获取指定是日期的时间范围
     * @author:wkj
     * @date  2017/12/1 15:32
     * @param      $startDate 开始日期
     * @param      $dateType  1日 2周 3月 4自定义
     * @param null $endDate   结束日期
     * @return string
     */
    public static function getTableDateSuffix($startDate, $dateType, $endDate = null){
        $dateArr = self::getStartAndEndTime($startDate, $dateType, $endDate);
        $dateMap = [
            self::DATE_DAY_TYPE          => '日报',
            self::DATE_WEEK_TYPE         => '周报',
            self::DATE_MONTH_TYPE        => '月报',
            self::DATE_USER_DEFINED_TYPE => '自定义报',
        ];
        $suffixArr = [];
        $suffixArr[] = $dateMap[$dateType] . ' ';
        $suffixArr[] = $dateArr[0] . '日';
        $suffixArr[] = ' - ' . $dateArr[1] . '日';
        if ($dateType == self::DATE_DAY_TYPE) {
            unset($suffixArr[2]);
        }

        return implode('', $suffixArr);
    }

    /**
     *  获取条口名称
     * @author:wkj
     * @date 2017/12/11 15:48
     * @param $portal_id
     * @return string
     */
    public static function getTablePortalSuffix($portal_id){
        $portalMap = array_column(\Conf::getPortal()->toArray(), 'name', 'id');

        return isset($portalMap[$portal_id]) ? $portalMap[$portal_id] : '';
    }

    /**
     *  根据query生成日期和端口的表后缀
     * @author:wkj
     * @date  2017/12/11 15:53
     * @param $querys
     * @return array
     */
    public static function getDateAndPortalSuffixByQuerys($querys){
        if (!$querys) {
            return [
                'date'   => '',
                'portal' => '',
            ];
        }
        $search = [];
        foreach ($querys as $vo) {
            $value = $vo['value'];
            switch ($vo['name']) {
                case "begin_time":
                    $search['begin_time'] = $value;
                    break;
                case "end_time":
                    $search['end_time'] = $value;
                    break;
                case "type":
                    $search['type'] = (int)$value;
                    break;
                case "portal":
                    $search['portal'] = (int)$value;
                    break;
            }
        }
        $dateType = (int)$search['type'];
        $begin_time = $search['begin_time'];
        $end_time = $search['end_time'];
        //搜索条件不带条口的时候使用当前的条口
        if (!isset($search['portal'])) {
            $search['portal'] = self::$userSessionStatic->portal_id;;
        }
        $portal = $search['portal'];
        $return['date'] = self::getTableDateSuffix($begin_time, $dateType, $end_time);
        $return['portal'] = self::getTablePortalSuffix($portal);

        return $return;
    }

    /**
     *  一维数组累加或者新增 [2 => 10, 3 => 1] , [1 => 2, 2 => 2]  =====>  [2 => 12, 3 => 1 1 => 2]
     * @author:wkj
     * @date  2017/12/26 9:36
     * @param array $origin 原始数据
     * @param array $array  新增数据
     * @return array
     */
    public static function array_sum_or_create(array $origin, array $array){
        foreach ($array as $key => $vo) {
            !isset($origin[$key]) && $origin[$key] = 0;
            $origin[$key] += $vo;
        }

        return $origin;
    }

    /**
     *  生成echarts所需数据
     * @author:wkj
     * @date 2018/4/16 11:41
     * @param $data
     * @return array
     */
    public static function buildEchartsData($data){
        $legendOrAxis = [];//图例或者横坐标数组
        $graphData = [];  //图表数据
        foreach ($data as $key => $vo){
            $legendOrAxis[] = $vo['name'];
            $graphData[] = [
                'name'  => $vo['name'],
                'value' => (int)$vo['count'],
            ];
        }
        return [$legendOrAxis, $graphData];
    }

    /**
     *  标题处理
     * @author:wkj
     * @date 2018/4/17 10:33
     * @param string $string
     * @param array  $date
     * @return string
     */
    public static function buildEchartsTitle($string = '', array $date){
        if ($date && is_array($date)) {
            if (is_numeric($date[0])) {
                $date[0] = date('Y-m-d', $date[0]);
                $date[1] = date('Y-m-d', $date[1]);
            }
            $string = $date[0] . '-' . $date[1] . ' ' . $string;
        }

        return $string;
    }
}
