<?php

return [
    'app' => [
        'title' => 'General',
        'desc' => 'All the general settings for application.',
        'icon' => 'fas fa-cube',

        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'app_name', // unique name for field
                'label' => 'Bussiness Name', // you know what label it is
                'rules' => 'required|min:2|max:50', // validation rule of laravel
                'class' => '', // any class for input
                'value' => config('app.name'), // default value if you want
                
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'doctor_app_name', // unique name for field
                'label' => 'Doctor App Name', // you know what label it is
                'rules' => 'required|min:2|max:50', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'Health Wellness Doctor App', // default value if you want
                
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'user_app_name', // unique name for field
                'label' => 'User App Name', // you know what label it is
                'rules' => 'required|min:2|max:50', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'Health Wellness User App', // default value if you want
                
            ],
            // [
            //     'type' => 'text', // input fields type
            //     'data' => 'bussiness', // data type, string, int, boolean
            //     'name' => 'footer_text', // unique name for field
            //     'label' => 'Footer Text', // you know what label it is
            //     'rules' => 'required|min:2', // validation rule of laravel
            //     'class' => '', // any class for input
            //     'value' => 'Built with ♥ from <a href="https://iqonic.design" target="_blank">IQONIC DESIGN.</a>', // default value if you want
            //     
            // ],
            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'helpline_number', // unique name for field
                'label' => 'Contact Number', // you know what label it is
                'rules' => 'required|min:2', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '1234567890', // default value if you want
                
            ],
            // [
            //     'type' => 'text', // input fields type
            //     'data' => 'bussiness', // data type, string, int, boolean
            //     'name' => 'copyright_text', // unique name for field
            //     'label' => 'Copyright Text', // you know what label it is
            //     'rules' => 'required|min:2', // validation rule of laravel
            //     'class' => '', // any class for input
            //     'value' => 'Copyright © '.date("Y"), // default value if you want
            //     
            // ],
            // [
            //     'type' => 'text', // input fields type
            //     'data' => 'bussiness', // data type, string, int, boolean
            //     'name' => 'ui_text', // unique name for field
            //     'label' => 'UI Text', // you know what label it is
            //     'rules' => 'required|min:2', // validation rule of laravel
            //     'class' => '', // any class for input
            //     'value' => 'UI Powered By <a href="https://hopeui.iqonic.design/" target="_blank">HOPE UI</a>', // default value if you want
            //     
            // ],
            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'inquriy_email', // unique name for field
                'label' => 'Email', // you know what label it is
                'rules' => 'required|min:2', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'admin@kivicare.com', // default value if you want
                
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'short_description', // unique name for field
                'label' => 'Short Description', // you know what label it is
                'rules' => 'required|min:2', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'Dummy Text ', // default value if you want
                
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'google_analytics', // unique name for field
                'label' => 'Google Analytics', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                
            ],
            [
                'type' => 'file', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'logo', // unique name for field
                'label' => 'Logo', // you know what label it is
                'rules' => 'nullable|image|mimes:jpg,png,gif', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'img/logo/logo.webp', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
                // 
            ],
            [
                'type' => 'file', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'mini_logo', // unique name for field
                'label' => 'Mini Logo', // you know what label it is
                'rules' => 'nullable|image|mimes:jpg,png,gif', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'img/logo/mini_logo.webp', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
                
            ],
            [
                'type' => 'file', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'dark_logo', // unique name for field
                'label' => 'Dark Logo', // you know what label it is
                'rules' => 'nullable|image|mimes:jpg,png,gif', // validation rule of laravel
                'class' => '', // any class for input
                'imageClass' => 'bg-dark',
                'value' => 'img/logo/dark_logo.png', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
                
            ],
            [
                'type' => 'file', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'dark_mini_logo', // unique name for field
                'label' => 'Dark Mini Logo', // you know what label it is
                'rules' => 'nullable|image|mimes:jpg,png,gif', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'img/logo/dark_mini_logo.webp', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
                
            ],
            [
                'type' => 'file', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'favicon', // unique name for field
                'label' => 'Favicon', // you know what label it is
                'rules' => 'nullable|image|mimes:jpg,png,gif,ico', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'img/logo/mini_logo.webp', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
                
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'bussiness_address_line_1', // unique name for field
                'rules' => 'nullable|min:2|max:199', // validation rule of laravel
                'value' => '', // default value if you want
                
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'bussiness_address_line_2', // unique name for field
                'rules' => 'nullable|min:2|max:199', // validation rule of laravel
                'value' => '', // default value if you want
                
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'country', // unique name for field
                'rules' => '', // validation rule of laravel
                'value' => '', // default value if you want
                
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'state', // unique name for field
                'rules' => '', // validation rule of laravel
                'value' => '', // default value if you want
                
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'city', // unique name for field
                'rules' => '', // validation rule of laravel
                'value' => '', // default value if you want
                
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'bussiness_address_postal_code', // unique name for field
                'rules' => 'nullable|min:2|max:199', // validation rule of laravel
                'value' => '', // default value if you want
                
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'bussiness_address_latitude', // unique name for field
                'rules' => 'nullable|min:2|max:199', // validation rule of laravel
                'value' => '', // default value if you want
                
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'bussiness_address_longitude', // unique name for field
                'rules' => 'nullable|min:2|max:199', // validation rule of laravel
                'value' => '', // default value if you want
                
            ],
        ],
    ],
    'module_setting' => [
        'title' => 'Module Settings',
        'desc' => 'Module Settings',
        'icon' => 'fas fa-chart-line',

        'elements' => [
            [
                'type' => 'checkbox', // input fields type
                'data' => 'module_settings', // data type, string, int, boolean
                'name' => 'view_patient_soap', // unique name for field
                'label' => 'Clinic SOAP', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'module_settings', // data type, string, int, boolean
                'name' => 'is_body_chart', // unique name for field
                'label' => 'Body Chart', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'module_settings', // data type, string, int, boolean
                'name' => 'is_telemed_setting', // unique name for field
                'label' => 'Telemed Setting', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'module_settings', // data type, string, int, boolean
                'name' => 'is_multi_vendor', // unique name for field
                'label' => 'Clinic Admin', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'module_settings', // data type, string, int, boolean
                'name' => 'is_blog', // unique name for field
                'label' => 'Blog', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'module_settings', // data type, string, int, boolean
                'name' => 'is_encounter_problem', // unique name for field
                'label' => 'Problem', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'module_settings', // data type, string, int, boolean
                'name' => 'is_encounter_observation', // unique name for field
                'label' => 'Observation', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'module_settings', // data type, string, int, boolean
                'name' => 'is_encounter_note', // unique name for field
                'label' => 'Note', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'module_settings', // data type, string, int, boolean
                'name' => 'is_encounter_prescription', // unique name for field
                'label' => 'Prescription', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
        ],

    ],
    'social' => [
        'title' => 'Social Profiles',
        'desc' => 'Link of all the social profiles.',
        'icon' => 'fas fa-users',

        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'facebook_url', // unique name for field
                'label' => 'Facebook Page URL', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '#', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'twitter_url', // unique name for field
                'label' => 'Twitter Profile URL', // you know what label it is
                'rules' => 'required|nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '#', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'instagram_url', // unique name for field
                'label' => 'Instagram Account URL', // you know what label it is
                'rules' => 'required|nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '#', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'linkedin_url', // unique name for field
                'label' => 'LinkedIn URL', // you know what label it is
                'rules' => 'required|nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '#', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'youtube_url', // unique name for field
                'label' => 'Youtube Channel URL', // you know what label it is
                'rules' => 'required|nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '#', // default value if you want
            ],
        ],

    ],
    'misc' => [
        'title' => 'Misc ',
        'desc' => 'Application Data',
        'icon' => 'fas fa-globe-asia',

        'elements' => [

            [
                'type' => 'text', // input fields type
                'data' => 'misc', // data type, string, int, boolean
                'name' => 'default_language', // unique name for field
                'label' => 'Language', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'en', // default value if you want
                
            ],

            [
                'type' => 'text', // input fields type
                'data' => 'misc', // data type, string, int, boolean
                'name' => 'default_time_zone', // unique name for field
                'label' => 'Time Zone', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'Asia/Kolkata', // default value if you want
                
            ],

            [
                'type' => 'text', // input fields type
                'data' => 'misc', // data type, string, int, boolean
                'name' => 'data_table_limit', // unique name for field
                'label' => 'Datatable Limit', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '10', // default value if you want
                
            ],

            [
                'type' => 'text', // input fields type
                'data' => 'misc', // data type, string, int, boolean
                'name' => 'date_formate', // unique name for field
                'label' => 'Date Formate', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '10', // default value if you want
                
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'misc', // data type, string, int, boolean
                'name' => 'time_formate', // unique name for field
                'label' => 'Time Formate', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '10', // default value if you want
                
            ],

            [
                'type' => 'text', // input fields type
                'data' => 'misc', // data type, string, int, boolean
                'name' => 'booking_invoice_prifix', // unique name for field
                'label' => 'Booking Invoice Prefix', // you know what label it is
                'rules' => 'required|min:2', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'Inv#', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'misc', // data type, string, int, boolean
                'name' => 'is_enable_all', // unique name for field
                'label' => 'All', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '1', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'misc', // data type, string, int, boolean
                'name' => 'clinic', // unique name for field
                'label' => 'Clinic', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '1', // default value if you want
            ],
        ],
    ],
    'analytics' => [
        'title' => 'Analytics',
        'desc' => 'Application Analytics',
        'icon' => 'fas fa-chart-line',

        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'text', // data type, string, int, boolean
                'name' => 'google_analytics', // unique name for field
                'label' => 'Google Analytics (gtag)', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
                
            ],
        ],

    ],
    'integration' => [
        'title' => 'Integration',
        'desc' => 'Integration',
        'icon' => 'fas fa-chart-line',

        'elements' => [
            [
                'type' => 'checkbox', // input fields type
                'data' => 'integaration', // data type, string, int, boolean
                'name' => 'is_google_login', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            
            [
                'type' => 'checkbox', // input fields type
                'data' => 'integaration', // data type, string, int, boolean
                'name' => 'is_facebook_login', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'integaration', // data type, string, int, boolean
                'name' => 'is_webpush_notification', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'integaration', // data type, string, int, boolean
                'name' => 'is_custom_webhook_notification', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
           
            [
                'type' => 'checkbox', // input fields type
                'data' => 'integaration', // data type, string, int, boolean
                'name' => 'is_map_key', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'integaration', // data type, string, int, boolean
                'name' => 'is_application_link', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'is_google_login', // data type, string, int, boolean
                'name' => 'google_secretkey', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'is_google_login', // data type, string, int, boolean
                'name' => 'google_publickey', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'is_facebook_login', // data type, string, int, boolean
                'name' => 'facebook_secretkey', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'is_facebook_login', // data type, string, int, boolean
                'name' => 'facebook_publickey', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'is_map_key', // data type, string, int, boolean
                'name' => 'google_maps_key', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],

            [
                'type' => 'checkbox', // input fields type
                'data' => 'integaration', // data type, string, int, boolean
                'name' => 'isForceUpdateforAndroid', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'isForceUpdateforAndroid', // data type, string, int, boolean
                'name' => 'patient_android_min_force_update_code', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'isForceUpdateforAndroid', // data type, string, int, boolean
                'name' => 'patient_android_latest_version_update_code', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'isForceUpdateforAndroid', // data type, string, int, boolean
                'name' => 'clinicadmin_android_min_force_update_code', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'isForceUpdateforAndroid', // data type, string, int, boolean
                'name' => 'clinicadmin_android_latest_version_update_code', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],

            [
                'type' => 'checkbox', // input fields type
                'data' => 'integaration', // data type, string, int, boolean
                'name' => 'isForceUpdateforIos', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'isForceUpdateforIos', // data type, string, int, boolean
                'name' => 'patient_ios_min_force_update_code', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'isForceUpdateforIos', // data type, string, int, boolean
                'name' => 'patient_ios_latest_version_update_code', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'isForceUpdateforIos', // data type, string, int, boolean
                'name' => 'clinicadmin_ios_min_force_update_code', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'isForceUpdateforIos', // data type, string, int, boolean
                'name' => 'clinicadmin_ios_latest_version_update_code', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],

            [
                'type' => 'text', // input fields type
                'data' => 'is_application_link', // data type, string, int, boolean
                'name' => 'patient_app_play_store', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'is_application_link', // data type, string, int, boolean
                'name' => 'patient_app_app_store', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'is_application_link', // data type, string, int, boolean
                'name' => 'clinicadmin_app_play_store', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'is_application_link', // data type, string, int, boolean
                'name' => 'clinicadmin_app_app_store', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],

            [
                'type' => 'text', // input fields type
                'data' => 'is_custom_webhook_notification', // data type, string, int, boolean
                'name' => 'custom_webhook_content_key', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'is_custom_webhook_notification', // data type, string, int, boolean
                'name' => 'custom_webhook_url', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'integaration', // data type, string, int, boolean
                'name' => 'is_sms_integration', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'is_sms_integration', // data type, string, int, boolean
                'name' => 'sms_account_sid', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'is_sms_integration', // data type, string, int, boolean
                'name' => 'sms_auth_token', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'is_sms_integration', // data type, string, int, boolean
                'name' => 'mobile_number', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'integaration', // data type, string, int, boolean
                'name' => 'is_whatsapp_integration', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'is_whatsapp_integration', // data type, string, int, boolean
                'name' => 'whatsapp_account_sid', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'is_whatsapp_integration', // data type, string, int, boolean
                'name' => 'whatsapp_auth_token', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'is_whatsapp_integration', // data type, string, int, boolean
                'name' => 'whatsapp_number', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'integaration', // data type, string, int, boolean
                'name' => 'is_dummy_credentials', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.'
            ]
        ],

    ],
    'custom_css' => [
        'title' => 'Custom Code',
        'desc' => 'Custom code area',
        'icon' => 'fa-solid fa-file-code',

        'elements' => [
            [
                'type' => 'textarea', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'custom_css_block', // unique name for field
                'label' => 'Custom Css Code', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
            ],
            [
                'type' => 'textarea', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'custom_js_block', // unique name for field
                'label' => 'Custom Js Code', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
            ],

        ],

    ],
    'customization' => [
        'title' => 'Customization',
        'desc' => 'Setting on admin panel',
        'icon' => 'fa-solid fa-file-code',
        'elements' => [
            [
                'type' => 'hidden', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'customization_json', // unique name for field
                'label' => 'Customization', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '{}', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
            ],
            [
                'type' => 'hidden', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'root_colors', // unique name for field
                'label' => 'root_colors', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '{}', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
            ],
        ],
    ],
    'mobile' => [
        'title' => 'Mobile',
        'desc' => 'Application Mobile',
        'icon' => 'fas fa-chart-line',

        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'primary', // unique name for field
                'label' => 'Primary', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'bussiness', // data type, string, int, boolean
                'name' => 'secondary', // unique name for field
                'label' => 'Secondary', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
        ],

    ],

    'mail' => [
        'title' => 'Mail Setting',
        'desc' => 'Mail settings',
        'icon' => 'fas fa-envelope',

        'elements' => [
            [
                'type' => 'email', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'email', // unique name for field
                'label' => 'Email', // you know what label it is
                'rules' => 'required|email', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'info@example.com', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'mail_driver', // unique name for field
                'label' => 'Mail Driver', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'smtp', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'mail_host', // unique name for field
                'label' => 'Mail Host', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'smtp.gmail.com', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'mail_port', // unique name for field
                'label' => 'Mail Port', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '587', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'mail_encryption', // unique name for field
                'label' => 'Mail Encryption', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'tls', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'mail_username', // unique name for field
                'label' => 'Mail Username', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'youremail@gmail.com', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'mail_password', // unique name for field
                'label' => 'Mail Password', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'Password', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'mail_from', // unique name for field
                'label' => 'Mail From', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'youremail@gmail.com', // default value if you wantPassword
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'mail_config', // data type, string, int, boolean
                'name' => 'from_name', // unique name for field
                'label' => 'From Name', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'Frezka', // default value if you wantPassword
            ],
        ],

    ],
    'payment' => [
        'title' => 'Payment',
        'desc' => 'Payment',
        'icon' => 'fas fa-chart-line',

        'elements' => [
            [
                'type' => 'checkbox', // input fields type
                'data' => 'razorpayPayment', // data type, string, int, boolean
                'name' => 'razor_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want

            ],
            [
                'type' => 'text', // input fields type
                'data' => 'razor_payment_method', // data type, string, int, boolean
                'name' => 'razorpay_secretkey', // unique name for field
                'label' => 'razorpayPayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'razor_payment_method', // data type, string, int, boolean
                'name' => 'razorpay_publickey', // unique name for field
                'label' => 'razorpayPayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'stripePayment', // data type, string, int, boolean
                'name' => 'str_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want

            ],
            [
                'type' => 'text', // input fields type
                'data' => 'str_payment_method', // data type, string, int, boolean
                'name' => 'stripe_secretkey', // unique name for field
                'label' => 'stripePayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'str_payment_method', // data type, string, int, boolean
                'name' => 'stripe_publickey', // unique name for field
                'label' => 'stripePayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'paystackPayment', // data type, string, int, boolean
                'name' => 'paystack_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want

            ],
            [
                'type' => 'text', // input fields type
                'data' => 'paystack_payment_method', // data type, string, int, boolean
                'name' => 'paystack_secretkey', // unique name for field
                'label' => 'paystackPayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'paystack_payment_method', // data type, string, int, boolean
                'name' => 'paystack_publickey', // unique name for field
                'label' => 'paystackPayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'paypalPayment', // data type, string, int, boolean
                'name' => 'paypal_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want

            ],
            [
                'type' => 'text', // input fields type
                'data' => 'paypal_payment_method', // data type, string, int, boolean
                'name' => 'paypal_secretkey', // unique name for field
                'label' => 'paypalPayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'paypal_payment_method', // data type, string, int, boolean
                'name' => 'paypal_clientid', // unique name for field
                'label' => 'paypalPayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'flutterwavePayment', // data type, string, int, boolean
                'name' => 'flutterwave_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want

            ],
            [
                'type' => 'text', // input fields type
                'data' => 'flutterwave_payment_method', // data type, string, int, boolean
                'name' => 'flutterwave_secretkey', // unique name for field
                'label' => 'flutterwavePayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'flutterwave_payment_method', // data type, string, int, boolean
                'name' => 'flutterwave_publickey', // unique name for field
                'label' => 'flutterwavePayment', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'airtelPayment', // data type, string, int, boolean
                'name' => 'airtel_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want

            ],
            [
                'type' => 'text', // input fields type
                'data' => 'airtel_payment_method', // data type, string, int, boolean
                'name' => 'airtel_secretkey', // unique name for field
                'label' => 'airtel_money', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'airtel_payment_method', // data type, string, int, boolean
                'name' => 'airtel_clientid', // unique name for field
                'label' => 'airtel_money', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'phonepayPayment', // data type, string, int, boolean
                'name' => 'phonepay_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want

            ],
            [
                'type' => 'text', // input fields type
                'data' => 'phonepay_payment_method', // data type, string, int, boolean
                'name' => 'phonepay_app_id', // unique name for field
                'label' => 'App ID', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'phonepay_payment_method', // data type, string, int, boolean
                'name' => 'phonepay_merchant_id', // unique name for field
                'label' => 'Merchant Id', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'phonepay_payment_method', // data type, string, int, boolean
                'name' => 'phonepay_salt_key', // unique name for field
                'label' => 'Salt Key', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'number', // input fields type
                'data' => 'phonepay_payment_method', // data type, string, int, boolean
                'name' => 'phonepay_salt_index', // unique name for field
                'label' => 'Salt Index', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],

            [
                'type' => 'checkbox', // input fields type
                'data' => 'midtransPayment', // data type, string, int, boolean
                'name' => 'midtrans_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want

            ],
            [
                'type' => 'text', // input fields type
                'data' => 'midtrans_payment_method', // data type, string, int, boolean
                'name' => 'midtrans_clientid', // unique name for field
                'label' => 'midtrans', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'cinetpayPayment', // data type, string, int, boolean
                'name' => 'cinet_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want

            ],
            [
                'type' => 'text', // input fields type
                'data' => 'cinet_payment_method', // data type, string, int, boolean
                'name' => 'cinet_siteid', // unique name for field
                'label' => 'Site ID', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'cinet_payment_method', // data type, string, int, boolean
                'name' => 'cinet_apikey', // unique name for field
                'label' => 'Api Key', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'cinet_payment_method', // data type, string, int, boolean
                'name' => 'cinet_secretkey', // unique name for field
                'label' => 'Secret Key', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'sadadpayPayment', // data type, string, int, boolean
                'name' => 'sadad_payment_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want

            ],
            [
                'type' => 'text', // input fields type
                'data' => 'sadad_payment_method', // data type, string, int, boolean
                'name' => 'sadad_id', // unique name for field
                'label' => 'Sadad ID', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'sadad_payment_method', // data type, string, int, boolean
                'name' => 'sadad_key', // unique name for field
                'label' => 'Sadad Key', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'sadad_payment_method', // data type, string, int, boolean
                'name' => 'sadad_domain', // unique name for field
                'label' => 'Sadad Domain', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
        ],
    ],
    'online_service' => [
        'title' => 'Online Service Setting',
        'desc' => 'Online Service Setting',
        'icon' => 'fas fa-chart-line',

        'elements' => [
            [
                'type' => 'checkbox', // input fields type
                'data' => 'googleMeet', // data type, string, int, boolean
                'name' => 'google_meet_method', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want

            ],
            [
                'type' => 'text', // input fields type
                'data' => 'google_meet_method', // data type, string, int, boolean
                'name' => 'google_clientid', // unique name for field
                'label' => 'googleMeet', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'google_meet_method', // data type, string, int, boolean
                'name' => 'google_secret_key', // unique name for field
                'label' => 'googleMeet', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'google_meet_method', // data type, string, int, boolean
                'name' => 'google_appname', // unique name for field
                'label' => 'googleMeet', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'google_meet_method', // data type, string, int, boolean
                'name' => 'content', // unique name for field
                'label' => 'googleMeet', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'google_meet_method', // data type, string, int, boolean
                'name' => 'google_event', // unique name for field
                'label' => 'googleMeet', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'integaration', // data type, string, int, boolean
                'name' => 'is_zoom', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],

            [
                'type' => 'text', // input fields type
                'data' => 'is_zoom', // data type, string, int, boolean
                'name' => 'account_id', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'is_zoom', // data type, string, int, boolean
                'name' => 'client_id', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'is_zoom', // data type, string, int, boolean
                'name' => 'client_secret', // unique name for field
                'label' => 'integration', // you know what label it is
                'rules' => 'required|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
        ],
    ],
    'invoice_setting' => [
        'title' => 'Invoice Setting',
        'desc' => 'Order Related Setting.',
        'icon' => '',
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'inv_prefix', // unique name for field
                'label' => 'lbl_order_prefix', // you know what label it is
                'rules' => 'nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '# - ORDER', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'int', // data type, string, int, boolean
                'name' => 'order_code_start', // unique name for field
                'label' => 'lbl_order_starts', // you know what label it is
                'rules' => 'nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '10001', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'spacial_note', // unique name for field
                'label' => 'lbl_spacial_note', // you know what label it is
                'rules' => 'nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'Thank you for visiting our store and choosing to make a purchase with us.', // default value if you want
            ],
        ],
    ],
    'other_settings' => [
        'title' => 'Other Settings',
        'desc' => 'Other Settings',
        'icon' => 'fas fa-chart-line',

        'elements' => [
            [
                'type' => 'checkbox', // input fields type
                'data' => 'other_settings', // data type, string, int, boolean
                'name' => 'is_event', // unique name for field
                'label' => 'Enable Events', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'other_settings', // data type, string, int, boolean
                'name' => 'is_blog', // unique name for field
                'label' => 'Enable Blogs', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'other_settings', // data type, string, int, boolean
                'name' => 'is_user_push_notification', // unique name for field
                'label' => 'Enable User Push Notification', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'other_settings', // data type, string, int, boolean
                'name' => 'is_provider_push_notification', // unique name for field
                'label' => 'Enable Provider Push Notification', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
           
            [
                'type' => 'checkbox', // input fields type
                'data' => 'other_settings', // data type, string, int, boolean
                'name' => 'enable_chat_gpt', // unique name for field
                'label' => 'Enable ChatGPT', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'other_settings', // data type, string, int, boolean
                'name' => 'test_without_key', // unique name for field
                'label' => 'Test Without Key', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '1', // default value if you want
                'help' => 'Paste the only the Measurement Id of Google Analytics stream.', // Help text for the input field.
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'other_settings', // data type, string, int, boolean
                'name' => 'chatgpt_key', // unique name for field
                'label' => 'Key', // you know what label it is
                'rules' => 'required|min:2', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'Paste the only the Measurement Id of Google Analytics stream.', // default value if you want
            ],

            [
                'type' => 'text', // input fields type
                'data' => 'other_settings', // data type, string, int, boolean
                'name' => 'firebase_key', // unique name for field
                'label' => 'Firebase Key', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
            ],

        ],

    ],
    'bodychart_setting' => [
        'title' => 'Body Chart Setting',
        'desc' => 'Order Related Setting.',
        'icon' => '',
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'theme_mode', // unique name for field
                'label' => 'lbl_theme_mode', // you know what label it is
                'rules' => 'nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'whiteTheme', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'Menubar_position', // unique name for field
                'label' => 'lbl_Menubar_position', // you know what label it is
                'rules' => 'nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'bottom', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'image_handling', // unique name for field
                'label' => 'lbl_image_handling', // you know what label it is
                'rules' => 'nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '', // default value if you want
            ],
            [
                'type' => 'array', // input fields type
                'data' => 'array', // data type, string, int, boolean
                'name' => 'menu_items', // unique name for field
                'label' => 'lbl_menu_items', // you know what label it is
                'rules' => 'nullable|max:191', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'crop', // default value if you want
            ],
            [
                'type' => 'file', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'body_image', // unique name for field
                'label' => 'body Image', // you know what label it is
                'rules' => 'nullable|image|mimes:jpg,png,gif,ico', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'img/logo/mini_logo.png', // default value if you want
                'help' => '', // Help text for the input field.
                'display' => 'raw', // Help text for the input field.
                
            ],

        ],
    ],

    'cancellation_charges' => [
        'title' => 'Cancellation Charges',
        'desc' => 'Cancellation Charges',
        'icon' => '',
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'cancellation_charges', // data type, string, int, boolean
                'name' => 'cancellation_type', // unique name for field
                'label' => 'Cancellation Type', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => 'percentage', // default value if you want
                
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'cancellation_charges', // data type, string, int, boolean
                'name' => 'cancellation_charge', // unique name for field
                'label' => 'Cancellation Charge', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '1', // default value if you want
                
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'cancellation_charges', // data type, string, int, boolean
                'name' => 'cancellation_charge_hours', // unique name for field
                'label' => 'Cancellation Charge Hours', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '1', // default value if you want
                
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'cancellation_charges', // data type, string, int, boolean
                'name' => 'is_cancellation_charge', // unique name for field
                'label' => 'Is Type', // you know what label it is
                'rules' => '', // validation rule of laravel
                'class' => '', // any class for input
                'value' => '0', // default value if you want

            ],


        ],
    ],
];
