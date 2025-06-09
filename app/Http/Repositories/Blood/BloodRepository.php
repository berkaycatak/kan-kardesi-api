<?php

namespace App\Http\Repositories\Blood;

use App\Http\Repositories\Repository;
use App\Models\BloodCompatibility;
use App\Models\BloodRequest;
use App\Models\BloodType;

class BloodRepository extends Repository
{
    /**
     * Yeni kan bağış ihtiyacı oluşturur..
     * @param int $required_blood_type_id
     * @param int $units_needed
     * @param int $city
     * @param String $description
     * @return BloodRequest|int[]|mixed
     */
    public function sendRequest(
        int $required_blood_type_id,
        int $units_needed,
        int $city,
        String $description
    ): mixed
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

    /**
     * Kullanıcının kan verebileceği açık ilanları getirir.
     * @param int $city
     * @param int $blood_type_id
     * @return mixed
     */
    public function search(int $city, int $blood_type_id): mixed
    {
        try {
            $compatibleBloodTypes = BloodCompatibility::where('donor_blood_type_id', $blood_type_id)
                ->pluck('recipient_blood_type_id');

            if ($compatibleBloodTypes->isEmpty()) {
                throw new \Exception("Bu kan grubu için uygun alıcı bulunamadı.");
            }

            if (empty($city)) {
                throw new \Exception("Şehir alanı boş bırakılamaz.");
            }

            if ($city < 1 || $city > 81) {
                throw new \Exception("Geçersiz şehir bilgisi.");
            }

            $bloodRequests = BloodRequest::whereIn('required_blood_type_id', $compatibleBloodTypes)
                ->where('city', $city)
                ->where('status', 'pending')
                ->get();

            //if ($bloodRequests->isEmpty()) {
            //    throw new \Exception("Bu şehirde uygun bir kan talebi bulunamadı.");
            //}

            $this->output["blood_requests"] = $bloodRequests;
            return $this->output;

        } catch (\Exception $exception) {
            $this->output["error"] = 1;
            $this->output["msg"] = $exception->getMessage();
            return $this->output;
        }
    }


    /**
     * @return mixed
     */
    public function getBloodTypes(): mixed
    {
        try {
            $this->output["blood_types"] = BloodType::get();
            return $this->output;

        } catch (\Exception $exception) {
            $this->output["error"] = 1;
            $this->output["msg"] = $exception->getMessage();
            return $this->output;
        }
    }

}
