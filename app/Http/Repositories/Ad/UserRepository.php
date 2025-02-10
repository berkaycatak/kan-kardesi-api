<?php

namespace App\Http\Repositories\Ad;

use App\Http\Repositories\Repository;
use App\Models\BloodType;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository extends Repository
{

    /**
     * Update user information
     * @param String|null $name
     * @param String|null $email
     * @param String|null $phone_number
     * @param int|null $blood_type_id
     * @param String|null $last_donation_date
     * @return array
     */
    public function update(
        ?String $name,
        ?String $email,
        ?String $phone_number,
        ?int $blood_type_id,
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
}
