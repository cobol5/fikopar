<?php

/*
* PHP SAMPLE TEMPLATE
*/

/*
* APP MAIN FUNCTION
* Called each second after the function last time finished
* Should not take longer than max 20 seconds to execute
* ELSE you should call NTServiceResponder(); at least each 20 secs
* WARNING: NTServiceResponder will exit; if there was a STOP request
*/
function Service_Main()
{
    // YOU APPLICATION CODE HERE !!!
    echo "my service\n";
    sleep(1); // dummy something
}

/********************************************************************
*
* SERVICE CONTROLLING
*
********************************************************************/

$SERVICE_NAME = "phptestservice";
$SERVICE_DISPLAY = "Test Service with PHP 2";

// so u can get: $SERVICE_PATH_PARTS["dirname"] $SERVICE_PATH_PARTS["basename"]  $SERVICE_PATH_PARTS["extension"]
$SERVICE_PATH_PARTS = pathinfo(__FILE__);

$SERVICE_PARAMS = " run";

    if (!isset($argv[1]))
    {
        die("this application need to be installed as a service.\n run with param install");
    }

    if ($argv[1] == 'install')
    {
        $x = win32_create_service(array(
                                        'service' => $SERVICE_NAME,
                                        'display' => $SERVICE_DISPLAY,
                                        'params' =>  __FILE__ . $SERVICE_PARAMS,
                                        //'path' =>  $SERVICE_PATH_PARTS["dirname"] . '\php.exe'
                                        ));
        debug_zval_dump($x);
        exit;
    }
    else if ($argv[1] == 'uninstall')
    {
        $x = win32_delete_service('dummyphp');
        debug_zval_dump($x);
        exit;
    }
    else if ($argv[1] != 'run')
    {
        die("bogus args, needs to run as service");
    }

    // Connect to service dispatcher and notify that startup was successful
    if (!win32_start_service_ctrl_dispatcher($SERVICE_NAME)) die('Could not connect to service :'.$SERVICE_NAME);
    win32_set_service_status(WIN32_SERVICE_RUNNING);

    // Main Server Loop
    while (1)
    {
        NTServiceResponder();

        // Main script goes here
        Service_Main();

        sleep(1); // at least 1 sec delay per loop
    }
    win32_set_service_status(WIN32_SERVICE_STOPPED);

/*
* Response to NTServiceRequests
*/
function NTServiceResponder()
{

    switch (win32_get_last_control_message())
    {
    case 0: // PATCH for: seems never to go to 4 (WIN32_SERVICE_CONTROL_INTERROGATE)
        win32_set_service_status(WIN32_SERVICE_RUNNING);
        return TRUE;
        break;
    case WIN32_SERVICE_CONTROL_CONTINUE:
        return TRUE; // "Continue"
    case WIN32_SERVICE_CONTROL_INTERROGATE:
        win32_set_service_status(WIN32_SERVICE_RUNNING);
        return TRUE; // Respond with status
    case WIN32_SERVICE_CONTROL_STOP:
        win32_set_service_status(WIN32_SERVICE_STOPPED);
        exit; // Terminate script
    default:
        win32_set_service_status(WIN32_ERROR_CALL_NOT_IMPLEMENTED); // Add more cases to handle other service calls
    }

    return FALSE;
}

?>