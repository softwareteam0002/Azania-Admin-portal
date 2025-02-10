<?php

namespace App\Helper;

use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class Converter
{
    public static function arrayToXml(array $data): bool|string
    {
        // Create a new SimpleXMLElement instance with the specified root node
        $xmlData = new SimpleXMLElement('<?xml version="1.0"?><AgencyBanking></AgencyBanking>');

        // Loop through the array and convert to XML
        foreach ($data as $key => $value) {
            // Ensure the key is a valid XML element name
            $key = is_numeric($key) ? 'item' . $key : $key;

            // Add value to XML; if it's an array, convert to XML
            if (is_array($value)) {
                $subNode = $xmlData->addChild($key);
                foreach ($value as $subKey => $subValue) {
                    $subKey = is_numeric($subKey) ? 'item' . $subKey : $subKey;
                    $subNode->addChild($subKey, htmlspecialchars($subValue));
                }
            } else {
                $xmlData->addChild($key, htmlspecialchars($value));
            }
        }

        return $xmlData->asXML();
    }

    public static function XmlToArray($xmlContent)
    {
        try {
            // Load XML as an object
            $xmlObject = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOCDATA);

            // Check if the XML is valid
            if ($xmlObject === false) {
                throw new \Exception('Failed to parse XML.');
            }

            // Convert XML object to JSON, then decode to array
            $json = json_encode($xmlObject);
            $array = json_decode($json, true);

            return $array;
        } catch (\Exception $e) {
            // Handle errors (optional: log the error for debugging)
            Log::channel('agency')->error("XML to Array Conversion Error: " . $e->getMessage());
            return null; // or handle the error as needed
        }
    }


}
