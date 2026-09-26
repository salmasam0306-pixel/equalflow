@props([
    'id' => '',
    'name' => '',
    'label' => '',
    'value' => null,
    'required' => false,
    'min' => null,
    'max' => null,
    'disabledDates' => [],
    'highlightDates' => []
])

<div class="date-picker-wrapper">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-semibold text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <input type="date" 
               id="{{ $id }}" 
               name="{{ $name }}" 
               value="{{ $value ? (is_string($value) ? $value : $value->format('Y-m-d')) : '' }}"
               min="{{ $min }}"
               max="{{ $max }}"
               {{ $required ? 'required' : '' }}
               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent transition date-input"
               onchange="highlightDate(this, '{{ json_encode($highlightDates) }}')">

        <!-- Date Info -->
        <div class="mt-1 flex items-center gap-4 text-xs text-gray-400">
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 bg-indigo-100 border border-indigo-300 rounded inline-block"></span>
                <span>Selected</span>
            </span>
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 bg-green-100 border border-green-300 rounded inline-block"></span>
                <span>Available</span>
            </span>
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 bg-gray-100 border border-gray-200 rounded inline-block"></span>
                <span>Booked</span>
            </span>
        </div>
    </div>
</div>

<script>
function highlightDate(input, highlightDatesJson) {
    const highlightDates = JSON.parse(highlightDatesJson || '[]');
    const date = input.value;
    
    if (!date) return;
    
    // Check if date is in highlight list
    if (highlightDates.includes(date)) {
        input.style.borderColor = '#6366f1';
        input.style.backgroundColor = '#eef2ff';
        input.style.boxShadow = '0 0 0 3px rgba(99, 102, 241, 0.2)';
    } else {
        input.style.borderColor = '#d1d5db';
        input.style.backgroundColor = '#ffffff';
        input.style.boxShadow = 'none';
    }
}

// Auto-highlight on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.date-input').forEach(function(input) {
        const highlightData = input.getAttribute('data-highlight-dates') || '[]';
        highlightDate(input, highlightData);
    });
});
</script>