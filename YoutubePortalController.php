<?php

App::uses('AppController', 'Controller');

App::import(
    'Vendor',
    'Google_Client',
    array('file' => 'google-api-php-client' . DS . 'src' . DS . 'Google_Client.php')
);

App::import(
    'Vendor',
    'Google_YouTubeService',
    array('file' => 'google-api-php-client' . DS . 'src' . DS . 'contrib' . DS . 'Google_YouTubeService.php')
);

class YoutubePortalController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array('SearchTerm');
        
    public function beforeFilter() {
        
       parent::beforeFilter();
             $DEVELOPER_KEY = 'AIzaSyDOkg-u9jnhP-WnzX5WPJyV1sc5QQrtuyc';

             $client = new Google_Client();
             $client->setDeveloperKey($DEVELOPER_KEY);
            $this->youtube = new Google_YoutubeService($client);
         
    }

    /*
     * main file
     * 
     */
    
    
    
    function index() {
    
     $htmlBody='';
    
    if ($this->request->is('post')) {
        
      if(isset($this->request->data['SearchTerm']['query'])&& !empty($this->request->data['SearchTerm']['query']))
        {
              //   pr($this->request->data);
                 $query=$this->request->data['SearchTerm']['query'];
                 $maxResults=$this->request->data['SearchTerm']['maxResults'];
                 $ip= $_SERVER['REMOTE_ADDR'];
                 $data['SearchTerm']['keyword']=$query;
                 $data['SearchTerm']['ip_address']=$ip; 
                 $data['SearchTerm']['plateform']=$_SERVER['HTTP_USER_AGENT'];
                 $rs=$this->SearchTerm->save($data);
                 
            try {
                $searchResponse =$this->youtube->search->listSearch('id,snippet', array(
                    'q' =>$query,
                    'maxResults' =>$maxResults,
                ));
         
                //http://www.youtube.com/user/YourUsernameHere
                //    echo"<pre>";
                //       print_r($searchResponse);
                //     echo"</pre>";

             
                  } catch (Google_ServiceException $e) {
                        $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
                    } catch (Google_Exception $e) {
                        $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
                    } 
                
                    $this->set(compact('htmlBody','searchResponse'));
              }
         }
    
    }
    
    
    /*
     * details page
     */
    
    
    function watch(){
          if (isset($this->request->params['named']['v']) && $this->request->params['named']['v']) {
               $video_id =$this->request->params['named']['v'];

                $video_info = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=snippet&id=' . $video_id . '&key=AIzaSyCLql6-_jIuHxObSQGewceBYc0y1seDs88'));
                $video_details = $video_info->items[0]->snippet;
            //    echo"<pre>";
            //    var_dump($video_details);
            //    echo"</pre>";
                if (isset($this->request->params['named']['user'])) {
                    $user_id =$this->request->params['named']['user'];
                    $channel_data = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/search?key=AIzaSyCLql6-_jIuHxObSQGewceBYc0y1seDs88&channelId=' . $user_id . '&part=snippet,id&order=date&maxResults=20'));

                    $channel_data = $channel_data;
            //    echo"<pre>";
            //    print_r($channel_data);
            //  echo"</pre>";
                    
                       $this->set(compact('channel_data','video_details','video_id','user_id'));
             }
        } else {
            $this->redirect('index');
            }
    }
     
    /*
     * 
     */
    
    
         function channel(){
          if (isset($this->request->params['named']['user']) && $this->request->params['named']['user']) {
            $user_id =$this->request->params['named']['user'];
                //echo 'https://www.googleapis.com/youtube/v3/search?key=AIzaSyCLql6-_jIuHxObSQGewceBYc0y1seDs88&channelId=' . $user_id . '&part=snippet,id&order=date&maxResults=2';
            $channel_data = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/search?key=AIzaSyCLql6-_jIuHxObSQGewceBYc0y1seDs88&channelId=' . $user_id . '&part=snippet,id&order=date&maxResults=2'));
             
            $this->set(compact('channel_data','user_id'));
             }
        else {
            $this->redirect('index');
            }
    }
    
    
    function video(){
          if (isset($this->request->params['named']['v']) && $this->request->params['named']['v']) {
               $video_id =$this->request->params['named']['v'];

                $video_info = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=snippet&id=' . $video_id . '&key=AIzaSyCLql6-_jIuHxObSQGewceBYc0y1seDs88'));
                $video_details = $video_info->items[0]->snippet;
                           $this->set(compact('channel_data','video_details','video_id','user_id'));
                 }
              else {
            $this->redirect('index');
            }
        
    }
    
}
