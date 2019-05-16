<?php

/**
 * Copyright 2018 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * For instructions on how to run the samples:
 *
 * @see https://github.com/GoogleCloudPlatform/php-docs-samples/tree/master/dlp/README.md
 */

// Include Google Cloud dependendencies using Composer
require_once __DIR__ . '/../vendor/autoload.php';

if (count($argv) < 5 || count($argv) > 6) {
    return print("Usage: php reidentify_fpe.php PROJECT_ID STRING KEY_NAME WRAPPED_KEY [SURROGATE_TYPE]\n");
}
list($_, $projectId, $string, $keyName, $wrappedKey) = $argv;
$surrogateType = isset($argv[5]) ? $argv[5] : '';

# [START dlp_reidentify_fpe]
/**
 * Reidentify a deidentified string using Format-Preserving Encryption (FPE).
 */
use Google\Cloud\Dlp\V2\CryptoReplaceFfxFpeConfig;
use Google\Cloud\Dlp\V2\CryptoReplaceFfxFpeConfig\FfxCommonNativeAlphabet;
use Google\Cloud\Dlp\V2\CryptoKey;
use Google\Cloud\Dlp\V2\DlpServiceClient;
use Google\Cloud\Dlp\V2\PrimitiveTransformation;
use Google\Cloud\Dlp\V2\KmsWrappedCryptoKey;
use Google\Cloud\Dlp\V2\InfoType;
use Google\Cloud\Dlp\V2\InspectConfig;
use Google\Cloud\Dlp\V2\InfoTypeTransformations\InfoTypeTransformation;
use Google\Cloud\Dlp\V2\InfoTypeTransformations;
use Google\Cloud\Dlp\V2\ContentItem;
use Google\Cloud\Dlp\V2\CustomInfoType;
use Google\Cloud\Dlp\V2\DeidentifyConfig;
use Google\Cloud\Dlp\V2\CustomInfoType\SurrogateType;

/** Uncomment and populate these variables in your code */
// The project ID to run the API call under
// $projectId = 'YOUR_PROJECT_ID';

// The string to reidentify
// $string = 'My SSN is PHONE_TOKEN(9):#########';

// The name of the Cloud KMS key used to encrypt (wrap) the AES-256 key
// $keyName = 'projects/YOUR_GCLOUD_PROJECT/locations/YOUR_LOCATION/keyRings/YOUR_KEYRING_NAME/cryptoKeys/YOUR_KEY_NAME'

// The encrypted ('wrapped') AES-256 key to use
// This key should be encrypted using the Cloud KMS key specified above
// $wrappedKey = 'YOUR_ENCRYPTED_AES_256_KEY';

// The name of the surrogate custom info type to use when reidentifying data
// $surrogateType = 'SOME_INFO_TYPE_DEID';

// Instantiate a client.
$dlp = new DlpServiceClient();

// The infoTypes of information to mask
$ssnInfoType = (new InfoType())
    ->setName('US_SOCIAL_SECURITY_NUMBER');
$infoTypes = [$ssnInfoType];

// The set of characters to replace sensitive ones with
// For more information, see https://cloud.google.com/dlp/docs/reference/rest/v2/organizations.deidentifyTemplates#ffxcommonnativealphabet
$commonAlphabet = FfxCommonNativeAlphabet::NUMERIC;

// Create the wrapped crypto key configuration object
$kmsWrappedCryptoKey = (new KmsWrappedCryptoKey())
    ->setWrappedKey(base64_decode($wrappedKey))
    ->setCryptoKeyName($keyName);

// Create the crypto key configuration object
$cryptoKey = (new CryptoKey())
    ->setKmsWrapped($kmsWrappedCryptoKey);

// Create the surrogate type object
$surrogateType = (new InfoType())
    ->setName($surrogateType);

$customInfoType = (new CustomInfoType())
    ->setInfoType($surrogateType)
    ->setSurrogateType(new SurrogateType());

// Create the crypto FFX FPE configuration object
$cryptoReplaceFfxFpeConfig = (new CryptoReplaceFfxFpeConfig())
    ->setCryptoKey($cryptoKey)
    ->setCommonAlphabet($commonAlphabet)
    ->setSurrogateInfoType($surrogateType);

// Create the information transform configuration objects
$primitiveTransformation = (new PrimitiveTransformation())
    ->setCryptoReplaceFfxFpeConfig($cryptoReplaceFfxFpeConfig);

$infoTypeTransformation = (new InfoTypeTransformation())
    ->setPrimitiveTransformation($primitiveTransformation);

$infoTypeTransformations = (new InfoTypeTransformations())
    ->setTransformations([$infoTypeTransformation]);

// Create the inspect configuration object
$inspectConfig = (new InspectConfig())
    ->setCustomInfoTypes([$customInfoType]);

// Create the reidentification configuration object
$reidentifyConfig = (new DeidentifyConfig())
    ->setInfoTypeTransformations($infoTypeTransformations);

$item = (new ContentItem())
    ->setValue($string);

$parent = $dlp->projectName($projectId);

// Run request
$response = $dlp->reidentifyContent($parent, [
    'reidentifyConfig' => $reidentifyConfig,
    'inspectConfig' => $inspectConfig,
    'item' => $item
]);

// Print the results
$reidentifiedValue = $response->getItem()->getValue();
print($reidentifiedValue);
# [END dlp_reidentify_fpe]
