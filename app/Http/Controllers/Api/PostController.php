<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mail;
use Redirect;
use App\Mail\EmailVerificationMail;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    //Signup Function that register user and generate email verification code (token) and
    // insert that in database 
    public function signup(StorePostRequest $request)
    {
          
        $request->merge([  'password' => Hash::make($request->newPassword)]);
        $verify=false;
        $picture=$request->file('picture')->store('apiDocs');
        $user=post::create([
            'name'=>$request->input('name'),
            'email'=>$request->input('email'),
            'gender'=>$request->input('gender'),
            'age'=>$request->input('age'),
            'password'=>$request->input('password'),
            'picture'=>$picture,
            'email_verification_code'=>Str::random(40),
            'verified'=>$verify]);
        $message;
        $status;
        $value;
        if($user)
        {
            $message="Account Created Successfully, please check your mail for Email Verification Link";
            $value=200;
            $status=true;
        }
        else
        {
            $message="Signup Failed Try Again";
            $value=404;
            $status=false;
        }
     Mail::to($request->email)->send(new EmailVerificationMail($user));   
     return response()->json(['status'=>$status,'message'=>$message],$value);  
    }


    //Verify_email will send  verification mail to the user and update the time of email verification
    //and verified 
    public function verify_email($verification_code)
    {
        $user=post::where('email_verification_code',$verification_code)->first();
        $status;
        $route;
        $message;
        if(!$user)
        {
            $status='error';
            $message='Invalid URL';
        }
        else
        {
            if($user->email_verified_at)
            {

                $status='error';
                $message='Email Already Exist';
            }
            else
            {
                $user->update([
                    'email_verified_at'=>\Carbon\carbon::now(),
                    'verified'=>true
                ]);
                $status='Success';
                $message='Email Successfully Verified';
               
            }
        }
        return \redirect(route('home'))->with($status,$message);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */

    //login using Email and password
    public function login(Request $request)
    {
        $email=$request->input('email');
       // $user=post::where($email);
        $user = DB::table('posts')->where('email', $email)->select('password', 'email')->first();
        $password=$request->password;
        $pass;
        $status;
        $route;
        $message;
        if($user)
        {
            $pass=$user->password;
            $answer=Hash::check($request->password, $pass);
            if($answer)
           {
            $value=200;
            $status='Success';
            $message='Account Login Successfully';

           }
           else
           {
            $value=200;
            $status='error';
            $message='Password not matched';
           }
        }
        else
        {
            $status='error';
            $message='Email Not Exist';
            $value=404;
        }
        return response()->json(['status'=>$status,'message'=>$message],$value); 
        
    }
    //reset password
    public function reset( $email)
    {
        $user=post::where($email);
        if(count($user) < 1)
        {
            $status="error";
            $message="User does not exist";
        }
        $tokenData = DB::table('posts')->where('email', $email)->first();

        if ($this->sendResetEmail($request->email, $tokenData->email_verification_code)) 
        {
            $status="error";
            $message="Emaill Limk has been send";
        } 
        else
        {
            $status="error";
            $message="Network error occur";
            
        }
        return \redirect(route('home'))->with($status,$message);  
    }
    // send reset email
    private function sendResetEmail($email, $token)
    {

        $user = DB::table('posts')->where('email', $email)->select('name', 'email')->first();
        $link = config('base_url') . 'password/reset/' . $token . '?email=' . urlencode($user->email);

        try 
        {
           return true;
        } 
        catch (\Exception $e) 
        {
          return false;
        }
}
    // forget password code 
    public function forget(Request $request)
    {
        $email=$request->input('email');
        $user=post::where($email);
        if($user)
        {
            $status="success";
            $message="email has been send";

           //return Redirect::action('PostController@reset');
        
            
        }
        else
        {
            $status="error";
            $message="Account not exist";
            
        }
        return \redirect(route('home'))->with($status,$message);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }
}
