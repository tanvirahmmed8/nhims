<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Consultation;
use App\Models\Dispensing;
use App\Models\LabOrder;
use App\Models\LabOrderItem;
use App\Models\LabResult;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NhimsAccessAndValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_patient_portal_is_private_and_admin_routes_are_denied(): void
    {
        $patient = User::where('email', 'patient@nhims.test')->firstOrFail();

        $this->actingAs($patient)
            ->get('/portal')
            ->assertOk();

        $this->actingAs($patient)
            ->get('/admin/reports')
            ->assertForbidden();

        $this->actingAs($patient)
            ->get('/patients/2')
            ->assertForbidden();

        $this->assertTrue(AuditLog::where('actor_id', $patient->id)
            ->where('action', 'patient.view.denied')
            ->where('auditable_id', 2)
            ->where('outcome', 'denied')
            ->exists());
    }

    public function test_facility_scope_and_authorized_cross_facility_history_are_enforced(): void
    {
        $doctorAtChattogram = User::where('email', 'doctor.chattogram@nhims.test')->firstOrFail();
        $receptionistAtDhaka = User::where('email', 'receptionist@nhims.test')->firstOrFail();

        $this->actingAs($doctorAtChattogram)
            ->get('/patients/1')
            ->assertOk();

        $this->actingAs($receptionistAtDhaka)
            ->get('/patients/3')
            ->assertForbidden();
    }

    public function test_duplicate_patient_submission_returns_validation_error_without_partial_record(): void
    {
        $receptionist = User::where('email', 'receptionist@nhims.test')->firstOrFail();
        $facility = Facility::where('code', 'DHK-CCH')->firstOrFail();
        $before = Patient::count();

        $this->actingAs($receptionist)
            ->post('/patients', [
                'registered_at_facility_id' => $facility->id,
                'demo_national_id' => 'NID-DEMO-000001',
                'name' => 'Duplicate Demo Patient',
                'date_of_birth' => '1990-01-01',
                'sex' => 'other',
                'phone' => '01700000000',
                'address' => 'Synthetic address',
            ])
            ->assertSessionHasErrors('demo_national_id');

        $this->assertSame($before, Patient::count());
    }

    public function test_inactive_account_cannot_use_authenticated_routes(): void
    {
        $receptionist = User::where('email', 'receptionist@nhims.test')->firstOrFail();
        $receptionist->update(['status' => 'inactive']);

        $this->actingAs($receptionist)
            ->get('/home')
            ->assertForbidden();
    }

    public function test_main_clinical_workflow_connects_registration_to_portal(): void
    {
        $facility = Facility::where('code', 'DHK-CCH')->firstOrFail();
        $receptionist = User::where('email', 'receptionist@nhims.test')->firstOrFail();
        $doctor = User::where('email', 'doctor.dhaka@nhims.test')->firstOrFail();
        $labStaff = User::where('email', 'lab@nhims.test')->firstOrFail();
        $pharmacyStaff = User::where('email', 'pharmacy@nhims.test')->firstOrFail();

        $this->actingAs($receptionist)
            ->post('/patients', [
                'registered_at_facility_id' => $facility->id,
                'demo_national_id' => 'NID-QA-000001',
                'name' => 'QA Synthetic Patient',
                'date_of_birth' => '1990-01-01',
                'sex' => 'other',
                'phone' => '01700000011',
                'email' => 'qa.patient@nhims.test',
                'address' => 'Synthetic address',
            ])
            ->assertRedirect();

        $patient = Patient::where('demo_national_id', 'NID-QA-000001')->firstOrFail();

        $this->actingAs($receptionist)
            ->post('/appointments', [
                'patient_id' => $patient->id,
                'facility_id' => $facility->id,
                'doctor_id' => $doctor->id,
                'scheduled_at' => now()->addDays(5)->setTime(9, 0)->format('Y-m-d H:i:s'),
                'type' => 'General consultation',
                'reason' => 'QA workflow check',
                'status' => 'booked',
            ])
            ->assertRedirect();

        $appointment = Appointment::where('patient_id', $patient->id)->latest('id')->firstOrFail();

        $this->actingAs($doctor)
            ->post("/appointments/{$appointment->id}/consultation", [
                'symptoms' => 'Synthetic symptom',
                'examination' => 'Synthetic examination',
                'diagnosis' => 'Synthetic diagnosis',
                'notes' => 'QA consultation note',
                'follow_up' => 'Follow up in seven days',
                'status' => 'completed',
            ])
            ->assertRedirect();

        $consultation = Consultation::where('appointment_id', $appointment->id)->firstOrFail();
        $labTestId = (int) \App\Models\LabTest::query()->value('id');

        $this->actingAs($doctor)
            ->post("/consultations/{$consultation->id}/prescriptions", [
                'items' => [[
                    'medicine_name' => 'QA medicine',
                    'dose' => '10 mg',
                    'route' => 'Oral',
                    'frequency' => 'Once daily',
                    'duration' => '5 days',
                    'quantity' => 5,
                    'instructions' => 'Take after food',
                ]],
            ])
            ->assertRedirect();

        $prescription = Prescription::where('consultation_id', $consultation->id)->firstOrFail();
        $prescriptionItem = $prescription->items()->firstOrFail();

        $this->actingAs($doctor)
            ->post("/consultations/{$consultation->id}/laboratory", [
                'test_ids' => [$labTestId],
                'priority' => 'routine',
                'sample_type' => 'Blood',
                'notes' => 'QA laboratory order',
            ])
            ->assertRedirect();

        $labOrder = LabOrder::where('consultation_id', $consultation->id)->firstOrFail();
        $labItem = LabOrderItem::where('lab_order_id', $labOrder->id)->firstOrFail();
        $labStaff->facilities()->syncWithoutDetaching([$facility->id => ['is_primary' => false]]);

        $this->actingAs($labStaff)
            ->post("/laboratory/items/{$labItem->id}/result", [
                'value' => 'Normal',
                'unit' => 'n/a',
                'reference_range' => 'Normal',
                'comment' => 'QA published result',
            ])
            ->assertRedirect();

        $this->actingAs($pharmacyStaff)
            ->post("/pharmacy/prescriptions/{$prescription->id}/dispense", [
                'items' => [[
                    'prescription_item_id' => $prescriptionItem->id,
                    'quantity' => 5,
                ]],
                'notes' => 'QA dispensing',
            ])
            ->assertRedirect();

        $patientUser = User::where('email', 'patient@nhims.test')->firstOrFail();
        $patientUser->update(['patient_id' => $patient->id]);

        $this->actingAs($patientUser)
            ->get('/portal')
            ->assertOk()
            ->assertSee('QA Synthetic Patient')
            ->assertSee('QA medicine')
            ->assertSee('Normal');

        $this->assertDatabaseHas('consultations', ['id' => $consultation->id, 'status' => 'completed']);
        $this->assertDatabaseHas('lab_results', ['lab_order_item_id' => $labItem->id, 'status' => 'published']);
        $this->assertDatabaseHas('dispensings', ['prescription_id' => $prescription->id, 'status' => 'complete']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'prescription.dispensed', 'auditable_id' => $prescription->id]);
    }
}
