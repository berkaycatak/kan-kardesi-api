<?php

namespace App\Http\Repositories\Ad;

use App\Http\Repositories\Repository;
use App\Models\BloodType;
use App\Models\City;
use App\Models\User;
use App\Models\UserDevices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRepository extends Repository
{

    /**
     * @param String $email
     * @param String $password
     * @param String|null $fcmToken
     * @return int[]|mixed
     */
    public function login(
        String $email,
        String $password,
        ?String $fcmToken
    ): mixed
    {
        try {
            $user = User::where("email", $email)->first();

            if (!$user || !Hash::check($password, $user->password))
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

            $this->output["status"] = true;
            $this->output["user"] = $user;
            $this->output["token"] = $token;

        }catch (\Exception $exception){
            $this->output['error'] = 1;
            $this->output['msg'] = $exception->getMessage();
        }
        return $this->output;
    }

    /**
     * @param String $name
     * @param String $phone_number
     * @param String $email
     * @param int $blood_type
     * @param String $password
     * @param String|null $lat
     * @param String|null $lng
     * @param int|null $city
     * @return mixed
     */
    public function create(
        String $name,
        String $phone_number,
        String $email,
        int $blood_type,
        String $password,
        ?String $lat,
        ?String $lng,
        ?int $city
    ): mixed
    {
        try {
            $user_check = User::where("email", $email)->count() == 0;
            if (!$user_check)
                throw new \Exception("Bu e-posta adresi kullanılıyor.");

            $user_check = User::where("phone", $phone_number)->count() == 0;
            if (!$user_check)
                throw new \Exception("Bu telefon numarası kullanılıyor.");

            // check blood type
            if (!empty($blood_type_id))
            {
                $check_blood_type = BloodType::find($blood_type_id);
                if ($check_blood_type == null) {
                    throw new \Exception("Kan grubu bulunamadı.");
                }
            }

            // check city
            if (!empty($city_id))
            {
                $city = City::find($city_id);
                if ($city == null) {
                    throw new \Exception("Şehir bulunamadı.");
                }
            }

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

            $this->output["status"] = true;
            $this->output["user"] = User::find($user->id);
            $this->output["token"] = $token;

        }catch (\Exception $exception){
            $this->output['error'] = 1;
            $this->output['msg'] = $exception->getMessage();
        }
        return $this->output;
    }

    public function splash(
        ?String $fcmToken
    )
    {
        try {
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

            $this->output["status"] = true;
            $this->output["user"] = $user;
            $this->output["token"] = $token;

        }catch (\Exception $exception){
            $this->output["error"] = 1;
            $this->output["msg"] = $exception->getMessage();
        }
        return $this->output;
    }

    /**
     * @return int[]|mixed
     */
    public function logout(): mixed
    {
        try {
            auth()->user()->tokens()->delete();
            $this->output["status"] = true;
            $this->output["msg"] = "Başrıyla çıkış yapıldı.";
        }catch (\Exception $exception){
            $this->output["error"] = 1;
            $this->output["msg"] = $exception->getMessage();
        }
        return $this->output;
    }

    /**
     * Update user information
     * @param String|null $name
     * @param String|null $email
     * @param String|null $phone_number
     * @param int|null $blood_type_id
     * @param int|null $city_id
     * @param String|null $last_donation_date
     * @return array
     */
    public function update(
        ?String $name,
        ?String $email,
        ?String $phone_number,
        ?int $blood_type_id,
        ?int $city_id,
        ?String $last_donation_date
    ): array
    {
        try {
            // check user
            $user = User::where("id", auth()->user()->id)->first();
            if ($user == null) {
                throw new \Exception("Kullanıcı bulunamadı.");
            }

            // check blood type
            if (!empty($blood_type_id))
            {
                $check_blood_type = BloodType::find($blood_type_id);
                if ($check_blood_type == null) {
                    throw new \Exception("Kan grubu bulunamadı.");
                }
            }

            // check city
            if (!empty($city_id))
            {
                $city = City::find($city_id);
                if ($city == null) {
                    throw new \Exception("Şehir bulunamadı.");
                }
            }

            // check phone number
            $check_phone_number = User::where("phone", $phone_number)->where("id", "!=", $user->id)->first();
            if ($check_phone_number != null) {
                throw new \Exception("Telefon numarası başka bir kullanıcı tarafından kullanılıyor.");
            }

            // check email adress
            $check_email = User::where("email", $email)->where("id", "!=", $user->id)->first();
            if ($check_email != null) {
                throw new \Exception("E-posta adresi başka bir kullanıcı tarafından kullanılıyor.");
            }

            if (!empty($name)) {
                $user->name = $name;
            }

            if (!empty($email)) {
                $user->email = $email;
            }

            if (!empty($phone_number)) {
                $user->phone = $phone_number;
            }

            if (!empty($blood_type_id)) {
                $user->blood_type_id = $blood_type_id;
            }

            if (!empty($last_donation_date)) {
                $user->last_donation_date = $last_donation_date;
            }

            if (!empty($city_id)) {
                $user->city = $city_id;
            }

            $status = $user->save();
            if (!$status) {
                throw new \Exception("Kullanıcı güncellenirken bir problem yaşandı.");
            }

            $this->output["user"] = $user;
        }catch (\Exception $exception){
            $this->output["error"] = 1;
            $this->output["msg"] = $exception->getMessage();
        }
        return $this->output;
    }

    /**
     * @param String $old_password
     * @param String $new_password
     * @return mixed
     */
    public function changePassword(
        String $old_password,
        String $new_password
    ): mixed
    {
        try {
            $user = User::where("id", auth()->user()->id)->first();
            if ($user == null) {
                throw new \Exception("Kullanıcı bulunamadı.");
            }

            if (!Hash::check($old_password, $user->password)) {
                throw new \Exception("Mevcut parolanızı yanlış girdiniz.");
            }

            $user->password = Hash::make($new_password);
            $status = $user->save();
            if (!$status) {
                throw new \Exception("Parola değiştirilirken bir problem yaşandı.");
            }

            $this->output["user"] = $user;
        }catch (\Exception $exception){
            $this->output["error"] = 1;
            $this->output["msg"] = $exception->getMessage();
        }
        return $this->output;
    }

    public function getProfile(
        int $id,
    ): mixed
    {
        try {
            $user = User::where("id", $id)
                ->with(["bloodType", "bloodRequests"])
                ->first();

            if ($user == null) {
                throw new \Exception("Kullanıcı bulunamadı.");
            }

            $this->output["user"] = $user;
        }catch (\Exception $exception){
            $this->output["error"] = 1;
            $this->output["msg"] = $exception->getMessage();
        }
        return $this->output;
    }


}
