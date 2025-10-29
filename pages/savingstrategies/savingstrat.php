<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CtrlSave | Saving Strategies</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">

  <!-- External CSS -->
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
  <link rel="stylesheet" href="../../assets/css/savingStrategies.css">

  <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
</head>

<body>
  <?php include("../../assets/shared/navigationBar.php"); ?>
  <?php include("../../assets/shared/sideBar.php"); ?>

  <!-- Content Wrapper -->
  <div class="bg-green-custom d-flex position-relative">
    <div id="overlay" class="d-none"></div>

    <div class="flex-grow-1 p-4 content-scroll">
      <!-- Fixed Header -->
      <header>
        <h1>Smart Saving Strategies</h1>
        <p class="tagline">Watch. Read. Apply. Save Smart.</p>
      </header>

      <!-- Main Scrollable Content -->
      <div class="container main-content">
        <div class="section">
          <h2>Watch: Learn the Basics of Saving</h2>

          <div class="video-container">
            <iframe src="https://www.youtube.com/embed/beJeJFHxnDI?si=wdUnNRUMfHUR8C9r" allowfullscreen></iframe>
          </div>
          <h5 class="video-caption">Ultimate Guide to Save Money on a Tight Budget</h5>

          <div class="video-container">
            <iframe src="https://www.youtube.com/embed/HyMQpsGsmwg?si=0khMCjlT3b_Fr-2k" allowfullscreen></iframe>
          </div>
          <h5 class="video-caption">The Best Way to Save Money and Invest</h5>

          <div class="video-container">
            <iframe src="https://www.youtube.com/embed/_jrUzpd-WPg?si=gyID3Vz-Gj_9218F" allowfullscreen></iframe>
          </div>
          <h5 class="video-caption">Guide to SAVINGS for BEGINNERS (what, why, how)</h5>
        </div>

        <div class="section">
          <h2>Read: Resources to Save Smarter</h2>
          <div class="resources">
            <div class="resource-card">
              <a href="https://www.consumer.gov/sites/www.consumer.gov/files/pdf-1020-make-budget-worksheet_form.pdf" target="_blank">
                Budgeting Guide (PDF)
              </a>
              <p>A step-by-step guide to creating a personal budget to save more effectively.</p>
            </div>
            <div class="resource-card">
              <a href="https://www.nerdwallet.com/article/finance/how-to-save-money" target="_blank">
                How to Save Money: 17 Proven Ways
              </a>
              <p>Practical tips from NerdWallet to cut expenses and boost savings.</p>
            </div>
            <div class="resource-card">
              <a href="https://www.ramseysolutions.com/saving/how-to-save-money" target="_blank">
                20 Simple Ways to Save Money
              </a>
              <p>Ramsey Solutions offers actionable strategies for everyday savings.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
