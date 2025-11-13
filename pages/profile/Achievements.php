<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievements</title>
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #44B87D;
            overflow-x: hidden;
            overflow-y: auto;
            font-family: "Roboto", sans-serif;
            margin: 0;
            padding: 0;
            max-width: 480px;
            margin: 0 auto;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }

        .page-title {
            position: sticky;
            top: 72px;
            left: 0;
            right: 0;
            max-width: 480px;
            margin: 0 auto;
            z-index: 50;
            background-color: #44B87D;
            padding: 20px 30px;
            color: #FFFFFF;
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            font-size: 28px;
        }

        .achievement-card {
            background: #F0F1F6;
            border-radius: 20px;
            padding: 20px;
            margin: 15px 20px;
            border: 2px solid #F6D25B;
        }

        .achievement-card h3 {
            margin-bottom: 15px;
            color: #000000;
            font-weight: bold;
            font-family: "Poppins", sans-serif;
            font-size: 20px;
            text-align: center;
        }

        .achievement-container {
            max-height: 300px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .achievement-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            background: #ffffff;
            padding: 15px;
            border-radius: 20px;
            color: #44B87D;
            min-height: 70px;
            font-family: "Roboto", sans-serif;
        }

        .achievement-info {
            flex: 1;
        }

        .achievement-title {
            font-weight: bold;
            font-size: 16px;
            color: #44B87D;
            margin-bottom: 3px;
        }

        .achievement-description {
            font-size: 12px;
            color: #888;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .achievement-item button.claim-btn {
            border: none;
            background-color: #ffc107;
            color: black;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
            transition: transform 0.1s ease, background-color 0.3s ease;
            font-size: 14px;
            min-width: 80px;
        }

        .achievement-item button.claim-btn:active {
            transform: scale(0.95);
        }

        .achievement-item button.claimed {
            background-color: transparent;
            color: #FFC727;
            font-weight: 600;
            border: 2px solid #FFC727;
            cursor: default;
            font-size: 14px;
            min-width: 80px;
        }

        .emoji-icon {
            font-size: 20px;
            margin-right: 10px;
        }

        .achievement-icon {
            width: 40px;
            height: 40px;
            margin-right: 5px;
            object-fit: contain;
        }

        .level-badge {
            display: inline-block;
            width: 60px;               
            height: auto;               
            object-fit: contain;        
            margin-top: 8px;
            margin-bottom: 10px;
        }

        /* Scrollbar styling */
        .achievement-container::-webkit-scrollbar {
            width: 6px;
        }

        .achievement-container::-webkit-scrollbar-track {
            background: #e0e0e0;
            border-radius: 10px;
        }

        .achievement-container::-webkit-scrollbar-thumb {
            background: #FFC727;
            border-radius: 10px;
        }
    </style>

</head>

<body>
    <!-- Navigation Bar -->
    <?php include("../../assets/shared/navigationBar.php") ?>
    <!-- Sidebar content-->
    <?php include("../../assets/shared/sideBar.php") ?>

    <!-- Page Title -->
    <div class="page-title">
        Achievements
    </div>

    <!-- Titles Section -->
    <section style="padding-bottom: 20px;">
        <div class="achievement-card">
            <h3>Titles</h3>
            <div class="achievement-container">
                <div class="achievement-item">
                    <div class="achievement-info">
                        <div class="achievement-title">Newbie Saver</div>
                        <!-- Must be Level 1-->
                        <div class="achievement-description">
                            Reach Level 1 <img class="level-badge" src="../../assets/img/challenge/newbieTitle.png" alt="Level 1 Badge">
                        </div>
                    </div>
                    <button class="claimed">Claimed</button>
                </div>

                <div class="achievement-item">
                    <div class="achievement-info">
                        <div class="achievement-title">Passionate Saver</div>
                        <!-- Must be Level 5-->
                        <div class="achievement-description">
                            Reach Level 5 <img class="level-badge" src="../../assets/img/challenge/passionateTitle.png" alt="Level 5 Badge">
                        </div>
                    </div>
                    <button class="claim-btn">Claim</button>
                </div>

                <div class="achievement-item">
                    <div class="achievement-info">
                        <div class="achievement-title">Elite Saver</div>
                        <!-- Must be Level 7-->
                        <div class="achievement-description">
                            Reach Level 7 <img class="level-badge" src="../../assets/img/challenge/eliteTitle.png" alt="Level 7 Badge">
                        </div>
                    </div>
                    <button class="claim-btn">Claim</button>
                </div>

                <div class="achievement-item">
                    <div class="achievement-info">
                        <div class="achievement-title">Veteran Saver</div>
                        <!-- Must be Level 10-->
                        <div class="achievement-description">
                            Reach Level 10 <img class="level-badge" src="../../assets/img/challenge/veteranTitle.png" alt="Level 10 Badge">
                        </div>
                    </div>
                    <button class="claim-btn">Claim</button>
                </div>
            </div>
        </div>

        <!-- Badges Section -->
        <div class="achievement-card">
            <h3>Badges</h3>
            <div class="achievement-container">
                <div class="achievement-item">
                    <img src="../../assets/img/challenge/newcomerBadge.png" alt="Newcomer" class="achievement-icon">
                    <div class="achievement-info">
                        <div class="achievement-title">Newcomer</div>
                        <!-- Must be login, this is claimable agad-->
                        <div class="achievement-description">Login to CtrlSave</div>
                    </div>
                    <button class="claimed">Claimed</button>
                </div>

                <div class="achievement-item">
                    <img src="../../assets/img/challenge/incomeproBadge.png" alt="Income" class="achievement-icon">
                    <div class="achievement-info">
                        <div class="achievement-title">Income Badge</div>
                        <!-- Must get the complete 20 income from Income and Expense page-->
                        <div class="achievement-description">Add 20 Income Transaction</div>
                    </div>
                    <button class="claim-btn">Claim</button>
                </div>

                <div class="achievement-item">
                    <img src="../../assets/img/challenge/savinggoalsBadge.png" alt="Saving" class="achievement-icon">
                    <div class="achievement-info">
                        <div class="achievement-title">Saving Badge</div>
                        <!-- Must get the completed a Saving Goal from Saving Goal page -->
                        <div class="achievement-description">Complete a Saving Goal</div>
                    </div>
                    <button class="claim-btn">Claim</button>
                </div>

                <div class="achievement-item">
                    <img src="../../assets/img/challenge/challengeproBadge.png" alt="Challenge" class="achievement-icon">
                    <div class="achievement-info">
                        <div class="achievement-title">Challenge Pro</div>
                        <!-- Must get the completed 20 challenges from Challenge page -->
                        <div class="achievement-description">Complete 0/20 Challenges</div>
                    </div>
                    <button class="claim-btn">Claim</button>
                </div>
            </div>
        </div>
    </section>

<!-- Reward Modal -->
<div class="modal fade" id="rewardModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4" style="background-color: #44B87D; border-radius: 20px;">
      <h5 class="fw-bold" style="color: #fff; font-size: 30px;">Achievement Unlocked!</h5>
      <div class="position-relative d-inline-block my-3">
        <img id="achievementImage"
             src="../../assets/img/challenge/newcomerBadge.png"
             alt="Achievement Icon"
             style="width: 150px; height: 150px; object-fit: contain; border-radius: 10px; transition: all 0.3s ease;">
      </div>
      <p style="color: white; font-size: 18px;" id="achievementName">New Achievement</p>
    </div>
  </div>
</div>

<script>
const claimButtons = document.querySelectorAll('.claim-btn');

claimButtons.forEach(button => {
  button.addEventListener('click', () => {
    const achievementItem = button.closest('.achievement-item');
    const achievementTitle = achievementItem.querySelector('.achievement-title')?.textContent || "Achievement";
    
    // Check which image exists (badge or title)
    const achievementImage = achievementItem.querySelector('.achievement-icon, .level-badge');
    const achievementIconSrc = achievementImage?.getAttribute('src') || "../../assets/img/challenge/newcomerBadge.png";
    const isLevelBadge = achievementImage?.classList.contains('level-badge');

    // Update modal with correct name and image
    const modalImage = document.getElementById('achievementImage');
    const modalTitle = document.getElementById('achievementName');
    
    modalTitle.textContent = achievementTitle;
    modalImage.src = achievementIconSrc;

    // Adjust modal image style depending on badge type
    if (isLevelBadge) {
      modalImage.style.width = "180px";
      modalImage.style.height = "auto";
      modalImage.style.borderRadius = "8px";
      modalImage.style.objectFit = "contain";
    } else {
      modalImage.style.width = "150px";
      modalImage.style.height = "150px";
      modalImage.style.borderRadius = "50%";
      modalImage.style.objectFit = "cover";
    }

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('rewardModal'));
    modal.show();

    // Confetti celebration
    launchConfetti();

    // Change button to claimed state
    button.textContent = 'Claimed';
    button.classList.remove('claim-btn');
    button.classList.add('claimed');
  });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

<script>
function launchConfetti() {
  const duration = 1500;
  const end = Date.now() + duration;

  (function frame() {
    confetti({
      particleCount: 5,
      angle: 60,
      spread: 55,
      origin: { x: 0 },
      colors: ['#FFC727', '#44B87D', '#ffffff']
    });
    confetti({
      particleCount: 5,
      angle: 120,
      spread: 55,
      origin: { x: 1 },
      colors: ['#FFC727', '#44B87D', '#ffffff']
    });

    if (Date.now() < end) requestAnimationFrame(frame);
  })();
}
</script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>