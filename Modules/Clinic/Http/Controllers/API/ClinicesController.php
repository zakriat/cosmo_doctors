<?php

namespace Modules\Clinic\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Clinic\Models\Clinics;
use Modules\Clinic\Models\ClinicsCategory;
use Modules\Clinic\Models\ClinicsService;
use Modules\Clinic\Models\Doctor;
use Modules\Clinic\Transformers\CategoryResource;
use Modules\Clinic\Transformers\ClinicsResource;
use Modules\Clinic\Transformers\ServiceResource;
use Modules\Clinic\Transformers\DoctorResource;
use Modules\Clinic\Transformers\ClinicDetailsResource;
use Modules\Clinic\Transformers\ServiceDetailsResource;
use Modules\Clinic\Transformers\SystemServiceResource;
use Modules\Clinic\Trait\ClinicTrait;
use Modules\Clinic\Models\ClinicGallery;
use Modules\Clinic\Models\SystemService;
use Auth;
use Modules\Service\Models\SystemServiceCategory;
use Modules\Clinic\Transformers\SpecializationResource;
use Modules\Clinic\Transformers\ClinicSessionListResource;
use Modules\Clinic\Models\ClinicSession;
use Modules\Clinic\Models\DoctorRating;
use Modules\Clinic\Models\Receptionist;
use Modules\FrontendSetting\Models\FrontendSetting;

class ClinicesController extends Controller
{
    use ClinicTrait;

    public function SystemServiceList(Request $request){

        $perPage = $request->input('per_page', 10);

        $system_service=SystemService::with('category','sub_category','clinicservice');

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $system_service->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%");
            });
        }

        if($request->filled('is_featured')){

          $system_service->where('is_featured',$request->is_featured);
        }

        if($request->filled('category_id')){
            $system_service->where('category_id',$request->category_id);
        }

        $system_service->where('status',1);

        $system_service = $system_service->orderBy('updated_at', 'desc');

        $system_service = $system_service->paginate($perPage);

        $responseData = SystemServiceResource::collection($system_service);


        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('clinic.doctor_list'),
        ], 200);

    }

    public function ClinicList(Request $request){
        $perPage = $request->input('per_page', 10);

        if(auth()->user()){
            $clinic_list = Clinics::CheckMultivendor()->setRole(auth()->user())->with('specialty','countries','states','cities','clinicgallery','clinicsessions','clinicservices');
        }
        else{
            $clinic_list = Clinics::CheckMultivendor()->with('specialty','countries','states','cities','clinicgallery','clinicsessions','clinicservices');
        }



        if(auth()->user() !== null ){

            if(auth()->user()->hasRole('vendor')){

                $vendor_id=$request->has('vendor_id') ? $request->vendor_id : Auth::id();
                $clinic_list =$clinic_list->where('vendor_id', $vendor_id);
            }elseif(auth()->user()->hasRole('doctor')){

                $doctor_id=$request->has('doctor_id') ? $request->doctor_id : Auth::id();

                $clinic_list->whereHas('clinicdoctor', function ($query) use ($doctor_id) {
                    $query->where('doctor_id',$doctor_id);
                });

                $clinic_list->where('status',1);

            }
            
        }else{

            $clinic_list =$clinic_list->where('status',1);
        }

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $clinic_list->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");

            });
        }
        $clinicIds = $sectionData['section_5']['clinic_id'] ?? [];
        $perfectClinics = is_array($clinicIds) && !empty($clinicIds)
            ? Clinics::whereIn('id', $clinicIds)->get()
            : collect();

        if($request->filled('is_popular') && $request->is_popular == 1) {
            $sectionData = [];
            $section = FrontendSetting::where('key', 'section_5')->first();
            $sectionData = $section ? json_decode($section->value, true) : null;

            $clinicIds = $sectionData['clinic_id'] ?? [];
            if (is_array($clinicIds) && !empty($clinicIds)) {
                $clinic_list->whereIn('id', $clinicIds);
            }
        }

        if($request->filled('service_id')){
            $ServiceId=$request->service_id;
            $clinic_list->whereHas('clinicservices', function ($query) use ($ServiceId) {
                $query->where('service_id',$ServiceId);
            });
        }

        if($request->filled('doctor_id')){
            $doctorId=$request->doctor_id;
            $clinic_list->whereHas('clinicdoctor', function ($query) use ($doctorId) {
                $query->where('doctor_id',$doctorId);
            });
        }

        if($request->filled('vendor_id')){
            $clinic_list->where('vendor_id',$request->vendor_id);
        }

        if($request->filled('system_service_category')){
            $clinic_list->where('system_service_category',$request->system_service_category);
        }


        if($request->filled('latitude') && $request->filled('longitude')){

            $clincs = $this->getNearestclinic($request->latitude,$request->longitude);

            $clinicIds= $clincs->pluck('id');

            $clinic_list->whereIn('id',$clinicIds);

        }

        if($request->has('receptionist_login') &&  $request->receptionist_login==1){

            $clinicIds = Receptionist::pluck('clinic_id')->toArray();

            $clinic_list->whereNotIn('id', $clinicIds);

        }
    
        // $clinic_list = $clinic_list->where('status',1);
        $clinic = $clinic_list->orderBy('updated_at', 'asc');
    
        if($perPage=='all'){

            $clinic = $clinic->get();

        }else{

            $clinic = $clinic->paginate($perPage);
        }

        $responseData = ClinicsResource::collection($clinic);

        if($request->filled('clinic_id')){

            $clinic= $clinic_list->where('id',$request->clinic_id)->first();

            if ($clinic) {

                $clinic->load('clinicdoctor.doctor.user', 'clinicappointment'); // Load relations only for the specific clinic

                // Calculate manually for this single clinic
                $total_appointments = optional($clinic->clinicappointment)->where('status', 'checkout')->count();
                $clinic->total_appointments = $total_appointments;
                $total_ratings = 0;
                $total_reviews = 0;

                if ($clinic->clinicdoctor) {
                    foreach ($clinic->clinicdoctor as $clinicDoctor) {
                        $doctor = $clinicDoctor->doctor;
                        $doctor_id = optional($doctor->user)->id;
                        if ($doctor_id) {
                            $reviews = DoctorRating::where('doctor_id', $doctor_id)->get();
                            $average_rating = $reviews->avg('rating');

                            if ($average_rating) {
                                $total_ratings += $average_rating * $reviews->count();
                                $total_reviews += $reviews->count();
                            }
                        }
                    }
                }

                $clinic_average_rating = $total_reviews > 0 ? $total_ratings / $total_reviews : 0;
                $satisfaction_percentage = ($clinic_average_rating / 5) * 100;

                $clinic->satisfaction_percentage = $satisfaction_percentage;
            }

            $responseData = new ClinicDetailsResource($clinic);

            return response()->json([
                'status' => true,
                'data' => $responseData,
                'message' => __('clinic.clinic_details'),
            ], 200);
        }

        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('clinic.clinic_list'),
        ], 200);
    }


    public function CategoryList(Request $request){

        $perPage = $request->input('per_page', 10);
        $category_list = ClinicsCategory::query();

        if ($request->has('search')) {
            $searchTerm = $request->search;
            $category_list->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%");

            });
        }

        if($request->filled('is_featured')){
            $category_list->where('is_featured',$request->is_featured);
        }

        if($request->filled('category_id')){
            $category_list->where('parent_id',$request->category_id);
        }

        $category_list =$category_list->where('status',1);

        $category = $category_list->orderBy('updated_at', 'asc');
        $category = $category->paginate($perPage);

        $responseData = CategoryResource::collection($category);

        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('clinic.clinic_category_list'),
        ], 200);
    }


    public function ServiceList(Request $request){

        $perPage = $request->input('per_page', 10);
        if($request->has('services') && $request->input('services') == 1){
            $service_list = ClinicsService::CheckMultivendor()->with('category','sub_category','ClinicServiceMapping','systemservice');
        }else{
            $service_list = ClinicsService::CheckMultivendor()->with('category','sub_category','ClinicServiceMapping','doctor_service','systemservice');
        }
        if(auth()->user() !== null ){

            if(auth()->user()->hasRole('vendor')){

                $vendor_id=$request->has('vendor_id') ? $request->vendor_id : Auth::id();
                $service_list =$service_list->where('vendor_id', $vendor_id);

            }elseif(auth()->user()->hasRole('doctor')){

                $doctor_id=$request->has('doctor_id') ? $request->doctor_id : Auth::id();
                if(!$request->has('services') && !$request->input('services') == 1){
                    $service_list->whereHas('doctor_service', function ($query) use ($doctor_id) {
                        $query->where('doctor_id',$doctor_id);
                    });
                }
                if ($service_list->count() == 0) {
                    $service_list = ClinicsService::CheckMultivendor()->with('category','sub_category','ClinicServiceMapping','doctor_service','systemservice');
                    if($request->filled('clinic_id')){

                        $clinicId=$request->clinic_id;
                        $service_list->whereHas('ClinicServiceMapping', function ($query) use ($clinicId) {
                            $query->where('clinic_id',$clinicId);
                        });
                    }
                }

                // $service_list->where('status',1);

            }elseif(auth()->user()->hasRole('receptionist')){
                $user_id = auth()->id();
                $service_list = $service_list->whereHas('ClinicServiceMapping.center', function ($qry) use ($user_id) {
                    $qry->whereHas('receptionist', function ($qry) use ($user_id) {
                        $qry->where('receptionist_id', $user_id);
                    });
                });
                 }

          }else{

            $service_list =$service_list->where('status',1);
         }

        if($request->filled('is_featured')){

            $service_list->whereHas('systemservice', function ($query) use ($searchTerm) {
                $query->where('is_featured',$request->is_featured);
            });

        }

        if($request->filled('is_popular') && $request->is_popular == 1) {
            $sectionData = [];
            $section = FrontendSetting::where('key', 'section_3')->first();
            $sectionData = $section ? json_decode($section->value, true) : null;

            $serviceIds = $sectionData['service_id'] ?? [];
            if (is_array($serviceIds) && !empty($serviceIds)) {
                $service_list->whereIn('id', $serviceIds);
            }
        }

        if($request->filled('system_service_id')){
            $service_list->where('system_service_id',$request->system_service_id);
        }

        if($request->filled('category_id')){
            $service_list->where('category_id',$request->category_id);
        }

        if($request->filled('subcategory_id')){
            $service_list->where('subcategory_id',$request->subcategory_id);
        }
        if($request->filled('is_enable_advance_payment')){

            $enableAdvancedPayment=$request->is_enable_advance_payment;
            $service_list->where('is_enable_advance_payment',$enableAdvancedPayment);

        }

        if($request->filled('clinic_id')){

            $clinicId=$request->clinic_id;
            $service_list->whereHas('ClinicServiceMapping', function ($query) use ($clinicId) {
                $query->where('clinic_id',$clinicId);
            });
        }

        if($request->filled('doctor_id')){

            $doctorId=$request->doctor_id;

            $service_list->whereHas('doctor_service', function ($query) use ($doctorId) {
                $query->where('doctor_id',$doctorId);
            });
        }

        if($request->filled('vendor_id')){
            $service_list->where('vendor_id',$request->vendor_id);
        }

        if($request->filled('type')){
            $service_list->where('type',$request->type);
        }

        if($request->filled('is_price_min') && $request->filled('is_price_max') ){
            $service_list->whereBetween('charges', [$request->is_price_min, $request->is_price_max]);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $searchTerm = "%{$searchTerm}%";

            $service_list->where(function ($query) use ($searchTerm) {
                $query->whereHas('systemservice', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', $searchTerm);
                })
                ->orWhereHas('category', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', $searchTerm);
                });
            });
        }


        $Service = $service_list->orderBy('updated_at', 'desc');

        if($perPage=='all'){

            $Service = $Service->get();

        }else{

            $Service = $Service->paginate($perPage);
        }
        $responseData = ServiceResource::collection($Service);

        if($request->filled('service_id')){
           $service_data=$service_list->where('id',$request->service_id)->first();

           $responseData =New ServiceDetailsResource($service_data);

           return response()->json([
               'status' => true,
               'data' => $responseData,
               'message' => __('clinic.clinic_details'),
           ], 200);

        }

        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('clinic.Clinic_service_list'),
        ], 200);
    }

    public function DoctorList(Request $request){

        $perPage = $request->input('per_page', 10);
        $doctor_list = Doctor::with('user');

        if(auth()->user() !== null && auth()->user()->hasRole('vendor')){

            $vendor_id=$request->has('vendor_id') ? $request->vendor_id : Auth::id();
            $doctor_list =$doctor_list->where('vendor_id', $vendor_id) ;

          }else{

            $doctor_list =$doctor_list->where('status',1);
         }

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $doctor_list->whereHas('user', function ($query) use ($searchTerm) {
                $query->where('first_name', 'like', "%{$searchTerm}%")
                ->orWhere('last_name', 'like', "%{$searchTerm}%")
                ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        if($request->filled('clinic_id')){

            $clinicId=$request->clinic_id;
            $doctor_list->whereHas('doctorclinic', function ($query) use ($clinicId) {
                $query->where('clinic_id',$clinicId);
            });
        }

        if($request->filled('service_id')){

            $serviceId=$request->service_id;
            $doctor_list->whereHas('doctorService', function ($query) use ($serviceId) {
                $query->where('service_id',$serviceId);
            });
        }


        if($request->filled('vendor_id')){
            $doctor_list->where('vendor_id',$request->vendor_id);
         }


        $doctor = $doctor_list->orderBy('updated_at', 'desc');

        $doctor = $doctor->paginate($perPage);
        $responseData = DoctorResource::collection($doctor);

        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('clinic.doctor_list'),
        ], 200);
    }

    public function  ClinicGallery(Request $request){

     $clinic_gallery=ClinicGallery::where('clinic_id',$request->clinic_id)->get();

        return response()->json([
            'status' => true,
            'data' => $clinic_gallery,
            'message' => __('clinic.clinic_gallery'),
        ], 200);

    }

    public function specializationList(Request $request){
        $perPage = $request->input('per_page', 10);
        $specialization_list = SystemServiceCategory::with('mainCategory', 'subCategories');

        if ($request->has('search')) {
            $searchTerm = $request->search;
            $specialization_list->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%");

            });
        }

        $specialization_list =$specialization_list->where('status',1);

        $specialization = $specialization_list->orderBy('updated_at', 'desc');

        $specialization = $specialization->paginate($perPage);
        $responseData = SpecializationResource::collection($specialization);

        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('clinic.specialization_list'),
        ], 200);
    }

    public function clinicSessionList(Request $request){

        $perPage = $request->input('per_page', 10);

        $clinic_session_list = ClinicSession::with('clinic');

        $clinic_session_list = $clinic_session_list->orderBy('updated_at', 'desc');

        $clinic_session_list  = $clinic_session_list ->paginate($perPage);

        $responseData = ClinicSessionListResource::collection($clinic_session_list);

        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('clinic.clinic_session_list'),
        ], 200);

    }

    public function uploadGalleryImages(Request $request)
    {

        $id = $request->id;

        if($request->has('remove_image_ids')){
            $remove_image_ids = json_decode($request->remove_image_ids);
            ClinicGallery::whereIn('id', $remove_image_ids)->delete();
        }

        if ($request->hasFile('gallery_images') &&  $request->file('gallery_images') !=null ) {
            $clinicGallerys = [];
            foreach ($request->file('gallery_images') as $file) {
                $clinicGallery = ClinicGallery::create(['clinic_id' => $id]);
                $media = $clinicGallery->addMedia($file)->toMediaCollection('gallery_images');
                $clinicGallery->full_url = $media->getUrl();
                $clinicGallery->save();
                $clinicGallerys[] = $media->getUrl();
            }

            return response()->json([
                'status' => true,
                'message' => __('clinic.upload_image'),
            ], 200);
        }

    }

    public function clinicSession(Request $request){

        $clinic_list=[];

        if($request->has('clinic_id') && $request->clinic_id){

            $clinic_list=ClinicSession::where('clinic_id',$request->clinic_id)->get();

        }

        return response()->json([
            'status' => true,
            'data' => $clinic_list,
            'message' => __('clinic.clinic_session'),
        ], 200);

    }



}
