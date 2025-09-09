<!-- Another Example -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Savings Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../assets/imgs/ctrlsaveLogo.png">
</head>

<body>
    <!-- Nav Bar -->
    <nav class="bg-white px-4 d-flex justify-content-between align-items-center shadow" style="height: 73px;">
        <a href="saving1.php" class="text-decoration-none">
            <img src="../../assets/img/shared/backArrow.png" alt="Back" style="width: 32px;">
        </a>
        <h5 class="m-0 fw-bold text-dark"></h5>
        <button class="btn p-0" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
            <img src="../../assets/img/savings/deleteIcon.png" alt="Delete" style="width: 24px;">
        </button>
    </nav>

    <!-- Main Content -->
    <div class="bg-green-custom min-vh-100 p-3" style="background-color: #77D09A;">
        <div class="text-center mb-3">
            <h2 class="fs-5 fw-bold text-white">Car</h2>
            <p class="text-white mb-2">P 225,000 / P 225,000</p>
        </div>

        <div class="bg-white rounded-circle mx-auto mb-3 d-flex justify-content-center align-items-center position-relative"
            style="width: 140px; height: 140px;">
            <img src="../../assets/img/savings/car.png" alt="House" style="width: 100px;">
        <div class="position-absolute top-50 start-50 translate-middle fw-bold" style="color: #F6D25B;">100%</div>
        </div>

        <div class="text-center mb-4">
            <span class="badge bg-white text-success fw-semibold px-4 py-2 rounded-pill">Complete</span>
        </div>


        <!-- Transactions List -->
        <div class="bg-white rounded-4 p-3">
            <h5 class="fw-semibold mb-3">Transactions</h5>

            <div class="d-flex justify-content-between border-bottom py-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="../../assets/img/savings/edit.png" alt="Edit" style="width: 24px;"
                        data-bs-toggle="modal" data-bs-target="#editTransactionModal">
                    <small class="text-muted">May 07, 2025</small>
                </div>
                <span class="text-success fw-medium">+ P4,166</span>
            </div>

            <div class="d-flex justify-content-between border-bottom py-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="../../assets/img/savings/edit.png" alt="Edit" style="width: 24px;"
                        data-bs-toggle="modal" data-bs-target="#editTransactionModal">
                    <small class="text-muted">May 08, 2025</small>
                </div>
                <span class="text-success fw-medium">+ P2,000</span>
            </div>

            <div class="d-flex justify-content-between border-bottom py-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="../../assets/img/savings/edit.png" alt="Edit" style="width: 24px;"
                        data-bs-toggle="modal" data-bs-target="#editTransactionModal">
                    <small class="text-muted">May 09, 2025</small>
                </div>
                <span class="text-success fw-medium">+ P2,000</span>
            </div>

            <div class="d-flex justify-content-between py-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="../../assets/img/savings/edit.png" alt="Edit" style="width: 24px;"
                        data-bs-toggle="modal" data-bs-target="#editTransactionModal">
                    <small class="text-muted">May 10, 2025</small>
                </div>
                <span class="text-success fw-medium">+ P1,334</span>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editTransactionModal" tabindex="-1" aria-labelledby="editTransactionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg" style="background-color: #77D09A;">

                <!-- Modal Header -->
                <div
                    class="modal-header border-0 bg-white rounded-top-4 px-4 pt-4 pb-2 d-flex align-items-center justify-content-between">
                   <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close">
                    <img src="../../assets/img/shared/backArrow.png" alt="Back"  style="width: 24px; height: 24px;">
                    </button>
                    <h5 class="modal-title fw-bold">Edit</h5>
                    <button type="button" class="btn p-0 text-danger fs-5" aria-label="Delete">
                        <img src="../../assets/img/savings/deleteIcon.png" alt="Delete" style="width: 20px;">
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body px-4 pt-3 pb-0">
                    <!-- Saving Amount -->
                    <label class="form-label fw-semibold mb-1">Saving Amount</label>
                    <div class="input-group mb-4 rounded-pill overflow-hidden" style="background-color: #F0f1f6;">
                        <input type="number" class="form-control border-0 bg-transparent text-success fw-semibold ps-3"
                            value="4166" style="border-radius: 0;">
                        <span class="input-group-text border-0 bg-transparent text-warning fw-bold pe-3">PHP</span>
                    </div>

                    <!-- Date -->
                      <label class="form-label fw-semibold text-white">Date</label>
                    <div class="input-group mb-4 rounded-3" style="background-color: #F0f1f6;">
                        <input type="date" class="form-control border-0 bg-transparent text-success fw-semibold">
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-0 bg-white rounded-bottom-4 justify-content-center py-3">
                    <button class="btn fw-bold text-dark shadow-sm"
                        style="background-color: #F6D25B; padding: 12px 36px; border-radius: 40px;">
                        Save Changes
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4" style="background-color: #77D09A;">
                <div class="modal-header border-0 bg-white rounded-top">
                    <h5 class="modal-title fw-bold mx-auto" id="confirmDeleteModalLabel">Delete Goal</h5>
                </div>
                <div class="modal-body text-center text-white" style="font-size: 1.3rem;">
                Are you sure you want to delete this saving goal?
                </div>
                <div class="modal-footer border-0 bg-white rounded-bottom justify-content-center">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill"
                        data-bs-dismiss="modal">Cancel</button>
                    <a href="saving1.php" class="btn btn-danger px-4 rounded-pill">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</php>