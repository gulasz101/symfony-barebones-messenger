<?php

require __DIR__ . "/vendor/autoload.php";

echo "START " . date('h:i:s') . PHP_EOL;

for ($i = 0; $i < 10; $i++) {
    \React\Async\async(function () {
        $browser = new \React\Http\Browser();

        return (string)\React\Async\await($browser->get('http://localhost:8080/trackings/lorem'))->getBody();
    })()->then(fn() => dump(date('h:i:s'), func_get_args()));
}

echo "STOP " . date('h:i:s') . PHP_EOL;
