<?php
require_once('../layouts/header.php');

if (!isset($permission)) dd('Access Denied...!');
?>

<div class="container-xxl flex-grow-1 container-p-y d-flex justify-content-center">
    <form id="leave-request-form" action="<?= url('services/ajax_functions.php') ?>" method="POST" enctype="multipart/form-data" class="card p-4" style="max-width: 800px; width: 100%;">
        <input type="hidden" name="action" value="request_leave" />

        <h2 class="fw-bold mb-4 text-center">Submit Leave Request</h2>
        <p class="text-center mb-4">From sick days to personal time, we're here to make your leave easy!</p>

        <div class="mb-3">
            <label for="request_type" class="form-label d-block text-center">Choose a request Reason</label>
            <select id="request_type" name="request_type" class="form-select" required>
                <option value="" disabled selected>-- Select Leave Type --</option>
                <option value="sick">Sick Leave</option>
                <option value="personal">Personal Leave</option>
                <option value="vacation">Vacation</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="mb-3" id="other_reason_div" style="display:none;">
            <label for="other_reason" class="form-label d-block text-center">Specify Other Reason</label>
            <input type="text" id="other_reason" name="other_reason" class="form-control" placeholder="Type your reason here">
        </div>
        <div class="mb-3">
            <label for="date_off" class="form-label d-block text-center">Date(s) Off</label>
            <input type="date" id="date_off" name="date_off" class="form-control" required />
        </div>

        <div class="mb-3">
            <label for="reason" class="form-label d-block text-center">Explanation / Description</label>
            <textarea id="reason" name="reason" rows="5" class="form-control" placeholder="Provide details or additional info here..." required></textarea>
        </div>
        <input type="hidden" name="user_id" value="<?= $userId?>" >
        <button type="submit" class="btn btn-primary d-block mx-auto sub-leave-req">Submit Request</button>
    </form>
</div>
<script src="<?= asset('assets/forms-js/leave.js') ?>"></script>
<script>
    const requestType = document.getElementById('request_type');
    const otherDiv = document.getElementById('other_reason_div');
    
    requestType.addEventListener('change', () => {
        if(requestType.value === 'other') {
            otherDiv.style.display = 'block';
            document.getElementById('other_reason').setAttribute('required', 'required');
        } else {
            otherDiv.style.display = 'none';
            document.getElementById('other_reason').removeAttribute('required');
        }
    });
</script>
<?php
require_once('../layouts/footer.php');
?>