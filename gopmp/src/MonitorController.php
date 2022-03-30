<?php
namespace Celepar\Gopmp;

use Config;
use Exception;
use Artisan;
use Response;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class MonitorController extends \App\Http\Controllers\Controller
{

    private $openGOPBuilder;
    private $artisanPath;
    private $timeoutDefault;

    public function __construct()
    {
        $this->openGOPBuilder = new OpenGOPBuilder('1.0', 'UTF-8');
        $appTitle = Config::get('layout.browserTitle', 'App sem título');
        if (is_array($appTitle)) {
            $appTitle = 'App sem título';
        }
        $this->openGOPBuilder->setTitle("Monitoramento Aplicação {$appTitle}");

        $this->artisanPath = base_path('artisan');
        $this->timeoutDefault = Config::get('gopmp::timeout', 10);
    }


    public function check($monitor=null)
    {
        if (! $this->isIpAllowed(\Request::ip()) ) {
            return 'Ip not allowed';
        }

        $commands = Artisan::all();

        foreach ($commands as $command) {
            if ( \Illuminate\Support\Str::startsWith($command->getName(), 'gopmp:') ) {
                $this->checkCommand($command);
            }
        }

        return $this->makeXmlResponse();
    }


    private function checkCommand($command)
    {
        $artisaCommandLine = "php {$this->artisanPath} {$command->getName()}";

        $timeout = property_exists($command, 'timeout') ? $command->timeout : $this->timeoutDefault;

        $process = new Process($artisaCommandLine, null, null, null, $timeout);

        try {
            $process->run();
            $msgErro = $process->getOutput();

            if (is_null($msgErro) || empty($msgErro)) {
                $testValue = true;
            } else {
                $testValue = false;
            }

        } catch (ProcessTimedOutException $e) {
            $testValue = false;
            $msgErro = "Timeout: sem resposta em {$timeout} segundos.";
        }

        $this->openGOPBuilder->addItem($command->getName(), $command->getDescription(), $testValue, $msgErro);
    }

    private function makeXmlResponse()
    {
        return Response::make($this->openGOPBuilder->printXML(), 200, ['Content-Type'=>'application/xml']);
    }


    private function isIpAllowed($ip)
    {
        $ipsAllowed = Config::get('gopmp.ips_allowed', []);

        foreach ($ipsAllowed as $idx=>$ipAllowed) {
            if ($ip == $ipAllowed) {
                return true;
            } else if ( strpos($ip, $ipAllowed) !== false ) {
                return true;
            }
        }

        return false;
        //desabilitando o exception
        //throw new Exception("Ip ($ip) não permitido para execução do monitor.");
    }

}
