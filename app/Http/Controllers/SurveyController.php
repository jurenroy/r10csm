<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Service;
use App\Models\SurveyLog;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\QRCodeErrorCorrectionLevel;

class SurveyController extends Controller
{
    // Show survey form
    public function survey_view()
    {
        $services = Service::all();
        return view('feedback-form')->with(['services' => $services]);
    }

    // Save survey response
    public function save_survey_answer(Request $request)
    {
        // Validate input
        $data = $request->validate([
            'service_id' => 'required|exists:services,id',
            'age' => 'required',
            'sex' => 'required',
            'region' => 'required',
            'client_type' => 'required',
            'agency_visited' => 'required',
            'cc1' => 'required',
            'cc2' => '',
            'cc3' => '',
            'sqd1' => 'required',
            'sqd2' => 'required',
            'sqd3' => 'required',
            'sqd4' => 'required',
            'sqd5' => 'required',
            'sqd6' => 'required',
            'sqd7' => 'required',
            'sqd8' => 'required',
        ]);

        $service = Service::findOrFail($data['service_id']);

        // Use a transaction to prevent duplicates
        $survey = DB::transaction(function () use ($data, $service) {

            // Lock the last row to prevent race conditions
            $latestSurveyLog = SurveyLog::lockForUpdate()->latest('created_at')->first();

            $control_number = $this->generateControlNo($latestSurveyLog);

            return SurveyLog::create(array_merge($data, [
                'control_no' => $control_number,
                'division_id' => $service->division_id,
            ]));
        });

        // Redirect to survey response page
        return redirect()->route('survey.show_response', $survey->control_no);
    }

    // Generate unique control number
    private function generateControlNo($latestSurveyLog)
    {
        $year = date('Y');
        $month = date('m');

        if (!$latestSurveyLog) {
            $sequence = 1;
        } else {
            $parts = explode('-', $latestSurveyLog->control_no);

            // OL-YYYY-MM-SEQ
            if ($parts[1] == $year && $parts[2] == $month) {
                $sequence = (int)$parts[3] + 1; // ✅ increment last sequence
            } else {
                $sequence = 1; // new month or year
            }
        }

        return sprintf("OL-%s-%s-%s",
            $year,
            str_pad($month, 2, '0', STR_PAD_LEFT),
            str_pad($sequence, 4, '0', STR_PAD_LEFT)
        );
    }

    // Show survey response with QR code
    public function show_response($control_no)
    {
        $survey = SurveyLog::where('control_no', $control_no)->firstOrFail();
    
        // Just pass the URL to Blade
        $qrUrl = route('survey.show_response', $control_no);
    
        return view('survey-response', [
            'survey' => $survey,
            'qrUrl'  => $qrUrl
        ]);
    }
}