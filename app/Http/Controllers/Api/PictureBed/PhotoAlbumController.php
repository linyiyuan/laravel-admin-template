<?php

namespace App\Http\Controllers\Api\PictureBed;

use App\Http\Controllers\Api\CommonController;
use App\Models\PictureBed\PhotoAlbum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

/**
 * Class PhotoAlbumController
 * @package App\Http\Controllers\Api\PictureBed
 * @Author YiYuan-LIn
 * @Date: 2019/12/24
 */
class PhotoAlbumController extends CommonController
{
    /**
     * 请求参数
     * @var array
     */
    protected $params;

    /**
     * PhotoAlbumController constructor.
     */
    public function __construct()
    {
        $this->params = Input::all();
    }

    /**
     * 相册集列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $type = $this->params['type'] ?? '';
            $query = PhotoAlbum::query();
            $total = $query->count();

            //判断如果是获取列表的话
            if ($type == 'getPhotoAlbumList') {
                $photoAlbum = $query->get()->toArray();
                $photoAlbumList = [];
                foreach ($photoAlbum as $key) {
                    array_push($photoAlbumList, [
                        'label' => $key['album_name'],
                        'value' => $key['id'],
                    ]);
                }
               return handleResult(200, 'success',
                    [
                        'list' => $photoAlbumList,
                        'total' => $total
                    ]);

            }
            if (!empty($this->params['album_name'])) $query->where('album_name', 'like', '%' . $this->params['album_name'] .'%');

            $query = $this->pagingCondition($query, $this->params);
            $list = $query->get()->toArray();

            return handleResult(200, 'success',
                [
                'list' => $list,
                'total' => $total
            ]);
        }catch (\Exception $e) {
            return $this->errorExp($e);
        }
    }

    /**
     * 添加相册
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $params = $this->params['postData'] ?? '';
            $data = [
                'album_name'  => $params['album_name'] ?? '',
                'album_desc'  => $params['album_desc'] ?? '',
                'album_type'  => $params['album_type'] ?? 1,
                'album_status' => $params['album_status'] ?? 1,
                'album_author' => $params['album_author'] ?? '',
                'album_cover' => $params['album_cover'] ?? '',
                'album_question' => $params['album_question'] ?? '',
                'album_answer' => $params['album_answer'] ?? '',
                'album_sort' => $params['album_sort'] ?? 99
            ];
            //配置验证
            $rules = [
                'album_name'  => 'required',
            ];

            //错误信息
            $message = [
                'album_name.required' => '[album_name]缺失',
            ];

            if ($data['album_type'] == 2) {
                $rules['album_question'] = 'required';
                $rules['album_answer'] = 'required';
                $message['album_question.required'] = '[album_question]缺失';
                $message['album_answer.required'] = '[album_answer]缺失';
            }

            $this->verifyParams($data, $rules, $message);

            $photoAlbumObj = new PhotoAlbum();
            $photoAlbumObj->album_name = $data['album_name'];
            $photoAlbumObj->album_desc = $data['album_desc'];
            $photoAlbumObj->album_type = $data['album_type'];
            $photoAlbumObj->album_status = $data['album_status'];
            $photoAlbumObj->album_author = $data['album_author'];
            $photoAlbumObj->album_cover = $data['album_cover'];
            $photoAlbumObj->album_question = $data['album_question'];
            $photoAlbumObj->album_answer = $data['album_answer'];
            $photoAlbumObj->album_sort = $data['album_sort'];
            if (!$photoAlbumObj->save()) $this->throwExp(400, '添加相册失败');

            return handleResult(200, '添加相册成功');
        }catch (\Exception $e) {
            return $this->errorExp($e);
        }
    }

    /**
     * 获取修改的单例
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        try {
            if (!intval($id)) $this->throwExp(400, '非法参数');

            $photoAlbumObj = PhotoAlbum::query()->find($id);
            if (empty($photoAlbumObj)) $this->throwExp(400, '查询不到该数据项');

            return handleResult(200, 'success', $photoAlbumObj);
        }catch (\Exception $e) {
            return $this->errorExp($e);
        }
    }

    /**
     * 修改相册
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $params = $this->params['postData'] ?? '';
            $data = [
                'id' => $id,
                'album_name'  => $params['album_name'] ?? '',
                'album_desc'  => $params['album_desc'] ?? '',
                'album_type'  => $params['album_type'] ?? 1,
                'album_status' => $params['album_status'] ?? 1,
                'album_author' => $params['album_author'] ?? '',
                'album_cover' => $params['album_cover'] ?? '',
                'album_question' => $params['album_question'] ?? '',
                'album_answer' => $params['album_answer'] ?? '',
                'album_sort' => $params['album_sort'] ?? 99
            ];
            //配置验证
            $rules = [
                'album_name'  => 'required',
                'id'  => 'required|integer',
            ];
            //错误信息
            $message = [
                'album_name.required' => '[album_name]缺失',
                'id.required' => '[id]缺失',
                'id.integer' => '[id] 必须为整型',
            ];

            $this->verifyParams($data, $rules, $message);

            $photoAlbumObj = PhotoAlbum::query()->find($id);
            if (empty($photoAlbumObj)) $this->throwExp(400, '查询数据为空');
            $photoAlbumObj->album_name = $data['album_name'];
            $photoAlbumObj->album_desc = $data['album_desc'];
            $photoAlbumObj->album_type = $data['album_type'];
            $photoAlbumObj->album_status = $data['album_status'];
            $photoAlbumObj->album_author = $data['album_author'];
            $photoAlbumObj->album_cover = $data['album_cover'];
            $photoAlbumObj->album_question = $data['album_question'];
            $photoAlbumObj->album_answer = $data['album_answer'];
            $photoAlbumObj->album_sort = $data['album_sort'];
            if (!$photoAlbumObj->save()) $this->throwExp(400, '修改相册失败');

            return handleResult(200, '修改相册成功');
        }catch (\Exception $e) {
            return $this->errorExp($e);
        }
    }

    /**
     * 删除相册
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            if (!intval($id)) $this->throwExp(500, '参数错误');
            if (!PhotoAlbum::destroy($id)) $this->throwExp(500, '删除失败');

            return handleResult(200, '删除成功');
        }catch (\Exception $e) {
            return $this->errorExp($e);
        }
    }
}
