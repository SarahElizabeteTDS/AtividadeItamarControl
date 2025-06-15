<?php
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

class Creator 
{
    private $con;
    private $servidor;
    private $banco;
    private $usuario;
    private $senha;


    function __construct() 
    {
        $this->servidor=$_POST["servidor"];
        $this->banco=$_POST["banco"];
        $this->usuario=$_POST["usuario"];
        $this->senha=$_POST["senha"];
        $this->conectar();
    }

    function conectar() 
    {
        try 
        {
            $this->con = new PDO
            (
                "mysql:host=" . $this->servidor . ";dbname=" . $this->banco,
                $this->usuario,
                $this->senha
            );
        } catch (Exception $e) 
        {
            echo "Erro ao conectar com o Banco de dados: " . $e->getMessage();
        }
    }

    function criarClassesConexao()
    {
        $conteudo = <<<EOT
        <?php

        class Conexao
        {
            private \$server;
            private \$banco;
            private \$usuario;
            private \$senha;

            function __construct()
            {
                \$this->server = "[INFORME O SERVIDOR]";
                \$this->banco = "[INFORME O BANCO]";
                \$this->usuario = "[INFORME O USUARIO]";
                \$this->senha = "[INFORME A SENHA]";
            }

            function conectar()
            {
                try
                {
                    \$con = new PDO("mysql:host=" . \$this->server . ";dbname=" . \$this->banco, \$this->usuario, \$this->senha);
                    return \$con;
                }catch(Exception \$e)
                {
                    print "Erro ao conectar com o banco de dados: " . \$e->getMessage();
                }
            }
        }
        EOT;
        file_put_contents("sistema/model/Conexao.php", $conteudo);
    }


    function ClassesControl()
    {
        fileExists("control"); 

        $sql = "SHOW TABLES";
        $query = $this->con->query($sql);
        $tabelas = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tabelas as $tabela) 
        {
            $tabelaNome = array_values((array) $tabela)[0];
            $tabelaNomeM = ucfirst($tabelaNome);
        }

        $content = <<<EOT
        <?php
        require_once ("../model/{$tabelaNomeM}.php");
        require_once ("../dao/{$tabelaNomeM}Dao.php");

        class {$tabelaNomeM}Control
        {
            private \${$tabelaNome};
            private \$acao;
            private \$dao;

            public function __construct()
            {
                \$this->{$tabelaNome}=new {$tabelaNomeM}();
                \$this->dao=new {$tabelaNomeM}Dao();
                \$this->acao=\$_GET["a"];
                \$this->verificaAcao();
            }

            function verificaAcao()
            {}
            function inserir()
            {}
            function excluir()
            {}
            function alterar()
            {}
            function buscarId({$tabelaNomeM} \${$tabelaNome})
            {}
            function buscaTodos()
            {}

        }

        new {$tabelaNomeM}Control();

        EOT;
        file_put_contents("sistema/control/{$tabelaNomeM}Control.php", $content);
    }

    function ClassesModel() 
    {
        fileExists("model");

        $this->criarClassesConexao();
        $sql = "SHOW TABLES";
        $query = $this->con->query($sql);
        $tabelas = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tabelas as $tabela) 
        {
            $nomeTabela = array_values((array) $tabela)[0];
            $sql="show columns from ".$nomeTabela;
            $atributos = $this->con->query($sql)->fetchAll(PDO::FETCH_OBJ);
            $nomeAtributos="";
            $geters_seters="";
            foreach ($atributos as $atributo) 
            {
                $atributo=$atributo->Field;
                $nomeAtributos.="\tprivate \${$atributo};\n";
                $metodo=ucfirst($atributo);
                $geters_seters.="\tfunction get".$metodo."()\n\t{\n";
                $geters_seters.="\t\treturn \$this->{$atributo};\n\t}\n\n";
                $geters_seters.="\tfunction set".$metodo."(\${$atributo})\n\t{\n";
                $geters_seters.="\t\t\$this->{$atributo}=\${$atributo};\n\t}\n\n";
            }
            $nomeTabela=ucfirst($nomeTabela);
            $conteudo = <<<EOT
            <?php
            class {$nomeTabela} 
            {
            {$nomeAtributos}
            {$geters_seters}
            }
            ?>
            EOT;
            file_put_contents("sistema/model/{$nomeTabela}.php", $conteudo);
        }
    }
}

function fileExists($file)
{
    if (!file_exists("sistema")) 
    {
        mkdir("sistema",0777);
    }

    if (!file_exists("sistema/". $file)) 
    {
        mkdir("sistema/". $file,0777);
    }
}

(new Creator())->ClassesControl();
(new Creator())->ClassesModel();
