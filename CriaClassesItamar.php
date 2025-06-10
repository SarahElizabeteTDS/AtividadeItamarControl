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

    //CLASSES DAO METADE FEITAS

    // function ClassesDAO()
    // {
    //     if (!file_exists("sistema")) 
    //     {
    //         mkdir("sistema");
    //         if (!file_exists("sistema/DAO"))
    //             mkdir("sistema/DAO");
    //     }
    //     $sql = "SHOW TABLES";
    //     $query = $this->con->query($sql);
    //     $tabelas = $query->fetchAll(PDO::FETCH_ASSOC);
        
    //     foreach($tabelas as $tabela)
    //     {
    //         $nomeTabela = array_values((array) $tabela)[0];
            
    //         $nomeTabela = ucfirst($nomeTabela);
    //         $conteudo = <<<EOT
    //         <?php
    //         class {$nomeTabela}DAO
    //         {
    //             private \$con;

    //             function __construct()
    //             {
    //                 include_once("Conexao.php");
    //                 \$conexao = new Conexao();
    //                 \$this->con = \$conexao->conectar();}
    //         }
    //         EOT;
    //     }
    // }

    function ClassesModel() 
    {
        if (!file_exists("sistema")) 
        {
            mkdir("sistema");
            if (!file_exists("sistema/model"))
                mkdir("sistema/model");
        }
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
                $geters_seters.="\tfunction get".$metodo."(){\n";
                $geters_seters.="\t\treturn \$this->{$atributo};\n\t}\n";
                $geters_seters.="\tfunction set".$metodo."(\${$atributo}){\n";
                $geters_seters.="\t\t\$this->{$atributo}=\${$atributo};\n\t}\n";
            }
            $nomeTabela=ucfirst($nomeTabela);
            $conteudo = <<<EOT
<?php
class {$nomeTabela} {
{$nomeAtributos}
{$geters_seters}
}
?>
EOT;
      file_put_contents("sistema/model/{$nomeTabela}.php", $conteudo);

        }
    }
}

(new Creator())->ClassesModel();
