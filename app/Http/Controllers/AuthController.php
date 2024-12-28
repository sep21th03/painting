<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Laravel\Sanctum\TransientToken;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\Api\Auth\ChangePasswordRequest;
use Illuminate\Support\Str;

class AuthController extends Controller
{
     /**
     * Handle user login.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * 
     * @OA\Post(
     *     path="/api/login",
     *     summary="User login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Đăng nhập thành công"),
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="redirect_url", type="string", example="/dashboard")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Xác thực thất bại"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Đã có lỗi xảy ra"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        if (Auth::check()) {
            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'Da dang nhap',
            //     'redirect_url' => '/',
            // ]);
            return redirect()->route('dashboard');
            // return redirect()->away('http://127.0.0.1:53293/index.html');
        }
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:8',
            ]);

            $credentials = $request->only('email', 'password');
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Thông tin đăng nhập không chính xác',
                ]);
            }

            $user = User::getByUsername($request->email);
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mật khẩu không hợp lệ',
                ]);
            }

            $existingToken = $user->tokens->first();
            $check_token = User::getByUsername($request->email);
            if ($existingToken && $check_token->remember_token != null) {
                $get_user = User::getByUsername($request->email);
                $tokenResult = $get_user->remember_token;
            } else {
                $tokenResult = $user->createToken('authToken')->plainTextToken;
                $save_token = User::updateToken($request->email, $tokenResult);
                if (!$save_token) {
                    throw new \Exception('Lỗi truy vấn cơ sở dữ liệu');
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Đăng nhập thành công',
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'redirect_url' => route('dashboard')
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Xác thực thất bại',
                'errors' => $validationException->errors()
            ], 422);
        } catch (\Exception $error) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã có lỗi xảy ra',
                'errors' => ['message' => $error->getMessage()]
            ], 500);
        }
    }
      /**
     * Handle user registration.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Post(
     *     path="/api/register",
     *     summary="User registration",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="repassword", type="string", example="password123"),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="phone", type="string", example="123456789"),
     *             @OA\Property(property="address", type="string", example="123 Main St")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Dang ky thanh cong"),
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Co loi xay ra"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|unique:users,email',
                'password' => 'required|min:8',
                'repassword' => 'required|min:8',
                'name' => 'required',
                'phone' => 'required|unique:users',
                'address' => 'required',
            ]);
            if (User::checkUsername($request->email)) {
                throw ValidationException::withMessages([
                    'email' => ['Email da ton tai'],
                ]);
            }
            if ($request->password != $request->repassword) {
                throw ValidationException::withMessages([
                    'password' => ['Password khong giong nhau'],
                ]);
            }
            $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($request->name) . '&background=random';

            $user = User::createUser(
                $request->email,
                $request->name,
                Hash::make($request->password),
                $request->address,
                $request->phone,
                $avatarUrl
            );
            $user->assignRole('member');
            Auth::login($user);
            $tokenResult = $user->createToken($user->id)->plainTextToken;
            return response()->json([
                'status' => 'success',
                'message' => 'Dang ky thanh cong',
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Co loi xay ra',
                'errors' => ['message' => $e->getMessage()]
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if ($user) {
                if (Auth::guard('sanctum')->check()) {
                    $accessToken = $user->currentAccessToken();

                    if (!($accessToken instanceof TransientToken)) {
                        $accessToken->delete();
                    }
                } else {
                    Auth::logout();
                }

                $user->remember_token = null;
                $user->save();
            }

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return response()->json([
                'message' => 'Đã đăng xuất thành công',
                'redirect' => route('auth.login')
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Có lỗi xảy ra khi đăng xuất', 'message' => $e->getMessage()], 500);
        }
    }

    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')
                ->redirect();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Không thể kết nối với Google. Vui lòng thử lại sau.');
        }
    }



    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]
            );

            if (!$user->google_id) {
                $user->google_id = $googleUser->getId();
                $user->save();
            }

            // Đăng nhập user
            Auth::login($user, true);

            return redirect()->intended('/dashboard')
                ->with('success', 'Đăng nhập thành công!');
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Đã có lỗi xảy ra trong quá trình đăng nhập. Vui lòng thử lại.');
        }
    }


    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();
        if (!Hash::check($data['current_password'], $user->password)) {
            return jsonResponse(2, message: "Mật khẩu cũ không chính xác.");
        }
        $user->update(['password' => Hash::make($data['password'])]);
        if ($user) {
            $user->tokens()->delete();
            return jsonResponse(0, message: "Thay đổi mật khẩu thành công.");
        } else {
            return jsonResponse(2, message: "Có lỗi xảy ra, vui lòng thử lại sau.");
        }
    }
    function sendResetLinkEmail(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            return jsonResponse(1, message: "Không tìm thấy người dùng");
        }
        $status = Password::sendResetLink([
            'email' => $user->email,
        ]);
        \Log::info($status);

        if ($status == Password::RESET_LINK_SENT) {
            return jsonResponse(0, message: "Yêu cầu đã được gửi");
        }
    }
    function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'token' => 'required',
            "password_confirmation" => 'required|min:6',
        ]);
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );
        return jsonResponse($status === Password::PASSWORD_RESET ? 0 : 1, $status);
    }
}
