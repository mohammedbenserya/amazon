<?php 
if(isset($_GET['path'])){


    //Read the filename
    $filename = $_GET['path'];
    //Check the file exists or not
    if(file_exists($filename)) {
    
    //Define header information
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: 0");
    header('Content-Disposition: attachment; filename="'.basename($filename).'"');
    header('Content-Length: ' . filesize($filename));
    header('Pragma: public');
    
    //Clear system output buffer
    flush();
    
    //Read the size of the file
    readfile($filename);
    file_put_contents('products.json', []);

    //Terminate from the script
    die();
    }
    else{
    echo "File does not exist.";
    }
    

    
    }
    elseif(isset($_POST['asin'])){
$str='https://www.amazon.co.uk/dp/'.$_POST['asin'];

$curl = curl_init($str);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Accept-Language: en-US,en;q=0.9']);
curl_setopt($curl, CURLOPT_ENCODING, 'gzip, deflate, br');
curl_setopt($curl,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36');

$page =(curl_exec($curl));
curl_close($curl);

if(!empty($curl)) { //if any html is actually returned


    $DOM = new DOMDocument;
    libxml_use_internal_errors(true);
    $DOM->loadHTML($page);
    libxml_clear_errors();

    $DOM = new DOMXPath($DOM);
    #snippet json_encode($arr) https://www.amazon.fr/dp/225325763X livre_pages  /div[3]/span str_replace('(Auteur)','', }
    $data=[];
    $titre = $DOM->query('//*[@id="title"]');

 
        if ($titre->length > 0) {
            $titre = ($titre[0]->textContent);
           # echo json_encode(['rating' => $rating]);
        } else {
            $titre ='unavailable';
        }
    #$livre_auteur=$livre_auteur[0]->textContent;
    
    //$url_img_fullsize = $DOM->query('//img[@id="landingImage"]');
    //$url_img_fullsize=($url_img_fullsize[0]->getAttribute('src'));

    
    $url_img_fullsize = $DOM->query('//img[@id="landingImage"]');
        
        if ($url_img_fullsize->length > 0) {
            $url_img_fullsize = $url_img_fullsize[0]->getAttribute('src');
           # echo json_encode(['rating' => $rating]);
        } else {
            $url_img_fullsize ='unavailable';
        }

    #UPDATE `livres` SET `id`='[value-1]',`livre_url`='[value-2]',`livre_timestamp `='[value-3]',`livre_titre `='[value-4]',`livre_auteur `='[value-5]',`livre_isbn13 `='[value-6]',`url_img_fullsize `='[value-7]',`livre_pages `='[value-8]',`livre_editeur `='[value-9]',`livre_description `='[value-10]',`livre_prix `='[value-11]',`livre_date `='[value-12]' WHERE 1  bylineInfo a-price-range
    $description=$DOM->query('//div[@id="feature-bullets"]');
        
        if ($description->length > 0) {
            $description = ($description[0]->textContent);
           # echo json_encode(['rating' => $rating]);
        } else {
            $description ='unavailable';
        }

    $price=$DOM->query('//span[@class="a-offscreen"]')[0]->textContent;
    $price=(substr($price,2,strlen($price)));
    $brand=str_replace('Brand: ','',str_replace(" Store","",str_replace('Visit the ',"",$DOM->query('//a[@id="bylineInfo"]')[0]->textContent)));
    /*$price_range=$DOM->query('//span[@class="a-price-range"]');
    if (count($price_range)>0) 
    $price=$price_range[0]->textContent;*/
        // Assuming $DOM is your DOMDocument object
        $ratingElements = $DOM->query('//span[@id="acrPopover"]');
        
        if ($ratingElements->length > 0) {
            $rating = $ratingElements[0]->getAttribute('title');
           # echo json_encode(['rating' => $rating]);
        } else {
            $rating ='unavailable';
        }
    

    $ratingElementsNbr = $DOM->query('//span[@id="acrCustomerReviewText"]');
        
        if ($ratingElementsNbr->length > 0) {
            $nbr_ratings = $ratingElementsNbr[0]->textContent;
           # echo json_encode(['rating' => $rating]);
        } else {
            $nbr_ratings ='unavailable';
        }
    $data[] = [
        'title' => $titre,
        'image' => $url_img_fullsize,
        'description' => $description,
        'price' => ($price),
        'rating' => $rating,
        'brand' => $brand,
        'nbr rating' => $nbr_ratings,
        'is out of stock' => (empty($price) || is_null($price)) ? true : false
    ];
    if(isset($_POST['iscat'])){
        #$inp = file_get_contents('products.json');
        #$json = json_decode($inp,true);
        #$json[]=$data[0];
        #$jsonData = json_encode($json);
        #file_put_contents('products.json', $jsonData);
        echo json_encode($data);
    }
    else{
    #$jsonData = json_encode($data);
    #file_put_contents('products_'.$_POST['asin'].'.json', $jsonData);
    echo json_encode($data);}
    /*$livre_prix=utf8_encode(str_replace('’',"'",utf8_decode($livre_prix)));
    $livre_prix=(substr($livre_prix,0,strlen($livre_prix)-3));*/
    



   


    
}


}
else
die();