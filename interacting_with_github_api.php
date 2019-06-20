<?php
/**
 * @author Mofehintolu Mumuni
 * @copyright 2019
 * 
 *The function below GitHubEventsScorer takes a Github username and
 *returns the users score based on the following criterias
 * 
 *   PushEvent = 10 points.
 *   PullRequestEvent = 5 points.
 *  IssueCommentEvent = 4 points.
 *   Any other event = 1 point
 * 
 * 
 */
 function GitHubEventsScorer($GitHubUsername){
        //check the username to ensure that it is not empty
     if((strlen($GitHubUsername) != 0) && ($GitHubUsername != null))
     {
        //Call the github api to get the required Json payload which contains
        //user events information
       
       $url =  "https://api.github.com/users/".$GitHubUsername."/events/public";
       try{
       $opts = [
       'http' => [
               'method' => 'GET',
               'header' => [
                       'User-Agent: PHP'
               ]
       ]
           ];
       $resource_context = stream_context_create($opts);
         $get_history = @file_get_contents($url,false,$resource_context);
        //convert the API payload in Json into an array and store it in $decode variable
        
         $decode = json_decode($get_history,true);
        //check if the API call was successful and throw an Exception if it wasn't
         if(!$get_history)
         {
             throw new \Exception('Unable to receive data, Json payload not obtained!');
         }
        //check if the $decode variable is an array and throw an Exception if it isn't
         
         if(!is_array($decode))
         {
         throw new \Exception('Unable to receive data, please try again!');
     }
     }
    //catch the thrown exception and return it
     catch(\Exception $e)
     {
         return 'API link error: '.$e->getMessage();
         //return view('Any_required_view')->with('error','API link error: '.$e->getMessage());
     }
        /*create a collection named $collection which is made up of the user events
        data from the Github API call
         */
       
      $collection = collect($decode);
       
    //count the collection
       
      $collection_count = $collection->count();
       
   //define variables that would hold scores for the various Github user events
       
     $PushEventGrade = 10;
     $PullRequestEventGrade = 5;
     $IssueCommentEventGrade = 4;
     $anyOtherEventGrade = 1;
       
        /*use the collection object to obtain the number of PushEvent
        PullRequestEvent and IssueCommentEvent that occur
         */
       
      $pushEventCount = $collection->where('type','PushEvent')->count();
      $pullEventCount = $collection->where('type','PullRequestEvent')->count();
      $issueEventCount = $collection->where('type','IssueCommentEvent')->count();
       
        /*Obtian the number of events that the user performed other than PushEvent
        PullRequestEvent and IssueCommentEvent that occur
         */
       
      $anyOtherEventCount = $collection_count - collect([$pushEventCount,$pullEventCount,$issueEventCount])->sum();
       
       /* calculate the score for each user event
        using the number of times each event occurred and the grade score assigned
         to each event
        */
       
      $pushEventScore = $pushEventCount * $PushEventGrade;
      $pullEventScore = $pullEventCount * $PullRequestEventGrade;
      $IssueCommentEventScore = $issueEventCount * $IssueCommentEventGrade;
      $anyOtherEventScore = $anyOtherEventCount * $anyOtherEventGrade;
       
      /*Use a collection object to obtain the total score
      for all events using the collection sum method
      */
       
     $totalScore = collect([$pushEventScore,$pullEventScore,$IssueCommentEventScore,$anyOtherEventScore])->sum();
       
     //return the user total score
       
     return 'Github events total score for '.$GitHubUsername.' is: '.$totalScore;
     //return view('Any_required_view')->with('GitHubEventsScore','Github events total score for '.$GitHubUsername.' is: '.$totalScore);
     }
     else
     {
     //return an error message if username is empty
     return('Invalid GitHub Username');
     //return view('Any_required_view')->with('error','Invalid GitHub Username');
     }
   }
?>