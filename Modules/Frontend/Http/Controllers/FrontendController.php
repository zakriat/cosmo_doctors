<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IncidenceController;
use App\Models\Incidence;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Blog\Models\Blog;
use Modules\Clinic\Models\ClinicsCategory;
use Modules\Clinic\Models\Clinics;
use Modules\Clinic\Models\Doctor;
use Modules\Clinic\Models\DoctorRating;
use Modules\Appointment\Models\Appointment;
use Modules\Clinic\Models\ClinicsService;
use Modules\FAQ\Models\Faqs;
use Modules\Slider\Models\Slider;
use Modules\FrontendSetting\Models\FrontendSetting;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Modules\Page\Models\Page;
use Yajra\DataTables\DataTables;
use Modules\Appointment\Trait\AppointmentTrait;
use App\Models\User;
use Modules\NotificationTemplate\Models\NotificationTemplate;
use Illuminate\Support\Facades\Mail;
use App\Mail\IncidenceMail;

class FrontendController extends Controller
{
    use AppointmentTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // If user is authenticated, redirect to their dashboard
        if (auth()->check()) {
            return redirect('/home');
        }
        
        // If not authenticated, redirect to login
        return redirect()->route('login-page');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('frontend::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('frontend::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('frontend::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    public function searchList()
    {
        return view('frontend::search');
    }

    public function faqList()
    {
        $faqs = faqs::where('status', 1)->get();
        return view('frontend::faq', compact('faqs'));
    }

    public function aboutUs()
    {
        $about_us = Page::where('slug', 'about-us')->first();

        return view('frontend::about_us', compact('about_us'));
    }

    public function contactUs()
    {
        $settings = Setting::whereIn('type', ['bussiness', 'string'])->pluck('val', 'name')->toArray();
        return view('frontend::contact_us', compact('settings'));
    }

    public function incidenceReport()
    {
        if (!auth()->check()) {
            return redirect()->route('frontend.index');
        }
        return view('frontend::incidence');
    }

    public function index_data(Request $request)
    {
        $incidence_list = Incidence::where('user_id', auth()->id());

        // Get status from filter array (single level)
        $filter = $request->input('filter', []);
        $status = $filter['status'] ?? null;
        if ($status === 'open') {
            $incidence_list->where('incident_type', 1);
        } elseif ($status === 'close') {
            $incidence_list->where('incident_type', 2);
        } elseif ($status === 'reject') {
            $incidence_list->where('incident_type', 3);
        }
        // 'all' or empty: no filter

        $incidences = $incidence_list->orderBy('updated_at', 'desc');

        return DataTables::of($incidences)
            ->addColumn('card', function ($incidence) {

                return view('frontend::components.card.incidence_card', compact('incidence'))->render();
            })
            ->rawColumns(['card'])
            ->make(true);
    }

    public function incidenceSave(Request $request)
    {

        // try {
        $rules = [
            'title' => [
                'required',
                'string',
                // Rule::unique('users', 'mobile')->ignore(Auth::id()),
            ],
            'description' => [
                'required',
                'string'
            ],
            'phone' => [
                'required',
                'string'
            ],
            'email' => [
                'required',
                'email'
            ]
        ];
        $messages = [
            'title.required' => 'Title is required.',
            'description.required' => 'Description is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'phone.required' => 'Mobile number is required.',
        ];

        $validatedData = $request->validate($rules, $messages);

        // $incidence = new Incidence();
        $reqdata['title'] = $request->title;
        $reqdata['description'] = $request->description;
        $reqdata['email'] = $validatedData['email'];
        $reqdata['phone'] = $request->phone;
        $reqdata['user_id'] = Auth::id();
        $reqdata['profile_image'] = Auth::user()->profile_image;
        $reqdata['created_by'] = Auth::id();
        $reqdata['incident_date'] = date('Y-m-d');

        $data = Incidence::create($reqdata);

        if ($request->hasFile('file_url')) {
            storeMediaFile($data, $request->file('file_url'));
        }


        self::sendNotificationOnIncidence($data);


        // $controller = new IncidenceController();
        // $controller->sendNotificationOnIncidence($data);

        return response()->json([
            'success' => true,
            'message' => 'Incidence report created successfully.',
        ]);
        // } catch (\Illuminate\Validation\ValidationException $e) {
        //     // Return validation errors
        //     return response()->json([
        //         'success' => false,
        //         'errors' => $e->errors(),
        //     ], 422);
        // } catch (\Exception $e) {
        //     // Log the error for debugging
        //     \Log::error('Incidence report error:', ['error' => $e->getMessage()]);

        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Something went wrong. Please try again later.',
        //     ], 500);
        // }
    }

    public function sendNotificationOnIncidence($data)
    {
        $createdBy = User::selectRaw('CONCAT(first_name," ",last_name) as name')->where('id', $data->created_by)->pluck('name')->first();

        $notification_data = [
            'id' => $data->id,
            'title' => $data->title,
            'description' => $data->description,
            'phone' => $data->phone,
            'email' => $data->email,
            'user_name' => $createdBy
        ];

        $template = NotificationTemplate::where('type', 'new_incidence')->with('defaultNotificationTemplateMap')->firstOrFail();

        $mail_template = $template->defaultNotificationTemplateMap->mail_template_detail ?? '<p>New incidence report created.</p> <p>Title:  [[ title ]]  ,  Description: [[ description ]] and Phone: [[ phone ]] , Email: [[ email ]] and Created By: [[ user_name ]] </p>';

        // Replace [[ reply ]] and other keys in the template with values from $notification_data
        $bodyData = $mail_template;
        foreach ($notification_data as $key => $value) {
            $bodyData = str_replace('[[ ' . $key . ' ]]', $value, $bodyData);
            $bodyData = str_replace('[[ ' . $key . ' ]]', $value, $bodyData); // handle both with and without space
        }

        try {
            Mail::to($data->email)->send(new IncidenceMail($bodyData));
        } catch (\Exception $e) {
            \Log::error('Mail not sent: ' . $e->getMessage());
        }



        $this->sendNotificationOnIncidenceCreate('new_incidence', $notification_data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function changeStatus(Request $request)
    {
        $reqdata = $request->all();
        $data = Incidence::find($reqdata['id']);
        if (!$data) {
            return response()->json([
                'success' => false,
                'errors' => 'Invalid incidence referance!',
            ], 422);
            return response()->json(['message' => 'Invalid incidence referance!', 'status' => false]);
        }

        if (!isset($reqdata['status']) && !isset($reqdata['incident_type'])) {
            return response()->json([
                'success' => false,
                'errors' => 'Invalid request!',
            ], 422);
        }

        isset($reqdata['status']) && $attributes['status'] = $reqdata['status'];
        isset($reqdata['incident_type']) && $attributes['incident_type'] = $reqdata['incident_type'];
        (isset($reqdata['incident_type']) && $reqdata['incident_type'] == 2) && $attributes['incident_closed_date'] = date('Y-m-d');

        $data->update($attributes);
        return response()->json([
            'success' => true,
            'message' => 'Successfully status changed!',
        ]);
    }

    public function getSearch(Request $request)
    {
        $category_list = ClinicsCategory::query()->where('status', 1);

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $category_list->where('name', 'like', "%{$searchTerm}%");
        }

        // Execute the query and get the results
        $categories = $category_list->orderBy('updated_at', 'desc')->get();

        $clinics_list = Clinics::CheckMultivendor()->where('status', 1);

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $clinics_list->where('name', 'like', "%{$searchTerm}%");
        }

        // Execute the query and get the results
        $Clinics = $clinics_list->orderBy('updated_at', 'desc')->get();

        $service_list = ClinicsService::CheckMultivendor()->with('category')->where('status', 1);

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $service_list->where('name', 'like', "%{$searchTerm}%");
        }

        // Execute the query and get the results
        $service = $service_list->orderBy('updated_at', 'desc')->get();

        $doctor_list = Doctor::CheckMultivendor()->with('user', 'doctorclinic', 'doctorService')->where('status', 1);

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $doctor_list->whereHas('user', function ($query) use ($searchTerm) {
                $query->whereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?", [strtolower("%{$searchTerm}%")]);
            });
        }

        // Execute the query and get the results
        $doctors = $doctor_list->orderBy('updated_at', 'desc')->get();

        $html = '';
        if ($categories->isNotEmpty()) {
            foreach ($categories as $index => $category) {
                $html .= '<div class="col">';
                $html .= '<a href="' . route('services', ['category_id' => $category->id]) . '" class="d-block text-decoration-none">'; // Link to services filtered by category
                $html .= '<div class="serach-card p-3 text-center rounded">'; // Inner card structure remains
                $html .= '<div class="position-relative">';
                $html .= '<div class="d-block clinics-img">';
                $html .= '<img src="' . (!empty($category->file_url) ? $category->file_url : default_file_url()) . '" alt="category-image" class="w-100 rounded object-cover">';
                $html .= '</div>';
                $html .= '<h6 class="clinics-title line-count-1 mt-3 mb-2 pb-1">';
                $html .= htmlspecialchars($category->name ?? '') . '</h6>'; // Removed nested anchor
                $html .= '<p class="mb-0 text-capitalize line-count-2 font-size-14">';
                $html .= 'Checkups are vital for early detection & wellness</p>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</a>'; // Close the anchor tag
                $html .= '</div>';
            }
        }

        // Clinics Section
        if ($Clinics->isNotEmpty()) {
            foreach ($Clinics as $index => $clinic) {
                $html .= '<div class="col">';
                $html .= '<a href="' . route('clinic-details', ['id' => $clinic->id]) . '" class="d-block text-decoration-none">'; // Replaced the first div with an anchor tag
                $html .= '<div class="serach-card p-3 text-center rounded">'; // Inner card structure remains
                $html .= '<div class="position-relative">';
                $html .= '<div class="d-block">';
                $html .= '<img src="' . (!empty($clinic->file_url) ? $clinic->file_url : default_file_url()) . '" alt="clinic-image" class="w-100 rounded object-cover">';
                $html .= '</div>';
                $html .= '<h6 class="line-count-1 mt-3 mb-2 pb-1">';
                $html .= htmlspecialchars($clinic->name ?? '') . '</h6>'; // Removed nested anchor
                $html .= '<p class="mb-0 text-capitalize line-count-2 font-size-14 text-body">';
                $html .= htmlspecialchars($clinic->description ?? '') . '</p>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</a>'; // Close the anchor tag
                $html .= '</div>';
            }
        }

        // service Section
        if ($service->isNotEmpty()) {
            foreach ($service as $index => $service) {
                $html .= '<div class="col">';
                $html .= '<a href="' . route('service-details', ['id' => $service->id]) . '" class="d-block text-decoration-none">'; // Replaced the first div with an anchor tag
                $html .= '<div class="serach-card p-3 text-center rounded">'; // Inner card structure remains
                $html .= '<div class="position-relative">';
                $html .= '<div class="d-block">';
                $html .= '<img src="' . (!empty($service->file_url) ? $service->file_url : default_file_url()) . '" alt="service-image" class="w-100 rounded object-cover">';
                $html .= '</div>';
                $html .= '<h6 class="line-count-1 mt-3 mb-2 pb-1">';
                $html .= htmlspecialchars($service->name ?? '') . '</h6>'; // Removed nested anchor
                $html .= '<p class="mb-0 text-capitalize line-count-2 font-size-14 text-body">';
                $html .= htmlspecialchars($service->description ?? '') . '</p>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</a>'; // Close the anchor tag
                $html .= '</div>';
            }
        }

        if ($doctors->isNotEmpty()) {
            foreach ($doctors as $index => $doctor) {
                $html .= '<div class="col">';
                $html .= '<a href="' . route('doctor-details', ['id' => $doctor->id]) . '" class="d-block text-decoration-none">'; // Replaced the first div with an anchor tag
                $html .= '<div class="serach-card p-3 text-center rounded">'; // Inner card structure remains
                $html .= '<div class="position-relative">';
                $html .= '<div class="d-block">';
                $html .= '<img src="' . (!empty(optional($doctor->user)->profile_image) ? optional($doctor->user)->profile_image : user_avatar()) . '" alt="doctor-image" class="w-100 rounded object-cover">';
                $html .= '</div>';
                $html .= '<h6 class="line-count-1 mt-3 mb-2 pb-1">';
                $html .= htmlspecialchars(optional($doctor->user)->first_name . ' ' . optional($doctor->user)->last_name ?? 'Unkown') . '</h6>'; // Removed nested anchor
                $html .= '<p class="mb-0 text-capitalize line-count-2 font-size-14 text-body">';
                $html .= htmlspecialchars(optional(optional($doctor->user)->profile)->expert ?? '') . '</p>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</a>'; // Close the anchor tag
                $html .= '</div>';
            }
        }

        // Handle case where no other data types have results
        if (empty($categories) && empty($Clinics)) {
            $html .= '';
        }

        return response()->json([
            'status' => true,
            'html' => $html,
            'message' => __('movie.search_list'),
        ], 200);
    }
}
