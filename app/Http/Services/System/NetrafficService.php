<?php

namespace App\Http\Services\System;
use App\Http\Services\BaseService;

/**
 * Class NetrafficService
 * @package App\Http\Services\System
 * @Author YiYuan-LIn
 * @Date: 2020/3/2
 * 流量监控
 */
class NetrafficService extends BaseService
{
    /**
     * 文件路径
     * @var string
     */
     protected $logPath = '/data/laravel-admin-template/logs/';

    /**
     * @Author YiYuan-LIn
     * @Date: 2020/3/1
     * @param $params
     * @return array
     * @description 流量监控
     */
    public function netraffic($params)
    {
        $date = $params['date'];
        $time_interval = $params['time_interval'];
        $time_interval = $time_interval * 60;
        $website = $params['website'];

        //获取日志内容并处理
        $fileContent = file_get_contents($this->logPath . date('Ymd', strtotime($date)) . '/' . $website);
        $fileContent = explode("\r\n", $fileContent);

        $date_netraffic = [];
        foreach ($fileContent as $key => $val) {
            if (empty($val)) continue;
            $temp = explode(' ', $val);
            $temp[0] = $date . ' '. $temp[0];
            $date_netraffic[$temp[0]]['outPut'] = $temp[1];
            $date_netraffic[$temp[0]]['inPut'] = $temp[2];
        }

        //处理日期
        $time_quantum = [];
        for ($i = 1800; $i <= 86400; $i += $time_interval) {
            $time_quantum[] = strtotime($date) + $i;
        }

        //对返回结果进行处理
        $result = [];
        foreach($time_quantum as $key => $val) {
            $resultTemp['date'] = $val;
            $resultTemp['outPut'] = 0;
            $resultTemp['inPut']= 0;
            $resultTemp['times'] = 0;
            foreach ($date_netraffic as $k => $v) {
                if (($val - $time_interval) <= strtotime($k) && strtotime($k) <= $val) {
                    if ($v['outPut'] != -1 && $v['inPut'] != 1) {
                        $resultTemp['outPut']  += $v['outPut'];
                        $resultTemp['inPut']  += $v['inPut'];
                        $resultTemp['times'] += 1;
                    }
                }
            }

            if ($resultTemp['times'] != 0) {
                $resultTemp['outPut'] = round($resultTemp['outPut'] / $resultTemp['times']);
                $resultTemp['inPut'] = round($resultTemp['inPut'] / $resultTemp['times']);
                $resultTemp['date'] = date('H:i:s', $resultTemp['date']);
                unset($resultTemp['times']);
                array_push($result, $resultTemp);
            }
        }
        return $result;
    }

    /**
     * @Author YiYuan-LIn
     * @Date: 2020/3/1
     * @enumeration:
     * @return array|false
     * @description 获取所有站点
     */
    public function getWebSiteList($params)
    {
        $date = $params['date'];
        $list = glob($this->logPath . date('Ymd', strtotime($date)) .'/*.flow.log');

        $result = [];
        foreach ($list as $key => $val) {
            array_push($result, [
                'label' => str_replace($this->logPath . date('Ymd', strtotime($date)) . '/', '', $val),
                'value' => str_replace($this->logPath . date('Ymd', strtotime($date)) . '/', '', $val),
            ]);
        }

        return $result;
    }
}