<div class="card-body m-2">
    <div class="table-responsive rounded">

        @php
        $taxData = json_decode($data['tax_data'], true) ?? [];
    @endphp


        <table class="table table-lg m-0" id="service_list_table">
            <thead>
                <tr class="text-white">
                    <th>{{ __('appointment.sr_no') }}</th>
                    <th>{{ __('appointment.lbl_name') }}</th>
                    <th>{{ __('appointment.tax') }}</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($taxData as $index => $tax)


                    <tr>
                        <td>
                            <h6 class="text-primary">
                                {{  $index+1 }}
                            </h6>

                        </td>
                        <td>
                            <h6 class="text-primary">
                                {{ $tax['title'] }}
                            </h6>

                        </td>
                        <td>
                            <p class="m-0">
                                @if( $tax['value'] ==null )
                                   -
                                @else
                                @if($tax['type']=='fixed')
                                  <span >{{ Currency::format($tax['value']) }}</span>
                                @else
                                  <span >{{ $tax['value'] }}% </span>
                                @endif
                                @endif
                            </p>
                        </td>

                    </tr>
                @endforeach


                 @if(count($taxData) <= 0)
                    <tr>
                        <td colspan="5">
                            <div class="my-1 text-danger text-center">{{ __('appointment.no_tax_found') }}
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
