<?php

/*
 * Only overrides what the app needs. Laravel merges this file over the framework's
 * default validation messages (array_replace_recursive), so every message not listed
 * here keeps its default text.
 *
 * 'attributes' turns raw field keys into readable names in validation messages:
 * "The full name en field is required." becomes "The Full name (English) field is required."
 */
return [
    'attributes' => [
        // Student / admission form: academic
        'academic_session_id' => 'Academic session',
        'school_class_id' => 'Class',
        'section_id' => 'Section',
        'group_id' => 'Group',
        'roll' => 'Roll',
        'student_cid' => 'Student ID',
        'application_number' => 'Application number',

        // Basic information
        'full_name_en' => 'Full name (English)',
        'full_name_bn' => 'Full name (Bangla)',
        'date_of_birth' => 'Date of birth',
        'gender' => 'Gender',
        'religion' => 'Religion',
        'blood_group' => 'Blood group',
        'birth_certificate_number' => 'Birth certificate number',
        'phone' => 'Phone',
        'image' => 'Photo',
        'disable' => 'Disability',

        // Address
        'present_address' => 'Present address',
        'present_division_id' => 'Present division',
        'present_district_id' => 'Present district',
        'present_police_station_id' => 'Present police station',
        'present_post_office_id' => 'Present post office',
        'permanent_address' => 'Permanent address',
        'permanent_division_id' => 'Permanent division',
        'permanent_district_id' => 'Permanent district',
        'permanent_police_station_id' => 'Permanent police station',
        'permanent_post_office_id' => 'Permanent post office',

        // Parents
        'father_name' => "Father's name",
        'father_phone' => "Father's phone",
        'father_email' => "Father's email",
        'father_nid_number' => "Father's NID number",
        'fathers_profession_id' => "Father's profession",
        'mother_name' => "Mother's name",
        'mother_phone' => "Mother's phone",
        'mother_email' => "Mother's email",
        'mother_nid_number' => "Mother's NID number",
        'mothers_profession_id' => "Mother's profession",
        'annual_income' => 'Annual income',

        // Guardian
        'guardian_type' => 'Guardian',
        'guardian_relation' => 'Relation to student',
        'guardian_name' => "Guardian's name",
        'guardian_phone' => "Guardian's phone",
        'guardian_email' => "Guardian's email",
        'guardian_address' => "Guardian's address",
        'guardian_profession_id' => "Guardian's profession",

        // Previous academic history
        'previous_school' => 'Previous school',
        'previous_class_appeared' => 'Previous class',
        'tc_number' => 'TC number',

        // Common fields on other forms
        'name_en' => 'Name (English)',
        'name_bn' => 'Name (Bangla)',
        'employee_type' => 'Employee type',
        'designation_id' => 'Designation',
        'department_id' => 'Department',
        'reporting_manager_id' => 'Reporting manager',
        'joining_date' => 'Joining date',
        'payment_method' => 'Payment method',
        'bank_name' => 'Bank name',
        'account_number' => 'Account number',
        'mobile_wallet_provider' => 'Mobile wallet provider',
        'mobile_wallet_number' => 'Mobile wallet number',
        'discount_amount' => 'Discount',
        'payment_reference' => 'Payment reference',
        'exam_id' => 'Exam',
        'subject_id' => 'Subject',
        'session_id' => 'Academic session',
        'class_id' => 'Class',
        'document_type' => 'Document type',
    ],
];
