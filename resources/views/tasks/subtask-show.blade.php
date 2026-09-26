{{-- resources/views/tasks/subtask-show.blade.php --}}
@extends('layouts.app')

@section('title', $task->title)

@section('content')

<style>
.card {
    background: #fff;
    border-radius: 1rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

/* ============================================ */
/* MEDIUM TINTED CARD (top info + history header) */
/* ============================================ */
.card-tinted {
    background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
    border: 1px solid #bfdbfe;
    border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(26, 42, 74, 0.06);
    position: relative;
    overflow: hidden;
}
.card-tinted::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 65%;
    height: 65%;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.08) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}
.card-tinted > * {
    position: relative;
    z-index: 1;
}

/* ============================================ */
/* SUBMISSION ITEM (light blue tint)            */
/* ============================================ */
.submission-item {
    border: 1px solid #bfdbfe;
    border-radius: 0.75rem;
    padding: 1rem;
    margin-bottom: 0.75rem;
    background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%);
    transition: all 0.2s ease;
}
.submission-item:hover {
    border-color: #93c5fd;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.08);
}

.file-upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 0.75rem;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}
.file-upload-area:hover,
.file-upload-area.dragover {
    border-color: #1a2a4a;
    background: #f8fafc;
}
.file-upload-area .upload-icon {
    font-size: 2.2rem;
    color: #9ca3af;
    margin-bottom: 0.4rem;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.65rem;
    border-radius: 9999px;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: capitalize;
    white-space: nowrap;
}
.status-todo         { background: #fef3c7; color: #92400e; }
.status-in_progress  { background: #dbeafe; color: #1e40af; }
.status-review       { background: #ede9fe; color: #5b21b6; }
.status-done         { background: #d1fae5; color: #065f46; }
.status-blocked      { background: #fecaca; color: #991b1b; }

.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.2rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.6rem;
    font-weight: 600;
}
.badge-approved           { background: #d1fae5; color: #065f46; }
.badge-pending            { background: #fef3c7; color: #92400e; }
.badge-rejected           { background: #fecaca; color: #991b1b; }
.badge-revision_requested { background: #fed7aa; color: #9a3412; }

/* ============================================ */
/* BUTTONS                                      */
/* ============================================ */
.btn-primary {
    background: linear-gradient(135deg, #1a2a4a, #2d4a7a);
    color: #fff;
    padding: 0.6rem 1.25rem;
    border-radius: 0.65rem;
    font-weight: 600;
    font-size: 0.85rem;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    text-decoration: none;
    transition: all 0.2s ease;
}
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(26, 42, 74, 0.25); color: #fff; }

.btn-secondary {
    background: #f3f4f6;
    color: #475569;
    padding: 0.55rem 1.1rem;
    border-radius: 0.65rem;
    font-weight: 600;
    font-size: 0.8rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.2s ease;
}
.btn-secondary:hover { background: #e5e7eb; color: #334155; }

.btn-danger {
    background: #fef2f2;
    color: #dc2626;
    padding: 0.4rem 0.8rem;
    border-radius: 0.5rem;
    border: 1px solid #fca5a5;
    font-weight: 500;
    font-size: 0.75rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    transition: all 0.2s ease;
}
.btn-danger:hover { background: #fee2e2; border-color: #dc2626; }
</style>

@php
    $user = auth()->user();
    $parent = $task->parent;
    $isAssignee = $task->assigned_to === $user->id;
    $isLeader = $parent && $parent->assigned_to === $user->id;
    $isPrincipal = $user->isPrincipal();

    $canEdit = $isAssignee || $isLeader;
    $canReviewSubmissions = ($isLeader || $isPrincipal);
@endphp

<div class="max-w-4xl mx-auto px-4 py-6">

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm flex items-start gap-2">
            <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm flex items-start gap-2">
            <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 text-amber-700 rounded-xl text-sm flex items-start gap-2">
            <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm space-y-1">
            @foreach($errors->all() as $error)
                <p class="flex items-start gap-2">
                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5 text-xs"></i>
                    <span>{{ $error }}</span>
                </p>
            @endforeach
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            @if($parent)
                <a href="{{ route('tasks.show', $parent) }}"
                   class="text-xs text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-1">
                    <i class="fas fa-arrow-left"></i> {{ $parent->title }}
                </a>
            @endif
            <h2 class="text-2xl font-bold text-gray-900">{{ $task->title }}</h2>
            @if($task->description)
                <p class="text-sm text-gray-500 mt-1">{{ $task->description }}</p>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span class="status-pill status-{{ $task->status }}">
                <i class="fas {{ $task->getStatusIcon() }}"></i> {{ $task->getStatusLabel() }}
            </span>
            @if($canEdit)
                <a href="{{ route('tasks.subtask.edit', $task) }}" class="btn-secondary">
                    <i class="fas fa-pen"></i> Edit
                </a>
            @endif
        </div>
    </div>

    {{-- INFO CARD (medium tint) --}}
    <div class="card-tinted p-5 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="text-gray-500 text-xs uppercase tracking-wider">Assignee</span>
                <div class="flex items-center gap-2 mt-1">
                    @if($task->assignee)
                        <img src="{{ $task->assignee->getProfilePictureUrl() }}"
                             class="w-7 h-7 rounded-full object-cover border border-white" alt="">
                        <span class="font-medium text-gray-800">{{ $task->assignee->name }}</span>
                    @else
                        <span class="text-gray-500">Unassigned</span>
                    @endif
                </div>
            </div>

            <div>
                <span class="text-gray-500 text-xs uppercase tracking-wider">Department</span>
                <p class="font-medium text-gray-800 mt-1">
                    {{ $task->department->name ?? 'Standalone' }}
                </p>
            </div>

            <div>
                <span class="text-gray-500 text-xs uppercase tracking-wider">Due Date</span>
                <p class="font-medium text-gray-800 mt-1">
                    {{ $task->due_date ? $task->due_date->format('d M Y') : '—' }}
                </p>
            </div>

            <div>
                <span class="text-gray-500 text-xs uppercase tracking-wider">Priority</span>
                <p class="font-medium text-gray-800 mt-1 capitalize">
                    {{ $task->getPriorityLabel() }}
                </p>
            </div>
        </div>

        @if(!empty($task->skills_required))
            <div class="mt-4 pt-4 border-t border-blue-200">
                <span class="text-gray-500 text-xs uppercase tracking-wider">Skills Required</span>
                <div class="flex flex-wrap gap-1 mt-2">
                    @foreach($task->skills_required as $skill)
                        <span class="text-xs px-2 py-0.5 bg-white text-indigo-700 rounded-full border border-indigo-200">{{ $skill }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- SUBMISSIONS HISTORY --}}
    <div class="card p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <span class="w-6 h-6 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center text-white text-xs">
                    <i class="fas fa-history"></i>
                </span>
                Submission History
            </h3>
            <span class="text-xs text-gray-400">{{ $task->submissions->count() }} submission(s)</span>
        </div>

        @forelse($task->submissions as $submission)
            <div class="submission-item">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2 mb-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm font-medium text-gray-900">Version {{ $submission->version }}</span>
                        <span class="badge badge-{{ $submission->status }}">
                            <i class="fas {{ $submission->getStatusIcon() }}"></i>
                            {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                        </span>
                        @if($submission->submitted_by === $user->id && $submission->status === 'pending')
                            <span class="text-xs px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full">
                                Your submission
                            </span>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('tasks.submissions.download', $submission) }}"
                           class="btn-secondary text-xs" title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                        @if($submission->status === 'pending'
                            && ($submission->submitted_by === $user->id || $isLeader))
                            <form method="POST"
                                  action="{{ route('tasks.submissions.delete', $submission) }}"
                                  onsubmit="return confirm('Delete this submission?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="text-xs text-gray-600 flex flex-wrap gap-x-3 gap-y-1">
                    <span><i class="fas fa-user text-blue-500"></i> {{ $submission->submitter->name }}</span>
                    <span><i class="fas fa-calendar text-gray-500"></i> {{ $submission->submitted_at?->format('d M Y H:i') }}</span>
                    <span><i class="fas fa-file text-purple-500"></i> {{ $submission->file_name }}</span>
                    <span><i class="fas fa-weight text-gray-500"></i> {{ $submission->getFileSizeFormatted() }}</span>
                </div>

                @if($submission->description)
                    <p class="text-sm text-gray-700 mt-2">{{ $submission->description }}</p>
                @endif

                @if($submission->review_notes)
                    <div class="mt-2 p-2 bg-white rounded-lg text-sm text-blue-700 border border-blue-200">
                        <strong><i class="fas fa-comment"></i> Review Notes:</strong>
                        {{ $submission->review_notes }}
                    </div>
                @endif

                @if($submission->reviewer)
                    <div class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-check-circle text-green-500"></i>
                        Reviewed by {{ $submission->reviewer->name }}
                        @if($submission->reviewed_at)
                            on {{ $submission->reviewed_at->format('d M Y H:i') }}
                        @endif
                    </div>
                @endif

                @if($submission->comments->count())
                    <div class="mt-3 pt-3 border-t border-blue-200 space-y-2">
                        @foreach($submission->comments as $comment)
                            <div class="flex items-start gap-2 text-sm">
                                <img src="{{ $comment->user->getProfilePictureUrl() }}"
                                     class="w-6 h-6 rounded-full object-cover border border-white flex-shrink-0" alt="">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-medium text-gray-800">{{ $comment->user->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-700 mt-0.5">{{ $comment->comment }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('tasks.submissions.comment', $submission) }}"
                      class="flex gap-2 mt-3">
                    @csrf
                    <input type="text" name="comment" placeholder="Add a comment..." required
                           class="flex-1 px-3 py-1.5 border border-blue-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent">
                    <button type="submit" class="btn-primary text-xs">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>

                @if($submission->status === 'pending'
                    && $canReviewSubmissions
                    && $submission->submitted_by !== $user->id)
                    <form method="POST"
                          action="{{ route('tasks.submissions.review', $submission) }}"
                          class="flex flex-col sm:flex-row gap-2 mt-3">
                        @csrf
                        <select name="status"
                                class="px-3 py-1.5 border border-blue-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent">
                            <option value="approved">✅ Approve</option>
                            <option value="revision_requested">📝 Request Revision</option>
                            <option value="rejected">❌ Reject</option>
                        </select>
                        <input type="text" name="review_notes" placeholder="Review notes..."
                               class="flex-1 px-3 py-1.5 border border-blue-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent">
                        <button type="submit" class="btn-primary text-xs">
                            <i class="fas fa-check-circle"></i> Submit Review
                        </button>
                    </form>
                @endif
            </div>
        @empty
            <div class="text-center py-10">
                <div class="text-3xl text-gray-300 mb-2"><i class="fas fa-inbox"></i></div>
                <p class="text-sm text-gray-400">No submissions yet.</p>
            </div>
        @endforelse
    </div>

    {{-- UPLOAD NEW SUBMISSION --}}
    @if($isAssignee && $task->status !== 'done' && !$task->is_blocked)
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center text-white text-xs">
                    <i class="fas fa-upload"></i>
                </span>
                Upload New Submission
            </h3>

            <form method="POST"
                  action="{{ route('tasks.submit.upload', $task) }}"
                  enctype="multipart/form-data" id="uploadForm">
                @csrf

                <div class="file-upload-area" id="dropZone">
                    <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                    <p class="text-sm text-gray-500">
                        <strong class="text-[#1a2a4a]">Click to upload</strong> or drag and drop
                    </p>
                    <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, JPG, PNG, ZIP · Max 20MB each</p>
                    <input type="file" name="files[]" id="fileInput" multiple required
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip"
                           style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0;">
                </div>

                <div id="fileList" class="mt-3 hidden">
                    <p class="text-xs font-medium text-gray-500 mb-2">Selected files:</p>
                    <div id="fileItems" class="space-y-1"></div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Description (optional)
                    </label>
                    <textarea name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent"
                              placeholder="Brief description of your work..."></textarea>
                </div>

                <button type="submit" class="btn-primary w-full mt-4 justify-center">
                    <i class="fas fa-upload"></i> Submit for Review
                </button>
            </form>
        </div>
    @elseif($isAssignee && $task->status === 'done')
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            This sub-task has been completed and approved.
        </div>
    @elseif($isAssignee && $task->is_blocked)
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm flex items-center gap-2">
            <i class="fas fa-lock"></i>
            This sub-task is blocked by dependencies.
        </div>
    @elseif($isPrincipal)
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-blue-700 text-sm flex items-center gap-2">
            <i class="fas fa-eye"></i>
            Viewing as Principal. You can review submissions above, but not edit this sub-task.
        </div>
    @elseif(!$isAssignee && !$isLeader)
        <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl text-gray-500 text-sm flex items-center gap-2">
            <i class="fas fa-info-circle"></i>
            You are not assigned to this sub-task.
        </div>
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropZone  = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileList  = document.getElementById('fileList');
    const fileItems = document.getElementById('fileItems');
    const form      = document.getElementById('uploadForm');

    if (!dropZone || !fileInput || !form) return;

    // Prevent drag events from bubbling to the page
    ['dragenter','dragover','dragleave','drop'].forEach(ev =>
        dropZone.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); })
    );

    // Visual feedback while dragging
    ['dragenter','dragover'].forEach(ev =>
        dropZone.addEventListener(ev, () => dropZone.classList.add('dragover'))
    );
    ['dragleave','drop'].forEach(ev =>
        dropZone.addEventListener(ev, () => dropZone.classList.remove('dragover'))
    );

    // Handle drop — only assign if files actually arrived
    dropZone.addEventListener('drop', e => {
        const dropped = e.dataTransfer && e.dataTransfer.files;
        if (dropped && dropped.length > 0) {
            fileInput.files = dropped;
            updateFileList(fileInput.files);
        }
    });

    // Open the file picker when the drop zone is clicked
    dropZone.addEventListener('click', () => fileInput.click());

    // CRITICAL: stop the input's own click from re-triggering dropZone's click
    fileInput.addEventListener('click', e => e.stopPropagation());

    // Update the file list when the picker returns files
    fileInput.addEventListener('change', () => {
        if (fileInput.files && fileInput.files.length > 0) {
            updateFileList(fileInput.files);
        }
    });

    function updateFileList(files) {
        if (!files || !files.length) {
            fileList.classList.add('hidden');
            fileItems.innerHTML = '';
            return;
        }
        fileList.classList.remove('hidden');
        fileItems.innerHTML = '';
        Array.from(files).forEach((file, i) => {
            const size = formatSize(file.size);
            const icon = getFileIcon(file.name);
            fileItems.insertAdjacentHTML('beforeend', `
                <div class="flex items-center justify-between gap-2 px-2 py-1 bg-gray-50 rounded text-xs">
                    <div class="flex items-center gap-2 min-w-0">
                        <i class="fas ${icon} text-gray-400"></i>
                        <span class="truncate text-gray-700">${file.name}</span>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-gray-400">${size}</span>
                        <button type="button" onclick="removeFile(${i})"
                                class="text-red-400 hover:text-red-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>`);
        });
    }

    window.removeFile = function (index) {
        const dt = new DataTransfer();
        Array.from(fileInput.files).forEach((f, i) => {
            if (i !== index) dt.items.add(f);
        });
        fileInput.files = dt.files;
        updateFileList(fileInput.files);
    };

    function formatSize(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B','KB','MB','GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    function getFileIcon(name) {
        const ext = name.split('.').pop().toLowerCase();
        const map = {
            pdf: 'fa-file-pdf',
            doc: 'fa-file-word', docx: 'fa-file-word',
            xls: 'fa-file-excel', xlsx: 'fa-file-excel',
            jpg: 'fa-file-image', jpeg: 'fa-file-image',
            png: 'fa-file-image', gif: 'fa-file-image',
            zip: 'fa-file-archive', rar: 'fa-file-archive',
        };
        return map[ext] || 'fa-file';
    }

    // Prevent submit if no files were selected
    form.addEventListener('submit', function (e) {
        if (!fileInput.files.length) {
            e.preventDefault();
            alert('Please select at least one file.');
        }
    });
});
</script>

@endsection