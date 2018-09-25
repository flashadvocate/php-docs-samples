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
 * For instructions on how to run the full sample:
 *
 * @see https://github.com/GoogleCloudPlatform/php-docs-samples/tree/master/speech/README.md
 */

namespace Google\Cloud\Samples\Speech;

# [START speech_transcribe_model_selection]
use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionConfig\AudioEncoding;

/**
 * Transcribe the given audio file synchronously with the selected model
 */
function transcribe_auto_punctuation($path)
{
    // get contents of a file into a string
    $handle = fopen($path, 'r');
    $content = fread($handle, filesize($path));
    fclose($handle);

    // set string as audio content
    $audio = new RecognitionAudio();
    $audio->setContent($content);

    // set config
    $config = new RecognitionConfig();
    $config->setEncoding(AudioEncoding::LINEAR16);
    $config->setSampleRateHertz(32000);
    $config->setLanguageCode('en-US');
    $config->setEnableAutomaticPunctuation(true);

    // create the speech client
    $client = new SpeechClient();

    // Make the API call
    $response = $client->recognize($config, $audio);
    $results = $response->getResults();

    // print results
    foreach ($results as $result) {
        $alternatives = $result->getAlternatives();
        $mostLikely = $alternatives[0];
        $transcript = $mostLikely->getTranscript();
        $confidence = $mostLikely->getConfidence();
        printf('Transcript: %s' . PHP_EOL, $transcript);
        printf('Confidence: %s' . PHP_EOL, $confidence);
    }

    $client->close();
}
# [END speech_transcribe_model_selection]