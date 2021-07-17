<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class SolutionController extends Controller
{
    /**
     * @param $puzzle
     * @param $words
     * @return array|false
     */
    public function search($puzzle, $words):array
    {
        //Add padding to puzzle to avoid boundaries
        $modifiedPuzzle = $this->modifyPuzzle($puzzle);
        $puzzleColumnCount = strlen($puzzle[0]); //Column count of original puzzle
        $columnCount = $puzzleColumnCount + 2; //Column count of original puzzle will help us to create a vector array
        //We use this vector array to search in all 8 directions
        $vectors = [
            -$columnCount - 1,
            -$columnCount,
            -$columnCount + 1,
            -1,
            1,
            $columnCount - 1,
            $columnCount,
            $columnCount + 1,
        ];
        $tempArray = array(); //Where we log our findings temporarily
        $lostAndFound = array(); //Where we log our findings

        //Loop through every words and find them in the puzzle
        foreach ($words as $word) {
            for ($x = 0; $x < strlen($modifiedPuzzle); $x++) {
                $index = ''; //reset index
                if ($modifiedPuzzle[$x] == $word[0]) {
                    // If first letter matches, add value in the temp array and continue for searching next letter in the word
                    // We use tempArray to log found indexes and when all letters are found we dump the temp Array in lostAndFound array (where we log our results)
                    $index = $x;
                    $tempArray[] = $x;
                    //Loop the letters in the word and look for the next letter
                    for ($j = 1; $j < strlen($word); $j++) {
                        $flag = 0; //Variable helps us to mark if next letter is found. If not no need to continue searching and we move on

                        //Since we found the first letter we search for next letter, and to do that we use our vector array
                        foreach ($vectors as $vector) {
                            //Search for the letter in found letter's index + vector position
                            if ($modifiedPuzzle[$index + $vector] == $word[$j]) {
                                if (!in_array($index + $vector, $tempArray)) {
                                    $tempArray[] = $index + $vector;
                                    $index = $index + $vector; //Move index to the next one
                                    $flag = 1;
                                    break;
                                }
                            }
                        }
                        if ($flag === 0) {
                            //If next letter is not found search no more
                            unset($tempArray);
                            break;
                        }
                    }

                    // If we found the letter then we break, otherwise continue searching.
                    if (isset ($tempArray) && count($tempArray) === strlen($word)) {
                        break;
                    }
                }
            }
            // If all letters in a word are found then add it into an array
            if (count($tempArray) === strlen($word)) {
                foreach ($tempArray as $key => $value) {
                    if (!in_array($value, $lostAndFound)) {
                        $lostAndFound[] = $value;
                    }
                }
            }
            unset($tempArray);
        }

        return $this->prepareOutput($modifiedPuzzle, $lostAndFound, $puzzleColumnCount);
    }

    /**
     * Method below returns the formatted solution of a puzzle
     * @param $modifiedPuzzle
     * @param $lostAndFound
     * @param $puzzleColumnCount
     * @return \Illuminate\Http\JsonResponse
     */
    public function prepareOutput($modifiedPuzzle, $lostAndFound, $puzzleColumnCount): array
    {
        //Prepare the result and return it as an array
        $puzzleSolution = '';

        for ($i = 0; $i < strlen($modifiedPuzzle); $i++) {
            if ($modifiedPuzzle[$i] !== '+') {
                if (!in_array($i, $lostAndFound)) {
                    $puzzleSolution .= '*';
                } else {
                    $puzzleSolution .= $modifiedPuzzle[$i];
                }
            }
        }

        return str_split($puzzleSolution, $puzzleColumnCount);
    }

    /**
     * Method below adds padding to avoid boundary tests and simply converts our 2 dimensional array to 1 dimensional array
     * @param $puzzle
     * @return string
     */
    public function modifyPuzzle($puzzle): string
    {
        $rowCount = count($puzzle); //row count
        $columnCount = strlen($puzzle[0]); //column count
        $puzzlePadding = ""; //temp variable we use while creating the modified puzzle
        $newPuzzle = ""; //modified puzzle

        //Adding padding to  avoid boundary tests
        for ($p = 0; $p < $columnCount + 2; $p++) {
            $puzzlePadding .= '+';
        }
        $newPuzzle .= $puzzlePadding; //first line of the modified puzzle
        for ($puz = 0; $puz < $rowCount; $puz++) {
            $newPuzzle .= '+' . $puzzle[$puz] . '+';
        }
        $newPuzzle .= $puzzlePadding;//last line of the modified puzzle
        return $newPuzzle;
    }


    /**
     *
     * @return array|false|\Illuminate\Http\Response
     */
    public function index()
    {

        $puzzle =
            [
                'GQPVMISSIOSSTUDVUWMSE',
                'REGIUSVICTRIXSDUCUNIA',
                'NUNQUEMIMPERIPHPUMADI',
                'URIASVJLUMINCUBICULEM',
                'ASSIVDVSERGTSOPERENRH'
            ];

        $words =
            [
                'VUEJS',
                'PHP',
                'REDIS',
                'POSTGRES'
            ];
        //Find the solution of the puzzle
        //Return the solution of the puzzle in a blade template

        return $this->search($puzzle, $words);
    }
}
