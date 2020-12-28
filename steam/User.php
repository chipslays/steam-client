<?php

namespace Steam;

class User
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Send report profile.
     * 
     * They have an inappropriate avatar picture
     *  $abuseType = 20
     *  $ingameAppID = null
     *  $abuseDescription = 'your text here' (required) 
     * 
     * They have an inappropriate profile name
     *  $abuseType = 21
     *  $ingameAppID = null
     *  $abuseDescription = 'your text here' (required)
     * 
     * They are posting inappropriate or offensive content
     *  $abuseType = 11
     *  $ingameAppID = null
     *  $abuseDescription = 'your text here' (required)
     * 
     * They are spamming, annoying or harassing me
     * --> They are spamming invites, advertisements or links
     *      $abuseType = 3
     *      $ingameAppID = null
     *      $abuseDescription = 'your text here' (required)
     * 
     * --> They are harassing myself or someone I know
     *      $abuseType = 9
     *      $ingameAppID = 753 (required)
     *      $abuseDescription = 'your text here' (required)
     * 
     * They are involved in theft, scamming, fraud or other malicious activity
     * --> They are impersonating me or someone I know
     *     $abuseType = 6
     *     $ingameAppID = null
     *     $abuseDescription = 'your text here' (required)
     * 
     * --> They are engaged in item theft or scamming
     *     $abuseType = 18
     *     $ingameAppID = null
     *     $abuseDescription = 'your text here' (required)
     * 
     * --> They are trying to steal my account or information
     *     $abuseType = 14
     *     $ingameAppID = null
     *     $abuseDescription = 'your text here' (required)
     *  
     * --> Their account seems to have been compromised
     *     $abuseType = 14
     *     $ingameAppID = null
     *     $abuseDescription = 'your text here' (required)
     * 
     * --> They are sending suspicious links
     *     $abuseType = 3
     *     $ingameAppID = null
     *     $abuseDescription = 'your text here' (required)
     * 
     * They are cheating in a game
     *  $abuseType = 10
     *  $ingameAppID = 753 (required)
     *  $abuseDescription = 'your text here' (required)
     * 
     * @param string $steamId Victim
     * @param integer $abuseType Type of abuse (cheater, spam, etc)
     * @param string $abuseDescription Text for Steam support
     * @param integer $appId 753 Steam, 730 CS:GO, 440 TF2, 570 Dota 2 
     * 
     * @link https://developer.valvesoftware.com/wiki/Steam_Application_IDs
     * 
     * @return boolean
     */
    public function sendReport($steamId, $abuseType = 3, $abuseDescription = null, $appId = null): bool
    {
        $params = [
            'sessionid' => $this->client->getSessionId(),
            'abuseID' => $steamId,
            'eAbuseType' => $abuseType,
            'abuseDescription' => $abuseDescription ?? 'He tried to scam me and my friends!!1!!11',
            'ingameAppID' => $appId,
            'l' => 'english',
            // 'json' => '1',
        ];

        $response = $this->client->request()->post('https://steamcommunity.com/actions/ReportAbuse/', $params);

        if (stripos($response, 'sorry') !== false || stripos($response, 'error')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Invite user to your group.
     *
     * @param string $groupId
     * @param string $steamId
     * @return array
     */
    public function inviteToGroup($groupId, $steamId): array
    {
        $params = [
            'sessionID' => $this->client->getSessionId(),
            'group' => $groupId,
            'invitee' => $steamId,
            'type' => 'groupInvite',
            'json' => '1',
            'l' => 'english',
        ];

        $response = $this->client->request()->post('https://steamcommunity.com/actions/GroupInvite/', $params);

        if ($response->get('results') == 'OK') {
            return ['ok' => true, 'response' => $response];
        } else {
            return ['ok' => false, 'response' => $response];
        }
    }
}
