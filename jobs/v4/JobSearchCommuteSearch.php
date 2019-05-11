<?php
/*
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/*
 * DO NOT EDIT! This is a generated sample ("RequestPagedAll",  "job_search_commute_search")
 */

// [START job_search_commute_search]
require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\Talent\V4beta1\JobServiceClient;
use Google\Cloud\Talent\V4beta1\CommuteFilter;
use Google\Cloud\Talent\V4beta1\CommuteMethod;
use Google\Cloud\Talent\V4beta1\JobQuery;
use Google\Cloud\Talent\V4beta1\RequestMetadata;
use Google\Protobuf\Duration;
use Google\Type\LatLng;

/** Search Jobs using commute distance */
function sampleSearchJobs($projectId, $tenantId)
{
    // [START job_search_commute_search_core]

    $jobServiceClient = new JobServiceClient();

    // $projectId = 'Your Google Cloud Project ID';
    // $tenantId = 'Your Tenant ID (using tenancy is optional)';
    $formattedParent = $jobServiceClient->tenantName($projectId, $tenantId);
    $domain = 'www.example.com';
    $sessionId = 'Hashed session identifier';
    $userId = 'Hashed user identifier';
    $requestMetadata = new RequestMetadata();
    $requestMetadata->setDomain($domain);
    $requestMetadata->setSessionId($sessionId);
    $requestMetadata->setUserId($userId);
    $commuteMethod = CommuteMethod::TRANSIT;
    $seconds = 1800;
    $travelDuration = new Duration();
    $travelDuration->setSeconds($seconds);
    $latitude = 37.422408;
    $longitude = 122.084068;
    $startCoordinates = new LatLng();
    $startCoordinates->setLatitude($latitude);
    $startCoordinates->setLongitude($longitude);
    $commuteFilter = new CommuteFilter();
    $commuteFilter->setCommuteMethod($commuteMethod);
    $commuteFilter->setTravelDuration($travelDuration);
    $commuteFilter->setStartCoordinates($startCoordinates);
    $jobQuery = new JobQuery();
    $jobQuery->setCommuteFilter($commuteFilter);

    try {
        // Iterate through all elements
        $pagedResponse = $jobServiceClient->searchJobs($formattedParent, $requestMetadata, ['customRankingInfo' => $customRankingInfo, 'orderBy' => $orderBy]);
        foreach ($pagedResponse->iterateAllElements() as $responseItem) {
            printf('Job summary: %s'.PHP_EOL, $responseItem->getJobSummary());
            printf('Job title snippet: %s'.PHP_EOL, $responseItem->getJobTitleSnippet());
            $job = $responseItem->getJob();
            printf('Job name: %s'.PHP_EOL, $job->getName());
            printf('Job title: %s'.PHP_EOL, $job->getTitle());
        }
    } finally {
        $jobServiceClient->close();
    }

    // [END job_search_commute_search_core]
}
// [END job_search_commute_search]

$opts = [
    'project_id::',
    'tenant_id::',
];

$defaultOptions = [
    'project_id' => 'Your Google Cloud Project ID',
    'tenant_id' => 'Your Tenant ID (using tenancy is optional)',
];

$options = getopt('', $opts);
$options += $defaultOptions;

$projectId = $options['project_id'];
$tenantId = $options['tenant_id'];

sampleSearchJobs($projectId, $tenantId);
