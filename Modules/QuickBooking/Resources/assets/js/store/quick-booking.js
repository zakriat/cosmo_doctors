import { defineStore } from 'pinia'

export const useQuickBooking = defineStore('quickBooking', {
    state: () => ({
        user : {
            "email": null,
            "first_name": null,
            "last_name": null,
            "mobile": null,
            "gender" : null
        },
        booking: {
            "clinic_id": null,
            "system_service_id": null,
            "vendor_id": null,
            "date": null,
            "start_time": null,
            "note": "",
            "category_id": null,
            "doctor_id": null,
            "services": [
                {
                    "doctor_id": null,
                    "service_id": null,
                    "service_name": null,
                    "service_price": null,
                    "duration_min": null,
                    "start_date_time": null,
                    "discount_amount": 0,
                    "discount_type": null,
                    "inclusive_tax_details": null,
                    "price": null,
                    "discount_value": null,
                    "payable_amount": 0,
                    'vendor_name':null
                }
            ]
        },
        bookingResponse: null
    }),
    actions: {
        updateUserValues(payload) {
            this.user[payload.key] = payload.value
        },
        updateBookingValues(payload) {
            this.booking[payload.key] = payload.value
        },
        updateBookingEmployeeValues(payload) {
            this.booking['services'][0].employee_id = payload
        },
        updateBookingServiceTimeValues(payload) {
            this.booking['services'][0].start_date_time = payload
        },
        updateBookingResponse (payload) {
            this.bookingResponse = payload
        },
        resetState () {
            this.bookingResponse = null
            this.booking = {
                "clinic_id": null,
                "system_service_id": null,
                "vendor_id": null,
                "date": null,
                "start_time": null,
                "note": "",
                "category_id": null,
                "doctor_id": null,
                "services": [
                    {
                        "doctor_id": null,
                        "service_id": null,
                        "service_name": null,
                        "service_price": null,
                        "duration_min": null,
                        "start_date_time": null,
                        "discount_amount": 0,
                        "discount_type": null,
                        "discount_value": null,
                        "inclusive_tax_details": null,
                        "price": null,
                        "payable_amount": 0,
                        'vendor_name':null
                    }
                ]
    
            }
            this.user = {
                "email": null,
                "first_name": null,
                "last_name": null,
                "mobile": null,
                "gender" : null
            }
        }
    }
  })
