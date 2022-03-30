<?php namespace Celepar\Light\CentralSeguranca;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class User implements UserContract {


    private $authIdentifier;
    public $id;
    public $name;
    public $login;
    public $email;
    public $lastLogin;

    //Dados extras da Central do Cidadão
    public $idCidadao;
    public $rg;
    public $orgaoExpedidor;
    public $dtEmissaoRG;
    public $ufRg;
    public $cpf;
    public $dataCadastroUsuario;
    public $tentativasSenhaErrada;
    public $dataTrocaSenha;
    public $usuarioMainFrame;
    public $telefone;
    public $celular;
    public $dataHoraSenhaErrada;
    public $indicativoUsuarioAtivo;
    public $indicativoBloqueio;
    public $nmMae;
    public $dtNascimento;
    public $nivelConfiabilidadeCadastro;
    public $codSistemaNivelCadastroUsuario;
    public $groups;




    public function setAuthIdentifier($authIdentifier)
    {
        $this->authIdentifier = $authIdentifier;
        $this->id = $authIdentifier;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->authIdentifier;
    }

    /**
     * Alias
     * @return mixed
     */
    public function getId()
    {
        return $this->getAuthIdentifier();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return null;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return null;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        return null;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;
    }

    public function getLastLogin()
    {
        return null;
    }

    //DADOS RETORNADOS PELA CENTRAL DE SEGURANCA
    /**
     * @return mixed
     */
    public function getIdCidadao()
    {
        return $this->idCidadao;
    }
    /**
     * @param mixed $idCidadao
     */
    public function setIdCidadao($idCidadao)
    {
        $this->idCidadao = $idCidadao;
    }
    /**
     * @return mixed
     */
    public function getRg()
    {
        return $this->rg;
    }
    /**
     * @param mixed $rg
     */
    public function setRg($rg)
    {
        $this->rg = $rg;
    }
    /**
     * @return mixed
     */
    public function getOrgaoExpedidor()
    {
        return $this->orgaoExpedidor;
    }
    /**
     * @param mixed $orgaoExpedidor
     */
    public function setOrgaoExpedidor($orgaoExpedidor)
    {
        $this->orgaoExpedidor = $orgaoExpedidor;
    }
    /**
     * @return mixed
     */
    public function getDtEmissaoRG()
    {
        return $this->dtEmissaoRG;
    }
    /**
     * @param mixed $dtEmissaoRG
     */
    public function setDtEmissaoRG($dtEmissaoRG)
    {
        $this->dtEmissaoRG = $dtEmissaoRG;
    }
    /**
     * @return mixed
     */
    public function getUfRg()
    {
        return $this->ufRg;
    }
    /**
     * @param mixed $ufRg
     */
    public function setUfRg($ufRg)
    {
        $this->ufRg = $ufRg;
    }
    /**
     * @return mixed
     */
    public function getCpf()
    {
        return $this->cpf;
    }
    /**
     * @param mixed $cpf
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;
    }
    /**
     * @return mixed
     */
    public function getDataCadastroUsuario()
    {
        return $this->dataCadastroUsuario;
    }
    /**
     * @param mixed $dataCadastroUsuario
     */
    public function setDataCadastroUsuario($dataCadastroUsuario)
    {
        $this->dataCadastroUsuario = $dataCadastroUsuario;
    }
    /**
     * @return mixed
     */
    public function getTentativasSenhaErrada()
    {
        return $this->tentativasSenhaErrada;
    }
    /**
     * @param mixed $tentativasSenhaErrada
     */
    public function setTentativasSenhaErrada($tentativasSenhaErrada)
    {
        $this->tentativasSenhaErrada = $tentativasSenhaErrada;
    }
    /**
     * @return mixed
     */
    public function getDataTrocaSenha()
    {
        return $this->dataTrocaSenha;
    }
    /**
     * @param mixed $dataTrocaSenha
     */
    public function setDataTrocaSenha($dataTrocaSenha)
    {
        $this->dataTrocaSenha = $dataTrocaSenha;
    }
    /**
     * @return mixed
     */
    public function getUsuarioMainFrame()
    {
        return $this->usuarioMainFrame;
    }
    /**
     * @param mixed $usuarioMainFrame
     */
    public function setUsuarioMainFrame($usuarioMainFrame)
    {
        $this->usuarioMainFrame = $usuarioMainFrame;
    }
    /**
     * @return mixed
     */
    public function getTelefone()
    {
        return $this->telefone;
    }
    /**
     * @param mixed $telefone
     */
    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
    }
    /**
     * @return mixed
     */
    public function getCelular()
    {
        return $this->celular;
    }
    /**
     * @param mixed $celular
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;
    }
    /**
     * @return mixed
     */
    public function getDataHoraSenhaErrada()
    {
        return $this->dataHoraSenhaErrada;
    }
    /**
     * @param mixed $dataHoraSenhaErrada
     */
    public function setDataHoraSenhaErrada($dataHoraSenhaErrada)
    {
        $this->dataHoraSenhaErrada = $dataHoraSenhaErrada;
    }
    /**
     * @return mixed
     */
    public function getIndicativoUsuarioAtivo()
    {
        return $this->indicativoUsuarioAtivo;
    }
    /**
     * @param mixed $indicativoUsuarioAtivo
     */
    public function setIndicativoUsuarioAtivo($indicativoUsuarioAtivo)
    {
        $this->indicativoUsuarioAtivo = $indicativoUsuarioAtivo;
    }
    /**
     * @return mixed
     */
    public function getIndicativoBloqueio()
    {
        return $this->indicativoBloqueio;
    }
    /**
     * @param mixed $indicativoBloqueio
     */
    public function setIndicativoBloqueio($indicativoBloqueio)
    {
        $this->indicativoBloqueio = $indicativoBloqueio;
    }
    /**
     * @return mixed
     */
    public function getNmMae()
    {
        return $this->nmMae;
    }
    /**
     * @param mixed $nmMae
     */
    public function setNmMae($nmMae)
    {
        $this->nmMae = $nmMae;
    }
    /**
     * @return mixed
     */
    public function getDtNascimento()
    {
        return $this->dtNascimento;
    }
    /**
     * @param mixed $dtNascimento
     */
    public function setDtNascimento($dtNascimento)
    {
        $this->dtNascimento = $dtNascimento;
    }
    /**
     * @return mixed
     */
    public function getNivelConfiabilidadeCadastro()
    {
        return $this->nivelConfiabilidadeCadastro;
    }
    /**
     * @param mixed $nivelConfiabilidadeCadastro
     */
    public function setNivelConfiabilidadeCadastro($nivelConfiabilidadeCadastro)
    {
        $this->nivelConfiabilidadeCadastro = $nivelConfiabilidadeCadastro;
    }
    /**
     * @return mixed
     */
    public function getCodSistemaNivelCadastroUsuario()
    {
        return $this->codSistemaNivelCadastroUsuario;
    }
    /**
     * @param mixed $codSistemaNivelCadastroUsuario
     */
    public function setCodSistemaNivelCadastroUsuario($codSistemaNivelCadastroUsuario)
    {
        $this->codSistemaNivelCadastroUsuario = $codSistemaNivelCadastroUsuario;
    }

    //GRUPOS DO USUÁRIO, RETORNADOS PELO CentralSeguranca
    public function getGroups()
    {
        return $this->groups;
    }

    public function setGroup($groups)
    {
        $this->groups = $groups;
    }
}
