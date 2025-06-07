<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Ad\UserRepository;
use App\Http\Repositories\Blood\BloodRepository;
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


            $user_repository = new UserRepository();
            $response = $user_repository->login(
                email: $email,
                password: $password,
                fcmToken: $fcmToken
            );

            if ($response["error"] == 1)
                throw new \Exception($response["msg"]);

            $this->output["login"] = true;
            $this->output["user"] = $response["user"];
            $this->output["token"] = $response["token"];

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

            $user_repository = new UserRepository();
            $response = $user_repository->create(
                name: $name,
                phone_number: $phone_number,
                email: $email,
                blood_type: $blood_type,
                password: $password,
                lat: $lat,
                lng: $lng,
                city: $city
            );

            if ($response["error"] == 1)
                throw new \Exception($response["msg"]);

            $this->output["login"] = true;
            $this->output["user"] = $response["user"];
            $this->output["token"] = $response["token"];

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
        $blood_types = [];
        try {
            $blood_repository =  new BloodRepository();
            $blood_types =  $blood_repository->getBloodTypes()["blood_types"];
        }catch (\Exception $exception){
        }finally{
            $this->output["blood_types"] = $blood_types;
        }


        try {
            $fcmToken = $request->playerId;
            $user_repository = new UserRepository();
            $response = $user_repository->splash(
                fcmToken: $fcmToken
            );

            if ($response["error"] == 1)
                throw new \Exception($response["msg"]);

            $this->output["status"] = true;
            $this->output["user"] = $response["user"];
            $this->output["token"] = $response["token"];
        }catch (\Exception $exception){

            $this->output["status"] = false;
            $this->output["msg"] = $exception->getMessage();
        }

        return $this->output;
    }

    public function logout(Request $request)
    {
        try {
            $user_repository = new UserRepository();
            $response = $user_repository->logout();

            if ($response["error"] == 1)
                throw new \Exception($response["msg"]);

            $this->output["status"] = true;
            $this->output["msg"] = $response["msg"];
        }catch (\Exception $exception){
            $this->output["status"] = false;
            $this->output["msg"] = $exception->getMessage();
        }

        return $this->output;
    }
}
