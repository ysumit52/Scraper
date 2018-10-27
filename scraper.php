<?php
include "simple_html_dom.php";
 ini_set('max_execution_time', 30000);

$candidateDetails = array();
function getData($url,$candID,$candidateDetails)
{
   
    $ch = curl_init();
    $links = array();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    $html = new simple_html_dom();
    $html->load($response);
    if($html) {
        $plaintext = $html->plaintext;
        if (strpos($plaintext, 'Page Not Found')) {
            //echo 'Failed: '.$candID.'<br>';
        } else {
            $candidateDetails[$candID] = array();
            $i = 0;
            //echo /*'Passed: '.*/$candID.'<br>';
            $candDetails[0] = "Candidate MNID";
            $candDetails[1] = $candID;
            // var_dump($candDetails);
            // exit();
            $candidateDetails[$candID][$i] = $candDetails[1];
            $i++;

            // Candidate name
            foreach($html->find('.main-title') as $e) {
                //echo $e->plaintext . '<br>';
                $candDetails[0] = "Candidate Name";
                $candDetails[1] = strip_tags(trim($e->innertext));
            }
            // var_dump($candDetails);
            // exit();
            $candidateDetails[$candID][$i] = $candDetails[1];
            $i++;
            // Candidate constituency
            foreach($html->find('h5') as $e) {
                //echo $e->innertext . '<br>';
                $candDetails[0] = "Place of Contest";
                $candDetails[1] = trim($e->innertext);
            }
            //var_dump($candDetails);
            $candidateDetails[$candID][$i] = $candDetails[1];
            $i++;
            foreach($html->find('div.grid_2') as $e) {
                //echo $e->innertext . '<br>';
                $candDetails = $e->innertext;
                $candDetails = explode("<br>", $candDetails);
                foreach ($candDetails as $key => $cand) {
                    $candDetails[$key] = explode("</b>", trim($cand));

                    foreach ($candDetails[$key] as $key1 => $cand1) {
                        if ($key1==0) {
                            $candDetails[$key1] = substr($cand1, 3, -1);
                           // var_dump($candDetails);
                        } else {
                            $candDetails[$key1] = trim($cand1);
                            $candidateDetails[$candID][$i] = trim($cand1);
                            $i++;
                        }
                    }
                }
                
            }

            //echo "============================================";
        }
        //$candID++;
        /*$num_fields = mysql_num_fields($res);
        $headers = array(); 
        for ($i = 0; $i < $num_fields; $i++) 
        {     

             $headers[] = mysql_field_name($res , $i); 
        }*/
        //var_dump($html);
        /*foreach($html->find('main-title') as $e) 
            echo $e->plaintext . '<br>';*/
        /*$containers = $html->find('div.mapbox div.mapbox-text strong.street-address address.address');
        foreach($containers as $container) {
            $comments = $container->find('address.address span');
            $item = new stdClass();
            foreach($comments as $comment) {
                $address.= $comment->itemprop; //append the content of each span
            }
            echo $address;

            $getphone = $container->find('span.biz-phone');
            $phone = $getphone->itempro;
        }   

        $Imgcontainers = $html->find('div.js-photo photo photo-1 div.showcase-photo-box img.a la beverly sills');
        echo $Imgcontainers->img;*/
    }
    if ($candID==3500) {
        exportCSV($candidateDetails);
        // print_r($candidateDetails);
    }
    return $candidateDetails;
}
function exportCSV($candidateDetails) {     
    $fp = fopen('php://output', 'w');
    if ($fp) 
    {   
        $headers = array(
            0 => 'MYNETAID',
            1 => 'Name',
            2 => 'Place of contest',
            3 => 'Party',
            4 => 'S/o|D/o|W/o',
            5 => 'Age',
            6 => 'Address',
            7 => 'Email',
            8 => 'Contact Number',
        );
         header('Content-Type: text/csv; charset="UTF-8"');
         header('Content-Disposition: attachment; filename="export.csv"');
         header('Pragma: no-cache');    
         header('Expires: 0');
         fputcsv($fp, $headers); 
         foreach ($candidateDetails as $key => $row) {
            fputcsv($fp, array_values($row)); 
         }
      //die; 
    }
}
for ($i=3000; $i <= 3500; $i++) { 
    //$url = 'http://www.xxxx.info/xxxx2017/candidate.php?candidate_id='.$i.'';
    $url = 'http://www.xxxx.info/xxx2014/candidate.php?candidate_id='.$i.'';
    $candidateDetails = getData($url,$i,$candidateDetails);
}
?>