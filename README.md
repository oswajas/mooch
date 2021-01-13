# mooch

[Moodle](https://moodle.org/) plugins for chess.

## qtype_mooch

A question type for one move chess puzzles.

### Features

#### For Teachers

  1. Create a quiz.
  2. Add a chess question to the quiz.
  3. Enter a position in Forsyth-Edwards notation or by using the graphical
     board editor.
  4. Add one or more answers with corresponding scores and feedback.

#### For Learners

  1. Take a quiz with chess questions.
  2. Input answers by typing a move in long algebraic notation or by playing it
     on the displayed chess board.
  3. Submit the quiz to have it automatically graded and to get feedback.
  
### Requirements

#### Server

  - moodle (tested with version 3.10)
  - php (tested with 7.3 and 7.4; might work with versions as early as 7.0; will
    definitely not work with 5.6 and older versions)

#### Client

  - A browser that understands ES6 modules, specifically [dynamic
    imports](https://caniuse.com/es6-module-dynamic-import). If your browser
    version was released in 2019 or later, you should be fine.

### Installation

  1. Create the folder `question/type/mooch/` in your moodle installation.
  2. Copy the contents of the folder `qtype_mooch` to `question/type/mooch/`.
  3. Open your moodle website to have moodle automatically pick up the new
     plugin.
  4. Optionally browse to `admin/settings.php?section=qtypesettingmooch` of your
     moodle website to configure the theme that should be used to display chess 
     boards and pieces.
     
### Credits

qtype_mooch uses [Chessground](https://github.com/ornicar/chessground) for
displaying and interacting with chessboards and pieces. Chessground is licensed
under the GNU GPL-3.0 license.

Graphics for chessboards and pieces come from the
[lila](https://github.com/ornicar/lila) project, licensed under the GNU AGPL-3
license.

## License

mooch is licensed under GNU GPL-3.0.
