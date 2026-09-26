@extends('layouts.app')

@section('title', 'Company Settings')

@section('content')

<style>
/* ============================================ */
/* BASE FORM CONTROLS                            */
/* ============================================ */
.form-input, .form-textarea {
    padding: 0.7rem 1rem;
    border-radius: 0.65rem;
    border: 1px solid #e5e7eb;
    background: #fafbfc;
    transition: all 0.18s ease;
    width: 100%;
    font-size: 0.875rem;
    color: #1f2937;
}
.form-input:hover, .form-textarea:hover { border-color: #cbd5e1; }
.form-input:focus, .form-textarea:focus {
    background: #fff;
    border-color: #1a2a4a;
    box-shadow: 0 0 0 4px rgba(26, 42, 74, 0.08);
    outline: none;
}
.form-input::placeholder, .form-textarea::placeholder { color: #9ca3af; }

.form-label {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    color: #64748b;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    margin-bottom: 0.45rem;
}

/* ============================================ */
/* PAGE HEADER                                   */
/* ============================================ */
.page-header {
    background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
    border-radius: 1rem;
    padding: 1.25rem 1.5rem;
    border: 1px solid #e5e7eb;
    margin-bottom: 1.5rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

/* ============================================ */
/* SECTION CARDS                                 */
/* ============================================ */
.form-section {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 1rem;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1rem;
    transition: all 0.2s ease;
}
.form-section:hover {
    border-color: #d1d5db;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
}
.section-title {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.9rem;
}
.section-title .icon {
    width: 24px;
    height: 24px;
    border-radius: 7px;
    background: linear-gradient(135deg, #1a2a4a, #2d4a7a);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 0.68rem;
    flex-shrink: 0;
}
.section-title .section-hint {
    margin-left: auto;
    font-size: 0.68rem;
    font-weight: 400;
    color: #94a3b8;
    letter-spacing: 0;
    text-transform: none;
}

/* ============================================ */
/* FILE INPUT                                    */
/* ============================================ */
.file-input-wrapper {
    position: relative;
}
.file-input-wrapper input[type="file"] {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    top: 0;
    left: 0;
}
.file-input-wrapper .file-label {
    background: #fafbfc;
    border: 2px dashed #d1d5db;
    border-radius: 0.65rem;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    transition: all 0.18s ease;
    color: #64748b;
    font-size: 0.875rem;
}
.file-input-wrapper:hover .file-label {
    border-color: #1a2a4a;
    background: #f1f5f9;
}

/* ============================================ */
/* INVITE CODE DISPLAY                           */
/* ============================================ */
.invite-code-display {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-radius: 0.75rem;
    padding: 1.15rem;
    text-align: center;
    border: 1px solid #bfdbfe;
}
.invite-code-display .code {
    font-size: 1.6rem;
    font-weight: 700;
    color: #1a2a4a;
    font-family: 'Courier New', monospace;
    letter-spacing: 0.1em;
    margin: 0.35rem 0;
}

/* ============================================ */
/* STAT ITEMS                                    */
/* ============================================ */
.stat-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.65rem 0;
    border-bottom: 1px solid #f3f4f6;
}
.stat-item:last-child { border-bottom: none; }
.stat-item .stat-label {
    font-size: 0.8rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.stat-item .stat-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1f2937;
}
.stat-item .stat-value.blue { color: #2563eb; }
.stat-item .stat-value.green { color: #059669; }
.stat-item .stat-value.indigo { color: #4f46e5; }
.stat-item .stat-value.purple { color: #7c3aed; }

/* ============================================ */
/* DANGER ZONE                                   */
/* ============================================ */
.danger-zone {
    border: 1px solid #fecaca;
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border-radius: 1rem;
    padding: 1.25rem 1.5rem;
    margin-top: 1rem;
}

/* ============================================ */
/* BUTTONS                                       */
/* ============================================ */
.btn-primary {
    background: linear-gradient(135deg, #1a2a4a, #2d4a7a);
    color: #fff;
    padding: 0.75rem 1.5rem;
    border-radius: 0.7rem;
    font-weight: 600;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: all 0.18s ease;
    box-shadow: 0 2px 8px rgba(26, 42, 74, 0.15);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
}
.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(26, 42, 74, 0.28);
}

.btn-secondary {
    background: #f3f4f6;
    color: #475569;
    padding: 0.65rem 1.25rem;
    border-radius: 0.7rem;
    font-weight: 500;
    font-size: 0.8rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    transition: all 0.18s ease;
    border: none;
    cursor: pointer;
}
.btn-secondary:hover { background: #e5e7eb; transform: translateY(-1px); }

.btn-danger {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: #fff;
    padding: 0.65rem 1.25rem;
    border-radius: 0.7rem;
    font-weight: 600;
    font-size: 0.8rem;
    border: none;
    cursor: pointer;
    transition: all 0.18s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
}
.btn-danger:hover {
    background: linear-gradient(135deg, #b91c1c, #991b1b);
    box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
    transform: translateY(-1px);
}

.btn-warning {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    padding: 0.6rem 1.25rem;
    border-radius: 0.7rem;
    font-weight: 600;
    font-size: 0.8rem;
    border: 1px solid #fcd34d;
    cursor: pointer;
    transition: all 0.18s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    width: 100%;
}
.btn-warning:hover {
    background: linear-gradient(135deg, #fde68a 0%, #fcd34d 100%);
    transform: translateY(-1px);
}

/* ============================================ */
/* MODAL                                         */
/* ============================================ */
.modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
    padding: 1rem;
}
.modal-backdrop.hidden { display: none; }
.modal-content {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 24px 48px rgba(0, 0, 0, 0.2);
    max-width: 28rem;
    width: 100%;
    padding: 1.5rem;
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

/* ============================================ */
/* RESPONSIVE                                    */
/* ============================================ */
@media (max-width: 640px) {
    .invite-code-display .code { font-size: 1.25rem; }
    .page-header { padding: 1rem; }
}
</style>

<div class="max-w-6xl mx-auto px-4 py-6">

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-cog text-[#1a2a4a]"></i> Company Settings
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">Manage your company details and settings.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- LEFT: MAIN SETTINGS FORM --}}
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('company.settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- SECTION: Company Info --}}
                <div class="form-section">
                    <div class="section-title">
                        <span class="icon"><i class="fas fa-building"></i></span>
                        Company Information
                    </div>

                    {{-- Logo --}}
                    <div class="mb-5">
                        <label class="form-label">Company Logo</label>
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                @if($org->getLogoUrl())
                                    <img src="{{ $org->getLogoUrl() }}"
                                         alt="{{ $org->name }}"
                                         class="w-16 h-16 rounded-xl object-cover border-2 border-indigo-200 shadow-sm">
                                @else
                                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-indigo-100 to-blue-100 flex items-center justify-center text-lg font-bold text-indigo-600 border-2 border-indigo-200 shadow-sm">
                                        {{ strtoupper(substr($org->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="file-input-wrapper">
                                    <input type="file" name="logo" accept="image/*">
                                    <div class="file-label">
                                        <span><i class="fas fa-upload mr-2 text-indigo-500"></i> Choose logo</span>
                                        <span class="text-xs px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-full">PNG, JPG, SVG</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($org->logo)
                            <div class="mt-2">
                                <a href="{{ route('company.logo.remove') }}"
                                   onclick="return confirm('Remove company logo?')"
                                   class="text-xs text-rose-500 hover:text-rose-700 transition inline-flex items-center gap-1">
                                    <i class="fas fa-trash-alt"></i> Remove logo
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Name --}}
                    <div class="mb-4">
                        <label class="form-label">Company Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $org->name) }}" required
                               class="form-input"
                               placeholder="Enter company name">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Industry --}}
                    <div class="mb-4">
                        <label class="form-label">Industry</label>
                        <input type="text" name="industry" value="{{ old('industry', $org->industry) }}"
                               class="form-input"
                               placeholder="e.g., Engineering, Construction">
                        @error('industry')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="4"
                                  class="form-textarea"
                                  placeholder="Company description">{{ old('description', $org->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ACTION --}}
                <button type="submit" class="btn-primary w-full">
                    <i class="fas fa-save"></i> Update Company Settings
                </button>
            </form>
        </div>

        {{-- RIGHT: SIDEBAR --}}
        <div>

            {{-- Invite Code --}}
            <div class="form-section">
                <div class="section-title">
                    <span class="icon"><i class="fas fa-key"></i></span>
                    Invite Code
                </div>

                <div class="invite-code-display">
                    <p class="text-xs text-gray-500 uppercase tracking-wider flex items-center justify-center gap-1">
                        <i class="fas fa-qrcode text-indigo-500"></i> Share this code
                    </p>
                    <p class="code">{{ $org->invite_code }}</p>
                    <p class="text-xs text-gray-500">New members can join with this code</p>
                </div>

                <form method="POST" action="{{ route('company.settings.regenerate-invite') }}"
                      onsubmit="return confirm('⚠️ Regenerate invite code?\n\nThis will invalidate the current code.\nExisting members will not be affected.')"
                      class="mt-3">
                    @csrf
                    <button type="submit" class="btn-warning">
                        <i class="fas fa-sync-alt"></i> Regenerate Code
                    </button>
                </form>
            </div>

            {{-- Stats --}}
            <div class="form-section">
                <div class="section-title">
                    <span class="icon"><i class="fas fa-chart-bar"></i></span>
                    Company Stats
                </div>

                <div class="stat-item">
                    <span class="stat-label">
                        <span class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xs">
                            <i class="fas fa-users"></i>
                        </span>
                        Total Members
                    </span>
                    <span class="stat-value blue">{{ $totalMembers ?? 0 }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">
                        <span class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-xs">
                            <i class="fas fa-folder"></i>
                        </span>
                        Total Projects
                    </span>
                    <span class="stat-value purple">{{ $totalProjects ?? 0 }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">
                        <span class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-xs">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        Active Projects
                    </span>
                    <span class="stat-value green">{{ $activeProjects ?? 0 }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">
                        <span class="w-6 h-6 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 text-xs">
                            <i class="fas fa-trophy"></i>
                        </span>
                        Completed Projects
                    </span>
                    <span class="stat-value indigo">{{ $completedProjects ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- DANGER ZONE --}}
    <div class="danger-zone">
        <div class="section-title" style="color:#dc2626;">
            <span class="icon" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                <i class="fas fa-exclamation-triangle"></i>
            </span>
            Danger Zone
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white/60 rounded-xl p-4 border border-red-200">
                <h4 class="font-semibold text-red-600 text-sm">Delete Company</h4>
                <p class="text-xs text-red-500 mt-1 leading-relaxed">
                    Permanently delete this company and all associated data.
                </p>
                <button onclick="confirmDeleteCompany()" class="btn-danger mt-3">
                    <i class="fas fa-trash"></i> Delete Company
                </button>
            </div>

            <div class="bg-gray-50/50 rounded-xl p-4 border-2 border-dashed border-gray-200 flex items-center justify-center text-center">
                <div class="text-gray-400">
                    <i class="fas fa-shield-alt text-2xl mb-1 block"></i>
                    <p class="text-xs font-medium">Company Protected</p>
                    <p class="text-[0.65rem] text-gray-400 mt-0.5">Only the Principal can delete</p>
                </div>
            </div>
        </div>

        <div class="mt-4 p-3 bg-white/60 rounded-lg border border-red-100 flex items-start gap-2">
            <i class="fas fa-info-circle text-red-400 text-sm mt-0.5"></i>
            <p class="text-xs text-red-600">
                <span class="font-semibold">Note:</span> Deleting your company will remove all projects, tasks, and member access.
                This action is <span class="font-bold uppercase">permanent</span> and cannot be reversed.
            </p>
        </div>
    </div>

    {{-- DELETE MODAL --}}
    <div id="deleteCompanyModal" class="modal-backdrop hidden">
        <div class="modal-content">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-red-100 to-rose-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>

                <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Company?</h3>
                <p class="text-gray-500 text-sm mb-4">
                    Are you sure you want to delete <strong class="text-gray-900">{{ $org->name }}</strong>?
                </p>

                <div class="bg-gradient-to-r from-red-50 to-rose-50 rounded-xl p-4 mb-4 text-left border-2 border-red-100">
                    <p class="text-sm font-semibold text-red-600 mb-2 flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i> This will permanently delete:
                    </p>
                    <ul class="text-xs text-red-500 space-y-1.5">
                        <li class="flex items-center gap-2"><i class="fas fa-folder"></i> All projects</li>
                        <li class="flex items-center gap-2"><i class="fas fa-tasks"></i> All tasks</li>
                        <li class="flex items-center gap-2"><i class="fas fa-users"></i> All team member access</li>
                        <li class="flex items-center gap-2"><i class="fas fa-file"></i> All files and data</li>
                    </ul>
                </div>

                <div class="flex items-center justify-center gap-2 p-2 bg-red-50 rounded-lg border border-red-200 mb-5">
                    <i class="fas fa-exclamation-circle text-red-500 text-xs"></i>
                    <p class="text-red-600 text-xs font-semibold">This action cannot be undone!</p>
                </div>

                <div class="flex gap-3">
                    <button onclick="closeDeleteCompanyModal()" class="btn-secondary flex-1">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <form method="POST" action="{{ route('company.destroy') }}" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger w-full">
                            <i class="fas fa-trash"></i> Yes, Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDeleteCompany() {
        document.getElementById('deleteCompanyModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeDeleteCompanyModal() {
        document.getElementById('deleteCompanyModal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    document.getElementById('deleteCompanyModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteCompanyModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDeleteCompanyModal();
    });

    // File input label update
    document.querySelector('.file-input-wrapper input[type="file"]')?.addEventListener('change', function(e) {
        const label = this.closest('.file-input-wrapper').querySelector('.file-label span:first-child');
        if (this.files.length > 0) {
            label.innerHTML = '<i class="fas fa-file mr-2 text-indigo-500"></i> ' + this.files[0].name;
        } else {
            label.innerHTML = '<i class="fas fa-upload mr-2 text-indigo-500"></i> Choose logo';
        }
    });
</script>

@endsection