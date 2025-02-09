<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDevices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $fcmToken = $request->playerId;
            $email = $request->email;
            $password = $request->password;

            if (empty($email))
                throw new \Exception("Lütfen e-posta adresinizi girin.");
            if (empty($password))
                throw new \Exception("Lütfen parolanızı girin.");


            $user = User::where("email", $email)->first();

            if (!$user || !Hash::check($request->password, $user->password))
                throw new \Exception("E-posta veya parola hatalı");

            $token = $user->createToken("app_token")->plainTextToken;

            if ($fcmToken != null)
            {
                $userDevices = UserDevices::where("user_id", $user->id)->first();
                if($userDevices != null) {
                    $userDevices->player_id = $fcmToken;
                    $userDevices->save();
                } else {
                    $userDevices = UserDevices::create([
                        'user_id' => $user->id,
                        'player_id' => $fcmToken
                    ]);
                }
            }

            $this->output["login"] = true;
            $this->output["user"] = $user;
            $this->output["token"] = $token;

        }catch (\Exception $exception){
            $this->output = [
                'error' => 1,
                'msg' => $exception->getMessage()
            ];
        }

        return $this->output;
    }

    public function register(Request $request)
    {
        try {
            $name = $request->name;
            $phone_number = $request->phone_number;
            $email = $request->email;
            $blood_type = $request->blood_type;
            $password = $request->password;

            // location
            $lat    = $request->lat;
            $lng    = $request->lng;
            $city   = $request->city;

            if (empty($name))
                throw new \Exception("Lütfen isim soyisim girin.");
            if (empty($blood_type))
                throw new \Exception("Lütfen kan grubunuzu girin.");
            if (empty($email))
                throw new \Exception("Lütfen e-posta adresi girin.");
            if (empty($phone_number))
                throw new \Exception("Lütfen telefon numarası girin.");
            if (empty($password))
                throw new \Exception("Lütfen parola girin.");

            $user_check = User::where("email", $email)->count() == 0;
            if (!$user_check)
                throw new \Exception("Bu e-posta adresi kullanılıyor.");

            $user_check = User::where("phone", $phone_number)->count() == 0;
            if (!$user_check)
                throw new \Exception("Bu telefon numarası kullanılıyor.");

            $user                   = new User();
            $user->name             = $name;
            $user->email            = $email;
            $user->phone            = $phone_number;
            $user->blood_type_id    = $blood_type;
            $user->password         = bcrypt($password);
            // location
            $user->latitude         = $lat;
            $user->longitude        = $lng;
            $user->city             = $city;
            $save                   = $user->save();

            if (!$save)
                throw new \Exception("Kayıt gerçekleştirilemedi.");

            $token = $user->createToken("app_token")->plainTextToken;
            if(!$token)
                throw new \Exception("Kayıt gerçekleştirilemedi.");

            $user = User::find($user->id);

            $this->output["login"] = true;
            $this->output["user"] = $user;
            $this->output["token"] = $token;

        }catch (\Exception $exception){
            $this->output = [
                'error' => 1,
                'msg' => $exception->getMessage()
            ];
        }

        return $this->output;
    }

    public function loginCheck(Request $request)
    {
        try {
            $fcmToken = $request->playerId;
            $user = Auth::guard('sanctum')->user();
            if ($user == null)
                throw new \Exception("Unauthenticated.");

            $token = $user->createToken("app_token")->plainTextToken;
            $token_id = explode("|", $token)[0];
            $user->tokens()->where("id", "!=", $token_id)->delete();

            if ($fcmToken != null)
            {
                $userDevices = UserDevices::where("user_id", $user->id)->first();
                if($userDevices != null) {
                    $userDevices->player_id = $fcmToken;
                    $userDevices->save();
                } else {
                    $userDevices = UserDevices::create([
                        'user_id' => $user->id,
                        'player_id' => $fcmToken
                    ]);
                }
            }

            $this->output = [
                "status" => 1,
                "user" => $user,
                "token" => $token,
            ];

        }catch (\Exception $exception){
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return $this->output;
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return [
            "status" => 1,
            "message" => "Başarıyla çıkış yapıldı",
        ];
    }
}
