<?php
require_once('../layouts/header.php');

if (!isset($permission)) dd('Access Denied...!');
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card p-4">
                <form id="leave-request-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="request_leave" />
                    <h2 class="fw-bold mb-4 text-center">Submit Leave Request</h2>
                    <p class="text-center mb-4">From sick days to personal time, we're here to make your leave easy!</p>

                    <div class="row gy-3">
                        <div class="col-12 mb-3">
                            <label for="reason_type" class="form-label d-block text-center">Choose a request Reason</label>
                            <select id="reason_type" name="reason_type" class="form-select" required>
                                <option value="" disabled selected>-- Select Leave Type --</option>
                                <option value="sick">Sick Leave</option>
                                <option value="personal">Personal Leave</option>
                                <option value="vacation">Vacation</option>
                                <option value="half_day">Half Day</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3" id="other_reason_div" style="display:none;">
                            <label for="other_reason" class="form-label d-block text-center">Specify Other Reason</label>
                            <input type="text" id="other_reason" name="other_reason" class="form-control" placeholder="Type your reason here">
                        </div>

                        <div class="col-12 mb-3">
                            <label for="date_off" class="form-label d-block text-center">Leave Date</label>
                            <input type="date" id="date_off" name="date_off" class="form-control" required />
                        </div>

                        <div class="col-12 mb-3" id="half_day_off" style="display: none;">
                            <label class="form-label d-block text-center mb-2">Half Day Off</label>
                            <div class="d-flex justify-content-center" style="gap: 60px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="half_day" id="first_half" value="first" required>
                                    <label class="form-check-label" for="first_half">First Half (Morning)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="half_day" id="second_half" value="second" required>
                                    <label class="form-check-label" for="second_half">Second Half (Afternoon)</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-3" id="half_day_reason" style="display:none;">
                            <label for="half_day_detail" class="form-label d-block text-center">Specify Half Day Details</label>
                            <input type="text" id="half_day_detail" name="half_day_detail" class="form-control" placeholder="e.g. Morning / Afternoon">
                        </div>


                        <div class="col-12 mb-3">
                            <label for="description" class="form-label d-block text-center">Explanation / Description</label>
                            <textarea id="description" name="description" rows="5" class="form-control" placeholder="Provide details or additional info here..." required></textarea>
                        </div>

                        <input type="hidden" name="user_id" value="<?= $userId ?>">
                    </div>

                    <div class="mt-4 text-center">
                        <button type="submit" class="btn btn-primary sub-leave-req">Submit Request</button>
                    </div>
                </form>

                <div id="leave-success-message" style="display:none; text-align:center; padding:20px;">
                    <h3 class="text-success">Success!</h3>
                    <h3 class="text-failed" style="display:none; color:red;">Failed!</h3>
                    <p id="success-text"></p>
                    <button id="request-again" class="btn btn-outline-primary mt-3">Request Again</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('../layouts/footer.php'); ?>
<script src="<?= asset('assets/forms-js/leave-request.js') ?>"></script>