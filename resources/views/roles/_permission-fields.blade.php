@php
    $selectedPermissions = old('permissions', $selectedPermissions ?? []);
@endphp

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <label class="form-label mb-1">Permissions</label>
            <small class="text-muted d-block">Centang akses yang boleh digunakan oleh role ini.</small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary" id="check-all-permissions">Pilih Semua</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="clear-all-permissions">Kosongkan</button>
        </div>
    </div>

    <div class="row g-3">
        @foreach ($permissionGroups as $module => $group)
            <div class="col-md-6">
                <div class="border rounded p-3 h-100 {{ $module === 'dashboard' ? 'border-primary bg-light' : '' }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">{{ $group['label'] }}</h6>
                        <button type="button" class="btn btn-sm btn-link p-0 check-module" data-module="{{ $module }}">
                            Pilih Modul
                        </button>
                    </div>

                    @foreach ($group['permissions'] as $permission)
                        @php($permissionId = 'permission_' . str_replace('.', '_', $permission['name']))
                        <div class="form-check mb-2">
                            <input class="form-check-input permission-checkbox permission-module-{{ $module }}"
                                type="checkbox"
                                name="permissions[]"
                                value="{{ $permission['name'] }}"
                                id="{{ $permissionId }}"
                                {{ in_array($permission['name'], $selectedPermissions, true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $permissionId }}">
                                {{ $permission['label'] }}
                                <small class="text-muted d-block">{{ $permission['name'] }}</small>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    @error('permissions')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

@push('scripts')
    <script>
        $(function() {
            $('#check-all-permissions').on('click', function() {
                $('.permission-checkbox').prop('checked', true);
            });

            $('#clear-all-permissions').on('click', function() {
                $('.permission-checkbox').prop('checked', false);
            });

            $('.check-module').on('click', function() {
                const checkboxes = $(`.permission-module-${$(this).data('module')}`);
                const shouldCheck = checkboxes.filter(':checked').length !== checkboxes.length;
                checkboxes.prop('checked', shouldCheck);
            });
        });
    </script>
@endpush
