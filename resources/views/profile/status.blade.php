@extends('layouts.app')

@section('title', 'Update Status')

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
    background: linear-gradient(135deg, #f59e0b, #ea580c);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
    flex-shrink: 0;
}

/* ============================================ */
/* USER CARD                                     */
/* ============================================ */
.user-card {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border: 1px solid #e5e7eb;
    border-radius: 1rem;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.85rem;
    margin-bottom: 1rem;
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
/* STATUS OPTIONS                                */
/* ============================================ */
.status-option {
    border: 1.5px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 0.85rem 0.75rem;
    cursor: pointer;
    transition: all 0.18s ease;
    position: relative;
    overflow: hidden;
    background: #fff;
    display: block;
}
.status-option:hover {
    border-color: #94a3b8;
    transform: translateY(-1px);
}
.status-option:has(input:checked) {
    border-color: #1a2a4a;
    background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
    box-shadow: 0 4px 14px rgba(26, 42, 74, 0.08);
}
.status-option:has(input:checked)::after {
    content: '';
    position: absolute;
    top: 0; right: 0;
    width: 0; height: 0;
    border-style: solid;
    border-width: 0 26px 26px 0;
    border-color: transparent #1a2a4a transparent transparent;
}
.status-option:has(input:checked) .check-mark {
    position: absolute;
    top: 3px; right: 5px;
    color: white;
    font-size: 0.55rem;
    z-index: 1;
}
.status-option input { display: none; }
.status-option .status-icon { font-size: 1.35rem; margin-bottom: 0.2rem; }
.status-option .status-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #1f2937;
    display: block;
}
.status-option .status-desc {
    font-size: 0.65rem;
    color: #6b7280;
    display: block;
    margin-top: 0.1rem;
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

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    color: #94a3b8;
    font-size: 0.8rem;
    text-decoration: none;
    transition: color 0.15s ease;
}
.back-link:hover { color: #475569; }
</style>

<div class="max-w-2xl mx-auto px-4 py-6">

    {{-- Page Header --}}
    <div class="page-header">
        <span class="icon"><i class="fas fa-user-clock"></i></span>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Update Status</h2>
            <p class="text-sm text-gray-500 mt-0.5">Let your team know your availability.</p>
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

    {{-- User card --}}
    <div class="user-card">
        <img src="{{ $user->getProfilePictureUrl() }}"
             alt="{{ $user->name }}"
             class="w-12 h-12 rounded-full object-cover border-2 border-indigo-200 shadow-sm flex-shrink-0">
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
            <p class="text-xs text-gray-500 capitalize flex items-center gap-1">
                <i class="fas fa-user-tag text-gray-400"></i> {{ ucfirst($user->role) }}
            </p>
        </div>
        <span class="status-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-{{ $user->getStatusColor() }}-100 text-{{ $user->getStatusColor() }}-700">
            <i class="fas {{ $user->getStatusIcon() }}"></i>
            {{ $user->getStatusLabel() }}
        </span>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('status.update') }}" id="statusForm">
        @csrf
        @method('PUT')

        {{-- SECTION 1: Status Selection --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-circle-check"></i></span>
                Select Status
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5">
                <label class="status-option">
                    <input type="radio" name="status" value="active"
                           {{ old('status', $user->status) === 'active' ? 'checked' : '' }}
                           data-show-date="false">
                    <span class="check-mark"><i class="fas fa-check"></i></span>
                    <div class="text-center">
                        <div class="status-icon"><i class="fas fa-circle text-green-500"></i></div>
                        <span class="status-label">Active</span>
                        <span class="status-desc">Available</span>
                    </div>
                </label>

                <label class="status-option">
                    <input type="radio" name="status" value="outstation"
                           {{ old('status', $user->status) === 'outstation' ? 'checked' : '' }}
                           data-show-date="true">
                    <span class="check-mark"><i class="fas fa-check"></i></span>
                    <div class="text-center">
                        <div class="status-icon"><i class="fas fa-plane text-blue-500"></i></div>
                        <span class="status-label">Outstation</span>
                        <span class="status-desc">Remote/site</span>
                    </div>
                </label>

                <label class="status-option">
                    <input type="radio" name="status" value="annual_leave"
                           {{ old('status', $user->status) === 'annual_leave' ? 'checked' : '' }}
                           data-show-date="true">
                    <span class="check-mark"><i class="fas fa-check"></i></span>
                    <div class="text-center">
                        <div class="status-icon"><i class="fas fa-umbrella-beach text-yellow-500"></i></div>
                        <span class="status-label">Annual Leave</span>
                        <span class="status-desc">Vacation</span>
                    </div>
                </label>

                <label class="status-option">
                    <input type="radio" name="status" value="medical_leave"
                           {{ old('status', $user->status) === 'medical_leave' ? 'checked' : '' }}
                           data-show-date="true">
                    <span class="check-mark"><i class="fas fa-check"></i></span>
                    <div class="text-center">
                        <div class="status-icon"><i class="fas fa-notes-medical text-red-500"></i></div>
                        <span class="status-label">Medical Leave</span>
                        <span class="status-desc">Sick</span>
                    </div>
                </label>
            </div>

            @error('status')
                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- SECTION 2: Return Date (conditional) --}}
        <div id="untilDateContainer"
             class="form-section {{ in_array(old('status', $user->status), ['outstation', 'annual_leave', 'medical_leave']) ? '' : 'hidden' }}">
            <div class="section-title">
                <span class="icon"><i class="fas fa-calendar-alt"></i></span>
                Return Date
            </div>

            <input id="status_until" type="date" name="status_until"
                   value="{{ old('status_until', $user->status_until?->format('Y-m-d')) }}"
                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                   class="form-input @error('status_until') border-red-500 @enderror">

            @error('status_until')
                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror

            <p id="dateWarning" class="text-xs text-red-500 hidden flex items-center gap-1 mt-2">
                <i class="fas fa-exclamation-circle"></i> Please select a return date.
            </p>
        </div>

        {{-- SECTION 3: Note --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-comment"></i></span>
                Note
                <span style="margin-left:auto;font-size:0.68rem;color:#94a3b8;font-weight:400;text-transform:none;">Optional</span>
            </div>

            <textarea id="status_note" name="status_note" rows="3"
                      class="form-input @error('status_note') border-red-500 @enderror"
                      placeholder="Add any additional information...">{{ old('status_note', $user->status_note) }}</textarea>

            @error('status_note')
                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Last updated --}}
        <p class="text-xs text-gray-400 flex items-center gap-1 mb-3 px-1">
            <i class="fas fa-clock text-blue-400"></i>
            Last updated: {{ $user->status_updated_at ? $user->status_updated_at->format('d M Y H:i') : 'Never' }}
        </p>

        {{-- Submit --}}
        <button type="submit" class="btn-primary">
            <i class="fas fa-save"></i> Update Status
        </button>
    </form>

    {{-- Back link --}}
    <div class="mt-4 text-center">
        <a href="{{ route('profile.edit') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Profile
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusRadios = document.querySelectorAll('.status-option input[type="radio"]');
        const untilDateContainer = document.getElementById('untilDateContainer');
        const untilDateInput = document.getElementById('status_until');
        const dateWarning = document.getElementById('dateWarning');
        const statusForm = document.getElementById('statusForm');

        function toggleUntilDate() {
            let showDate = false;
            statusRadios.forEach(function(radio) {
                if (radio.checked && radio.dataset.showDate === 'true') {
                    showDate = true;
                }
            });

            if (showDate) {
                untilDateContainer.classList.remove('hidden');
                untilDateInput.required = true;
                dateWarning.classList.add('hidden');
            } else {
                untilDateContainer.classList.add('hidden');
                untilDateInput.required = false;
                untilDateInput.value = '';
                dateWarning.classList.add('hidden');
                untilDateInput.classList.remove('border-red-500');
            }
        }

        statusRadios.forEach(function(radio) {
            radio.addEventListener('change', toggleUntilDate);
        });

        toggleUntilDate();

        statusForm.addEventListener('submit', function(e) {
            if (!untilDateContainer.classList.contains('hidden')) {
                const dateValue = untilDateInput.value.trim();

                if (!dateValue) {
                    e.preventDefault();
                    alert('⚠️ Please select a return date.\n\nYou must specify when you will return to work.');
                    untilDateInput.focus();
                    untilDateInput.classList.add('border-red-500');
                    dateWarning.classList.remove('hidden');
                    return false;
                }

                const selectedDate = new Date(dateValue);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (selectedDate <= today) {
                    e.preventDefault();
                    alert('⚠️ Invalid return date.\n\nThe return date must be in the future.');
                    untilDateInput.focus();
                    untilDateInput.classList.add('border-red-500');
                    return false;
                }
            }

            return true;
        });

        untilDateInput.addEventListener('input', function() {
            this.classList.remove('border-red-500');
            dateWarning.classList.add('hidden');
        });
    });
</script>

@endsection