<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/LeaveLimit.php';

$leave_limit_details = new LeaveLimit();

$leave_limit_data = $leave_limit_details->getAllRemainingLeave($userId);



// print_r($leave_limit_data);
// die();

if (!isset($permission)) dd('Access Denied...!');
?>

<div class="container flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card p-4">
                <form id="leave-request-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="request_leave" />

                    <h2 class="fw-bold mb-4 text-center">Submit Leave Request</h2>
                    <p class="text-center mb-4">From sick days to personal time, we're here to make your leave easy!</p>

                    <div class="row gy-3">
                        <div class="col-md-6 mb-3">
                            <label for="reason_type" class="form-label d-block text-center">Leave Type</label>
                            <select id="reason_type" name="reason_type" class="form-select" required>
                                <option value="" disabled selected>-- Select Leave Type --</option>
                                <option value="annual">
                                    Annual - <?= $leave_limit_data[0]['annual_balance'] ?> days available
                                </option>
                                <option value="casual">
                                    Casual - <?= $leave_limit_data[0]['casual_balance'] ?> days available
                                </option>
                                <option value="medical">
                                    Medical - <?= $leave_limit_data[0]['medical_balance'] ?> days available
                                    <!-- </option>
                                <option value="other">Other</option> -->
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="leave_note" class="form-label d-block text-center">Reason for Leave</label>
                            <input type="text" id="leave_note" name="leave_note" class="form-control" placeholder="Leave Note">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="leave_duration" class="form-label d-block text-center">Duration</label>
                            <select id="leave_duration" name="leave_duration" class="form-select" required>
                                <option value="full" selected>Full Day</option>
                                <option value="multi">Multi Days</option>
                                <option value="half">Half Day</option>
                                <option value="short">Short Leave</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3" id="date_off_div">
                            <label for="date_off" class="form-label d-block text-center">Leave Date</label>
                            <input type="date" id="date_off" name="date_off" class="form-control" required />
                        </div>

                        <!-- Half Day Options -->
                        <div class="col-12 mb-3" id="half_day_off" style="display:none;">
                            <label class="form-label text-center d-block mb-2">Choose Half Day</label>
                            <div class="d-flex justify-content-center flex-wrap" style="gap: 15px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="half_day" id="first_half" value="first">
                                    <label class="form-check-label" for="first_half">Morning</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="half_day" id="second_half" value="second">
                                    <label class="form-check-label" for="second_half">Afternoon</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-3" id="multi_day_off" style="display:none;">
                            <label class="form-label text-center d-block mb-2">Select Date Range</label>
                            <div class="row justify-content-center">
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label d-block text-center">Start Date</label>
                                    <input type="date" id="start_date" name="start_date" class="form-control" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label d-block text-center">End Date</label>
                                    <input type="date" id="end_date" name="end_date" class="form-control" />
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label d-block text-center">Explanation / Description</label>
                            <textarea id="description" name="description" rows="4" class="form-control" placeholder="Provide details or additional info here..." required></textarea>
                        </div>

                        <input type="hidden" name="user_id" value="<?= $userId ?>">
                    </div>


                    <!-- Submit -->
                    <div class="mt-4 text-center">
                        <button type="submit" class="btn  sub-leave-req" style="<?= $styleMap['imara-button-purple'] ?>">Submit Request</button>
                    </div>
                </form>


                <!-- Success Message -->
                <div id="leave-success-message" style="display:none; text-align:center; padding:20px;">
                    <h3 class="text-success">Success!</h3>
                    <h3 class="text-failed" style="display:none; color:red;">Failed!</h3>
                    <p id="success-text"></p>
                    <button id="request-again" class="btn btn-outline-primary mt-3">Request Again</button>
                </div>
            </div>
        </div>
        <?php
        $leave_data = $leave_limit_data[0] ?? [];
        $total_leave =
            ($leave_data['annual_balance'] ?? 0) +
            ($leave_data['casual_balance'] ?? 0) +
            ($leave_data['medical_balance'] ?? 0);

        $extra_leave = 0;

        if (($leave_data['annual_status'] ?? '') === 'overused') {
            $extra_leave += ($leave_data['annual_extra'] ?? 0);
        }
        if (($leave_data['casual_status'] ?? '') === 'overused') {
            $extra_leave += ($leave_data['casual_extra'] ?? 0);
        }
        if (($leave_data['medical_status'] ?? '') === 'overused') {
            $extra_leave += ($leave_data['medical_extra'] ?? 0);
        }
        $div_class = $total_leave < 10
            ? 'text-center mb-4 mt-3 text-warning'
            : 'text-center mb-4 mt-3';
        ?>
        <?php if ($total_leave > 0 || $extra_leave > 0): ?>
            <div class="<?= htmlspecialchars($div_class) ?>" id="leave-limit-show">
                <?php if ($total_leave > 0): ?>
                    <b>Leave Requests Remaining:</b> <?= $total_leave ?><br>
                <?php endif; ?>
                <?php if ($extra_leave > 0): ?>
                    <span class="text-danger"><b>Extra Leave Taken:</b> <?= $extra_leave ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>


<?php require_once('../layouts/footer.php'); ?>
<script src="<?= asset('assets/forms-js/leave-request.js') ?>"></script>