<button onclick="startCheckup(<?php echo intval($checkup['c_id']); ?>, this)"
    class="<?php echo $checkup['c_status'] === 'in_progress' ? 
        'bg-blue-600 hover:bg-blue-700' : 
        ($checkup['c_urgent'] === 'urgent' ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'); ?> 
        text-white px-4 py-2 rounded-lg transition duration-200">
    <?php if ($checkup['c_status'] === 'in_progress'): ?>
        <i class="fas fa-edit mr-2"></i>Update Status
    <?php else: ?>
        <i class="fas fa-play mr-2"></i>Start Check-up
    <?php endif; ?>
</button>