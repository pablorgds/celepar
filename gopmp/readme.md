uso:

Instalar o pacote celepar/gopmp
# composer require celepar/gopmp

Adicionar o ServiceProvider (app/config/app.php)
'Celepar\Gopmp\GopmpServiceProvider',

Criar um comando:
Eg:
#php artisan make:console MonitorBancoDeDados
 
Registrar este comando em app/Console/Kernel.php
 Eg:

 protected $commands = [
     \App\Console\Commands\MonitorBancoDeDados::class
 ];


- O nome do comando deve iniciar com 'gopmp'
 Eg:
 protected $signature = 'gopmp:database';


 - Deve existir um descrição:
 Eg:
 protected $description = 'Verificar o banco de dados da aplicação php.celepar';
 
 
 - Seu teste deve estar no método handle
 - Caso o teste seja bem sucedido, este método NÃO deve retornar nenhum valor.
 - Caso o teste falhe, você deve retornar uma string com alguma informação
 Eg:
 	
 	public function handle()
 	{
         try {
             if ( ! \DB::statement('select version();') ) {
                 $this->info('Erro executando monitoramento no banco de dados.');
             }
         }
         catch (Exception $e) {
             $this->info('Erro executando monitoramento no banco de dados:' . $e->getMessage());
         }
 	}

 
Após os passos acima, você poderá executar o comando:
# php artisan gopmp:database

A URL de execução dos testes será:
http://www.foo.com/gopmp

Basta cadastrar esta URL no Gopmp.