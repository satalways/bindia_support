<?php

function human_filesize( $bytes, $decimals = 2 ) {
    if ( $bytes < 1024 ) {
        return $bytes . ' B';
    }

    $factor = floor( log( $bytes, 1024 ) );

    return sprintf( "%.{$decimals}f ", $bytes / pow( 1024, $factor ) ) . [
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB'
        ][ $factor ];
}

function is_local() {
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? '';

    return in_array( $_SERVER['HTTP_HOST'], [
        'localhost',
        'localhost:8000',
        'localhost:8080'
    ] );
}

function my_ip() {
    if ( getenv( 'HTTP_CLIENT_IP' ) ) {
        $ip = getenv( 'HTTP_CLIENT_IP' );
    } elseif ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
        $ip = getenv( 'HTTP_X_FORWARDED_FOR' );
    } elseif ( getenv( 'HTTP_X_FORWARDED' ) ) {
        $ip = getenv( 'HTTP_X_FORWARDED' );
    } elseif ( getenv( 'HTTP_FORWARDED_FOR' ) ) {
        $ip = getenv( 'HTTP_FORWARDED_FOR' );
    } elseif ( getenv( 'HTTP_FORWARDED' ) ) {
        $ip = getenv( 'HTTP_FORWARDED' );
    } else {
        $ip = @$_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}

function user( $id ) {
    $id     = (int) $id;
    $Admins = new \App\Admins();

    return $Admins::find( $id );
}
