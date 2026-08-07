<tr class="group-row group-row--section">
    <td colspan="{{ count($selectedColumns) + 1 }}">
        <span class="group-pill group-pill--section">{{ __('Section') }}</span>{{ $sectionGroup['section_name'] }}
        <span class="group-meta">{{ $sectionGroup['students']->count() }} {{ __('students') }}</span>
    </td>
</tr>
