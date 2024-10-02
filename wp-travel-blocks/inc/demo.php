<?php

add_action( 'rest_api_init', 'wptravel_demo_import_api' );

function wptravel_demo_import_api() {
    register_rest_route(
        'wptraveldemo/v1',
        '/import/(?P<fileUrl>\w+)',
        array(
            'methods'             => 'POST',
            'permission_callback' => '__return_true',
            'callback'            => 'wptravel_import_demo',
        )
    );

    register_rest_route(
        'wptraveldemo/v1',
        '/get_demo_list',
        array(
            'methods'             => 'GET',
            'permission_callback' => '__return_true',
            'callback'            => 'wptravel_get_demo_list',
        )
    );
}

function wptravel_import_demo( $request ) {
    $fileUrl = str_replace( '_', '-', $request->get_param( 'fileUrl' ) );

    try {
        if( class_exists( 'WPCF7' ) ) {
            $demo_import = new WP_Demo_Import();
            $demo_import->import( 'https://wpdemo.wensolutions.com/demo-data/data/' . $fileUrl . '.xml' );
            return $fileUrl;
        } else {
            return 'missing_wpcf7';
        }
    } catch(Exception $ex) {
        return $ex;
    }
}

function wptravel_get_demo_list( $request ) {
    
    $get_data_lists = file_get_contents( 'https://wpdemo.wensolutions.com/demo-data/demo.json' );

    $data_lists = array();
    foreach( json_decode($get_data_lists) as $data ){
        $data_list['demo_name'] = $data->demo_name;
        $data_list['text_domain'] = $data->text_domain;
        $data_list['screenshot'] = $data->screenshot;

        array_push( $data_lists, $data_list );
    }

    return $data_lists;
}

