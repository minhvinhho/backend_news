<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\HandleForgetPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserChangePassRequest;
use App\Mail\RegistrationMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\ForgetPasswordMail;

class AuthController extends Controller
{
    // Register
    /** 
     * @OA\Post( 
     * path="/api/register", 
     * summary="Register",
     * description="Login by email, password",
     * operationId="authLogin",
     * tags={"Register"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name","role_id","email","password","avatar_link"},
     * @OA\Property(property="name", type="string", format="text", example="minhcute"),
     * @OA\Property(property="role_id", type="string", format="text", example="4"),
     * @OA\Property(property="email", type="string", format="email", example="hominh4078@gmail.com"),
     * @OA\Property(property="password", type="string", format="password", example="12345678"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="12345678"),
     * @OA\Property(property="avatar_link", type="string", format="text", example="abc.com")
     *  ),
     * ),
     * @OA\Response(
     * response=200,
     * description="successful operation",
     * @OA\Response(response=400, description="Bad request"),
     * @OA\Response(response=404, description="Resource Not Found"),
     *    )
     *  )
     * )
     **/

    public function register(UserRegisterRequest $request)
    {
        $validated = $request->validated();
        $validated["password"] = bcrypt($validated["password"]);
        $user = User::create($validated);
        return response()->json(["user" => $user, 'msg' => 'Sign up successfully'], 200);
    }
    
    public function Registration(RegistrationRequest $request)
    {
        $data = $request->validated();
        $details = ['title' =>$data['name'],'token' => Str::random(60)];
        $data['password'] = Hash::make($data['password']);
        $data['token']= $details['token'];
        $user = User::create($data);
        Mail::to($data['email'])->send(new RegistrationMail($details));
        return response()->json(['status'=>true,'message'=>'Đăng ký thành công, vui lòng xác thực gmail','user'=>$user],200);
    }

    public function userVerification($token){
        $data = User::where('token',$token)->first();
        if($data){
            $data->email_verified_at=Carbon::now()->toDateTimeString();
            $data->token='';
            $data->save();
            return response()->json(['status'=>true,'message'=>'Xác thực thông tin thành công',],200);
        }else{
            return response()->json(['status'=>false,'message'=>'Bạn đã xác thực thông tin trong quá khứ'],211);
        }
    }

    // Login
    /** 
     * @OA\Post( 
     * path="/api/login", 
     * summary="Sign in",
     * description="Login by email, password",
     * operationId="authLogin",
     * tags={"LOGIN"},
     * @OA\RequestBody(
     * required=true,
     * description="Pass user credentials",
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     * @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *  ),
     * ),
     * @OA\Response(
     * response=422,
     * description="Wrong credentials response",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *    )
     *  )
     * )
     **/
    public function Login(LoginRequest $request){
        $data = $request->validated();
        if(auth()->attempt($data)){
            $user = Auth::user();
            $token = $user->createToken("authToken")->accessToken;
            return ($user['email_verified_at']) ? response()->json(['status'=>true,'user'=>$user,'message'=>'Đăng nhập thành công','token'=>$token],200) : response()->json(['status'=>true,'user'=>$user,'messageEmail'=>'Bạn chưa xác thực gmail','token'=>$token],200);
        }else{
            return response()->json(['status'=>false,'message_err'=>'Sai tài khoản hoặc mật khẩu'],211);}
    }

    public function checkInfo(){
        $user = Auth::user();
        return response()->json ($user) ? (['user'=>$user]) : (['status'=>false]);
    }

    public function resendEmailVerification(){
        $user = Auth::user();
        $details = ['title'=> $user['name'],'token' => $user['token']];
        Mail::to($user['email'])->send(new RegistrationMail($details));
        return response()->json(['status'=>true,'message'=>'Gửi lại mail thành công'],200);
    }
    public function sendEmailForgetPassword(ForgetPasswordRequest $request){
        $data = $request->validated();
        $user = User::where('email',$data['email'])->first();
        if($user){
            if(strlen($user['token'])==60){
                return response()->json(['status' => false,'message' => ['email'=>['Vui lòng xác thực tài khoản ở Gmail trước.']]],211);
            }else{
                $details = ['title' =>$user['name'],'token' => Str::random(70),];
                $token = User::where('token',$user['token'])->update(['token'=>$details['token']]);
                Mail::to($user['email'])->send(new ForgetPasswordMail($details));
                return response()->json(['status' => true,'message' => 'Chúng tôi đã gửi mail đặt lại mật khẩu vào mail của bạn'],200);
            }
        }else{
            return response()->json(['status' => false,'message' => ['email'=>['Email bạn nhập không tồn tại']]],211);
        }
    }
    public function handleForgetPassword(HandleForgetPasswordRequest $request){
        $data = $request->validated();
        $user = User::where('token',$request->token)->update(['token'=>' ','password'=>Hash::make($data['password'])]);
        return ($user) ? response()->json(['status'=>true],200) : response()->json(['status'=>false],211);
    }
    public function handleLoginGoogle(Request $request){
        $data = User::where('email',$request->email)->first();
        if($data){
            $token = $data->createToken("authToken")->accessToken;
            return response()->json(['status'=>true,'user'=>$data,'token'=>$token],200);
        }else{
            $user = User::create($request->all());
            $token = $user->createToken("authToken")->accessToken;
            return response()->json(['status'=>true,'user'=>$user,'token'=>$token],200);
        }
    }
    public function getAvatar($name){
        $data = User::where('name',$name)->first();
        return response()->json(['data'=>true,'user'=>$data],200);
    }
    public function updateAvatar(Request $request){
        $data = User::where('id',$request->id)->update(['avatar_link'=>$request->avatar_link]);
        return response()->json(['status'=>true,'data'=>$request->avatar_link],200);
    }
    public function RandomlySelectAuthor(){
        $Authors = User::all()->random(6);
        return response()->json($Authors,200);
    }

    //sign in
    public function signin(UserLoginRequest $request)
    {
        $validated = $request->validated();

        if (auth()->attempt($validated)) {
            $user = auth()->user();
            $token = $user->createToken("app")->accessToken;
            return response()->json(['user' => $user, 'token' => $token, 'msg' => "Successful login"], 200);
        } else {
            return response()->json(['msg' => "Login failed"], 211);
        }
    }
    // Get me
     /**
     * @OA\Get(
     *   path="/api/me",
     *   tags={"Get me"},
     *   operationId="logout",
     *   summary="Logout",
     *   security={{"Bearer":{}}},
     *   @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *  )
     */
    public function getMe()
    {
        $user = auth()->user();
        return response()->json(['user' => $user], 200);
    }

    // Change pass
    /** 
     * @OA\Post( 
     * path="/api/change_password", 
     * summary="Change password",
     * description="Login by email, password",
     * operationId="authLogin",
     * tags={"Change password"},
     * @OA\RequestBody(
     * required=true,
     * description="Pass user credentials",
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     * @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     * @OA\Property(property="persistent", type="boolean", example="true"),
     *  ),
     * ),
     * @OA\Response(
     * response=422,
     * description="Wrong credentials response",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *    )
     *  )
     * )
     **/
    public function changePassword(UserChangePassRequest $request)
    {

        $validated = $request->validated();
        $user = auth()->user();
        $isUpdate = User::where('id', $user->id)
           // ->where('password', bcrypt($validated["old_password"]))
            ->update(["password" => bcrypt($validated["password"])]);
        if ($isUpdate) {
            return response()->json(['msg' => "Change password successfully"], 200);
        } else {
            return response()->json(['msg' => "Change password failed"], 211);
        }
    }

    //Log out
    /**
     * @OA\Post(
     *   path="/logout",
     *   tags={"Log Out"},
     *   operationId="logout",
     *   summary="Logout",
     *   security={{"Bearer":{}}},
     *   @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *  )
     **/
    public function logout(Request $request)
    {
        $token = str_after($request->header('Authorization'), 'Bearer ');
        $user = User::where('token', $token)->first();

        if ($user) {
            $user->update(['token' => null]);
            return response()->json(['msg' => 'User Logged Out'], 200);
        } else {
            return response()->json(['msg' => 'User not found'], 404);
        }
    }
}
