<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Models use
use App\Models\Service;
use App\Models\Division;
use App\Models\SurveyLog;

class AuthenticatedController extends Controller
{
    // Dashboard view
    public function dashboard_view() {
        // Get divisions with services
        if(auth()->user()->division_id == null) {
            $divisions = Division::with('services')->get();

            // Get number of online and f2f respondents
            $no_of_respondents = DB::table('survey_logs')
                                    ->selectRaw("(SELECT COUNT(*) FROM survey_logs WHERE control_no LIKE '%OL%' AND YEAR(created_at) = YEAR(CURDATE())) AS online_respondents")
                                    ->selectRaw("(SELECT COUNT(*) FROM survey_logs WHERE control_no LIKE '%F2F%' AND YEAR(created_at) = YEAR(CURDATE())) AS f2f_respondents")
                                    ->first();

            // Get count for male and female
            $sex_count = DB::table('survey_logs')
                            ->selectRaw('(SELECT COUNT(sex) FROM survey_logs WHERE sex = 1 AND YEAR(survey_logs.created_at) = YEAR(CURDATE())) as male')
                            ->selectRaw('(SELECT COUNT(sex) FROM survey_logs WHERE sex = 0 AND YEAR(survey_logs.created_at) = YEAR(CURDATE())) as female')
                            ->first();

            // Get Citizen Charter 3 survey logs
            $cc3_result = DB::table('survey_logs')
                            ->selectRaw('(SELECT COUNT(cc3) FROM survey_logs WHERE cc3 = 1 AND YEAR(survey_logs.created_at) = YEAR(CURDATE())) AS cc3_1')
                            ->selectRaw('(SELECT COUNT(cc3) FROM survey_logs WHERE cc3 = 2 AND YEAR(survey_logs.created_at) = YEAR(CURDATE())) AS cc3_2')
                            ->selectRaw('(SELECT COUNT(cc3) FROM survey_logs WHERE cc3 = 3 AND YEAR(survey_logs.created_at) = YEAR(CURDATE())) AS cc3_3')
                            ->first();

            // Get Citizen Charter 2 survey logs
            $cc2_result = DB::table('survey_logs')
                            ->selectRaw('(SELECT COUNT(cc2) FROM survey_logs WHERE cc2 = 1 AND YEAR(survey_logs.created_at) = YEAR(CURDATE())) AS cc2_1')
                            ->selectRaw('(SELECT COUNT(cc2) FROM survey_logs WHERE cc2 = 2 AND YEAR(survey_logs.created_at) = YEAR(CURDATE())) AS cc2_2')
                            ->selectRaw('(SELECT COUNT(cc2) FROM survey_logs WHERE cc2 = 3 AND YEAR(survey_logs.created_at) = YEAR(CURDATE())) AS cc2_3')
                            ->first();

            // Get Citizen Charter 1 survey logs
            $cc1_result = DB::table('survey_logs')
                            ->selectRaw('(SELECT COUNT(cc1) FROM survey_logs WHERE cc1 = 1 AND YEAR(survey_logs.created_at) = YEAR(CURDATE())) AS cc1_1')
                            ->selectRaw('(SELECT COUNT(cc1) FROM survey_logs WHERE cc1 = 2 AND YEAR(survey_logs.created_at) = YEAR(CURDATE())) AS cc1_2')
                            ->selectRaw('(SELECT COUNT(cc1) FROM survey_logs WHERE cc1 = 3 AND YEAR(survey_logs.created_at) = YEAR(CURDATE())) AS cc1_3')
                            ->first();
            
        } else {
            $division_id = auth()->user()->division_id;
            $divisions = Division::with('services')->where('id', $division_id)->get();

            // Get number of online and f2f respondents
            $no_of_respondents = DB::table('survey_logs')
                                    ->selectRaw("(SELECT COUNT(*) FROM survey_logs WHERE control_no LIKE '%OL%' AND YEAR(created_at) = YEAR(CURDATE()) AND division_id = {$division_id}) AS online_respondents")
                                    ->selectRaw("(SELECT COUNT(*) FROM survey_logs WHERE control_no LIKE '%F2F%' AND YEAR(created_at) = YEAR(CURDATE()) AND division_id = {$division_id}) AS f2f_respondents")
                                    ->first();

            // Get count for male and female
            $sex_count = DB::table('survey_logs')
                            ->selectRaw("(SELECT COUNT(sex) FROM survey_logs WHERE sex = 1 AND YEAR(survey_logs.created_at) = YEAR(CURDATE()) AND division_id = {$division_id}) as male")
                            ->selectRaw("(SELECT COUNT(sex) FROM survey_logs WHERE sex = 0 AND YEAR(survey_logs.created_at) = YEAR(CURDATE()) AND division_id = {$division_id}) as female")
                            ->first();

            // Get Citizen Charter 3 survey logs
            $cc3_result = DB::table('survey_logs')
                            ->selectRaw("(SELECT COUNT(cc3) FROM survey_logs WHERE cc3 = 1 AND YEAR(survey_logs.created_at) = YEAR(CURDATE()) AND division_id = {$division_id}) AS cc3_1")
                            ->selectRaw("(SELECT COUNT(cc3) FROM survey_logs WHERE cc3 = 2 AND YEAR(survey_logs.created_at) = YEAR(CURDATE()) AND division_id = {$division_id}) AS cc3_2")
                            ->selectRaw("(SELECT COUNT(cc3) FROM survey_logs WHERE cc3 = 3 AND YEAR(survey_logs.created_at) = YEAR(CURDATE()) AND division_id = {$division_id}) AS cc3_3")
                            ->first();

            // Get Citizen Charter 2 survey logs
            $cc2_result = DB::table('survey_logs')
                            ->selectRaw("(SELECT COUNT(cc2) FROM survey_logs WHERE cc2 = 1 AND YEAR(survey_logs.created_at) = YEAR(CURDATE()) AND division_id = {$division_id}) AS cc2_1")
                            ->selectRaw("(SELECT COUNT(cc2) FROM survey_logs WHERE cc2 = 2 AND YEAR(survey_logs.created_at) = YEAR(CURDATE()) AND division_id = {$division_id}) AS cc2_2")
                            ->selectRaw("(SELECT COUNT(cc2) FROM survey_logs WHERE cc2 = 3 AND YEAR(survey_logs.created_at) = YEAR(CURDATE()) AND division_id = {$division_id}) AS cc2_3")
                            ->first();

            // Get Citizen Charter 1 survey logs
            $cc1_result = DB::table('survey_logs')
                            ->selectRaw("(SELECT COUNT(cc1) FROM survey_logs WHERE cc1 = 1 AND YEAR(survey_logs.created_at) = YEAR(CURDATE()) AND division_id = {$division_id}) AS cc1_1")
                            ->selectRaw("(SELECT COUNT(cc1) FROM survey_logs WHERE cc1 = 2 AND YEAR(survey_logs.created_at) = YEAR(CURDATE()) AND division_id = {$division_id}) AS cc1_2")
                            ->selectRaw("(SELECT COUNT(cc1) FROM survey_logs WHERE cc1 = 3 AND YEAR(survey_logs.created_at) = YEAR(CURDATE()) AND division_id = {$division_id}) AS cc1_3")
                            ->first();
        }

        return view('dashboard')->with([
            'cc1_result' => $cc1_result,
            'cc2_result' => $cc2_result,
            'cc3_result' => $cc3_result,
            'sex_count' => $sex_count,
            'divisions' => $divisions,
            'no_of_respondents' => $no_of_respondents
        ]);
    }

    // Services view
    public function services_view() {
        // Get all divisions
        $divisions = Division::all();

        return view('services')->with(['divisions' => $divisions]);
    }

    // Received service form and save to database
    public function save_service(Request $request) {
        // Validate data
        $service = $request->validate([
            'service_code' => 'required',
            'service_name' => 'required',
            'division_id' => 'exists:divisions,id'
        ]);

        //  Save to database
        Service::create($service);

        // Return to services view
        return redirect()->route('services.view');
    }

    // Get list of services for datatable
    public function get_services() {
        $services = Service::with('division')->get();

        return response()->json(['data' => $services]);
    }

    // Get individual service data for update form
    public function get_service($id) {
        $service = Service::findOrFail($id);

        return response()->json(['service' => $service]);
    }

    // Update service
    public function update_service(Request $request, $id) {
        $service = Service::findOrFail($id);

        // Validate inputs
        $service_data = $request->validate([
            'edit_service_code' => 'required',
            'edit_service_name' => 'required',
            'edit_division_id' => 'exists:divisions,id'
        ]);

        $service->service_code = $service_data['edit_service_code'];
        $service->service_name = $service_data['edit_service_name'];
        $service->division_id = $service_data['edit_division_id'];
        $service->save();

        // Return to services view
        return redirect()->route('services.view');
    }

    // Delete Service
    public function delete_service($id) {
        $service = Service::findOrFail($id);

        if($service->delete()) {
            return response()->json(['message' => "Record has been deleted successfully"], 200);
        } else {
            return response()->json(['message' => "An errro has been encountered."], 500);
        }
    }

    // Services individual details
    public function service_detail($id) {
        $service = Service::findOrFail($id);
        $survey_logs_years = SurveyLog::select(DB::raw('YEAR(created_at) as year'))
                                        ->groupBy(DB::raw('YEAR(created_at)'))
                                        ->get();

        return view('service_detail')->with([
            'service' => $service,
            'years' => $survey_logs_years,
            'id' => $id
        ]);
    }
    
    // Retrieve survey logs base on the sqd, year and service
    public function get_survey_logs($service_id, $sqd, $year) {
        $datapoints = SurveyLog::select(
                                        DB::raw("SUM(sqd$sqd) as sqd_sum"),    // total sum
                                        DB::raw("COUNT(*) as count"),          // number of records
                                        DB::raw("SUM(sqd$sqd)/COUNT(*) as sqd_avg"), // average
                                        DB::raw("MONTH(created_at) as month")  // month number
                                    )
                                ->where('service_id', $service_id)
                                ->whereYear('created_at', $year)
                                ->groupBy(DB::raw('MONTH(created_at)'))
                                ->get();
    
        return response()->json([
            'datapoints' => $datapoints,
        ]);
    }
}
