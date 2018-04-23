<?php
namespace App\Http\Controllers;
use App\Appmodel;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class UsersController extends Controller {

    public function authenticate(Request $request){
        $tag = new Appmodel();
        $Email = $request->input('Email');
        $Password = $request->input('Password');
        if((!empty($Email)) && (!empty($Password))){
          $encPassword=base64_encode($Password);
          $em =$tag->checkEmailValidate($Email);   
          if($em=='1'){
            if($Email=='itadmin@cueserve.com'){
               $emp = DB::table('hruserlogins')->where('LoginProvider',$Email)->where("ProviderKey",$encPassword)->where("Status",'1')->get();
      
                if (empty($emp)){
                    $data=array('Status'=>'400', 'Message'=>'Unauthenticate User.');
                }else{
                    foreach ($emp as $svalue) {
                        $eml=$svalue->LoginProvider;                
                        $name=$svalue->ProviderDisplayName;
                        $id=$svalue->UserId;
                        $status=$svalue->Status;
                        $key=mt_rand();
                        DB::table('hruserlogins')
                        ->where('UserId', $id)
                        ->update(array('Accesstoken' => $key));

                        $request->session()->put('Loginemail', $eml);
                        $request->session()->put('loginname', $name);
                        $request->session()->put('LoginId', $id);
                        $request->session()->put('Status', $status);
                        $userdetail=array(
                           'ID'=>$id,
                           'Email'=>$eml,
                           'Username'=>$name,
                           'Status'=>$svalue->Status,
                           'Accesstoken'=>$key,
                         );
                         $data=array('Status'=>'200', 'Message'=>'Login successfully.', 'Userdetail'=>$userdetail);
                    }    
                }
            }else{
                $encPassword=base64_encode($Password);
                $emp = DB::table('hruserlogins')->where('LoginProvider',$Email)->where("ProviderKey",$encPassword)->get();
      
                if (empty($emp)){
                    $data=array('Status'=>'400', 'Message'=>'Unauthenticate User.');
                }else{ foreach ($emp as $svalue) {
                    $eml=$svalue->LoginProvider;                
                    $name=$svalue->ProviderDisplayName;
                    $id=$svalue->UserId;
                    $status=$svalue->Status;
                    $key=mt_rand();
                        DB::table('hruserlogins')
                        ->where('UserId', $id)
                        ->update(array('Accesstoken' => $key)); 

                    $request->session()->put('Loginemail', $eml);
                    $request->session()->put('loginname', $name);
                    $request->session()->put('LoginId', $id);
                    $request->session()->put('Status', $status);
                    $userdetail=array(
                      'ID'=>$id,
                      'Email'=>$eml,
                      'Username'=>$name,
                      'Status'=>$svalue->Status,
                      'Accesstoken'=>$key,
                    );
                    $data=array('Status'=>'200', 'Message'=>'Login successfully.', 'Userdetail'=>$userdetail);
                 } 
          }
        }          
        }else{
           $data=array('Status'=>'400', 'Message'=>'Invalid Email Id');
        }
        }else{
          $data=array('Status'=>'400', 'Message'=>'All Fields required');
        }
        return response()->json($data, 201);
    }
 


    
 
    public function usersCreate(Request $request){
      
        $Email = $request->input('Email');
        $Role = $request->input('Role');
        $accesskey = $request->input('AccessKey');
        $error = '';

        $tag = new Appmodel();
        $chk =$tag->authenticateAPI($accesskey);
      
       // $chk='1';
        if($chk=='1'){ 
          if((!empty($Email)) && (!empty($Role)) && (!empty($accesskey))){
            
            $em =$tag->checkEmailValidate($Email);          
            $ems =$tag->checkEmailExist($Email);
            if($em=='1' && $ems=='0'){
                $parts = explode('@', $Email);
                $uname=$parts[0];

               $to = DB::table('hrusers')->insertGetId(
                   array('Email' => $Email,
                         'UserStatus' => $Role,
                         'Status' => '3',
                         'UserName' => $Email,
                         )
                );

               $to1 = DB::table('hruserlogins')->insertGetId(
                   array('LoginProvider' => $Email,
                         'ProviderDisplayName' => $Email,
                         'UserId' => $to,
                         'Status' => '3',
                         )
                );


                DB::table('hrusers')
              ->where('id', $to)
              ->update(array('UserId' => $to)); 
              
$url= 'http://trackit.cueserve.com/trackit-api/v1/users/reset-password/'.$to; 
            $toemail = $Email;
$subject = "User Login Details";

$message = "
<html>
<head>
<title>Login Details</title>
</head>
<body>
<table border='1'>
<tr>
<th>Email : ".$Email." </th>
<th> Reset Password Link : <a href='".$url."' target='_blank'>Reset Password</a></th>
</tr>
</table>
</body>
</html>
";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <bhavna@cueserve.com>' . "\r\n";
//$headers .= 'Cc: myboss@example.com' . "\r\n";

mail($toemail,$subject,$message,$headers);






              $userdetail = array(
                                  'Email' => $Email,
                                  'UserID' => $to,  );

                $error = array('Status' => "200", "Message" => "Insert Successfully","Userdetail" => $userdetail);
              
            }else{
                
              if($ems=='1'){
                $error = array('Status' => "400", "Message" => "Email ID already Exist ");
              }
             
              else{
                $error = array('Status' => "400", "Message" => "Invalid Email format");
              }
              
              
            }

          }
          else{
            $error=array('Status'=>'400', 'Message'=>'All Fields required');
          }
        }else{
          $error=array('Status'=>'400', 'Message'=>'Unauthenticate Users');
        }
        return response()->json($error, 201);        
    }
    public function getUsers_bkup(REQUEST $request){
   
         $tag = new Appmodel();
        $chk =$tag->authenticate($request);
         $chk='1';
        if($chk!=''){
           $users = DB::select('select * from hrusers');
            
            $error=array('Status'=>'200', 'Users'=>$users);

        }else{
          $error=array('Status'=>'400', 'Message'=>'Unauthenticate Users');
        }
         return response()->json($error, 201);  
    }
     public function getUsers(REQUEST $request){
        $accesskey = $request->input('AccessKey');
        $tag = new Appmodel();
        $chk =$tag->authenticateAPI($accesskey);
        $chk='1';
        if($chk=='1'){

           $uses = DB::select('select * from hrusers');
            
          foreach($uses as $us){
               $img=$us->ProfileImage;
               if($img!=''){
                   $i='http://trackit.cueserve.com/trackit-api/public/images/'.$img;
               }else{$i='http://trackit.cueserve.com/trackit-api/public/images/dummy.jpg';}
$maried=$us->MaritalStatus;
if($maried=='1'){ $m='married';}else{ $m='single';}
            $users[]=array(
                'Id'=>$us->Id,
                'Email'=>$us->Email,
                'FirstName'=>$us->FirstName,
                'LastName'=>$us->LastName,
                'PersonalEmail'=>$us->PersonalEmail,
                'NormalizedUserName'=>$us->NormalizedUserName,
                'ReferencePhoneNumber'=>$us->ReferencePhoneNumber,
                'PhoneNumber'=>$us->PhoneNumber,
                'BirthDate'=>$us->BirthDate,
                'AnniversaryDate'=>$us->AnniversaryDate,
                'EmergencyContactPerson'=>$us->EmergencyContactPerson,
                'BloodGroup'=>$us->BloodGroup,
                'FacebookId'=>$us->FacebookId,
                'FacebookPageLike'=>$us->FacebookPageLike,
                 'LinkedIn'=>$us->LinkedIn,
                'Address'=>$us->Address,
                 'UserStatus'=>$us->UserStatus,
                'UserId'=>$us->UserId,
                 'ProfileImage'=>$i,
'Gender'=>$us->Gender,'MaritalStatus'=>$m,'SpouseName'=>$us->SpouseName,'Relation'=>$us->ContactRelation,'SpouseContact'=>$us->SpouseContact,
                );
              }

            $error=array('Status'=>'200', 'Users'=>$users);

        }else{
          $error=array('Status'=>'400', 'Message'=>'Unauthenticate Users');
        }
         return response()->json($error, 201);  
    }


    public function getUsersDetail($id){
       // $value = session()->get('Loginemail');  
        //echo $accesskey = $request->input('AccessKey');
       //die;
        //$tag = new Appmodel();
        //$chk =$tag->authenticateAPI($accesskey);
        $chk='1';
        if($chk=='1'){

          $uses = DB::select('select * from hrusers where UserId = ?',[$id]);
          if(!empty($uses)){
              foreach($uses as $us){
               $img=$us->ProfileImage;
               if($img!=''){
                   $i='http://trackit.cueserve.com/trackit-api/public/images/'.$img;
               }else{$i='http://trackit.cueserve.com/trackit-api/public/images/dummy.jpg';}
$maried=$us->MaritalStatus;
if($maried=='1'){ $m='married';}else{ $m='single';}
            $users[]=array(
                'Id'=>$us->Id,
                'Email'=>$us->Email,
                'FirstName'=>$us->FirstName,
                'LastName'=>$us->LastName,
                'PersonalEmail'=>$us->PersonalEmail,
                'NormalizedUserName'=>$us->NormalizedUserName,
                'ReferencePhoneNumber'=>$us->ReferencePhoneNumber,
                'PhoneNumber'=>$us->PhoneNumber,
                'BirthDate'=>$us->BirthDate,
                'AnniversaryDate'=>$us->AnniversaryDate,
                'EmergencyContactPerson'=>$us->EmergencyContactPerson,
                'BloodGroup'=>$us->BloodGroup,
                'FacebookId'=>$us->FacebookId,
                'FacebookPageLike'=>$us->FacebookPageLike,
                 'LinkedIn'=>$us->LinkedIn,
                'Address'=>$us->Address,
                 'UserStatus'=>$us->UserStatus,
                'UserId'=>$us->UserId,
                 'ProfileImage'=>$i,

'Gender'=>$us->Gender,'MaritalStatus'=>$m,'SpouseName'=>$us->SpouseName,'Relation'=>$us->ContactRelation,'SpouseContact'=>$us->SpouseContact,
                );
              }
            $error=array('Status'=>'200', 'Users'=>$users);
          }else{
             $error=array('Status'=>'400', 'Message'=>'User Not Exist');
          }

        }else{
          $error=array('Status'=>'400', 'Message'=>'Unauthenticate Users');
        }
         return response()->json($error, 201);  
    }
    public function editUser(Request $request,$id){
       $FirstName = $request->input('FirstName');
      $LastName = $request->input('LastName');
      $PersonalEmail = $request->input('PersonalEmail');
      $NormalizedUserName = $request->input('NormalizedUserName');
      $PhoneNumber = $request->input('PhoneNumber');
      $ReferencePhoneNumber = $request->input('ReferencePhoneNumber');
      $BirthDate = date("Y-m-d", strtotime($request->input('BirthDate')));
      $AnniversaryDate = date("Y-m-d", strtotime($request->input('AnniversaryDate')));
      $EmergencyContactPerson = $request->input('EmergencyContactPerson');
      $BloodGroup = $request->input('BloodGroup');
      $FacebookId = $request->input('FacebookId');
      $FacebookPageLike = $request->input('FacebookPageLike');
      $LinkedIn = $request->input('LinkedIn');
      $Address = $request->input('Address');
$Relation= $request->input('Relation');

      $adminId = $request->session()->get('LoginId');
       $accesskey = $request->input('AccessKey');
       
        $tag = new Appmodel();
        $chk =$tag->authenticateAPI($accesskey);
        $chk='1';
        if($chk=='1'){

          if((!empty($FirstName)) && (!empty($LastName)) &&
           (!empty($PhoneNumber)) && (!empty($ReferencePhoneNumber)) && (!empty($BirthDate)) && (!empty($EmergencyContactPerson)) && (!empty($Address))){
             $em =$tag->checkEmailValidate($PersonalEmail);   
               if($em=='1'){

                 $users = DB::select('select * from hrusers where UserId = ?',[$id]);
                 if(!empty($users)){
                     DB::table('hrusers')
                  ->where('UserId', $id)
                  ->update(array('FirstName' => $FirstName,
                         'LastName' => $LastName,
                         'PersonalEmail' => $PersonalEmail,
                         'NormalizedUserName' => $NormalizedUserName,
                         'PhoneNumber' => $PhoneNumber,
                         'ReferencePhoneNumber' => $ReferencePhoneNumber,
                         'BirthDate' => $BirthDate,
                         'AnniversaryDate' => $AnniversaryDate,
                         'EmergencyContactPerson' => $EmergencyContactPerson,
                         'BloodGroup' => $BloodGroup,
                         'FacebookId' => $FacebookId,
                         'FacebookPageLike' => $FacebookPageLike,
                         'LinkedIn' => $LinkedIn,
                         'Address' => $Address,
'ContactRelation'=>$Relation,
                         ));


                    $users = DB::select('select * from hrusers where UserId = ?',[$id]);

                   $error=array('Status'=>'200', 'Message'=>'Update details Successfully','Userdetail'=>$users);
                 }else{
                    $error=array('Status'=>'400', 'Message'=>'User not exist');
                 }
               

               }else{
                  $error=array('Status'=>'400', 'Message'=>'Invalid Email Id formate');
               }  
          }else{
            $error=array('Status'=>'400', 'Message'=>'All fields required');
          }
        }else{
          $error=array('Status'=>'400', 'Message'=>'Unauthenticate Users');
        }
        return response()->json($error, 201);   
    }

   
     public function deleteUser(Request $request,$id){     
        $accesskey = $request->input('AccessKey');
       
        $tag = new Appmodel();
        $chk =$tag->authenticateAPI($accesskey);
        $chk='1';
        if($chk=='1'){


         $users = DB::select('select * from hrusers where UserId = ?',[$id]);
                 if(!empty($users)){
          DB::table('hruserlogins')
              ->where('UserId', $id)
              ->update(array('Status' => '2')); 
          $error=array('Status'=>'200', 'Message'=>'User inactive Successfully.');
        }else{
           $error=array('Status'=>'400', 'Message'=>'User not exist.');
        }

        }else{
          $error=array('Status'=>'400', 'Message'=>'Unauthenticate Users');
        }
         return response()->json($error, 201);
    }

    public function forgotPassword(Request $request){
        $emailid = $request->input('Email');
        $tag = new Appmodel();
         $em =$tag->checkEmailValidate($emailid); 
         if($em=='1'){
            $users = DB::select('select * from hrusers where Email = ?',[$emailid]);
            if(!empty($users)){
             $uid=$users[0]->Id; 
             if($uid!=''){
                 $url= 'http://trackit.cueserve.com/trackit-api/v1/users/reset-password/'.$uid;
                 
                
               $toemail = $emailid;
                $subject = "Reset Password - TrackIt";

                $message = "Reset Password Url : ".$url;

                // Always set content-type when sending HTML email
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                // More headers
                $headers .= 'From: <bhavna@cueserve.com>' . "\r\n";

                mail($toemail,$subject,$message,$headers);
                 
                 $data['url']=$url;
                 $data['success']='1';
                  $error=array('Status'=>'200', 'Message'=>'Send email successfully. please check your mail','url'=>$url);
                 
             }
            }else{
                $error=array('Status'=>'400', 'Message'=>'Email Id not Exist');
            }    
         }else{
          $error=array('Status'=>'400', 'Message'=>'Email Id not valid');
        }
         return response()->json($error, 201);
        
    }

    
  public function resetPassword(Request $request,$id){
       
        $newpassword = $request->input('newpassword');
        $confirmpassword = $request->input('confirmpassword');
        
        if($id!=''){
          
        if(($newpassword!='' && $confirmpassword!='')|| ($newpassword!='' || $confirmpassword!='')){
            $newpassword = base64_encode($request->input('newpassword'));
            $confirmpassword = base64_encode($request->input('confirmpassword'));
            $users = DB::select('select * from hrusers where UserId = ?',[$id]);
             if(!empty($users)){
                if($newpassword==$confirmpassword){
                    
                      DB::table('hrusers')
                  ->where('UserId', $id)
                  ->update(array('PasswordHash' => $newpassword,
                         ));
                       DB::table('hruserlogins')
                  ->where('UserId', $id)
                  ->update(array('ProviderKey' => $newpassword,
                         ));
                        $error=array('Status'=>'200', 'Message'=>'Reset password successfully.Please Login with new password');
                }else{
                     $error=array('Status'=>'400', 'Message'=>'Password and Confirm password does not match');
                }
             }else{
                 $error=array('Status'=>'400', 'Message'=>'User not Exist');
             }
        }else{
            $error=array('Status'=>'400', 'Message'=>'All Fields required');
        }
             
        }else{
             $error=array('Status'=>'400', 'Message'=>'User not Exist');
        }
       $data['id']=$id;
        return response()->json($error, 201);
    }

   public function changePassword_bkup(Request $request){
        $data['msg']=''; $data['succmsg']='';
        $oldPassword = $request->input('oldPassword');
        $newPassword = $request->input('newPassword');
        $ConfirmPassword = $request->input('ConfirmPassword');
        
        $loginemail = $request->session()->get('Loginemail');
        $LoginId = $request->session()->get('LoginId');
        
        $accesskey = $request->input('AccessKey');
       
        $tag = new Appmodel();
        $chk =$tag->authenticateAPI($accesskey);
        $chk='1';
        if($chk=='1'){
        return redirect('web/index'); }
        if($oldPassword!='' && $newPassword!=''){
            $encPassword=base64_encode($oldPassword);
            $emp = DB::table('hruserlogins')->where('UserID',$LoginId)->where("ProviderKey",$encPassword)->get();
            if(!empty($emp)){
                $newPassword=base64_encode($newPassword);
                $ConfirmPassword=base64_encode($ConfirmPassword);
                
                if($newPassword==$ConfirmPassword){
                    DB::table('hrusers')
                  ->where('UserId', $LoginId)
                  ->update(array('PasswordHash' => $newPassword,
                         ));
                       DB::table('hruserlogins')
                  ->where('UserId', $LoginId)
                  ->update(array('ProviderKey' => $newPassword,
                         ));
                    $error=array('Status'=>'200', 'Message'=>'Change Password successfully.');
                }else{
                    $error=array('Status'=>'400', 'Message'=>'New password and confirm password does not match');
                }
            }else{
                $error=array('Status'=>'400', 'Message'=>'Old Password does not match.');
            }
        }else{
            $error=array('Status'=>'400', 'Message'=>'All fields required.');
            
        }
       return response()->json($error, 201);
    }
    public function changePassword(Request $request){
        
        $oldPassword = $request->input('oldPassword');
        $newPassword = $request->input('newPassword');
        
        $LoginId = $request->input('Accesstoken');
        $accesskey = $request->input('AccessKey');
       
        $tag = new Appmodel();
        $chk =$tag->authenticateAPI($accesskey);
        $chk='1';
        if($chk=='1'){

       
        if($oldPassword!='' && $newPassword!=''){
            $encPassword=base64_encode($oldPassword);
            $emp = DB::table('hruserlogins')->where('Accesstoken',$LoginId)->where("ProviderKey",$encPassword)->get();
            if(!empty($emp)){
				
                $newPassword=base64_encode($newPassword);
                
                
                    DB::table('hrusers')
                  ->where('UserId', $emp[0]->UserId)
                  ->update(array('PasswordHash' => $newPassword,
                         ));
                       DB::table('hruserlogins')
                  ->where('UserId', $emp[0]->UserId)
                  ->update(array('ProviderKey' => $newPassword,
                         ));
                    $error=array('Status'=>'200', 'Message'=>'Change Password successfully.');
                
            }else{
                $error=array('Status'=>'400', 'Message'=>'Old Password does not match.');
            }
        }else{
            $error=array('Status'=>'400', 'Message'=>'All fields required.');
            
        }
        }else{
            $error=array('Status'=>'400', 'Message'=>'Unauthenticate user.');
        }
       return response()->json($error, 201);
    }

public function editUser1(Request $request,$id){

      $FirstName = $request->input('FirstName');
      $LastName = $request->input('LastName');
      $PersonalEmail = $request->input('PersonalEmail');
      $NormalizedUserName = $request->input('NormalizedUserName');
      $PhoneNumber = $request->input('PhoneNumber');
      $ReferencePhoneNumber = $request->input('ReferencePhoneNumber');
      $BirthDate = date("Y-m-d", strtotime($request->input('BirthDate')));
      $AnniversaryDate = date("Y-m-d", strtotime($request->input('AnniversaryDate')));
      $EmergencyContactPerson = $request->input('EmergencyContactPerson');
      $BloodGroup = $request->input('BloodGroup');
      $FacebookId = $request->input('FacebookId');
      $FacebookPageLike = $request->input('FacebookPageLike');
      $LinkedIn = $request->input('LinkedIn');
      $Address = $request->input('Address');
      $Gender = $request->input('Gender');
      $MaritalStatus = $request->input('MaritalStatus');
      $SpouseName = $request->input('SpouseName');
      $SpouseContact = $request->input('SpouseContact');
$Relation= $request->input('Relation');

      $adminId = $request->session()->get('LoginId');
      $accesskey = $request->input('AccessKey');
       
       
       
        $tag = new Appmodel();
        $chk =$tag->authenticateAPI($accesskey);
        $chk='1';
        if($chk=='1'){


          if((!empty($FirstName)) && (!empty($LastName)) && (!empty($Gender)) &&
           (!empty($PhoneNumber)) && (!empty($BirthDate)) && (!empty($Address))){
             $em =$tag->checkEmailValidate($PersonalEmail);   
               if($em=='1'){

                 $users = DB::select('select * from hrusers where UserId = ?',[$id]);
                 if(!empty($users)){
                    //-------------------------------------------------
                     $image = $request->file('ProfileImage');
 $image = $request->file('ProfileImage');
                     DB::table('hruserlogins')
                            ->where('UserId', $id)
                            ->update(array('Status' => '0',
                            ));
                    if($image==''){
			DB::table('hrusers')
                            ->where('UserId', $id)
                            ->update(array('FirstName' => $FirstName,
                                   'LastName' => $LastName,
                                   'PersonalEmail' => $PersonalEmail,
                                   'NormalizedUserName' => $NormalizedUserName,
                                   'PhoneNumber' => $PhoneNumber,
                                   'ReferencePhoneNumber' => $ReferencePhoneNumber,
                                   'BirthDate' => $BirthDate,
                                   'AnniversaryDate' => $AnniversaryDate,
                                   'EmergencyContactPerson' => $EmergencyContactPerson,
                                   'BloodGroup' => $BloodGroup,
                                   'FacebookId' => $FacebookId,
                                   'FacebookPageLike' => $FacebookPageLike,
                                   'LinkedIn' => $LinkedIn,
                                   'Address' => $Address,
                                   'Gender' => $Gender,
                                   'MaritalStatus' => $MaritalStatus,
                                   'SpouseName' => $SpouseName,
                                   'SpouseContact' => $SpouseContact,
'ContactRelation' => $Relation,'Status'=>'0',
                         ));

                    }else{
                        $input['imagename'] = time().'.'.$image->getClientOriginalExtension();
                        $destinationPath = public_path('/images');
                        $image->move($destinationPath, $input['imagename']);
					 
			if($image!=''){
                            $img=$input['imagename'];
                            DB::table('hrusers')
                            ->where('UserId', $id)
                            ->update(array('FirstName' => $FirstName,
                                   'LastName' => $LastName,
                                   'PersonalEmail' => $PersonalEmail,
                                   'NormalizedUserName' => $NormalizedUserName,
                                   'PhoneNumber' => $PhoneNumber,
                                   'ReferencePhoneNumber' => $ReferencePhoneNumber,
                                   'BirthDate' => $BirthDate,
                                   'AnniversaryDate' => $AnniversaryDate,
                                   'EmergencyContactPerson' => $EmergencyContactPerson,
                                   'BloodGroup' => $BloodGroup,
                                   'FacebookId' => $FacebookId,
                                   'FacebookPageLike' => $FacebookPageLike,
                                   'LinkedIn' => $LinkedIn,
                                   'Address' => $Address,
                                   'ProfileImage'=>$img,
                                   'Gender' => $Gender,
                                   'MaritalStatus' => $MaritalStatus,
                                   'SpouseName' => $SpouseName,
                                   'SpouseContact' => $SpouseContact,
'ContactRelation' => $Relation,'Status'=>'0',
                                   ));

			}else{
                                DB::table('hrusers')
                  ->where('UserId', $id)
                  ->update(array('FirstName' => $FirstName,
                         'LastName' => $LastName,
                         'PersonalEmail' => $PersonalEmail,
                         'NormalizedUserName' => $NormalizedUserName,
                         'PhoneNumber' => $PhoneNumber,
                         'ReferencePhoneNumber' => $ReferencePhoneNumber,
                         'BirthDate' => $BirthDate,
                         'AnniversaryDate' => $AnniversaryDate,
                         'EmergencyContactPerson' => $EmergencyContactPerson,
                         'BloodGroup' => $BloodGroup,
                         'FacebookId' => $FacebookId,
                         'FacebookPageLike' => $FacebookPageLike,
                         'LinkedIn' => $LinkedIn,
                         'Address' => $Address,
                         'Gender' => $Gender,
                                   'MaritalStatus' => $MaritalStatus,
                                   'SpouseName' => $SpouseName,
                                   'SpouseContact' => $SpouseContact,
'ContactRelation' => $Relation,'Status'=>'0',
                         ));

                             }
			}
                    //--------------------------------------------------
                     
                     
                     
           //-------------------------------------------------------
                    $uses = DB::select('select * from hrusers where UserId = ?',[$id]);
                       foreach($uses as $us){
                        $img=$us->ProfileImage;
                        if($img!=''){
                            $i='http://trackit.cueserve.com/public/images/'.$img;
                        }else{$i='http://trackit.cueserve.com/public/images/dummy.jpg';}
                        if($us->MaritalStatus=='1'){ $m='married';}else{ $m='Single';}
                     $users=array(
                         'Id'=>$us->Id,
                         'Email'=>$us->Email,
                         'FirstName'=>$us->FirstName,
                         'LastName'=>$us->LastName,
                         'PersonalEmail'=>$us->PersonalEmail,
                         'NormalizedUserName'=>$us->NormalizedUserName,
                         'ReferencePhoneNumber'=>$us->ReferencePhoneNumber,
                         'PhoneNumber'=>$us->PhoneNumber,
                         'BirthDate'=>$us->BirthDate,
                         'AnniversaryDate'=>$us->AnniversaryDate,
                         'EmergencyContactPerson'=>$us->EmergencyContactPerson,
                         'BloodGroup'=>$us->BloodGroup,
                         'FacebookId'=>$us->FacebookId,
                         'FacebookPageLike'=>$us->FacebookPageLike,
                          'LinkedIn'=>$us->LinkedIn,
                         'Address'=>$us->Address,
                          'UserStatus'=>$us->UserStatus,
                         'UserId'=>$us->UserId,
                          'ProfileImage'=>$i,
                          'Gender' => $us->Gender,
                                   'MaritalStatus' => $m,
                                   'SpouseName' => $us->SpouseName,
                                   'SpouseContact' => $us->SpouseContact,
'Relation' => $us->ContactRelation,
                         );

                   // print_r();
                       }
                   $error=array('Status'=>'200', 'Message'=>'Update details Successfully','Userdetail'=>$users);
                 }else{
                    $error=array('Status'=>'400', 'Message'=>'User not exist');
                 }
               

               }else{
                  $error=array('Status'=>'400', 'Message'=>'Invalid Email Id formate');
               }  
          }else{
            $error=array('Status'=>'400', 'Message'=>'All fields required');
          }
        }else{
          $error=array('Status'=>'400', 'Message'=>'Unauthenticate Users');
        }
        return response()->json($error, 201);   
    }


public function editUser2(Request $request,$id){

       $FirstName = $request->input('FirstName');
      $LastName = $request->input('LastName');
      $PersonalEmail = $request->input('PersonalEmail');
      $NormalizedUserName = $request->input('NickName');
      $PhoneNumber = $request->input('PhoneNumber');
      $ReferencePhoneNumber = $request->input('ReferencePhoneNumber');
      $BirthDate = date("Y-m-d", strtotime($request->input('BirthDate')));
      $AnniversaryDate = date("Y-m-d", strtotime($request->input('AnniversaryDate')));
      $EmergencyContactPerson = $request->input('EmergencyContactPerson');
      $BloodGroup = $request->input('BloodGroup');
      $FacebookId = $request->input('FacebookId');
      $FacebookPageLike = $request->input('FacebookPageLike');
      $LinkedIn = $request->input('LinkedIn');
      $Address = $request->input('Address');

        $accesskey = $request->input('AccessKey');
      
        $tag = new Appmodel();
        $chk =$tag->authenticateAPI($accesskey);
       // $chk='1';
        if($chk=='1'){
 
          if((!empty($FirstName)) && (!empty($LastName)) &&
           (!empty($PhoneNumber)) && (!empty($ReferencePhoneNumber)) && (!empty($BirthDate)) && (!empty($EmergencyContactPerson)) && (!empty($Address))){ 
             $em =$tag->checkEmailValidate($PersonalEmail);   
               if($em=='1'){

                 $users = DB::select('select * from hrusers where UserId = ?',[$id]);
                 if(!empty($users)){ 
                    //-------------------------------------------------
                     $image = $request->file('ProfileImage');
                    if($image==''){
			DB::table('hrusers')
                            ->where('UserId', $id)
                            ->update(array('FirstName' => $FirstName,
                                   'LastName' => $LastName,
                                   'PersonalEmail' => $PersonalEmail,
                                   'NormalizedUserName' => $NormalizedUserName,
                                   'PhoneNumber' => $PhoneNumber,
                                   'ReferencePhoneNumber' => $ReferencePhoneNumber,
                                   'BirthDate' => $BirthDate,
                                   'AnniversaryDate' => $AnniversaryDate,
                                   'EmergencyContactPerson' => $EmergencyContactPerson,
                                   'BloodGroup' => $BloodGroup,
                                   'FacebookId' => $FacebookId,
                                   'FacebookPageLike' => $FacebookPageLike,
                                   'LinkedIn' => $LinkedIn,
                                   'Address' => $Address,
                         ));

                    }else{ 
                        $input['imagename'] = time().'.'.$image->getClientOriginalExtension();
                        $destinationPath = public_path('/images');
                        $image->move($destinationPath, $input['imagename']);
					 
			if($image!=''){
                            $img=$input['imagename'];
                            DB::table('hrusers')
                            ->where('UserId', $id)
                            ->update(array('FirstName' => $FirstName,
                                   'LastName' => $LastName,
                                   'PersonalEmail' => $PersonalEmail,
                                   'NormalizedUserName' => $NormalizedUserName,
                                   'PhoneNumber' => $PhoneNumber,
                                   'ReferencePhoneNumber' => $ReferencePhoneNumber,
                                   'BirthDate' => $BirthDate,
                                   'AnniversaryDate' => $AnniversaryDate,
                                   'EmergencyContactPerson' => $EmergencyContactPerson,
                                   'BloodGroup' => $BloodGroup,
                                   'FacebookId' => $FacebookId,
                                   'FacebookPageLike' => $FacebookPageLike,
                                   'LinkedIn' => $LinkedIn,
                                   'Address' => $Address,
                                   'ProfileImage'=>$img,
                                   ));

			}else{
                                DB::table('hrusers')
                  ->where('UserId', $id)
                  ->update(array('FirstName' => $FirstName,
                         'LastName' => $LastName,
                         'PersonalEmail' => $PersonalEmail,
                         'NormalizedUserName' => $NormalizedUserName,
                         'PhoneNumber' => $PhoneNumber,
                         'ReferencePhoneNumber' => $ReferencePhoneNumber,
                         'BirthDate' => $BirthDate,
                         'AnniversaryDate' => $AnniversaryDate,
                         'EmergencyContactPerson' => $EmergencyContactPerson,
                         'BloodGroup' => $BloodGroup,
                         'FacebookId' => $FacebookId,
                         'FacebookPageLike' => $FacebookPageLike,
                         'LinkedIn' => $LinkedIn,
                         'Address' => $Address,
                         ));

                             }
			}
                    //--------------------------------------------------
                     
                     
                    
                      /* --- Histroy logs --- */
                     $date = date('Y-m-d H:i:s');
                     $historyusers = DB::select('select * from hremployeehistory where UserId = ?',[$id]);
                     if(!empty($historyusers)){
                         //update
                        DB::table('hremployeehistory')
                        ->where('UserId', $id)
                        ->update(array('PersonalEmail' => $PersonalEmail,
                        'FirstName' => $FirstName,
                        'LastName' => $LastName,
                        'PhoneNumber' => $PhoneNumber,
                        'ReferencePhoneNumber' => $ReferencePhoneNumber,
                        'BirthDate' => $BirthDate,
                        'AnniversaryDate' => $AnniversaryDate,
                        'EmergencyContactNo' => $EmergencyContactPerson,
                        'BloodGroup' => $BloodGroup,
                        'FacebookId' => $FacebookId,
                        'FacebookPageLike' => $FacebookPageLike,
                        'LinkedIn' => $LinkedIn,
                        'Address' => $Address,
                        'Created' => $date,
                        'ProfileImage' => $img,                      
                         ));
                     }else{
                         //insert
                         
                         DB::table('hremployeehistory')->insert(
                        array('PersonalEmail' => $PersonalEmail,
                         'FirstName' => $FirstName,
                         'LastName' => $LastName,
                         'PhoneNumber' => $PhoneNumber,
                         'ReferencePhoneNumber' => $ReferencePhoneNumber,
                         'BirthDate' => $BirthDate,
                         'AnniversaryDate' => $AnniversaryDate,
                         'EmergencyContactNo' => $EmergencyContactPerson,
                         'BloodGroup' => $BloodGroup,
                         'FacebookId' => $FacebookId,
                         'FacebookPageLike' => $FacebookPageLike,
                         'LinkedIn' => $LinkedIn,
                         'Address' => $Address,
                         'Created' => $date,
                         'ProfileImage' => $img, 
                         'UserId' => $id,
                          )
                         );
                     }
                     
                     DB::table('hremployeehistryold')->insert(
                        array('PersonalEmail' => $PersonalEmail,
                         'FirstName' => $FirstName,
                         'LastName' => $LastName,
                         'PhoneNumber' => $PhoneNumber,
                         'ReferencePhoneNumber' => $ReferencePhoneNumber,
                         'BirthDate' => $BirthDate,
                         'AnniversaryDate' => $AnniversaryDate,
                         'EmergencyContactNo' => $EmergencyContactPerson,
                         'BloodGroup' => $BloodGroup,
                         'FacebookId' => $FacebookId,
                         'FacebookPageLike' => $FacebookPageLike,
                         'LinkedIn' => $LinkedIn,
                         'Address' => $Address,
                         'Created' => $date,
                         'ProfileImage' => $img, 
                         'UserId' => $id,
                          )
                         );
                     
                     
                    /* ---- Ended Code ---- */
                     

                    $uses = DB::select('select * from hrusers where UserId = ?',[$id]);
                       foreach($uses as $us){
                        $img=$us->ProfileImage;
                        if($img!=''){
                            $i='http://trackit.cueserve.com/trackit-api/public/images/'.$img;
                        }else{$i='http://trackit.cueserve.com/trackit-api/public/images/dummy.jpg';}
                     $users=array(
                         'Id'=>$us->Id,
                         'Email'=>$us->Email,
                         'FirstName'=>$us->FirstName,
                         'LastName'=>$us->LastName,
                         'PersonalEmail'=>$us->PersonalEmail,
                         'NormalizedUserName'=>$us->NormalizedUserName,
                         'ReferencePhoneNumber'=>$us->ReferencePhoneNumber,
                         'PhoneNumber'=>$us->PhoneNumber,
                         'BirthDate'=>$us->BirthDate,
                         'AnniversaryDate'=>$us->AnniversaryDate,
                         'EmergencyContactPerson'=>$us->EmergencyContactPerson,
                         'BloodGroup'=>$us->BloodGroup,
                         'FacebookId'=>$us->FacebookId,
                         'FacebookPageLike'=>$us->FacebookPageLike,
                          'LinkedIn'=>$us->LinkedIn,
                         'Address'=>$us->Address,
                          'UserStatus'=>$us->UserStatus,
                         'UserId'=>$us->UserId,
                          'ProfileImage'=>$i,
                         );

                   // print_r();
                       }
                   $error=array('Status'=>'200', 'Message'=>'Update details Successfully','Userdetail'=>$users);
                 }else{
                    $error=array('Status'=>'400', 'Message'=>'User not exist');
                 }
               

               }else{
                  $error=array('Status'=>'400', 'Message'=>'Invalid Email Id formate');
               }  
          }else{
            $error=array('Status'=>'400', 'Message'=>'All fields required');
          }
        }else{
          $error=array('Status'=>'400', 'Message'=>'Unauthenticate Users');
        }
        return response()->json($error, 201);   
    }

    public function historyLog(Request $request,$id){
      
        //$accesskey = $request->input('AccessKey');
       
        //$tag = new Appmodel();
        //$chk =$tag->authenticateAPI($accesskey);
        $chk='1';
        if($chk=='1'){
 
         $users = DB::table('hremployeehistryold')->where('UserId',$id)->orderBy('Id', 'desc')->get();
          if(empty($users)){
              $error=array('Status'=>'200', 'Message'=>'Empty logs','Userdetails'=>$users);
          }else{
             
              foreach($users as $us){
                  
                        $img=$us->ProfileImage;
                        if($img!=''){
                            $i='http://trackit.cueserve.com/trackit-api/public/images/'.$img;
                        }else{$i='http://trackit.cueserve.com/trackit-api/public/images/dummy.jpg';}
                         $usersde[]=array(
                         'UserId'=>$us->UserId,
                         'PersonalEmail'=>$us->PersonalEmail,
                         'FirstName'=>$us->FirstName,
                         'LastName'=>$us->LastName,
                         'PhoneNumber'=>$us->PhoneNumber,
                         'ReferencePhoneNumber'=>$us->ReferencePhoneNumber,
                         'BirthDate'=>$us->BirthDate,
                         'AnniversaryDate'=>$us->AnniversaryDate,
                         'EmergencyContactNo'=>$us->EmergencyContactNo,
                         'BloodGroup'=>$us->BloodGroup,
                         'FacebookId'=>$us->FacebookId,
                         'FacebookPageLike'=>$us->FacebookPageLike,
                         'LinkedIn'=>$us->LinkedIn,
                         'Address'=>$us->Address,
                          'Created'=>$us->Created,
                          'ProfileImage'=>$i,
                         );
                       
                   // print_r();
                       }
               $error=array('Status'=>'200', 'Message'=>'User logs','Userdetails'=>$usersde);
          
          }
          }else{
          $error=array('Status'=>'400', 'Message'=>'Unauthenticate Users');
        }
         
       return response()->json($error, 201);
    }

   public function historyLogAllusers(Request $request){

        //$accesskey = $request->input('AccessKey');
       
       // $tag = new Appmodel();
        //$chk =$tag->authenticateAPI($accesskey);
        $chk='1';
        if($chk=='1'){

         $users =DB::select('select * from hremployeehistryold ');

          if(empty($users)){
              $error=array('Status'=>'200', 'Message'=>'Empty logs','Userdetails'=>$users);
          }else{
             
              foreach($users as $us){
                 
                        $img=$us->ProfileImage;
                        if($img!=''){
                            $i='http://trackit.cueserve.com/trackit-api/public/images/'.$img;
                        }else{$i='http://trackit.cueserve.com/trackit-api/public/images/dummy.jpg';}
                         $usersde[]=array(
                         'UserId'=>$us->UserId,
                         'PersonalEmail'=>$us->PersonalEmail,
                         'FirstName'=>$us->FirstName,
                         'LastName'=>$us->LastName,
                         'PhoneNumber'=>$us->PhoneNumber,
                         'ReferencePhoneNumber'=>$us->ReferencePhoneNumber,
                         'BirthDate'=>$us->BirthDate,
                         'AnniversaryDate'=>$us->AnniversaryDate,
                         'EmergencyContactNo'=>$us->EmergencyContactNo,
                         'BloodGroup'=>$us->BloodGroup,
                         'FacebookId'=>$us->FacebookId,
                         'FacebookPageLike'=>$us->FacebookPageLike,
                         'LinkedIn'=>$us->LinkedIn,
                         'Address'=>$us->Address,
                          'Created'=>$us->Created,
                          'ProfileImage'=>$i,
                         );
                       
                   // print_r();
                       }
               $error=array('Status'=>'200', 'Message'=>'User logs','Userdetails'=>$usersde);
          
          }
          }else{
          $error=array('Status'=>'400', 'Message'=>'Unauthenticate Users');
        }
         return response()->json($error, 201);   
   } 



  }












