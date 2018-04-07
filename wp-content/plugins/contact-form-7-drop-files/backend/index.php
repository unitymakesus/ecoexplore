<?php
class cf7_dropfiles_backend {
	function __construct(){
		//add_action("admin_enqueue_scripts",array($this,"add_lib"));
        add_action( 'wpcf7_init', array($this,'wpcf7_add_form_tag_file') );
        add_filter( 'wpcf7_form_enctype', array($this,'wpcf7_file_form_enctype_filter') );
        add_filter( 'wpcf7_validate_dropfiles', array($this,'wpcf7_file_validation_filter'), 10, 2 );
        add_filter( 'wpcf7_validate_dropfiles*', array($this,'wpcf7_file_validation_filter'), 10, 2 );
        add_action( 'wpcf7_admin_init', array($this,'wpcf7_add_tag_generator_file'), 50 );
        add_filter("wpcf7_mail_components",array($this,"cf7_add_files"),10,3);
        add_action( 'wp_ajax_cf7_dropfiles_remove', array($this,'cf7_dropfiles_remove') );
        add_action( 'wp_ajax_nopriv_cf7_dropfiles_remove', array($this,'cf7_dropfiles_remove') );
        add_filter( 'wpcf7_messages', array($this,'wpcf7_drop_messages') );

        add_action( 'wp_ajax_cf7_dropfiles_upload', array($this,'cf7_dropfiles_upload') );
        add_action( 'wp_ajax_nopriv_cf7_dropfiles_upload', array($this,'cf7_dropfiles_upload') );
	}

function wpse_183245_upload_dir( $dirs ) {
    $dirs['subdir'] = '/cf7-uploads-custom';
    $dirs['path'] = $dirs['basedir'] . '/cf7-uploads-custom';
    $dirs['url'] = $dirs['baseurl'] . '/cf7-uploads-custom';
    return $dirs;
}

function cf7_dropfiles_upload(){
    add_filter( 'upload_dir', array($this,'wpse_183245_upload_dir') );
    $file = @$_FILES["file"];

    $size = @$_REQUEST["size"];
    $type = @$_REQUEST["type"];

    $filename = sanitize_file_name(wpcf7_canonicalize( time()."-". $file["name"] ));

    $upload_overrides = array( 'test_form' => false );

    $allowed_type =  explode("|",$type);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $upload = true;
    if(!in_array($ext,$allowed_type) ) {
        wp_send_json( array("status"=>"not","text"=> __( 'Sorry, this file type is not permitted for security reasons.' ) ) );
        $upload = false;
    }
    if($_FILES['file']['size'] > $size)  {
        wp_send_json( array("status"=>"not","text"=>  __( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.' )) );
        $upload = false;
    }

    if( $upload ) {
        $upload_dir   = wp_upload_dir();
        $filename = wp_unique_filename( $upload_dir, $filename );

        $cf7_dirname = $upload_dir['basedir'].'/cf7-uploads-custom';
            if ( ! file_exists( $cf7_dirname ) ) {
                wp_mkdir_p( $cf7_dirname );
        }

        $movefile = @wp_handle_upload( $file, $upload_overrides );

        remove_filter( 'upload_dir', array($this,'wpse_183245_upload_dir') );
        if ( $movefile && ! isset( $movefile['error'] ) ) {
           $file_name = explode("/",$movefile["file"]);
             wp_send_json( array("status"=>"ok","text"=>$file_name[count($file_name) - 1] ) );
        } else {
            /**
             * Error generated by _wp_handle_upload()
             * @see _wp_handle_upload() in wp-admin/includes/file.php
             */
            wp_send_json( array("status"=>"not","text"=>$movefile['error'] ) );
        }

    }
}

function cf7_dropfiles_remove(){
    $name = $_POST["name"];
    $upload_dir = wp_upload_dir();
    $path_main = $upload_dir['basedir'] . '/cf7-uploads-custom/'.$name;
    if ( @is_readable( $path_main ) && @is_file( $path_main ) ) { 
        @unlink($path_main);
        echo "ok";
    }else{
        echo "not";
    }
}


function cf7_add_files( $components,$current , $form  ) {
    if ( $submission = WPCF7_Submission::get_instance() ) { 
        $data = $submission->get_posted_data();
        $upload_dir = wp_upload_dir();
        $attachments = $components['attachments'];
        $path_main = $upload_dir['basedir'] . '/cf7-uploads-custom/';
        foreach ($data as $key => $value) {
            if( preg_match("#dropfiles#",$key)) {
                $data = explode("|",$value);
                foreach ($data as $path) {
                    $path = $path_main.$path;
                    if ( @is_readable( $path ) && @is_file( $path ) ) {
                            $attachments[] = $path;
                    }
                }
                
            }
        }
        $components['attachments']= $attachments;
    }
    
    return $components;
}

function wpcf7_add_form_tag_file() {
    wpcf7_add_form_tag( array( 'dropfiles', 'dropfiles*' ),
        array($this,'wpcf7_file_form_tag_handler'), array( 'name-attr' => true ) );
}

function wpcf7_file_form_tag_handler( $tag ) {
    if ( empty( $tag->name ) ) {
        return '';
    }

    $validation_error = wpcf7_get_validation_error( $tag->name );

    $class = wpcf7_form_controls_class( $tag->type );

    if ( $validation_error ) {
        $class .= ' wpcf7-not-valid';
    }

    $atts = array();
    $class .=" cf7-drop-upload";
    $atts['size'] = $tag->get_size_option( '40' );
    $atts['class'] = $tag->get_class_option( $class );
    $atts['id'] = $tag->get_id_option();
    $atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

    if ( $tag->is_required() ) {
        $atts['aria-required'] = 'true';
    }

    $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

    $atts['type'] = 'hidden';
    $atts['name'] = $tag->name;

    $atts = wpcf7_format_atts( $atts );


    $allowed_file_types = array();

    if ( $file_types_a = $tag->get_option( 'filetypes' ) ) {
        foreach ( $file_types_a as $file_types ) {
            $file_types = explode( '|', $file_types );

            foreach ( $file_types as $file_type ) {
                $file_type = trim( $file_type, '.' );
                $file_type = str_replace( array( '.', '+', '*', '?' ),
                    array( '\.', '\+', '\*', '\?' ), $file_type );
                $allowed_file_types[] = $file_type;
            }
        }
    }

    $allowed_file_types = array_unique( $allowed_file_types );
    $file_type_pattern = implode( '|', $allowed_file_types );

    $allowed_size = 1048576; // default size 1 MB

    if ( $file_size_a = $tag->get_option( 'limit' ) ) {
        $limit_pattern = '/^([1-9][0-9]*)([kKmM]?[bB])?$/';

        foreach ( $file_size_a as $file_size ) {
            if ( preg_match( $limit_pattern, $file_size, $matches ) ) {
                $allowed_size = (int) $matches[1];

                if ( ! empty( $matches[2] ) ) {
                    $kbmb = strtolower( $matches[2] );

                    if ( 'kb' == $kbmb ) {
                        $allowed_size *= 1024;
                    } elseif ( 'mb' == $kbmb ) {
                        $allowed_size *= 1024 * 1024;
                    }
                }

                break;
            }
        }
    }

    $max = $tag->get_option( 'max', 'signed_int', true );
    /* File type validation */

    // Default file-type restriction
    if ( '' == $file_type_pattern ) {
        $file_type_pattern = 'jpg|jpeg|png|gif|pdf|doc|docx|ppt|pptx|odt|avi|ogg|m4a|mov|mp3|mp4|mpg|wav|wmv';
    }

    $abc = '<div class="cf7-dragandrophandler-container">
              <div class="cf7-dragandrophandler" data-type="'.$file_type_pattern.'" data-size="'.$allowed_size.'" data-max="'.$max.'">
                <div class="cf7-dragandrophandler-inner">
                    <div class="cf7-text-drop">'.__("Drag & Drop Files Here",CT7_DROPFILES_DOMAIN).'</div>
                    <div class="cf7-text-or">'.__("or",CT7_DROPFILES_DOMAIN).'</div>
                    <div class="cf7-text-browser"><a href="#">'.__("Browser Files",CT7_DROPFILES_DOMAIN).'</a></div>
                </div>
                <input type="file" class="input-uploads hidden" multiple>
            </div></div>';
    $html = sprintf(
        '<span class="wpcf7-form-control-wrap %1$s">'.$abc.'<input %2$s />%3$s</span>',
        sanitize_html_class( $tag->name ), $atts, $validation_error );

    return $html;
}


/* Encode type filter */



function wpcf7_file_form_enctype_filter( $enctype ) {
    $multipart = (bool) wpcf7_scan_form_tags( array( 'type' => array( 'dropfiles', 'dropfiles*' ) ) );

    if ( $multipart ) {
        $enctype = 'multipart/form-data';
    }

    return $enctype;
}


/* Validation + upload handling filter */


function wpcf7_file_validation_filter( $result, $tag ) {
    $name = $tag->name;
    $value = isset( $_POST[$name] )
        ? trim( wp_unslash( strtr( (string) $_POST[$name], "\n", " " ) ) )
        : '';

    if ( $tag->is_required() && '' == $value ) {
        $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
    }
    $min = $tag->get_option( 'min', 'signed_int', true );
    if(!$min) {
        $min = -1;
    }

    $max = $tag->get_option( 'max', 'signed_int', true );
    if(!$max) {
        $max = 999;
    }
    if($value == "") {
        $count = 0;
    }else{
        $count = count(explode("|",$value));
    }
    if( $count < $min) {
        $result->invalidate( $tag, wpcf7_get_message( 'invalid_file_min' ) );
    }
     if( $count > $max) {
        $result->invalidate( $tag, wpcf7_get_message( 'invalid_file_max' ) );
    }
    return $result;
}


/* Tag generator */

function wpcf7_drop_messages($messages){
    return array_merge( $messages, array(
        'invalid_file_min' => array(
            'description' => __( "Count files are smaller than the minimum allowed", CT7_DROPFILES_DOMAIN ),
            'default' => __( "Count files are smaller than the minimum allowed", CT7_DROPFILES_DOMAIN )
        ),

        'invalid_file_max' => array(
            'description' => __( "Count files are smaller than the maximum allowed", CT7_DROPFILES_DOMAIN ),
            'default' => __( "Count files are smaller than the maximum allowed", CT7_DROPFILES_DOMAIN )
        ),
    ) );
}

function wpcf7_add_tag_generator_file() {
    $tag_generator = WPCF7_TagGenerator::get_instance();
    $tag_generator->add( 'dropfiles', __( 'Drop files', 'contact-form-7' ),
        array($this,'wpcf7_tag_generator_file') );
}

function wpcf7_tag_generator_file( $contact_form, $args = '' ) {
        $args = wp_parse_args( $args, array() );
        $type = 'dropfiles';

        $description = __( "Generate a form-tag for a file uploading field. For more details, see %s.", 'contact-form-7' );

        $desc_link = wpcf7_link( __( 'https://contactform7.com/file-uploading-and-attachment/', 'contact-form-7' ), __( 'File Uploading and Attachment', 'contact-form-7' ) );

    ?>
    <div class="control-box">
    <fieldset>
    <legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

    <table class="form-table">
    <tbody>
        <tr>
        <th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
        <td>
            <fieldset>
            <legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
            <label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
            </fieldset>
        </td>
        </tr>

        <tr>
        <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
        <td><input readonly="readonly" type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" />
            <p class="description mail-tag"><strong>Name support</strong>: dropfiles-*****</p>
        </td>
        </tr>

        <tr>
        <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-limit' ); ?>"><?php echo esc_html( __( "File size limit (bytes)", 'contact-form-7' ) ); ?></label></th>
        <td><input type="text" name="limit" class="filesize oneline option" id="<?php echo esc_attr( $args['content'] . '-limit' ); ?>" /></td>
        </tr>

        <tr>
        <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-filetypes' ); ?>"><?php echo esc_html( __( 'Acceptable file types', 'contact-form-7' ) ); ?></label></th>
        <td><input type="text" name="filetypes" class="filetype oneline option" id="<?php echo esc_attr( $args['content'] . '-filetypes' ); ?>" /></td>
        </tr>

        <tr>
        <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Min files', 'contact-form-7' ) ); ?></label></th>
        <td><input type="text" name="min" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-min' ); ?>" /></td>
        </tr>
        
        <tr>
        <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Max files', 'contact-form-7' ) ); ?></label></th>
        <td><input type="text" name="max" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-max' ); ?>" /></td>
        </tr>

        <tr>
        <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
        <td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
        </tr>

        <tr>
        <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
        <td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
        </tr>

    </tbody>
    </table>
    </fieldset>
    </div>

    <div class="insert-box">
        <input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

        <div class="submitbox">
        <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
        </div>

        <br class="clear" />

        <p class="description mail-tag"><strong>Email</strong>: Automatic Attachments in the email. You don’t need to do anything!</p>
    </div>
    <?php
    }


}
new cf7_dropfiles_backend;