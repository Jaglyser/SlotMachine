<?php

namespace App\Http\Controllers;

use App\Author;
use Illuminate\Http\Request;



class RawController extends Controller
{
    public function generateBoard(){

        // mt_rand uses Mersenne Twister pseudorandom function, which is actually pretty good.
        // generate a random key to the array of symbols.
        $symbols = ['9', '10', 'J', 'Q', 'K', 'A', 'cat', 'dog', 'monkey', 'bird']; 
        $board = [];


        for($n = 0; $n < 15; $n++){
            $key = mt_rand(0, 9);
            $board[] = $symbols[$key];
        }
        return $board;
    }


    // generates the matching numbers to the board
    public function generateBoardNum(){
        for($i = 0; $i < 3; $i++){
            for($j = 0; $j < 5; $j++){
                $entries[] = $i + ($j * 3);
            }
        }
        return $entries;
    }

    public function findWinner(){
        $board = $this->generateBoard();

        // constants
        $bet = 100;
        $paylines = [
            "0 3 6 9 12",
            "1 4 7 10 13",
            "2 5 8 11 14",
            "0 4 8 10 12",
            "2 4 6 10 14"
        ];

        

        // variables which are used later to determine whether a payline is winning or not
        $previous = 0;
        $current = 0;
        $connected = [];
        $streak= 0;

        // multiplier for the win amount
        $multiplier = 0;
        $win = 0;

        // generate array with all matching numbers to the board
        $entries = $this->generateBoardNum();
        $multi_board = array_combine($entries, $board);
        $fpaylines = [];

        foreach($paylines as $payline){
            // when two or more symbols are the same next to each other the system gathers them in $connected
            // it also counts how many fields the array connected contains
            // so when looping through the paylines, it resets $connected and $streak when looking through a new payline
            $streak = 0;
            $connected = [];

            // takes the payline as a string, splits it at every whitespace and then trims the whitespaces away
            $characters = array_map('trim', explode(' ', $payline));

            // loops through all the matching numbers in the recently split payline
            foreach($characters as $char){
                // using the number as every symbol has been indexed to the number
                // takes the current symbol
                $current = $multi_board[$char];

                // checks if the current symbol is the same as the previous, and if so starts a streak
                // if not it resets the streak
                if($current == $previous){
                    $connected[] = $current;
                    $streak = count($connected) + 1;

                } else {
                    $connected = [];
                    $streak = 0;
                }

                // if the streak is 3 symbols in a row it makes the win multiplier 20%
                if($streak == 3){
                    $multiplier = 0.2;
                    // $fpaylines[] =  array($payline=>3);
                }

                // 4 symbol streak = 200%
                if($streak == 4){
                    $multiplier = 2;
                    // $fpaylines[] =  array($payline=>4);
                }

                // 5 symbol steak = 1000%
                if($streak == 5){
                    $multiplier = 10;
                    // $fpaylines[] =  array($payline=>5);

                }
                // remembers the previous symbol
                $previous = $multi_board[$char];
            }

            // the total win sum
            $win += $multiplier*100;
            
            // calls the payLineNumber function which remembers:
            // which payline that won, and how many symbols in a row it was
            if($multiplier != 0){
                $fpaylines[] = $this->payLineNunber($payline, $multiplier);
            }
            
            // resets the multiplier
            $multiplier = 0;
        }

        // indexes the object properly before conversion to JSON
        $array_json = ['board' => $board, 'paylines' => $fpaylines, 'bet_amount' => $bet, 'total_win' => $win];
        
        // prints to command line and encodes to JSON in forced object form
        echo json_encode($array_json, JSON_PRETTY_PRINT);

        // works for the API, but wont print to commandline.
        return response()->json($array_json);
    }

    public function playSlot(){
        $this->findWinner();
    }
    
    // remebers which payline won and how many symbols in a row
    public function payLineNunber($payline, $multiplier){
        $pay_line_number = [
            0.2 => [$payline => 3],
            2 => [$payline => 4],
            10 => [$payline=>5],
        ];

        return $pay_line_number[$multiplier];
    }

}
