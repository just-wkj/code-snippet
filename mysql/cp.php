<?php
$step = 200000;
$sqlBase = 'insert into %s (select * from newcrm.%s where %s >%s AND %s <= %s );';
$sqlBaseSpecial = 'insert into %s(%s) (select %s from newcrm.%s where %s >%s AND %s <= %s );';

//$sqlBase = 'insert into %s (memid,loan_company_type,loan_pay_type,loan_work_year,loan_salary,loan_credit_nums,loan_credit_debt,loan_debt)
//                  (select memid,loan_company_type,loan_pay_type,loan_work_year,loan_salary,loan_credit_nums,loan_credit_debt,loan_debt from newcrm.%s where %s >%s AND %s <= %s );';

//$sqlBase = 'insert into %s (id,date,city,cate,loupanid,join_all_nums,join_web_nums,join_below_nums,join_web_from,join_below_from,add_time,update_time) (select id,date,city,cate,loupanid,join_all_nums,join_web_nums,join_below_nums,join_web_from,join_below_from,add_time,update_time from newcrm.%s where %s >%s AND %s <= %s );';

$config = ['crm_mem_info','mem_id',7968566,200000];
$config = ['crm_mem_extinfo','mem_id',7968566,200000];
$config = ['crm_mem_join','memid',7968566,200000];
$config = ['crm_mem_lifeinfo','memid',7968566,200000];
$config = ['crm_mem_memo','id',30929,200000];
$config = ['crm_mem_repairinfo','memid',7486140,200000];
$config = ['crm_mem_prefect','join_id',24706232,200000];
$config = ['crm_mem_lookinfo','memid',7968566,200000];

$config = ['crm_mem_other','memid',7968566,200000];
$config = ['crm_mem_loaninfo','memid',7968566,200000];
$config = ['crm_report_dloupan','id',1563758,200000,[
    'memid' => 'memid',
    'loan_company_type' => 'loanCompanyType',
]];


if (isset($config[4]) && is_array($config[4]) && $config[4]) {
    $formatParams = function($data){
        return [$data[0],implode(',',array_keys($data[4])),implode(',',array_values($data[4])),$data[0],$data[1],'%s',$data[1],'%s'];
    };
    $joinSql = function($config,$start, $end) use($formatParams,$sqlBaseSpecial){
        printf(vsprintf($sqlBaseSpecial,$formatParams($config)), $start, $end);
    };
} else {
    $formatParams = function($data){
        return [$data[0],$data[0],$data[1],'%s',$data[1],'%s'];
    };
     $joinSql = function($config,$start, $end) use($formatParams,$sqlBase){
        printf(vsprintf($sqlBase,$formatParams($config)), $start, $end);
    };
}

$total = (int)isset($config[2]) ? $config[2] : 0;
$total = $total > 0 ? $total : 0;
$step = isset($config[3]) ? $config[3] : $step;
for ($i = 0; ; $i++) {
    $start = $i * $step;
    if ($start >= $total) {
        return;
    }
    $end = ($i + 1) * $step;
    $joinSql($config,$start, $end);
    echo "\n";
}
die;