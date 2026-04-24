@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('suratTugasForm');
            if (!form) return;

            const formatRupiah = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0));
            const parseNumber = (value) => Number(String(value || '').replace(/[^\d]/g, '') || 0);

            function recalculateGrandTotal() {
                const rows = form.querySelectorAll('.biaya-row');
                let total = 0;
                rows.forEach(row => {
                    total += parseNumber(row.querySelector('.item-total-display').value);
                });
                form.querySelector('.grand-total-display').value = `Rp ${formatRupiah(total)}`;
            }

            function reindexRows() {
                form.querySelectorAll('.biaya-row').forEach((row, idx) => {
                    row.querySelector('input[name*="[deskripsi]"]').name = `items[${idx}][deskripsi]`;
                    row.querySelector('input[name*="[qty]"]').name = `items[${idx}][qty]`;
                    row.querySelector('input[name*="[total]"]').name = `items[${idx}][total]`;
                });
            }

            function bindRowEvents(row) {
                const totalDisplay = row.querySelector('.item-total-display');
                const totalHidden = row.querySelector('.item-total-hidden');

                totalDisplay.addEventListener('input', function() {
                    const numeric = parseNumber(this.value);
                    this.value = numeric ? `Rp ${formatRupiah(numeric)}` : '';
                    totalHidden.value = numeric;
                    recalculateGrandTotal();
                });

                row.querySelector('.btn-remove-row').addEventListener('click', function() {
                    const rows = form.querySelectorAll('.biaya-row');
                    if (rows.length <= 1) {
                        Swal.fire('Info', 'Minimal harus ada 1 item biaya.', 'info');
                        return;
                    }

                    row.remove();
                    reindexRows();
                    recalculateGrandTotal();
                });
            }

            function createItemRow() {
                const wrapper = form.querySelector('.biaya-wrapper');
                const idx = wrapper.querySelectorAll('.biaya-row').length;

                const html = `
                    <div class="row g-2 biaya-row mb-2">
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="items[${idx}][deskripsi]" placeholder="Deskripsi" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" name="items[${idx}][qty]" min="1" placeholder="Qty" value="1" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control item-total-display" placeholder="Total" required>
                            <input type="hidden" class="item-total-hidden" name="items[${idx}][total]" value="0">
                        </div>
                        <div class="col-md-1 d-grid">
                            <button type="button" class="btn btn-danger btn-remove-row">-</button>
                        </div>
                    </div>
                `;

                wrapper.insertAdjacentHTML('beforeend', html);
                const newRow = wrapper.querySelectorAll('.biaya-row')[idx];
                bindRowEvents(newRow);
                recalculateGrandTotal();
            }

            form.querySelector('.btn-add-row').addEventListener('click', createItemRow);
            form.querySelectorAll('.biaya-row').forEach(row => bindRowEvents(row));
            recalculateGrandTotal();

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: form.dataset.confirmTitle || 'Yakin?',
                    text: form.dataset.confirmText || 'Data akan diproses.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, lanjutkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

            @if ($errors->any())
                Swal.fire('Validasi gagal', 'Silakan cek kembali data input.', 'error');
            @endif
        });
    </script>
@endpush
