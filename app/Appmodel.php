<?php

namespace App;
use DB;

use Illuminate\Database\Eloquent\Model;
class Appmodel extends Model
{
    //
    public function __construct()
    {
        parent::__construct();
    }
    public function authenticate($request=''){
        $value = $request->session()->get('Loginemail'); // display session value
        return $value;
    }
    public function checkEmailValidate($email){
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
            if (preg_match($regex, $email)) {
             return 1;
            } else { 
             return 0;
            }  
    }
   public function authenticateAPI($accesskey=''){ // display session value
        $users = DB::select('select * from hruserlogins where Accesstoken = ?',[$accesskey]);
        if(!empty($users)){
            return 1;
        }else{
            return 0;
        }
        return 0;
    }

    public function checkEmailExist($email){
        $users = DB::select('select * from hrusers where Email = ?',[$email]);
        if(!empty($users)){
            return 1;
        }else{
            return 0;
        }
    }
     public function checkEmailExistPersonal($email,$id){
        //$users = DB::table('hrusers')->where('PersonalEmail',$email)->orWhere("Email",$email)->where('UserId', '<>', $id)->get();
        $users = DB::table('hrusers')->Where("Email",$email)->orwhere('PersonalEmail',$email)->where('UserId', '<>', $id)->get();
      
        if(!empty($users)){
            return 1;
        }else{
            return 0;
        }
    }

    public function checkPhoneExist($phone){
        $users = DB::select('select * from hrusers where PhoneNumber = ?',[$phone]);
        if(!empty($users)){
            return 1;
        }else{
            return 0;
        }
    }

    

}


