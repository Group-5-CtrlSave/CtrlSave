<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calculator</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../../assets/img/shared/logo_s.png">
  <link rel="stylesheet" href="../../assets/css/calculator.css">
</head>
<body>

  <!-- Top Navbar -->
  <nav>
    <a href="../home/home.php" class="text-decoration-none">
      <img src="../../assets/img/shared/backArrow.png" alt="Back">
    </a>
  </nav>

  <!-- Calculator Card -->
  <div class="calculator-container">
    <div class="display" id="display">0</div>

    <div class="buttons">
      <button class="btn-calc" onclick="clearDisplay()">C</button>
      <button class="btn-calc" onclick="deleteChar()">⌫</button>
      <button class="btn-calc btn-operator" onclick="appendOperator('/')">÷</button>
      <button class="btn-calc btn-operator" onclick="appendOperator('*')">×</button>

      <button class="btn-calc" onclick="appendNumber('7')">7</button>
      <button class="btn-calc" onclick="appendNumber('8')">8</button>
      <button class="btn-calc" onclick="appendNumber('9')">9</button>
      <button class="btn-calc btn-operator" onclick="appendOperator('-')">−</button>

      <button class="btn-calc" onclick="appendNumber('4')">4</button>
      <button class="btn-calc" onclick="appendNumber('5')">5</button>
      <button class="btn-calc" onclick="appendNumber('6')">6</button>
      <button class="btn-calc btn-operator" onclick="appendOperator('+')">+</button>

      <button class="btn-calc" onclick="appendNumber('1')">1</button>
      <button class="btn-calc" onclick="appendNumber('2')">2</button>
      <button class="btn-calc" onclick="appendNumber('3')">3</button>
      <button class="btn-calc btn-operator" onclick="calculate()">=</button>

      <button class="btn-calc" onclick="appendNumber('0')" style="grid-column: span 2;">0</button>
      <button class="btn-calc" onclick="appendNumber('.')">.</button>
    </div>
  </div>

  <script>
    let display = document.getElementById('display');

    function clearDisplay() {
      display.textContent = '0';
    }

    function deleteChar() {
      display.textContent = display.textContent.slice(0, -1) || '0';
    }

    function appendNumber(num) {
      if (display.textContent === '0') {
        display.textContent = num;
      } else {
        display.textContent += num;
      }
    }

    function appendOperator(op) {
      const lastChar = display.textContent.slice(-1);
      if (!['+', '-', '*', '/'].includes(lastChar)) {
        display.textContent += op;
      }
    }

    function calculate() {
      try {
        display.textContent = eval(display.textContent);
      } catch {
        display.textContent = 'Error';
      }
    }
  </script>
</body>
</html>
