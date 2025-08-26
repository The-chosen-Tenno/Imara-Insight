<?php
require_once('../layouts/header.php');

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
                                <option value="sick">Sick Leave</option>
                                <option value="personal">Personal Leave</option>
                                <option value="vacation">Vacation</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="leave_note" class="form-label d-block text-center">Reason for Leave</label>
                            <input type="text" id="leave_note" name="leave_note" class="form-control" placeholder="Leave Note">
                        </div>

                        <!-- Leave Date + Duration side by side -->
                        <div class="col-md-6 mb-3">
                            <label for="date_off" class="form-label d-block text-center">Leave Date</label>
                            <input type="date" id="date_off" name="date_off" class="form-control" required />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="leave_duration" class="form-label d-block text-center">Duration</label>
                            <select id="leave_duration" name="leave_duration" class="form-select" required>
                                <option value="full" selected>Full Day</option>
                                <option value="half">Half Day</option>
                            </select>
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

                        <!-- Description -->
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label d-block text-center">Explanation / Description</label>
                            <textarea id="description" name="description" rows="4" class="form-control" placeholder="Provide details or additional info here..." required></textarea>
                        </div>

                        <input type="hidden" name="user_id" value="<?= $userId ?>">
                    </div>


                    <!-- Submit -->
                    <div class="mt-4 text-center">
                        <button type="submit" class="btn btn-primary sub-leave-req">Submit Request</button>
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
    </div>
</div>


<?php require_once('../layouts/footer.php'); ?>
<script src="<?= asset('assets/forms-js/leave-request.js') ?>"></script>