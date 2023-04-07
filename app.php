<?php

class Game {
    const NUMBER_OF_FRAMES = 10;

    public function __construct() {
        $this->rolls = [];
        $this->framesScores = [];
        $this->bonuses = [];
    }

    public function roll(int $pins) {
        array_push($this->rolls, $pins);
    }

    public function getScore() {
        $score = 0;
        $frameStartIndex = 0;

        for ($i = 0; $i < Game::NUMBER_OF_FRAMES; $i++) {
            if ($this->isStrike($frameStartIndex)) {
                $score += 10 + $this->getNextTwoRollsSum($frameStartIndex + 1);
                $this->bonuses[$i] = $this->getNextTwoRollsSum($frameStartIndex + 1);
                $frameStartIndex += 1;
            } elseif ($this->isSpare($frameStartIndex)) {
                $score += 10 + $this->GetNextRollPins($frameStartIndex + 2);
                $this->bonuses[$i] = $this->GetNextRollPins($frameStartIndex + 2);
                $frameStartIndex += 2;
            } else {
                $score += $this->GetNextTwoRollsSum($frameStartIndex);
                $frameStartIndex += 2;
            }
        }

        return $score;
    }

    private function isSpare(int $frameStartIndex): int {
        return $this->GetNextTwoRollsSum($frameStartIndex) == 10;
    }

    private function isStrike(int $frameStartIndex): int {
        return $this->GetNextRollPins($frameStartIndex) == 10;
    }

    private function GetNextRollPins(int $index): int {
        return isset($this->rolls[$index]) ? $this->rolls[$index] : 0;
    }

    private function GetNextTwoRollsSum(int $index): int {
        return $this->GetNextRollPins($index) + $this->GetNextRollPins($index + 1);
    }

    public function getProperScore() {
        $properScore = $this->framesScores;
        foreach ($this->bonuses as $key => $value) {
            $properScore[$key] += $value;
        }
        return $properScore;
    }

    public function playFrame(int $frameNumber) {
        $remainingPins = 10;
        $firstRoll = true;

        while ($firstRoll || $remainingPins > 0) {
            echo "Enter the number of pins knocked down (0-{$remainingPins}): ";
            $pins = intval(trim(fgets(STDIN)));

            if ($pins < 0 || $pins > $remainingPins) {
                echo "Invalid number of pins. Please enter a number between 0 and {$remainingPins}.\n";
                continue;
            }

            $this->roll($pins);
            echo "Score after roll in frame " . ($frameNumber + 1) . ": {$this->getScore()}\n";

            if ($firstRoll && !$this->isStrike(count($this->rolls) - 1)) {
                $remainingPins -= $pins;
                $firstRoll = false;
            } else {
                break;
            }
        }

        array_push($this->framesScores, $this->getScore());
    }

    public function playBonusRoll() {
        $remainingPins = 10;
        echo "BONUS! Enter the number of pins knocked down (0-{$remainingPins}): ";
        $pins = intval(trim(fgets(STDIN)));
        $this->roll($pins);
    }

    public function playGame() {
        for ($frame = 0; $frame < Game::NUMBER_OF_FRAMES; $frame++) {
            $this->playFrame($frame);
        }

        if ($this->isStrike(count($this->rolls) - 1) || $this->isSpare(count($this->rolls) - 2)) {
            $this->playBonusRoll();
        }

        echo "The game has been finished! Final score: {$this->getScore()}\n";
        echo "Frame Scores: ";
        print_r($this->getProperScore());
    }
}

$game = new Game();
$game->playGame();

?>

