<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Consultation;
use App\Models\Dispensing;
use App\Models\DispensingItem;
use App\Models\EmergencyContact;
use App\Models\Facility;
use App\Models\LabOrder;
use App\Models\LabOrderItem;
use App\Models\LabResult;
use App\Models\LabTest;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        DB::transaction(function (): void {
            $roles = collect([
                ['key' => 'admin', 'name' => 'Administrator'],
                ['key' => 'receptionist', 'name' => 'Receptionist'],
                ['key' => 'doctor', 'name' => 'Doctor'],
                ['key' => 'lab_staff', 'name' => 'Laboratory Staff'],
                ['key' => 'pharmacy_staff', 'name' => 'Pharmacy Staff'],
                ['key' => 'patient', 'name' => 'Patient'],
            ])->mapWithKeys(function (array $attributes): array {
                $role = Role::updateOrCreate(['key' => $attributes['key']], $attributes);

                return [$attributes['key'] => $role];
            });

            User::whereNull('role_id')->update([
                'role_id' => $roles['patient']->id,
                'status' => 'active',
            ]);

            $facilityA = Facility::updateOrCreate(
                ['code' => 'DHK-CCH'],
                [
                    'name' => 'Dhaka Central Community Hospital',
                    'type' => 'hospital',
                    'district' => 'Dhaka',
                    'address' => 'Agargaon, Dhaka',
                    'phone' => '02-55000001',
                    'status' => 'active',
                ],
            );

            $facilityB = Facility::updateOrCreate(
                ['code' => 'CTG-CDC'],
                [
                    'name' => 'Chattogram Coastal Diagnostic Centre',
                    'type' => 'diagnostic_centre',
                    'district' => 'Chattogram',
                    'address' => 'Panchlaish, Chattogram',
                    'phone' => '031-620000',
                    'status' => 'active',
                ],
            );

            $makeUser = function (string $name, string $email, string $roleKey, Facility $facility, ?Patient $patient = null) use ($roles): User {
                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'role_id' => $roles[$roleKey]->id,
                        'patient_id' => $patient?->id,
                        'name' => $name,
                        'password' => Hash::make('password'),
                        'status' => 'active',
                        'email_verified_at' => now(),
                    ],
                );

                $user->facilities()->syncWithoutDetaching([
                    $facility->id => [
                        'is_primary' => true,
                        'active_from' => today(),
                    ],
                ]);

                return $user;
            };

            $patient1 = Patient::updateOrCreate(
                ['patient_number' => 'NHIMS-000001'],
                [
                    'registered_at_facility_id' => $facilityA->id,
                    'demo_national_id' => 'NID-DEMO-000001',
                    'name' => 'Ayesha Rahman',
                    'date_of_birth' => '1992-04-18',
                    'sex' => 'female',
                    'phone' => '01711000001',
                    'email' => 'ayesha.patient@nhims.test',
                    'address' => 'Mirpur, Dhaka',
                    'allergies' => 'Penicillin',
                    'conditions' => 'Seasonal asthma',
                    'status' => 'active',
                ],
            );

            $patient2 = Patient::updateOrCreate(
                ['patient_number' => 'NHIMS-000002'],
                [
                    'registered_at_facility_id' => $facilityA->id,
                    'demo_national_id' => 'NID-DEMO-000002',
                    'name' => 'Tanvir Hossain',
                    'date_of_birth' => '1988-09-06',
                    'sex' => 'male',
                    'phone' => '01711000002',
                    'email' => 'tanvir.patient@nhims.test',
                    'address' => 'Uttara, Dhaka',
                    'allergies' => null,
                    'conditions' => 'Hypertension',
                    'status' => 'active',
                ],
            );

            $patient3 = Patient::updateOrCreate(
                ['patient_number' => 'NHIMS-000003'],
                [
                    'registered_at_facility_id' => $facilityB->id,
                    'demo_national_id' => 'NID-DEMO-000003',
                    'name' => 'Nusrat Jahan',
                    'date_of_birth' => '1998-01-22',
                    'sex' => 'female',
                    'phone' => '01811000003',
                    'email' => 'nusrat.patient@nhims.test',
                    'address' => 'Panchlaish, Chattogram',
                    'allergies' => null,
                    'conditions' => null,
                    'status' => 'active',
                ],
            );

            EmergencyContact::updateOrCreate(
                ['patient_id' => $patient1->id, 'name' => 'Farida Rahman'],
                ['relationship' => 'Mother', 'phone' => '01711000009', 'address' => 'Mirpur, Dhaka'],
            );

            $admin = $makeUser('NHIMS Administrator', 'admin@nhims.test', 'admin', $facilityA);
            $receptionist = $makeUser('Rumana Akter', 'receptionist@nhims.test', 'receptionist', $facilityA);
            $doctorA = $makeUser('Dr. Mahmud Karim', 'doctor.dhaka@nhims.test', 'doctor', $facilityA);
            $doctorB = $makeUser('Dr. Sharmeen Islam', 'doctor.chattogram@nhims.test', 'doctor', $facilityB);
            $labStaff = $makeUser('Jahidul Lab', 'lab@nhims.test', 'lab_staff', $facilityB);
            $pharmacyStaff = $makeUser('Mita Pharmacy', 'pharmacy@nhims.test', 'pharmacy_staff', $facilityA);
            $patientUser = $makeUser('Ayesha Rahman', 'patient@nhims.test', 'patient', $facilityA, $patient1);

            $appointmentCompleted = Appointment::firstOrCreate(
                ['patient_id' => $patient1->id, 'doctor_id' => $doctorA->id, 'scheduled_at' => now()->subDay()->setTime(10, 0)],
                [
                    'facility_id' => $facilityA->id,
                    'type' => 'General consultation',
                    'reason' => 'Breathing difficulty and follow-up',
                    'status' => 'completed',
                ],
            );
            $appointmentCompleted->update(['status' => 'completed']);

            $appointmentUpcoming = Appointment::firstOrCreate(
                ['patient_id' => $patient1->id, 'doctor_id' => $doctorA->id, 'scheduled_at' => now()->addDay()->setTime(11, 30)],
                [
                    'facility_id' => $facilityA->id,
                    'type' => 'Follow-up',
                    'reason' => 'Review treatment progress',
                    'status' => 'confirmed',
                ],
            );

            Appointment::firstOrCreate(
                ['patient_id' => $patient1->id, 'doctor_id' => $doctorB->id, 'scheduled_at' => now()->addDays(3)->setTime(14, 0)],
                [
                    'facility_id' => $facilityB->id,
                    'type' => 'Diagnostic referral',
                    'reason' => 'Cross-facility diagnostic review',
                    'status' => 'booked',
                ],
            );

            $consultation = Consultation::updateOrCreate(
                ['appointment_id' => $appointmentCompleted->id],
                [
                    'patient_id' => $patient1->id,
                    'facility_id' => $facilityA->id,
                    'doctor_id' => $doctorA->id,
                    'symptoms' => 'Shortness of breath during exercise',
                    'examination' => 'Mild wheeze observed',
                    'diagnosis' => 'Seasonal asthma flare-up',
                    'notes' => 'Continue prescribed inhaler and monitor symptoms.',
                    'follow_up' => 'Review after seven days.',
                    'status' => 'completed',
                ],
            );

            $prescription = Prescription::updateOrCreate(
                ['consultation_id' => $consultation->id],
                [
                    'patient_id' => $patient1->id,
                    'facility_id' => $facilityA->id,
                    'doctor_id' => $doctorA->id,
                    'status' => 'partially_dispensed',
                    'issued_at' => now()->subDay(),
                ],
            );

            $prescriptionItem = PrescriptionItem::updateOrCreate(
                ['prescription_id' => $prescription->id, 'medicine_name' => 'Salbutamol inhaler'],
                [
                    'dose' => '100 mcg',
                    'route' => 'Inhaled',
                    'frequency' => 'As needed',
                    'duration' => '7 days',
                    'quantity' => 1,
                    'instructions' => 'Use during breathing difficulty.',
                ],
            );

            $cbc = LabTest::updateOrCreate(
                ['code' => 'CBC'],
                [
                    'name' => 'Complete Blood Count',
                    'specimen_type' => 'Blood',
                    'unit' => 'cells/uL',
                    'reference_range' => '4,000-11,000',
                    'active' => true,
                ],
            );

            $labOrder = LabOrder::updateOrCreate(
                ['consultation_id' => $consultation->id],
                [
                    'patient_id' => $patient1->id,
                    'facility_id' => $facilityB->id,
                    'doctor_id' => $doctorA->id,
                    'priority' => 'routine',
                    'sample_type' => 'Blood',
                    'notes' => 'Process at the referred diagnostic centre.',
                    'status' => 'completed',
                    'requested_at' => now()->subDay(),
                ],
            );

            $labOrderItem = LabOrderItem::updateOrCreate(
                ['lab_order_id' => $labOrder->id, 'lab_test_id' => $cbc->id],
                ['status' => 'completed'],
            );

            LabResult::updateOrCreate(
                ['lab_order_item_id' => $labOrderItem->id],
                [
                    'patient_id' => $patient1->id,
                    'entered_by' => $labStaff->id,
                    'value' => '7,200',
                    'unit' => 'cells/uL',
                    'reference_range' => '4,000-11,000',
                    'comment' => 'Within expected range for this demonstration record.',
                    'status' => 'published',
                    'published_at' => now()->subHours(8),
                ],
            );

            $dispensing = Dispensing::updateOrCreate(
                ['prescription_id' => $prescription->id],
                [
                    'patient_id' => $patient1->id,
                    'facility_id' => $facilityA->id,
                    'pharmacy_user_id' => $pharmacyStaff->id,
                    'status' => 'partial',
                    'dispensed_at' => now()->subHours(4),
                    'notes' => 'One item dispensed for demonstration.',
                ],
            );

            DispensingItem::updateOrCreate(
                ['dispensing_id' => $dispensing->id, 'prescription_item_id' => $prescriptionItem->id],
                ['quantity' => 1],
            );

            AuditLog::firstOrCreate(
                ['actor_id' => $doctorA->id, 'action' => 'patient.viewed', 'auditable_type' => Patient::class, 'auditable_id' => $patient1->id],
                ['facility_id' => $facilityA->id, 'outcome' => 'success', 'metadata' => ['reason' => 'consultation'], 'ip_address' => '127.0.0.1'],
            );

            AuditLog::firstOrCreate(
                ['actor_id' => $doctorB->id, 'action' => 'patient.viewed', 'auditable_type' => Patient::class, 'auditable_id' => $patient1->id],
                ['facility_id' => $facilityB->id, 'outcome' => 'success', 'metadata' => ['reason' => 'referral'], 'ip_address' => '127.0.0.1'],
            );

            AuditLog::firstOrCreate(
                ['actor_id' => $patientUser->id, 'action' => 'patient.viewed', 'auditable_type' => Patient::class, 'auditable_id' => $patient2->id],
                ['facility_id' => $facilityA->id, 'outcome' => 'denied', 'metadata' => ['reason' => 'record belongs to another patient'], 'ip_address' => '127.0.0.1'],
            );

            unset($admin, $receptionist, $patient2, $patient3, $appointmentUpcoming);
        });
    }
}
