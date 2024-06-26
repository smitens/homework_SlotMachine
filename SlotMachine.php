<?php
function getSymbols(): array
{
    return [
        '9' => 9,
        '5' => 5,
        '2' => 2,
        '^' => 0,
        '#' => 0,
    ];
}

function createBoard($rowSize, $columnSize): array
{
    $board = [];
    for ($row = 0; $row < $rowSize; $row++) {
        $currentRow = [];
        for ($column = 0; $column < $columnSize; $column++) {
            $currentRow[] = array_rand(getSymbols());
        }
        $board[] = $currentRow;
    }
    return $board;
}

function checkWin($board, $winCondition): array
{
    $winningCombinations = [];
    $directions = [
        [0, 1],  // H
        [1, 0],  // V
        [1, 1],  // D 1
        [-1, 1], // D 2
    ];
    $usedPositions = [];

    foreach ($board as $row => $rowData) {
        foreach ($rowData as $column => $symbol) {
            foreach ($directions as $direction) {
                list ($dRow, $dColumn) = $direction;
                $count = 0;
                $i = $row;
                $j = $column;
                $winningCombination = [];

                while ($i >= 0 && $i < count($board) && $j >= 0 && $j < count($rowData) && $board[$i][$j] == $symbol) {
                    $count++;
                    $winningCombination[] = ['row' => $i, 'column' => $j];
                    $i += $dRow;
                    $j += $dColumn;
                }
                if ($count >= $winCondition) {
                    $isNewCombination = true;
                    foreach ($winningCombination as $position) {
                        $row = $position['row'];
                        $column = $position['column'];
                        if (array_key_exists($row, $usedPositions) && array_key_exists($column, $usedPositions[$row])) {
                            $isNewCombination = false;
                            break;
                        }
                    }
                    if ($isNewCombination) {
                        $winningCombinations[] = ['symbol' => $symbol, 'condition' => $winCondition, 'positions' => $winningCombination];
                        foreach ($winningCombination as $position) {
                            $row = $position['row'];
                            $column = $position['column'];
                            $usedPositions[$row][$column] = true;
                        }
                    }
                }
            }
        }
    }
    return $winningCombinations;
}

function calculatePayout($winningCombinations, $betCoefficient)
{
    $symbols = getSymbols();
    $totalPayout = 0;

    foreach ($winningCombinations as $combination) {
        $winningSymbol = $combination['symbol'];
        $winCondition = $combination['condition'];
        $symbolValue = $symbols[$winningSymbol];
        $payout = $symbolValue * $winCondition * $betCoefficient;
        $totalPayout += $payout;
    }

    return $totalPayout;
}

function displayBoard($board, $winningCombinations, $payout)
{
    echo "Spin is done!\n Slot Machine Board:\n";
    foreach ($board as $row) {
        echo implode(" ", $row) . "\n";
    }

    if (!empty($winningCombinations) && $payout > 0) {
        echo "Congratulations, you have win! Your win payout is: $payout EUR \n";
    } else {
        echo "No luck this time! Will be better next!\n";
    }
}

function playSlotMachine()
{
    $allSpinsPayout = 0;
    while (true) {
        $money = intval(readline("1 spin = 2 EUR\n Enter amount of money you want to play with: \n"));
        if ($money < 1) {
            echo "You have entered invalid value! Please enter value that is more than 1.\n";
            continue;
        }
        $bet = intval(readline("Basic bet = 2 EUR\n Enter your bet: \n"));
        if ($bet < 1) {
            echo "You have entered invalid value! Please enter value that is more than 1.\n";
            continue;
        }
        $boardSize = readline("Enter the size of the board (rows, columns, for example 3, 4 = 3X4 board): \n");
        list($boardRows, $boardColumns) = explode(',', $boardSize);
        if (!ctype_digit($boardRows) || !ctype_digit($boardColumns) || $boardRows <= 1 || $boardColumns <= 1) {
            echo "Invalid input. Please enter positive integers for rowSize and columnSize.\n";
            continue;
        }
        $winCondition = intval(readline("Enter the number of consequent symbols needed for a win (for example 3): \n"));
        if ($winCondition < 1) {
            echo "You have entered invalid value! Please enter value that is more than 1.\n";
            continue;
        }
        $availableSpins = $money / 2;
        $betCoefficient = $bet / 2;
        while ($availableSpins > 0) {
            $board = createBoard($boardRows, $boardColumns);
            $winningCombinations = checkWin($board, $winCondition);
            $payout = calculatePayout($winningCombinations, $betCoefficient);
            displayBoard($board, $winningCombinations, $payout);
            $availableSpins--;
            $allSpinsPayout += $payout;
            if ($availableSpins > 0) {
                readline("Press Enter to spin again!");
            }
        }
        echo "Total Payout for all spins: EUR " . $allSpinsPayout . "\n";

        echo "Your spins are over!\n";
        $playAgain = readline("Do you want to play again? (yes/no): ");
        if (strtolower($playAgain) == 'yes') {
            $allSpinsPayout = 0;
        } else {
            break;
        }
    }
}
playSlotMachine();# homework_SlotMachine