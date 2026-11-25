<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registered_at_facility_id')->constrained('facilities');
            $table->string('patient_number', 40)->unique();
            $table->string('demo_national_id', 60)->nullable()->unique();
            $table->string('name', 150);
            $table->date('date_of_birth');
            $table->string('sex', 30);
            $table->string('phone', 30);
            $table->string('email', 191)->nullable();
            $table->string('address', 255);
            $table->text('allergies')->nullable();
            $table->text('conditions')->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->timestamps();
        });

        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('relationship', 80);
            $table->string('phone', 30);
            $table->string('address', 255)->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('patient_id')->nullable()->after('role_id')->constrained('patients')->nullOnDelete();
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('facility_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->restrictOnDelete();
            $table->dateTime('scheduled_at')->index();
            $table->string('type', 60);
            $table->string('reason', 255);
            $table->string('status', 30)->default('booked')->index();
            $table->timestamps();

            $table->index(['facility_id', 'scheduled_at']);
            $table->index(['doctor_id', 'scheduled_at']);
        });

        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('facility_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->restrictOnDelete();
            $table->text('symptoms')->nullable();
            $table->text('examination')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('notes')->nullable();
            $table->text('follow_up')->nullable();
            $table->string('status', 30)->default('draft')->index();
            $table->timestamps();
        });

        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained()->restrictOnDelete();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('facility_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->restrictOnDelete();
            $table->string('status', 40)->default('issued')->index();
            $table->dateTime('issued_at')->nullable();
            $table->timestamps();
        });

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->string('medicine_name', 150);
            $table->string('dose', 100);
            $table->string('route', 60);
            $table->string('frequency', 100);
            $table->string('duration', 100);
            $table->unsignedInteger('quantity');
            $table->string('instructions', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('name', 150);
            $table->string('specimen_type', 80);
            $table->string('unit', 50)->nullable();
            $table->string('reference_range', 100)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('lab_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained()->restrictOnDelete();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('facility_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->restrictOnDelete();
            $table->string('priority', 30)->default('routine');
            $table->string('sample_type', 80)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('requested')->index();
            $table->dateTime('requested_at')->nullable();
            $table->timestamps();
        });

        Schema::create('lab_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_test_id')->constrained()->restrictOnDelete();
            $table->string('status', 30)->default('requested')->index();
            $table->timestamps();

            $table->unique(['lab_order_id', 'lab_test_id']);
        });

        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_order_item_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('entered_by')->constrained('users')->restrictOnDelete();
            $table->text('value');
            $table->string('unit', 50)->nullable();
            $table->string('reference_range', 100)->nullable();
            $table->text('comment')->nullable();
            $table->string('status', 30)->default('draft')->index();
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_results');
        Schema::dropIfExists('lab_order_items');
        Schema::dropIfExists('lab_orders');
        Schema::dropIfExists('lab_tests');
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('consultations');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('emergency_contacts');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropColumn('patient_id');
        });

        Schema::dropIfExists('patients');
    }
};
