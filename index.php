<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <title>簡易計算機</title>
</head>

<?php
  $OPERATOR = [
  'addition' => '+',
  'subtraction' => '-',
  'multiplication' => '*',
  'division' => '/'
];
$EQUALITY = '=';

function calculator($numA, $numB, $operator) {
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
        $result = 'Division by zero';
      }
  }
  return $result;
}

function inputParser()
{
  global $OPERATOR;

  $userInput = trim($_POST['display']);
  $numbers = null;
  $operator = null;

  foreach ($OPERATOR as $operator_sign) {
    if (strpos($userInput, $operator_sign) != false) {
      $numbers = explode($operator_sign, $userInput);
      $operator = $operator_sign;
      break;
    }
  }
  unset($operator_sign);

  return [
    intval($numbers['0']), 
    intval($numbers['1']),
    $operator
  ];
}

function equalityHandler() {
  global $EQUALITY;

  if (!isset($_POST['options'])) {
    return null;
  }

  if ($_POST['options'] !== $EQUALITY) {
    return null;
  }

  if (!isset($_POST['display'])) {
    return null;
  }

  [$numA, $numB, $operator] = inputParser();
  return calculator($numA, $numB, $operator);
}
$result = equalityHandler();
?>

<body>
  <nav class="navbar navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="/index.php">簡易計算機</a>
    </div>
  </nav>
  <form class="container" method="post" action="index.php">
    <div class="row g-3">
      <div class="col">
        <input 
          type="text" 
          name="display" 
          id="display" 
          value="<?php
                  if ($result != null) {
                    echo $result;
                  } elseif (isset($_POST['display'])) {
                    echo trim($_POST['display'] . $_POST['options']);
                  }
                ?>" 
        readonly>
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
        <input type="radio" class="btn-check" name="options" id="zero" value="0" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-secondary" for="zero">0</label>

        <input type="radio" class="btn-check" name="options" id="equality" value="=" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-warning" for="equality">=</label>

        <input type="radio" class="btn-check" name="options" id="addition" value="+" autocomplete="off" onchange="this.form.submit();">
        <label class="btn btn-warning" for="addition">+</label>
      </div>
    </div>

    <div class="row g-3">
      <div class="col">
        <a class="btn btn-danger" href="/index.php">重新填寫</a>
      </div>
    </div>
  </form>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>
