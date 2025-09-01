<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use App\Models\TrafficAccident;

class AccidentAnalysisService
{
    public function generateHandlingPlan(TrafficAccident $accident): string
    {
        $prompt = "Buatkan rencana penanganan untuk kecelakaan lalu lintas berikut:\n".
                  "- Jenis tabrakan: {$accident->jenis_tabrakan}\n".
                  "- Jumlah korban meninggal: {$accident->korban_md}\n".
                  "- Jumlah korban luka: {$accident->korban_ll}\n".
                  "- Waktu kejadian: {$accident->waktu_kecelakaan}\n".
                  "- Jenis kendaraan: {$accident->jenis_kendaraan}\n".
                  "- Usia korban: {$accident->usia_korban}\n".
                  "- Jenis pekerjaan korban: {$accident->jenis_pekerjaan}\n".
                  "Buatkan penanganan yang tepat dan terstruktur.";
                  $response = OpenAI::chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);
                

        return trim($response['choices'][0]['message']['content']);
    }
}
