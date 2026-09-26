@extends('layouts.app')

@section('title', 'Invite People')

@section('content')

<style>
/* ============================================ */
/* BASE FORM CONTROLS                            */
/* ============================================ */
.form-input, .form-select {
    padding: 0.7rem 1rem;
    border-radius: 0.65rem;
    border: 1px solid #e5e7eb;
    background: #fafbfc;
    transition: all 0.18s ease;
    width: 100%;
    font-size: 0.875rem;
    color: #1f2937;
}
.form-input:hover, .form-select:hover { border-color: #cbd5e1; }
.form-input:focus, .form-select:focus {
    background: #fff;
    border-color: #1a2a4a;
    box-shadow: 0 0 0 4px rgba(26, 42, 74, 0.08);
    outline: none;
}
.form-input::placeholder { color: #9ca3af; }
.form-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.9rem center;
    background-size: 12px;
    padding-right: 2.2rem;
}

/* ============================================ */
/* LABELS                                        */
/* ============================================ */
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
    background: linear-gradient(135deg, #6366f1, #a855f7);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
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
.section-title .section-hint {
    margin-left: auto;
    font-size: 0.68rem;
    font-weight: 400;
    color: #94a3b8;
    letter-spacing: 0;
    text-transform: none;
}

/* ============================================ */
/* INVITE CODE DISPLAY                           */
/* ============================================ */
.invite-code-display {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.75rem 1rem;
    background: #f8fafc;
    border: 1px dashed #bfdbfe;
    border-radius: 0.65rem;
}
.invite-code-display .code {
    flex: 1;
    font-family: 'Courier New', monospace;
    font-weight: 700;
    color: #1a2a4a;
    letter-spacing: 0.1em;
    font-size: 1rem;
    background: transparent;
    border: none;
    outline: none;
    padding: 0;
}
.invite-code-display .copy-btn {
    background: linear-gradient(135deg, #1a2a4a, #2d4a7a);
    color: #fff;
    padding: 0.5rem 1rem;
    border-radius: 0.55rem;
    font-size: 0.75rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    transition: all 0.15s ease;
    flex-shrink: 0;
}
.invite-code-display .copy-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(26, 42, 74, 0.25);
}
.invite-code-hint {
    margin-top: 0.6rem;
    font-size: 0.72rem;
    color: #64748b;
    display: flex;
    align-items: flex-start;
    gap: 0.4rem;
    line-height: 1.45;
}
.invite-code-hint i { color: #3b82f6; margin-top: 0.1rem; flex-shrink: 0; }

/* ============================================ */
/* MEMBER ITEM                                   */
/* ============================================ */
.member-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.7rem 0.9rem;
    border: 1px solid #f1f5f9;
    border-radius: 0.65rem;
    margin-bottom: 0.4rem;
    background: #fafbfc;
    transition: all 0.15s ease;
}
.member-item:last-child { margin-bottom: 0; }
.member-item:hover {
    background: #ffffff;
    border-color: #bfdbfe;
}

.member-badge-creator {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.2rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.65rem;
    font-weight: 700;
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
    border: 1px solid #fcd34d;
}

.member-remove-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0.7rem;
    border-radius: 0.5rem;
    border: none;
    background: #fee2e2;
    color: #991b1b;
    font-size: 0.7rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}
.member-remove-btn:hover { background: #fecaca; }

/* ============================================ */
/* BUTTONS + ACTION BAR                          */
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
    padding: 0.75rem 1.5rem;
    border-radius: 0.7rem;
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    transition: all 0.18s ease;
    border: none;
    cursor: pointer;
}
.btn-secondary:hover {
    background: #e5e7eb;
    color: #1f2937;
    transform: translateY(-1px);
}

.action-bar {
    position: sticky;
    bottom: 0;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-top: 1px solid #e5e7eb;
    border-radius: 1rem 1rem 0 0;
    padding: 1rem 1.5rem;
    margin-top: 1rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: center;
    z-index: 10;
}

/* ============================================ */
/* EMPTY STATE                                   */
/* ============================================ */
.empty-hint {
    text-align: center;
    padding: 1.5rem 1rem;
    color: #94a3b8;
    font-size: 0.8rem;
}
.empty-hint i { font-size: 1.5rem; color: #cbd5e1; display: block; margin-bottom: 0.4rem; }

@media (max-width: 640px) {
    .form-section { padding: 1rem; }
    .invite-code-display .code { font-size: 0.85rem; }
}
</style>

<div class="max-w-2xl mx-auto px-4 py-6">

    {{-- Page Header --}}
    <div class="page-header">
        <span class="icon"><i class="fas fa-user-plus"></i></span>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Invite People</h2>
            <p class="text-sm text-gray-500 flex items-center gap-1 mt-0.5">
                <i class="fas fa-folder text-blue-400"></i> {{ $project->name }}
            </p>
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

    {{-- SECTION: Invite Code --}}
    <div class="form-section">
        <div class="section-title">
            <span class="icon"><i class="fas fa-key"></i></span>
            Invite Code
            <span class="section-hint">Share with people to join</span>
        </div>

        <div class="invite-code-display">
            <i class="fas fa-key text-amber-500"></i>
            <input type="text" value="{{ $project->invite_code }}" readonly
                   class="code"
                   onclick="this.select()">
            <button type="button"
                    onclick="copyToClipboard('{{ $project->invite_code }}')"
                    class="copy-btn">
                <i class="fas fa-copy"></i> Copy
            </button>
        </div>

        <p class="invite-code-hint">
            <i class="fas fa-info-circle"></i>
            <span>
                Share this code with others to invite them to this project.
                They can join by going to <strong class="text-gray-700">Join Personal Project</strong> in the sidebar.
            </span>
        </p>
    </div>

    {{-- SECTION: Current Members --}}
    <div class="form-section">
        <div class="section-title">
            <span class="icon"><i class="fas fa-users"></i></span>
            Current Members
            <span class="section-hint">{{ $project->members->count() }} total</span>
        </div>

        @forelse($project->members as $member)
            <div class="member-item">
                <div class="flex items-center gap-3 min-w-0">
                    <img src="{{ $member->getProfilePictureUrl() }}"
                         alt="{{ $member->name }}"
                         class="w-9 h-9 rounded-full object-cover border border-gray-200 flex-shrink-0">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $member->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $member->email }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    @if($member->id === $project->created_by)
                        <span class="member-badge-creator">
                            <i class="fas fa-crown"></i> Creator
                        </span>
                    @elseif(auth()->id() === $project->created_by)
                        <form method="POST"
                              action="{{ route('personal.members.remove', ['project' => $project, 'user' => $member->id]) }}"
                              onsubmit="return confirm('Remove {{ $member->name }} from this project?\n\nThis will also unassign any tasks assigned to them.')"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="member-remove-btn">
                                <i class="fas fa-user-minus"></i> Remove
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-hint">
                <i class="fas fa-users-slash"></i>
                No members yet.
            </div>
        @endforelse
    </div>

    {{-- SECTION: Invite New User (Creator only) --}}
    @if(auth()->id() === $project->created_by)
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-user-plus"></i></span>
                Invite New User
                <span class="section-hint">Direct invite to project members</span>
            </div>

            <form method="POST" action="{{ route('personal.invite.send', $project) }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Select User <span class="text-red-500">*</span></label>
                    <select name="user_id" required class="form-select">
                        <option value="">Choose a user to invite...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                @if($users->isEmpty())
                    <p class="text-xs text-gray-400 mb-3 flex items-center gap-1">
                        <i class="fas fa-info-circle text-blue-400"></i> No users available to invite.
                    </p>
                @endif

                <button type="submit" class="btn-primary w-full" {{ $users->isEmpty() ? 'disabled' : '' }}>
                    <i class="fas fa-paper-plane"></i> Send Invite
                </button>
            </form>
        </div>
    @endif

    {{-- ACTION BAR --}}
    <div class="action-bar">
        <div class="flex-1"></div>
        <a href="{{ route('personal.show', $project) }}" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Project
        </a>
    </div>
</div>

{{-- ============================================ --}}
{{-- SCRIPTS                                     --}}
{{-- ============================================ --}}
<script>
function copyToClipboard(text) {
    if (!text) {
        showToast('No invite code available', 'error');
        return;
    }

    navigator.clipboard.writeText(text).then(function() {
        showToast('Invite code copied!', 'success');
    }).catch(function() {
        // Fallback for older browsers
        const input = document.createElement('input');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        showToast('Invite code copied!', 'success');
    });
}

function showToast(message, type = 'success') {
    const colors = {
        success: 'bg-emerald-500',
        error: 'bg-red-500',
        warning: 'bg-amber-500',
        info: 'bg-blue-500'
    };

    const toast = document.createElement('div');
    toast.className = `fixed bottom-6 right-6 ${colors[type] || 'bg-emerald-500'} text-white px-4 py-3 rounded-xl shadow-lg text-sm z-50 transition-opacity duration-300 flex items-center gap-3 max-w-sm`;
    toast.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'} text-white/80"></i>
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
</script>

<style>
@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.fixed { animation: slideUp 0.3s ease-out; }
</style>

@endsection