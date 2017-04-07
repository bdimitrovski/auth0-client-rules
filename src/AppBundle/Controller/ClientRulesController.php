<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClientRulesController extends Controller
{
    const AUDIENCE = 'https://bojandimitrovski.eu.auth0.com/api/v2/';
    const DOMAIN = 'bojandimitrovski.eu.auth0.com';
    const CLIENT_ID = 'dtIcPX2tNg8bQqvUm0zlNtgEXUrW2NDF';
    const CLIENT_SECRET = 'XiKo7JGbFoZRQn8H3c8ojGMNoNGkccJkK1o5F5LxExRdVGlxQ7a6QnWSPJVPTlJN';
    const GRANT_TYPE = 'client_credentials';

    /**
     * @Route("/clients/rules")
     */
    public function showClientsAndRules()
    {

        // Get the token and obtain client data from the /api/v2/clients endpoint.
        $base_url = 'https://' . urlencode(self::DOMAIN);
        $urlParams = [
            'client_id=' . urlencode(self::CLIENT_ID),
            'client_secret=' . urlencode(self::CLIENT_SECRET),
            'audience=' . urlencode(self::AUDIENCE),
            'grant_type=' . urlencode(self::GRANT_TYPE),
        ];
        $encoded = implode('&', $urlParams);
        $url = $base_url . '/oauth/token';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $encoded,
        ]);
        $result = curl_exec($curl);
        $oauth = json_decode($result);
        $access_token = $oauth->access_token;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $base_url . '/api/v2/clients',
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json',
            ],
        ]);

        $result = curl_exec($curl);
        $res = json_decode($result);

        $clients = [];

        foreach ($res as $data) {
            $clients[] = [
                'name' => $data->name,
                'id' => $data->client_id,
                'rules' => []
            ];
        }

        // Get the rules from the /api/v2/rules endpoint
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $base_url . '/api/v2/rules',
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json',
            ],
        ]);

        $result = curl_exec($curl);
        $res = json_decode($result);

        // Perform rule check based on clientID or name and render all rules for all clients
       // and rules that apply to some clients only
        foreach ($clients as $key => $client) {
            foreach ($res as $rule) {
                if (
                    strpos($rule->script, $client['name']) !== false ||
                    strpos($rule->script, $client['id']) !== false
                ){
                    $clients[$key]['rules'][] = $rule->name;
                }
            }
        }

        $allRules = array_map(function($r) { return $r->name; }, $res);
        $specificRules = array_reduce($clients, function($memo, $c) {
            return array_merge($memo, $c['rules']);
        }, []);

        $rules_for_all_clients = array_unique(array_diff($allRules, $specificRules));

        // Pass the data to the template
        return $this->render('rules/list.html.twig', array(
            'clients' => $clients,
            'all_clients' => $rules_for_all_clients
        ));
    }
}
