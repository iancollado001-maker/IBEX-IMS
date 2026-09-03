<?php if($paginator->hasPages()): ?>
<nav>
    <ul class="pagination mb-0" style="gap:4px;display:flex;list-style:none;padding:0;margin:0;">
        
        <?php if($paginator->onFirstPage()): ?>
            <li style="opacity:0.4;">
                <span class="btn-ibex btn-ibex-ghost btn-ibex-sm"><i class="bi bi-chevron-left"></i></span>
            </li>
        <?php else: ?>
            <li>
                <a href="<?php echo e($paginator->previousPageUrl()); ?>" class="btn-ibex btn-ibex-ghost btn-ibex-sm">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        <?php endif; ?>

        
        <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(is_string($element)): ?>
                <li><span class="btn-ibex btn-ibex-ghost btn-ibex-sm"><?php echo e($element); ?></span></li>
            <?php endif; ?>

            <?php if(is_array($element)): ?>
                <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($page == $paginator->currentPage()): ?>
                        <li>
                            <span class="btn-ibex btn-ibex-primary btn-ibex-sm"><?php echo e($page); ?></span>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="<?php echo e($url); ?>" class="btn-ibex btn-ibex-ghost btn-ibex-sm"><?php echo e($page); ?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <?php if($paginator->hasMorePages()): ?>
            <li>
                <a href="<?php echo e($paginator->nextPageUrl()); ?>" class="btn-ibex btn-ibex-ghost btn-ibex-sm">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        <?php else: ?>
            <li style="opacity:0.4;">
                <span class="btn-ibex btn-ibex-ghost btn-ibex-sm"><i class="bi bi-chevron-right"></i></span>
            </li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>
<?php /**PATH C:\Users\my pc\Desktop\IBEX1\ibex-ims\resources\views/components/pagination.blade.php ENDPATH**/ ?>