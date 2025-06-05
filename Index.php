<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <div class="container">
        <div class="formulario">
            <form action="CriaClassesItamar.php" method="POST">
                <h1>FrameWork</h1>
                <br>
                <h2>Configuração</h2>

                <br>
                <div class="campos">
                    <label for="servidor">Servidor:</label>
                    <input type="text" id="servidor" name="servidor" required>
                    
                    <label for="banco">Banco de dados:</label>
                    <input type="text" id="banco" name="banco" required>

                    <label for="usuario">Usuário:</label>
                    <input type="text" id="usuario" name="usuario" required>

                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required>

                    <button type="submit">Enviar</button>
                </div>
            </form>
        </div>
    </div>  
</body>
</html>