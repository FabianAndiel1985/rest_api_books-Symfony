<?php


namespace App\Service;

class UpdateProduct
{
    public function updateObjectValues($requestObj, $dbObject) {

        foreach($requestObj as $key => $value) {
            if(!empty($value)) {
                if($key == "title" ) {
                    $dbObject->setTitle($value);
                }
                if($key == "description" ) {
                    $dbObject->setDescription($value);
                }
                if($key == "author" ) {
                    $dbObject->setAuthor($value);
                }
                if($key == "isbn" ) {
                    $dbObject->setIsbn($value);
                }
                if($key == "price"){
                    $dbObject->setPrice($value);
                }
                if($key == "available") {
                    $dbObject->setAvailable($value);
                }
            }
        }
        return $dbObject;
    } 
}