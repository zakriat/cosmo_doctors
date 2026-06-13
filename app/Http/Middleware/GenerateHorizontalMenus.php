<?php

namespace App\Http\Middleware;

use App\Trait\HorizontalMenu;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GenerateHorizontalMenus
{
    use HorizontalMenu;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Menu::make('horizontal_menu', function ($menu) {
            $user = auth()->user();
        if (auth()->check()) {
            
           if ($user->hasRole('admin') || $user->hasRole('demo_admin')) {
    
              $this->mainRoute($menu, [
                  'icon' => 'ph ph-squares-four',
                  'title' => __('sidebar.dashboard'),
                  'route' => 'backend.home',
                  'active' => ['app', 'app/dashboard'],
                  'order' => 0,
              ]);


          } else if ($user->hasRole('doctor')) {
              
              $this->mainRoute($menu, [
                  'icon' => 'ph ph-squares-four',
                  'title' => __('sidebar.dashboard'),
                  'route' => 'backend.doctor-dashboard',
                  'active' => ['app', 'app/doctor-dashboard'],
                  'order' => 0,
              ]);
          } else if ($user->hasRole('receptionist')) {
           
              $this->mainRoute($menu, [
                  'icon' => 'ph ph-squares-four',
                  'title' => __('sidebar.dashboard'),
                  'route' => 'backend.receptionist-dashboard',
                  'active' => ['app', 'app/receptionist-dashboard'],
                  'order' => 0,
              ]);
          } else if ($user->hasRole('vendor')) {

              $this->mainRoute($menu, [
                  'icon' => 'ph ph-squares-four',
                  'title' => __('sidebar.dashboard'),
                  'route' => 'backend.vendor-dashboard',
                  'active' => ['app', 'app/receptionist-dashboard'],
                  'order' => 0,
              ]);
          }

            
            if(!$user->hasRole('receptionist')){
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
            'icon' => 'ph ph-sliders-horizontal',
            'title' => __('sidebar.appointment'),
            'route' => 'backend.appointments.index',
            'permission' => ['view_clinic_appointment_list'],
            'active' => ['app/appointments'],
            'order' => 0,
        ]);


        
        $this->mainRoute($menu, [
          'icon' => 'ph ph-clock-counter-clockwise',
          'title' =>  __('sidebar.encounter'),
          'route' => 'backend.encounter.index',
          'permission' => ['view_encounter'],
          'nickname' => 'encounter',
          'order' => 0,
      ]);

    

        }
            // Access Permission Check
            $menu->filter(function ($item) {
              if ($item->data('permission')) {
                  if (auth()->check()) {
                      if (auth()->user()->hasRole('admin')) {
                          return true;
                      } elseif (auth()->user()->hasAnyPermission($item->data('permission'))) {
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
                          if ($item->hasParent()) {
                              $item->parent()->active();
                          }
                      }
                  }
              }

              return true;
          });
        })->sortBy('order');

        return $next($request);
    }
}
