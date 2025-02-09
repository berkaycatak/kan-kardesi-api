<?php

namespace App\Http\Repositories\Blood;

use App\Models\BloodRequest;
use App\Models\BloodType;

class BloodRepository
{
    private $output = [
        'error' => 0,
    ];

    public function sendRequest(
        int $required_blood_type_id,
        int $units_needed,
        int $city,
        String $description
    )
    {
        try {
            $check_blood_type = BloodType::find($required_blood_type_id);
            if ($check_blood_type == null) {
                throw new \Exception("Kan grubu bulunamadı.");
            }

            if ($units_needed <= 0) {
                throw new \Exception("Kan talebi için en az 1 ünite girmelisiniz.");
            }

            if ($units_needed > 5) {
                throw new \Exception("Kan talebi için en fazla 5 ünite girebilirsiniz.");
            }

            if (empty($city)) {
                throw new \Exception("Şehir alanı boş bırakılamaz.");
            }

            if (empty($description)) {
                throw new \Exception("Açıklama alanı boş bırakılamaz.");
            }

            $request = new BloodRequest();
            $request->user_id = auth()->id();
            $request->required_blood_type_id = $required_blood_type_id;
            $request->units_needed = $units_needed;
            $request->city = $city;
            $request->description = $description;
            $status = $request->save();
            if (!$status) {
                throw new \Exception("Kan talebi oluşturulurken bir problem yaşandı.");
            }

            $this->output["blood_request"] = $request;
            return $request;
        }catch (\Exception $exception){
            $this->output["error"] = 1;
            $this->output["msg"] = $exception->getMessage();
            return $this->output;
        }
    }
}
