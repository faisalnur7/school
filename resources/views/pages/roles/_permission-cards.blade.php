@php
    $columnKeys = array_keys($permissionColumns);
@endphp

<div class="permission-card-grid">
    @foreach ($permissionSections as $section)
        @php
            $sectionPermissionIds = collect($section['rows'])
                ->flatMap(fn ($row) => collect($row['cells'])->filter()->pluck('id'))
                ->values()
                ->all();
            $mutablePermissionIds = collect($section['rows'])
                ->flatMap(fn ($row) => collect($row['cells'])->filter(fn ($permission) => $permission && $permission->name !== 'view_dashboard')->pluck('id'))
                ->values()
                ->all();
            $selectedCount = collect($sectionPermissionIds)
                ->filter(fn ($id) => in_array($id, $selectedPermissionIds))
                ->count();
            $mutableSelectedCount = collect($mutablePermissionIds)
                ->filter(fn ($id) => in_array($id, $selectedPermissionIds))
                ->count();
            $modalId = 'permissionModal_' . $section['slug'];
            $allChecked = !empty($mutablePermissionIds) && count($mutablePermissionIds) === $mutableSelectedCount;
        @endphp

        <button type="button"
            class="permission-card-tile"
            data-toggle="modal"
            data-target="#{{ $modalId }}">
            <span class="permission-card-tile__copy">
                <span class="permission-card-tile__title">{{ $section['name'] }}</span>
                <span class="permission-card-tile__subtitle">
                    {{ count($sectionPermissionIds) }} permissions · {{ $selectedCount }} selected
                </span>
            </span>
            <span class="permission-card-tile__icon" aria-hidden="true">
                <i class="fas fa-chevron-right"></i>
            </span>
        </button>

        <div class="modal fade permission-modal" id="{{ $modalId }}" tabindex="-1" role="dialog"
            aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content permission-modal__content">
                    <div class="modal-header permission-modal__header">
                        <div>
                            <h5 class="modal-title mb-0" id="{{ $modalId }}Label">{{ $section['name'] }}</h5>
                            <small class="text-muted">Select the permissions for this card only.</small>
                        </div>
                        <div class="d-flex align-items-center ml-auto mr-3 permission-modal__header-actions">
                            <label class="permission-modal-select-all mb-0 mr-3" style="cursor:pointer;">
                                <input type="checkbox"
                                    class="permission-modal-toggle mr-1"
                                    data-category="cat_{{ $section['slug'] }}"
                                    {{ $allChecked ? 'checked' : '' }}
                                    {{ empty($mutablePermissionIds) ? 'checked disabled' : '' }}>
                                All
                            </label>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="modal-body permission-modal__body">
                        <div class="table-responsive permission-matrix-wrap">
                            <table class="table table-borderless mb-0 permission-matrix">
                                <thead>
                                    <tr>
                                        <th class="permission-matrix__module">Module</th>
                                        @foreach ($permissionColumns as $columnLabel)
                                            <th class="text-center permission-matrix__action">{{ $columnLabel }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($section['rows'] as $row)
                                        <tr>
                                            <td class="permission-row-label">{{ $row['label'] }}</td>
                                            @foreach ($columnKeys as $columnKey)
                                                @php
                                                    $permission = $row['cells'][$columnKey] ?? null;
                                                    $isRequiredPermission = $permission?->name === 'view_dashboard';
                                                @endphp
                                                <td class="text-center permission-cell">
                                                    @if ($permission)
                                                        <div class="d-inline-flex align-items-center justify-content-center permission-checkbox-wrap">
                                                            <input type="checkbox"
                                                                class="perm-check cat_{{ $section['slug'] }} permission-checkbox"
                                                                id="perm_{{ $permission->id }}"
                                                                name="permissions[]"
                                                                value="{{ $permission->id }}"
                                                                data-fixed-permission="{{ $isRequiredPermission ? 'true' : 'false' }}"
                                                                {{ $isRequiredPermission ? 'checked disabled' : (in_array($permission->id, $selectedPermissionIds) ? 'checked' : '') }}>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer permission-modal__footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
    (function () {
        function syncPermissionModalToggle($modal) {
            const $toggle = $modal.find('.permission-modal-toggle');
            if (!$toggle.length) {
                return;
            }

            const category = $toggle.data('category');
            const $mutableChecks = $modal.find('.perm-check.' + category + ':not([data-fixed-permission="true"])');
            const total = $mutableChecks.length;
            const checked = $mutableChecks.filter(':checked').length;

            $toggle.prop('checked', total > 0 && total === checked);
        }

        function wirePermissionModal($modal) {
            const $toggle = $modal.find('.permission-modal-toggle');

            $toggle.off('.permissionModal').on('change.permissionModal', function () {
                const category = $(this).data('category');
                const isChecked = $(this).is(':checked');

                $modal.find('.perm-check.' + category + ':not([data-fixed-permission="true"])')
                    .prop('checked', isChecked);
            });

            $modal.find('.perm-check').off('.permissionModal').on('change.permissionModal', function () {
                syncPermissionModalToggle($modal);
            });

            $modal.off('shown.bs.modal.permissionModal').on('shown.bs.modal.permissionModal', function () {
                syncPermissionModalToggle($modal);
            });

            syncPermissionModalToggle($modal);
        }

        $(function () {
            $('.permission-modal').each(function () {
                wirePermissionModal($(this));
            });
        });
    })();
</script>
