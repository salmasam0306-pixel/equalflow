{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Profile')

@section('content')

<style>
/* ============================================ */
/* BASE FORM CONTROLS                            */
/* ============================================ */
.form-input {
    padding: 0.7rem 1rem;
    border-radius: 0.65rem;
    border: 1px solid #e5e7eb;
    background: #fafbfc;
    transition: all 0.18s ease;
    width: 100%;
    font-size: 0.875rem;
    color: #1f2937;
}
.form-input:hover { border-color: #cbd5e1; }
.form-input:focus {
    background: #fff;
    border-color: #1a2a4a;
    box-shadow: 0 0 0 4px rgba(26, 42, 74, 0.08);
    outline: none;
}
.form-input::placeholder { color: #9ca3af; }

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
    display: flex;
    align-items: center;
    gap: 0.85rem;
    margin-bottom: 1.5rem;
}
.page-header .icon {
    width: 44px;
    height: 44px;
    border-radius: 0.75rem;
    background: linear-gradient(135deg, #3b82f6, #4f46e5);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
    flex-shrink: 0;
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

/* ============================================ */
/* READ-ONLY INFO ROW                            */
/* ============================================ */
.readonly-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.65rem 0.85rem;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 0.65rem;
    margin-bottom: 0.5rem;
}
.readonly-row:last-child { margin-bottom: 0; }
.readonly-row .label {
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.readonly-row .value {
    font-size: 0.85rem;
    color: #1f2937;
    font-weight: 500;
    text-align: right;
}

/* ============================================ */
/* INFO BOX                                      */
/* ============================================ */
.info-box {
    border-radius: 0.65rem;
    padding: 0.85rem 1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    font-size: 0.8rem;
    line-height: 1.4;
}
.info-box.blue {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1e40af;
}
.info-box.purple {
    background: #f5f3ff;
    border: 1px solid #ddd6fe;
    color: #5b21b6;
}
.info-box i { flex-shrink: 0; }

/* ============================================ */
/* PROFILE PICTURE                               */
/* ============================================ */
.pfp-wrapper {
    position: relative;
    display: inline-block;
}
.pfp-wrapper img {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e0e7ff;
    box-shadow: 0 4px 12px rgba(26, 42, 74, 0.12);
}
.pfp-wrapper .verified-badge {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 22px;
    height: 22px;
    background: #10b981;
    border-radius: 50%;
    border: 2px solid #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 0.55rem;
    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.4);
}

.file-input-custom {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    font-size: 0.8rem;
    color: #6b7280;
}
.file-input-custom input[type="file"] {
    font-size: 0.75rem;
    color: #6b7280;
    cursor: pointer;
}
.file-input-custom input[type="file"]::file-selector-button {
    background: #1a2a4a;
    color: #fff;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.15s ease;
    margin-right: 0.5rem;
}
.file-input-custom input[type="file"]::file-selector-button:hover {
    background: #0f1a30;
}

/* ============================================ */
/* STATUS BADGE (inline)                         */
/* ============================================ */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

/* ============================================ */
/* BUTTONS                                       */
/* ============================================ */
.btn-primary {
    background: linear-gradient(135deg, #1a2a4a, #2d4a7a);
    color: #fff;
    padding: 0.75rem 1.75rem;
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
    width: 100%;
}
.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(26, 42, 74, 0.28);
}

.btn-danger {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: #fff;
    padding: 0.75rem 1.5rem;
    border-radius: 0.7rem;
    font-weight: 600;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: all 0.18s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    width: 100%;
}
.btn-danger:hover {
    background: linear-gradient(135deg, #b91c1c, #991b1b);
    box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
    transform: translateY(-1px);
}

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
.danger-zone .section-title { color: #dc2626; }
.danger-zone .section-title .icon {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
}
</style>

<div class="max-w-2xl mx-auto px-4 py-6">

    {{-- Page Header --}}
    <div class="page-header">
        <span class="icon"><i class="fas fa-user-cog"></i></span>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Profile</h2>
            <p class="text-sm text-gray-500 mt-0.5">Update your profile information and settings.</p>
        </div>
    </div>

    {{-- Alerts --}}
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
    @if(session('info'))
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-info-circle"></i> {{ session('info') }}
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        {{-- SECTION 1: Personal Information --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-user"></i></span>
                Personal Information
            </div>

            {{-- Profile Picture --}}
            <div class="mb-5">
                <label class="form-label">Profile Picture</label>
                <div class="flex items-center gap-5">
                    <div class="pfp-wrapper flex-shrink-0">
                        <img src="{{ auth()->user()->getProfilePictureUrl() }}" alt="Profile Picture">
                        @if(auth()->user()->profile_picture)
                            <span class="verified-badge">
                                <i class="fas fa-check"></i>
                            </span>
                        @endif
                    </div>
                    <div class="flex-1 file-input-custom">
                        <input type="file" name="profile_picture" accept="image/*">
                    </div>
                </div>
                @if(auth()->user()->profile_picture)
                    <div class="mt-2">
                        <a href="{{ route('profile.picture.remove') }}"
                           onclick="return confirm('Remove your profile picture?')"
                           class="text-xs text-rose-500 hover:text-rose-700 transition inline-flex items-center gap-1">
                            <i class="fas fa-trash-alt"></i> Remove profile picture
                        </a>
                    </div>
                @endif
            </div>

            {{-- Name --}}
            <div class="mb-4">
                <label class="form-label">Name <span class="text-red-500">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="form-input @error('name') border-red-500 @enderror"
                       placeholder="Your full name">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label class="form-label">Email <span class="text-red-500">*</span></label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="form-input @error('email') border-red-500 @enderror"
                       placeholder="you@company.com">
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label class="form-label">Mobile Phone</label>
                <input id="phone" type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="form-input @error('phone') border-red-500 @enderror"
                       placeholder="e.g., 012-345 6789"
                       maxlength="30">
                @error('phone')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- SECTION 2: Role & Job Scope (read-only) --}}
        @if(auth()->user()->isMember() || auth()->user()->isLeader())
            <div class="form-section">
                <div class="section-title">
                    <span class="icon"><i class="fas fa-briefcase"></i></span>
                    Role & Job Scope
                    <span class="section-hint" style="margin-left:auto;font-size:0.68rem;color:#94a3b8;font-weight:400;text-transform:none;">
                        <i class="fas fa-lock mr-1"></i> Set by the Principal
                    </span>
                </div>

                {{-- Role --}}
                <div class="readonly-row">
                    <span class="label">
                        <i class="fas fa-user-tag text-emerald-500"></i> Role
                    </span>
                    <span class="value capitalize flex items-center gap-1.5">
                        @if($user->role === 'principal')
                            <i class="fas fa-crown text-yellow-500"></i>
                        @elseif($user->role === 'leader')
                            <i class="fas fa-star text-blue-500"></i>
                        @else
                            <i class="fas fa-user text-gray-500"></i>
                        @endif
                        {{ ucfirst($user->role) }}
                    </span>
                </div>

                {{-- Job Scope --}}
                <div class="readonly-row">
                    <span class="label">
                        <i class="fas fa-briefcase text-blue-500"></i> Job Scope
                    </span>
                    <span class="value">
                        @if($user->job_scope)
                            <i class="fas {{ $user->getJobScopeIcon() }} text-gray-500 mr-1"></i>
                            {{ $user->getJobScopeLabel() }}
                        @else
                            <span class="text-gray-400 italic text-xs">Not set yet</span>
                        @endif
                    </span>
                </div>

                {{-- Specialties --}}
                <div class="mt-3">
                    <label class="form-label">Specialties</label>
                    <div class="bg-gray-50 px-3 py-2.5 rounded-lg border border-gray-200 min-h-[44px]">
                        @if(!empty($user->specialties) && is_array($user->specialties))
                            <div class="flex flex-wrap gap-1">
                                @foreach($user->specialties as $skill)
                                    <span class="text-xs px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-full border border-indigo-100">
                                        {{ $skill }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-400 italic">Not set yet</p>
                        @endif
                    </div>
                </div>
            </div>
        @elseif(auth()->user()->isPersonal())
            <div class="form-section">
                <div class="info-box blue">
                    <i class="fas fa-info-circle mt-0.5"></i>
                    <span>Personal users don't need a job scope or specialties.</span>
                </div>
            </div>
        @elseif(auth()->user()->isPrincipal())
            <div class="form-section">
                <div class="info-box purple">
                    <i class="fas fa-info-circle mt-0.5"></i>
                    <span>Principals don't need a job scope or specialties.</span>
                </div>
            </div>
        @endif

        {{-- SECTION 3: Status --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-clock"></i></span>
                Current Status
            </div>

            <div class="readonly-row">
                <span class="label">
                    <i class="fas fa-circle text-emerald-500"></i> Availability
                </span>
                <div class="flex items-center gap-2">
                    <span class="status-badge bg-{{ $user->getStatusColor() }}-100 text-{{ $user->getStatusColor() }}-700">
                        <i class="fas {{ $user->getStatusIcon() }}"></i>
                        {{ $user->getStatusLabel() }}
                    </span>
                    @if($user->status_until)
                        <span class="text-xs text-gray-400">until {{ $user->status_until->format('d M Y') }}</span>
                    @endif
                </div>
            </div>

            @if($user->status_note)
                <div class="mt-2 text-xs text-gray-500 flex items-start gap-1.5 px-1">
                    <i class="fas fa-comment text-purple-400 mt-0.5"></i>
                    <span>{{ $user->status_note }}</span>
                </div>
            @endif

            <a href="{{ route('status.edit') }}" class="mt-3 inline-flex items-center gap-1.5 text-sm text-[#1a2a4a] hover:text-[#0f1a30] font-medium">
                <i class="fas fa-edit"></i> Update status
            </a>
        </div>

        {{-- SUBMIT --}}
        <button type="submit" class="btn-primary">
            <i class="fas fa-save"></i> Update Profile
        </button>
    </form>

    {{-- DANGER ZONE --}}
    <div class="danger-zone">
        <div class="section-title">
            <span class="icon"><i class="fas fa-exclamation-triangle"></i></span>
            Danger Zone
        </div>

        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('DELETE')

            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <input id="password" type="password" name="password" required
                       class="form-input @error('password') border-red-500 @enderror"
                       placeholder="Enter your password to delete account">
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-danger"
                    onclick="return confirm('⚠️ Are you sure you want to delete your account?\n\nThis will permanently delete:\n- All your personal projects\n- All your tasks\n- Your company memberships\n\nThis action cannot be undone!')">
                <i class="fas fa-trash-alt"></i> Delete Account
            </button>
        </form>
    </div>
</div>

@endsection