<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <style>
    .container {
      width: 250px;
    }
    .row {
      margin-top: 3px;
    }
    .col {
      margin-top: 0px;
    }
    #display {
      width: 213px;
      font-size: 20px;
    }
    .btn {
      width: 50px;
      height: 50px;
      text-align: center;
      line-height: 35px;
      font-size: 20px;
    }
  </style>
  <title>簡易計算機</title>
</head>

<?php
session_start();

// data
$OPERATOR = [
  'addition' => '+',
  'subtraction' => '-',
  'multiplication' => '*',
  'division' => '/'
];
$STATUS = [
  'waitForFirstNumberComplete' => 0,
  'waitForSecondNumberComplete' => 1,
  'afterEquality' => 2
];
$displayNumber = null;
$errorMessage = null;

if (!isset($_SESSION['numericInput'])) {
  $_SESSION['numericInput'] = ['', ''];
}

if (!isset($_SESSION['operatorInput'])) {
  $_SESSION['operatorInput'] = '';
}

if (!isset($_SESSION['calculatedResult'])) {
  $_SESSION['calculatedResult'] = null;
}

if (!isset($_SESSION['currentStatus'])) {
  $_SESSION['currentStatus'] = $STATUS['waitForFirstNumberComplete'];
}


// controller
function isNumberValid($numericStr) {
  global $errorMessage;

  if (!is_numeric($numericStr) && $numericStr !== '') {
    $errorMessage = 'Please enter a number.';
    return false;
  }

  $num = floatval($numericStr);
  $max = 9999999999.0;
  $min = -999999999.0;
  if ($num > $max) {
    $errorMessage = 'Number is too big. Should smaller than '.$max;
    return false;
  }

  if ($num < $min) {
    $errorMessage = 'Number is too small. Should bigger than '.$min;
    return false;
  }

  return true;
}

function calculator($numA, $numB, $operator) {
  global $errorMessage;

  switch ($operator) {
    case '+':
      $result = $numA + $numB;
      break;
    case '-':
      $result = $numA - $numB;
      break;
    case '*':
      $result = $numA * $numB;
      break;
    default:
      if ($numB != 0) {
        $result = $numA / $numB;
      } else {
        $result = 0;
        $errorMessage = 'Division by zero.';
      }
  }
  return $result;
}

function getNumericA() {
  return $_SESSION['numericInput']['0'];
}
function updateNumericA($string) {
    $_SESSION['numericInput']['0'] = $string;
}

function getNumericB() {
  return $_SESSION['numericInput']['1'];
}
function updateNumericB($string) {
    $_SESSION['numericInput']['1'] = $string;
}

function getOperator() {
  return $_SESSION['operatorInput'];
}
function updateOperator($string) {
  $_SESSION['operatorInput'] = $string;
}

function getStatus() {
  return $_SESSION['currentStatus'];
}
function updateStatus($integer) {
  $_SESSION['currentStatus'] = $integer;
}

function getResult() {
  return $_SESSION['calculatedResult'];
}
function updateResult($value) {
  $_SESSION['calculatedResult'] = $value;
}

function controller() {
  if (!isset($_POST['options'])) {
    return null;
  }

  global $STATUS;
  global $OPERATOR;
  global $displayNumber;
  global $errorMessage;
  $equality_sign = '=';
  $userInput = $_POST['options'];

  if ($userInput === 'reset') {
    $isSuccess = session_destroy();
    if (!$isSuccess) {
      $errorMessage = 'Something wrong, please click the "C" button again.';
    }
    return null;
  }

  // Status: Wait For First Number Complete
  if (getStatus() === $STATUS['waitForFirstNumberComplete']) {
    if (is_numeric($userInput)) {
      updateNumericA(getNumericA() . $userInput);
      $displayNumber = getNumericA();
      return null;
    }

    if(!isNumberValid(getNumericA())) {
      updateNumericA('');
      $displayNumber = 0;
      return null;
    }

    
    if ($userInput === $equality_sign) {
      $numA = floatval(getNumericA());
      updateResult($numA);
      updateNumericA('');
      updateStatus($STATUS['afterEquality']);
      $displayNumber = getResult();
      return null;
    }

    foreach($OPERATOR as $operator) {
      if ($userInput === $operator) {
        updateOperator($operator);
        updateStatus($STATUS['waitForSecondNumberComplete']);
        $displayNumber = getNumericA();
        return null;
      }
    }
    return null;
  }

  // Status: Wait For Second Number Complete
  if (getStatus() === $STATUS['waitForSecondNumberComplete']) {
    if (is_numeric($userInput)) {
      updateNumericB(getNumericB() . $userInput);
      $displayNumber = getNumericB();
      return null;
    }

    if (!isNumberValid(getNumericB())) {
      updateNumericB('');
      $displayNumber = 0;
      return null;
    }

    $numA = floatval(getNumericA());
    $numB = floatval(getNumericB());
    $result = calculator($numA, $numB, getOperator());
    updateResult($result);
    updateNumericB('');
    if ($userInput === $equality_sign) {
      updateNumericA('');
      updateOperator('');
      updateStatus($STATUS['afterEquality']);
      $displayNumber = $result;
    }

    foreach($OPERATOR as $operator) {
      if ($userInput === $operator) {
        updateNumericA(strval($result));
        updateOperator($operator);
        updateStatus($STATUS['waitForSecondNumberComplete']);
        $displayNumber = getNumericA();
        return null;
      }
    }

    return null;
  }

  // Status: After Equality
  if (getStatus() === $STATUS['afterEquality']) {
    if (is_numeric($userInput)) {
      updateResult('');
      updateNumericA(getNumericA() . $userInput);
      updateStatus($STATUS['waitForFirstNumberComplete']);
      $displayNumber = getNumericA();
      return null;
    }

    if ($userInput === $equality_sign) {
      $displayNumber = getResult();
      return null;
    }

    updateNumericA(floatval(getResult()));
    updateResult('');
    if (!isNumberValid(getNumericA())) {
      updateNumericA('');
      $displayNumber = 0;
      return null;
    }

    foreach($OPERATOR as $operator) {
      if ($userInput === $operator) {
        updateOperator($operator);
        updateStatus($STATUS['waitForSecondNumberComplete']);
        $displayNumber = getNumericA();
        return null;
      }
    }

    return null;
  }


  $errorMessage = 'Something wrong, please click "C" button to reset settings';

  return null;
}
controller();
?>

<body>
  <nav class="navbar navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="/index.php">簡易計算機</a>
    </div>
  </nav>

  <?php if($errorMessage != null) : ?>
    <div class="alert alert-warning" role="alert">
      <?php echo $errorMessage ?>
    </div>
  <?php endif; ?>
  <div class="alert alert-dark" role="alert">
    <?php
      echo 'Current formula: '.getNumericA().getOperator().getNumericB();
    ?>
  </div>

  <form class="container mt-3" method="post" action="index.php">
    <div class="row g-3">
      <div class="col">
        <input type="text" name="display" id="display" 
        value="<?php  echo $displayNumber; ?>" readonly>
      </div>
    </div>

    <div class="row g-3">
      <div class="col">
        <input type="radio" class="btn-check" name="options" id="seven" value="7" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-secondary" for="seven">7</label>

        <input type="radio" class="btn-check" name="options" id="eight" value="8" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-secondary" for="eight">8</label>

        <input type="radio" class="btn-check" name="options" id="nine" value="9" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-secondary" for="nine">9</label>

        <input type="radio" class="btn-check" name="options" id="division" value="/" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-warning" for="division">/</label>
      </div>
    </div>

    <div class="row g-3">
      <div class="col">
        <input type="radio" class="btn-check" name="options" id="four" value="4" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-secondary" for="four">4</label>

        <input type="radio" class="btn-check" name="options" id="five" value="5" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-secondary" for="five">5</label>

        <input type="radio" class="btn-check" name="options" id="six" value="6" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-secondary" for="six">6</label>

        <input type="radio" class="btn-check" name="options" id="multiplication" value="*" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-warning" for="multiplication">*</label>
      </div>
    </div>

    <div class="row g-3">
      <div class="col">
        <input type="radio" class="btn-check" name="options" id="one" value="1" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-secondary" for="one">1</label>

        <input type="radio" class="btn-check" name="options" id="two" value="2" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-secondary" for="two">2</label>

        <input type="radio" class="btn-check" name="options" id="three" value="3" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-secondary" for="three">3</label>

        <input type="radio" class="btn-check" name="options" id="subtraction" value="-" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-warning" for="subtraction">-</label>
      </div>
    </div>

    <div class="row g-3">
      <div class="col">
        <input type="radio" class="btn-check" name="options" id="reset" value="reset" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-danger" for="reset">C</label>

        <input type="radio" class="btn-check" name="options" id="zero" value="0" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-secondary" for="zero">0</label>

        <input type="radio" class="btn-check" name="options" id="equality" value="=" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-warning" for="equality">=</label>

        <input type="radio" class="btn-check" name="options" id="addition" value="+" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-warning" for="addition">+</label>
      </div>
    </div>
  </form>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>
