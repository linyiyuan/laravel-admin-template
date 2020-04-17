<?php

namespace App\Http\Controllers\Api;

use App\Models\Users;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use JWTAuth;
use JWTFactory;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TestController extends CommonController
{
    public function test()
    {
        $result = DB::table('test')->count();

        if ($result > 0 ) {
            echo 0;
        }else{
            DB::table('test')->insert([
                'name' => 'name1',
                'age' => 1,
            ]);
            echo 1;
        }

    }
}
