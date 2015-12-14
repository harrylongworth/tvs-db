<?php
/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();


/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new \Slim\Slim();
$app->response->headers->set('Content-Type', 'application/json');
// $body = $app->request->getBody();
/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */

// GET route

$app->get(
    '/',
    function () {
        $template = <<<EOT
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8"/>
            <title>Slim Framework for PHP 5</title>
        </head>
        <body>
            <h1>Welcome to Slim!</h1>
            <p>Hello World!</p>
            <p>APIs include: fslist, crud, test</p>
 
        </body>
    </html>
EOT;
        echo $template;
    }
);




// POST route

$app->post(
    '/post',
    function () {
        // echo 'This is a POST route';

// Helpful reference:
        // http://coenraets.org/blog/2011/12/restful-services-with-jquery-php-and-the-slim-framework/
// key change being need to use \Slim\Slim:: not just Slim::
    $request = \Slim\Slim::getInstance()->request();
    // $data = json_encode($request->getBody());
    $data = $request->getBody();

    $allPostVars = json_encode($request->post());

    $file="post.json";

    $fp = fopen($file, 'w+');
    if (!$fp) { 
                        exit("Couldn't open file
                        "); 
                    }
    fwrite($fp,$allPostVars);
    fclose($fp);

    echo "Posted \n";

    echo $data;
    
    }
); //  END POST

$app->get(
    '/get',
    function () {
        $mydata = file_get_contents("test.json");
        print $mydata;
    }
);
// PUT route
$app->put(
    '/put',
    function () {
        echo 'This is a PUT route';
    }
);

// PATCH route
$app->patch('/patch', function () {
    echo 'This is a PATCH route';
});

// DELETE route
$app->delete(
    '/delete',
    function () {
        echo 'This is a DELETE route';
    }
);

/*  My APIs */

$app->get('/hello/:name', function ($name) {
    echo "Hello, " . $name;
});

$app->get('/fslist/:foldername', function ($foldername) {

    $targetpath = "../$foldername";

    $testexists = file_exists($targetpath);



    $rootdir = preg_replace( '~(\w)$~' , '$1' . DIRECTORY_SEPARATOR , realpath( $targetpath) )."*";

     /*
    foreach(glob($rootdir, GLOB_ONLYDIR) as $dir)  
        { 
            $dir = basename($dir); 
            echo "Directory: $dir <br>";
            $imgText = $dir."/icon.png";
            $imgTest = file_exists( $imgText);
                if ($imgTest)
                    {
                        echo '<a href="'.$dir.'"><img class="mybutton" alt="'.$dir.'" src="'.$imgText.'" /></a>';
                    }   
                else 
                    {
                        // Icon provided so use the default
                        echo '<a href="'.$dir.'"><img class="mybutton" alt="'.$dir.'" src="default.png" /></a>';
                    }
        } // END foreach
    */

    if ($testexists) {
        $folderarray = array();

        foreach(glob($rootdir, GLOB_ONLYDIR) as $dir)  
            { 
                $dir = basename($dir); 
                // echo "Directory: $dir <br>";
                $imgText = $dir.DIRECTORY_SEPARATOR."icon.png";
                $imgTest = file_exists( $targetpath.DIRECTORY_SEPARATOR.$imgText);
                $url = $targetpath.DIRECTORY_SEPARATOR.$dir;
                $smallname = substr($dir,0,13);
                if ($imgTest)
                    {
                        $imgText=$targetpath.DIRECTORY_SEPARATOR.$imgText;
                    }   
                else 
                    {
                        // Icon not provided so use the default
                        $imgText="default.png"; 
                    }


                $folderarray[]=array("name"=>$dir,"smallname"=>$smallname,"image"=>$imgText,"url"=>$url );
            } // END foreach

        /*
        echo "<h2>Array Contents</h2>";
        var_dump($folderarray);
        */

        print '{"items":';
        print json_encode($folderarray);
        print '}';

    } else {

        print "NULL";
    } // END else
    


}); // END get fslist

/*
$app->get('/payloads', function () {
    echo "Payloads";
});

$app->get('/payloads/:ptype', function ($ptype) {
    echo "Type: $ptype\n";
});
*/

$app->get('/payloads(/:ptype(/:payload))', function ($ptype="no",$payload="no") {
    
    // echo "Type: $ptype\n";
    // echo "Payload: $payload\n";


    $payloadsarray[]=array("type"=>$ptype,"payload"=>$payload);
        print '{"payloads":';
        print json_encode($payloadsarray);
        print '}';

});

$app->get('/crud(/:ptype(/:payload(/:dataset(/:id))))', function ($ptype="no",$payload="no",$dataset="no",$id="no") {
    /*
    echo "Type: $ptype\n";
    echo "Payload: $payload\n";
    echo "dataset: $dataset\n";
    echo "id: $id\n";
    */

    $crudarray[]=array("type"=>$ptype,"payload"=>$payload,"dataset"=>$dataset,"id"=>$id);
    
    $data='{"crud":'.json_encode($crudarray).'}';

    print $data;

    $file="test.json";

    $fp = fopen($file, 'w+');
    if (!$fp) { 
                        exit("Couldn't open file
                        "); 
                    }
    fwrite($fp,$data);
    fclose($fp);


   // $result = file_put_content($file, $data);
    // which should work doesn't!!!

    
// echo "END";
   

}); // END get

$app->post('/crud(/:ptype(/:payload(/:dataset(/:id))))', function ($ptype="no",$payload="no",$dataset="no",$id="no") {
    /*
    echo "Type: $ptype\n";
    echo "Payload: $payload\n";
    echo "dataset: $dataset\n";
    echo "id: $id\n";
    */

    $crudarray[]=array("type"=>$ptype,"payload"=>$payload,"dataset"=>$dataset,"id"=>$id);

    $data='{"crud":'.json_encode($crudarray).'}';

    print $data;

    $file="test.json";

    $fp = fopen($file, 'w+');
    if (!$fp) { 
                        exit("Couldn't open file
                        "); 
                    }
    fwrite($fp,$data);
    fclose($fp);

}); // END post


/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
