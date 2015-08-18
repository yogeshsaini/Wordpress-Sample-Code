 <?php
include_once( "CloudKey.php" );

class Sample_Cloud_OwnMethod extends ErrorReporting
{
                
                //Variable for sample cloud
                protected $dmc_uid;
                protected $dmc_api_key;
                
                //For create instance of daliymotion cloud
                protected $cloudkey;
                
                //For retun data
                public $returndata = array( );
                
                function __construct( $dmc_uid = NULL, $dmc_api_key = NULL )
                {
                                $this->dmc_uid     = $dmc_uid;
                                $this->dmc_api_key = $dmc_api_key;
                                
                                if ( !empty( $this->dmc_uid ) && !empty( $this->dmc_api_key ) ) {
                                                $this->cloudkey = new CloudKey( $this->dmc_uid, $this->dmc_api_key );
                                } else {
                                                throw new InvalidArgumentException( 'Invalide credential in api parameter.' );
                                }
                                return $this;
                }
                
                /**
                 * Method to sp_update sample video
                 */
                public function get_sample_cloud_userInfo( )
                {
                                $res = $this->cloudkey->user->info( array(
                                                 'fields' => array(
                                                                 'id' 
                                                ) 
                                ) );
                                return isset( $res->id ) ? $res->id : null;
                }
                
                /**
                 * Method to sp_update sample video
                 */
                public function get_connected_Information( )
                {
                                $result = array( );
                                try {
                                                $userdata                = $this->cloudkey->user->info( array(
                                                                 "fields" => array(
                                                                                 "id",
                                                                                "username",
                                                                                "email",
                                                                                "first_name",
                                                                                "last_name" 
                                                                ) 
                                                ) );
                                                $mediadata               = $this->cloudkey->media->list( array(
                                                                 'fields' => array(
                                                                                 'id' 
                                                                ) 
                                                ) );
                                                $lastuploaded            = $this->cloudkey->media->search( array(
                                                                 "fields" => array(
                                                                                 "created" 
                                                                ),
                                                                "query" => "SORT:-created",
                                                                "page" => 1,
                                                                "per_page" => 1 
                                                ) );
                                                $result['udata']         = $userdata->first_name;
                                                $result['mdata']         = $mediadata->total;
                                                $result['last_uploaded'] = ( isset( $lastuploaded->list[0]->created ) ) ? date( 'M d, Y', $lastuploaded->list[0]->created ) : 'No video uploaded yet.';
                                                return $result;
                                }
                                catch ( Exception $e ) {
                                                $this->log_error( $e->getMessage() . ' in class.cloud.php function name get_connected_Information', 'phparray', $e->getLine(), $e->getFile() );
                                }
                }
                
                /**
                 * Method to sp_update sample video
                 */
                public function delete_sample_cloud_media( $media_id = NULL )
                {
                                try {
                                                if ( !empty( $media_id ) ) {
                                                                $res = $this->cloudkey->media->delete( array(
                                                                                 'id' => $media_id 
                                                                ) );
                                                } else {
                                                                
                                                }
                                }
                                catch ( Exception $e ) {
                                                $this->log_error( $e->getMessage() . ' in class.cloud.php function name delete_sample_cloud_media', 'phparray', $e->getLine(), $e->getFile() );
                                }
                }
                
                /**
                 * Method to sp_update sample video
                 */
                public function get_sample_cloud_player( )
                {
                                try {
                                                $result = array( );
                                                $res    = $this->cloudkey->player_preset->list();
                                                if ( isset( $res ) ) {
                                                                foreach ( $res as $players ) {
                                                                                $result[$players->id] = $players->name;
                                                                }
                                                                return $result;
                                                }
                                }
                                catch ( Exception $e ) {
                                                $this->log_error( $e->getMessage() . ' in class.cloud.php function name get_sample_cloud_player', 'phparray', $e->getLine(), $e->getFile() );
                                }
                }
                
                /**
                 * Method to sp_update sample video
                 */
                public function get_sample_cloud_channels( )
                {
                                try {
                                                return array(
                                                                 'news' => 'News &amp; Politics',
                                                                'fun' => 'Funny',
                                                                'shortfilms' => 'Film &amp; TV',
                                                                'music' => 'Music',
                                                                'auto' => 'Auto-Moto',
                                                                'travel' => 'Travel',
                                                                'creation' => 'Arts',
                                                                'videogames' => 'Gaming',
                                                                'webcam' => 'Webcam &amp; Vlogs',
                                                                'sport' => 'Sports &amp; Extreme',
                                                                'animals' => 'Animals',
                                                                'people' => 'People &amp; Family',
                                                                'tech' => 'Tech &amp; Science',
                                                                'school' => 'College',
                                                                'lifestyle' => 'Life &amp; Style',
                                                                'latino' => 'Latino',
                                                                'gaylesbian' => 'Gay &amp; Lesbian',
                                                                'sexy' => 'Sexy',
                                                                'tv' => 'TV' 
                                                );
                                }
                                catch ( Exception $e ) {
                                                $this->log_error( $e->getMessage() . ' in class.cloud.php function name get_sample_cloud_channels', 'phparray', $e->getLine(), $e->getFile() );
                                }
                }
                
                /**
                 * Method to sp_update sample video
                 */
                public function sp_get_sample_cloud_videos( $page_no = 1, $per_page = 10, $search_title = '', $filter = '-created' )
                {
                                $result = array( );
                                try {
                                                if ( !empty( $search_title ) ) {
                                                                $search_where = 'meta.title:' . $search_title;
                                                } else {
                                                                $search_where = '';
                                                }
                                                $res = $this->cloudkey->media->search( array(
                                                                 'fields' => array(
                                                                                 'id',
                                                                                'meta.title',
                                                                                'created',
                                                                                'stats.global.total',
                                                                                'assets.jpeg_thumbnail_medium.stream_url',
                                                                                //'assets.jpeg_thumbnail_source.stream_url',
                                                                                'assets.source.duration' 
                                                                ),
                                                                "query" => "$search_where SORT:$filter",
                                                                'page' => ( $page_no ) ? $page_no : 1,
                                                                'per_page' => ( $per_page ) ? $per_page : 10 
                                                ) );
                                                
                                                if ( isset( $res->list ) && !empty( $res->list ) ) {
                                                                foreach ( $res->list as $key => $media ) {
                                                                                $expires   = time() + 3600 * 24 * 7;
                                                                                $embed_url = $this->cloudkey->media->get_embed_url( array(
                                                                                                 'id' => $media->id,
                                                                                                'expires' => $expires 
                                                                                ) );
                                                                                
                                                                                $result['videos'][$key]['title']      = !empty( $media->meta->title ) ? $media->meta->title : null;
                                                                                $result['videos'][$key]['media_id']   = !empty( $media->id ) ? $media->id : null;
                                                                                $result['videos'][$key]['stream_url'] = !empty( $media->assets->jpeg_thumbnail_medium->stream_url ) ? $media->assets->jpeg_thumbnail_medium->stream_url : null;
                                                                                $result['videos'][$key]['embed_url']  = !empty( $embed_url ) ? $embed_url : null;
                                                                                $result['videos'][$key]['created']    = !empty( $media->created ) ? date( 'M d, Y', $media->created ) : null;
                                                                                $result['videos'][$key]['total_view'] = !empty( $media->stats->global->total ) ? $media->stats->global->total : 0;
                                                                                $result['videos'][$key]['duration']   = !empty( $media->assets->source->duration ) ? $media->assets->source->duration : null;
                                                                }
                                                                $result['total_record'] = $res->total;
                                                                $result['total_pages']  = $res->pages;
                                                }
                                                
                                                return ( $result );
                                }
                                catch ( Exception $e ) {
                                                $this->log_error( $e->getMessage() . ' in class.cloud.php function name sp_get_sample_cloud_videos', 'phparray', $e->getLine(), $e->getFile() );
                                }
                }
                
                /**
                 * Method to sp_update sample video
                 */
                public function sp_get_sample_cloud_videosDetails( $media_id = null )
                {
                                $result = array( );
                                try {
                                                if ( !empty( $media_id ) ) {
                                                                $res = $this->cloudkey->media->info( array(
                                                                                 'fields' => array(
                                                                                                 'id',
                                                                                                'created',
                                                                                                'assets.jpeg_thumbnail_source.stream_url' 
                                                                                ),
                                                                                'id' => $media_id 
                                                                ) );
                                                                
                                                                $expires   = time() + 3600 * 24 * 7;
                                                                $embed_url = $this->cloudkey->media->get_embed_url( array(
                                                                                 'id' => $media_id,
                                                                                'expires' => $expires 
                                                                ) );
                                                                $metadata  = $this->cloudkey->media->get_meta( array(
                                                                                 'id' => $media_id 
                                                                ) );
                                                                
                                                                $result['media_id']   = $media_id;
                                                                $result['embed_url']  = !empty( $embed_url ) ? $embed_url : null;
                                                                $result['stream_url'] = !empty( $res->assets->jpeg_thumbnail_source->stream_url ) ? $res->assets->jpeg_thumbnail_source->stream_url : null;
                                                                
                                                                if ( isset( $metadata ) && !empty( $metadata ) ) {
                                                                                foreach ( $metadata as $key => $val ) {
                                                                                                if ( !in_array( $key, array(
                                                                                                                 'sharing_key',
                                                                                                                'explicit',
                                                                                                                'channel',
                                                                                                                'author' 
                                                                                                ) ) ) {
                                                                                                                $result['meta'][$key] = $val;
                                                                                                }
                                                                                }
                                                                }
                                                                return $result;
                                                } else {
                                                                
                                                }
                                }
                                catch ( Exception $e ) {
                                                $this->log_error( $e->getMessage() . ' in class.cloud.php function name sp_get_sample_cloud_videosDetails', 'phparray', $e->getLine(), $e->getFile() );
                                }
                }
                
                /**
                 * Method to sp_update sample video
                 */
                public function sp_get_sample_cloud_videosKeywords( $media_id = null )
                {
                                try {
                                                if ( !empty( $media_id ) ) {
                                                                
                                                                $metadata = $this->cloudkey->media->get_meta( array(
                                                                                 'id' => $media_id 
                                                                ) );
                                                                if ( isset( $metadata ) && !empty( $metadata ) ) {
                                                                                foreach ( $metadata as $key => $val ) {
                                                                                                if ( in_array( $key, array(
                                                                                                                 'keywords' 
                                                                                                ) ) ) {
                                                                                                                return $val;
                                                                                                }
                                                                                }
                                                                } else {
                                                                                return '';
                                                                }
                                                                
                                                } else {
                                                                
                                                }
                                }
                                catch ( Exception $e ) {
                                                $this->log_error( $e->getMessage() . ' in class.cloud.php function name sp_get_sample_cloud_videosKeywords', 'phparray', $e->getLine(), $e->getFile() );
                                }
                }
                
                /**
                 * Method to sp_update sample video
                 */
                public function set_sample_cloud_video_metas( $media_id = null, $args = array( ) )
                {
                                try {
                                                if ( !empty( $media_id ) && !empty( $args ) ) {
                                                                $metadata = $this->cloudkey->media->set_meta( array(
                                                                                 'id' => $media_id,
                                                                                'meta' => $args 
                                                                ) );
                                                                return $metadata;
                                                } else {
                                                                
                                                }
                                }
                                catch ( Exception $e ) {
                                                $this->log_error( $e->getMessage() . ' in class.cloud.php function name set_sample_cloud_video_metas', 'phparray', $e->getLine(), $e->getFile() );
                                }
                }
                
                /**
                 * Method to sp_update sample video
                 */
                public function remove_sample_cloud_video_metas( $media_id = null, $args = array( ) )
                {
                                try {
                                                if ( !empty( $media_id ) && !empty( $args ) ) {
                                                                $metadata = $this->cloudkey->media->remove_meta( array(
                                                                                 'id' => $media_id,
                                                                                'keys' => $args 
                                                                ) );
                                                } else {
                                                                
                                                }
                                }
                                catch ( Exception $e ) {
                                                $this->log_error( $e->getMessage() . ' in class.cloud.php function name remove_sample_cloud_video_metas', 'phparray', $e->getLine(), $e->getFile() );
                                }
                }
                
                /**
                 * Method to sp_update sample video
                 */
                public function set_sample_cloud_video_thumbnail( $media_id = null, $url = NULL )
                {
                                try {
                                                if ( !empty( $media_id ) && !empty( $url ) ) {
                                                                $metadata = $this->cloudkey->media->set_thumbnail( array(
                                                                                 'id' => $media_id,
                                                                                'url' => $url 
                                                                ) );
                                                } else {
                                                                
                                                }
                                }
                                catch ( Exception $e ) {
                                                $this->log_error( $e->getMessage() . ' in class.cloud.php function name set_sample_cloud_video_thumbnail', 'phparray', $e->getLine(), $e->getFile() );
                                }
                }
                
                /**
                 * Method to sp_update sample video
                 */
                public function upload_sample_cloud_video( $video_url = null, $file_name = null )
                {
                                if ( !empty( $video_url ) && file_exists( $video_url ) && !empty( $file_name ) ) {
                                                $res        = $this->cloudkey->file->upload_file( $video_url );
                                                $source_url = $res->url;
                                                $assets     = array(
                                                                 'mp4_h264_aac',
                                                                'mp4_h264_aac_hq',
                                                                'jpeg_thumbnail_medium',
                                                                'jpeg_thumbnail_source' 
                                                );
                                                $meta       = array(
                                                                 'title' => $file_name 
                                                );
                                                $media      = $this->cloudkey->media->create( array(
                                                                 'assets_names' => $assets,
                                                                'meta' => $meta,
                                                                'url' => $source_url 
                                                ) );
                                                return $media;
                                }
                }
                
                /**
                 * Create new user on cloud when someone register
                 */
                public function create_new_user_on_organigation( $user_id = null, $user_login = null, $email_id = null )
                {
                                try {
                                                $userdata = $this->cloudkey->user->set( array(
                                                                 'id' => null,
                                                                'fields' => array(
                                                                                 "org_id" => "53a46cba9473995cf2633a0c",
                                                                                "username" => $user_login,
                                                                                "password" => $email_id,
                                                                                "email" => $email_id 
                                                                ) 
                                                ) );
                                                return $userdata;
                                }
                                catch ( Exception $e ) {
                                                $error = $this->log_error( $e->getMessage(), 'phparray', $e->getLine(), $e->getFile() );
                                                return $error;
                                }
                }
}

?> 