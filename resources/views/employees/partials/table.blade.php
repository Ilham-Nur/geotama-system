<div class="table-wrapper table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th width="60">
                    <h6>No</h6>
                </th>
                <th>
                    <h6>Kode</h6>
                </th>
                <th>
                    <h6>Nama</h6>
                </th>
                <th>
                    <h6>Posisi</h6>
                </th>
                <th>
                    <h6>Status</h6>
                </th>
                <th>
                    <h6>Akun Sistem</h6>
                </th>
                <th>
                    <h6>Aksi</h6>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $index => $employee)
                <tr>
                    <td>{{ $employees->firstItem() + $index }}</td>
                    <td>{{ $employee->employee_code }}</td>
                    <td>{{ $employee->full_name }}</td>
                    <td>{{ $employee->position ?? '-' }}</td>
                    <td><span class="badge bg-secondary text-uppercase">{{ $employee->employment_status }}</span></td>
                    <td>
                        @if ($employee->user)
                            <span class="badge bg-success">Terhubung</span>
                            <small class="d-block">{{ $employee->user->username }}</small>
                        @else
                            <span class="badge bg-light text-dark">Belum Ada Akun</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2 align-items-center">
                            <button type="button"
                                class="text-info text-white btn-employee-detail border-0 bg-transparent p-0"
                                title="Detail" data-id="{{ $employee->id }}">
                                <i class="lni lni-eye"></i>
                            </button>

                            @can('employees.edit')
                                <button type="button"
                                    class="text-primary btn-generate-contract border-0 bg-transparent p-0"
                                    title="Generate Kontrak" data-id="{{ $employee->id }}"
                                    data-name="{{ $employee->full_name }}" data-status="{{ $employee->employment_status }}">
                                    <i class="lni lni-files"></i>
                                </button>
                            @endcan

                            @can('employees.edit')
                                <a href="{{ route('employees.edit', $employee->id) }}" class="text-warning" title="Edit">
                                    <i class="lni lni-pencil"></i>
                                </a>
                            @endcan

                            @can('employees.delete')
                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="text-danger btn-delete-employee border-0 bg-transparent p-0" title="Hapus">
                                        <i class="lni lni-trash-can"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Belum ada data karyawan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $employees->links() }}
</div>
