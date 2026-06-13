<li class="notification-card section-bg rounded p-4">
    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
        <div class="badge d-flex column-gap-5 row-gap-2 flex-wrap rounded-pill bg-primary-subtle">
            <span>{{ $notification->created_at->diffForHumans() }}</span>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-3">
            
                @php
                    // Ensure $notification->data is decoded into an array
                    $notificationData = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                @endphp

                @if(isset($notificationData['data']))
                    @if (isset($notificationData['data']['notification_group']) && $notificationData['data']['notification_group'] == 'appointment')
                    <p class="mb-0">ID:</p>
                        <p class="mb-0 font-size-14 text-primary">
                            <a href="{{ route('appointment-details', ['id' => $notificationData['data']['id'], 'notification_id' => $notification->id]) }}">
                                #{{ $notificationData['data']['id'] }}
                            </a>
                        </p> 
                    </p>
                    @elseif(isset($notificationData['data']['notification_group']) && $notificationData['data']['notification_group'] == 'requestservice')
                    <p class="mb-0">ID:</p>
                        <p class="mb-0 font-size-14 text-primary">
                        <a href="{{ route('backend.requestservices.index') }}">
                            #{{ $notificationData['data']['id'] }}
                        </a>
                    </p> 
                </p>
                    @elseif( isset($notificationData['data']['notification_group']) && $notificationData['data']['notification_group'] == 'wallet')
                  
                    @else
                    <!-- <p class="mb-0">ID:</p>
                        <p class="mb-0 font-size-14 text-primary">
                        
                    </p>  -->
                </p>
                    @endif
                @endif

        </div>
    </div>

    <div class="d-flex flex-md-row flex-column column-gap-5 row-gap-3 mt-4">
        @php
            // Determine the user based on the notification type
            $user = null;
            if (isset($notificationData['data']['notification_group'])) {
                $userIdKey = $notificationData['data']['notification_group'] == 'requestservice' ? 'vendor_id' : 'user_id';
                $user = \App\Models\User::find($notificationData['data'][$userIdKey] ?? null);
            }
        @endphp

        <!-- <div class="d-flex flex-md-row flex-column gap-3">
            <img src="{{ $user->profile_image ?? default_user_avatar() }}" alt="avatar"
                class="avatar avatar-50 rounded-pill object-fit-cover">
            <div>
                <h6 class="mb-1">{{ $user->full_name ?? default_user_name() }}</h6>
                <span class="text-break">{{ $user->email ?? '--' }}</span>
            </div>
        </div> -->

        @if( isset($notificationData['data']['notification_group']))
        <div>
            <div class="d-flex gap-2 align-items-center mb-2">
                <p class="mb-0">Type:</p>
                <h6 class="mb-0">{{ ucfirst($notificationData['data']['notification_group'] ?? '') }}</h6>
            </div>

            @php
                // Fetch the notification message safely
                $notificationTemplate = \Modules\NotificationTemplate\Models\NotificationTemplateContentMapping::where(
                    'subject',
                    $notificationData['subject'] ?? null
                )->first();
            @endphp


            @if($notificationData['data']['notification_group'] == 'wallet')
            <a href="{{ route('wallet-history') }}">
                <h6 class="text-secondary">{{ $notificationData['subject'] ?? '' }}</h6>
            </a>
            @elseif ($notificationData['data']['notification_group'] == 'appointment')
            <a href="{{ route('appointment-details', ['id' => $notificationData['data']['id']]) }}">
                <h6 class="text-secondary">{{ $notificationData['subject'] ?? '' }}</h6>
            </a>
            @else
                <a href="#">
                    <h6 class="text-secondary">{{ $notificationData['subject'] ?? '' }}</h6>
                </a>
            @endif

              @if(isset($notificationData) && isset($notificationData['data']) && $notificationData['data']['notification_type']=='wallet_refund' || $notificationData['data']['notification_type']=='cancel_appointment'  )

            <span>{{ $notificationData['data']['notification_msg'] ?? '' }}</span>

            @else

            <span>{{ $notificationTemplate->notification_message ?? '' }}</span>

            @endif
            
        </div>
        @else

       <div>

        <div class="d-flex gap-2 align-items-center mb-2">
                <p class="mb-0">Type:</p>

               

                <a >   {{ isset($notificationData['data']['notification_type']) 
        ? \Illuminate\Support\Str::title(str_replace('_', ' ', $notificationData['data']['notification_type'])) 
        : '' 
    }}  </a>
               
            </div>

          <a href="#">
             <h6 class="text-secondary">{{ $notificationData['subject'] ?? '' }}</h6>
         </a>

          <span>{{ $notificationData['subject'] ?? '' }} By {{ $notificationData['data']['logged_in_user_fullname'] ?? '' }}</span>

         </div>

        @endif
    </div>
</li>
