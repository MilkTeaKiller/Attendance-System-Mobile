<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use PHPMailer\PHPMailer\PHPMailer;
class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';
    protected $user = [];
    public function check_login(){
        $user = session("login_user");

        if(!$user){
            $this->redirect("login");
        }else{
            $user['username'] = $user['firstname']." ".$user['lastname'];
            $user['id'] = $user['userID'];
            //获取部门的数据
            $where = [
                'departmentID' => $user['departmentID'],
            ];
            $department = db('department');
            $dep_name = $department->field("department")->where($where)->find();
            $user['department'] = $dep_name['department'] ? $dep_name['department'] :'';
            $this->assign("user",$user);
            $this->user = $user;
        }
    }

    public function index()
    {   
        $this->check_login();
        return $this->view->fetch('dashboard');
    }
    public function login(){
        return $this->view->fetch();
    }
    //转盘页面
    public function dashboard(){
        $this->check_login();
        return $this->view->fetch();
    }
    //打卡页面
    public function profile(){
        $this->check_login();
        return $this->view->fetch();
    }
    //消息页面
    public function msg(){
        $this->check_login();
        $db_msg = db("user_msg");
        $where = [
            'user_id' => $this->user['id'],
        ];
        $list = $db_msg->where($where)->order("id desc")->select();
        $this->assign('list',$list);
        return $this->view->fetch();
    }
    //忘记密码
    public function forget_psd(){
       return $this->view->fetch('forget'); 
    }
    //个人地图
    public function user_map(){
        return $this->view->fetch();
    }
    public function layout(){
        session("login_user",null);
        $this->redirect("login");
    }
    //签到页面
    public function sign(){
        $this->check_login();
        $where = [
            'userID' => $this->user['id'],
        ];
        $db = db("attendance_record");
        $list = $db->where($where)->field("Date")->order("recordID desc")->select();
        $time_list = array_map('strtotime',array_column($list,'Date'));
        $this->assign("list",implode(",",$time_list));
        return $this->view->fetch();
    }
    //登录页面
    public function forget(){

    }
    public function map(){
        $this->check_login();
        return $this->view->fetch();
    }
    //用户签到
    public function user_sign(){
        $param = input("param.");
        $H = date("H");
        $day = date("Y-m-d");
        $db_msg = db("user_msg");
        if($H<12){
            $where = [
                'userID'=>$param['user_id'],
                "Date" => $day,
            ];
            $db = db('attendance_record');
            $count = $db->where($where)->where("punchIn_morning","neq","00:00:00")->find();
            if($count){
                $count_out = $db->where($where)->where("punchOut_morning","neq","00:00:00")->find();
                if($count_out){
                    $this->res([
                        'code' => 0,
                        'msg'  => "Already punched my card this morning",
                    ]);
                }else{
                    $update = [
                        'punchOut_morning' => date("H:i:s"),
                    ];
                    $db->where($where)->update($update);
                    $this->res([
                        'code' => 0,
                        'msg'  => "Check out successfully in the morning",
                    ]);
                }
            }else{
                $add = [
                    'userID'  => $param['user_id'],
                    "Date" => $day,
                    "punchIn_morning" => date("H:i:s"),
                    "punchIn_afternoon" => "00:00:00",
                    'punchOut_afternoon' => "00:00:00",
                    'punchOut_morning' => "00:00:00",
                ];
                $add_msg = [
                    'user_id' => $param["user_id"],
                    'msg' => 'Successful signing in in the morning',
                    "add_time" => date("Y-m-d H:i:s"),
                ];
                $db->insert($add);
              
                $this->res([
                    'code' => 1,
                    'msg'  => "Clock in successfully in the morning",
                ]);
            }

        }else{
            $where = [
                'userID'=>$param['user_id'],
                "Date" => $day,
            ];
            $db = db('attendance_record');
            $count = $db->where($where)->where("punchIn_afternoon","neq","00:00:00")->find();
            if($count){
                $count_out = $db->where($where)->where("punchOut_afternoon","neq","00:00:00")->find();
                if($count_out){
                    $this->res([
                        'code' => 0,
                        'msg'  => "Already punched my card this afternoon",
                    ]);
                }else{
                    $update = [
                        'punchOut_afternoon' => date("H:i:s"),
                    ];
                    $db->where($where)->update($update);
                    $this->res([
                        'code' => 0,
                        'msg'  => "Successfully punch out in the afternoon",
                    ]);
                }
                
            }
            else{
                $count_today = $db->where($where)->find();
                if($count_today){
                    $update = [
                        "punchIn_afternoon" => date("H:i:s"),
                    ];
                    $db->where($where)->update($update);
                }else{
                    $add = [
                        'userID'  => $param['user_id'],
                        "Date" => $day,
                        "punchIn_afternoon" => date("H:i:s"),
                        'punchOut_afternoon' => "00:00:00",
                        'punchIn_morning' => "00:00:00",
                        'punchOut_morning' => "00:00:00",
                    ];
                    $db->insert($add);
                }
                
                $this->res([
                    'code' => 1,
                    'msg'  => "Clock in successfully in the afternoon",
                ]);
            }
        }
        
    }
  
    public function user_login(){
        $param = input('param.');
        $where = [
            'email' => $param['email'],
        ];
        $user = db("user")->where($where)->find();
        if(empty($user)){
            $this->res([
                'code' => 0,
                'msg'  => "Your information could not be found",
            ]);
        }
        if($user['password'] != md5($param['psw'].$user['salt'])){
            $this->res([
                'code' => 0,
                'msg'  => "Your password is incorrect",
            ]);
        }
        session("login_user",$user);
        $this->res([
                'code' => 1,
                'msg'  => "Login succeeded",
            ]);
    }
    //用户修改密码
    public function user_forget(){
        $param = input("param.");
        $code = session("user_code");
        if($code != $param['code']){
            $this->res([
                'code' => 0,
                'msg' => "Incorrect verification code",
            ]);
        }
        $where = [
            'email' => $param['email'],
        ];
        $db = db("user");
        $user = db("user")->where($where)->find();
        if($user){
            $res = $db->where($where)->update([
                'password' => md5($param['psd']),
            ]);
            if($res){
                $this->res([
                    'code' => 1,
                    'msg' => "Update succeeded",
                ]); 
            }else{
                 $this->res([
                    'code' => 0,
                    'msg' => "Update failed",
                ]); 
            }
        }else{
            $add = [
                'email' => $param['email'],
                'password' => md5($param['psd']),
                'time' => date("Y-m-d H:i:s"),
            ];
            $res = $db->insert($add);
            if($res){
                 $this->res([
                            'code' => 1,
                            'msg' => "New user succeeded",
                        ]);
            }else{
                $this->res([
                            'code' => 0,
                            'msg' => "Failed to create new user",
                        ]);
            }
           
        }
    }
    public function send_msg(){
        $param = input("param.");
        $code = $this->createCode(4);
        session("user_code", $code);
        $res = $this->sendMail($param['mail'],'Verification code mail',"Your verification code is:{$code}");
        if($res){
            $this->res([
                'code' => 1,
                'msg' => "Verification code sent",
            ]);
        }else{
            $this->res([
                'code' => 1,
                'msg' => "Verification code sending failed",
            ]);
        }
    }
    //生成验证码
    public function createCode($length = 6){
        $key = "";
        $num = "123456789";
        for($i=0;$i<$length;$i++){
            $key .= $num[mt_rand(0,8)];
        }
        return $key;
    }
  
    public function sendMail($to,$title,$content) {
     
        $mail = new PHPMailer;
    
        $mail->isSMTP();                                      
        $mail->SMTPAuth = true;                               
        $mail->Host = 'smtp.qq.com';
        $mail->Username = '469127946@qq.com';                 
        $mail->Password = 'ngryodkcmhmfbhdg';     
        $mail->SMTPSecure = 'ssl';                            
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';                        
        $mail->setFrom('469127946@qq.com', $title);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $title;
        $mail->Body = $content;
    
        if(!$mail->send()) {
          return false;
        } else {
          return true;
        }
    }

}
