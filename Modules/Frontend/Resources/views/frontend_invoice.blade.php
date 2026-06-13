<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Inter;
        }

        .column {
            float: left;
            width: 30%;
            padding: 0 10px;
        }

        .row {
            margin: 0 -5px;
        }

        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        .card {
            padding: 16px;
            text-align: center;
            background-color: #F6F7F9;
        }

        table tr td {
            font-size: 14px;
        }

        table thead th {
            font-size: 14px;
        }
    </style>
</head>

<body>




    <div style="padding: 24px 0 0;">
        <div style="padding-bottom: 16px; margin-bottom: 16px; border-bottom:  1px solid #ccc;">
            <div style="overflow: hidden;">
                <div style="float: left; display: inline-block;">
                <div class="logo-default">
                  <a class="navbar-brand text-primary" href="{{ route('frontend.index') }}"> 
                      <div class="logo-main">
                         
                          <div class="logo-normal">
                              <img src="{{ asset(setting('logo')) }}" height="30" alt="{{ app_name() }}">
                          </div>
                    
                      </div>
                  </a>
             </div>
               
                </div>
                <div style="float:right; text-align:right;">
                    <span style="color:#6C757D;">Invoice Date:</span><span style="color: #1C1F34; padding-right: 60px;">
                    {{ DateFormate($data['appointment_date']) }}</span>
                    <span style="color:#6C757D;"> Invoice ID -</span><span style="color: #5F60B9;">#{{ $data['id'] ?? '--' }}</span>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>

      
        <div>
            <div style="float: left; display: inline-block;">
                <p style="color: #6C757D; margin-bottom: 16px;">Thanks, you have already completed the payment for
                    this invoice</p>
            </div>
            <div style="float:right; text-align:right;">

            @php

            $payment_data=$data['appointmenttransaction'] ?? null ;

            @endphp

        

                <span>Payment Status:</span> 

                @if(isset($payment_data) && $payment_data['payment_status']==1) 
                <span style="margin-left: 16px; background-color: #219653; color: #fff; margin-left: 16px; padding: 4px 10px; border-radius: 4px;">PAID</span>
                @else

                <span style="margin-left: 16px; background-color:#dd0d26; color: #fff; margin-left: 16px; padding: 4px 10px; border-radius: 4px;">PENDING</span>

                @endif
                </div>
            <div style="clear: both;"></div>
        </div>

        <!-- {{dd($data)}} -->

        <div style="margin-bottom: 16px;">
            <div style="overflow: hidden;">
                <div style="float: left; width: 75%; display: inline-block;">
                    <h5 style="color: #1C1F34; margin: 0;">Kivicare HQ</h5>
                    <p style="color: #6C757D;  margin-top: 12px; margin-bottom: 0;">1234 Innovation Avenue, Suite 500,
                        Tech City, Silicon Valley, California, 94043, United States</p>
                </div>
                <div style="float:left; width: 25%; text-align:right;">
                    <p style="color: #1C1F34;  margin-top: 12px; margin-bottom: 0;">+ (480) 555-0103</p>
                    <span style="color: #1C1F34; margin-bottom: 12px;">customer@kivicare.com</span>
                </div>
            </div>
        </div>
        <div>
            <h5 style="color: #1C1F34; margin-top: 0;">Appointment Information:</h5>
            <div style="background: #F6F7F9; padding:8px 24px;">
                <div style="display: inline-block;">
                    <span style="color: #1C1F34;">Appointment Date:</span>
                    <span style="color: #6B6B6B; margin-left: 16px;">19/03/2024</span>
                </div>
                <div style="display: inline-block; padding-left: 24px;">
                    <span style="color: #1C1F34;">Appointment Time:</span>
                    <span style="color: #6B6B6B; margin-left: 16px;">12AM</span>
                </div>
                <div style="display: inline-block; padding-left: 24px;">
                    <span style="color: #1C1F34;">Appointment ID:</span>
                    <span style="color: #6B6B6B; margin-left: 16px;">#125</span>
                </div>
            </div>
        </div>

        <div style="padding: 16px 0;">
            <div class="row">
                <div class="column">
                    <h5 style="margin: 8px 0;">Patient Detail:</h5>
                    <div class="card" style="text-align: start;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody style="background: #F6F7F9;">
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34">Name:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">Ryan Matthews</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34;">Mobile Number:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">+1 (800) 555-1234</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34;">Email:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">matthewsryan@gmail.com
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="column">
                    <h5 style="margin: 8px 0;">Clinic Detail:</h5>
                    <div class="card">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody style="background: #F6F7F9;">
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34">Name:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">Sparkle smile dentist
                                        studio</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34;">Mobile Number:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">660-528-2158</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34;">Email:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">sparkledentist@gmail.com
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="column">
                    <h5 style="margin: 8px 0;">Doctor Detail:</h5>
                    <div class="card">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody style="background: #F6F7F9;">
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34; width:50%;">Name test:
                                    </td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">Kim J. Gamez</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34; width:50%;">Mobile
                                        Number:
                                    </td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">660-528-2158</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34; width:50%;">Email:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">alexthompson@gmail.com
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #ccc;">
            <thead style="background: #F6F7F9;">
                <th style="padding:12px 30px; text-align: start;">Services</th>
                <th style="padding:12px 30px; text-align: end;">Price</th>
                <th style="padding:12px 30px; text-align: end;">Amount</th>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:12px 30px; text-align: start;">Cleaning & Polishing</td>
                    <td style="padding:12px 30px; text-align: end;">$255.00</td>
                    <td style="padding:12px 30px; text-align: end;">$255</td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%; border-collapse: collapse; margin-top: 24px;">
            <tbody style="background: #F6F7F9;">
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">Subtotal</td>
                    <td style="padding:12px 30px; text-align: end; color: #1C1F34;">$295</td>
                </tr>
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">Discount (20%)</td>
                    <td style="padding:12px 30px; text-align: end; color: #219653;">-$20</td>
                </tr>
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">Coupon Discount</td>
                    <td style="padding:12px 30px; text-align: end; color: #219653;">-$20</td>
                </tr>
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">Tax <br>(Service Tax 5%,
                        Booking
                        Fee $4.00)</td>
                    <td style="padding:12px 30px; text-align: end; color: #FB2F2F;">$4.75</td>
                </tr>
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #010A0F; border-top:1px solid #ccc;">
                        Total Amount:</td>
                    <td style="padding:12px 30px; text-align: end; color: #010A0F; border-top:1px solid #ccc;">$259.75
                    </td>
                </tr>
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">
                        Advance Payment Amount (5%)</td>
                    <td style="padding:12px 30px; text-align: end; color: #3F414D;">$5
                    </td>
                </tr>
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">
                        Remaining Amount(5%) <span
                            style="margin-left: 16px; background-color: #219653; color: #fff; padding: 4px 10px; border-radius: 4px;">PAID</span>
                    </td>
                    <td style="padding:12px 30px; text-align: end; color: #3F414D;">$254.75
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="bottom-section">
            <h4 style="margin-bottom: 8px;">Terms & Condition</h4>
            <p style="margin:8px 0; font-size: 14px;">By purchasing a subscription, you agree to automatic billing based
                on the selected plan. You can cancel anytime through your account settings, but no refunds will be
                issued for unused periods. Content availability may vary by region and is subject to change.
                Subscriptions are for personal use only, and sharing your account may result in termination. For
                assistance, contact customer support at <a href="#"
                    style="text-decoration: none; color: #5F60B9;">support@kivicare.com.</a>
            </p>
        </div>
        <footer style="margin-top: 8px;">
            <div style="display: inline; vertical-align: middle; margin-right: 10px;">
                <h5 style="display: inline;">For more information, visit our website:</h5>
                <a href="www.kivicare.com" style="color: #5F60B9;"> www.kivicare.com</a>
                <h5 style="display: block; margin: 8px 0 0;">© 2024 All Rights Reserved by IQONIC Design</h5>
            </div>
        </footer>
    </div>
</body>

</html>