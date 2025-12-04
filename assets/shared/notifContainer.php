<div class="floating-notification">
    <div class="d-flex align-items-center gap-3 p-3">
        <div class="notif-img">
            <img src="../../assets/img/shared/logo_L.png" alt="Notification" />
        </div>
        <div class="flex-grow-1">
            <h6 class="notif-title mb-0">Electricity</h6>
            <div class="notif-due">
                <span class="due-label">Due Date:</span>
                <span class="due-date">June 07, 2025</span>
            </div>
        </div>
        <span class="notif-badge">Now</span>
    </div>
</div>

<style>
    .floating-notification {
        position: fixed;
        top: 90px;
        right: 20px;
        background: linear-gradient(135deg, #f5f7fa 0%, #e8f0ed 100%);
        border-radius: 20px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        z-index: 1;
        max-width: 350px;
        animation: slideInRight 0.4s ease-out;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .notif-img {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    .notif-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .notif-title {
        color: #000000ff;
        font-weight: 600;
        font-size: 1rem;
        line-height: 1.2;
    }

    .notif-due {
        font-size: 0.85rem;
        margin-top: 2px;
    }

    .due-label {
        color: #ffc107;
        font-weight: 500;
    }

    .due-date {
        color: #333;
        font-weight: 500;
        margin-left: 4px;
    }

    .notif-badge {
        background: #44B87D;
        color: white;
        padding: 6px 14px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        white-space: nowrap;
        flex-shrink: 0;
    }

    @media (max-width: 768px) {
        .floating-notification {
            right: 15px;
            top: 80px;
            max-width: 320px;
        }
    }

    @media (max-width: 576px) {
        .floating-notification {
            right: 10px;
            left: 10px;
            max-width: none;
        }

        .notif-title {
            font-size: 0.9rem;
        }

        .notif-due {
            font-size: 0.8rem;
        }
    }
</style>