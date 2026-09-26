<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EqualFlow — Complete Your Profile</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * { font-family: 'Inter', sans-serif; }

        .setup-gradient {
            background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 50%, #4a6a9a 100%);
            position: relative;
            overflow: hidden;
        }

        .setup-gradient::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 80%;
            height: 80%;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            pointer-events: none;
        }

        .setup-gradient::after {
            content: '';
            position: absolute;
            bottom: -40%;
            left: -20%;
            width: 60%;
            height: 60%;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 50%;
            pointer-events: none;
        }

        .setup-gradient .content {
            position: relative;
            z-index: 1;
            text-align: left;
            width: 100%;
            max-width: 400px;
        }

        .specialty-tag {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
            background: #f8fafc;
            cursor: pointer;
            user-select: none;
        }

        .specialty-tag:hover {
            transform: translateY(-2px);
            border-color: #1a2a4a;
            background: #f1f5f9;
        }

        .specialty-tag:has(input:checked) {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-color: #1a2a4a;
            box-shadow: 0 2px 10px rgba(26, 42, 74, 0.1);
        }

        .specialty-tag input:checked + span {
            color: #1a2a4a;
            font-weight: 600;
        }

        .specialty-tag input {
            display: none;
        }

        .specialty-tag .tag-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0;
            font-size: 0.8rem;
            color: #4b5563;
            transition: all 0.3s ease;
        }

        .specialty-tag:has(input:checked) .tag-label {
            color: #1a2a4a;
        }

        .specialty-tag .tag-icon {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 2px solid #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.3s ease;
            font-size: 0.55rem;
            color: transparent;
        }

        .specialty-tag:has(input:checked) .tag-icon {
            background: #1a2a4a;
            border-color: #1a2a4a;
            color: white;
        }

        .form-select {
            transition: all 0.3s ease;
            border-color: #e5e7eb;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 12px;
        }

        .form-select:focus {
            border-color: #1a2a4a;
            box-shadow: 0 0 0 3px rgba(26, 42, 74, 0.1);
            outline: none;
        }

        .form-select:hover {
            border-color: #9ca3af;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0f1a30 0%, #1a2a4a 100%);
            box-shadow: 0 4px 15px rgba(26, 42, 74, 0.3);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
            transform: translateY(-1px);
        }

        .content-left {
            text-align: left;
        }

        .content-left .flex-wrap {
            justify-content: flex-start;
        }

        @media (max-width: 1024px) {
            .setup-gradient {
                min-height: 250px;
            }
            .setup-gradient .content {
                text-align: center;
                max-width: 100%;
            }
            .setup-gradient .content .flex-wrap {
                justify-content: center;
            }
            .setup-gradient .content .flex.items-center.gap-2 {
                justify-content: center;
            }
        }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">

<div class="flex flex-col lg:flex-row min-h-screen">

    <!-- LEFT SIDE -->
    <div class="lg:w-2/5 setup-gradient flex items-center justify-center p-8 lg:p-12">
        <div class="content content-left">
            <div class="flex items-center gap-3 mb-6">
                <svg class="w-10 h-10 text-indigo-300 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
                <span class="text-3xl font-extrabold text-white tracking-tight">
                    Equal<span class="text-indigo-300">Flow</span>
                </span>
            </div>

            <h1 class="text-3xl font-bold text-white leading-tight">
                Welcome to your team!
            </h1>
            <p class="mt-3 text-indigo-200 text-sm leading-relaxed">
                Let's set up your profile so we can match you with the right tasks and projects.
            </p>

            <div class="mt-6 flex flex-wrap items-center gap-3 text-sm">
                <span class="flex items-center gap-2 px-3 py-1.5 bg-white/10 rounded-full border border-white/10">
                    <i class="fas fa-building text-indigo-300"></i>
                    <span class="text-white">{{ $user->currentOrganization()->name ?? 'Company' }}</span>
                </span>
            </div>

            <div class="mt-6 p-4 bg-white/10 backdrop-blur-sm rounded-xl border border-white/10">
                <p class="font-semibold text-white text-sm flex items-center gap-2">
                    <i class="fas fa-lightbulb text-indigo-300"></i> Why this matters:
                </p>
                <p class="mt-1 text-indigo-200 text-xs leading-relaxed">
                    Your job scope and specialties help the AI match you with the right tasks and projects.
                </p>
            </div>

        </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="flex-1 lg:w-3/5 bg-white flex items-center justify-center p-6 lg:p-12">
        <div class="w-full max-w-2xl">

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white text-sm">
                        <i class="fas fa-user-edit"></i>
                    </span>
                    Complete Your Profile
                </h2>
                <p class="text-sm text-gray-500 mt-1">Tell us about your role and expertise to get the best experience.</p>
            </div>

            <form method="POST" action="{{ route('profile.setup.store') }}" id="profileSetupForm">
                @csrf

                {{-- JOB SCOPE --}}
                <div class="mb-6">
                    <label for="job_scope" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-briefcase text-blue-500"></i> Job Scope <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="job_scope" name="job_scope" required
                                class="form-select w-full px-4 py-3 pr-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent transition bg-white">
                            <option value="">Select your job scope...</option>
                            <option value="civil_engineer">Civil Engineer</option>
                            <option value="structural_engineer">Structural Engineer</option>
                            <option value="drafter">Drafter / Draftsperson</option>
                            <option value="iow_road">IOW - Road</option>
                            <option value="iow_drainage">IOW - Drainage</option>
                            <option value="iow_earthwork">IOW - Earthwork</option>
                            <option value="iow_wall">IOW - Wall / Retaining Structure</option>
                            <option value="iow_bridge">IOW - Bridge</option>
                            <option value="clerk">Clerk / Documentation Specialist</option>
                            <option value="project_manager">Project Manager</option>
                            <option value="site_supervisor">Site Supervisor</option>
                            <option value="quality_control">Quality Control</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                    @error('job_scope')
                        <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- SPECIALTIES --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-tags text-purple-500"></i> Specialties <span class="text-red-500">*</span>
                    </label>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2" id="specialtiesContainer">
                        @php
                            $specialtyOptions = [
                                'Road Design', 'Highway Engineering', 'Drainage System', 'Earthworks',
                                'Slope Design', 'Site Grading', 'Water Reticulation', 'Sewerage System',
                                'Structural Design', 'Steel Structure', 'Reinforced Concrete',
                                'Foundation Design', 'Piling', 'Pre-stressed Concrete',
                                'AutoCAD', 'Civil 3D', 'Revit', 'MicroStation',
                                'ETABS', 'STAAD Pro', 'SAP2000', 'BIM Modeling',
                                'Project Management', 'Site Supervision', 'Quality Control',
                                'Documentation', 'Submissions', 'Contract Administration',
                                'Cost Estimation', 'Scheduling'
                            ];
                        @endphp
                        @foreach($specialtyOptions as $specialty)
                            <label class="specialty-tag px-3 py-2 rounded-lg border border-gray-200">
                                <input type="checkbox" name="specialties[]" value="{{ $specialty }}"
                                       class="specialty-checkbox" onchange="updateSpecialtyCount()">
                                <span class="tag-label">
                                    <span class="tag-icon">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    {{ $specialty }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-3 flex items-center justify-between">
                        <p class="text-xs text-gray-400 flex items-center gap-1">
                            <i class="fas fa-check-circle text-emerald-500"></i>
                            Selected: <span id="specialtyCount">0</span> / 5
                        </p>
                        <p id="specialtyError" class="text-xs text-red-500 hidden flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i> Please select at least 1 specialty.
                        </p>
                    </div>

                    @error('specialties')
                        <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- FORM ACTIONS --}}
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="btn-primary flex-1 inline-flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i> Complete Profile
                    </button>
                    <button type="button"
                            onclick="skipProfile()"
                            class="btn-secondary inline-flex items-center justify-center gap-2">
                        <i class="fas fa-forward"></i> Skip for Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.specialty-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const checked = document.querySelectorAll('.specialty-checkbox:checked');
            if (checked.length > 5) {
                this.checked = false;
                showToast('You can select a maximum of 5 specialties.', 'warning');
            }
            updateSpecialtyCount();
        });
    });

    function updateSpecialtyCount() {
        const checked = document.querySelectorAll('.specialty-checkbox:checked');
        document.getElementById('specialtyCount').textContent = checked.length;

        const errorEl = document.getElementById('specialtyError');
        if (checked.length === 0) {
            errorEl.classList.remove('hidden');
        } else {
            errorEl.classList.add('hidden');
        }
    }

    document.getElementById('profileSetupForm').addEventListener('submit', function(e) {
        const checked = document.querySelectorAll('.specialty-checkbox:checked');
        if (checked.length === 0) {
            e.preventDefault();
            const errorEl = document.getElementById('specialtyError');
            errorEl.classList.remove('hidden');
            errorEl.textContent = 'Please select at least 1 specialty.';
            errorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    function skipProfile() {
        const form = document.getElementById('profileSetupForm');
        form.action = '{{ route('profile.setup.skip') }}';
        form.submit();
    }

    function showToast(message, type = 'info') {
        const colors = {
            success: 'bg-emerald-500',
            error: 'bg-red-500',
            warning: 'bg-amber-500',
            info: 'bg-blue-500'
        };

        const toast = document.createElement('div');
        toast.className = `fixed bottom-6 right-6 ${colors[type] || 'bg-blue-500'} text-white px-4 py-3 rounded-xl shadow-lg text-sm z-50 max-w-sm transition-opacity duration-300 flex items-center gap-3`;
        toast.innerHTML = `
            <i class="fas ${type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'} text-white/80"></i>
            <span>${message}</span>
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }

    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'Enter') {
            document.getElementById('profileSetupForm').submit();
        }
        if (e.key === 'Escape') {
            skipProfile();
        }
    });

    updateSpecialtyCount();
</script>

</body>
</html>