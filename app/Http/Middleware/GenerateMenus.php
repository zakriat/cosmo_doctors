<?php

namespace App\Http\Middleware;

use App\Trait\Menu;

class GenerateMenus
{
    use Menu;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle()
    {
        return \Menu::make('menu', function ($menu) {
            if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')) {
                $this->staticMenu($menu, [
                    'title' =>  __('menu.main'),
                    'order' => 0
                ]);
                $this->mainRoute($menu, [
                    'icon' => 'ph ph-squares-four',
                    'title' => __('sidebar.dashboard'),
                    'route' => 'backend.home',
                    'active' => ['app', 'app/dashboard'],
                    'order' => 0,
                ]);
            } else if (auth()->user()->hasRole('doctor')) {
                $this->staticMenu($menu, ['title' => __('menu.main'), 'order' => 0]);

                $this->mainRoute($menu, [
                    'icon' => 'ph ph-squares-four',
                    'title' => __('sidebar.dashboard'),
                    'route' => 'backend.doctor-dashboard',
                    'active' => ['app', 'app/doctor-dashboard'],
                    'order' => 0,
                ]);
            } else if (auth()->user()->hasRole('receptionist')) {
                $this->staticMenu($menu, ['title' => __('menu.main'), 'order' => 0]);

                // main
                $this->mainRoute($menu, [
                    'icon' => 'ph ph-squares-four',
                    'title' => __('sidebar.dashboard'),
                    'route' => 'backend.receptionist-dashboard',
                    'active' => ['app', 'app/receptionist-dashboard'],
                    'order' => 0,
                ]);
            } else if (auth()->user()->hasRole('nurse')) {
                $this->staticMenu($menu, ['title' => __('menu.main'), 'order' => 0]);

                // main
                $this->mainRoute($menu, [
                    'icon' => 'ph ph-squares-four',
                    'title' => __('sidebar.dashboard'),
                    'route' => 'backend.nurse-dashboard',
                    'active' => ['app', 'app/nurse-dashboard'],
                    'order' => 0,
                ]);
            } else if (auth()->user()->hasRole('vendor')) {

                $this->mainRoute($menu, [
                    'icon' => 'ph ph-squares-four',
                    'title' => __('sidebar.dashboard'),
                    'route' => 'backend.vendor-dashboard',
                    'active' => ['app', 'app/receptionist-dashboard'],
                    'order' => 0,
                ]);
            }

            // // ── Triage: nurse sees flat link, admin sees parent menu ──────────
            // if (auth()->user()->hasRole('nurse')) {
            //     $this->mainRoute($menu, [
            //         'icon'       => 'ph ph-clipboard-text',
            //         'title'      => __('triage.menu_title'),
            //         'route'      => 'backend.triage.index',
            //         'active'     => 'app/triage',
            //         'permission' => ['view_triage_queue'],
            //         'order'      => 0,
            //     ]);
            // }

            
            // ── Triage: nurse/doctor sees flat link, admin sees parent menu ──────────
            if (auth()->user()->hasRole('nurse') || auth()->user()->hasRole('doctor')) {
                $this->mainRoute($menu, [
                    'icon'       => 'ph ph-clipboard-text',
                    'title'      => __('triage.menu_title'),
                    'route'      => 'backend.triage.index',
                    'active'     => 'app/triage',
                    'permission' => ['view_triage_queue'],
                    'order'      => 0,
                ]);
            }


            if (auth()->user()->hasRole(['admin', 'demo_admin'])) {
                $triageMenu = $this->parentMenu($menu, [
                    'icon'       => 'ph ph-clipboard-text',
                    'title'      => __('triage.menu_title'),
                    'nickname'   => 'triage',
                    'permission' => ['view_triage_queue'],
                    'order'      => 0,
                ]);
                $this->childMain($triageMenu, [
                    'icon'       => 'ph ph-list-bullets',
                    'title'      => __('triage.queue'),
                    'route'      => 'backend.triage.index',
                    'active'     => 'app/triage',
                    'permission' => ['view_triage_queue'],
                    'order'      => 0,
                ]);
                $this->childMain($triageMenu, [
                    'icon'       => 'ph ph-tag',
                    'title'      => __('triage.categories'),
                    'route'      => 'backend.triage-category.index',
                    'active'     => 'app/triage-category',
                    'permission' => ['view_triage_category'],
                    'order'      => 0,
                ]);
            }
            // ─────────────────────────────────────────────────────────────────

            $this->mainRoute($menu, [
                'icon' => 'ph ph-sliders-horizontal',
                'title' => __('sidebar.appointment'),
                'route' => 'backend.appointments.index',
                'permission' => ['view_clinic_appointment_list'],
                'active' => ['app/appointments'],
                'order' => 0,
            ]);
            
            // Blood Tests Menu Item
            $this->mainRoute($menu, [
                'icon' => 'ph ph-test-tube',
                'title' => '🩸 Blood Tests',
                'route' => 'backend.blood-tests.index',
                'permission' => ['view_clinic_appointment_list'],
                'active' => ['app/blood-tests'],
                'order' => 0,
            ]);
            
            if (auth()->user()->hasRole('receptionist') || auth()->user()->hasRole('nurse')) {
                $this->mainRoute($menu, [
                    'icon' => 'ph ph-list-bullets',
                    'title' => __('sidebar.encounter'),
                    'route' => 'backend.encounter.index',
                    'active' => 'app/encounter',
                    'permission' => ['view_encounter'],
                    'order' => 0,
                ]);
            }

            if (auth()->user()->hasRole(['admin', 'demo_admin',])) {
                $encounter = $this->parentMenu($menu, [
                    'icon' => 'ph ph-clock-counter-clockwise',
                    'title' =>  __('sidebar.encounter'),
                    'route' => 'backend.encounter.index',
                    'permission' => ['view_encounter'],
                    'nickname' => 'encounter',
                    'order' => 0,
                ]);
                $this->childMain($encounter, [
                    'icon' => 'ph ph-list-bullets',
                    'title' => __('sidebar.encounter'),
                    'route' => 'backend.encounter.index',
                    'active' => 'app/encounter',
                    'permission' => ['view_encounter'],
                    'order' => 0,
                ]);

                $this->childMain($encounter, [
                    'icon' => 'ph ph-layout',
                    'title' => __('sidebar.encounter_template'),
                    'route' => 'backend.encounter-template.index',
                    'active' => 'app/encounter-template',
                    'permission' => ['view_encounter_template'],
                    'order' => 0,
                ]);

                $this->childMain($encounter, [
                    'icon' => 'ph ph-warning-diamond',
                    'title' => __('sidebar.problems'),
                    'route' => 'backend.problems.index',
                    'active' => 'app/problems',
                    'permission' => ['view_encounter'],
                    'order' => 0,
                ]);
                $this->childMain($encounter, [
                    'icon' => 'ph ph-eye',
                    'title' => __('appointment.observation'),
                    'route' => 'backend.observation.index',
                    'active' => 'app/observation',
                    'permission' => ['view_encounter'],
                    'order' => 0,
                ]);
            }

            if (!auth()->user()->hasRole(['doctor'])) {
                $doctor = $this->parentMenu($menu, [
                    'icon' => 'ph ph-stethoscope',
                    'title' => __('sidebar.doctor'),
                    'route' => 'backend.doctor.index',
                    'permission' => ['view_doctors_session'],
                    'nickname' => 'doctor',
                    'order' => 0,
                ]);
                $this->childMain($doctor, [
                    'icon' => 'ph ph-stethoscope',
                    'title' => __('sidebar.doctor'),
                    'route' => 'backend.doctor.index',
                    'active' => 'app/doctor',
                    'permission' => ['view_doctors'],
                    'order' => 0,
                ]);
                $this->childMain($doctor, [
                    'icon' => 'ph ph-clock',
                    'title' => __('clinic.doctor_session'),
                    'route' => 'backend.doctor-session.index',
                    'active' => 'app/doctor-session',
                    'permission' => ['view_doctors_session'],
                    'order' => 0,
                ]);
            }

            $this->mainRoute($menu, [
                'icon' => 'ph ph-calendar-heart',
                'title' => __('clinic.specialization'),
                'route' => 'backend.specializations.index',
                'active' => ['app/specializations'],
                'permission' => ['view_specialization'],
                'order' => 0,
            ]);


            $permissionsToCheck = ['view_clinics_center', 'view_clinics_category', 'view_clinics_service', 'view_doctors', 'view_doctors_session', 'view_clinic_patient_list', 'view_patient_soap', 'view_clinic_appointment_list', 'view_encounter_template', 'view_encounter'];

            if (collect($permissionsToCheck)->contains(fn($permission) => auth()->user()->can($permission))) {

                if (multiVendor() == "1" || auth()->user()->hasRole(['admin', 'demo_admin', 'vendor', 'doctor', 'receptionist'])) {

                    $this->staticMenu($menu, ['title' => __('sidebar.clinic_center'), 'order' => 0]);
                }
            }
            if (!auth()->user()->hasRole('receptionist')) {
                $this->mainRoute($menu, [
                    'icon' => 'ph ph-hospital',
                    'title' => __('sidebar.clinic'),
                    'route' => 'backend.clinics.index',
                    'permission' => ['view_clinics_center'],
                    'active' => ['app/clinics'],
                    'order' => 0,
                ]);
            }
            $this->mainRoute($menu, [
                'icon' => 'ph ph-list-bullets',
                'title' => __('sidebar.categories'),
                'route' => 'backend.category.index',
                'permission' => ['view_clinics_category'],
                'active' => ['app/category'],
                'order' => 0,
            ]);



            $this->mainRoute($menu, [
                'icon' => 'ph ph-first-aid-kit',
                'title' => __('sidebar.services'),
                'route' => 'backend.services.index',
                'active' => ['app/services'],
                'permission' => ['view_clinics_service'],
                'order' => 0,
            ]);



            if (auth()->user()->hasRole(['doctor'])) {
                $this->mainRoute($menu, [
                    'icon' => 'ph ph-clock',
                    'title' => __('clinic.doctor_session'),
                    'route' => 'backend.doctor-session.index',
                    'active' => 'app/doctor-session',
                    'permission' => ['view_doctors_session'],
                    'order' => 0,
                ]);
            }


            if (auth()->user()->hasRole(['doctor'])) {
                $this->mainRoute($menu, [
                    'icon' => 'ph ph-list-bullets',
                    'title' => __('sidebar.encounter'),
                    'route' => 'backend.encounter.index',
                    'active' => 'app/encounter',
                    'permission' => ['view_encounter'],
                    'order' => 0,
                ]);
            }
            $this->mainRoute($menu, [
                'icon' => ' ph ph-star',
                'title' => __('sidebar.reviews'),
                'route' => ['backend.doctors.review'],
                'active' => ['app/doctors-review'],
                'permission' => ['view_reviews'],
                'order' => 0,
            ]);


            //shop

            // $permissionsToCheck = ['view_product', 'view_brand', 'view_product_category', 'view_product_subcategory','view_unit','view_tag','view_product_variation',
            // 'view_order','view_supply','view_logistics','view_shipping_zones'];


            // if (collect($permissionsToCheck)->contains(fn ($permission) => auth()->user()->can($permission))) {
            //     $this->staticMenu($menu, ['title' => __('sidebar.shop'), 'order' => 0]);
            // }
            // $product = $this->parentMenu($menu, [
            //     'icon' => 'fa-solid fa-store',
            //     'title' => __('sidebar.product'),
            //     'route' => 'backend.products.index',
            //     'permission' => ['view_product'],
            //     'nickname' => 'PR',
            //     'order' => 0,
            // ]);
            // $this->childMain($product, [
            //     'title' => __('sidebar.all_product'),
            //     'route' => 'backend.products.index',
            //     'active' => 'app/products',
            //     'shortTitle' => 'AP',
            //     'permission' => ['view_product'],
            //     'order' => 0,
            // ]);
            // $this->childMain($product, [
            //     'title' => __('sidebar.brand'),
            //     'route' => 'backend.brands.index',
            //     'shortTitle' => 'BR',
            //     'permission' => ['view_brand'],
            //     'active' => ['app/brands'],
            //     'order' => 0,
            // ]);
            // $this->childMain($product, [
            //     'title' => __('sidebar.categories'),
            //     'route' => 'backend.products-categories.index',
            //     'shortTitle' => 'C',
            //     'permission' => ['view_product_category'],
            //     'active' => ['app/products-categories'],
            //     'order' => 0,
            // ]);
            // $this->childMain($product, [
            //     'title' => __('sidebar.sub_categories'),
            //     'route' => 'backend.products-categories.index_nested',
            //     'shortTitle' => 'SC',
            //     'permission' => ['view_product_subcategory'],
            //     'active' => ['app/products-sub-categories'],
            //     'order' => 0,
            // ]);

            // $this->childMain($product, [
            //     'title' => __('sidebar.units'),
            //     'route' => 'backend.units.index',
            //     'shortTitle' => 'U',
            //     'permission' => ['view_unit'],
            //     'active' => ['app/units'],
            //     'order' => 0,
            // ]);

            // $this->childMain($product, [
            //     'title' => __('sidebar.tag'),
            //     'route' => 'backend.tags.index',
            //     'shortTitle' => 'T',
            //     'permission' => ['view_tag'],
            //     'active' => ['app/tags'],
            //     'order' => 0,
            // ]);

            // $this->mainRoute($menu, [
            //     'icon' => 'fa-solid fa-swatchbook',
            //     'title' => __('sidebar.variations'),
            //     'route' => ['backend.variations.index'],
            //     'active' => ['app/variations'],
            //     'permission' => ['view_product_variation'],
            //     'order' => 0,
            // ]);

            // $this->mainRoute($menu, [
            //     'icon' => 'fa-solid fa-bag-shopping',
            //     'title' => __('sidebar.orders'),
            //     'permission' => 'view_tag',
            //     'route' => ['backend.orders.index'],
            //     'permission' => ['view_order'],
            //     'active' => ['app/orders'],
            //     'order' => 0,
            // ]);

            // $supply = $this->parentMenu($menu, [
            //     'icon' => 'fa-solid fa-truck-field',
            //     'title' => __('sidebar.supply'),
            //     'nickname' => 'supply',
            //     'permission' => ['view_supply'],
            //     'order' => 0,
            // ]);

            // $this->childMain($supply, [
            //     'title' => __('sidebar.logistics'),
            //     'route' => 'backend.logistics.index',
            //     'shortTitle' => 'AP',
            //     'active' => ['app/logistics'],
            //     'permission' => ['view_logistics'],
            //     'order' => 0,
            // ]);

            // $this->childMain($supply, [
            //     'title' => __('sidebar.logistic_zone'),
            //     'route' => 'backend.logistic-zones.index',
            //     'permission' => ['view_shipping_zones'],
            //     'shortTitle' => 'AP',
            //     'active' => ['app/logistic-zones'],
            //     'order' => 0,
            // ]);

            // FINANCE Static

            $permissionsToCheck = ['view_customer', 'view_clinic_receptionist_list', 'view_clinic_nurse_list', 'view_vendor_list'];

            if (collect($permissionsToCheck)->contains(fn($permission) => auth()->user()->can($permission))) {
                $this->staticMenu($menu, ['title' => __('sidebar.user'), 'order' => 0]);
            }


            $this->mainRoute($menu, [
                'icon' => 'ph ph-users',
                'title' =>  __('sidebar.patient'),
                'route' => 'backend.customers.index',
                'active' => ['app/customers'],
                'permission' => 'view_customer',
                'order' => 0,
            ]);

            $this->mainRoute($menu, [
                'icon' => 'ph ph-user-circle-gear',
                'title' => __('sidebar.receptionist'),
                'route' => 'backend.receptionist.index',
                'active' => ['app/receptionist'],
                'permission' => ['view_clinic_receptionist_list'],
                'order' => 0,
            ]);

            $this->mainRoute($menu, [
                'icon' => 'ph ph-user-nurse',
                'title' => __('sidebar.nurse'),
                'route' => 'backend.nurse.index',
                'active' => ['app/nurse'],
                'permission' => ['view_clinic_nurse_list'],
                'order' => 0,
            ]);
            if (multiVendor() == "1" && auth()->user()->hasRole(['admin', 'demo_admin'])) {
                $this->mainRoute($menu, [
                    'icon' => 'ph ph-user-square',
                    'title' => __('sidebar.vendors'),
                    'route' => 'backend.multivendors.index',
                    'active' => ['app/multivendors'],
                    'permission' => ['view_vendor_list'],
                    'order' => 0,
                ]);
            }
            $permissionsToCheck = ['view_tax', 'view_earning', 'view_billing_record'];

            if (collect($permissionsToCheck)->contains(fn($permission) => auth()->user()->can($permission))) {
                $this->staticMenu($menu, ['title' => __('sidebar.finance'), 'order' => 0]);
            }


            $this->mainRoute($menu, [
                'icon' => 'ph ph-percent',
                'title' => __('sidebar.tax'),
                'route' => 'backend.tax.index',
                'active' => ['app/tax'],
                'permission' => 'view_tax',
                'order' => 0,
            ]);
            $this->mainRoute($menu, [
                'icon' => 'ph ph-invoice',
                'title' => __('sidebar.billing_record'),
                'route' => 'backend.billing-record.index',
                'active' => ['app/billing-record'],
                'permission' => 'view_billing_record',
                'order' => 0,
            ]);
            $this->mainRoute($menu, [
                'icon' => 'ph ph-currency-dollar',
                'title' => __('sidebar.doctor_earning'),
                'route' => 'backend.earnings.index',
                'active' => ['app/earnings'],
                'permission' => ['view_doctor_earning'],
                'order' => 0,
            ]);
            if (multiVendor() == "1" && auth()->user()->hasRole(['admin', 'demo_admin'])) {
                $this->mainRoute($menu, [
                    'icon' => 'ph ph-user-circle-check',
                    'title' => __('sidebar.vendor_earning'),
                    'route' => 'backend.vendor-earnings.index',
                    'active' => ['app/vendor-earnings'],
                    'permission' => ['view_vendor_earning'],
                    'order' => 0,
                ]);
            }



            //Report

            $permissionsToCheck = ['view_daily_bookings', 'view_overall_bookings', 'view_staff_payouts', 'view_staff_service', 'view_order_reports', 'view_commission_reports', 'view_appointment_overview', 'view_clinic_overview'];

            if (collect($permissionsToCheck)->contains(fn($permission) => auth()->user()->can($permission))) {
                $this->staticMenu($menu, ['title' => __('sidebar.reports'), 'order' => 0]);
            }



            if (auth()->user()->hasRole('vendor')) {

                $this->mainRoute($menu, [
                    'icon' => 'ph ph-percent',
                    'title' =>  __('appointment.revenue_breakdown'),
                    'route' => 'backend.reports.commission-revenue',
                    'active' => ['app/commission-revenue'],
                    'order' => 0,
                ]);
            }


            if (auth()->user()->hasRole('vendor') || auth()->user()->hasRole('demo_admin') ||  auth()->user()->hasRole('admin')) {

                $this->mainRoute($menu, [
                    'icon' => 'ph ph-file-magnifying-glass',
                    'title' =>  __('dashboard.lbl_title_appointment_overview'),
                    'route' => 'backend.reports.appointment-overview',
                    'active' => ['app/appointment-overview'],
                    'order' => 0,
                ]);
                $this->mainRoute($menu, [
                    'icon' => 'ph ph-map-pin-plus',
                    'title' =>  __('sidebar.clinic_overview'),
                    'route' => 'backend.reports.clinic-overview',
                    'active' => ['app/clinic-overview'],
                    'order' => 0,
                ]);
            }


            if (multiVendor() == "1") {
                $this->mainRoute($menu, [
                    'icon' => 'ph ph-share',
                    'title' =>  __('sidebar.request_service'),
                    'route' => 'backend.requestservices.index',
                    'active' => ['app/requestservices'],
                    'permission' => ['view_request_service'],
                    'order' => 0,
                ]);
            }


            $this->mainRoute($menu, [
                'icon' => 'ph ph-currency-dollar',
                'title' => __('sidebar.doctor_payout'),
                'route' => 'backend.reports.doctor-payout-report',
                'active' => ['app/doctor-payout-report'],
                'permission' => ['view_doctor_payouts'],
                'order' => 0,
            ]);
            if (multiVendor() == "1" && auth()->user()->hasRole(['admin', 'demo_admin'])) {
                $this->mainRoute($menu, [
                    'icon' => 'ph ph-currency-dollar-simple',
                    'title' => __('sidebar.vendor_payout'),
                    'route' => 'backend.reports.vendor-payout-report',
                    'active' => ['app/vendor-payout-report'],
                    'permission' => ['view_vendor_payouts'],
                    'order' => 0,
                ]);
            }

            // System Static
            $permissionsToCheck = [
                'view_setting',
                'add_setting',
                'edit_setting',
                'delete_setting',
                'view_location',
                'view_city',
                'view_state',
                'view_country',
                'view_pages',
                'view_notification',
                'view_notification_template',
                'view_app_banner',
                'view_constant',
                'view_permission',
                'view_promotions',
                'view_vital',
                'view_subscription',
                'view_my_account',
                'view_subscription_list',
                'view_plan_list',
                'view_plan_limitation',
                'view_backup'
            ];

            if (collect($permissionsToCheck)->contains(fn($permission) => auth()->user()->can($permission))) {
                $this->staticMenu($menu, ['title' => __('sidebar.system'), 'order' => 0]);
            }
            if (multiVendor() == "1" && auth()->user()->hasRole(['admin', 'demo_admin'])) {
                $this->mainRoute($menu, [
                    'icon' => 'ph ph-airplay',
                    'title' => __('sidebar.system_service'),
                    'route' => 'backend.system-service.index',
                    'active' => ['app/system-service'],
                    'permission' => ['view_system_service'],
                    'order' => 0,
                ]);
            }

            // --- INCIDENCE REPORT ---
            $this->mainRoute($menu, [
                'icon' => 'ph ph-map-pin-plus',
                'title' => __('messages.incidence'),
                'route' => 'backend.incidence.index',
                'active' => 'app/incidence',
                'permission' => ['view_incidence_report'],
                'order' => 0,
            ]);

            // --- BLOG ---
            $this->mainRoute($menu, [
                'icon' => 'ph ph-pencil-simple',
                'title' => __('sidebar.blog'),
                'route' => 'backend.blog.index',
                'active' => ['app/blog'],
                'order' => 0,
            ]);

            $location = $this->parentMenu($menu, [
                'icon' => 'ph ph-map-pin-line',
                'title' => __('sidebar.location'),
                'nickname' => 'location',
                'permission' => ['view_location'],
                'order' => 0,
            ]);

            $this->childMain($location, [
                'title' => __('sidebar.city'),
                'route' => 'backend.city.index',
                'active' => 'app/city',
                'shortTitle' => 'CT',
                'permission' => ['view_city'],
                'order' => 0,
                'icon' => 'ph ph-city',
            ]);
            $this->childMain($location, [
                'title' => __('sidebar.state'),
                'route' => 'backend.state.index',
                'shortTitle' => 'CT',
                'permission' => ['view_state'],
                'active' => ['app/state'],
                'order' => 0,
                'icon' => 'ph ph-map-trifold',
            ]);
            $this->childMain($location, [
                'title' => __('sidebar.country'),
                'route' => 'backend.country.index',
                'shortTitle' => 'CT',
                'active' => ['app/country'],
                'permission' => ['view_country'],
                'order' => 0,
                'icon' => 'ph ph-globe-hemisphere-east',
            ]);

            $this->mainRoute($menu, [
                'icon' => ' ph ph-note',
                'title' => __('page.title'),
                'route' => ['backend.pages.index'],
                'active' => ['app/pages'],
                'permission' => ['view_pages'],
                'order' => 0,
            ]);

            // --- APP BANNER ---
            $this->mainRoute($menu, [
                'icon' => 'ph ph-exam',
                'title' => __('sidebar.app_banner'),
                'route' => 'backend.app-banners.index',
                'active' => 'app/app-banners',
                'permission' => ['view_app_banner'],
                'order' => 0,
            ]);

            // --- FAQ ---
            $this->mainRoute($menu, [
                'icon' => 'ph ph-question',
                'title' => __('messages.faq_title'),
                'route' => 'backend.faqs.index',
                'active' => ['app/faqs'],
                'order' => 0,
            ]);

            // --- CUSTOM FORMS ---
            if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')) {
                $custom_form = $this->parentMenu($menu, [
                    'icon' => 'ph ph-table',
                    'title' => __('messages.customforms'),
                    'nickname' => 'custom_form',
                    'permission' => ['view_notification'],
                    'order' => 0,
                ]);
                $this->childMain($custom_form, [
                    'icon' => 'ph ph-list-bullets',
                    'title' => __('messages.customforms_list'),
                    'route' => 'backend.custom-form.index',
                    'shortTitle' => 'Li',
                    'active' => 'app/settings#/customform',
                    'permission' => ['view_notification'],
                    'order' => 0,
                ]);
            }

            // --- NOTIFICATION ---
            $notification = $this->parentMenu($menu, [
                'icon' => 'ph ph-bell',
                'title' => __('notification.title'),
                'nickname' => 'notifications',
                'permission' => ['view_notification'],
                'order' => 0,
            ]);

            $this->childMain($notification, [
                'icon' => 'ph ph-list-bullets',
                'title' => __('notification.list'),
                'route' => 'backend.notifications.index',
                'shortTitle' => 'Li',
                'active' => 'app/notifications',
                'permission' => ['view_notification'],
                'order' => 0,
            ]);
            if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')) {
                $this->childMain($notification, [
                    'icon' => 'ph ph-layout',
                    'title' => __('notification.template'),
                    'route' => 'backend.notification-templates.index',
                    'shortTitle' => 'TE',
                    'active' => 'app/notification-templates*',
                    'permission' => ['view_notification_template'],
                    'order' => 0,
                ]);
            }

            // --- SETTING ---
            $this->mainRoute($menu, [
                'icon' => 'ph ph-gear-six',
                'title' => __('menu.settings'),
                'route' => 'backend.settings',
                'active' => 'app/settings',
                'permission' => ['view_setting'],
                'order' => 0,
            ]);

            // --- FRONTEND SETTING ---
            if (!auth()->user()->hasRole('doctor') && !auth()->user()->hasRole('vendor') && !auth()->user()->hasRole('receptionist')) {
                $this->mainRoute($menu, [
                    'icon' => 'ph ph-layout',
                    'title' =>  __('sidebar.frontend_setting'),
                    'route' => 'frontend_setting.index',
                    'active' => ['app/frontend_setting'],
                    'permission' => 'view_customer',
                    'order' => 0,
                ]);
            }

            // --- LOG/BACKUPS ---
            $location11 = $this->parentMenu($menu, [
                'icon' => 'ph ph-note',
                'title' => __('sidebar.log'),
                'nickname' => 'log',
                'permission' => ['view_backup'],
                'order' => 0,
            ]);
            $this->childMain($location11, [
                'title' => __('sidebar.backups'),
                'route' => 'backend.backups.index',
                'active' => 'app/backups',
                'shortTitle' => '',
                'permission' => ['view_backup'],
                'order' => 0,
                'icon' => 'ph ph-note',
            ]);
            $this->childMain($location11, [
                'title' => __('sidebar.activity_logs'),
                'route' => 'backend.backups.logs',
                'active' => 'app/backups/logs',
                'shortTitle' => '',
                'permission' => ['view_backup'],
                'order' => 0,
                'icon' => 'ph ph-note',
            ]);

            // --- ACCESS CONTROL ---
            if (auth()->user()->hasRole('admin')) {

                $this->mainRoute($menu, [
                    'icon' => 'ph ph-devices',
                    'title' => __('sidebar.access_control'),
                    'route' => 'backend.permission-role.list',
                    'active' => ['app/permission-role'],
                    'order' => 10,
                ]);
            }

            // Access Permission Check
            $menu->filter(function ($item) {
                if ($item->data('permission')) {
                    if (auth()->check()) {
                        if (\Auth::getDefaultDriver() == 'admin') {
                            return true;
                        }
                        if (auth()->user()->hasAnyPermission($item->data('permission'), \Auth::getDefaultDriver())) {
                            return true;
                        }
                    }

                    return false;
                } else {
                    return true;
                }
            });
            // Set Active Menu
            $menu->filter(function ($item) {
                if ($item->activematches) {
                    $activematches = (is_string($item->activematches)) ? [$item->activematches] : $item->activematches;
                    foreach ($activematches as $pattern) {
                        if (request()->is($pattern)) {
                            $item->active();
                            $item->link->active();
                            if ($item->hasParent()) {
                                $item->parent()->active();
                            }
                        }
                    }
                }

                return true;
            });
        })->sortBy('order');
    }
}
