<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Api\CommonController;
use App\Http\Services\System\NetrafficService;
use Illuminate\Http\Request;

class NetrafficController extends CommonController
{

    /**
     * @Author YiYuan-LIn
     * @Date: 2020/3/1
     * @enumeration:
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @description 获取流量监控
     */
    public function netraffic(Request $request)
    {
        try {
            $params = [
                'date' => $request->date ?? '2014-08-21',
                'time_interval' => $request->time_interval ?? 30,
                'website' => $request->website ?? ''
            ];
            $rules = [
                'date' => 'required',
                'time_interval' => 'required|integer',
                'website' => 'required',
            ];
            $message = [
                'date.required' => ' date 缺失',
                'website.required' => ' website 缺失',
                'time_interval.required' => ' time_interval 缺失',
                'time_interval.integer' => ' time_interval 必须为整数',
            ];
            $this->verifyParams($params, $rules, $message);

            $result = NetrafficService::getInstance()->netraffic($params);

            return handleResult(200, '获取数据成功',
                [
                    'columns' => ['date', 'outPut', 'inPut'],
                    'rows' => $result
                ]
            );
        }catch (\Exception $e) {
            return $this->errorExp($e);
        }
    }

    /**
     * @Author YiYuan-LIn
     * @Date: 2020/3/2
     * @enumeration:
     * @return \Illuminate\Http\JsonResponse
     * @description 获取站点列表
     */
    public function getWebSiteList ()
    {
        try {
            $params = [
                'date' => $request->date ?? '2014-08-21',
            ];
            $rules = [
                'date' => 'required',
            ];
            $message = [
                'date.required' => ' date 缺失',
            ];
            $this->verifyParams($params, $rules, $message);

            $result = NetrafficService::getInstance()->getWebSiteList($params);

            return handleResult(200, '获取数据成功', $result);
        }catch (\Exception $e) {
            return $this->errorExp($e);
        }
    }
}
