<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attempt;
use App\Models\Passage;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $passage = Passage::find($request->passage_id);
        $spoken = strtolower($request->spoken_text);
        $expected = strtolower($passage->content);

        // Simple word comparison
        $spokenWords = explode(' ', $spoken);
        $expectedWords = explode(' ', $expected);

        $matches = count(array_intersect($spokenWords, $expectedWords));
        $accuracy = round(($matches / count($expectedWords)) * 100, 2);

        // Identify missed or mispronounced words
        $missingWords = array_diff($expectedWords, $spokenWords);
        $feedback = "Great job ". $request->student_name . " ! Based on your reading skills, your accuracy score is: {$accuracy}%.";
        //$feedback .= "Accuracy: {$accuracy}%. ";
        if (count($missingWords) > 0) {
            $feedback .= "You missed these words ". $request->student_name. " :" .implode(', ', array_slice($missingWords, 0, 10));
        } else {
            $feedback .= "You have successfully read everything correctly!";
        }

        Attempt::create([
            //'user_id' => auth()->id(),
            'student_name' => $request->student_name,
            'passage_id' => $passage->id,
            'spoken_text' => $spoken,
            'accuracy_score' => $accuracy,
            'feedback' => $feedback
        ]);

        return redirect()->back()->with('success', $feedback);
    }
}
