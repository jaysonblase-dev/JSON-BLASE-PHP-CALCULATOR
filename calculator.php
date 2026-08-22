<?php
/*
    ============================================
    ADVANCED PHP CALCULATOR
    XAMPP VERSION
    ============================================
*/

$result = "";
$error = "";

/*
|--------------------------------------------------------------------------
| PHP Calculator Backend
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $expression = $_POST["expression"] ?? "";

    // Remove dangerous characters.
    $expression = preg_replace('/[^0-9+\-*\/().% ]/', '', $expression);

    if (trim($expression) === "") {
        $error = "Please enter a calculation.";
    } else {

        /*
        | Convert percentage:
        | 50% -> (50/100)
        */
        $expression = preg_replace(
            '/(\d+(?:\.\d+)?)%/',
            '($1/100)',
            $expression
        );

        /*
        | Basic validation
        */
        if (preg_match('/\/\s*0(?:\.0*)?(?![0-9])/', $expression)) {
            $error = "Cannot divide by zero.";
        } else {

            /*
            | Evaluate basic arithmetic.
            | This is intentionally restricted to numbers
            | and arithmetic operators only.
            */
            try {

                $allowed = preg_match(
                    '/^[0-9+\-*\/().\s]+$/',
                    $expression
                );

                if (!$allowed) {
                    throw new Exception("Invalid expression.");
                }

                /*
                | PHP does not have a safe built-in math
                | expression parser, so this project uses
                | eval only after strict character validation.
                */
                $phpExpression = '$answer = ' . $expression . ';';

                eval($phpExpression);

                if (isset($answer)) {

                    if (is_infinite($answer) || is_nan($answer)) {
                        throw new Exception("Invalid mathematical result.");
                    }

                    $result = $answer;

                } else {
                    throw new Exception("Invalid calculation.");
                }

            } catch (Throwable $e) {

                $error = "Invalid calculation.";

            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Ultimate PHP Calculator</title>

<style>

/* =========================================================
   GLOBAL
========================================================= */

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    min-height: 100vh;

    color: white;

    background:
        radial-gradient(
            circle at top left,
            #263b70,
            transparent 35%
        ),
        radial-gradient(
            circle at bottom right,
            #5b246f,
            transparent 35%
        ),
        #080b16;

    padding: 25px;
}

/* =========================================================
   MAIN CONTAINER
========================================================= */

.container {

    max-width: 1250px;

    margin: auto;

    display: grid;

    grid-template-columns:
        1fr 340px;

    gap: 25px;
}

/* =========================================================
   CALCULATOR
========================================================= */

.calculator {

    background:
        rgba(255,255,255,0.08);

    border:
        1px solid
        rgba(255,255,255,0.15);

    backdrop-filter:
        blur(20px);

    border-radius: 25px;

    padding: 25px;

    box-shadow:
        0 25px 80px
        rgba(0,0,0,0.45);
}

/* =========================================================
   HEADER
========================================================= */

.header {

    display: flex;

    justify-content:
        space-between;

    align-items:
        center;

    margin-bottom: 20px;
}

.logo {

    font-size: 28px;

    font-weight: bold;

    background:
        linear-gradient(
            90deg,
            #00f5ff,
            #9d5cff
        );

    -webkit-background-clip:
        text;

    color: transparent;
}

.subtitle {

    color:
        #aeb7d0;

    font-size: 13px;
}

/* =========================================================
   DISPLAY
========================================================= */

.display {

    background:
        rgba(0,0,0,0.35);

    border-radius:
        18px;

    padding:
        20px;

    margin-bottom:
        15px;

    min-height:
        130px;

    display:
        flex;

    flex-direction:
        column;

    justify-content:
        flex-end;

    overflow:
        hidden;
}

.expression {

    color:
        #8e9bbd;

    font-size:
        18px;

    min-height:
        25px;

    text-align:
        right;

    word-break:
        break-all;
}

.answer {

    font-size:
        42px;

    font-weight:
        bold;

    text-align:
        right;

    margin-top:
        10px;

    color:
        #ffffff;

    word-break:
        break-all;
}

/* =========================================================
   BUTTON GRID
========================================================= */

.buttons {

    display:
        grid;

    grid-template-columns:
        repeat(5, 1fr);

    gap:
        10px;
}

button {

    border:
        none;

    border-radius:
        15px;

    padding:
        18px 8px;

    font-size:
        17px;

    font-weight:
        bold;

    color:
        white;

    cursor:
        pointer;

    background:
        rgba(255,255,255,0.09);

    transition:
        0.2s;
}

button:hover {

    transform:
        translateY(-3px);

    background:
        rgba(255,255,255,0.17);

    box-shadow:
        0 8px 20px
        rgba(0,0,0,0.25);
}

button:active {

    transform:
        scale(0.95);
}

/* Operator buttons */

.operator {

    background:
        linear-gradient(
            135deg,
            #7048ff,
            #9b3dff
        );
}

.operator:hover {

    background:
        linear-gradient(
            135deg,
            #825eff,
            #b051ff
        );
}

/* Equal */

.equal {

    background:
        linear-gradient(
            135deg,
            #00c6ff,
            #0072ff
        );

    grid-column:
        span 2;
}

.clear {

    background:
        linear-gradient(
            135deg,
            #ff416c,
            #ff4b2b
        );
}

/* Function buttons */

.function {

    background:
        rgba(255,255,255,0.13);

    color:
        #aeefff;
}

/* =========================================================
   SIDE PANEL
========================================================= */

.sidebar {

    display:
        flex;

    flex-direction:
        column;

    gap:
        20px;
}

/* =========================================================
   CARD
========================================================= */

.card {

    background:
        rgba(255,255,255,0.08);

    border:
        1px solid
        rgba(255,255,255,0.13);

    border-radius:
        20px;

    padding:
        20px;

    backdrop-filter:
        blur(15px);

    box-shadow:
        0 15px 40px
        rgba(0,0,0,0.25);
}

.card h2 {

    font-size:
        18px;

    margin-bottom:
        15px;
}

/* =========================================================
   ADVERTISEMENT
========================================================= */

.ad {

    min-height:
        250px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    text-align:
        center;

    border:
        1px dashed
        rgba(255,255,255,0.25);

    border-radius:
        15px;

    color:
        #929bb5;

    background:
        rgba(0,0,0,0.15);
}

.ad strong {

    display:
        block;

    color:
        #ffffff;

    margin-bottom:
        8px;
}

/* =========================================================
   MEME
========================================================= */

.meme {

    text-align:
        center;

    font-size:
        15px;

    line-height:
        1.5;

    color:
        #dce2f4;
}

.meme-emoji {

    font-size:
        55px;

    margin-bottom:
        10px;
}

/* =========================================================
   HISTORY
========================================================= */

.history {

    max-height:
        250px;

    overflow-y:
        auto;
}

.history-item {

    padding:
        9px;

    margin-bottom:
        7px;

    border-radius:
        10px;

    background:
        rgba(255,255,255,0.06);

    font-size:
        13px;

    color:
        #cbd3e8;
}

/* =========================================================
   FOOTER
========================================================= */

footer {

    max-width:
        1250px;

    margin:
        25px auto 0;

    text-align:
        center;

    color:
        #737d99;

    font-size:
        12px;
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 900px) {

    .container {

        grid-template-columns:
            1fr;
    }

    .buttons {

        grid-template-columns:
            repeat(4, 1fr);
    }

}

@media (max-width: 500px) {

    body {
        padding: 10px;
    }

    .calculator {
        padding: 15px;
    }

    .buttons {

        grid-template-columns:
            repeat(4, 1fr);

        gap: 7px;
    }

    button {

        padding:
            15px 5px;

        font-size:
            14px;
    }

    .answer {

        font-size:
            32px;
    }
}

</style>

</head>

<body>

<div class="container">

    <!-- =================================================
         CALCULATOR
    ================================================== -->

    <main class="calculator">

        <div class="header">

            <div>

                <div class="logo">
                    CALC<span style="color:white;">X</span>
                </div>

                <div class="subtitle">
                    Advanced PHP Calculator
                </div>

            </div>

            <div>
                🧮
            </div>

        </div>


        <!-- DISPLAY -->

        <div class="display">

            <div
                class="expression"
                id="expression">
                0
            </div>

            <div
                class="answer"
                id="display">
                <?php

                if ($error !== "") {
                    echo "ERROR";
                }
                elseif ($result !== "") {
                    echo htmlspecialchars($result);
                }
                else {
                    echo "0";
                }

                ?>
            </div>

        </div>


        <!-- BUTTONS -->

        <div class="buttons">

            <button
                class="clear"
                onclick="clearDisplay()">
                AC
            </button>

            <button
                class="function"
                onclick="backspace()">
                DEL
            </button>

            <button
                class="function"
                onclick="addValue('%')">
                %
            </button>

            <button
                class="function"
                onclick="squareRoot()">
                √
            </button>

            <button
                class="operator"
                onclick="addValue('/')">
                ÷
            </button>


            <button onclick="addValue('7')">
                7
            </button>

            <button onclick="addValue('8')">
                8
            </button>

            <button onclick="addValue('9')">
                9
            </button>

            <button
                class="function"
                onclick="squareNumber()">
                x²
            </button>

            <button
                class="operator"
                onclick="addValue('*')">
                ×
            </button>


            <button onclick="addValue('4')">
                4
            </button>

            <button onclick="addValue('5')">
                5
            </button>

            <button onclick="addValue('6')">
                6
            </button>

            <button
                class="function"
                onclick="inverseNumber()">
                1/x
            </button>

            <button
                class="operator"
                onclick="addValue('-')">
                −
            </button>


            <button onclick="addValue('1')">
                1
            </button>

            <button onclick="addValue('2')">
                2
            </button>

            <button onclick="addValue('3')">
                3
            </button>

            <button
                class="function"
                onclick="addValue('3.14159265359')">
                π
            </button>

            <button
                class="operator"
                onclick="addValue('+')">
                +
            </button>


            <button onclick="addValue('0')">
                0
            </button>

            <button onclick="addValue('.')">
                .
            </button>

            <button
                class="function"
                onclick="addValue('2.71828182846')">
                e
            </button>

            <button
                class="equal"
                onclick="calculate()">
                =
            </button>

        </div>

    </main>


    <!-- =================================================
         SIDEBAR
    ================================================== -->

    <aside class="sidebar">


        <!-- ADVERTISEMENT -->

        <div class="card">

            <h2>📢 Advertisement</h2>

            <div class="ad">

                <div>

                    <strong>
                        YOUR AD HERE
                    </strong>

                    <span>
                        300 × 250 Advertisement
                    </span>

                    <br><br>

                    <small>
                        Replace this area with
                        your approved ad code.
                    </small>

                </div>

            </div>

        </div>


        <!-- MEME -->

        <div class="card">

            <h2>😂 Calculator Meme</h2>

            <div class="meme">

                <div
                    class="meme-emoji"
                    id="memeEmoji">
                    🧮
                </div>

                <div id="memeText">
                    When the answer is
                    definitely not 2. 😭
                </div>

                <br>

                <button
                    onclick="newMeme()">
                    NEW MEME
                </button>

            </div>

        </div>


        <!-- HISTORY -->

        <div class="card">

            <h2>📜 History</h2>

            <div
                class="history"
                id="history">

                <div class="history-item">
                    No calculations yet.
                </div>

            </div>

        </div>

    </aside>

</div>


<footer>

    PHP Calculator • XAMPP •
    Made for learning

</footer>


<script>

/*
=========================================================
CALCULATOR JAVASCRIPT
=========================================================
*/

let currentExpression = "";

let history = [];


/*
|--------------------------------------------------------------------------
| Add value
|--------------------------------------------------------------------------
*/

function addValue(value) {

    if (
        document.getElementById("display")
            .innerText === "ERROR"
    ) {

        clearDisplay();

    }

    currentExpression += value;

    updateDisplay();

}


/*
|--------------------------------------------------------------------------
| Update display
|--------------------------------------------------------------------------
*/

function updateDisplay() {

    document.getElementById(
        "expression"
    ).innerText =
        currentExpression || "0";

}


/*
|--------------------------------------------------------------------------
| Clear
|--------------------------------------------------------------------------
*/

function clearDisplay() {

    currentExpression = "";

    document.getElementById(
        "expression"
    ).innerText = "0";

    document.getElementById(
        "display"
    ).innerText = "0";

}


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

function backspace() {

    currentExpression =
        currentExpression.slice(0, -1);

    updateDisplay();

}


/*
|--------------------------------------------------------------------------
| Square root
|--------------------------------------------------------------------------
*/

function squareRoot() {

    if (!currentExpression) return;

    let value =
        parseFloat(currentExpression);

    if (value < 0) {

        showError();

        return;
    }

    let answer =
        Math.sqrt(value);

    showAnswer(
        "√(" + value + ")",
        answer
    );

}


/*
|--------------------------------------------------------------------------
| Square
|--------------------------------------------------------------------------
*/

function squareNumber() {

    if (!currentExpression) return;

    let value =
        parseFloat(currentExpression);

    let answer =
        value * value;

    showAnswer(
        value + "²",
        answer
    );

}


/*
|--------------------------------------------------------------------------
| Inverse
|--------------------------------------------------------------------------
*/

function inverseNumber() {

    if (!currentExpression) return;

    let value =
        parseFloat(currentExpression);

    if (value === 0) {

        showError();

        return;
    }

    let answer =
        1 / value;

    showAnswer(
        "1/" + value,
        answer
    );

}


/*
|--------------------------------------------------------------------------
| Show answer
|--------------------------------------------------------------------------
*/

function showAnswer(
    expression,
    answer
) {

    document.getElementById(
        "expression"
    ).innerText = expression;

    document.getElementById(
        "display"
    ).innerText = answer;

    addHistory(
        expression,
        answer
    );

    currentExpression =
        String(answer);

}


/*
|--------------------------------------------------------------------------
| Error
|--------------------------------------------------------------------------
*/

function showError() {

    document.getElementById(
        "display"
    ).innerText = "ERROR";

}


/*
|--------------------------------------------------------------------------
| Calculate
|--------------------------------------------------------------------------
*/

function calculate() {

    if (!currentExpression) {

        return;

    }


    /*
    | Check basic safety before
    | sending the expression to PHP.
    */

    if (
        !/^[0-9+\-*\/().%\s]+$/
            .test(currentExpression)
    ) {

        showError();

        return;
    }


    let form =
        document.createElement("form");

    form.method = "POST";

    form.style.display = "none";


    let input =
        document.createElement("input");

    input.name = "expression";

    input.value =
        currentExpression;


    form.appendChild(input);

    document.body.appendChild(form);

    /*
    | Save expression for history.
    */

    let expressionBeforeSubmit =
        currentExpression;

    localStorage.setItem(
        "lastExpression",
        expressionBeforeSubmit
    );

    form.submit();

}


/*
|--------------------------------------------------------------------------
| History
|--------------------------------------------------------------------------
*/

function addHistory(
    expression,
    answer
) {

    history.unshift({
        expression: expression,
        answer: answer
    });

    if (history.length > 20) {

        history.pop();

    }

    renderHistory();

}


function renderHistory() {

    let container =
        document.getElementById(
            "history"
        );

    if (history.length === 0) {

        container.innerHTML =
            '<div class="history-item">' +
            'No calculations yet.' +
            '</div>';

        return;

    }


    container.innerHTML = "";

    history.forEach(item => {

        let div =
            document.createElement("div");

        div.className =
            "history-item";

        div.innerText =
            item.expression +
            " = " +
            item.answer;

        container.appendChild(div);

    });

}


/*
|--------------------------------------------------------------------------
| Keyboard
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "keydown",
    function(event) {

        let key = event.key;

        if (
            /[0-9+\-*\/().%]/.test(key)
        ) {

            addValue(key);

        }

        else if (key === "Enter") {

            calculate();

        }

        else if (key === "Backspace") {

            backspace();

        }

        else if (
            key === "Escape"
        ) {

            clearDisplay();

        }

    }
);


/*
|--------------------------------------------------------------------------
| Memes
|--------------------------------------------------------------------------
*/

const memes = [

    {
        emoji: "😭",
        text:
            "When you calculate 1 + 1 and somehow get 3."
    },

    {
        emoji: "🧠",
        text:
            "Me: I don't need a calculator. Also me: 27 + 48..."
    },

    {
        emoji: "🤓",
        text:
            "Math is easy until the teacher says: show your solution."
    },

    {
        emoji: "💀",
        text:
            "When the answer is 0.000001 and your confidence is 100%."
    },

    {
        emoji: "🔥",
        text:
            "This calculator is doing more work than I am."
    },

    {
        emoji: "😂",
        text:
            "Teacher: You can use a calculator. Me: Finally!"
    },

    {
        emoji: "🫠",
        text:
            "Me after trying to divide by zero."
    }

];


function newMeme() {

    let meme =
        memes[
            Math.floor(
                Math.random() *
                memes.length
            )
        ];

    document.getElementById(
        "memeEmoji"
    ).innerText =
        meme.emoji;

    document.getElementById(
        "memeText"
    ).innerText =
        meme.text;

}


/*
|--------------------------------------------------------------------------
| Load saved expression
|--------------------------------------------------------------------------
*/

window.addEventListener(
    "load",
    function() {

        updateDisplay();

    }
);

</script>

</body>

</html>