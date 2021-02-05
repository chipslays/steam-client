<?php

namespace Steam;

class Market
{
    /**
     * @var Client
     */
    private $client;

    public const CANT_TRADE = 1;
    public const CAN_TRADE = 2;
    public const GUARD_7_DAYS_BAN = 3;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get balance.
     *
     * @return array
     */
    public function getBalance()
    {
        $response = $this->client->request()->get('https://steamcommunity.com/market/', ['l' => 'english']);

        $pattern = '/<span id="marketWalletBalanceAmount">(.+?)<\/span>/';
        preg_match_all($pattern, $response, $matches);

        if (empty($matches[1]) || sizeof($matches[1]) == 0) {
            return false;
        }

        $rawBalance = trim($matches[1][0]);
        $cleanBalance = (float) filter_var(str_ireplace(',', '.', $rawBalance), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        return [
            'raw' => $rawBalance,
            'clean' => $cleanBalance,
        ];
    }

    /**
     * Can trade.
     *
     * @return int 
     */
    public function canTrade()
    {
        $response = $this->client->request()->get('https://steamcommunity.com/market/', ['l' => 'english']);

        if (stripos($response, 'market_warning_header') !== false) {
            if (stripos($response, 'Steam Guard for 7 days') !== false) {
                return self::GUARD_7_DAYS_BAN;
            }
            return self::CANT_TRADE;
        } else {
            return self::CAN_TRADE;
        }
    }

    /**
     * Accept offer.
     *
     * @param array $offer
     * @return void
     */
    public function acceptOffer($offer)
    {
        $url = 'https://steamcommunity.com/tradeoffer/' . $offer['tradeofferid'] . '/accept';
        $referer = 'https://steamcommunity.com/tradeoffer/' . $offer['tradeofferid']  . '/';
        $params = [
            'sessionid' => $this->client->getSessionId(),
            'serverid' => '1',
            'tradeofferid' => $offer['tradeofferid'],
            'partner' => Util::toCommunityID($offer['accountid_other'])
        ];

        $request = $this->client->request();
        $request->setReferrer($referer);
        $response = $request->post($url, $params);
        print_r($response);
        die;
    }
}
