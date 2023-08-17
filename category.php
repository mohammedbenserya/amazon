<?php 
if(isset($_POST['url'])){
if(isset($_POST['ccheck']))
$str=$_POST['url'];
else
$str=$_POST['url'].$_POST['page'];

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
    $asins = $DOM->query('//div[@data-component-type="s-search-result"]');
    foreach($asins as $asin)
    $data[]=[$asin->getAttribute('data-asin')];
    /*$livre_prix=utf8_encode(str_replace('’',"'",utf8_decode($livre_prix)));
    $livre_prix=(substr($livre_prix,0,strlen($livre_prix)-3));*/
    
   if(isset($_POST['ccheck'])){
    $count = $DOM->query('//span[@aria-disabled="true"]');
    if(count($count)<2){
    $count = $DOM->query('//a[@class="s-pagination-item s-pagination-button"]');
    if (count($count)<2){
        echo '1';
    }
    else
    {
        echo $count[count($count)-1]->textContent;

    }
#s-pagination-item s-pagination-button
}else
    echo $count[count($count)-1]->textContent;
}
else{
    echo json_encode($data);

}


    
}
}
else
die();

