<?php
class ConexionSupabase {
    private $url = "https://cpkhclrlomqkatteemtu.supabase.co/rest/v1";
    private $apikey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImNwa2hjbHJsb21xa2F0dGVlbXR1Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTc2MjcwNzcsImV4cCI6MjA3MzIwMzA3N30.h1gcYcjUj60eqQW27DFvXXuC3q0TjWT5cwy2O_5hBbk";

    public function request($method, $endpoint, $data = null) {
        $url = $this->url . $endpoint;

        $options = [
            "http" => [
                "header" => "Content-Type: application/json\r\n" .
                            "apikey: {$this->apikey}\r\n" .
                            "Authorization: Bearer {$this->apikey}\r\n",
                "method" => $method
            ]
        ];

        if ($data) {
            $options["http"]["content"] = json_encode($data);
        }

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            return ["error" => "Error en la petición a Supabase"];
        }

        return json_decode($result, true);
    }
}
