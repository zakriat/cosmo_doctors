<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Clinic\Models\Doctor;
use Yajra\DataTables\DataTables;
use Modules\Appointment\Models\Appointment;
use Modules\Clinic\Models\DoctorRating;
use Modules\Clinic\Models\Clinics;
use Modules\Clinic\Models\ClinicsCategory;
use Modules\Clinic\Models\ClinicsService;

class DoctorController extends Controller
{
    public function doctorsList(Request $request)
    {
        $clinic_id = $request->query('clinic_id');
        $clinics = Clinics::checkMultivendor()->with('clinicdoctor', 'specialty', 'clinicdoctor', 'receptionist')->where('status', 1)->get();

        $services = ClinicsService::active()->get(); 

        return view('frontend::doctors', compact('clinic_id', 'clinics', 'services'));
    }

    public function index_data(Request $request)
    {

        $doctor_list = Doctor::CheckMultivendor()->with('user', 'doctorclinic', 'doctorService', 'doctorReviews');
        $filter = $request->filter;
        $search = $request->input('search');
        if ($search) {
            $doctor_list = $doctor_list->whereHas('user', function ($query) use ($search) {
                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        if (isset($filter)) {

            if (isset($filter['clinic_id'])) {
                $clinicId = $filter['clinic_id'];
                $doctor_list = $doctor_list->whereHas('doctorclinic', function ($query) use ($clinicId) {
                    $query->where('clinic_id', $clinicId);
                });
            }
            if (isset($filter['service_id'])) {
                $serviceId = $filter['service_id'];
                $doctor_list = $doctor_list->whereHas('doctorService', function ($query) use ($serviceId) {
                    $query->where('service_id', $serviceId);
                });

            }
            if (isset($filter['ratingFilter'])) {
                $ratingFilter = $filter['ratingFilter'];

                $doctor_list = $doctor_list->whereHas('doctorReviews', function ($query) use ($ratingFilter) {
                    $query->where('rating', $ratingFilter);
                });
            }

            if (isset($filter['rating'])) {
                $ratingFilter = $filter['rating'];
            
                list($minRating, $maxRating) = explode('-', $ratingFilter);
    
                $doctor_list = $doctor_list->whereHas('doctorReviews', function ($query) use ($minRating, $maxRating) {
                    $query->whereBetween('rating', [$minRating, $maxRating]);
                });

            }
            

            if (isset($filter['category_id'])) {
                $category_id = $filter['category_id'];
                $doctor_list = $doctor_list->whereHas('doctorclinic.clinics.specialty', function ($query) use ($category_id) {
                    $query->where('id', $category_id);
                });
            }

        }


        $clinic_id = $request->query('clinic_id');
        if ($clinic_id) {
            $doctor_list = $doctor_list->whereHas('doctorclinic', function ($query) use ($clinic_id) {
                $query->where('clinic_id', $clinic_id);
            });
        }

        $doctor_list = $doctor_list->where('status', 1);

        $doctors = $doctor_list->orderBy('updated_at', 'desc');

        return DataTables::of($doctors)
            ->addColumn('card', function ($doctor) {
                $doctor_id = optional($doctor->user)->id;
                $total_appointment = Appointment::where('doctor_id', $doctor_id)->where('status', 'checkout')->count();
                $doctor->total_appointment = $total_appointment;
                $reviews = DoctorRating::where('doctor_id', $doctor_id)->get();
                $average_rating = $reviews->avg('rating');
                $doctor->average_rating = $average_rating;
                return view('frontend::components.card.doctor_card', compact('doctor'))->render();
            })
            ->rawColumns(['card'])
            ->make(true);

    }

    public function doctorDetails($id)
    {
        $doctor = Doctor::CheckMultivendor()->with('user', 'doctorCommission', 'doctorService', 'doctorclinic', 'doctorDocuments', 'doctorReviews')->where('id', $id)->where('status', 1)->first();

        $doctor_id = optional($doctor->user)->id;

        $total_services = $doctor->doctorService->count();
        $doctor->total_services = $total_services;
        $total_appointment = Appointment::where('doctor_id', $doctor_id)->where('status', 'checkout')->count();
        $doctor->total_appointment = $total_appointment;
        $reviews = DoctorRating::where('doctor_id', $doctor_id)->get();
        $average_rating = $reviews->avg('rating');
        $satisfaction_percentage = $average_rating ? ($average_rating / 5) * 100 : 0;
        return view('frontend::doctor_detail', compact('doctor','total_appointment', 'average_rating', 'satisfaction_percentage'));
    }

    public function reviewsList(Request $request)
    {
        $doctor_id = $request->query('doctor_id');
        $reviews = DoctorRating::where('doctor_id', $doctor_id)->get();
        return view('frontend::reviews', compact('reviews'));
    }

    public function checkBookingStatus($serviceId, $doctorId, $clinicId)
    {
        $booking = Appointment::where('service_id', $serviceId)
            ->where('doctor_id', $doctorId)
            ->where('clinic_id', $clinicId)
            ->where('user_id', Auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($booking) {
            return response()->json(['status' => 'pending']);
        }

        return response()->json(['status' => 'available']);
    }
}
