<?php
/**
 * A simple example of how you could consume (ie: process) statuses collected by the ghetto-queue-collect.
 * 
 * This script in theory supports multi-processing assuming your filesystem supports flock() semantics. If you're not 
 * sure what that means, you probably don't need to worry about it :)
 * 
 * Caveat: I'm not sure if this works properly/at all on windows.
 * 
 * See: http://code.google.com/p/phirehose/wiki/Introduction
 */
class GhettoQueueConsumer
{
  
  /**
   * Member attribs
   */
  protected $queueDir;
  protected $filePattern;
  protected $checkInterval;
  
  /**
   * Construct the consumer and start processing
   */
  public function __construct($queueDir = '/tmp', $filePattern = 'phirehose-ghettoqueue*.queue', $checkInterval = 10)
  {
    $this->queueDir = $queueDir;
    $this->filePattern = $filePattern;
    $this->checkInterval = $checkInterval;
    
    // Sanity checks
    if (!is_dir($queueDir)) {
      throw new ErrorException('Invalid directory: ' . $queueDir);
    }
    
  }
  
  /**
   * Method that actually starts the processing task (never returns).
   */
  public function process() {
    
    // Init some things
    $lastCheck = 0;
    
    // Loop infinitely
    while (TRUE) {
      
      // Get a list of queue files
      $queueFiles = glob($this->queueDir . '/' . $this->filePattern);
      $lastCheck = time();
      
      $this->log('Found ' . count($queueFiles) . ' queue files to process...');
      
      // Iterate over each file (if any)
      foreach ($queueFiles as $queueFile) {
        $this->processQueueFile($queueFile);
      }
      
      // Wait until ready for next check
      $this->log('Sleeping...');
      while (time() - $lastCheck < $this->checkInterval) {
        sleep(1);
      }
      
    } // Infinite loop
    
  } // End process()
  
  /**
   * Processes a queue file and does something with it (example only)
   * @param string $queueFile The queue file
   */
  protected function processQueueFile($queueFile) {
    $this->log('Processing file: ' . $queueFile);
    
    // Open file
    $fp = fopen($queueFile, 'r');
    
    // Check if something has gone wrong, or perhaps the file is just locked by another process
    if (!is_resource($fp)) {
      $this->log('WARN: Unable to open file or file already open: ' . $queueFile . ' - Skipping.');
      return FALSE;
    }
    
    // Lock file
    flock($fp, LOCK_EX);
    
    // Loop over each line (1 line per status)
    $statusCounter = 0;
    while ($rawStatus = fgets($fp, 8192)) {
      $statusCounter ++;
      
      /** **************** NOTE ********************
       * This is the part where you would normally do your processing. If you're extracting/trending information 
       * about the tweets it should happen here, where it doesn't matter so much if things are slow (you will
       * catch up on the next loop).
       */
      //Open a MySQL Connection to store array: 
        $db = mysql_connect('localhost', 'root', ''); 
        mysql_select_db('tweets', $db); 
                //Pickup feed 
                $data = json_decode($rawStatus); 
                //Prep and Format array for insertion 
                $id = mysql_real_escape_string($data->{'id'}); 
                $screen_name = mysql_real_escape_string($data->{'user'}->{'screen_name'}); 
                $created_at = mysql_real_escape_string(strtotime($data->{'created_at'})); 
                $geo = mysql_real_escape_string($data->{'geo'}); 
                $url = mysql_real_escape_string($data->{'url'}); 
                $text = mysql_real_escape_string($data->{'text'}); 
                $followers_count = mysql_real_escape_string($data->{'user'}->{'followers_count'}); 
               

                //Store tweets in to MySQL datebase 
                $ok = mysql_query("INSERT INTO guitar (id, user_name,  created_at, geo, tweet_url, text, followers_count, entry_stamp) VALUES ('$id', '$screen_name', '$created_at', $geo, $tweet_url, $text, $followers_count,  NOW())"); 
                if (!$ok) {echo "Mysql Error: ".mysql_error();} 
                flush(); 

      
    } // End while
    
    // Release lock and close
    flock($fp, LOCK_UN);
    fclose($fp);
    
    // All done with this file
    $this->log('Successfully processed ' . $statusCounter . ' tweets from ' . $queueFile . ' - deleting.');
    unlink($queueFile);    
    
  }
  
  /**
   * Basic log function.
   *
   * @see error_log()
   * @param string $messages
   */
  protected function log($message)
  {
    @error_log($message, 0);
  }
    
}

// Construct consumer and start processing
$gqc = new GhettoQueueConsumer();
$gqc->process();