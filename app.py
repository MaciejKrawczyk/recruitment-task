class Game:
    NUMBER_OF_FRAMES = 10

    def __init__(self):
        self.rolls = []
        self.frames_scores = []
        self.bonuses = {}

    def roll(self, pins):
        self.rolls.append(pins)

    def get_score(self):
        score = 0
        frame_start_index = 0

        for i in range(Game.NUMBER_OF_FRAMES):
            if self.is_strike(frame_start_index):
                score += 10 + self.get_next_two_rolls_sum(frame_start_index + 1)
                self.bonuses[i] = self.get_next_two_rolls_sum(frame_start_index + 1)
                frame_start_index += 1
            elif self.is_spare(frame_start_index):
                score += 10 + self.get_next_roll_pins(frame_start_index + 2)
                self.bonuses[i] = self.get_next_roll_pins(frame_start_index + 2)
                frame_start_index += 2
            else:
                score += self.get_next_two_rolls_sum(frame_start_index)
                frame_start_index += 2

        return score

    def is_spare(self, frame_start_index):
        return self.get_next_two_rolls_sum(frame_start_index) == 10

    def is_strike(self, frame_start_index):
        return self.get_next_roll_pins(frame_start_index) == 10

    def get_next_roll_pins(self, index):
        return self.rolls[index] if index < len(self.rolls) else 0

    def get_next_two_rolls_sum(self, index):
        return self.get_next_roll_pins(index) + self.get_next_roll_pins(index + 1)

    def get_proper_score(self):
        proper_score = self.frames_scores.copy()
        for key, value in self.bonuses.items():
            proper_score[key] = proper_score[key] + value
        return proper_score

    def play_frame(self, frame_number):
        remaining_pins = 10
        first_roll = True

        while first_roll or remaining_pins > 0:
            try:
                pins = int(input(f"Enter the number of pins knocked down (0-{remaining_pins}): "))
            except ValueError:
                print("Please enter a valid number.")
                continue

            if pins < 0 or pins > remaining_pins:
                print(f"Invalid number of pins. Please enter a number between 0 and {remaining_pins}.")
                continue

            self.roll(pins)
            print(f"Score after roll in frame {frame_number + 1}: {self.get_score()}")

            if first_roll and not self.is_strike(len(self.rolls) - 1):
                remaining_pins -= pins
                first_roll = False
            else:
                break

        self.frames_scores.append(self.get_score())

    def play_bonus_roll(self):
        remaining_pins = 10
        pins = int(input(f"BONUS! Enter the number of pins knocked down (0-{remaining_pins}): "))
        self.roll(pins)

    def play_game(self):
        for frame in range(Game.NUMBER_OF_FRAMES):
            self.play_frame(frame)

        if self.is_strike(len(self.rolls) - 1) or self.is_spare(len(self.rolls) - 2):
            self.play_bonus_roll()

        print(f"The game has been finished! Final score: {self.get_score()}")
        print("Frame Scores: ", game.get_proper_score())


if __name__ == "__main__":
    game = Game()
    game.play_game()
