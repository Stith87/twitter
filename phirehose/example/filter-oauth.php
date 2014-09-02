<?php
require_once('../lib/Phirehose.php');
require_once('../lib/OauthPhirehose.php');

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
    /*
     * In this simple example, we will just display to STDOUT rather than enqueue.
     * NOTE: You should NOT be processing tweets at this point in a real application, instead they should be being
     *       enqueued and processed asyncronously from the collection process. 
     */
    $data = json_decode($status, true);
    if (is_array($data) && isset($data['user']['screen_name'])) {
      print $data['user']['screen_name'] . ': ' . urldecode($data['text']) . "\n";
    }
  }
}

// The OAuth credentials you received when registering your app at Twitter
define("TWITTER_CONSUMER_KEY", "WOr9sH9pXURDejQ47PTf2QPHR");
define("TWITTER_CONSUMER_SECRET", "HVJgdXuNwtyD5mceFvPIJFsqJF9OcK5gsIkV6DhnTWA991jdqS");


// The OAuth data for the twitter account
define("OAUTH_TOKEN", "HVJgdXuNwtyD5mceFvPIJFsqJF9OcK5gsIkV6DhnTWA991jdqS");
define("OAUTH_SECRET", "RNnhvIMwvxbKBUuTUW3hqzvUV6mVrqAOOJkHZrYFopbhB");

// Start streaming
$sc = new FilterTrackConsumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);
$sc->setTrack(array('guitar'));
$sc->consume();
