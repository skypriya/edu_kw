<?php
 namespace App\Controller\Component;
 use Cake\Controller\Component;
 use Aws\S3\S3Client;

 class AmazonComponent extends Component{
    public $config = null;
    public $s3 = null;

    public function initialize(array $config){
        parent::initialize($config);
        $this->config = [
           's3' => [
               'key' => 'DFYEKXA6LKEEM5RHTDLH',
               'secret' => 'Ge0uR5SV3QKqOS1QctIXgxNZvkxGfG9I3Dl1qwIwRg8',
               'bucket' => 'akcess',
               'endpoint' => 'sgp1.digitaloceanspaces.com',
           ]
        ];
        $this->s3 = S3Client::factory([
            'credentials' => [
               'key' => $this->config['s3']['key'],
               'secret' => $this->config['s3']['secret']
            ],
        'region' => 'us-east-1',
        'version' => 'latest'
        ]);
    }
}