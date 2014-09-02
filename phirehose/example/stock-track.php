<?php
require_once('../lib/Phirehose.php');
require_once('../lib/OauthPhirehose.php');

$db = mysql_connect('localhost', 'root', ''); 
mysql_select_db('tweets', $db); 
/**
 * Example of using Phirehose to display a live filtered stream using track words
 */
class FilterTrackConsumer extends OauthPhirehose
{
  /**
   * Enqueue each status
   *
   * @param string $status
   */

  

  public function enqueueStatus($status)
  {
      $data = json_decode($status, true);
      if (is_array($data) && isset($data['user']['screen_name'])) {

                //Prep and Format array for insertion 
               
                $text = mysql_real_escape_string(urldecode($data['text'])); 
                $screen_name = mysql_real_escape_string($data['user']['screen_name']); 
                $followers_count = mysql_real_escape_string($data['user']['followers_count']); 
                $created_at = mysql_real_escape_string($data['created_at']); 

                //Store tweets in to MySQL datebase 
                $ok = mysql_query("INSERT INTO stocks (text, screen_name, followers_count, created_at, entry_stamp) VALUES ('$text', '$screen_name', '$followers_count', '$created_at', NOW())"); 
                if (!$ok) {echo "Mysql Error: ".mysql_error();} 
                flush(); 
                print $data['user']['screen_name'] . ': ' . urldecode($data['text']) . "\n";
      }
  
    }


}


// The OAuth credentials you received when registering your app at Twitter
define("TWITTER_CONSUMER_KEY", "");
define("TWITTER_CONSUMER_SECRET", " ");


// The OAuth data for the twitter account
define("OAUTH_TOKEN", "");
define("OAUTH_SECRET", "");

// Start streaming
$sc = new FilterTrackConsumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);
$sc->setTrack(array('$HPQ'));
$sc->consume();
