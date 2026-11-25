<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NHIMS | National Health Information Management System</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="nhims-public-body">
    <header class="nhims-public-nav">
        <div class="container d-flex align-items-center justify-content-between gap-3 py-3">
            <a href="{{ url('/') }}" class="nhims-public-brand">
                <span class="nhims-brand-mark" aria-hidden="true"><i class="bi bi-heart-pulse"></i></span>
                <span>
                    <strong>NHIMS</strong>
                    <small>Health information</small>
                </span>
            </a>
            <nav class="d-flex align-items-center gap-2" aria-label="Public navigation">
                <a href="#capabilities" class="btn btn-link text-dark d-none d-md-inline-flex">Capabilities</a>
                <a href="#workflow" class="btn btn-link text-dark d-none d-md-inline-flex">How it works</a>
                @auth
                    <a href="{{ url('/home') }}" class="btn btn-primary"><i class="bi bi-grid-1x2 me-2"></i>Open workspace</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">Sign in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary">Patient registration</a>
                    @endif
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <section class="nhims-hero">
            <div class="container position-relative py-5 py-lg-6">
                <div class="row align-items-center g-5">
                    <div class="col-lg-7">
                        <span class="nhims-eyebrow"><i class="bi bi-shield-check me-2"></i>Connected care for Bangladesh</span>
                        <h1 class="nhims-hero-title">One trusted view of every patient journey.</h1>
                        <p class="nhims-hero-lead">NHIMS brings registration, appointments, consultation, prescriptions, laboratory results, pharmacy dispensing, and patient self-service into one secure workspace.</p>
                        <div class="d-flex flex-wrap gap-3 mt-4">
                            @auth
                                <a href="{{ url('/home') }}" class="btn btn-primary btn-lg px-4"><i class="bi bi-arrow-right-circle me-2"></i>Open workspace</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4"><i class="bi bi-box-arrow-in-right me-2"></i>Sign in to NHIMS</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-outline-dark btn-lg px-4">Register as a patient</a>
                                @endif
                            @endauth
                        </div>
                        <p class="small text-secondary mt-4 mb-0"><i class="bi bi-info-circle me-2"></i>Public registration is for patients. Staff accounts are created and assigned by an administrator.</p>
                    </div>
                    <div class="col-lg-5">
                        <div class="nhims-hero-visual" aria-label="NHIMS care coordination preview">
                            <div class="nhims-hero-visual-glow"></div>
                            <div class="nhims-hero-panel">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div>
                                        <span class="small text-secondary">Today's care overview</span>
                                        <h2 class="h5 mb-0 mt-1">Dhaka Central Community Hospital</h2>
                                    </div>
                                    <span class="nhims-live-dot"><span></span>Live</span>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6"><div class="nhims-preview-metric"><i class="bi bi-people"></i><strong>3,842</strong><span>Patients</span></div></div>
                                    <div class="col-6"><div class="nhims-preview-metric nhims-preview-metric-teal"><i class="bi bi-calendar2-check"></i><strong>128</strong><span>Appointments</span></div></div>
                                </div>
                                <div class="nhims-preview-list">
                                    <div class="d-flex align-items-center gap-3"><span class="nhims-preview-icon nhims-preview-icon-blue"><i class="bi bi-clipboard2-pulse"></i></span><span><strong>Consultation queue</strong><small>Doctor workflow ready</small></span><span class="ms-auto nhims-status nhims-status-info">12</span></div>
                                    <div class="d-flex align-items-center gap-3"><span class="nhims-preview-icon nhims-preview-icon-amber"><i class="bi bi-eyedropper"></i></span><span><strong>Laboratory results</strong><small>Published securely</small></span><span class="ms-auto nhims-status nhims-status-success">24</span></div>
                                    <div class="d-flex align-items-center gap-3"><span class="nhims-preview-icon nhims-preview-icon-green"><i class="bi bi-shield-check"></i></span><span><strong>Audit coverage</strong><small>Every sensitive action</small></span><span class="ms-auto text-success"><i class="bi bi-check-circle-fill"></i></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="nhims-public-section nhims-section-tight">
            <div class="container">
                <div class="row g-3">
                    <div class="col-6 col-lg-3"><div class="nhims-stat-card"><span class="nhims-stat-icon"><i class="bi bi-diagram-3"></i></span><strong>6</strong><span>Connected user roles</span></div></div>
                    <div class="col-6 col-lg-3"><div class="nhims-stat-card"><span class="nhims-stat-icon nhims-stat-icon-teal"><i class="bi bi-building"></i></span><strong>2</strong><span>Fictional facilities</span></div></div>
                    <div class="col-6 col-lg-3"><div class="nhims-stat-card"><span class="nhims-stat-icon nhims-stat-icon-amber"><i class="bi bi-arrow-repeat"></i></span><strong>1</strong><span>Connected care flow</span></div></div>
                    <div class="col-6 col-lg-3"><div class="nhims-stat-card"><span class="nhims-stat-icon nhims-stat-icon-blue"><i class="bi bi-shield-lock"></i></span><strong>100%</strong><span>Server-side access control</span></div></div>
                </div>
            </div>
        </section>

        <section id="capabilities" class="nhims-public-section">
            <div class="container">
                <div class="text-center nhims-public-heading mx-auto">
                    <span class="nhims-eyebrow">Designed around care delivery</span>
                    <h2>Everything teams need to move care forward.</h2>
                    <p>Each role gets a focused workspace while the patient record stays connected across the care journey.</p>
                </div>
                <div class="row g-4 mt-2">
                    <div class="col-md-6 col-lg-4"><article class="nhims-feature-card"><span class="nhims-feature-icon"><i class="bi bi-person-plus"></i></span><h3>Patient registration</h3><p>Capture identity, contact, emergency information, and a unique patient number with duplicate checks.</p></article></div>
                    <div class="col-md-6 col-lg-4"><article class="nhims-feature-card"><span class="nhims-feature-icon nhims-feature-icon-blue"><i class="bi bi-calendar2-week"></i></span><h3>Appointments</h3><p>Book, filter, reschedule, and track appointments by facility, doctor, date, and status.</p></article></div>
                    <div class="col-md-6 col-lg-4"><article class="nhims-feature-card"><span class="nhims-feature-icon nhims-feature-icon-amber"><i class="bi bi-clipboard2-pulse"></i></span><h3>Clinical workflow</h3><p>Doctors can review authorized history, document consultations, prescribe medicines, and request tests.</p></article></div>
                    <div class="col-md-6 col-lg-4"><article class="nhims-feature-card"><span class="nhims-feature-icon nhims-feature-icon-purple"><i class="bi bi-eyedropper"></i></span><h3>Laboratory services</h3><p>Manage test orders, enter results, publish findings, and keep unpublished data private.</p></article></div>
                    <div class="col-md-6 col-lg-4"><article class="nhims-feature-card"><span class="nhims-feature-icon nhims-feature-icon-green"><i class="bi bi-capsule"></i></span><h3>Pharmacy dispensing</h3><p>Verify prescription items, record quantities, and update dispensing status for the patient.</p></article></div>
                    <div class="col-md-6 col-lg-4"><article class="nhims-feature-card"><span class="nhims-feature-icon nhims-feature-icon-red"><i class="bi bi-shield-check"></i></span><h3>Reports and audit</h3><p>Administrators see aggregate service summaries and trace sensitive reads, writes, and denied access.</p></article></div>
                </div>
            </div>
        </section>

        <section id="workflow" class="nhims-public-section nhims-workflow-section">
            <div class="container">
                <div class="row align-items-end g-4 mb-5">
                    <div class="col-lg-7"><span class="nhims-eyebrow">One connected workflow</span><h2 class="nhims-public-title">From first registration to confident follow-up.</h2></div>
                    <div class="col-lg-5"><p class="mb-0 text-secondary">NHIMS keeps every handoff visible to the right role, at the right facility, with an audit trail behind it.</p></div>
                </div>
                <div class="row g-4">
                    <div class="col-md-4"><div class="nhims-step"><span class="nhims-step-number">01</span><h3>Register and book</h3><p>Receptionists create a patient profile and schedule the visit with the assigned doctor.</p></div></div>
                    <div class="col-md-4"><div class="nhims-step"><span class="nhims-step-number">02</span><h3>Care and coordinate</h3><p>Doctors document the consultation, issue prescriptions, and request laboratory tests.</p></div></div>
                    <div class="col-md-4"><div class="nhims-step"><span class="nhims-step-number">03</span><h3>Publish and follow up</h3><p>Lab and pharmacy teams complete their work while patients review published records online.</p></div></div>
                </div>
            </div>
        </section>

        <section class="nhims-public-section">
            <div class="container">
                <div class="nhims-access-panel">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-7"><span class="nhims-eyebrow nhims-eyebrow-light">Built for responsible access</span><h2>Clear roles. Scoped facilities. Traceable actions.</h2><p>Administrators manage access, care teams see the records they need, and patients see their own published information. Every sensitive action is designed to be accountable.</p></div>
                        <div class="col-lg-5"><div class="row g-2"><div class="col-6"><div class="nhims-access-item"><i class="bi bi-person-badge"></i><strong>Role-based</strong><span>Permissions</span></div></div><div class="col-6"><div class="nhims-access-item"><i class="bi bi-building-check"></i><strong>Facility-aware</strong><span>Scope</span></div></div><div class="col-6"><div class="nhims-access-item"><i class="bi bi-file-earmark-lock2"></i><strong>Patient-owned</strong><span>History</span></div></div><div class="col-6"><div class="nhims-access-item"><i class="bi bi-journal-check"></i><strong>Audit-ready</strong><span>Events</span></div></div></div></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="nhims-public-section nhims-section-tight pt-0">
            <div class="container">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 nhims-facility-strip">
                    <div><span class="nhims-eyebrow">Fictional demo network</span><h2 class="h4 mb-0">Two facilities, one connected record.</h2></div>
                    <div class="d-flex flex-wrap gap-2"><span class="nhims-facility-pill"><i class="bi bi-building me-2"></i>Dhaka Central Community Hospital</span><span class="nhims-facility-pill"><i class="bi bi-building me-2"></i>Chattogram Coastal Diagnostic Centre</span></div>
                </div>
            </div>
        </section>
    </main>

    <footer class="nhims-public-footer">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-2"><span class="nhims-brand-mark" aria-hidden="true"><i class="bi bi-heart-pulse"></i></span><span><strong>NHIMS</strong><small class="d-block">Synthetic demonstration environment</small></span></div>
            <small>Laravel 13 · Bootstrap 5 · MySQL</small>
        </div>
    </footer>
</body>
</html>
