<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LotteryController extends Controller
{

    public function index()
    {
        $query = DB::connection('mysql_lucky');
        $res = $query->table('lucky_turn')->get();
        $turns = toArray($res);
        return view('system.lottery',['turns'=>$turns]);
    }

    //获取所有抽奖等级
    public function ajaxGetTurn(Request $request)
    {
        $query = DB::connection('mysql_lucky');
        $res = $query->table('lucky_turn')->get();
        $turns = toArray($res);
        return $turns;
    }

    //检查轮数是否存在
    public function ajaxGetTurns(Request $request)
    {
        $turn_id = $request->input('turn_id');
        $query = DB::connection('mysql_lucky');
        $res = $query->table('lucky_turn')->where(['turn_id'=>$turn_id])->get();
        $turns = toArray($res);
        $re = $query->table('lucky_config')->get();
        $turns[0]['turn_pre_num'] = $re[0]->turn_pre_num;
        return $turns;
    }

    //检查额外轮数标识是否已经存在
    public function ajaxGetMarkIfExist(Request $request)
    {
        $turn_id = $request->input('mark');
        $query = DB::connection('mysql_lucky');
        $res = $query->table('lucky_turn')->where(['turn_id'=>$turn_id])->get();
        $turns = toArray($res);
        return $turns;
    }

    //检查用户id是否已经存在
    public function ajaxcheckUid(Request $request)
    {
        $uid = $request->input('uid');
        $query = DB::connection('mysql_lucky');
        $res = $query->table('lucky_user')->where(['id'=>$uid])->get();
        $turns = toArray($res);
        return $turns;
    }

    //抽奖配置
    public function lotteryone(Request $request)
    {
        $turn_id = $request->input('turn');
        $turn_pre_num = $request->input('pre_num');
        $turn_total_number = $request->input('total');

        if (!$turn_id ||!$turn_pre_num || !$turn_pre_num){
            $ret =  [
                'status' => 300,
                'message' => '请选择抽奖等级,填写每次抽奖人数与总数',
            ];
            return response()->json($ret);
        }

        $data = [
            'turn_id'=>$turn_id,
            'turn_pre_num'=>$turn_pre_num,
        ];
        $query = DB::connection('mysql_lucky');
        $res = $query->table('lucky_config')->update($data);
        $lucky_turn = [
            'turn_total_number'=>$turn_total_number
        ];
        $re =  $query->table('lucky_turn')->where(['turn_id'=>$turn_id])->update($lucky_turn);

        $ret =  [
            'status' => 200,
            'message' => '修改成功',
        ];
        return response()->json($ret);
    }

    //加抽2
    public function lotterytwo(Request $request)
    {
        $turn_id = $request->input('mark');
        $turn_pre_num = $request->input('pre_nums');
        $turn_total_number = $request->input('totals');
        $man = $request->input('man');
        $woman = $request->input('peoples');

        if (!$turn_id ||!$turn_pre_num || !$turn_total_number ||!$man){
            $ret =  [
                'status' => 300,
                'message' => '请填写标识,每轮抽奖数，总数',
            ];
            return response()->json($ret);
        }

        //添加奖池
        if ($man==100){

        }elseif($man==200){

        }else{
            $ret =  [
                'status' => 300,
                'message' => '未知错误',
            ];
            return response()->json($ret);
        }

        $query = DB::connection('mysql_lucky');
        //更新当前抽奖配置
        $data = [
            'turn_id'=>$turn_id,
            'turn_pre_num'=>$turn_pre_num,
        ];
        $res =$query->table('lucky_config')->update($data);
        //添加抽奖奖项
        $lucky_turn = [
            'turn_id'=>$turn_id,
            'turn_name'=>'额外加抽'.$turn_id,
            'turn_total_number'=>$turn_total_number
        ];
        $re =  $query->table('lucky_turn')->insert($lucky_turn);



        $ret =  [
            'status' => 200,
            'message' => $res ? '添加成功':'添加失败',
        ];
        return response()->json($ret);
    }

    //添加用户3
    public function lotterythree(Request $request)
    {
        $uid = $request->input('username');
        $realname = $request->input('trueName');
        $level = $request->input('level');

        if (!$uid ||!$realname||!$level){
            $ret =  [
                'status' => 300,
                'message' => '请填写序号或姓名,用户等级',
            ];
            return response()->json($ret);
        }
        $query = DB::connection('mysql_lucky');
        $r = $query->table('lucky_user')->where(['id'=>$uid])->get();
        if (!empty(toArray($r))){
            $ret =  [
                'status' => 300,
                'message' => '用户已存在',
            ];
            return response()->json($ret);
        }
        $data = [
            'id'=>$uid,
            'real_name'=>$realname,
            'level'=>$level,
        ];


        $res = $query->table('lucky_user')->insert($data);

        $ret =  [
            'status' => 200,
            'message' => $res ? '添加成功':'添加失败',
        ];
        return response()->json($ret);
    }

    //展示用户
    public function user()
    {
        $query = DB::connection('mysql_lucky');
        $user = $query->table('lucky_user')->get();
        return view('system.lotteryuser',['user'=>$user]);
    }

}