/**
 * IBEX IMS — Inventory Management JS
 */

$(function () {

    // ─── DataTable ───────────────────────────────────────────
    const table = $('#assetsTable').DataTable({
        paging:    false,
        ordering:  true,
        searching: true,
        info:      false,
        autoWidth: false,
        columnDefs: [{ orderable: false, targets: [0, 7] }]
    });

    $('#assetSearch').on('keyup', function () {
        table.search(this.value).draw();
        updateFilterBadge();
    });

    // ─── Column Filters ─────────────────────────────────────
    $.fn.dataTable.ext.search.push(function (settings, data) {
        const cat    = $('#filterCategory').val().toLowerCase();
        const brand  = $('#filterBrand').val().toLowerCase();
        const status = $('#filterStatus').val().toLowerCase();
        const from   = $('#filterDateFrom').val();
        const to     = $('#filterDateTo').val();

        if (cat    && !(data[3] || '').toLowerCase().includes(cat))    return false;
        if (brand  && !(data[4] || '').toLowerCase().includes(brand))  return false;
        if (status && !(data[5] || '').toLowerCase().includes(status)) return false;

        if (from || to) {
            const d = new Date(data[6] || '');
            if (from && d < new Date(from)) return false;
            if (to   && d > new Date(to))   return false;
        }
        return true;
    });

    $('#filterCategory, #filterBrand, #filterStatus, #filterDateFrom, #filterDateTo')
        .on('change', function () { table.draw(); updateFilterBadge(); });

    function updateFilterBadge() {
        const has = $('#filterCategory').val() || $('#filterBrand').val() ||
                    $('#filterStatus').val()   || $('#filterDateFrom').val() ||
                    $('#filterDateTo').val()   || $('#assetSearch').val();
        $('#activeFilterBadge').toggle(!!has);
    }

    $('#clearFiltersBtn').on('click', function () {
        $('#filterCategory, #filterBrand, #filterStatus, #filterDateFrom, #filterDateTo').val('');
        $('#assetSearch').val('');
        table.search('').draw();
        updateFilterBadge();
    });

    // ─── Form State ──────────────────────────────────────────
    let currentAssetId  = null;
    let pendingDeleteId = null;

    function setFormMode(mode) {
        if (mode === 'add') {
            currentAssetId = null;
            $('#assetId').val('');
            $('#formPanelTitle').text('Add New Asset');
            $('#addBtn').show();
            $('#updateBtn, #removeBtn').hide();
        } else {
            $('#formPanelTitle').text('Edit Asset');
            $('#addBtn').hide();
            $('#updateBtn, #removeBtn').show();
        }
    }

    function clearForm() {
        $('#assetForm')[0].reset();
        $('#date_added').val(todayStr());
        $('#serialError').hide().text('');
        $('#serial_number').removeClass('scanning');
        setFormMode('add');
    }

    function todayStr() {
        return new Date().toISOString().split('T')[0];
    }

    $('#clearBtn').on('click', clearForm);
    setFormMode('add');

    // ─── Edit row ────────────────────────────────────────────
    $(document).on('click', '.editBtn', function () {
        const row = $(this).closest('tr');
        currentAssetId = row.data('id');
        $('#assetId').val(currentAssetId);
        $('#serial_number').val(row.data('serial'));
        $('#asset_tag').val(row.data('tag'));
        $('#category_id').val(row.data('category'));
        $('#brand_id').val(row.data('brand'));
        $('#status').val(row.data('status'));
        $('#date_added').val(row.data('date'));
        setFormMode('edit');
        $('.ibex-sidebar').animate({ scrollTop: 0 }, 300);
    });

    // ─── Add Asset ───────────────────────────────────────────
    $('#assetForm').on('submit', function (e) {
        e.preventDefault();
        submitAsset('POST', ASSETS_URL);
    });

    // ─── Update Asset ────────────────────────────────────────
    $('#updateBtn').on('click', function () {
        if (!currentAssetId) return;
        submitAsset('PUT', `${UPDATE_BASE}/${currentAssetId}`);
    });

    function submitAsset(method, url) {
        const payload = {
            _token:        CSRF,
            _method:       method === 'PUT' ? 'PUT' : undefined,
            category_id:   $('#category_id').val(),
            serial_number: $('#serial_number').val().trim(),
            asset_tag:     $('#asset_tag').val().trim(),
            brand_id:      $('#brand_id').val(),
            status:        $('#status').val(),
            date_added:    $('#date_added').val(),
        };

        if (!payload.serial_number) {
            showFieldError('Serial number is required.');
            return;
        }

        $.ajax({
            url, method: 'POST', data: payload,
            success(res) {
                if (res.success) {
                    toastr.success(res.message);
                    clearForm();
                    setTimeout(() => location.reload(), 600);
                } else {
                    toastr.error(res.message || 'Something went wrong.');
                    if (res.errors?.serial_number) showFieldError(res.errors.serial_number[0]);
                }
            },
            error(xhr) {
                const res = xhr.responseJSON;
                if (res?.errors?.serial_number) {
                    showFieldError(res.errors.serial_number[0]);
                } else {
                    toastr.error(res?.message || 'Server error. Please try again.');
                }
            }
        });
    }

    function showFieldError(msg) {
        $('#serialError').text(msg).show();
        setTimeout(() => $('#serialError').hide(), 4000);
    }

    // ─── Remove Asset ────────────────────────────────────────
    $('#removeBtn').on('click', function () {
        if (!currentAssetId) { toastr.warning('No asset selected.'); return; }
        pendingDeleteId = currentAssetId;
        $('#deleteModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function () {
        if (!pendingDeleteId) { $('#deleteModal').modal('hide'); return; }

        const btn = $(this);
        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Removing…');

        $.ajax({
            url:    `${UPDATE_BASE}/${pendingDeleteId}`,
            method: 'POST',
            data:   { _token: CSRF, _method: 'DELETE' },
            success(res) {
                if (res.success) {
                    toastr.success(res.message || 'Asset removed.');
                    $('#deleteModal').modal('hide');
                    clearForm();
                    pendingDeleteId = null;
                    setTimeout(() => location.reload(), 700);
                } else {
                    toastr.error(res.message || 'Failed to remove asset.');
                }
            },
            error(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Server error while deleting.');
            },
            complete() {
                btn.prop('disabled', false).html('<i class="bi bi-trash3"></i> Yes, Remove');
            }
        });
    });

    $('#deleteModal').on('hidden.bs.modal', function () { pendingDeleteId = null; });

    // ─── Monthly Summary ─────────────────────────────────────
    $('#monthlyPeriod').on('change', function () {
        $.get(MONTHLY_URL, { period: $(this).val() }, function (res) {
            $('#monthly-added').text(res.added);
            $('#monthly-removed').text(res.removed);
        });
    });

    // =========================================================
    // CATEGORY MANAGEMENT
    // =========================================================

    // Add category
    $('#saveCategoryBtn').on('click', addCategory);
    $('#newCategoryName').on('keypress', e => { if (e.which === 13) addCategory(); });

    function addCategory() {
        const name = $('#newCategoryName').val().trim();
        if (!name) { toastr.warning('Enter a category name.'); return; }

        $.ajax({
            url: CATEGORY_URL, method: 'POST',
            data: { _token: CSRF, category_name: name },
            success(res) {
                if (res.success) {
                    // Add to form selects + filter
                    const opt = `<option value="${res.id}" selected>${res.name}</option>`;
                    $('#category_id').append(opt);
                    $('#filterCategory').append(`<option value="${res.name}">${res.name}</option>`);

                    // Add to manage list
                    $('#categoryEmpty').remove();
                    $('#categoryList').append(buildCatRow(res.id, res.name));

                    $('#newCategoryName').val('');
                    toastr.success('Category added!');
                }
            },
            error(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Failed to add category.');
            }
        });
    }

    function buildCatRow(id, name) {
        return `<div class="manage-list-item" id="cat-row-${id}">
            <span class="manage-item-name">${name}</span>
            <button class="btn-ibex btn-ibex-danger btn-ibex-sm deleteCategoryBtn"
                data-id="${id}" data-name="${name}" title="Delete category">
                <i class="bi bi-trash3"></i>
            </button>
        </div>`;
    }

    // Delete category — click handler (delegated for dynamic rows)
    $(document).on('click', '.deleteCategoryBtn', function () {
        const id   = $(this).data('id');
        const name = $(this).data('name');
        openLookupConfirm(
            'Delete Category?',
            `"${name}" will be removed. This cannot be undone.`,
            () => doDeleteCategory(id)
        );
    });

    function doDeleteCategory(id) {
        $.ajax({
            url:    `${CAT_DEL_BASE}/${id}`,
            method: 'POST',
            data:   { _token: CSRF, _method: 'DELETE' },
            success(res) {
                if (res.success) {
                    // Remove from selects
                    $(`#category_id option[value="${id}"]`).remove();
                    // Remove from filter (match by text)
                    const name = $(`#cat-row-${id} .manage-item-name`).text();
                    $(`#filterCategory option`).filter(function () {
                        return $(this).val() === name;
                    }).remove();
                    // Remove from list
                    $(`#cat-row-${id}`).remove();
                    if ($('#categoryList .manage-list-item').length === 0) {
                        $('#categoryList').append('<div class="manage-empty" id="categoryEmpty">No categories yet. Add one above.</div>');
                    }
                    $('#deleteLookupModal').modal('hide');
                    toastr.success('Category deleted.');
                } else {
                    toastr.error(res.message || 'Cannot delete category.');
                    $('#deleteLookupModal').modal('hide');
                }
            },
            error(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Failed to delete category.');
                $('#deleteLookupModal').modal('hide');
            }
        });
    }

    // =========================================================
    // BRAND MANAGEMENT
    // =========================================================

    // Add brand
    $('#saveBrandBtn').on('click', addBrand);
    $('#newBrandName').on('keypress', e => { if (e.which === 13) addBrand(); });

    function addBrand() {
        const name = $('#newBrandName').val().trim();
        if (!name) { toastr.warning('Enter a brand name.'); return; }

        $.ajax({
            url: BRAND_URL, method: 'POST',
            data: { _token: CSRF, brand_name: name },
            success(res) {
                if (res.success) {
                    $('#brand_id').append(`<option value="${res.id}" selected>${res.name}</option>`);
                    $('#filterBrand').append(`<option value="${res.name}">${res.name}</option>`);

                    $('#brandEmpty').remove();
                    $('#brandList').append(buildBrandRow(res.id, res.name));

                    $('#newBrandName').val('');
                    toastr.success('Brand added!');
                }
            },
            error(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Failed to add brand.');
            }
        });
    }

    function buildBrandRow(id, name) {
        return `<div class="manage-list-item" id="brand-row-${id}">
            <span class="manage-item-name">${name}</span>
            <button class="btn-ibex btn-ibex-danger btn-ibex-sm deleteBrandBtn"
                data-id="${id}" data-name="${name}" title="Delete brand">
                <i class="bi bi-trash3"></i>
            </button>
        </div>`;
    }

    // Delete brand
    $(document).on('click', '.deleteBrandBtn', function () {
        const id   = $(this).data('id');
        const name = $(this).data('name');
        openLookupConfirm(
            'Delete Brand?',
            `"${name}" will be removed. This cannot be undone.`,
            () => doDeleteBrand(id)
        );
    });

    function doDeleteBrand(id) {
        $.ajax({
            url:    `${BRAND_DEL_BASE}/${id}`,
            method: 'POST',
            data:   { _token: CSRF, _method: 'DELETE' },
            success(res) {
                if (res.success) {
                    $(`#brand_id option[value="${id}"]`).remove();
                    const name = $(`#brand-row-${id} .manage-item-name`).text();
                    $(`#filterBrand option`).filter(function () {
                        return $(this).val() === name;
                    }).remove();
                    $(`#brand-row-${id}`).remove();
                    if ($('#brandList .manage-list-item').length === 0) {
                        $('#brandList').append('<div class="manage-empty" id="brandEmpty">No brands yet. Add one above.</div>');
                    }
                    $('#deleteLookupModal').modal('hide');
                    toastr.success('Brand deleted.');
                } else {
                    toastr.error(res.message || 'Cannot delete brand.');
                    $('#deleteLookupModal').modal('hide');
                }
            },
            error(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Failed to delete brand.');
                $('#deleteLookupModal').modal('hide');
            }
        });
    }

    // ─── Shared lookup confirm modal ─────────────────────────
    let _lookupCallback = null;

    function openLookupConfirm(title, msg, callback) {
        _lookupCallback = callback;
        $('#deleteLookupTitle').text(title);
        $('#deleteLookupMsg').text(msg);
        $('#deleteLookupModal').modal('show');
    }

    $('#confirmLookupDeleteBtn').on('click', function () {
        if (typeof _lookupCallback === 'function') _lookupCallback();
        _lookupCallback = null;
    });

    $('#deleteLookupModal').on('hidden.bs.modal', function () { _lookupCallback = null; });

    // ─── USB Barcode Scanner ─────────────────────────────────
    let barcodeBuffer = '';
    let barcodeTimer  = null;

    $(document).on('keypress', function (e) {
        const tag = e.target.tagName;
        if (tag === 'INPUT' && e.target.id !== 'serial_number') return;
        if (tag === 'SELECT' || tag === 'TEXTAREA') return;

        clearTimeout(barcodeTimer);
        if (e.which === 13) {
            if (barcodeBuffer.length > 3) {
                $('#serial_number').val(barcodeBuffer).addClass('scanning');
                setTimeout(() => $('#serial_number').removeClass('scanning'), 1500);
                toastr.info('Barcode scanned: ' + barcodeBuffer);
            }
            barcodeBuffer = '';
        } else {
            barcodeBuffer += String.fromCharCode(e.which);
            barcodeTimer = setTimeout(() => { barcodeBuffer = ''; }, 100);
        }
    });

    // ─── Camera Scanner ──────────────────────────────────────
    let html5QrCode = null;

    $('#scannerModal').on('shown.bs.modal', function () {
        html5QrCode = new Html5Qrcode('qr-reader');
        html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 150 } },
            (decoded) => {
                $('#serial_number').val(decoded).addClass('scanning');
                setTimeout(() => $('#serial_number').removeClass('scanning'), 1500);
                $('#scan-result').html(
                    `<span style="color:var(--ibex-success);">
                        <i class="bi bi-check-circle-fill"></i> Scanned: <strong>${decoded}</strong>
                    </span>`
                );
                stopScanner();
                setTimeout(() => $('#scannerModal').modal('hide'), 1000);
                toastr.success('Barcode captured!');
            },
            () => {}
        ).catch(() => {
            $('#scan-result').html(
                '<span style="color:var(--ibex-danger);">Camera not accessible. Use USB scanner instead.</span>'
            );
        });
    });

    function stopScanner() {
        if (html5QrCode) { html5QrCode.stop().catch(() => {}); html5QrCode = null; }
    }

    $('#scannerModal').on('hide.bs.modal', stopScanner);
    $('#cancelScan').on('click', stopScanner);

});
