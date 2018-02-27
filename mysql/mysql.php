<?php
/**
 * @author     :wkj
 * @createTime :2017/12/19 15:28
 * @description:
 */

/*根据日期分组 时间戳字段

SELECT DATE_FORMAT( FROM_UNIXTIME( add_time ) ,  '%Y-%m-%d' ) AS date, COUNT( * ) AS total
FROM  `tableName`
WHERE add_time >=1509465600
AND add_time <1512057600
GROUP BY DATE_FORMAT( FROM_UNIXTIME( add_time ) ,  '%Y-%m-%d' )*/

/*
SELECT DATE_FORMAT( FROM_UNIXTIME( tr_begtime ) ,  '%Y-%m-%d' ) AS DATE, COUNT( * ) AS total
FROM  `tel_recoder`
WHERE tr_long_tel =  '4008988365'
AND tr_begtime >=1514736000
AND tr_callresult =0
GROUP BY DATE
*/
/*
* 复制表
*  insert into crm_activity_copy (select * from newcrm.crm_activity);
* */