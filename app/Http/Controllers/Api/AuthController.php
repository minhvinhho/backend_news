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
        * operationId="Register",
        * tags={"Auth"},
        * summary="User Register",
        * description="User Register here",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"name","role_id","email", "password", "password_confirmation","avatar_link"},
        *               @OA\Property(property="name", type="text"),
        *               @OA\Property(property="role_id", type="text"),
        *               @OA\Property(property="email", type="text"),
        *               @OA\Property(property="password", type="password"),
        *               @OA\Property(property="password_confirmation", type="password"),
        *               @OA\Property(property="avatar_link", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Register Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Register Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Resource Not Found"),
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
        return response()->json(['status'=>true,'message'=>'????ng k?? th??nh c??ng, vui l??ng x??c th???c gmail','user'=>$user],200);
    }

    public function userVerification($token){
        $data = User::where('token',$token)->first();
        if($data){
            $data->email_verified_at=Carbon::now()->toDateTimeString();
            $data->token='';
            $data->save();
            return response()->json(['status'=>true,'message'=>'X??c th???c th??ng tin th??nh c??ng',],200);
        }else{
            return response()->json(['status'=>false,'message'=>'B???n ???? x??c th???c th??ng tin trong qu?? kh???'],211);
        }
    }

    // Login
     /**
        * @OA\Post(
        * path="/api/signin",
        * operationId="authLogin",
        * tags={"Auth"},
        * summary="User Login",
        * description="Login User Here",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"email", "password"},
        *               @OA\Property(property="email", type="email"),
        *               @OA\Property(property="password", type="password")
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Login Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Login Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Resource Not Found"),
        * )
        **/
    public function Login(LoginRequest $request){
        $data = $request->validated();
        if(auth()->attempt($data)){
            $user = Auth::user();
            $token = $user->createToken("authToken")->accessToken;
            return ($user['email_verified_at']) ? response()->json(['status'=>true,'user'=>$user,'message'=>'????ng nh???p th??nh c??ng','token'=>$token],200) : response()->json(['status'=>true,'user'=>$user,'messageEmail'=>'B???n ch??a x??c th???c gmail','token'=>$token],200);
        }else{
            return response()->json(['status'=>false,'message_err'=>'Sai t??i kho???n ho???c m???t kh???u'],211);}
    }

    public function checkInfo(){
        $user = Auth::user();
        return response()->json ($user) ? (['user'=>$user]) : (['status'=>false]);
    }

    public function resendEmailVerification(){
        $user = Auth::user();
        $details = ['title'=> $user['name'],'token' => $user['token']];
        Mail::to($user['email'])->send(new RegistrationMail($details));
        return response()->json(['status'=>true,'message'=>'G???i l???i mail th??nh c??ng'],200);
    }
    public function sendEmailForgetPassword(ForgetPasswordRequest $request){
        $data = $request->validated();
        $user = User::where('email',$data['email'])->first();
        if($user){
            if(strlen($user['token'])==60){
                return response()->json(['status' => false,'message' => ['email'=>['Vui l??ng x??c th???c t??i kho???n ??? Gmail tr?????c.']]],211);
            }else{
                $details = ['title' =>$user['name'],'token' => Str::random(70),];
                $token = User::where('token',$user['token'])->update(['token'=>$details['token']]);
                Mail::to($user['email'])->send(new ForgetPasswordMail($details));
                return response()->json(['status' => true,'message' => 'Ch??ng t??i ???? g???i mail ?????t l???i m???t kh???u v??o mail c???a b???n'],200);
            }
        }else{
            return response()->json(['status' => false,'message' => ['email'=>['Email b???n nh???p kh??ng t???n t???i']]],211);
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
     *      path="/api/getme",
     *      operationId="getme",
     *      tags={"Auth"},
     *  security={{"passport": {}}},
     *      summary="Get me",
     *      description="Returns user login",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *     ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *  )
     **/
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
     * operationId="authLogout",
     * tags={"Auth"},
     * security={
     * {"passport": {}},
     * },
     * @OA\RequestBody(
     * required=true,
     * description="Pass user credentials",
     * @OA\JsonContent(
     * required={"password","password_confirmation"},
     * @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="PassWord12345"),
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
     *   path="/api/logout",
     *   tags={"Auth"},
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
