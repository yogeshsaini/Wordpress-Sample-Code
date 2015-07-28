<?php
include_once( "Sample.php" );
class SampleOwnMethod extends ErrorReporting
{
    //For create instance of daliymotion
    protected $sample;
    //For retun data
    public $returndata = array( );

    public function __construct( )
    {
        global $current_user;
        $this->sample = new Sample();
        $sampleoption = get_user_meta($current_user->ID, 'sample_option_auth', false);
        $apiKey = @$sampleoption[0]['sample_apikey'];
        $apiSecret = @$sampleoption[0]['sample_secretkey'];
        $this->sample->setGrantType( Sample::GRANT_TYPE_TOKEN, $apiKey, $apiSecret, array(
             'manage_videos'
        ) );
    }

    /**
     * get assosciated chennel list from sample
     */
    public function getSampleChannelList( )
    {
        try {
            $result = $this->sample->get( '/channels', array(
                 'fields' => array(
                     'id',
                    'name'
                )
            ) );
            if ( isset( $result['list'] ) && !empty( $result['list'] ) ) {
                foreach ( $result['list'] as $media ) {
                    $this->returndata[$media['id']] = $media['name'];
                }
            }
            return ( $this->returndata );
        }
        catch ( Exception $e ) {
            $this->log_error( $e->getMessage() . ' in class.sample.php function name getSampleChannelList', 'phparray', $e->getLine(), $e->getFile() );
        }
    }
    /**
     * Get My sample video lists for Meta videos
     */
    public function getSampleVideoListForSideVideos( $flag = NULL, $fields = array( 'id', 'title', 'embed_url', 'thumbnail_url', 'private', 'type' ),  $page_no = 1, $per_page = 10, $search_title )
    {
         try {
                    if ( !empty( $search_title ) ) {
                        $result = $this->sample->get( '/me/videos?sort=relevance', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'limit' => ( $per_page ),
                            'search' => $search_title
                        ) );
                    } else {
                        $result = $this->sample->get( '/me/videos?filters=creative-official', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'limit' => ( $per_page )
                        ) );
                    }
                    //print '<pre>'; print_r($result); print '</pre>';
                    if ( isset( $result['list'] ) && !empty( $result['list'] ) ) {
                        foreach ( $result['list'] as $key => $media ) {
                            $this->returndata['videos'][$key]['id'] = !empty( $media['id'] ) ? $media['id'] : null;
                            $this->returndata['videos'][$key]['title'] = !empty( $media['title'] ) ? $media['title'] : null;
                            $this->returndata['videos'][$key]['embed_url'] = !empty( $media['embed_url'] ) ? $media['embed_url'] : null;
                            $this->returndata['videos'][$key]['thumbnail_url'] = !empty( $media['thumbnail_url'] ) ? $media['thumbnail_url'] : null;
                            $this->returndata['videos'][$key]['description'] = !empty( $media['description'] ) ? $media['description'] : null;
                            $this->returndata['videos'][$key]['views_total'] = !empty( $media['views_total'] ) ? $media['views_total'] : 0;
                            $this->returndata['videos'][$key]['tags'] = !empty( $media['tags'] ) ? $media['tags'] : null;
                            $this->returndata['videos'][$key]['channel.name'] = !empty( $media['channel.name'] ) ? $media['channel.name'] : null;
                            $this->returndata['videos'][$key]['created_time'] = !empty( $media['created_time'] ) ? $media['created_time'] : null;
                            $this->returndata['videos'][$key]['duration'] = !empty( $media['duration'] ) ? $media['duration'] : null;
                            $this->returndata['videos'][$key]['owner.screenname'] = !empty( $media['owner.screenname'] ) ? $media['owner.screenname'] : null;
                            $this->returndata['videos'][$key]['private'] = !empty( $media['private'] ) ? $media['private'] : null;
                        }
                        //$this->returndata['total_record'] = $result['total'];
                        $this->returndata['has_more'] = $result['has_more'];
                    }
                    return ( $this->returndata );
                }
                catch ( Exception $e ) {
                    $this->log_error( $e->getMessage() . ' in class.sample.php function name getSampleVideoList me video', 'phparray', $e->getLine(), $e->getFile() );
                }
    }
    /**
     * Get sample video lists
     */
    public function getSampleVideoList( $flag = NULL, $fields = array( 'id', 'title', 'embed_url', 'thumbnail_url', 'private', 'type' ), $status, $page_no = 1, $per_page = 10, $search_title )
    {
        switch ( $flag ) {
            case 'all':
                try {
                    if ( !empty( $search_title ) ) {
                        $result = $this->sample->get( '/videos?sort=relevance', array(
                             'fields' => $fields,
                            'country' => $this->visitorCountry(),
                            'search' => $search_title,
                            'page' => ( $page_no ),
                            'limit' => ( $per_page )
                        ) );
                    } else {
                        $result = $this->sample->get( '/videos?filters=creative-official', array(
                             'fields' => $fields,
                            'country' => $this->visitorCountry(),
                            'page' => ( $page_no ),
                            'limit' => ( $per_page )
                        ) );
                    }
                    if ( isset( $result['list'] ) && !empty( $result['list'] ) ) {
                        foreach ( $result['list'] as $key => $media ) {
                            $this->returndata['videos'][$key]['id'] = !empty( $media['id'] ) ? $media['id'] : null;
                            $this->returndata['videos'][$key]['title'] = !empty( $media['title'] ) ? $media['title'] : null;
                            $this->returndata['videos'][$key]['embed_url'] = !empty( $media['embed_url'] ) ? $media['embed_url'] : null;
                            $this->returndata['videos'][$key]['thumbnail_url'] = !empty( $media['thumbnail_url'] ) ? $media['thumbnail_url'] : null;
                            $this->returndata['videos'][$key]['description'] = !empty( $media['description'] ) ? $media['description'] : null;
                            $this->returndata['videos'][$key]['views_total'] = !empty( $media['views_total'] ) ? $media['views_total'] : 0;
                            $this->returndata['videos'][$key]['tags'] = !empty( $media['tags'] ) ? $media['tags'] : null;
                            $this->returndata['videos'][$key]['channel.name'] = !empty( $media['channel.name'] ) ? $media['channel.name'] : null;
                            $this->returndata['videos'][$key]['created_time'] = !empty( $media['created_time'] ) ? $media['created_time'] : null;
                            $this->returndata['videos'][$key]['duration'] = !empty( $media['duration'] ) ? $media['duration'] : null;
                            $this->returndata['videos'][$key]['owner.screenname'] = !empty( $media['owner.screenname'] ) ? $media['owner.screenname'] : null;
                            $this->returndata['videos'][$key]['private'] = !empty( $media['private'] ) ? $media['private'] : null;
                        }
                        $this->returndata['total_record'] = !empty( $result['total'] ) ? $result['total'] : null;
                        $this->returndata['has_more'] = !empty( $result['has_more'] ) ? $result['has_more'] : null;
                    }
                    return ( $this->returndata );
                }
                catch ( Exception $e ) {
                    $this->log_error( $e->getMessage() . ' in class.sample.php function name getSampleVideoList all video', 'phparray', $e->getLine(), $e->getFile() );
                }
                break;
            case 'me':
                try {
                    if ( !empty( $search_title ) && $status != 'all') {
                        $result = $this->sample->get( '/me/videos?sort=relevance', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'limit' => ( $per_page ),
                            'search' => $search_title,
                            'private' => $status,
                        ) );
                    } else if( !empty( $search_title ) && $status == 'all') {
                        $result = $this->sample->get( '/me/videos?sort=relevance', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'search' => $search_title,
                            'limit' => ( $per_page )
                        ) );
                    } else if(empty( $search_title ) && $status == 'all') {
                        $result = $this->sample->get( '/me/videos?filters=creative-official', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'limit' => ( $per_page )
                        ) );
                    } else if ( empty( $search_title ) && $status != 'all') {
                        $result = $this->sample->get( '/me/videos?filters=creative-official', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'limit' => ( $per_page ),
                            'private' => $status
                        ) );
                    }
                    //print '<pre>'; print_r($result); print '</pre>';
                    if ( isset( $result['list'] ) && !empty( $result['list'] ) ) {
                        foreach ( $result['list'] as $key => $media ) {
                            $this->returndata['videos'][$key]['id'] = !empty( $media['id'] ) ? $media['id'] : null;
                            $this->returndata['videos'][$key]['title'] = !empty( $media['title'] ) ? $media['title'] : null;
                            $this->returndata['videos'][$key]['embed_url'] = !empty( $media['embed_url'] ) ? $media['embed_url'] : null;
                            $this->returndata['videos'][$key]['thumbnail_url'] = !empty( $media['thumbnail_url'] ) ? $media['thumbnail_url'] : null;
                            $this->returndata['videos'][$key]['description'] = !empty( $media['description'] ) ? $media['description'] : null;
                            $this->returndata['videos'][$key]['views_total'] = !empty( $media['views_total'] ) ? $media['views_total'] : 0;
                            $this->returndata['videos'][$key]['tags'] = !empty( $media['tags'] ) ? $media['tags'] : null;
                            $this->returndata['videos'][$key]['channel.name'] = !empty( $media['channel.name'] ) ? $media['channel.name'] : null;
                            $this->returndata['videos'][$key]['created_time'] = !empty( $media['created_time'] ) ? $media['created_time'] : null;
                            $this->returndata['videos'][$key]['duration'] = !empty( $media['duration'] ) ? $media['duration'] : null;
                            $this->returndata['videos'][$key]['owner.screenname'] = !empty( $media['owner.screenname'] ) ? $media['owner.screenname'] : null;
                            $this->returndata['videos'][$key]['private'] = !empty( $media['private'] ) ? $media['private'] : null;
                            $this->returndata['videos'][$key]['published'] = !empty( $media['published'] ) ? $media['published'] : null;
                        }
                        //$this->returndata['total_record'] = $result['total'];
                        $this->returndata['has_more'] = $result['has_more'];
                    }
                    return ( $this->returndata );
                }
                catch ( Exception $e ) {
                    $this->log_error( $e->getMessage() . ' in class.sample.php function name getSampleVideoList me video', 'phparray', $e->getLine(), $e->getFile() );
                }
                break;
            case 'commented':
                try {
                    if ( !empty( $search_title ) && $status != 'all') {
                        $result = $this->sample->get( '/me/videos?sort=relevance', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'limit' => ( $per_page ),
                            'search' => $search_title,
                            'sort' => 'commented',
                            'private' => $status,
                        ) );
                    } else if( !empty( $search_title ) && $status == 'all') {
                        $result = $this->sample->get( '/me/videos?sort=relevance', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'search' => $search_title,
                            'limit' => ( $per_page ),
                            'sort' => 'commented'
                        ) );
                    } else if(empty( $search_title ) && $status == 'all') {
                        $result = $this->sample->get( '/me/videos?filters=creative-official', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'limit' => ( $per_page ),
                            'sort' => 'commented'
                        ) );
                    } else if ( empty( $search_title ) && $status != 'all') {
                        $result = $this->sample->get( '/me/videos?filters=creative-official', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'limit' => ( $per_page ),
                            'private' => $status,
                            'sort' => 'commented'
                        ) );
                    }
                    if ( isset( $result['list'] ) && !empty( $result['list'] ) ) {
                        foreach ( $result['list'] as $key => $media ) {
                            $this->returndata['videos'][$key]['id'] = !empty( $media['id'] ) ? $media['id'] : null;
                            $this->returndata['videos'][$key]['title'] = !empty( $media['title'] ) ? $media['title'] : null;
                            $this->returndata['videos'][$key]['embed_url'] = !empty( $media['embed_url'] ) ? $media['embed_url'] : null;
                            $this->returndata['videos'][$key]['thumbnail_url'] = !empty( $media['thumbnail_url'] ) ? $media['thumbnail_url'] : null;
                            $this->returndata['videos'][$key]['description'] = !empty( $media['description'] ) ? $media['description'] : null;
                            $this->returndata['videos'][$key]['views_total'] = !empty( $media['views_total'] ) ? $media['views_total'] : 0;
                            $this->returndata['videos'][$key]['tags'] = !empty( $media['tags'] ) ? $media['tags'] : null;
                            $this->returndata['videos'][$key]['channel.name'] = !empty( $media['channel.name'] ) ? $media['channel.name'] : null;
                            $this->returndata['videos'][$key]['created_time'] = !empty( $media['created_time'] ) ? $media['created_time'] : null;
                            $this->returndata['videos'][$key]['duration'] = !empty( $media['duration'] ) ? $media['duration'] : null;
                            $this->returndata['videos'][$key]['owner.screenname'] = !empty( $media['owner.screenname'] ) ? $media['owner.screenname'] : null;
                            $this->returndata['videos'][$key]['private'] = !empty( $media['private'] ) ? $media['private'] : null;
                            $this->returndata['videos'][$key]['published'] = !empty( $media['published'] ) ? $media['published'] : null;
                        }
                        //$this->returndata['total_record'] = $result['total'];
                        $this->returndata['has_more'] = $result['has_more'];
                    }
                    return ( $this->returndata );
                }
                catch ( Exception $e ) {
                    $this->log_error( $e->getMessage() . ' in class.sample.php function name getSampleVideoList comented video', 'phparray', $e->getLine(), $e->getFile() );
                }
                break;
            case 'rated':
                try {
                    if ( !empty( $search_title ) && $status != 'all') {
                        $result = $this->sample->get( '/me/videos?sort=relevance', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'limit' => ( $per_page ),
                            'search' => $search_title,
                            'sort' => 'rated',
                            'private' => $status,
                        ) );
                    } else if( !empty( $search_title ) && $status == 'all') {
                        $result = $this->sample->get( '/me/videos?sort=relevance', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'search' => $search_title,
                            'limit' => ( $per_page ),
                            'sort' => 'rated'
                        ) );
                    } else if(empty( $search_title ) && $status == 'all') {
                        $result = $this->sample->get( '/me/videos?filters=creative-official', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'limit' => ( $per_page ),
                            'sort' => 'rated'
                        ) );
                    } else if ( empty( $search_title ) && $status != 'all') {
                        $result = $this->sample->get( '/me/videos?filters=creative-official', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'limit' => ( $per_page ),
                            'private' => $status,
                            'sort' => 'rated'
                        ) );
                    }
                    if ( isset( $result['list'] ) && !empty( $result['list'] ) ) {
                        foreach ( $result['list'] as $key => $media ) {
                            $this->returndata['videos'][$key]['id'] = !empty( $media['id'] ) ? $media['id'] : null;
                            $this->returndata['videos'][$key]['title'] = !empty( $media['title'] ) ? $media['title'] : null;
                            $this->returndata['videos'][$key]['embed_url'] = !empty( $media['embed_url'] ) ? $media['embed_url'] : null;
                            $this->returndata['videos'][$key]['thumbnail_url'] = !empty( $media['thumbnail_url'] ) ? $media['thumbnail_url'] : null;
                            $this->returndata['videos'][$key]['description'] = !empty( $media['description'] ) ? $media['description'] : null;
                            $this->returndata['videos'][$key]['views_total'] = !empty( $media['views_total'] ) ? $media['views_total'] : 0;
                            $this->returndata['videos'][$key]['tags'] = !empty( $media['tags'] ) ? $media['tags'] : null;
                            $this->returndata['videos'][$key]['channel.name'] = !empty( $media['channel.name'] ) ? $media['channel.name'] : null;
                            $this->returndata['videos'][$key]['created_time'] = !empty( $media['created_time'] ) ? $media['created_time'] : null;
                            $this->returndata['videos'][$key]['duration'] = !empty( $media['duration'] ) ? $media['duration'] : null;
                            $this->returndata['videos'][$key]['owner.screenname'] = !empty( $media['owner.screenname'] ) ? $media['owner.screenname'] : null;
                            $this->returndata['videos'][$key]['private'] = !empty( $media['private'] ) ? $media['private'] : null;
                            $this->returndata['videos'][$key]['published'] = !empty( $media['published'] ) ? $media['published'] : null;
                        }
                        //$this->returndata['total_record'] = $result['total'];
                        $this->returndata['has_more'] = $result['has_more'];
                    }
                    return ( $this->returndata );
                }
                catch ( Exception $e ) {
                    $this->log_error( $e->getMessage() . ' in class.sample.php function name getSampleVideoList rated video', 'phparray', $e->getLine(), $e->getFile() );
                }
                break;
            case 'visited':
                try {
                    if ( !empty( $search_title ) && $status != 'all') {
                        $result = $this->sample->get( '/me/videos?sort=relevance', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'limit' => ( $per_page ),
                            'search' => $search_title,
                            'sort' => 'visited',
                            'private' => $status,
                        ) );
                    } else if( !empty( $search_title ) && $status == 'all') {
                        $result = $this->sample->get( '/me/videos?sort=relevance', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'search' => $search_title,
                            'limit' => ( $per_page ),
                            'sort' => 'visited'
                        ) );
                    } else if(empty( $search_title ) && $status == 'all') {
                        $result = $this->sample->get( '/me/videos?filters=creative-official', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'limit' => ( $per_page ),
                            'sort' => 'visited'
                        ) );
                    } else if ( empty( $search_title ) && $status != 'all') {
                        $result = $this->sample->get( '/me/videos?filters=creative-official', array(
                             'fields' => $fields,
                            'page' => ( $page_no ),
                            'limit' => ( $per_page ),
                            'private' => $status,
                            'sort' => 'visited'
                        ) );
                    }
                    if ( isset( $result['list'] ) && !empty( $result['list'] ) ) {
                        foreach ( $result['list'] as $key => $media ) {
                            $this->returndata['videos'][$key]['id'] = !empty( $media['id'] ) ? $media['id'] : null;
                            $this->returndata['videos'][$key]['title'] = !empty( $media['title'] ) ? $media['title'] : null;
                            $this->returndata['videos'][$key]['embed_url'] = !empty( $media['embed_url'] ) ? $media['embed_url'] : null;
                            $this->returndata['videos'][$key]['thumbnail_url'] = !empty( $media['thumbnail_url'] ) ? $media['thumbnail_url'] : null;
                            $this->returndata['videos'][$key]['description'] = !empty( $media['description'] ) ? $media['description'] : null;
                            $this->returndata['videos'][$key]['views_total'] = !empty( $media['views_total'] ) ? $media['views_total'] : 0;
                            $this->returndata['videos'][$key]['tags'] = !empty( $media['tags'] ) ? $media['tags'] : null;
                            $this->returndata['videos'][$key]['channel.name'] = !empty( $media['channel.name'] ) ? $media['channel.name'] : null;
                            $this->returndata['videos'][$key]['created_time'] = !empty( $media['created_time'] ) ? $media['created_time'] : null;
                            $this->returndata['videos'][$key]['duration'] = !empty( $media['duration'] ) ? $media['duration'] : null;
                            $this->returndata['videos'][$key]['owner.screenname'] = !empty( $media['owner.screenname'] ) ? $media['owner.screenname'] : null;
                            $this->returndata['videos'][$key]['private'] = !empty( $media['private'] ) ? $media['private'] : null;
                            $this->returndata['videos'][$key]['published'] = !empty( $media['published'] ) ? $media['published'] : null;
                        }
                        //$this->returndata['total_record'] = $result['total'];
                        $this->returndata['has_more'] = $result['has_more'];
                    }
                    return ( $this->returndata );
                }
                catch ( Exception $e ) {
                    $this->log_error( $e->getMessage() . ' in class.sample.php function name getSampleVideoList visited video', 'phparray', $e->getLine(), $e->getFile() );
                }
                break;
        }
    }

    /**
     * Delete sample video by its video id
     */
    public function deleteSampleVideo( $video_id = null )
    {
        try {
            $result = $this->sample->delete( "/video/$video_id" );
        }
        catch ( Exception $e ) {
            $this->log_error( $e->getMessage() . ' in class.sample.php function name deleteSampleVideo', 'phparray', $e->getLine(), $e->getFile() );
        }
    }

    /**
     * Get sample video details
     */
    public function getSampleVideoDetail( $videoId = null, $fields = array( 'id', 'title', 'embed_url', 'channel', 'thumbnail_url', 'description', 'tags', 'private', 'type' ) )
    {
        try {
            $result = array( );
            $result = $this->sample->get( "/video/$videoId", array(
                 'fields' => $fields
            ) );
            return $result;
        }
        catch ( Exception $e ) {
            $this->log_error( $e->getMessage() . ' in class.sample.php function name getSampleVideoDetail', 'phparray', $e->getLine(), $e->getFile() );
        }
    }

    /**
     * Update sample video data
     */
    public function visitorCountry( )
    {
        $result = "";
        if ( @filter_var( $_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP ) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( @filter_var( $_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP ) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $ip_data = @json_decode( file_get_contents( "http://www.geoplugin.net/json.gp?ip=" . $ip ) );
        if ( $ip_data && $ip_data->geoplugin_countryCode != null ) {
            $result = $ip_data->geoplugin_countryCode;
        }
        return !empty( $result ) ? $result : 'us';
    }

    /**
     * Update sample video data
     */
    public function updateSampleVideoData( $videoId = null, $data = array( ) )
    {
        try {
            $result = $this->sample->post( "/video/$videoId", $data );
        }
        catch ( Exception $e ) {
            $this->log_error( $e->getMessage() . ' in class.sample.php function name updateSampleVideoData', 'phparray', $e->getLine(), $e->getFile() );
        }
    }

    /**
     * Update sample video data
     */
    public function getSampleConnectedInformation( )
    {
        $videoresult = $this->sample->get( '/me/videos', array(
             'fields' => array(
                 'id',
                'title',
                'created_time'
            ),
            'page' => 1,
            'limit' => 10
        ) );
        $userresult = $this->sample->get( '/user/me', array(
             'fields' => array(
                 'id',
                'screenname',
                'type',
                'avatar_120_url'
            )
        ) );
        $this->returndata['screenname'] = !empty( $userresult['screenname'] ) ? $userresult['screenname'] : 'screenname not found';
        $this->returndata['total_record'] = !empty( $videoresult['total'] ) ? $videoresult['total'] : 0;
        $this->returndata['user_photo'] = !empty( $userresult['avatar_120_url'] ) ? $userresult['avatar_120_url'] : '';
        $this->returndata['last_uploaded'] = !empty( $videoresult['list'][0]['created_time'] ) ? date( 'M d, Y', $videoresult['list'][0]['created_time'] ) : 'Not Found';
        return $this->returndata;
    }

    /**
     * Check user is official
     */
    public function getUserType( )
    {
        $userresult = $this->sample->get( '/user/me', array(
             'fields' => array(
                 'type'
            )
        ) );
        if ( !empty( $userresult['type'] ) && ( $userresult['type'] == 'official' ) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update sample video data
     */
    public function getDMAuthorizationUrl( $display = 'popup' )
    {
        return $this->sample->getAuthorizationUrl( $display );
    }

    /**
     * Upload sample video
     */
    public function uploadVideoOnSample( $testVideoFile = null, $video_title = 'Sample Video', $channel = null )
    {
        $url = $this->sample->uploadFile( $testVideoFile );
        if ( !empty( $channel ) ) {
            $media = $this->sample->post( '/videos', array(
                 'url' => $url,
                'title' => $video_title,
                'published' => true,
                'channel' => $channel
            ) );
        } else {
            $media = $this->sample->post( '/videos', array(
                 'url' => $url,
                'title' => $video_title,
                'published' => true
            ) );
        }

        return $media;
    }
}