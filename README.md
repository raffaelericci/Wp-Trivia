# Wp-Trivia

## ** WARNING: This is a work in progress project. Absolutely not ready for use **

A simplified and prettified fork of Wp-Pro-Quiz, quiz plugin for WordPress

## Installation

1. Upload the Wp-Trivia folder to the wp-content/plugins directory
2. Activate the plugin through the 'Plugins' menu in WordPress

## Custom Hooks

```PHP
// Usage example (action wptrivia_after_answer):
$nParams = 2; // Number of action params
function your_callback_function($req, $res) {
    // Your callback operations
}
add_action('wptrivia_after_answer', 'your_callback_function', 10, $nParams);
```

Action | Description | N. of params | Params example
------------ | ------------- | ------------- | -------------
wptrivia_after_answer | Called after the answer check | 2 | Example (wrong answer): <br /> <br /> $req = [ <br /> &nbsp;&nbsp;&nbsp;&nbsp; "questionId" => 3, <br /> &nbsp;&nbsp;&nbsp;&nbsp; "questionType" => "single" <br /> &nbsp;&nbsp;&nbsp;&nbsp; "answer" => [ <br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 0 => 0, <br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1 => 1, <br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2 => 0, <br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 3 => 0 <br /> ]; <br /> <br /> $res = [ <br /> &nbsp;&nbsp;&nbsp;&nbsp; "isCorrect" => 0, <br /> &nbsp;&nbsp;&nbsp;&nbsp; "correctAnswer" => [ <br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 0 => 0, <br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1 => 0, <br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2 => 0, <br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 3 => 1 <br /> ];
wptrivia_quiz_ended | Called after the last question is answered | 1 | Example: <br /> <br /> $quizId = 10;
