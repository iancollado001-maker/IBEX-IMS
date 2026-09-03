@extends('layouts.app')

@section('content')

<div class="ibex-content">

    <!-- ===== LEFT SIDEBAR ===== -->
    <aside class="ibex-sidebar">

        <!-- Asset Form -->
        <div class="ibex-panel ibex-form" id="assetFormPanel">
            <div class="panel-title">
                <i class="bi bi-pencil-square"></i>
                <span id="formPanelTitle">Add New Asset</span>
            </div>

            <form id="assetForm" novalidate>
                @csrf
                <input type="hidden" id="assetId" name="asset_id" value="">

                <!-- Category -->
                <div class="mb-2">
                    <label class="form-label">Category</label>
                    <div class="ibex-input-group">
                        <select class="ibex-input form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn-ibex btn-ibex-ghost btn-ibex-icon"
                            title="Manage Categories" data-bs-toggle="modal" data-bs-target="#categoryModal">
                            <i class="bi bi-gear"></i>
                        </button>
                    </div>
                </div>

                <!-- Serial Number -->
                <div class="mb-2">
                    <label class="form-label">Serial Number</label>
                    <div class="ibex-input-group">
                        <input type="text" class="ibex-input form-control" id="serial_number"
                            name="serial_number" placeholder="Enter or scan serial #" required>
                        <button type="button" class="btn-ibex btn-ibex-ghost btn-ibex-icon"
                            title="Scan Barcode" data-bs-toggle="modal" data-bs-target="#scannerModal">
                            <i class="bi bi-upc-scan"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback d-none" id="serialError" style="display:block;font-size:12px;color:var(--ibex-danger);margin-top:3px;"></div>
                </div>

                <!-- Asset Tag -->
                <div class="mb-2">
                    <label class="form-label">Asset Tag</label>
                    <input type="text" class="ibex-input form-control" id="asset_tag"
                        name="asset_tag" placeholder="e.g. IBEX-2024-001">
                </div>

                <!-- Brand -->
                <div class="mb-2">
                    <label class="form-label">Item Brand</label>
                    <div class="ibex-input-group">
                        <select class="ibex-input form-select" id="brand_id" name="brand_id" required>
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn-ibex btn-ibex-ghost btn-ibex-icon"
                            title="Manage Brands" data-bs-toggle="modal" data-bs-target="#brandModal">
                            <i class="bi bi-gear"></i>
                        </button>
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-2">
                    <label class="form-label">Status</label>
                    <select class="ibex-input form-select" id="status" name="status" required>
                        <option value="">Select Status</option>
                        <option value="Available">Available</option>
                        <option value="Deployed">Deployed</option>
                        <option value="Spare">Spare</option>
                        <option value="Defective">Defective</option>
                    </select>
                </div>

                <!-- Date Added -->
                <div class="mb-3">
                    <label class="form-label">Date Added</label>
                    <input type="date" class="ibex-input form-control" id="date_added"
                        name="date_added" value="{{ date('Y-m-d') }}" required>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button type="submit" class="btn-ibex btn-ibex-success" id="addBtn">
                        <i class="bi bi-plus-circle"></i> Add Item
                    </button>
                    <button type="button" class="btn-ibex btn-ibex-warning" id="updateBtn" style="display:none;">
                        <i class="bi bi-pencil"></i> Update
                    </button>
                    <button type="button" class="btn-ibex btn-ibex-danger" id="removeBtn" style="display:none;">
                        <i class="bi bi-trash3"></i> Remove
                    </button>
                    <button type="button" class="btn-ibex btn-ibex-ghost" id="clearBtn">
                        <i class="bi bi-x-circle"></i> Clear
                    </button>
                </div>
            </form>
        </div>

        <!-- Filters Panel -->
        <div class="ibex-panel">
            <div class="panel-title">
                <i class="bi bi-funnel"></i>
                <span>Filters</span>
                <button type="button" class="btn-ibex btn-ibex-ghost btn-ibex-sm ms-auto" id="clearFiltersBtn">
                    <i class="bi bi-x"></i> Clear
                </button>
            </div>

            <div class="mb-2">
                <label class="form-label">Category</label>
                <select class="ibex-input form-select" id="filterCategory">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->category_name }}">{{ $cat->category_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-2">
                <label class="form-label">Item Brand</label>
                <select class="ibex-input form-select" id="filterBrand">
                    <option value="">All Brands</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->brand_name }}">{{ $brand->brand_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-2">
                <label class="form-label">Status</label>
                <select class="ibex-input form-select" id="filterStatus">
                    <option value="">All Statuses</option>
                    <option value="Available">Available</option>
                    <option value="Deployed">Deployed</option>
                    <option value="Spare">Spare</option>
                    <option value="Defective">Defective</option>
                </select>
            </div>

            <div class="mb-2">
                <label class="form-label">Date From</label>
                <input type="date" class="ibex-input form-control" id="filterDateFrom">
            </div>

            <div class="mb-0">
                <label class="form-label">Date To</label>
                <input type="date" class="ibex-input form-control" id="filterDateTo">
            </div>
        </div>

    </aside>

    <!-- ===== MAIN DASHBOARD ===== -->
    <div class="ibex-dashboard">

        <!-- Top Row: Stats + Monthly Panel -->
        <div class="dashboard-top">
            <div class="stats-section">
                <div class="stats-5">
                    <div class="stat-card total">
                        <div class="stat-icon"><i class="bi bi-boxes"></i></div>
                        <div class="stat-value" id="stat-total">{{ $stats['total'] }}</div>
                        <div class="stat-label">No. of Assets</div>
                    </div>
                    <div class="stat-card defective">
                        <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
                        <div class="stat-value" id="stat-defective">{{ $stats['defective'] }}</div>
                        <div class="stat-label">Defective</div>
                    </div>
                    <div class="stat-card deployed">
                        <div class="stat-icon"><i class="bi bi-send-check"></i></div>
                        <div class="stat-value" id="stat-deployed">{{ $stats['deployed'] }}</div>
                        <div class="stat-label">Deployed</div>
                    </div>
                    <div class="stat-card available">
                        <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                        <div class="stat-value" id="stat-available">{{ $stats['available'] }}</div>
                        <div class="stat-label">Available</div>
                    </div>
                    <div class="stat-card spare">
                        <div class="stat-icon"><i class="bi bi-archive"></i></div>
                        <div class="stat-value" id="stat-spare">{{ $stats['spare'] }}</div>
                        <div class="stat-label">Spare</div>
                    </div>
                </div>
            </div>

            <!-- Monthly Panel -->
            <div class="monthly-panel">
                <div class="panel-title" style="margin-bottom:.75rem;">
                    <i class="bi bi-calendar3"></i> Monthly Summary
                </div>
                <select class="monthly-select" id="monthlyPeriod">
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="this_year">This Year</option>
                </select>
                <div class="monthly-stat added-stat">
                    <div class="monthly-stat-label">
                        <i class="bi bi-arrow-up-circle-fill"></i> Added Assets
                    </div>
                    <div class="monthly-stat-val" id="monthly-added">{{ $monthly['added'] }}</div>
                </div>
                <div class="monthly-stat removed-stat">
                    <div class="monthly-stat-label">
                        <i class="bi bi-arrow-down-circle-fill"></i> Removed Assets
                    </div>
                    <div class="monthly-stat-val" id="monthly-removed">{{ $monthly['removed'] }}</div>
                </div>
            </div>
        </div>

        <!-- Asset Table -->
        <div class="ibex-table-panel">
            <div class="table-header">
                <div class="table-title">
                    <i class="bi bi-table"></i> List of Assets
                    <span class="filter-chip ms-1" id="activeFilterBadge" style="display:none;">
                        <i class="bi bi-funnel-fill"></i> Filtered
                    </span>
                </div>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" id="assetSearch" placeholder="Search serial # or asset tag...">
                    </div>
                    <a href="{{ route('reports.index') }}" class="btn-ibex btn-ibex-ghost btn-ibex-sm">
                        <i class="bi bi-bar-chart-line"></i> Report
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table id="assetsTable" class="table table-hover mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Serial Number</th>
                            <th>Asset Tag</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Status</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $index => $asset)
                        <tr data-id="{{ $asset->id }}"
                            data-serial="{{ $asset->serial_number }}"
                            data-tag="{{ $asset->asset_tag }}"
                            data-category="{{ $asset->category_id }}"
                            data-brand="{{ $asset->brand_id }}"
                            data-status="{{ $asset->status }}"
                            data-date="{{ $asset->date_added }}">
                            <td><span class="row-number">{{ $index + 1 }}</span></td>
                            <td><span class="serial-badge">{{ $asset->serial_number }}</span></td>
                            <td>{{ $asset->asset_tag ?: '—' }}</td>
                            <td>{{ $asset->category->category_name ?? '—' }}</td>
                            <td>{{ $asset->brand->brand_name ?? '—' }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($asset->status) }}">
                                    {{ $asset->status }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($asset->date_added)->format('M d, Y') }}</td>
                            <td>
                                <button class="btn-ibex btn-ibex-ghost btn-ibex-sm editBtn"
                                    data-id="{{ $asset->id }}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>No assets yet. Add your first asset using the form on the left.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <div style="font-size:12px;color:var(--ibex-text-muted);">
                    Showing {{ $assets->count() }} of {{ $assets->total() }} assets
                </div>
                <div>{{ $assets->links('components.pagination') }}</div>
            </div>
        </div>

    </div>
</div>

<!-- ===================================================
     MODALS
==================================================== -->

<!-- ── Category Manage Modal ── -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-tag me-2"></i>Manage Categories</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Add new -->
                <div class="manage-add-row">
                    <input type="text" class="ibex-input form-control" id="newCategoryName"
                        placeholder="New category name…" style="flex:1;">
                    <button type="button" class="btn-ibex btn-ibex-primary" id="saveCategoryBtn">
                        <i class="bi bi-plus-lg"></i> Add
                    </button>
                </div>

                <!-- List -->
                <div class="manage-list" id="categoryList">
                    @forelse($categories as $cat)
                    <div class="manage-list-item" id="cat-row-{{ $cat->id }}">
                        <span class="manage-item-name">{{ $cat->category_name }}</span>
                        <button class="btn-ibex btn-ibex-danger btn-ibex-sm deleteCategoryBtn"
                            data-id="{{ $cat->id }}" data-name="{{ $cat->category_name }}"
                            title="Delete category">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                    @empty
                    <div class="manage-empty" id="categoryEmpty">No categories yet. Add one above.</div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-ibex btn-ibex-ghost" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ── Brand Manage Modal ── -->
<div class="modal fade" id="brandModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-building me-2"></i>Manage Brands</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Add new -->
                <div class="manage-add-row">
                    <input type="text" class="ibex-input form-control" id="newBrandName"
                        placeholder="New brand name…" style="flex:1;">
                    <button type="button" class="btn-ibex btn-ibex-primary" id="saveBrandBtn">
                        <i class="bi bi-plus-lg"></i> Add
                    </button>
                </div>

                <!-- List -->
                <div class="manage-list" id="brandList">
                    @forelse($brands as $brand)
                    <div class="manage-list-item" id="brand-row-{{ $brand->id }}">
                        <span class="manage-item-name">{{ $brand->brand_name }}</span>
                        <button class="btn-ibex btn-ibex-danger btn-ibex-sm deleteBrandBtn"
                            data-id="{{ $brand->id }}" data-name="{{ $brand->brand_name }}"
                            title="Delete brand">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                    @empty
                    <div class="manage-empty" id="brandEmpty">No brands yet. Add one above.</div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-ibex btn-ibex-ghost" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ── Asset Delete Confirm Modal ── -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="delete-confirm-icon">
                    <i class="bi bi-trash3-fill"></i>
                </div>
                <h5 style="font-family:var(--font-display);font-weight:700;">Remove Asset?</h5>
                <p style="font-size:13px;color:var(--ibex-text-muted);">
                    This cannot be undone. The asset record will be permanently deleted.
                </p>
                <div class="d-flex gap-2 justify-content-center mt-3">
                    <button type="button" class="btn-ibex btn-ibex-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn-ibex btn-ibex-danger" id="confirmDeleteBtn">
                        <i class="bi bi-trash3"></i> Yes, Remove
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Category / Brand delete confirm ── -->
<div class="modal fade" id="deleteLookupModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="delete-confirm-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h5 style="font-family:var(--font-display);font-weight:700;" id="deleteLookupTitle">Delete?</h5>
                <p style="font-size:13px;color:var(--ibex-text-muted);" id="deleteLookupMsg">
                    This item will be permanently removed.
                </p>
                <div class="d-flex gap-2 justify-content-center mt-3">
                    <button type="button" class="btn-ibex btn-ibex-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn-ibex btn-ibex-danger" id="confirmLookupDeleteBtn">
                        <i class="bi bi-trash3"></i> Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Barcode Scanner Modal ── -->
<div class="modal fade" id="scannerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-upc-scan me-2"></i>Barcode / QR Scanner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeScannerBtn"></button>
            </div>
            <div class="modal-body">
                <div id="qr-reader" style="width:100%;"></div>
                <div id="scan-result" class="mt-2 text-center"
                    style="font-size:13px;color:var(--ibex-text-muted);">
                    Point your camera at a barcode or QR code
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-ibex btn-ibex-ghost" data-bs-dismiss="modal" id="cancelScan">Cancel</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF          = '{{ csrf_token() }}';
const ASSETS_URL    = '{{ route("inventory.store") }}';
const UPDATE_BASE   = '{{ url("inventory") }}';
const MONTHLY_URL   = '{{ route("inventory.monthly") }}';
const CATEGORY_URL  = '{{ route("categories.store") }}';
const BRAND_URL     = '{{ route("brands.store") }}';
const CAT_DEL_BASE  = '{{ url("categories") }}';
const BRAND_DEL_BASE= '{{ url("brands") }}';
</script>
<script src="{{ asset('js/inventory.js') }}"></script>
@endpush
