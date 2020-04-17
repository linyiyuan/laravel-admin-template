<?php

namespace App\Http\Controllers\Api\PictureBed;

use App\Http\Controllers\Api\CommonController;
use App\Models\PictureBed\Photo;
use App\Models\PictureBed\PhotoAlbum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

/**
 * Class PhotoController
 * @package App\Http\Controllers\Api\PictureBed
 * @Author YiYuan-LIn
 * @Date: 2019/12/24
 */
class PhotoController extends CommonController
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
     * 图片列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $query = Photo::query();
            $query = $query->from('photo as a');

            $query = $query->leftJoin('photo_album as b', 'a.photo_album', 'b.id');
            $query = $query->select('a.*', 'b.album_name');

            if (!empty($this->params['photo_album'])) $query->where('a.photo_album', $this->params['photo_album']);
            $total = $query->count();

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
                'photo_url'  => $params['photo_url'] ?? '',
                'photo_album'  => $params['photo_album'] ?? '',
            ];
            //配置验证
            $rules = [
                'photo_url'  => 'required',
                'photo_album'  => 'required',
            ];
            //错误信息
            $message = [
                'photo_url.required' => '[photo_url]缺失',
                'photo_album.required' => '[photo_album]缺失',
            ];

            $this->verifyParams($data, $rules, $message);
            if (empty(PhotoAlbum::query()->find($data['photo_album']))) $this->throwExp(400, '查询不到该相册');

            if (is_array($data['photo_url'])) {
                foreach ($data['photo_url'] as $key) {
                    Photo::query()->insert([
                        'photo_url' => $key,
                        'photo_album' => $data['photo_album'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
            return handleResult(200, '添加照片成功');
        }catch (\Exception $e) {
            return $this->errorExp($e);
        }
    }

    /**
     * 删除图片
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            if (strlen($id) < 0) $this->throwExp(500, '参数错误');

            if($id == 0) {
                $ids = $this->params['ids'] ?? '';
                if (empty($ids)) $this->throwExp(500, '参数错误');

                if (is_array($ids)) Photo::query()->whereIn('id', $ids)->delete();
                return handleResult(200, '删除成功');
            }

            if (!Photo::destroy($id)) $this->throwExp(500, '删除失败');

            return handleResult(200, '删除成功');
        }catch (\Exception $e) {
            return $this->errorExp($e);
        }
    }
}
