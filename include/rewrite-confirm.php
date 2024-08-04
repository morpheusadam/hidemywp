<?PHP
    
    header('Content-Type: application/json');
    
    $data   =   array(
                         "name"          => "nsp-code/wp-hide-pro",
                         "description"   => "Hide and increase Security for your WordPress website instance using smart techniques. No files are changed on your server.",
    
                        );
    
    echo json_encode($data);