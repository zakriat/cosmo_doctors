<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Appointment\Models\Appointment;
use Modules\Clinic\Models\Clinics;
use Modules\Clinic\Models\DoctorRating;
use Yajra\DataTables\DataTables;
use Modules\Clinic\Models\ClinicsService;

class ClinicController extends Controller
{
    public function clinicsList(Request $request)
    {
        $service_id = $request->query('service_id');

        $services = ClinicsService::where('status', 1)->get();

        return view('frontend::clinics', compact('service_id', 'services'));
    }

    public function index_data(Request $request)
    {
        $clinic_list = Clinics::CheckMultivendor();
        $filter = $request->filter;

        $serviceId = $request->query('service_id');
        if ($serviceId) {
            $clinic_list->whereHas('clinicservices', function ($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            });
        }

        $search = $request->input('search');
        if ($search) {
            $service_list = $clinic_list->where('name', 'like', '%' . $search . '%');
        }
        if (isset($filter)) {


            if (isset($filter['service_id'])) {
                $serviceId = $filter['service_id'];
                $clinic_list = $clinic_list->whereHas('clinicservices', function ($query) use ($serviceId) {
                    $query->where('service_id', $serviceId);
                });
            }
        }
        $clinic_list = $clinic_list->where('status', 1);

        $clinics = $clinic_list->orderBy('updated_at', 'desc');

        return DataTables::of($clinics)
            ->addColumn('card', function ($clinic) {
                return view('frontend::components.card.clinic_card', compact('clinic'))->render();
            })
            ->rawColumns(['card'])
            ->make(true);

    }

    public function clinicDetails($id)
    {
        $clinic = Clinics::CheckMultivendor()->where('id', $id)->with('specialty', 'countries', 'states', 'cities', 'clinicgallery', 'clinicsessions', 'clinicservices.service', 'clinicdoctor.doctor', 'clinicappointment')->first();

        $total_appointments = optional($clinic->clinicappointment)->where('status', 'checkout')->count();
        $total_doctors = optional($clinic->clinicdoctor)->count();
        $clinic->total_appointments = $total_appointments;
        $clinic->total_doctors = $total_doctors;
        $doctors = $clinic->clinicdoctor->pluck('doctor_id');
        $total_ratings = 0;
        $total_reviews = 0;

        // Fetch all doctors associated with the clinic
        foreach ($clinic->clinicdoctor as $clinicDoctor) {
            $doctor = $clinicDoctor->doctor;
            $doctor_id = optional($doctor->user)->id;
            $total_appointment = Appointment::where('doctor_id', $doctor_id)->where('status', 'checkout')->count();
            $doctor->total_appointment = $total_appointment; 
            $reviews = DoctorRating::where('doctor_id', $doctor_id)->get();
            $average_rating = $reviews->avg('rating');
            $doctor->average_rating = $average_rating;

            if ($average_rating) {
                $total_ratings += $average_rating * $reviews->count();
                $total_reviews += $reviews->count();
            }
        }

        $clinic_average_rating = $total_reviews > 0 ? $total_ratings / $total_reviews : 0;

        $satisfaction_percentage = ($clinic_average_rating / 5) * 100;

        return view('frontend::clinic_detail', compact('clinic', 'satisfaction_percentage'));
    }


    public function getClinicsByService(Request $request)
    {
        $service_id = $request->query('service_id');
        $clinic_list = Clinics::CheckMultivendor();

        if ($service_id) {
            $clinic_list->whereHas('clinicservices', function ($query) use ($service_id) {
                $query->where('service_id', $service_id);
            });
        }

        // Fetch the clinics
        $clinics = $clinic_list->get();

        // Return the clinics as a JSON response
        return response()->json($clinics);
    }

    public function getServicesByClinic(Request $request)
{
    $clinicId = $request->query('clinic_id');
    $service_list = ClinicsService::query();
    if ($clinicId) {
        $service_list->whereHas('ClinicServiceMapping', function ($query) use ($clinicId) {
            $query->where('clinic_id', $clinicId);
        });
    }
    $services = $service_list->where('is_enable_advance_payment', 0)->get();
    
    return response()->json($services);
}
}
