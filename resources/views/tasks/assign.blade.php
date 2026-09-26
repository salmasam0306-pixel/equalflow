@extends('layouts.app')

@section('title', 'Assign Task - AI Match')

@section('content')

<style>
/* ============================================ */
/* PAGE HEADER                                   */
/* ============================================ */
.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.page-header-title {
    display: flex;
    align-items: center;
    gap: 0.85rem;
}
.page-header-title .icon {
    width: 44px;
    height: 44px;
    border-radius: 0.75rem;
    background: linear-gradient(135deg, #1a2a4a, #2d4a7a);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(26, 42, 74, 0.22);
    flex-shrink: 0;
}

/* ============================================ */
/* CARD STYLES                                   */
/* ============================================ */
.card {
    background: #ffffff;
    border-radius: 1rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: all 0.2s ease;
}
.card:hover {
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
    border-color: #d1d5db;
}

/* ============================================ */
/* REQUIREMENTS SUMMARY                          */
/* ============================================ */
.req-item {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 0.85rem 1rem;
    transition: all 0.15s ease;
}
.req-item:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}
.req-item .req-label {
    font-size: 0.65rem;
    font-weight: 600;
    color: #94a3b8;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    margin-bottom: 0.5rem;
}

/* ============================================ */
/* CANDIDATE CARDS                               */
/* ============================================ */
.candidate-card {
    border-radius: 0.875rem;
    padding: 1.15rem 1.25rem;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
    background: #fff;
}
.candidate-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    transition: height 0.2s ease;
}
.candidate-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}
.candidate-card:hover::before {
    height: 4px;
}

/* Color variants */
.candidate-card.gold::before  { background: linear-gradient(90deg, #f59e0b, #d97706); }
.candidate-card.gold:hover    { border-color: #fcd34d; box-shadow: 0 10px 30px rgba(245, 158, 11, 0.15); }

.candidate-card.green::before { background: linear-gradient(90deg, #34d399, #059669); }
.candidate-card.green:hover   { border-color: #6ee7b7; box-shadow: 0 10px 30px rgba(52, 211, 153, 0.15); }

.candidate-card.blue::before  { background: linear-gradient(90deg, #60a5fa, #2563eb); }
.candidate-card.blue:hover    { border-color: #93c5fd; box-shadow: 0 10px 30px rgba(96, 165, 250, 0.15); }

.candidate-card.gray::before  { background: linear-gradient(90deg, #9ca3af, #6b7280); }
.candidate-card.gray:hover    { border-color: #d1d5db; box-shadow: 0 10px 30px rgba(156, 163, 175, 0.15); }

/* Top pick highlight */
.candidate-card.top-pick {
    border-width: 2px;
    border-color: #fbbf24;
    background: linear-gradient(135deg, #fffdf5 0%, #ffffff 100%);
}

/* ============================================ */
/* SCORE CIRCLE                                  */
/* ============================================ */
.score-circle {
    position: relative;
    width: 60px;
    height: 60px;
    flex-shrink: 0;
}
.score-circle svg { transform: rotate(-90deg); }
.score-circle .score-text {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    font-weight: 700;
    color: #1f2937;
    letter-spacing: -0.02em;
}

/* ============================================ */
/* SKILL TAGS                                    */
/* ============================================ */
.skill-tag-inline {
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
    padding: 0.15rem 0.55rem;
    border-radius: 9999px;
    font-size: 0.65rem;
    font-weight: 500;
    margin-right: 0.2rem;
    margin-bottom: 0.2rem;
}
.skill-tag-inline.matched {
    background: #d1fae5;
    color: #065f46;
}
.skill-tag-inline.missing {
    background: #f1f5f9;
    color: #94a3b8;
}

/* ============================================ */
/* MINI STAT BOX                                 */
/* ============================================ */
.stat-mini {
    text-align: center;
    padding: 0.35rem 0.75rem;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    min-width: 70px;
}
.stat-mini .stat-mini-value {
    display: block;
    font-size: 0.85rem;
    font-weight: 700;
    color: #1f2937;
    letter-spacing: -0.01em;
    line-height: 1.2;
}
.stat-mini .stat-mini-label {
    display: block;
    font-size: 0.55rem;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-weight: 600;
    margin-top: 0.15rem;
}

/* ============================================ */
/* INFO BOX                                      */
/* ============================================ */
.info-box {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 1px solid #bfdbfe;
    border-radius: 0.75rem;
    padding: 0.9rem 1.1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.7rem;
    font-size: 0.8rem;
    color: #1e40af;
    line-height: 1.45;
}
.info-box i { color: #3b82f6; flex-shrink: 0; }

/* ============================================ */
/* BUTTONS                                       */
/* ============================================ */
.btn-assign {
    background: linear-gradient(135deg, #1a2a4a, #2d4a7a);
    color: #fff;
    padding: 0.55rem 1.15rem;
    border-radius: 0.6rem;
    font-size: 0.8rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.18s ease;
    box-shadow: 0 2px 8px rgba(26, 42, 74, 0.15);
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}
.btn-assign:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(26, 42, 74, 0.28);
}

.btn-back {
    color: #6b7280;
    font-size: 0.8rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    border-radius: 0.5rem;
}
.btn-back:hover {
    color: #1a2a4a;
    background: #f3f4f6;
}

/* ============================================ */
/* BADGES                                        */
/* ============================================ */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.15rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.6rem;
    font-weight: 600;
    letter-spacing: 0.01em;
}
.badge-best {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
    border: 1px solid #fcd34d;
}
.badge-leader {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #1e40af;
    border: 1px solid #93c5fd;
}
.badge-you {
    background: #f3f4f6;
    color: #6b7280;
    border: 1px solid #e5e7eb;
}
.badge-candidates {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.85rem;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    border: 1px solid #bfdbfe;
    color: #1e40af;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 9999px;
}
</style>

<div class="max-w-5xl mx-auto px-4 py-6">

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <div class="page-header-title">
            <span class="icon"><i class="fas fa-tasks"></i></span>
            <div>
                <h2 class="text-xl font-bold text-gray-900">Assign Task</h2>
                <p class="text-sm text-gray-500 mt-0.5 flex items-center gap-1">
                    <i class="fas fa-folder text-blue-400 text-xs"></i>
                    {{ $task->title }} · {{ $task->project->name }}
                </p>
            </div>
        </div>
        <span class="text-xs px-3 py-1.5 rounded-full bg-amber-100 text-amber-700 font-medium inline-flex items-center gap-1">
            <i class="fas fa-clock"></i> {{ $task->status }}
        </span>
    </div>

    {{-- TASK INFO CARD --}}
    <div class="card p-5 mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            {{-- Skills --}}
            <div class="req-item">
                <p class="req-label">
                    <i class="fas fa-tags text-indigo-400"></i> Skills Required
                </p>
                <div class="flex flex-wrap">
                    @if(!empty($requirements['skills']))
                        @foreach($requirements['skills'] as $skill)
                            <span class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full inline-flex items-center gap-0.5 mr-1 mb-1">
                                <i class="fas fa-tag" style="font-size:0.55rem;"></i> {{ $skill }}
                            </span>
                        @endforeach
                    @else
                        <span class="text-xs text-gray-400 inline-flex items-center gap-1">
                            <i class="fas fa-info-circle"></i> No specific skills
                        </span>
                    @endif
                </div>
            </div>

            {{-- Experience --}}
            <div class="req-item">
                <p class="req-label">
                    <i class="fas fa-chart-bar text-emerald-400"></i> Experience Level
                </p>
                <p class="text-sm font-semibold text-gray-800">
                    {{ ucfirst($requirements['experience'] ?? 'Any') }}
                </p>
            </div>

            {{-- Hours --}}
            <div class="req-item">
                <p class="req-label">
                    <i class="fas fa-clock text-amber-400"></i> Estimated Hours
                </p>
                <p class="text-sm font-semibold text-gray-800">
                    {{ $requirements['hours'] ?? 'Not specified' }}{{ $requirements['hours'] ? ' hours' : '' }}
                </p>
            </div>
        </div>
    </div>

    {{-- INFO BOX --}}
    <div class="info-box mb-5">
        <i class="fas fa-clipboard-list text-lg"></i>
        <div>
            <p class="font-semibold">Project Leader Assignment</p>
            <p class="mt-0.5 text-xs">
                You are assigning this task to the best-matched team member.
                The AI has analyzed skills, experience, workload, and past performance.
            </p>
        </div>
    </div>

    {{-- CANDIDATES CARD --}}
    <div class="card p-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
            <div>
                <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                    <span class="w-7 h-7 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-xs">
                        <i class="fas fa-robot"></i>
                    </span>
                    AI Recommended Candidates
                </h3>
                <p class="text-xs text-gray-400 mt-0.5 ml-9 flex items-center gap-1">
                    <i class="fas fa-chart-simple"></i> Based on skills match, experience, workload, and past performance
                </p>
            </div>
            <span class="badge-candidates">
                <i class="fas fa-users"></i> {{ $matchedMembers->count() }} candidates
            </span>
        </div>

        @if($matchedMembers->isEmpty())
            <div class="text-center py-10">
                <i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-400 text-sm">No members available for assignment.</p>
                <p class="text-xs text-gray-300 mt-1">Try adding members to this project first.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($matchedMembers as $index => $memberData)
                    @php
                        if (is_array($memberData)) {
                            $member = $memberData['user'] ?? [];
                            $score = round($memberData['score'] ?? 0);
                            $skillsMatch = $memberData['skills_match'] ?? [];
                            $skillsMissing = $memberData['skills_missing'] ?? [];
                            $workload = round($memberData['workload'] ?? 0);
                            $experience = $memberData['experience'] ?? 0;
                            $completedTasks = $memberData['completed_tasks'] ?? 0;
                            $isLeader = $memberData['is_leader'] ?? false;
                            $userId = $member['id'] ?? null;
                            $userName = $member['name'] ?? 'Unknown';
                            $userEmail = $member['email'] ?? 'No email';
                            $userJobScope = $member['job_scope'] ?? null;
                        } else {
                            $member = $memberData->user;
                            $score = round($memberData->score);
                            $skillsMatch = $memberData->skills_match;
                            $skillsMissing = $memberData->skills_missing;
                            $workload = round($memberData->workload);
                            $experience = $memberData->experience;
                            $completedTasks = $memberData->completed_tasks;
                            $isLeader = $memberData->is_leader;
                            $userId = $member->id;
                            $userName = $member->name;
                            $userEmail = $member->email;
                            $userJobScope = $member->job_scope ?? null;
                        }

                        $isTop = $index === 0;

                        if ($score >= 80) {
                            $scoreColor = '#22c55e';
                            $scoreLabel = 'Excellent Match';
                            $cardClass = 'gold';
                        } elseif ($score >= 60) {
                            $scoreColor = '#eab308';
                            $scoreLabel = 'Good Match';
                            $cardClass = 'green';
                        } elseif ($score >= 40) {
                            $scoreColor = '#3b82f6';
                            $scoreLabel = 'Fair Match';
                            $cardClass = 'blue';
                        } else {
                            $scoreColor = '#9ca3af';
                            $scoreLabel = 'Needs Training';
                            $cardClass = 'gray';
                        }
                    @endphp

                    <div class="candidate-card {{ $cardClass }} {{ $isTop ? 'top-pick' : '' }}">
                        <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4">

                            {{-- Score + User --}}
                            <div class="flex items-center gap-4 w-full lg:w-auto flex-1 min-w-0">
                                {{-- Score Circle --}}
                                <div class="score-circle">
                                    <svg width="60" height="60">
                                        <circle cx="30" cy="30" r="26" fill="none" stroke="#f1f5f9" stroke-width="4"/>
                                        <circle cx="30" cy="30" r="26" fill="none"
                                            stroke="{{ $scoreColor }}"
                                            stroke-width="4"
                                            stroke-dasharray="{{ ($score / 100) * 163.36 }} 163.36"
                                            stroke-linecap="round"/>
                                    </svg>
                                    <span class="score-text">{{ $score }}%</span>
                                </div>

                                {{-- User Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <p class="font-semibold text-gray-900 text-sm flex items-center gap-1.5">
                                            {{ $userName }}
                                        </p>
                                        @if($isTop)
                                            <span class="badge badge-best">
                                                <i class="fas fa-trophy"></i> Best Match
                                            </span>
                                        @endif
                                        @if($isLeader)
                                            <span class="badge badge-leader">
                                                <i class="fas fa-star"></i> Leader
                                            </span>
                                        @endif
                                        @if($userId && $userId === auth()->id())
                                            <span class="badge badge-you">
                                                <i class="fas fa-user"></i> You
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $userEmail }}</p>
                                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                        @if($userJobScope)
                                            <span class="text-[0.65rem] px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full inline-flex items-center gap-1">
                                                <i class="fas fa-briefcase"></i> {{ ucfirst(str_replace('_', ' ', $userJobScope)) }}
                                            </span>
                                        @endif
                                        <span class="text-[0.65rem] text-gray-400 inline-flex items-center gap-1">
                                            <i class="fas fa-chart-bar"></i> {{ $experience }} yrs
                                        </span>
                                        <span class="text-[0.65rem] text-gray-400 inline-flex items-center gap-1">
                                            <i class="fas fa-check-circle text-emerald-400"></i> {{ $completedTasks }} done
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Stats --}}
                            <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                                <div class="stat-mini">
                                    <span class="stat-mini-value">{{ count($skillsMatch) }}/{{ count($task->skills_required ?? []) }}</span>
                                    <span class="stat-mini-label">Skills</span>
                                </div>
                                <div class="stat-mini">
                                    <span class="stat-mini-value"
                                        style="color: {{ $workload < 30 ? '#059669' : ($workload < 60 ? '#d97706' : '#dc2626') }}">
                                        {{ $workload }}%
                                    </span>
                                    <span class="stat-mini-label">Workload</span>
                                </div>
                            </div>
                        </div>

                        {{-- Skills Match Details --}}
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div class="flex flex-wrap">
                                @foreach($skillsMatch as $skill)
                                    <span class="skill-tag-inline matched">
                                        <i class="fas fa-check" style="font-size:0.5rem;"></i> {{ $skill }}
                                    </span>
                                @endforeach
                                @foreach($skillsMissing as $skill)
                                    <span class="skill-tag-inline missing">
                                        <i class="fas fa-times" style="font-size:0.5rem;"></i> {{ $skill }}
                                    </span>
                                @endforeach
                                @if(empty($skillsMatch) && empty($skillsMissing))
                                    <span class="text-xs text-gray-400 inline-flex items-center gap-1">
                                        <i class="fas fa-info-circle"></i> No skills specified
                                    </span>
                                @endif
                            </div>
                            <div class="mt-2 flex flex-wrap items-center gap-3">
                                <span class="text-xs text-gray-500 flex items-center gap-1.5">
                                    <span class="inline-block w-2 h-2 rounded-full" style="background: {{ $scoreColor }}"></span>
                                    {{ $scoreLabel }}
                                </span>
                                @if($workload >= 80)
                                    <span class="text-[0.65rem] text-red-500 flex items-center gap-1">
                                        <i class="fas fa-exclamation-triangle"></i> High workload
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Assign Button --}}
                        <div class="mt-4 flex justify-end">
                            <form method="POST" action="{{ route('tasks.assign.store', $task) }}">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $userId }}">
                                <button type="submit" class="btn-assign">
                                    <i class="fas fa-clipboard-list"></i> Assign to {{ $userName }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- BACK BUTTONS --}}
    <div class="mt-5 flex flex-wrap gap-2">
        <a href="{{ route('projects.show', $task->project->id) }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Project
        </a>
        @if(auth()->user()->isPrincipal())
            <a href="{{ route('tasks.edit', $task) }}" class="btn-back">
                <i class="fas fa-edit"></i> Edit Task
            </a>
        @endif
    </div>
</div>

@endsection