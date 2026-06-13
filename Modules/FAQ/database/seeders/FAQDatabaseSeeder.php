<?php

namespace Modules\FAQ\database\seeders;

use Illuminate\Database\Seeder;

class FAQDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \DB::table('faqs')->delete();
        if (env('IS_DUMMY_DATA')) {

        \DB::table('faqs')->insert(array(
            0 =>
                array(
                    'id' => 1,
                    'question' => 'What is KiviCare?',
                    'answer' => 'KiviCare is the most affordable self-hosted clinic and patient management system built on the WordPress platform. It allows you to easily set up an online clinic, manage patient records, appointments, and billing efficiently.',
                    'status' => 1,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'deleted_by' => NULL,
                    'created_at' => '2024-12-19 06:43:30',
                    'updated_at' => '2024-12-19 06:43:30',
                    'deleted_at' => NULL,
                ),
            1 =>
                array(
                    'id' => 2,
                    'question' => 'How do I set up my online clinic using KiviCare?',
                    'answer' => 'To set up your online clinic, simply install KiviCare on your WordPress site, configure your clinic details, and customize it according to your needs. The system allows you to manage patient records, schedule appointments, and more.',
                    'status' => 1,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'deleted_by' => NULL,
                    'created_at' => '2024-12-19 06:44:16',
                    'updated_at' => '2024-12-19 06:44:16',
                    'deleted_at' => NULL,
                ),
            2 =>
                array(
                    'id' => 3,
                    'question' => 'What features does KiviCare offer?',
                    'answer' => 'KiviCare offers features such as patient management, appointment scheduling, billing and invoicing, appointment reminders, medical history tracking, patient portal, and much more. It is designed to streamline clinic operations with ease.',
                    'status' => 1,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'deleted_by' => NULL,
                    'created_at' => '2024-12-19 06:44:36',
                    'updated_at' => '2024-12-19 06:44:36',
                    'deleted_at' => NULL,
                ),
            3 =>
                array(
                    'id' => 4,
                    'question' => 'Can I manage multiple clinics with KiviCare?',
                    'answer' => 'Yes, KiviCare supports managing multiple clinics from a single dashboard. You can add and manage multiple clinic locations and handle all patient information and appointments across them seamlessly.',
                    'status' => 1,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'deleted_by' => NULL,
                    'created_at' => '2024-12-19 06:44:57',
                    'updated_at' => '2024-12-19 06:44:57',
                    'deleted_at' => NULL,
                ),
            4 =>
                array(
                    'id' => 5,
                    'question' => 'How can I manage appointments on KiviCare?',
                    'answer' => 'To manage appointments, simply use the integrated appointment scheduler in the admin panel. You can set available times, manage appointments, and even send automated reminders to patients about their upcoming visits.',
                    'status' => 1,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'deleted_by' => NULL,
                    'created_at' => '2024-12-19 06:45:14',
                    'updated_at' => '2024-12-19 06:45:14',
                    'deleted_at' => NULL,
                ),
            5 =>
                array(
                    'id' => 6,
                    'question' => 'Can KiviCare help with patient billing?',
                    'answer' => 'Yes, KiviCare allows you to generate and manage invoices for your patients. You can easily create bills, track payments, and offer different payment options within the system.',
                    'status' => 1,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'deleted_by' => NULL,
                    'created_at' => '2024-12-19 06:45:33',
                    'updated_at' => '2024-12-19 06:45:33',
                    'deleted_at' => NULL,
                ),
            6 =>
                array(
                    'id' => 7,
                    'question' => 'Is there a patient portal in KiviCare?',
                    'answer' => 'Yes, KiviCare includes a patient portal where patients can view their medical records, appointment history, and upcoming visits. Patients can also request appointments and communicate with clinic staff.',
                    'status' => 1,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'deleted_by' => NULL,
                    'created_at' => '2024-12-19 06:45:48',
                    'updated_at' => '2024-12-19 06:45:48',
                    'deleted_at' => NULL,
                ),
            7 =>
                array(
                    'id' => 8,
                    'question' => 'How do I customize KiviCare for my clinic?',
                    'answer' => 'You can customize KiviCare by accessing the settings in the admin panel. From here, you can modify clinic details, set up working hours, define specialties, adjust themes, and more.',
                    'status' => 1,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'deleted_by' => NULL,
                    'created_at' => '2024-12-19 06:46:05',
                    'updated_at' => '2024-12-19 06:46:05',
                    'deleted_at' => NULL,
                ),
            8 =>
                array(
                    'id' => 9,
                    'question' => 'Can I integrate KiviCare with other tools?',
                    'answer' => 'Yes, KiviCare is designed to integrate with other tools like Google Calendar, email marketing services, and more. You can also extend functionality through WordPress plugins.',
                    'status' => 1,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'deleted_by' => NULL,
                    'created_at' => '2024-12-19 06:46:21',
                    'updated_at' => '2024-12-19 06:46:21',
                    'deleted_at' => NULL,
                ),
            9 =>
                array(
                    'id' => 10,
                    'question' => 'Is KiviCare mobile-friendly?',
                    'answer' => 'Yes, KiviCare is fully mobile-responsive. Your patients can access their profiles, schedule appointments, and manage their medical information via their smartphones or tablets.',
                    'status' => 1,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'deleted_by' => NULL,
                    'created_at' => '2024-12-19 06:46:40',
                    'updated_at' => '2024-12-19 06:46:40',
                    'deleted_at' => NULL,
                ),
            10 =>
                array(
                    'id' => 11,
                    'question' => 'Does KiviCare offer patient reminders?',
                    'answer' => 'Yes, KiviCare sends automatic reminders to patients about their upcoming appointments, which can be configured to send via email or SMS.',
                    'status' => 1,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'deleted_by' => NULL,
                    'created_at' => '2024-12-19 06:51:25',
                    'updated_at' => '2024-12-19 06:51:25',
                    'deleted_at' => NULL,
                ),
            11 =>
                array(
                    'id' => 12,
                    'question' => 'Can I add multiple doctors to KiviCare?',
                    'answer' => 'Yes, KiviCare allows you to add multiple doctors or healthcare professionals to the system. Each doctor can have their own schedule, appointments, and patient records.',
                    'status' => 1,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'deleted_by' => NULL,
                    'created_at' => '2024-12-19 06:51:47',
                    'updated_at' => '2024-12-19 06:51:47',
                    'deleted_at' => NULL,
                ),
            12 =>
                array(
                    'id' => 13,
                    'question' => 'Is there an option to track patient history in KiviCare?',
                    'answer' => 'Yes, KiviCare lets you track a patient\'s medical history, including past appointments, diagnoses, treatments, and prescriptions.',
                    'status' => 1,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'deleted_by' => NULL,
                    'created_at' => '2024-12-19 06:52:05',
                    'updated_at' => '2024-12-19 06:52:05',
                    'deleted_at' => NULL,
                ),
            13 =>
                array(
                    'id' => 14,
                    'question' => 'How do I set up patient billing?',
                    'answer' => 'You can set up patient billing by navigating to the billing section in your admin dashboard. You can create and send invoices, accept payments, and manage billing details.',
                    'status' => 1,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'deleted_by' => NULL,
                    'created_at' => '2024-12-19 06:52:21',
                    'updated_at' => '2024-12-19 06:52:21',
                    'deleted_at' => NULL,
                ),
            14 =>
                array(
                    'id' => 15,
                    'question' => 'Can I delete patient records?',
                    'answer' => 'Yes, you can delete patient records from the system, though we recommend keeping patient history for medical reference. Deletion is irreversible.',
                    'status' => 1,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'deleted_by' => NULL,
                    'created_at' => '2024-12-19 06:52:38',
                    'updated_at' => '2024-12-19 06:52:38',
                    'deleted_at' => NULL,
                ),
            15 =>
                array(
                    'id' => 16,
                    'question' => 'Can I provide access to staff members?',
                    'answer' => 'Yes, you can provide limited access to other staff members. Each staff member can be assigned specific roles and permissions, ensuring they can only access the parts of the system they need.',
                    'status' => 1,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'deleted_by' => NULL,
                    'created_at' => '2024-12-19 06:52:56',
                    'updated_at' => '2024-12-19 06:52:56',
                    'deleted_at' => NULL,
                ),
        ));
    }
    }
}
