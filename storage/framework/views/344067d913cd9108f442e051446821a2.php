<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IBEX Inventory Report</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1a1a2e; font-size: 12px; background: #fff; }
        .header { background: #0d1117; color: #fff; padding: 24px 32px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 22px; font-weight: 800; letter-spacing: 2px; }
        .header p { font-size: 11px; color: #7d8590; margin-top: 4px; }
        .accent { color: #f97316; }
        .content { padding: 24px 32px; }
        .stats-row { display: flex; gap: 12px; margin-bottom: 20px; }
        .stat-box { flex: 1; background: #f5f5f5; border-radius: 8px; padding: 12px; text-align: center; }
        .stat-box .val { font-size: 24px; font-weight: 800; }
        .stat-box .lbl { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #666; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead th { background: #0d1117; color: #e6edf3; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #eee; }
        tbody tr:nth-child(even) td { background: #f9f9f9; }
        .status-chip { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 600; }
        .s-available { background: #e6f9ec; color: #2d7a3a; }
        .s-deployed  { background: #fff9e6; color: #8a6a00; }
        .s-defective { background: #fde8e8; color: #c13232; }
        .s-spare     { background: #f0e9ff; color: #6a37c0; }
        .footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid #eee; display: flex; justify-content: space-between; color: #999; font-size: 10px; }
        @media print {
            .header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            thead th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .btn-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="header">
    <div>
        <h1>📦 <span class="accent">IBEX</span> IMS</h1>
        <p>Inventory Management System — Asset Report</p>
    </div>
    <div style="text-align: right; font-size: 11px; color: #7d8590;">
        <div>Generated: <?php echo e(now()->format('F d, Y h:i A')); ?></div>
        <?php if($dateFrom || $dateTo): ?>
        <div>Period: <?php echo e($dateFrom ?: '—'); ?> to <?php echo e($dateTo ?: 'Present'); ?></div>
        <?php endif; ?>
    </div>
</div>

<div class="content">

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-box">
            <div class="val" style="color:#1a73e8"><?php echo e($summary['total']); ?></div>
            <div class="lbl">Total Assets</div>
        </div>
        <div class="stat-box">
            <div class="val" style="color:#2d7a3a"><?php echo e($summary['available']); ?></div>
            <div class="lbl">Available</div>
        </div>
        <div class="stat-box">
            <div class="val" style="color:#8a6a00"><?php echo e($summary['deployed']); ?></div>
            <div class="lbl">Deployed</div>
        </div>
        <div class="stat-box">
            <div class="val" style="color:#c13232"><?php echo e($summary['defective']); ?></div>
            <div class="lbl">Defective</div>
        </div>
        <div class="stat-box">
            <div class="val" style="color:#6a37c0"><?php echo e($summary['spare']); ?></div>
            <div class="lbl">Spare</div>
        </div>
    </div>

    <div style="margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size: 14px; font-weight: 700;">Asset Inventory List</h2>
        <button class="btn-print" onclick="window.print()"
            style="background:#f97316;color:#fff;border:none;padding:6px 16px;border-radius:6px;cursor:pointer;font-size:12px;">
            🖨️ Print / Save PDF
        </button>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Serial Number</th>
                <th>Asset Tag</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Status</th>
                <th>Date Added</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($i + 1); ?></td>
                <td style="font-family: monospace; font-size: 11px;"><?php echo e($asset->serial_number); ?></td>
                <td><?php echo e($asset->asset_tag ?: '—'); ?></td>
                <td><?php echo e($asset->category->category_name ?? '—'); ?></td>
                <td><?php echo e($asset->brand->brand_name ?? '—'); ?></td>
                <td>
                    <span class="status-chip s-<?php echo e(strtolower($asset->status)); ?>"><?php echo e($asset->status); ?></span>
                </td>
                <td><?php echo e(\Carbon\Carbon::parse($asset->date_added)->format('M d, Y')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px; color: #999;">No records found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <span>IBEX Inventory Management System</span>
        <span>Total: <?php echo e($summary['total']); ?> assets</span>
    </div>
</div>

</body>
</html>
<?php /**PATH C:\Users\my pc\Desktop\IBEX1\ibex-ims\resources\views/reports/pdf.blade.php ENDPATH**/ ?>